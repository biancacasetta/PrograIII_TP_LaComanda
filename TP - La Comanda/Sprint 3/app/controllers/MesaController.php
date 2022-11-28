<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      if(isset($parametros['codigo']))
      {

        // Creamos la mesa
        $mesa = Mesa::obtenerMesa($parametros['codigo']);

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        $mesa->idEmpleado = $data->id;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa dada de alta con éxito"));
      }
      else
      {
        $payload = ["mensaje" => "Faltan datos para dar de alta a la mesa."];
      }
      
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos mesa por codigo
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorEstado($request, $response, $args)
    {
        $estado = $args['estado'];
        $lista = Mesa::obtenerTodosPorEstado($estado);
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {

    }

    public function BorrarUno($request, $response, $args)
    {

    }

    public function CerrarMesa($request, $response, $args)
    {
      $mesa = Mesa::obtenerMesa($args['codigo']);

      if(!is_bool($mesa))
      {
        if($mesa->estado == "Con cliente pagando")
        {
          $comandasActivas = Comanda::obtenerComandasPorMesa($mesa->codigo);

          if($comandasActivas == 0)
          {
            $mesa->idEmpleado = NULL;
            Mesa::modificarEmpleadoMesa($mesa);
            

            $mesa->estado = "Cerrada";
            if(Mesa::modificarEstadoMesa($mesa))
            {
              $mensaje = ["mensaje" => "La mesa se cerró con éxito."];
            }
            else
            {
              $mensaje = ["mensaje" => "No se pudo cerrar la mesa."];
            }
          }
          else
          {
            $mensaje = ["mensaje" => "No se puede cerrar la mesa si tiene una comanda activa."];
          }
        }
        else
        {
          $mensaje = ["mensaje" => "No se puede cerrar una mesa que ya está cerrada o antes de cobrar la cuenta."];
        }
      }
      else
      {
        $mensaje = ["mensaje" => "No existe la mesa ingresada."];
      }

      $payload = json_encode($mensaje);
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
