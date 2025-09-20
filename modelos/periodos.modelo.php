<?php

require_once "conexion.php";

class ModeloPeriodo {

    /*=============================================
    MOSTRAR PERIODO
    =============================================*/

    static public function mdlMostrarPeriodo($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            echo "<script>console.log('Ejecutando consulta básica de periodo');</script>";

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
            $stmt -> execute();

            $resultado = $stmt -> fetchAll();

            echo "<script>console.log('Resultados básicos:', " . json_encode($resultado) . ");</script>";

            // Ahora agregar los nombres de anios lectivos manualmente
            foreach($resultado as $key => $value) {
                $nombreAnioLectivo = self::mdlObtenerNombreAnioLectivo($value["anio_lectivo_id"]);
                $resultado[$key]["nombre_anio_lectivo"] = $nombreAnioLectivo;
            }

            echo "<script>console.log('Resultados con nombres:', " . json_encode($resultado) . ");</script>";

            return $resultado;
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    OBTENER DATOS COMPLETOS PERIODO (CON NOMBRES)
    =============================================*/

    static public function mdlMostrarPeriodoCompleto($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT p.*, a.anio as nombre_anio_lectivo 
                                                   FROM $tabla p 
                                                   LEFT JOIN anio_lectivo a ON p.anio_lectivo_id = a.id 
                                                   WHERE p.$item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT p.*, a.anio as nombre_anio_lectivo 
                                                   FROM $tabla p 
                                                   LEFT JOIN anio_lectivo a ON p.anio_lectivo_id = a.id 
                                                   ORDER BY p.id DESC");
            $stmt -> execute();

            return $stmt -> fetchAll();
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR PERIODO
    =============================================*/

    static public function mdlIngresarPeriodo($tabla, $datos) {

        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, fecha_inicio, fecha_fin, anio_lectivo_id) VALUES (:nombre, :fecha_inicio, :fecha_fin, :anio_lectivo_id)");

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_fin", $datos["fecha_fin"], PDO::PARAM_STR);
            $stmt->bindParam(":anio_lectivo_id", $datos["anio_lectivo_id"], PDO::PARAM_INT);

            if($stmt->execute()) {
                return "ok";
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . $errorInfo[2]);
                return "error: " . $errorInfo[2];
            }

        } catch(PDOException $e) {
            error_log("Error PDO: " . $e->getMessage());
            return "error: " . $e->getMessage();
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    BUSCAR AÑO LECTIVO POR NOMBRE
    =============================================*/

    static public function mdlBuscarAnioLectivoPorNombre($tabla, $nombreAnioLectivo) {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id FROM $tabla WHERE anio = :nombreAnioLectivo");
            $stmt->bindParam(":nombreAnioLectivo", $nombreAnioLectivo, PDO::PARAM_STR);
            $stmt->execute();

            $resultado = $stmt->fetch();

            if($resultado) {
                return $resultado["id"];
            } else {
                return false;
            }

        } catch(PDOException $e) {
            error_log("Error buscando año lectivo: " . $e->getMessage());
            return false;
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR NOMBRE DE PERIODO
    =============================================*/

    static public function mdlVerificarNombrePeriodo($tabla, $nombrePeriodo) {

        $stmt = Conexion::conectar()->prepare("SELECT nombre FROM $tabla WHERE nombre = :nombre");
        $stmt->bindParam(":nombre", $nombrePeriodo, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch();

        if($resultado) {
            return true;
        } else {
            return false;
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    EDITAR PERIODO
    =============================================*/

    static public function mdlEditarPeriodo($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, anio_lectivo_id = :anio_lectivo_id WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_fin", $datos["fecha_fin"], PDO::PARAM_STR);
        $stmt->bindParam(":anio_lectivo_id", $datos["anio_lectivo_id"], PDO::PARAM_INT);

        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    BORRAR PERIODO
    =============================================*/

    static public function mdlBorrarPeriodo($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER NOMBRE DE AÑO LECTIVO POR ID
    =============================================*/

    static public function mdlObtenerNombreAnioLectivo($idAnioLectivo) {

        $stmt = Conexion::conectar()->prepare("SELECT anio FROM anio_lectivo WHERE id = :id");
        $stmt->bindParam(":id", $idAnioLectivo, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();

        if($resultado) {
            return $resultado["anio"];
        } else {
            return "Año lectivo no encontrado";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER TODOS LOS AÑOS LECTIVOS PARA SELECT
    =============================================*/

    static public function mdlObtenerAniosLectivos() {

        $stmt = Conexion::conectar()->prepare("SELECT id, anio as nombre FROM anio_lectivo ORDER BY anio ASC");
        $stmt->execute();

        return $stmt->fetchAll();

        $stmt->close();
        $stmt = null;
    }
}
?>