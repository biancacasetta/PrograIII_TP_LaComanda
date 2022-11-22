<?php

class ItemMenu
{
    public $id;
    public $idArea;
    public $codigoComanda;
    public $nombre;
    public $precio;
    public $tiempoInicio;
    public $tiempoFin;
    public $duracion;

    public function crearItemMenu()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO items (idUsuario, idArea, nombreApellido, fechaAlta) VALUES
        (:idUsuario, :idArea, :nombreApellido, :fechaAlta)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':idArea', $this->idArea, PDO::PARAM_INT);
        $consulta->bindValue(':nombreApellido', $this->nombreApellido, PDO::PARAM_STR);
        $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function obtenerEmpleado($nombreApellido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados WHERE nombreApellido = :nombreApellido");
        $consulta->bindValue(':nombreApellido', $nombreApellido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }
}