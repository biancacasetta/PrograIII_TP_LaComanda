<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWMozo
{
    public function __invoke(Request $request, RequestHandler $handler) : Response
    { 
        $response = new Response();
        try
        {
            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);
            $data = AutentificadorJWT::ObtenerData($token);

            if($data->perfil == "Mozo" || $data->perfil == "Socio")
            {
                $response = $handler->handle($request);
            }
            else
            {
                throw new Exception("Solo mozos pueden realizar esta acción");
            }
        }
        catch(Exception $e)
        {
            $payload = json_encode(array("Error" => $e->getMessage()));
            $response->getBody()->write($payload);
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}


?>