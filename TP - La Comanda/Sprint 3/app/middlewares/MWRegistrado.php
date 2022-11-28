<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWRegistrado
{
    public function __invoke(Request $request, RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();

        try
        {
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header)[1]);
                AutentificadorJWT::VerificarToken($token);
                $response = $handler->handle($request);
            }
            else
            {
                throw new Exception("Necesita iniciar sesión (Token vacío)");
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