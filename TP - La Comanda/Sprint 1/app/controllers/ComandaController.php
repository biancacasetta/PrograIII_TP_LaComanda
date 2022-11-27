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
}
