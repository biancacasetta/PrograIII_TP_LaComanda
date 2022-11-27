<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['codigoComanda']) &&
        isset($parametros['idItem']))
        {
          $comanda = Comanda::obtenerComanda($parametros['codigoComanda']);
          $item = ItemMenu::obtenerItemMenu($parametros['idItem']);

          if(!is_bool($comanda))
          {
            if(!is_bool($item))
            {
              $codigoComanda = $parametros['codigoComanda'];
              $idItem = $parametros['idItem'];

              // Creamos el pedido
              $pedido = new Pedido();
              $pedido->codigoComanda = $codigoComanda;
              $pedido->idItem = $idItem;
              $pedido->crearPedido();

              $payload = json_encode(array("mensaje" => "Pedido creado con éxito", "item" => "Se agregó un/a $item->nombre a la comanda."));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "El item ingresado no existe."));
            }
          }
          else
          {
            $payload = json_encode(array("mensaje" => "La comanda ingresada no existe."));
          }
          
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Faltan datos para crear un pedido"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos pedido por id
        $id = $args['idPedido'];
        $pedido = Pedido::obtenerPedido($id);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['codigoComanda']) &&
        isset($parametros['idItem']) &&
        isset($parametros['idEmpleado']) &&
        isset($parametros['estado']) &&
        isset($parametros['duracion']))
        {
            $pedido = new Pedido();
            $pedido->codigoComanda = $parametros['codigoComanda'];
            $pedido->idItem = $parametros['idItem'];
            $pedido->idEmpleado = $parametros['idEmpleado'];
            $pedido->estado = $parametros['estado'];
            $pedido->duracion = $parametros['duracion'];
            $pedido->id = $args['id'];
            
            if(Pedido::modificarPedido($pedido))
            {
              $payload = json_encode(array("mensaje" => "Pedido modificado con éxito"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se pudo modificar el pedido"));
            }
        }
        else
        {
            $payload = ["mensaje" => "Faltan datos para modificar el pedido."];
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        
        if(Pedido::cancelarPedido($id))
        {
          $payload = json_encode(array("mensaje" => "Pedido cancelado con éxito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se pudo cancelar el pedido"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
