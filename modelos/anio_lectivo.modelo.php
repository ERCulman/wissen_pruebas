<?php

require_once "conexion.php";

class ModeloAnioLectivo {

    /*=============================================
    MOSTRAR AÑO LECTIVO
    =============================================*/

    static public function mdlMostrarAnioLectivo($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
            $stmt -> execute();

            return $stmt -> fetchAll();
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    BUSCAR AÑO LECTIVO POR NOMBRE
    =============================================*/

    static public function mdlBuscarAnioLectivoPorNombre($tabla, $nombre) {

        $stmt = Conexion::conectar()->prepare("SELECT id FROM $tabla WHERE nombre = :nombre");
        $stmt->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch();

        return ($resultado) ? $resultado["id"] : false;

        $stmt->close();
        $stmt = null;
    }
}
?>