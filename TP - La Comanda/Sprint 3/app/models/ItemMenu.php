<?php

class ItemMenu
{
    public $id;
    public $nombre;
    public $precio;
    public $perfil;

    public function crearItemMenu()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO items (nombre, precio, perfil) VALUES
        (:nombre, :precio, :perfil)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public function crearItemMenuConId()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO items_backup (id, nombre, precio, perfil) VALUES
        (:id, :nombre, :precio, :perfil)");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM items");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ItemMenu');
    }

    public static function obtenerItemMenu($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM items WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('ItemMenu');
    }

    public static function modificarItemMenu($item)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE items SET nombre = :nombre, precio = :precio, perfil = :perfil in WHERE id = :id");
        $consulta->bindValue(':nombre', $item->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $item->precio, PDO::PARAM_INT);
        $consulta->bindValue(':perfil', $item->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':id', $item->id, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function borrarItemMenu($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM items WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function obtenerItemsPorComanda($codigo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM items
        JOIN pedidos ON items.id = pedidos.idItem
        JOIN comandas ON comandas.codigo = pedidos.codigoComanda
        WHERE pedidos.codigoComanda = :codigoComanda");
        $consulta->bindValue(':codigoComanda', $codigo);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ItemMenu');
    }

    public static function limpiarItemsBackup()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("TRUNCATE TABLE items_backup");
        $consulta->execute();
        return $consulta->rowCount();
    }
}