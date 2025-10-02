<?php

require_once "conexion.php";
require_once __DIR__ . "/../servicios/ServicioAutorizacion.php";

class ModeloAsignacionDocenteAsignaturas
{

    /**
     * Obtiene instituciones según el contexto del usuario (con lógica de roles como usuarios).
     */
    static public function mdlObtenerTodasLasInstituciones()
    {
        // Inicia la lógica para mostrar instituciones según el rol
        $auth = ServicioAutorizacion::getInstance();

        // NIVEL 1: Si es Superadministrador, muestra todas las instituciones.
        if ($auth->esRolAdmin()) {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM institucion WHERE estado = 1 ORDER BY nombre");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        // NIVEL 2: Si tiene alcance institucional (Rector, Administrativo).
        } else if ($auth->tieneAlcanceInstitucional()) {
            // Obtenemos el ID del usuario que está realizando la consulta.
            $idUsuarioActual = $_SESSION['id_usuario'] ?? 0;

            // Consulta para obtener solo la institución del usuario actual
            $sql = "SELECT DISTINCT i.id, i.nombre FROM institucion i
                    INNER JOIN sede s ON i.id = s.institucion_id
                    INNER JOIN roles_institucionales ri ON s.id = ri.sede_id
                    WHERE ri.usuario_id = :id_usuario_actual
                      AND ri.estado = 'Activo'
                      AND i.estado = 1
                    ORDER BY i.nombre";

            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":id_usuario_actual", $idUsuarioActual, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        // NIVEL 3: Cualquier otro rol no tiene permiso para ver instituciones.
        } else {
            return [];
        }
    }

    /**
     * Obtiene el ID de institución del usuario actual.
     */
    static public function mdlObtenerInstitucionIdUsuario($usuarioId)
    {
        $stmt = Conexion::conectar()->prepare(
            "SELECT s.institucion_id 
             FROM roles_institucionales ri 
             INNER JOIN sede s ON ri.sede_id = s.id 
             WHERE ri.usuario_id = :usuario_id 
             AND ri.estado = 'Activo'
             LIMIT 1"
        );
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? (int)$resultado['institucion_id'] : null;
    }

    /**
     * SIN CAMBIOS FUNCIONALES: La consulta ya era correcta.
     */
    static public function mdlObtenerSedesInstitucion($institucionId)
    {
        $stmt = Conexion::conectar()->prepare("SELECT id, nombre_sede, tipo_sede FROM sede WHERE institucion_id = :institucion_id AND estado = 1");
        $stmt->bindParam(":institucion_id", $institucionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * MODIFICADO: Añade un filtro de seguridad por institución.
     * Previene que se acceda a docentes de una sede fuera del alcance del usuario.
     * El filtro se ignora si $institucionId es null (para Superadmin).
     */
    static public function mdlObtenerDocentesPorSede($sedeId, $institucionId)
    {
        $sql = "SELECT ri.id, u.nombres_usuario, u.apellidos_usuario, u.numero_documento, cd.id as cuerpo_docente_id, cd.max_horas_academicas_semanales
                FROM roles_institucionales ri
                INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
                INNER JOIN roles r ON ri.rol_id = r.id_rol
                INNER JOIN sede s ON ri.sede_id = s.id
                LEFT JOIN cuerpo_docente cd ON ri.id = cd.rol_institucional_id
                WHERE ri.sede_id = :sede_id 
                  AND ri.estado = 'Activo' 
                  AND r.nombre_rol = 'Docente'
                  AND (:institucion_id IS NULL OR s.institucion_id = :institucion_id)";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->bindParam(":institucion_id", $institucionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * MODIFICADO: Añade filtro de seguridad por institución.
     */
    static public function mdlObtenerAsignaturasPorSede($sedeId, $institucionId)
    {
        $sql = "SELECT DISTINCT ec.id, a.nombre as asignatura, ar.nombre as area
                FROM estructura_curricular ec
                INNER JOIN asignatura a ON ec.asignatura_id = a.id
                INNER JOIN area ar ON a.area_id = ar.id
                INNER JOIN oferta_academica oa ON ec.oferta_academica_id = oa.id
                INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                INNER JOIN sede s ON sj.sede_id = s.id
                WHERE sj.sede_id = :sede_id
                  AND (:institucion_id IS NULL OR s.institucion_id = :institucion_id)";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->bindParam(":institucion_id", $institucionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * MODIFICADO: Añade filtro de seguridad por institución.
     */
    static public function mdlObtenerAsignacionesDocente($cuerpoDocenteId, $institucionId)
    {
        $sql = "SELECT dah.id, a.nombre as asignatura, ar.nombre as area, dah.asignatura_estructura_curricular_id as estructura_curricular_id
                FROM docente_asignaturas_habilitadas dah
                INNER JOIN cuerpo_docente cd ON dah.cuerpo_docente_id = cd.id
                INNER JOIN roles_institucionales ri ON cd.rol_institucional_id = ri.id
                INNER JOIN sede s ON ri.sede_id = s.id
                INNER JOIN estructura_curricular ec ON dah.asignatura_estructura_curricular_id = ec.id
                INNER JOIN asignatura a ON ec.asignatura_id = a.id
                INNER JOIN area ar ON a.area_id = ar.id
                WHERE dah.cuerpo_docente_id = :cuerpo_docente_id
                  AND (:institucion_id IS NULL OR s.institucion_id = :institucion_id)";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->bindParam(":institucion_id", $institucionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==================================================================
       MÉTODOS DE ESCRITURA: No requieren filtro de institución porque
       la seguridad se aplica en el controlador antes de invocarlos.
    ================================================================== */

    static public function mdlObtenerCuerpoDocentePorRol($rolInstitucionalId) {
        $stmt = Conexion::conectar()->prepare("SELECT id FROM cuerpo_docente WHERE rol_institucional_id = :rol_institucional_id");
        $stmt->bindParam(":rol_institucional_id", $rolInstitucionalId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado ? $resultado['id'] : false;
    }

    static public function mdlCrearCuerpoDocente($rolInstitucionalId, $maxHoras, $observaciones) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO cuerpo_docente (rol_institucional_id, max_horas_academicas_semanales, observaciones) VALUES (:rol, :max_horas, :obs)");
        $stmt->bindParam(":rol", $rolInstitucionalId, PDO::PARAM_INT);
        $stmt->bindParam(":max_horas", $maxHoras, PDO::PARAM_INT);
        $stmt->bindParam(":obs", $observaciones, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return Conexion::conectar()->lastInsertId();
        }
        return "error";
    }

    static public function mdlAsignarAsignatura($cuerpoDocenteId, $estructuraCurricularId) {
        $stmt = Conexion::conectar()->prepare("INSERT IGNORE INTO docente_asignaturas_habilitadas (cuerpo_docente_id, asignatura_estructura_curricular_id) VALUES (:cuerpo_id, :estructura_id)");
        $stmt->bindParam(":cuerpo_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->bindParam(":estructura_id", $estructuraCurricularId, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    static public function mdlEliminarAsignacion($asignacionId) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM docente_asignaturas_habilitadas WHERE id = :id");
        $stmt->bindParam(":id", $asignacionId, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    static public function mdlActualizarHorasSemanales($cuerpoDocenteId, $horasSemanales) {
        $stmt = Conexion::conectar()->prepare("UPDATE cuerpo_docente SET max_horas_academicas_semanales = :horas WHERE id = :id");
        $stmt->bindParam(":horas", $horasSemanales, PDO::PARAM_INT);
        $stmt->bindParam(":id", $cuerpoDocenteId, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }
}