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

              if($pedido->crearPedido())
              {
                $comanda = Comanda::obtenerComanda($codigoComanda);
                $mesa = Mesa::obtenerMesa($comanda->codigoMesa);

                $mesa->estado = "Con cliente esperando pedido";
                Mesa::modificarEstadoMesa($mesa);

                $payload = json_encode(array("mensaje" => "Pedido creado con éxito", "item" => "Se agregó un/a $item->nombre a la comanda."));
              }
              else
              {
                $payload = json_encode(array("mensaje" => "No se pudo crear el pedido."));
              }
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

    public function TraerPendientesPorPerfil($request, $response, $args)
    {
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $data = AutentificadorJWT::ObtenerData($token);
      $perfil = $data->perfil;

      if($perfil == "Socio")
      {
        $lista = Pedido::obtenerPendientes();
        $payload = json_encode(array("listaPedidosPendientes" => $lista));
      }
      else
      {
        $lista = Pedido::obtenerPendientesPorPerfil($perfil);
        $payload = json_encode(array("listaPedidosPendientePorPerfil" => $lista));
      }

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

    public function TomarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['duracion']))
        {
            $pedido = Pedido::obtenerPedido($args['id']);

            if(!is_bool($pedido))
            {
              $header = $request->getHeaderLine('Authorization');
              $token = trim(explode("Bearer", $header)[1]);
              $data = AutentificadorJWT::ObtenerData($token);
              $perfil = Pedido::obtenerPerfil($pedido->id);

              if($perfil == $data->perfil)
              {
                $pedido->idEmpleado = $data->id;
                $pedido->estado = "En preparación";
                $pedido->duracion = $parametros['duracion'];
                
                if(Pedido::modificarPedido($pedido))
                {
                  $payload = json_encode(array("mensaje" => "Pedido tomado con éxito"));
                  $comanda = Comanda::obtenerComanda($pedido->codigoComanda);
                  $comanda->estado = "En preparación";
                  Comanda::modificarEstadoComanda($comanda);
                }
                else
                {
                  $payload = json_encode(array("mensaje" => "No se pudo tomar el pedido"));
                }
              }
              else
              {
                $payload = json_encode(array("mensaje" => "Solo los $perfil"."s pueden tomar este pedido (Usted es $data->perfil)."));
              }
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No existe el pedido ingresado"));
            }
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Faltan datos para tomar el pedido."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TerminarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedido = Pedido::obtenerPedido($args['id']);

        if(!is_bool($pedido))
        {
          $header = $request->getHeaderLine('Authorization');
          $token = trim(explode("Bearer", $header)[1]);
          $data = AutentificadorJWT::ObtenerData($token);
          $perfil = Pedido::obtenerPerfil($pedido->id);

          if($perfil == $data->perfil)
          {
            if($pedido->idEmpleado == $data->id)
            {
              $pedido->estado = "Listo para servir";
              
              if(Pedido::modificarEstadoPedido($pedido))
              {
                $payload = json_encode(array("mensaje" => "Pedido terminado con éxito"));
              }
              else
              {
                $payload = json_encode(array("mensaje" => "No se pudo terminar el pedido"));
              }
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No puede terminar un pedido que tomó otro $perfil."));
            }
          }
          else
          {
            $payload = json_encode(array("mensaje" => "Solo los $perfil"."s pueden terminar este pedido (Usted es $data->perfil)."));
          }
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No existe el pedido ingresado"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ServirPedidos($request, $response, $args)
    {
        $comanda = Comanda::obtenerComanda($args['codigoComanda']);

        if(!is_bool($comanda))
        {
          $codigoComanda = $comanda->codigo;

          if(Pedido::obtenerPedidosListosPorComanda($codigoComanda) ==
          Pedido::obtenerPedidosPorComanda($codigoComanda))
          {
            if(Pedido::modificarEstadoPedidosPorComanda($codigoComanda))
            {
              $mensaje = ["mensaje" => "Pedidos servidos con éxito"];
              
              $comanda->estado = "Servida. Pago pendiente";
              Comanda::modificarEstadoComanda($comanda);
              $mensaje += ["comanda" => "La comanda se encuentra servida."];

              $mesa = Mesa::obtenerMesa($comanda->codigoMesa);
              $mesa->estado = "Con cliente comiendo";
              Mesa::modificarEstadoMesa($mesa);
              $mensaje += ["mesa" => "Los clientes de la mesa se encuentran comiendo."];
            }
            else
            {
              $mensaje = ["mensaje" => "No se pudieron servir los pedidos"];
            }
          }
          else
          {
            $mensaje = ["mensaje" => "No se pueden servir los pedidos hasta que todos los de la misma comanda estén listos."];
          }
        }
        else
        {
          $mensaje = ["mensaje" => "No existe la comanda ingresada para servir sus pedidos"];
        }

        $payload = json_encode($mensaje);
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
