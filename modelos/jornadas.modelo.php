<?php

require_once "conexion.php";

class ModeloJornada {

    /*=============================================
    MOSTRAR JORNADA
    =============================================*/

    static public function mdlMostrarJornada($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            echo "<script>console.log('Ejecutando consulta básica de jornada');</script>";

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
            $stmt -> execute();

            $resultado = $stmt -> fetchAll();

            echo "<script>console.log('Resultados básicos:', " . json_encode($resultado) . ");</script>";

            return $resultado;
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR JORNADA
    =============================================*/

    static public function mdlIngresarJornada($tabla, $datos) {

        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(codigo, nombre) VALUES (:codigo, :nombre)");

            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);

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
    VERIFICAR CÓDIGO JORNADA
    =============================================*/

    static public function mdlVerificarCodigoJornada($tabla, $codigo) {

        $stmt = Conexion::conectar()->prepare("SELECT codigo FROM $tabla WHERE codigo = :codigo");
        $stmt->bindParam(":codigo", $codigo, PDO::PARAM_STR);
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
    EDITAR JORNADA
    =============================================*/

    static public function mdlEditarJornada($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET codigo = :codigo, nombre = :nombre WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR REFERENCIAS DE JORNADA
    =============================================*/

    static public function mdlVerificarReferenciasJornada($jornadaId) {
        $referencias = array();
        
        // Verificar sede_jornada
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM sede_jornada WHERE jornada_id = :jornada_id");
        $stmt->bindParam(":jornada_id", $jornadaId, PDO::PARAM_INT);
        $stmt->execute();
        $sedeJornada = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($sedeJornada > 0) {
            $referencias[] = "Sede-Jornada ($sedeJornada registros)";
        }
        
        return $referencias;
    }

    /*=============================================
    BORRAR JORNADA
    =============================================*/

    static public function mdlBorrarJornada($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    ACTUALIZAR ÚLTIMO LOGIN
    =============================================*/

    static public function mdlActualizarUltimoLogin($tabla, $item1, $valor1, $item2, $valor2) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
        $stmt -> bindParam(":".$item1, $valor1, PDO::PARAM_STR);
        $stmt -> bindParam(":".$item2, $valor2, PDO::PARAM_STR);
        $stmt -> execute();

        return "ok";

        $stmt -> close();
        $stmt = null;
    }
}
?>