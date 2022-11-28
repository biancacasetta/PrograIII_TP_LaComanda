<?php

class Encuesta
{
    public $id;
    public $codigoMesa;
    public $codigoComanda;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $puntajeMesa;
    public $puntajeRestaurante;
    public $promedio;
    public $comentario;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigoMesa, codigoComanda, puntajeMozo, puntajeCocinero, puntajeMesa, puntajeRestaurante, promedio, comentario)
        VALUES (:codigoMesa, :codigoComanda, :puntajeMozo, :puntajeCocinero, :puntajeMesa, :puntajeRestaurante, :promedio, :comentario)");
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigoComanda', $this->codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurante', $this->puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':promedio', $this->promedio);
        $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
        $consulta->execute();
    
        return $objAccesoDatos->obtenerUltimoId();
    }
}
    
