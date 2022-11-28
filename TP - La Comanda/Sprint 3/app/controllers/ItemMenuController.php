<?php
require_once './models/ItemMenu.php';
require_once './models/CSV.php';
require_once './interfaces/IApiUsable.php';

class ItemMenuController extends ItemMenu implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['nombre']) &&
        isset($parametros['precio']) &&
        isset($parametros['perfil']))
        {
          $nombre = $parametros['nombre'];
          $precio = $parametros['precio'];
          $perfil = $parametros['perfil'];

          // Creamos el pedido
          $item = new ItemMenu();
          $item->nombre = $nombre;
          $item->precio = $precio;
          $item->perfil = $perfil;
          $item->crearItemMenu();

          $payload = json_encode(array("mensaje" => "Item del menú creado con éxito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Faltan datos para crear un item del menú"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos item por id
        $id = $args['idItem'];
        $item = ItemMenu::obtenerItemMenu($id);
        $payload = json_encode($item);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = ItemMenu::obtenerTodos();
        $payload = json_encode(array("Menu" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombre']) &&
        isset($parametros['precio']) &&
        isset($parametros['perfil']))
        {
            $item = new ItemMenu();
            $item->nombre = $parametros['nombre'];
            $item->precio = $parametros['precio'];
            $item->perfil = $parametros['perfil'];
            $item->id = $args['id'];
            
            if(ItemMenu::modificarItemMenu($item))
            {
              $payload = json_encode(array("mensaje" => "Item del menú modificado con éxito"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se pudo modificar el item del menú"));
            }
        }
        else
        {
            $payload = ["mensaje" => "Faltan datos para modificar el item del menú."];
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        
        if(ItemMenu::borrarItemMenu($id))
        {
          $payload = json_encode(array("mensaje" => "Item borrado del menú con éxito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se pudo borrar el item del menú"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CrearCSVItems($request, $response, $args)
    {
      if(CSV::crearCSV())
      {
        $payload = json_encode(array("mensaje" => "Se creó el archivo .csv de los items del menú"));
      }
      else
      {
        $payload = json_encode(array("mensaje" => "No se pudo crear el archivo .csv"));
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarCSVItems($request, $response, $args)
    {
      try{     
        $archivo = $_FILES["csv"]["tmp_name"];

        if(CSV::cargarCSV($archivo))
        {       
          readfile($archivo);
          $res = $response->withHeader('Content-Type', 'text/csv');
        }
        else
        {
          $res = $response->withHeader('Content-Type', 'application/json');
          $response->getBody()->write(json_encode(array("Error" => "No se pudo cargar el archivo .csv")));
        }
      }
      catch(Exception $exp)
      {
        printf("Excepcion capturada: {$exp->getMessage()}");
      }
      finally
      {
        return $res;
      }
    }
}
