<?php

class Mesa
{
    public $codigo;
    public $idEmpleado;
    public $estado;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET idEmpleado = :idEmpleado WHERE codigo = :codigo");
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerTodosPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE estado = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_BOOL);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarEstadoMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $mesa->codigo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $mesa->estado, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function modificarEmpleadoMesa($mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET idEmpleado = :idEmpleado WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $mesa->codigo, PDO::PARAM_INT);
        $consulta->bindValue(':idEmpleado', $mesa->idEmpleado, PDO::PARAM_INT);

        return $consulta->execute();
    }
}