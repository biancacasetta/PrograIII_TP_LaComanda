<?php
require_once './models/Empleado.php';
require_once './interfaces/IApiUsable.php';

date_default_timezone_set('America/Argentina/Buenos_Aires');

class EmpleadoController extends Empleado implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['nombreApellido']) &&
        isset($parametros['perfil']) &&
        isset($parametros['clave']) &&
        isset($parametros['esSocio']))
        {
          $clave = $parametros['clave'];
          $nombreApellido = $parametros['nombreApellido'];
          $perfil = $parametros['perfil'];
          $esSocio = $parametros['esSocio'];
          $fechaAlta = date('Y-m-d');

          // Creamos el empleado
          $empleado = new Empleado();
          $empleado->clave = $clave;
          $empleado->nombreApellido = $nombreApellido;
          $empleado->perfil = $perfil;
          $empleado->esSocio = $esSocio;
          $empleado->fechaAlta = $fechaAlta;
          $empleado->crearEmpleado();

          $payload = json_encode(array("mensaje" => "Empleado creado con éxito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Faltan datos para crear un empleado"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos empleado por id
        $id = $args['idEmpleado'];
        $empleado = Empleado::obtenerEmpleado($id);
        $payload = json_encode($empleado);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Empleado::obtenerTodos();
        $payload = json_encode(array("listaEmpleados" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombreApellido']) &&
        isset($parametros['perfil']) &&
        isset($parametros['clave']) &&
        isset($parametros['esSocio']))
        {
            $empleado = new Empleado();
            $empleado->clave = $parametros['clave'];
            $empleado->nombreApellido = $parametros['nombreApellido'];
            $empleado->perfil = $parametros['perfil'];
            $empleado->esSocio = $parametros['esSocio'];
            $empleado->id = $args['id'];
            
            if(Empleado::modificarEmpleado($empleado))
            {
              $payload = json_encode(array("mensaje" => "Empleado modificado con éxito"));
            }
            else
            {
              $payload = json_encode(array("mensaje" => "No se pudo modificar al empleado"));
            }
        }
        else
        {
            $payload = ["mensaje" => "Faltan datos para modificar el empleado."];
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        
        if(Empleado::borrarEmpleado($id))
        {
          $payload = json_encode(array("mensaje" => "Empleado borrado con éxito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se pudo borrar al empleado"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
