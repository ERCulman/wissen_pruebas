<?php

require_once "conexion.php";
require_once __DIR__ . "/../servicios/ServicioAutorizacion.php";

class ModeloAsistencia {

    /*=============================================
    OBTENER PERÍODOS ACADÉMICOS
    =============================================*/
    static public function mdlObtenerPeriodos() {
        $sql = "SELECT id, nombre, fecha_inicio, fecha_fin 
                FROM periodo 
                ORDER BY fecha_inicio ASC";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    OBTENER ASIGNACIONES DOCENTE POR PERÍODO
    =============================================*/
    static public function mdlObtenerAsignacionesDocente($cuerpoDocenteId, $periodoId) {
        try {
            $sql = "SELECT 
                        g.id as grupo_id,
                        COALESCE(gp.nombre, g.nombre) as nombre_grupo,
                        grd.nombre as grado,
                        grd.numero as grado_numero,
                        a.nombre as asignatura,
                        ar.nombre as area,
                        aa.id as asignacion_id,
                        aa.cuerpo_docente_id,
                        aa.periodo_academico_id,
                        (SELECT COUNT(*) FROM matricula m WHERE m.grupo_id = g.id AND m.estado_matricula = 'Matriculado') as total_estudiantes
                    FROM asignacion_academica aa
                    INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                    INNER JOIN asignatura a ON ec.asignatura_id = a.id
                    INNER JOIN area ar ON a.area_id = ar.id
                    INNER JOIN grupo g ON aa.grupo_id = g.id
                    LEFT JOIN grupo gp ON g.grupo_padre_id = gp.id
                    INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                    INNER JOIN grado grd ON oa.grado_id = grd.id
                    WHERE aa.cuerpo_docente_id = :cuerpo_docente_id 
                      AND aa.periodo_academico_id = :periodo_id 
                      AND aa.estado = 'Activa'
                    ORDER BY grd.numero, nombre_grupo, ar.nombre, a.nombre";
            
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
            $stmt->bindParam(":periodo_id", $periodoId, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Asignaciones encontradas para docente $cuerpoDocenteId, período $periodoId: " . count($resultado));
            
            foreach ($resultado as $asig) {
                error_log("Asignación ID: {$asig['asignacion_id']}, Grupo: {$asig['nombre_grupo']} (ID: {$asig['grupo_id']}), Estudiantes: {$asig['total_estudiantes']}");
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en mdlObtenerAsignacionesDocente: " . $e->getMessage());
            return [];
        }
    }

    /*=============================================
    OBTENER ESTUDIANTES MATRICULADOS EN GRUPO
    =============================================*/
    static public function mdlObtenerEstudiantesGrupo($grupoId) {
        try {
            // Primero verificar que el grupo existe
            $sqlGrupo = "SELECT id, nombre FROM grupo WHERE id = :grupo_id";
            $stmtGrupo = Conexion::conectar()->prepare($sqlGrupo);
            $stmtGrupo->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
            $stmtGrupo->execute();
            $grupo = $stmtGrupo->fetch();
            
            if (!$grupo) {
                error_log("Grupo ID $grupoId no existe");
                return [];
            }
            
            error_log("Buscando estudiantes para grupo: {$grupo['nombre']} (ID: $grupoId)");
            
            $sql = "SELECT 
                        m.id as matricula_id,
                        u.nombres_usuario,
                        u.apellidos_usuario,
                        u.tipo_documento,
                        u.numero_documento,
                        m.grupo_id,
                        g.nombre as grupo_nombre
                    FROM matricula m
                    INNER JOIN roles_institucionales ri ON m.roles_institucionales_id = ri.id
                    INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
                    INNER JOIN grupo g ON m.grupo_id = g.id
                    WHERE m.grupo_id = :grupo_id 
                      AND m.estado_matricula = 'Matriculado'
                      AND ri.estado = 'Activo'
                    ORDER BY u.apellidos_usuario, u.nombres_usuario";
            
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Estudiantes encontrados para grupo $grupoId ({$grupo['nombre']}): " . count($resultado));
            
            if (count($resultado) > 0) {
                error_log("Primer estudiante: {$resultado[0]['nombres_usuario']} {$resultado[0]['apellidos_usuario']} - Matrícula ID: {$resultado[0]['matricula_id']}");
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en mdlObtenerEstudiantesGrupo: " . $e->getMessage());
            return [];
        }
    }

    /*=============================================
    REGISTRAR ASISTENCIA MASIVA
    =============================================*/
    static public function mdlRegistrarAsistenciaMasiva($asignacionId, $fecha, $horaInicio, $horaFin, $asistencias, $usuarioId) {
        try {
            $pdo = Conexion::conectar();
            
            // Validar que la asignación académica existe
            $sqlValidar = "SELECT id FROM asignacion_academica WHERE id = :asignacion_id";
            $stmtValidar = $pdo->prepare($sqlValidar);
            $stmtValidar->bindParam(":asignacion_id", $asignacionId, PDO::PARAM_INT);
            $stmtValidar->execute();
            
            if (!$stmtValidar->fetch()) {
                error_log("Error: Asignación académica ID $asignacionId no existe");
                return "error";
            }
            
            $pdo->beginTransaction();

            foreach ($asistencias as $asistencia) {
                // Validar que la matrícula existe
                $sqlValidarMatricula = "SELECT id FROM matricula WHERE id = :matricula_id";
                $stmtValidarMatricula = $pdo->prepare($sqlValidarMatricula);
                $stmtValidarMatricula->bindParam(":matricula_id", $asistencia['matricula_id'], PDO::PARAM_INT);
                $stmtValidarMatricula->execute();
                
                if (!$stmtValidarMatricula->fetch()) {
                    error_log("Error: Matrícula ID {$asistencia['matricula_id']} no existe");
                    continue; // Saltar este estudiante
                }
                // Usar minutos de retraso calculados en el frontend
                $minutosRetraso = isset($asistencia['minutos_retraso']) ? $asistencia['minutos_retraso'] : 0;
                
                // Determinar estado de justificación
                $justificacionEstado = 'No aplica';
                if ($asistencia['estado'] === 'Ausente') {
                    $justificacionEstado = 'Sin justificar';
                } elseif ($asistencia['estado'] === 'Retraso') {
                    $justificacionEstado = 'No aplica';
                }

                // Guardar todos los estados en la base de datos
                $sql = "INSERT INTO asistencia_clase 
                        (asignacion_academica_id, matricula_id, fecha, hora_inicio_clase, hora_fin_clase, 
                         estado, minutos_retraso, justificacion_estado, registrado_por_usuario_id)
                        VALUES (:asignacion_id, :matricula_id, :fecha, :hora_inicio, :hora_fin, 
                                :estado, :minutos_retraso, :justificacion_estado, :usuario_id)
                        ON DUPLICATE KEY UPDATE
                        estado = VALUES(estado),
                        minutos_retraso = VALUES(minutos_retraso),
                        justificacion_estado = VALUES(justificacion_estado),
                        hora_inicio_clase = VALUES(hora_inicio_clase),
                        hora_fin_clase = VALUES(hora_fin_clase)";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":asignacion_id", $asignacionId, PDO::PARAM_INT);
                $stmt->bindParam(":matricula_id", $asistencia['matricula_id'], PDO::PARAM_INT);
                $stmt->bindParam(":fecha", $fecha);
                $stmt->bindParam(":hora_inicio", $horaInicio);
                $stmt->bindParam(":hora_fin", $horaFin);
                $stmt->bindParam(":estado", $asistencia['estado']);
                $stmt->bindParam(":minutos_retraso", $minutosRetraso, PDO::PARAM_INT);
                $stmt->bindParam(":justificacion_estado", $justificacionEstado);
                $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
                $stmt->execute();
                
                error_log("Guardado en BD: Estudiante {$asistencia['matricula_id']}, Estado: {$asistencia['estado']}, Minutos: $minutosRetraso");
            }

            $pdo->commit();
            error_log("Asistencia guardada exitosamente para asignación $asignacionId, fecha $fecha");
            return "ok";
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error en mdlRegistrarAsistenciaMasiva: " . $e->getMessage());
            return "error";
        }
    }

    /*=============================================
    OBTENER ASISTENCIA EXISTENTE
    =============================================*/
    static public function mdlObtenerAsistenciaExistente($asignacionId, $fecha) {
        try {
            $sql = "SELECT 
                        ac.matricula_id,
                        ac.estado,
                        COALESCE(ac.minutos_retraso, 0) as minutos_retraso,
                        COALESCE(ac.justificacion_estado, 
                            CASE 
                                WHEN ac.estado = 'Ausente' THEN 'Sin justificar'
                                ELSE 'No aplica'
                            END
                        ) as justificacion_estado,
                        ac.hora_inicio_clase,
                        ac.hora_fin_clase
                    FROM asistencia_clase ac
                    WHERE ac.asignacion_academica_id = :asignacion_id 
                      AND ac.fecha = :fecha";
            
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":asignacion_id", $asignacionId, PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->execute();
            
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Asistencia existente encontrada: " . count($resultado) . " registros");
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error en mdlObtenerAsistenciaExistente: " . $e->getMessage());
            return [];
        }
    }

    /*=============================================
    CAMBIAR PENDIENTES A AUSENTES (AUTOMÁTICO AL FIN DE CLASE)
    =============================================*/
    static public function mdlCambiarPendientesAusentes($asignacionId, $fecha) {
        $sql = "UPDATE asistencia_clase 
                SET estado = 'Ausente', 
                    justificacion_estado = 'Sin justificar'
                WHERE asignacion_academica_id = :asignacion_id 
                  AND fecha = :fecha 
                  AND estado = 'Pendiente'
                  AND NOW() > CONCAT(fecha, ' ', hora_fin_clase)";
        
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":asignacion_id", $asignacionId, PDO::PARAM_INT);
        $stmt->bindParam(":fecha", $fecha);
        return $stmt->execute() ? "ok" : "error";
    }
}