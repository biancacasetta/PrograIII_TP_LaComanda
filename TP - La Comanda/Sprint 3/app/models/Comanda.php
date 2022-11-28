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

    public static function obtenerComandasPorMesa($codigoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(codigo) FROM comandas
        WHERE codigoMesa = :codigoMesa AND NOT estado = :estado");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "Cobrada");
        $consulta->execute();

        return $consulta->fetchColumn();
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

    public static function modificarEstadoComanda($comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE comandas SET estado = :estado WHERE codigo = :codigo");
        $consulta->bindValue(':estado', $comanda->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $comanda->codigo, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function modificarCuentaComanda($comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE comandas SET cuenta = :cuenta WHERE codigo = :codigo");
        $consulta->bindValue(':cuenta', $comanda->cuenta, PDO::PARAM_INT);
        $consulta->bindValue(':codigo', $comanda->codigo, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function obtenerDemoraComanda($codigoMesa, $codigoComanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT MAX(duracion) FROM pedidos
        JOIN comandas ON comandas.codigo = pedidos.codigoComanda
        WHERE pedidos.codigoComanda = :codigoComanda AND comandas.codigoMesa = :codigoMesa AND pedidos.estado = :estado");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigoComanda', $codigoComanda, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "En preparación");
        $consulta->execute();

        return $consulta->fetchColumn();
    }

    
}