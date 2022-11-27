<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      if(isset($parametros['codigo']) && isset($parametros['idEmpleado']))
      {

        // Creamos la mesa
        $mesa = Mesa::obtenerMesa($parametros['codigo']);
        $mesa->idEmpleado = $parametros['idEmpleado'];
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
        $parametros = $request->getParsedBody();

        if(isset($parametros['idEmpleado']) &&
        isset($parametros['estado']))
        {
            $mesa = new Mesa();
            $mesa->idEmpleado = $parametros['idEmpleado'];
            $mesa->estado = $parametros['estado'];
            $mesa->codigo = $args['codigo'];
            
            if(Mesa::modificarMesa($mesa))
            {
            $payload = json_encode(array("mensaje" => "Mesa modificada con éxito"));
            }
            else
            {
            $payload = json_encode(array("mensaje" => "No se pudo modificar a la mesa"));
            }
        }
        else
        {
            $payload = ["mensaje" => "Faltan datos para modificar la mesa."];
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $codigo = $args['codigo'];
        
        if(Mesa::borrarMesa($codigo))
        {
          $payload = json_encode(array("mensaje" => "Mesa borrada con éxito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se pudo borrar la mesa"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
