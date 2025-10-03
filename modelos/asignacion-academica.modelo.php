<?php

require_once "conexion.php";
require_once __DIR__ . "/../servicios/ServicioAutorizacion.php";

class ModeloAsignacionAcademica {

    /*=============================================
    OBTENER SEDES (SIN CAMBIOS, YA ERA COMPATIBLE)
    =============================================*/
    static public function mdlObtenerSedes() {
        $auth = ServicioAutorizacion::getInstance();
        if ($auth->esRolAdmin()) {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre_sede FROM sede WHERE estado = 1 ORDER BY nombre_sede ASC");
        } else if ($auth->tieneAlcanceInstitucional()) {
            $idUsuarioActual = $_SESSION['id_usuario'] ?? 0;
            $sql = "SELECT DISTINCT s.id, s.nombre_sede 
                    FROM sede s
                    INNER JOIN roles_institucionales ri ON s.id = ri.sede_id
                    WHERE ri.usuario_id = :id_usuario_actual AND ri.estado = 'Activo' AND s.estado = 1
                    ORDER BY s.nombre_sede ASC";
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":id_usuario_actual", $idUsuarioActual, PDO::PARAM_INT);
        } else {
            return array();
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    OBTENER DOCENTES POR SEDE (SIN CAMBIOS, YA ERA COMPATIBLE)
    =============================================*/
    static public function mdlObtenerDocentesPorSede($sedeId) {
        $sql = "SELECT ri.id, u.nombres_usuario, u.apellidos_usuario, u.numero_documento, 
                       cd.id as cuerpo_docente_id, cd.max_horas_academicas_semanales
                FROM roles_institucionales ri
                INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
                INNER JOIN roles r ON ri.rol_id = r.id_rol
                LEFT JOIN cuerpo_docente cd ON ri.id = cd.rol_institucional_id
                WHERE ri.sede_id = :sede_id AND ri.estado = 'Activo' AND r.nombre_rol = 'Docente'
                ORDER BY u.apellidos_usuario, u.nombres_usuario";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    OBTENER GRADOS POR SEDE (SIN CAMBIOS, YA ERA COMPATIBLE)
    =============================================*/
    static public function mdlObtenerGradosPorSede($sedeId) {
        $sql = "SELECT DISTINCT g.id, g.nombre, g.numero
                FROM grado g
                INNER JOIN oferta_academica oa ON g.id = oa.grado_id
                INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                WHERE sj.sede_id = :sede_id
                ORDER BY g.numero";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    OBTENER GRUPOS POR GRADO Y SEDE (CORREGIDO Y FILTRADO)
    =============================================*/
    static public function mdlObtenerGruposPorGradoSede($gradoId, $sedeId) {
        // AJUSTE: Se añaden las nuevas condiciones al WHERE para filtrar los grupos
        $sql = "SELECT g.id, g.nombre, g.tipo, g.grupo_padre_id,
                   g.nombre as nombre_completo
            FROM grupo g
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
            WHERE oa.grado_id = :grado_id 
              AND sj.sede_id = :sede_id
              AND g.curso_id IS NOT NULL 
              AND g.tipo = 'Regular'
            ORDER BY g.nombre";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    OBTENER ASIGNATURAS POR GRADO (SIN CAMBIOS, YA ERA COMPATIBLE)
    =============================================*/
    static public function mdlObtenerAsignaturasPorGrado($gradoId, $sedeId) {
        $sql = "SELECT ec.id, a.nombre as asignatura, ar.nombre as area, 
                       ec.intensidad_horaria_semanal
                FROM estructura_curricular ec
                INNER JOIN asignatura a ON ec.asignatura_id = a.id
                INNER JOIN area ar ON a.area_id = ar.id
                INNER JOIN oferta_academica oa ON ec.oferta_academica_id = oa.id
                INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                WHERE oa.grado_id = :grado_id AND sj.sede_id = :sede_id
                ORDER BY ar.nombre, a.nombre";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    OBTENER ASIGNACIONES DEL DOCENTE (CORREGIDO)
    =============================================*/
    static public function mdlObtenerAsignacionesDocente($cuerpoDocenteId) {
        // AJUSTE: Se usa la misma lógica del AJAX original para 'nombre_mostrar'
        $sql = "SELECT aa.id, a.nombre as asignatura, ar.nombre as area,
                       aa.intensidad_horaria_semanal, g.nombre as nombre_mostrar
                FROM asignacion_academica aa
                INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                INNER JOIN asignatura a ON ec.asignatura_id = a.id
                INNER JOIN area ar ON a.area_id = ar.id
                INNER JOIN grupo g ON aa.grupo_id = g.id
                WHERE aa.cuerpo_docente_id = :cuerpo_docente_id AND aa.estado = 'Activa'
                ORDER BY ar.nombre, a.nombre";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    CREAR ASIGNACIÓN ACADÉMICA (SIN CAMBIOS, YA ERA COMPATIBLE)
    =============================================*/
    static public function mdlCrearAsignacion($datos) {
        $sql = "INSERT INTO asignacion_academica 
                (cuerpo_docente_id, estructura_curricular_id, grupo_id, periodo_academico_id, intensidad_horaria_semanal) 
                VALUES (:cuerpo_docente_id, :estructura_curricular_id, :grupo_id, :periodo_academico_id, :intensidad_horaria_semanal)";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":cuerpo_docente_id", $datos["cuerpo_docente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":estructura_curricular_id", $datos["estructura_curricular_id"], PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id", $datos["grupo_id"], PDO::PARAM_INT);
        $stmt->bindParam(":periodo_academico_id", $datos["periodo_academico_id"], PDO::PARAM_INT);
        $stmt->bindParam(":intensidad_horaria_semanal", $datos["intensidad_horaria_semanal"], PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    /*=============================================
    ELIMINAR ASIGNACIÓN ACADÉMICA (SIN CAMBIOS, YA ERA COMPATIBLE)
    =============================================*/
    static public function mdlEliminarAsignacion($asignacionId) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM asignacion_academica WHERE id = :id");
        $stmt->bindParam(":id", $asignacionId, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    /*=============================================
    OBTENER PERIODO ACADÉMICO ACTIVO (CORREGIDO)
    =============================================*/
    static public function mdlObtenerPeriodoActivo() {
        // AJUSTE: Se usa la misma lógica del AJAX original para asegurar compatibilidad.
        // Aunque buscar por fecha es más robusto, restauramos la lógica original para que funcione.
        $stmt = Conexion::conectar()->prepare("SELECT id FROM periodo ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado ? $resultado['id'] : null;
    }

    /*=============================================
    CALCULAR HORAS ASIGNADAS (SIN CAMBIOS, YA ERA COMPATIBLE)
    =============================================*/
    static public function mdlCalcularHorasAsignadas($cuerpoDocenteId) {
        // Esta consulta es simple y era igual en ambos archivos.
        $sql = "SELECT SUM(aa.intensidad_horaria_semanal) as total_horas
                FROM asignacion_academica aa
                WHERE aa.cuerpo_docente_id = :cuerpo_docente_id AND aa.estado = 'Activa'";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado ? (int)$resultado['total_horas'] : 0;
    }
}