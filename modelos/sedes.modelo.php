<?php

require_once "conexion.php";

class ModeloSede {

    /*=============================================
    MOSTRAR SEDE
    =============================================*/

    static public function mdlMostrarSede($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            echo "<script>console.log('Ejecutando consulta básica de sede');</script>";

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
            $stmt -> execute();

            $resultado = $stmt -> fetchAll();

            echo "<script>console.log('Resultados básicos:', " . json_encode($resultado) . ");</script>";

            // Ahora agregar los nombres de instituciones manualmente
            foreach($resultado as $key => $value) {
                $nombreInstitucion = self::mdlObtenerNombreInstitucion($value["institucion_id"]);
                $resultado[$key]["nombre_institucion"] = $nombreInstitucion;
                $resultado[$key]["estado_texto"] = ($value["estado"] == 1) ? "Activo" : "Inactivo";
            }

            echo "<script>console.log('Resultados con nombres:', " . json_encode($resultado) . ");</script>";

            return $resultado;
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    OBTENER DATOS COMPLETOS SEDE (CON NOMBRES)
    =============================================*/

    static public function mdlMostrarSedeCompleta($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT s.*, i.nombre as nombre_institucion 
                                                   FROM $tabla s 
                                                   LEFT JOIN institucion i ON s.institucion_id = i.id 
                                                   WHERE s.$item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT s.*, i.nombre as nombre_institucion 
                                                   FROM $tabla s 
                                                   LEFT JOIN institucion i ON s.institucion_id = i.id 
                                                   ORDER BY s.id DESC");
            $stmt -> execute();

            return $stmt -> fetchAll();
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR SEDE
    =============================================*/

    static public function mdlIngresarSede($tabla, $datos) {

        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(numero_sede, tipo_sede, nombre_sede, codigo_dane, consecutivo_dane, resolucion_creacion, fecha_creacion_sede, telefono_sede, celular_sede, fecha_registro, fecha_actualizacion, institucion_id, direccion, estado) VALUES (:numero_sede, :tipo_sede, :nombre_sede, :codigo_dane, :consecutivo_dane, :resolucion_creacion, :fecha_creacion_sede, :telefono_sede, :celular_sede, :fecha_registro, :fecha_actualizacion, :institucion_id, :direccion, :estado)");

            $stmt->bindParam(":numero_sede", $datos["numero_sede"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo_sede", $datos["tipo_sede"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_sede", $datos["nombre_sede"], PDO::PARAM_STR);
            $stmt->bindParam(":codigo_dane", $datos["codigo_dane"], PDO::PARAM_STR);
            $stmt->bindParam(":consecutivo_dane", $datos["consecutivo_dane"], PDO::PARAM_STR);
            $stmt->bindParam(":resolucion_creacion", $datos["resolucion_creacion"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_creacion_sede", $datos["fecha_creacion_sede"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono_sede", $datos["telefono_sede"], PDO::PARAM_STR);
            $stmt->bindParam(":celular_sede", $datos["celular_sede"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_registro", $datos["fecha_registro"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_actualizacion", $datos["fecha_actualizacion"], PDO::PARAM_STR);
            $stmt->bindParam(":institucion_id", $datos["institucion_id"], PDO::PARAM_INT);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

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
    BUSCAR INSTITUCIÓN POR NOMBRE
    =============================================*/

    static public function mdlBuscarInstitucionPorNombre($tabla, $nombreInstitucion) {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id FROM $tabla WHERE nombre = :nombreInstitucion");
            $stmt->bindParam(":nombreInstitucion", $nombreInstitucion, PDO::PARAM_STR);
            $stmt->execute();

            $resultado = $stmt->fetch();

            if($resultado) {
                return $resultado["id"];
            } else {
                return false;
            }

        } catch(PDOException $e) {
            error_log("Error buscando institución: " . $e->getMessage());
            return false;
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR CÓDIGO DANE
    =============================================*/

    static public function mdlVerificarCodigoDane($tabla, $codigoDane) {

        $stmt = Conexion::conectar()->prepare("SELECT codigo_dane FROM $tabla WHERE codigo_dane = :codigo_dane");
        $stmt->bindParam(":codigo_dane", $codigoDane, PDO::PARAM_STR);
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
    EDITAR SEDE
    =============================================*/

    static public function mdlEditarSede($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET numero_sede = :numero_sede, tipo_sede = :tipo_sede, nombre_sede = :nombre_sede, codigo_dane = :codigo_dane, consecutivo_dane = :consecutivo_dane, resolucion_creacion = :resolucion_creacion, fecha_creacion_sede = :fecha_creacion_sede, telefono_sede = :telefono_sede, celular_sede = :celular_sede, fecha_actualizacion = :fecha_actualizacion, institucion_id = :institucion_id, direccion = :direccion, estado = :estado WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":numero_sede", $datos["numero_sede"], PDO::PARAM_STR);
        $stmt->bindParam(":tipo_sede", $datos["tipo_sede"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_sede", $datos["nombre_sede"], PDO::PARAM_STR);
        $stmt->bindParam(":codigo_dane", $datos["codigo_dane"], PDO::PARAM_STR);
        $stmt->bindParam(":consecutivo_dane", $datos["consecutivo_dane"], PDO::PARAM_STR);
        $stmt->bindParam(":resolucion_creacion", $datos["resolucion_creacion"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_creacion_sede", $datos["fecha_creacion_sede"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono_sede", $datos["telefono_sede"], PDO::PARAM_STR);
        $stmt->bindParam(":celular_sede", $datos["celular_sede"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_actualizacion", $datos["fecha_actualizacion"], PDO::PARAM_STR);
        $stmt->bindParam(":institucion_id", $datos["institucion_id"], PDO::PARAM_INT);
        $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR REFERENCIAS DE SEDE
    =============================================*/

    static public function mdlVerificarReferenciasSede($sedeId) {
        $referencias = array();
        
        // Verificar sede_jornada
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM sede_jornada WHERE sede_id = :sede_id");
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        $sedeJornada = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($sedeJornada > 0) {
            $referencias[] = "Sede-Jornada ($sedeJornada registros)";
        }
        
        // Verificar roles_institucionales
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM roles_institucionales WHERE sede_id = :sede_id");
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        $rolesInstitucionales = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($rolesInstitucionales > 0) {
            $referencias[] = "Roles Institucionales ($rolesInstitucionales registros)";
        }
        
        return $referencias;
    }

    /*=============================================
    BORRAR SEDE
    =============================================*/

    static public function mdlBorrarSede($tabla, $datos) {

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
    OBTENER NOMBRE DE INSTITUCIÓN POR ID
    =============================================*/

    static public function mdlObtenerNombreInstitucion($idInstitucion) {

        $stmt = Conexion::conectar()->prepare("SELECT nombre FROM institucion WHERE id = :id");
        $stmt->bindParam(":id", $idInstitucion, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();

        if($resultado) {
            return $resultado["nombre"];
        } else {
            return "Institución no encontrada";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER TODAS LAS INSTITUCIONES PARA SELECT
    =============================================*/

    static public function mdlObtenerInstituciones() {

        $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM institucion WHERE estado = 1 ORDER BY nombre ASC");
        $stmt->execute();

        return $stmt->fetchAll();

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    CONTAR SEDES
    =============================================*/

    static public function mdlContarSedes($tabla) {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM $tabla");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>