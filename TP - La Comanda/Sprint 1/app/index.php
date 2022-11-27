<?php
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

// Controllers
require_once './controllers/LoginController.php';
require_once './controllers/EmpleadoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/ItemMenuController.php';

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
  });

// Rutas Items
$app->group('/menu', function (RouteCollectorProxy $items) {
  $items->get('[/]', \ItemMenuController::class . ':TraerTodos');
  $items->get('/{idItem}', \ItemMenuController::class . ':TraerUno');
  $items->post('[/]', \ItemMenuController::class . ':CargarUno');
  $items->put('/{id}', \ItemMenuController::class . ':ModificarUno');
  $items->delete('/{id}', \ItemMenuController::class . ':BorrarUno');
});

// Rutas Mesas
$app->group('/mesas', function (RouteCollectorProxy $mesas) {
  $mesas->get('[/]', \MesaController::class . ':TraerTodos');
  $mesas->get('/estado/{estado}', \MesaController::class . ':TraerTodosPorEstado');
  $mesas->get('/{codigo}', \MesaController::class . ':TraerUno');
  $mesas->post('[/]', \MesaController::class . ':CargarUno');
  $mesas->put('/{codigo}', \MesaController::class . ':ModificarUno');
  $mesas->delete('/{codigo}', \MesaController::class . ':BorrarUno');
});

// Rutas Pedidos
$app->group('/pedidos', function (RouteCollectorProxy $pedidos) {
  $pedidos->get('[/]', \PedidoController::class . ':TraerTodos');
  $pedidos->get('/{idPedido}', \PedidoController::class . ':TraerUno');
  $pedidos->post('[/]', \PedidoController::class . ':CargarUno');
  $pedidos->put('/{id}', \PedidoController::class . ':ModificarUno');
  $pedidos->delete('/{id}', \PedidoController::class . ':BorrarUno');
});

// Rutas Comandas
$app->group('/comandas', function (RouteCollectorProxy $comandas) {
  $comandas->get('[/]', \ComandaController::class . ':TraerTodos');
  $comandas->get('/{codigo}', \ComandaController::class . ':TraerUno');
  $comandas->post('[/]', \ComandaController::class . ':CargarUno');
  $comandas->put('/{codigo}', \ComandaController::class . ':ModificarUno');
});

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "TP La Comanda - Bianca Casetta"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
