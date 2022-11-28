<?php
require_once './models/Comanda.php';
require_once './interfaces/IApiUsable.php';

class ComandaController extends Comanda implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['codigoMesa']) &&
        isset($parametros['nombreCliente']))
        {
          $mesa = Mesa::obtenerMesa($parametros['codigoMesa']);

          if(!is_bool($mesa))
          {
            if(!is_null($mesa->idEmpleado))
            {
              $comandasMesa = Comanda::obtenerComandasPorMesa($mesa->codigo);

              if($comandasMesa == 0)
              {
                $codigoMesa = $parametros['codigoMesa'];
                $nombreCliente = $parametros['nombreCliente'];
                $imagen = $_FILES['imagen']['name'];

                // Creamos la comanda
                $comanda = new Comanda();
                $comanda->codigo = Comanda::generarCodigo();
                $comanda->codigoMesa = $codigoMesa;
                $comanda->nombreCliente = $nombreCliente;

                if(isset($_FILES['imagen']))
                {
                  $mensajeImagen = $comanda->guardarImagen($imagen);
                }
                else
                {
                  $comanda->imagen = NULL;
                }

                $comanda->crearComanda();

                $mensaje = ["mensaje" => "Comanda creada con éxito"];
                $mensaje += $mensajeImagen;
              }
              else
              {
                $mensaje = ["mensaje" => "Hay una comanda activa. Solo se permite una a la vez por mesa."];
              }
              
            }
            else
            {
              $mensaje = ["mensaje" => "Aguarde que un mozo los atienda y les tome la comanda."];
            }
            
          }
          else
          {
            $mensaje = ["mensaje" => "La mesa ingresada no existe"];
          }
        }
        else
        {
          $mensaje = ["mensaje" => "Faltan datos para crear una comanda"];
        }

        $payload = json_encode($mensaje);
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos comanda por codigo
        $codigo = $args['codigo'];
        $comanda = Comanda::obtenerComanda($codigo);
        $payload = json_encode($comanda);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Comanda::obtenerTodos();
        $payload = json_encode(array("listaComandas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['codigoMesa']) &&
        isset($parametros['nombreCliente']) &&
        isset($parametros['imagen']) &&
        isset($parametros['cuenta']) && 
        isset($parametros['estado']))
        {
            $comanda = new Comanda();
            $comanda->codigoMesa = $parametros['codigoMesa'];
            $comanda->nombreCliente = $parametros['nombreCliente'];
            $comanda->imagen = $parametros['imagen'];
            $comanda->cuenta = $parametros['cuenta'];
            $comanda->estado = $parametros['estado'];
            $comanda->codigo = $args['codigo'];
            
            if(Comanda::modificarComanda($comanda))
            {
              $payload = json_encode(array("mensaje" => "Comanda modificada con éxito"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se pudo modificar la comanda"));
            }
        }
        else
        {
            $payload = ["mensaje" => "Faltan datos para modificar la comanda."];
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        
    }

    public function CalcularCuenta($request, $response, $args)
    {
      $comanda = Comanda::obtenerComanda($args['codigo']);

      if(!is_bool($comanda))
      {
        $cuenta = 0;
        $items = ItemMenu::obtenerItemsPorComanda($comanda->codigo);

        foreach($items as $item)
        {
          $cuenta += $item->precio;
        }

        $comanda->cuenta = $cuenta;

        if(Comanda::modificarCuentaComanda($comanda))
        {
          $mensaje = ["mensaje" => "La cuenta total de la comanda $comanda->codigo es $cuenta."];
          $comanda->estado = "Cobrada";
          Comanda::modificarEstadoComanda($comanda);
          $mensaje += ["comanda" => "La comanda se encuentra cobrada."];

          $mesa = Mesa::obtenerMesa($comanda->codigoMesa);
          $mesa->estado = "Con cliente pagando";
          Mesa::modificarEstadoMesa($mesa);
          $mensaje += ["mesa" => "El cliente de la mesa se encuentra pagando."];
        }
        else
        {
          $mensaje = ["mensaje" => "No se pudo calcular la cuenta de la comanda."];
        }
      }
      else
      {
        $mensaje = ["mensaje" => "No existe la comanda ingresada."];
      }

      $payload = json_encode($mensaje);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function ObtenerDemora($request, $response, $args)
    {
      if(isset($_GET['codigoMesa']) && isset($_GET['codigoComanda']))
      {
        $codigoMesa = $_GET['codigoMesa'];
        $codigoComanda = $_GET['codigoComanda'];
        $comanda = Comanda::obtenerComanda($codigoComanda);
        $mesa = Mesa::obtenerMesa($codigoMesa);

        if(!is_bool($mesa))
        {
          if(!is_bool($comanda))
          {
            $demora = Comanda::obtenerDemoraComanda($codigoMesa, $codigoComanda);
            
            if($demora)
            {
              $mensaje = ["mensaje" => "La demora de la comanda $comanda->codigo es de $demora minutos."];
            }
            else
            {
              $mensaje = ["mensaje" => "No se pudo calcular la demora de la comanda."];
            }
          }
          else
          {
            $mensaje = ["mensaje" => "No existe la comanda ingresada."];
          }
        }
        else
        {
          $mensaje = ["mensaje" => "No existe la mesa ingresada."];
        }

      }
      else
      {
        $mensaje = ["mensaje" => "Faltan datos para consultar la demora de la comanda."];
      }

      $payload = json_encode($mensaje);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
}
