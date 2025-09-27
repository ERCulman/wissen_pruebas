<?php

require_once "conexion.php";

class ModeloGrado {

    /*=============================================
    MOSTRAR GRADO
    =============================================*/

    static public function mdlMostrarGrado($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            echo "<script>console.log('Ejecutando consulta básica de grado');</script>";

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
            $stmt -> execute();

            $resultado = $stmt -> fetchAll();

            echo "<script>console.log('Resultados básicos:', " . json_encode($resultado) . ");</script>";

            // Ahora agregar los nombres de niveles educativos manualmente
            foreach($resultado as $key => $value) {
                $nombreNivelEducativo = self::mdlObtenerNombreNivelEducativo($value["nivel_educativo_id"]);
                $resultado[$key]["nombre_nivel_educativo"] = $nombreNivelEducativo;
            }

            echo "<script>console.log('Resultados con nombres:', " . json_encode($resultado) . ");</script>";

            return $resultado;
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    OBTENER DATOS COMPLETOS GRADO (CON NOMBRES)
    =============================================*/

    static public function mdlMostrarGradoCompleto($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT g.*, n.nombre as nombre_nivel_educativo 
                                                   FROM $tabla g 
                                                   LEFT JOIN nivel_educativo n ON g.nivel_educativo_id = n.id 
                                                   WHERE g.$item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT g.*, n.nombre as nombre_nivel_educativo 
                                                   FROM $tabla g 
                                                   LEFT JOIN nivel_educativo n ON g.nivel_educativo_id = n.id 
                                                   ORDER BY g.id DESC");
            $stmt -> execute();

            return $stmt -> fetchAll();
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR GRADO
    =============================================*/

    static public function mdlIngresarGrado($tabla, $datos) {

        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(numero, nombre, nivel_educativo_id) VALUES (:numero, :nombre, :nivel_educativo_id)");

            $stmt->bindParam(":numero", $datos["numero"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":nivel_educativo_id", $datos["nivel_educativo_id"], PDO::PARAM_INT);

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
    BUSCAR NIVEL EDUCATIVO POR NOMBRE
    =============================================*/

    static public function mdlBuscarNivelEducativoPorNombre($tabla, $nombreNivelEducativo) {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id FROM $tabla WHERE nombre = :nombreNivelEducativo");
            $stmt->bindParam(":nombreNivelEducativo", $nombreNivelEducativo, PDO::PARAM_STR);
            $stmt->execute();

            $resultado = $stmt->fetch();

            if($resultado) {
                return $resultado["id"];
            } else {
                return false;
            }

        } catch(PDOException $e) {
            error_log("Error buscando nivel educativo: " . $e->getMessage());
            return false;
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR NÚMERO DE GRADO
    =============================================*/

    static public function mdlVerificarNumeroGrado($tabla, $numeroGrado) {

        $stmt = Conexion::conectar()->prepare("SELECT numero FROM $tabla WHERE numero = :numero");
        $stmt->bindParam(":numero", $numeroGrado, PDO::PARAM_STR);
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
    EDITAR GRADO
    =============================================*/

    static public function mdlEditarGrado($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET numero = :numero, nombre = :nombre, nivel_educativo_id = :nivel_educativo_id WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":numero", $datos["numero"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":nivel_educativo_id", $datos["nivel_educativo_id"], PDO::PARAM_INT);

        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    BORRAR GRADO
    =============================================*/

    static public function mdlBorrarGrado($tabla, $datos) {

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
    OBTENER NOMBRE DE NIVEL EDUCATIVO POR ID
    =============================================*/

    static public function mdlObtenerNombreNivelEducativo($idNivelEducativo) {

        $stmt = Conexion::conectar()->prepare("SELECT nombre FROM nivel_educativo WHERE id = :id");
        $stmt->bindParam(":id", $idNivelEducativo, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();

        if($resultado) {
            return $resultado["nombre"];
        } else {
            return "Nivel educativo no encontrado";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR REFERENCIAS DE GRADO
    =============================================*/

    static public function mdlVerificarReferenciasGrado($gradoId) {
        $referencias = array();
        
        // Verificar oferta_academica
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM oferta_academica WHERE grado_id = :grado_id");
        $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
        $stmt->execute();
        $ofertaAcademica = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($ofertaAcademica > 0) {
            $referencias[] = "Oferta Académica ($ofertaAcademica registros)";
        }
        
        return $referencias;
    }

    /*=============================================
    OBTENER TODOS LOS NIVELES EDUCATIVOS PARA SELECT
    =============================================*/

    static public function mdlObtenerNivelesEducativos() {

        $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM nivel_educativo ORDER BY nombre ASC");
        $stmt->execute();

        return $stmt->fetchAll();

        $stmt->close();
        $stmt = null;
    }
}
?>