<?php
require_once './models/Encuesta.php';

class EncuestaController extends Encuesta
{
    public function CargarEncuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($_POST['codigoComanda']) &&
        isset($_POST['codigoMesa']) &&
        isset($_POST['puntajeMozo']) &&
        isset($_POST['puntajeCocinero']) &&
        isset($_POST['puntajeMesa']) &&
        isset($_POST['puntajeRestaurante']) &&
        isset($_POST['comentario']))
        {   
            $codigoComanda = $parametros['codigoComanda'];
            $codigoMesa = $parametros['codigoMesa'];
            $puntajeMozo = $parametros['puntajeMozo'];
            $puntajeCocinero = $parametros['puntajeCocinero'];
            $puntajeMesa =  $parametros['puntajeMesa'];
            $puntajeRestaurante = $parametros['puntajeRestaurante'];
            $promedio = ($puntajeMozo + $puntajeCocinero + $puntajeMesa + $puntajeRestaurante) / 4;
            $comentario = $parametros['comentario'];

            $comanda = Comanda::obtenerComanda($codigoComanda);
            $mesa = Mesa::obtenerMesa($codigoMesa);

            if(!is_bool($mesa))
            {
                if(!is_bool($comanda))
                {
                    if(($puntajeCocinero > 0 && $puntajeCocinero <= 10) &&
                        ($puntajeMesa > 0 && $puntajeMesa <= 10) && 
                        ($puntajeMozo > 0 && $puntajeMozo <= 10) &&
                        ($puntajeRestaurante > 0 && $puntajeRestaurante <= 10))
                        {
                            if(strlen($comentario) <= 66)
                            {
                                if($comanda->codigoMesa == $mesa->codigo)
                                {
                                    if($mesa->estado == "Cerrada")
                                    {
                                        // Creamos la encuesta
                                        $encuesta = new Encuesta();
                                        $encuesta->codigoMesa = $codigoMesa;
                                        $encuesta->codigoComanda = $codigoComanda;
                                        $encuesta->puntajeMozo = $puntajeMozo;
                                        $encuesta->puntajeCocinero = $puntajeCocinero;
                                        $encuesta->puntajeMesa = $puntajeMesa;
                                        $encuesta->puntajeRestaurante = $puntajeRestaurante;
                                        $encuesta->promedio = $promedio;
                                        $encuesta->comentario = $comentario;
                                        $encuesta->crearEncuesta();

                                        $mensaje = ["mensaje" => "Encuesta completada con éxito."];
                                    }
                                    else
                                    {
                                        $mensaje = ["mensaje" => "No se puede completar la encuesta hasta que la mesa esté cerrada."];
                                    }
                                }
                                else
                                {
                                    $mensaje = ["mensaje" => "La comanda ingresda no corresponde a la mesa ingresada."];
                                }
                            }
                            else
                            {
                                $mensaje = ["mensaje" => "El comentario no debe superar los 66 caracteres."];
                            }
                        }
                        else
                        {
                            $mensaje = ["mensaje" => "Los puntajes deben ser números enteros entre 1 y 10."];
                        }
                }
                else
                {
                    $mensaje = ["mensaje" => "La comanda ingresada no existe."];
                }
            }
            else
            {
                $mensaje = ["mensaje" => "La mesa ingresada no existe."];
            }
        }
        else
        {
            $mensaje = ["mensaje" => "Faltan datos para cargar la encuesta."];
        }

        $payload = json_encode($mensaje);
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}