<?php

require_once "conexion.php";

class ModeloInstitucion {

    /*=============================================
    MOSTRAR INSTITUCIÓN
    =============================================*/

    static public function mdlMostrarInstitucion($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            echo "<script>console.log('Ejecutando consulta básica de institución');</script>";

            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
            $stmt -> execute();

            $resultado = $stmt -> fetchAll();

            echo "<script>console.log('Resultados básicos:', " . json_encode($resultado) . ");</script>";

            // Ahora agregar los nombres de usuarios manualmente
            foreach($resultado as $key => $value) {
                $nombreUsuario = self::mdlObtenerNombreUsuario($value["id_usuario_representante"]);
                $resultado[$key]["nombre_representante"] = $nombreUsuario;
                $resultado[$key]["estado_texto"] = ($value["estado"] == 1) ? "Activo" : "Inactivo";
            }

            echo "<script>console.log('Resultados con nombres:', " . json_encode($resultado) . ");</script>";

            return $resultado;
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    OBTENER DATOS COMPLETOS INSTITUCIÓN (CON NOMBRES)
    =============================================*/

    static public function mdlMostrarInstitucionCompleta($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT i.*, u.nombres_usuario, u.apellidos_usuario 
                                                   FROM $tabla i 
                                                   LEFT JOIN usuarios u ON i.id_usuario_representante = u.id_usuario 
                                                   WHERE i.$item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT i.*, u.nombres_usuario, u.apellidos_usuario 
                                                   FROM $tabla i 
                                                   LEFT JOIN usuarios u ON i.id_usuario_representante = u.id_usuario 
                                                   ORDER BY i.id DESC");
            $stmt -> execute();

            return $stmt -> fetchAll();
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR INSTITUCIÓN
    =============================================*/

    static public function mdlIngresarInstitucion($tabla, $datos) {

        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, codigo_dane, nit, resolucion_creacion, direccion, email, telefono, cantidad_sedes, id_usuario_representante, estado) VALUES (:nombre, :codigo_dane, :nit, :resolucion_creacion, :direccion, :email, :telefono, :cantidad_sedes, :id_usuario_representante, :estado)");

            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":codigo_dane", $datos["codigo_dane"], PDO::PARAM_STR);
            $stmt->bindParam(":nit", $datos["nit"], PDO::PARAM_STR);
            $stmt->bindParam(":resolucion_creacion", $datos["resolucion_creacion"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":cantidad_sedes", $datos["cantidad_sedes"], PDO::PARAM_INT);
            $stmt->bindParam(":id_usuario_representante", $datos["id_usuario_representante"], PDO::PARAM_INT);
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
    BUSCAR USUARIO POR NOMBRE COMPLETO
    =============================================*/

    static public function mdlBuscarUsuarioPorNombre($tabla, $nombreCompleto) {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id_usuario FROM $tabla WHERE CONCAT(nombres_usuario, ' ', apellidos_usuario) = :nombreCompleto");
            $stmt->bindParam(":nombreCompleto", $nombreCompleto, PDO::PARAM_STR);
            $stmt->execute();

            $resultado = $stmt->fetch();

            if($resultado) {
                return $resultado["id_usuario"];
            } else {
                return false;
            }

        } catch(PDOException $e) {
            error_log("Error buscando usuario: " . $e->getMessage());
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
    EDITAR INSTITUCIÓN
    =============================================*/

    static public function mdlEditarInstitucion($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, codigo_dane = :codigo_dane, nit = :nit, resolucion_creacion = :resolucion_creacion, direccion = :direccion, email = :email, telefono = :telefono, cantidad_sedes = :cantidad_sedes, id_usuario_representante = :id_usuario_representante, estado = :estado WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":codigo_dane", $datos["codigo_dane"], PDO::PARAM_STR);
        $stmt->bindParam(":nit", $datos["nit"], PDO::PARAM_STR);
        $stmt->bindParam(":resolucion_creacion", $datos["resolucion_creacion"], PDO::PARAM_STR);
        $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
        $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
        $stmt->bindParam(":cantidad_sedes", $datos["cantidad_sedes"], PDO::PARAM_INT);
        $stmt->bindParam(":id_usuario_representante", $datos["id_usuario_representante"], PDO::PARAM_INT);
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
    VERIFICAR REFERENCIAS DE INSTITUCIÓN
    =============================================*/

    static public function mdlVerificarReferenciasInstitucion($institucionId) {
        $referencias = array();
        
        // Verificar sede
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM sede WHERE institucion_id = :institucion_id");
        $stmt->bindParam(":institucion_id", $institucionId, PDO::PARAM_INT);
        $stmt->execute();
        $sede = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($sede > 0) {
            $referencias[] = "Sedes ($sede registros)";
        }
        
        return $referencias;
    }

    /*=============================================
    BORRAR INSTITUCIÓN
    =============================================*/

    static public function mdlBorrarInstitucion($tabla, $datos) {

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

    /*=============================================
    OBTENER NOMBRE DE USUARIO POR ID
    =============================================*/

    static public function mdlObtenerNombreUsuario($idUsuario) {

        $stmt = Conexion::conectar()->prepare("SELECT nombres_usuario, apellidos_usuario FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();

        if($resultado) {
            return $resultado["nombres_usuario"] . " " . $resultado["apellidos_usuario"];
        } else {
            return "Usuario no encontrado";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER ESTADO TEXTO
    =============================================*/

    static public function mdlObtenerEstadoTexto($estado) {
        return ($estado == 1) ? "Activo" : "Inactivo";
    }

    /*=============================================
    CONTAR INSTITUCIONES
    =============================================*/

    static public function mdlContarInstituciones($tabla) {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM $tabla");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>