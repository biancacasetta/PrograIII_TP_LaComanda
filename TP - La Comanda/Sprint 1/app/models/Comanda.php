<?php

class Comanda
{
    public $codigo;
    public $codigoMesa;
    public $nombreCliente;
    public $imagen;
    public $cuenta;
    public $estado;

    public function crearComanda()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO comandas (codigo, codigoMesa, nombreCliente, imagen, estado)
        VALUES (:codigo, :codigoMesa, :nombreCliente, :imagen, :estado)");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':codigoMesa', $this->codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':imagen', $this->imagen, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "Pendiente");
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function generarCodigo()
    {
        $caracteresPermitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($caracteresPermitidos), 0, 5);
    }

    public function generarNombreImagen($imagen)
    {
        $extension = pathinfo($imagen, PATHINFO_EXTENSION);
        $nombre = $this->codigo."-".$this->codigoMesa."-".$this->nombreCliente.".".$extension;

        return $nombre;
    }

    public function guardarImagen($imagen)
    {
        $carpeta = "Media/Comandas";
        $destino = $carpeta."/".$this->generarNombreImagen($imagen);
        $this->imagen = $destino;
        $mensaje = "";

        if(!file_exists($carpeta))
        {
            mkdir($carpeta, 0777, true);
        }

        if(!file_exists($destino))
        {
            if(move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino))
            {
                $mensaje = ["imagen" => "Se agregó la imagen de la comanda a la carpeta $carpeta exitosamente."];
            }
            else
            {
                $mensaje = ["imagen" => "No se pudo agregar la imagen de la comanda a la carpeta $carpeta."];
            }
        }
        return $mensaje;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public static function obtenerComanda($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comandas WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Comanda');
    }

    public static function modificarComanda($comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE comandas SET codigoMesa = :codigoMesa, nombreCliente = :nombreCliente, imagen = :imagen, cuenta = :cuenta, estado = :estado WHERE id = :id");
        $consulta->bindValue(':codigoMesa', $comanda->codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $comanda->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':imagen', $comanda->imagen, PDO::PARAM_STR);
        $consulta->bindValue(':cuenta', $comanda->cuenta, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $comanda->estado, PDO::PARAM_STR);

        return $consulta->execute();
    }
}