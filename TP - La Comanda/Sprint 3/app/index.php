<?php
use Psr7Middlewares\Middleware;
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

// Middlewares
require_once './middlewares/MWRegistrado.php';
require_once './middlewares/MWSocio.php';
require_once './middlewares/MWMozo.php';

// Controllers
require_once './controllers/LoginController.php';
require_once './controllers/EmpleadoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ItemMenuController.php';
require_once './controllers/EncuestaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Login
$app->post('/login', \LoginController::class . ':Login');

// Rutas Empleados
$app->group('/empleados', function (RouteCollectorProxy $empleados) {
    $empleados->get('[/]', \EmpleadoController::class . ':TraerTodos');
    $empleados->get('/{idEmpleado}', \EmpleadoController::class . ':TraerUno');
    $empleados->post('[/]', \EmpleadoController::class . ':CargarUno');
    $empleados->put('/{id}', \EmpleadoController::class . ':ModificarUno');
    $empleados->delete('/{id}', \EmpleadoController::class . ':BorrarUno');
  })->add(new MWSocio())->add(new MWRegistrado());

// Rutas Items
$app->group('/menu', function (RouteCollectorProxy $items) {
  $items->get('[/]', \ItemMenuController::class . ':TraerTodos');
  $items->get('/{idItem}', \ItemMenuController::class . ':TraerUno');
  $items->post('[/]', \ItemMenuController::class . ':CargarUno');
  $items->put('/{id}', \ItemMenuController::class . ':ModificarUno');
  $items->delete('/{id}', \ItemMenuController::class . ':BorrarUno');
})->add(new MWSocio())->add(new MWRegistrado());

$app->get('/menu/csv/crear', \ItemMenuController::class . ':CrearCSVItems');
$app->post('/menu/csv/cargar', \ItemMenuController::class . ':CargarCSVItems');

// Rutas Mesas
$app->group('/mesas', function (RouteCollectorProxy $mesas) {
  $mesas->get('[/]', \MesaController::class . ':TraerTodos');
  $mesas->get('/estado/{estado}', \MesaController::class . ':TraerTodosPorEstado');
  $mesas->get('/{codigo}', \MesaController::class . ':TraerUno');
  $mesas->post('[/]', \MesaController::class . ':CargarUno');
  $mesas->put('/{codigo}', \MesaController::class . ':CerrarMesa')->add(new MWSocio());
})->add(new MWMozo())->add(new MWRegistrado());

// Rutas Pedidos
$app->group('/pedidos', function (RouteCollectorProxy $pedidos) {
  $pedidos->get('[/]', \PedidoController::class . ':TraerTodos')->add(new MWSocio());
  $pedidos->get('/pendientes', \PedidoController::class . ':TraerPendientesPorPerfil');
  $pedidos->get('/{idPedido}', \PedidoController::class . ':TraerUno');
  $pedidos->post('[/]', \PedidoController::class . ':CargarUno')->add(new MWMozo());
  $pedidos->put('/tomar/{id}', \PedidoController::class . ':TomarPedido');
  $pedidos->put('/terminar/{id}', \PedidoController::class . ':TerminarPedido');
  $pedidos->put('/servir/{codigoComanda}', \PedidoController::class . ':ServirPedidos')->add(new MWMozo());
  $pedidos->delete('/{id}', \PedidoController::class . ':BorrarUno')->add(new MWMozo());
})->add(new MWRegistrado());

// Rutas Comandas
$app->group('/comandas', function (RouteCollectorProxy $comandas) {
  $comandas->get('[/]', \ComandaController::class . ':TraerTodos')->add(new MWMozo());
  $comandas->get('/{codigo}', \ComandaController::class . ':TraerUno');
  $comandas->post('[/]', \ComandaController::class . ':CargarUno')->add(new MWMozo());
  $comandas->put('/{codigo}', \ComandaController::class . ':CalcularCuenta')->add(new MWMozo());
})->add(new MWRegistrado());

// Demora
$app->get('/demora', \ComandaController::class . ':ObtenerDemora');

// Encuesta
$app->post('/encuesta', \EncuestaController::class . ':CargarEncuesta');

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "TP La Comanda - Bianca Casetta"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
