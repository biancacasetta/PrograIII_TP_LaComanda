<?php

class Pedido
{
    public $id;
    public $codigoComanda;
    public $idItem;
    public $idEmpleado;
    public $estado;
    public $duracion;
    //public $horaInicio;
    //public $horaFin;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigoComanda, idItem, estado) VALUES
        (:codigoComanda, :idItem, :estado)");
        $consulta->bindValue(':codigoComanda', $this->codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':idItem', $this->idItem, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "Pendiente");
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPendientes()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos
        JOIN items ON pedidos.idItem = items.id
        WHERE estado = :estado");
        $consulta->bindValue(':estado', 'Pendiente');
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPendientesPorPerfil($perfil)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id, codigoComanda, idItem, estado, nombre, precio, perfil FROM pedidos
        JOIN items ON pedidos.idItem = items.id
        WHERE perfil = :perfil AND estado = :estado");

        if($perfil == "Mozo")
        {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id, codigoComanda, idItem, estado, nombre, precio, perfil FROM pedidos
            JOIN items ON pedidos.idItem = items.id
            WHERE estado = :estado");
            $consulta->bindValue(':estado', "Listo para servir");
        }
        else
        {
            $consulta->bindValue(':perfil', $perfil);
            $consulta->bindValue(':estado', 'Pendiente');
        }
        
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPerfil($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT perfil FROM pedidos
        JOIN items ON pedidos.idItem = items.id
        WHERE pedidos.id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchColumn();
    }

    public static function obtenerPedidosPorComanda($codigoComanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(codigoComanda)
        FROM pedidos WHERE codigoComanda = :codigoComanda");
        $consulta->bindValue(':codigoComanda', $codigoComanda);
        $consulta->execute();

        return $consulta->fetchColumn();
    }

    public static function obtenerPedidosListosPorComanda($codigoComanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(codigoComanda)
        FROM pedidos WHERE codigoComanda = :codigoComanda AND estado = :estado");
        $consulta->bindValue(':codigoComanda', $codigoComanda);
        $consulta->bindValue(':estado', "Listo para servir");
        $consulta->execute();

        return $consulta->fetchColumn();
    }

    public static function modificarPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET idEmpleado = :idEmpleado, estado = :estado, duracion = :duracion WHERE pedidos.id = :id");
        $consulta->bindValue(':idEmpleado', $pedido->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
        $consulta->bindValue(':duracion', $pedido->duracion, PDO::PARAM_INT);
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function modificarEstadoPedido($pedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado  WHERE id = :id");
        $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $pedido->id, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function modificarEstadoPedidosPorComanda($codigoComanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado  WHERE codigoComanda = :codigoComanda");
        $consulta->bindValue(':estado', "Servido");
        $consulta->bindValue(':codigoComanda', $codigoComanda);

        return $consulta->execute();
    }

    public static function cancelarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', "Cancelado");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        return $consulta->execute();
    }
}