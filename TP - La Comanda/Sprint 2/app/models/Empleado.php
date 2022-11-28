<?php

class Empleado
{
    public $id;
    public $clave;
    public $nombreApellido;
    public $perfil;
    public $esSocio;
    public $fechaAlta;
    public $fechaBaja;

    public function crearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (clave, nombreApellido, perfil, esSocio, fechaAlta) VALUES
        (:clave, :nombreApellido, :perfil, :esSocio, :fechaAlta)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':nombreApellido', $this->nombreApellido, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':esSocio', $this->esSocio, PDO::PARAM_BOOL);
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

    public static function obtenerEmpleado($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Empleado');
    }

    public static function modificarEmpleado($empleado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET clave = :clave, nombreApellido = :nombreApellido, perfil = :perfil, esSocio = :esSocio WHERE id = :id");
        $claveHash = password_hash($empleado->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':nombreApellido', $empleado->nombreApellido, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $empleado->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':esSocio', $empleado->esSocio, PDO::PARAM_BOOL);
        $consulta->bindValue(':id', $empleado->id, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function borrarEmpleado($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET fechaBaja = :fechaBaja WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d'));

        return $consulta->execute();
    }
}