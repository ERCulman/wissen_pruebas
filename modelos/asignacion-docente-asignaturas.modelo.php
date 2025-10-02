<?php

require_once "conexion.php";

class ModeloAsignacionDocenteAsignaturas {

    static public function mdlValidarAccesoUsuario($idUsuario) {
        $stmt = Conexion::conectar()->prepare("SELECT ri.sede_id, s.tipo_sede, s.institucion_id, i.id_usuario_representante, r.nombre_rol
                                               FROM roles_institucionales ri
                                               INNER JOIN sede s ON ri.sede_id = s.id
                                               INNER JOIN institucion i ON s.institucion_id = i.id
                                               INNER JOIN roles r ON ri.rol_id = r.id_rol
                                               WHERE ri.usuario_id = :usuario_id AND ri.estado = 'Activo'");
        $stmt->bindParam(":usuario_id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlObtenerSedesInstitucion($institucionId) {
        $stmt = Conexion::conectar()->prepare("SELECT id, nombre_sede, tipo_sede FROM sede WHERE institucion_id = :institucion_id AND estado = 1");
        $stmt->bindParam(":institucion_id", $institucionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlObtenerDocentesPorSede($sedeId) {
        $stmt = Conexion::conectar()->prepare("SELECT ri.id, u.nombres_usuario, u.apellidos_usuario, u.numero_documento, cd.id as cuerpo_docente_id, cd.max_horas_academicas_semanales
                                               FROM roles_institucionales ri
                                               INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
                                               INNER JOIN roles r ON ri.rol_id = r.id_rol
                                               LEFT JOIN cuerpo_docente cd ON ri.id = cd.rol_institucional_id
                                               WHERE ri.sede_id = :sede_id AND ri.estado = 'Activo' AND r.nombre_rol = 'Docente'");
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlObtenerAsignaturasPorSede($sedeId) {
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT ec.id, a.nombre as asignatura, ar.nombre as area
                                               FROM estructura_curricular ec
                                               INNER JOIN asignatura a ON ec.asignatura_id = a.id
                                               INNER JOIN area ar ON a.area_id = ar.id
                                               INNER JOIN oferta_academica oa ON ec.oferta_academica_id = oa.id
                                               INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                               WHERE sj.sede_id = :sede_id");
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlObtenerCuerpoDocentePorRol($rolInstitucionalId) {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT id FROM cuerpo_docente WHERE rol_institucional_id = :rol_institucional_id");
            $stmt->bindParam(":rol_institucional_id", $rolInstitucionalId, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch();
            return $resultado ? $resultado['id'] : false;
        } catch(Exception $e) {
            error_log("Error en mdlObtenerCuerpoDocentePorRol: " . $e->getMessage());
            return false;
        }
    }

    static public function mdlCrearCuerpoDocente($rolInstitucionalId, $maxHoras, $observaciones) {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO cuerpo_docente (rol_institucional_id, max_horas_academicas_semanales, observaciones) 
                                                   VALUES (:rol_institucional_id, :max_horas, :observaciones)");
            $stmt->bindParam(":rol_institucional_id", $rolInstitucionalId, PDO::PARAM_INT);
            $stmt->bindParam(":max_horas", $maxHoras, PDO::PARAM_INT);
            $stmt->bindParam(":observaciones", $observaciones, PDO::PARAM_STR);
            
            if($stmt->execute()) {
                return Conexion::conectar()->lastInsertId();
            } else {
                return "error";
            }
        } catch(Exception $e) {
            return "error";
        }
    }

    static public function mdlAsignarAsignatura($cuerpoDocenteId, $estructuraCurricularId) {
        try {
            // Verificar que la estructura curricular existe
            $stmtVerificar = Conexion::conectar()->prepare("SELECT id FROM estructura_curricular WHERE id = :estructura_id");
            $stmtVerificar->bindParam(":estructura_id", $estructuraCurricularId, PDO::PARAM_INT);
            $stmtVerificar->execute();
            
            if(!$stmtVerificar->fetch()) {
                error_log("Estructura curricular no existe: " . $estructuraCurricularId);
                return "error";
            }
            
            $stmt = Conexion::conectar()->prepare("INSERT IGNORE INTO docente_asignaturas_habilitadas (cuerpo_docente_id, asignatura_estructura_curricular_id) 
                                                   VALUES (:cuerpo_docente_id, :estructura_curricular_id)");
            $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
            $stmt->bindParam(":estructura_curricular_id", $estructuraCurricularId, PDO::PARAM_INT);
            
            if($stmt->execute()) {
                return "ok";
            } else {
                error_log("Error ejecutando INSERT en mdlAsignarAsignatura");
                return "error";
            }
        } catch(Exception $e) {
            error_log("Excepción en mdlAsignarAsignatura: " . $e->getMessage());
            return "error";
        }
    }

    static public function mdlObtenerAsignacionesDocente($cuerpoDocenteId) {
        $stmt = Conexion::conectar()->prepare("SELECT dah.id, a.nombre as asignatura, ar.nombre as area, dah.asignatura_estructura_curricular_id as estructura_curricular_id
                                               FROM docente_asignaturas_habilitadas dah
                                               INNER JOIN estructura_curricular ec ON dah.asignatura_estructura_curricular_id = ec.id
                                               INNER JOIN asignatura a ON ec.asignatura_id = a.id
                                               INNER JOIN area ar ON a.area_id = ar.id
                                               WHERE dah.cuerpo_docente_id = :cuerpo_docente_id");
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlEliminarAsignacion($asignacionId) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM docente_asignaturas_habilitadas WHERE id = :id");
        $stmt->bindParam(":id", $asignacionId, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    static public function mdlActualizarHorasSemanales($cuerpoDocenteId, $horasSemanales) {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE cuerpo_docente SET max_horas_academicas_semanales = :horas WHERE id = :id");
            $stmt->bindParam(":horas", $horasSemanales, PDO::PARAM_INT);
            $stmt->bindParam(":id", $cuerpoDocenteId, PDO::PARAM_INT);
            
            if($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch(Exception $e) {
            return "error";
        }
    }
}