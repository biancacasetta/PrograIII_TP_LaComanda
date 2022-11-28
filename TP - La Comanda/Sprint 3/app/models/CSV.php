<?php

require_once 'ItemMenu.php';

    class CSV
    {
        public static function crearCSV()
        {
            $retorno = false;
            $items = ItemMenu::obtenerTodos();
            $ruta = "./Media/menu.csv";
            $file = fopen($ruta, "w+");
            foreach($items as $item)
            {
                if($file)
                {
                    fwrite($file, implode(",", (array)$item).PHP_EOL); 
                }                           
            }

            fclose($file); 
            if(filesize($ruta) > 0)
            {
                $retorno = true;
            }

            return $retorno;
        }

        public static function cargarCSV($file)
        {
            $retorno = false;
            if(file_exists($file))
            {
                ItemMenu::limpiarItemsBackup();
                $archivo = fopen($file, "r");
                try
                {
                    while(!feof($archivo))
                    {
                        $lineaItem = fgets($archivo);                        
                        if(!empty($lineaItem))
                        {         
                            $item = new ItemMenu();

                            $datosItem = explode(",", $lineaItem);
                            $item->id = $datosItem[0];
                            $item->nombre = $datosItem[1];
                            $item->precio=$datosItem[2];
                            $item->perfil = $datosItem[3];
                            $item->crearItemMenuConId();                       
                        }
                    }
                    $retorno = true;
                }
                catch(Exception $ex)
                {
                    echo "Error al cargar el archivo -".$ex->getMessage();
                }
                finally
                {
                    fclose($archivo);
                    return $retorno;
                }
            }
        }
    }
?>