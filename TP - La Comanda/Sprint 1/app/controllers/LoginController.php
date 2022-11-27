<?php

require_once './models/AutentificadorJWT.php';

class LoginController
{
    public function Login($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $payload = json_encode(array("mensaje" => "Alguno de los datos es incorrecto"));

        try
        {
            if(isset($parametros['id']) && isset($parametros['clave']))
            {
                $empleado = Empleado::obtenerEmpleado($parametros['id']);

                if(!is_null($empleado))
                {
                    if(password_verify($parametros['clave'], $empleado->clave))
                    {
                        $datos = array("id" => $empleado->id, "nombreCompleto" => $empleado->nombreApellido, "perfil" => $empleado->perfil);
                        $token = AutentificadorJWT::CrearToken($datos);
                        $payload = json_encode(array("mensaje" => "Logueado con éxito", "nombreCompleto" => $empleado->nombreApellido, "perfil" => $empleado->perfil, "JWT" => $token));
                    }
                }
            }
        }
        catch(Exception $e)
        {
            $payload = json_encode(array("Error" => $e->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>