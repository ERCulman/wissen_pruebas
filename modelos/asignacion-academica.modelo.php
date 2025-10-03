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
    OBTENER GRUPOS POR GRADO Y SEDE (INCLUYE REGULAR Y MULTIGRADO)
    =============================================*/
    static public function mdlObtenerGruposPorGradoSede($gradoId, $sedeId) {
        $sql = "SELECT g.id, g.nombre, g.tipo, g.grupo_padre_id,
                   g.nombre as nombre_completo
            FROM grupo g
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
            WHERE oa.grado_id = :grado_id 
              AND sj.sede_id = :sede_id
              AND g.curso_id IS NOT NULL 
              AND g.curso_id != ''
              AND (g.tipo = 'Regular' OR g.tipo = 'Multigrado')
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
OBTENER ASIGNACIONES ÚNICAS DEL DOCENTE
=============================================*/
    static public function mdlObtenerAsignacionesDocente($cuerpoDocenteId) {
        $sql = "SELECT 
                MIN(aa.id) as id, 
                ANY_VALUE(a.nombre) as asignatura, 
                ANY_VALUE(ar.nombre) as area,
                MAX(aa.intensidad_horaria_semanal) as intensidad_horaria_semanal,
                ec.asignatura_id,
                COALESCE(g.grupo_padre_id, g.id) as grupo_id,
                ANY_VALUE(COALESCE(gp.nombre, g.nombre)) as nombre_mostrar,
                GROUP_CONCAT(DISTINCT aa.periodo_academico_id ORDER BY aa.periodo_academico_id) as periodos_ids,
                aa.estado,
                -- CAMBIO: Contamos cuántas filas se agruparon. Si es > 1, es multigrado.
                COUNT(aa.id) as num_agrupados,
                -- CAMBIO: Devolvemos un estructura_id solo si no es un grupo.
                CASE WHEN COUNT(aa.id) > 1 THEN NULL ELSE MIN(aa.estructura_curricular_id) END as estructura_curricular_id
            FROM asignacion_academica aa
            INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
            INNER JOIN asignatura a ON ec.asignatura_id = a.id
            INNER JOIN area ar ON a.area_id = ar.id
            INNER JOIN grupo g ON aa.grupo_id = g.id
            LEFT JOIN grupo gp ON g.grupo_padre_id = gp.id
            WHERE aa.cuerpo_docente_id = :cuerpo_docente_id AND aa.estado != 'Finalizada'
            GROUP BY ec.asignatura_id, COALESCE(g.grupo_padre_id, g.id), aa.estado
            ORDER BY ANY_VALUE(ar.nombre), ANY_VALUE(a.nombre)";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    ACTUALIZAR ESTADO DE ASIGNACIONES POR PERÍODOS
    =============================================*/
    static public function mdlActualizarEstadoAsignaciones($cuerpoDocenteId, $periodos, $estado) {
        $placeholders = str_repeat('?,', count($periodos) - 1) . '?';
        $sql = "UPDATE asignacion_academica 
                SET estado = ? 
                WHERE cuerpo_docente_id = ? AND periodo_academico_id IN ($placeholders)";
        $stmt = Conexion::conectar()->prepare($sql);
        $params = array_merge([$estado, $cuerpoDocenteId], $periodos);
        return $stmt->execute($params) ? "ok" : "error";
    }

    /*=============================================
    OBTENER DATOS COMPLETOS DEL MULTIGRADO
    =============================================*/
    static public function mdlObtenerDatosMultigrado($grupoId, $asignaturaId, $cuerpoDocenteId) {
        try {
            // Primero obtener todos los grupos relacionados (hijos del multigrado)
            $sqlGrupos = "SELECT g.id FROM grupo g WHERE g.grupo_padre_id = :grupo_id OR g.id = :grupo_id";
            $stmtGrupos = Conexion::conectar()->prepare($sqlGrupos);
            $stmtGrupos->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
            $stmtGrupos->execute();
            $gruposIds = $stmtGrupos->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($gruposIds)) {
                error_log("No se encontraron grupos para grupo_id: $grupoId");
                return false;
            }
            
            $placeholders = implode(',', array_fill(0, count($gruposIds), '?'));
            
            // Consulta principal
            $sql = "SELECT 
                        a.nombre as asignatura,
                        ar.nombre as area,
                        MAX(aa.intensidad_horaria_semanal) as intensidad_horaria_semanal,
                        GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.id SEPARATOR ', ') as periodos,
                        GROUP_CONCAT(DISTINCT CONCAT(gr.nombre, ' (', gr.numero, ')') ORDER BY gr.numero SEPARATOR ', ') as grados,
                        GROUP_CONCAT(DISTINCT g.nombre ORDER BY g.nombre SEPARATOR ', ') as grupos
                    FROM asignacion_academica aa
                    INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                    INNER JOIN asignatura a ON ec.asignatura_id = a.id
                    INNER JOIN area ar ON a.area_id = ar.id
                    INNER JOIN periodo p ON aa.periodo_academico_id = p.id
                    INNER JOIN grupo g ON aa.grupo_id = g.id
                    INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                    INNER JOIN grado gr ON oa.grado_id = gr.id
                    WHERE aa.cuerpo_docente_id = ?
                      AND ec.asignatura_id = ?
                      AND aa.grupo_id IN ($placeholders)
                      AND EXISTS (
                          SELECT 1 FROM asignacion_academica aa2 
                          WHERE aa2.grupo_id = aa.grupo_id 
                            AND aa2.cuerpo_docente_id = aa.cuerpo_docente_id
                            AND aa2.estructura_curricular_id = aa.estructura_curricular_id
                            AND aa2.estado = 'Activa'
                      )
                    GROUP BY a.id, ar.id";

            $params = array_merge([$cuerpoDocenteId, $asignaturaId], $gruposIds);
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch();
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en mdlObtenerDatosMultigrado: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    OBTENER DATOS PARA EDICIÓN DEL MULTIGRADO
    =============================================*/
    static public function mdlObtenerDatosEdicionMultigrado($grupoId, $asignaturaId, $cuerpoDocenteId) {
        try {
            // Obtener grupos relacionados
            $sqlGrupos = "SELECT g.id FROM grupo g WHERE g.grupo_padre_id = :grupo_id OR g.id = :grupo_id";
            $stmtGrupos = Conexion::conectar()->prepare($sqlGrupos);
            $stmtGrupos->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
            $stmtGrupos->execute();
            $gruposIds = $stmtGrupos->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($gruposIds)) {
                return false;
            }
            
            // Obtener datos básicos del multigrado
            $sqlBasicos = "SELECT 
                            gp.nombre as grupo_multigrado,
                            CONCAT(ar.nombre, ' - ', a.nombre) as asignatura,
                            ec.intensidad_horaria_semanal as ih_asignatura
                        FROM grupo gp
                        INNER JOIN asignacion_academica aa ON aa.grupo_id IN (" . implode(',', array_fill(0, count($gruposIds), '?')) . ")
                        INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                        INNER JOIN asignatura a ON ec.asignatura_id = a.id
                        INNER JOIN area ar ON a.area_id = ar.id
                        WHERE gp.id = ? AND aa.cuerpo_docente_id = ? AND ec.asignatura_id = ? AND aa.estado = 'Activa'
                        LIMIT 1";
            
            $paramsBasicos = array_merge($gruposIds, [$grupoId, $cuerpoDocenteId, $asignaturaId]);
            $stmtBasicos = Conexion::conectar()->prepare($sqlBasicos);
            $stmtBasicos->execute($paramsBasicos);
            $datosBasicos = $stmtBasicos->fetch();
            
            // Obtener detalles por grado (sin duplicar por períodos)
            $placeholders = implode(',', array_fill(0, count($gruposIds), '?'));
            $sqlDetalles = "SELECT 
                            gr.nombre as grado,
                            g.nombre as grupo,
                            g.id as grupo_id,
                            MAX(aa.intensidad_horaria_semanal) as ihs_actual,
                            MAX(aa.estado) as estado_actual,
                            GROUP_CONCAT(DISTINCT aa.id ORDER BY aa.id) as asignacion_ids
                        FROM asignacion_academica aa
                        INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                        INNER JOIN grupo g ON aa.grupo_id = g.id
                        INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                        INNER JOIN grado gr ON oa.grado_id = gr.id
                        WHERE aa.cuerpo_docente_id = ?
                          AND ec.asignatura_id = ?
                          AND aa.grupo_id IN ($placeholders)
                          AND aa.estado = 'Activa'
                        GROUP BY g.id, gr.id
                        ORDER BY gr.numero";
            
            $paramsDetalles = array_merge([$cuerpoDocenteId, $asignaturaId], $gruposIds);
            $stmtDetalles = Conexion::conectar()->prepare($sqlDetalles);
            $stmtDetalles->execute($paramsDetalles);
            $detalles = $stmtDetalles->fetchAll();
            
            // Obtener períodos disponibles desde la BD
            $sqlPeriodos = "SELECT id, nombre FROM periodo ORDER BY id ASC";
            $stmtPeriodos = Conexion::conectar()->prepare($sqlPeriodos);
            $stmtPeriodos->execute();
            $periodos = $stmtPeriodos->fetchAll();
            
            return [
                'basicos' => $datosBasicos,
                'detalles' => $detalles,
                'periodos' => $periodos
            ];
            
        } catch (Exception $e) {
            error_log("Error en mdlObtenerDatosEdicionMultigrado: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    ACTUALIZAR ASIGNACIONES DEL MULTIGRADO
    =============================================*/
    static public function mdlActualizarMultigrado($cambios, $eliminaciones, $periodosSeleccionados, $cuerpoDocenteId, $asignaturaId) {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();
            
            $placeholdersPeriodos = implode(',', array_fill(0, count($periodosSeleccionados), '?'));
            
            // Procesar eliminaciones
            if (!empty($eliminaciones)) {
                foreach ($eliminaciones as $grupoId) {
                    $sqlEliminar = "DELETE aa FROM asignacion_academica aa
                                   INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                                   WHERE aa.cuerpo_docente_id = ?
                                     AND ec.asignatura_id = ?
                                     AND aa.grupo_id = ?
                                     AND aa.periodo_academico_id IN ($placeholdersPeriodos)";
                    
                    $paramsEliminar = array_merge([$cuerpoDocenteId, $asignaturaId, $grupoId], $periodosSeleccionados);
                    $stmtEliminar = $conexion->prepare($sqlEliminar);
                    $stmtEliminar->execute($paramsEliminar);
                }
            }
            
            // Procesar cambios
            foreach ($cambios as $cambio) {
                $sql = "UPDATE asignacion_academica aa
                        INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                        SET aa.intensidad_horaria_semanal = ?, aa.estado = ?
                        WHERE aa.cuerpo_docente_id = ?
                          AND ec.asignatura_id = ?
                          AND aa.grupo_id = ?
                          AND aa.periodo_academico_id IN ($placeholdersPeriodos)";
                          
                $params = array_merge([
                    $cambio['ihs'],
                    $cambio['estado'],
                    $cuerpoDocenteId,
                    $asignaturaId,
                    $cambio['grupo_id']
                ], $periodosSeleccionados);
                
                $stmt = $conexion->prepare($sql);
                $stmt->execute($params);
            }
            
            $conexion->commit();
            return "ok";
            
        } catch (Exception $e) {
            if (isset($conexion)) {
                $conexion->rollback();
            }
            error_log("Error actualizando multigrado: " . $e->getMessage());
            return "error";
        }
    }

    /*=============================================
    VERIFICAR RELACIONES DE UNA ASIGNACIÓN ESPECÍFICA
    =============================================*/
    static public function mdlVerificarRelacionesAsignacion($asignacionId) {
        try {
            $conexion = Conexion::conectar();
            $relaciones = [];
            
            // Consultar dinámicamente todas las foreign keys que apuntan a asignacion_academica
            $sqlForeignKeys = "SELECT TABLE_NAME, COLUMN_NAME
                              FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                              WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
                                AND REFERENCED_TABLE_NAME = 'asignacion_academica'
                                AND REFERENCED_COLUMN_NAME = 'id'";
            
            $stmtFK = $conexion->prepare($sqlForeignKeys);
            $stmtFK->execute();
            $foreignKeys = $stmtFK->fetchAll();
            
            // Verificar cada tabla que tiene foreign key a asignacion_academica
            foreach ($foreignKeys as $fk) {
                $tableName = $fk['TABLE_NAME'];
                $columnName = $fk['COLUMN_NAME'];
                
                try {
                    $sqlCount = "SELECT COUNT(*) as total FROM `{$tableName}` WHERE `{$columnName}` = ?";
                    $stmtCount = $conexion->prepare($sqlCount);
                    $stmtCount->execute([$asignacionId]);
                    $resultado = $stmtCount->fetch();
                    
                    if ($resultado['total'] > 0) {
                        $relaciones[] = "Tiene {$resultado['total']} registros en {$tableName}";
                    }
                } catch (Exception $tableError) {
                    // Continuar con las demás tablas si hay error
                    continue;
                }
            }
            
            return $relaciones;
            
        } catch (Exception $e) {
            error_log("Error verificando relaciones de asignación: " . $e->getMessage());
            return [];
        }
    }

    /*=============================================
    OBTENER DETALLES DE ASIGNACIÓN
    =============================================*/
    static public function mdlObtenerDetalleAsignacion($estructuraCurricularId, $grupoId, $cuerpoDocenteId) {
        $sql = "SELECT aa.estructura_curricular_id, aa.grupo_id, 
                       MAX(aa.intensidad_horaria_semanal) as intensidad_horaria_semanal, 
                       MAX(aa.estado) as estado,
                       MAX(a.nombre) as asignatura, MAX(ar.nombre) as area, 
                       MAX(g.nombre) as grupo_nombre, MAX(gr.nombre) as grado_nombre,
                       GROUP_CONCAT(DISTINCT p.nombre ORDER BY aa.periodo_academico_id) as periodos_nombres,
                       GROUP_CONCAT(DISTINCT aa.periodo_academico_id ORDER BY aa.periodo_academico_id) as periodos_ids
                FROM asignacion_academica aa
                INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                INNER JOIN asignatura a ON ec.asignatura_id = a.id
                INNER JOIN area ar ON a.area_id = ar.id
                INNER JOIN grupo g ON aa.grupo_id = g.id
                INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                INNER JOIN grado gr ON oa.grado_id = gr.id
                INNER JOIN periodo p ON aa.periodo_academico_id = p.id
                WHERE aa.estructura_curricular_id = :estructura_id AND aa.grupo_id = :grupo_id AND aa.cuerpo_docente_id = :cuerpo_docente_id
                GROUP BY aa.estructura_curricular_id, aa.grupo_id";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":estructura_id", $estructuraCurricularId, PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /*=============================================
    VERIFICAR RELACIONES ACTIVAS DINÁMICAMENTE
    =============================================*/
    static public function mdlVerificarRelacionesActivas($estructuraCurricularId, $grupoId, $cuerpoDocenteId) {
        try {
            $conexion = Conexion::conectar();
            $relaciones = [];

            // Obtener IDs de asignaciones que se van a eliminar
            $sqlIds = "SELECT id FROM asignacion_academica 
                      WHERE estructura_curricular_id = ? AND grupo_id = ? AND cuerpo_docente_id = ?";
            $stmtIds = $conexion->prepare($sqlIds);
            $stmtIds->execute([$estructuraCurricularId, $grupoId, $cuerpoDocenteId]);
            $asignacionIds = $stmtIds->fetchAll(PDO::FETCH_COLUMN);

            if (empty($asignacionIds)) {
                return $relaciones; // No hay asignaciones para verificar
            }

            // Consultar dinámicamente todas las foreign keys que apuntan a asignacion_academica
            $sqlForeignKeys = "SELECT 
                                TABLE_NAME,
                                COLUMN_NAME,
                                CONSTRAINT_NAME
                              FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                              WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
                                AND REFERENCED_TABLE_NAME = 'asignacion_academica'
                                AND REFERENCED_COLUMN_NAME = 'id'";

            $stmtFK = $conexion->prepare($sqlForeignKeys);
            $stmtFK->execute();
            $foreignKeys = $stmtFK->fetchAll();

            // Si no hay foreign keys, la eliminación es segura
            if (empty($foreignKeys)) {
                return $relaciones; // No hay relaciones que verificar
            }

            // Verificar cada tabla que tiene foreign key a asignacion_academica
            foreach ($foreignKeys as $fk) {
                $tableName = $fk['TABLE_NAME'];
                $columnName = $fk['COLUMN_NAME'];

                try {
                    $placeholders = str_repeat('?,', count($asignacionIds) - 1) . '?';
                    $sqlCount = "SELECT COUNT(*) as total FROM `{$tableName}` WHERE `{$columnName}` IN ({$placeholders})";

                    $stmtCount = $conexion->prepare($sqlCount);
                    $stmtCount->execute($asignacionIds);
                    $resultado = $stmtCount->fetch();

                    if ($resultado['total'] > 0) {
                        $relaciones[] = "Tiene {$resultado['total']} registros en {$tableName}";
                    }
                } catch (Exception $tableError) {
                    // Si hay error con una tabla específica, continuar con las demás
                    error_log("Error verificando tabla {$tableName}: " . $tableError->getMessage());
                }
            }

            return $relaciones;

        } catch (Exception $e) {
            error_log("Error verificando relaciones: " . $e->getMessage());
            return []; // Retornar array vacío en lugar de error para permitir eliminación
        }
    }

    /*=============================================
    ELIMINAR ASIGNACIONES POR PERÍODO
    =============================================*/
    static public function mdlEliminarAsignacionesPorPeriodo($cuerpoDocenteId, $estructuraCurricularId, $grupoId, $periodoId) {
        $sql = "DELETE FROM asignacion_academica 
                WHERE cuerpo_docente_id = ? AND estructura_curricular_id = ? AND grupo_id = ? AND periodo_academico_id = ?";
        $stmt = Conexion::conectar()->prepare($sql);
        return $stmt->execute([$cuerpoDocenteId, $estructuraCurricularId, $grupoId, $periodoId]) ? "ok" : "error";
    }

    /*=============================================
    CREAR ASIGNACIÓN ACADÉMICA (MEJORADO CON VALIDACIÓN DE DUPLICADOS)
    =============================================*/
    static public function mdlCrearAsignacion($datos) {
        // Verificar si ya existe la asignación
        $sqlCheck = "SELECT COUNT(*) as existe FROM asignacion_academica 
                     WHERE cuerpo_docente_id = :cuerpo_docente_id 
                     AND estructura_curricular_id = :estructura_curricular_id 
                     AND grupo_id = :grupo_id 
                     AND periodo_academico_id = :periodo_academico_id 
                     AND estado = 'Activa'";
        $stmtCheck = Conexion::conectar()->prepare($sqlCheck);
        $stmtCheck->bindParam(":cuerpo_docente_id", $datos["cuerpo_docente_id"], PDO::PARAM_INT);
        $stmtCheck->bindParam(":estructura_curricular_id", $datos["estructura_curricular_id"], PDO::PARAM_INT);
        $stmtCheck->bindParam(":grupo_id", $datos["grupo_id"], PDO::PARAM_INT);
        $stmtCheck->bindParam(":periodo_academico_id", $datos["periodo_academico_id"], PDO::PARAM_INT);
        $stmtCheck->execute();
        $resultado = $stmtCheck->fetch();

        if ($resultado['existe'] > 0) {
            return "asignacion_duplicada";
        }

        // Si no existe, crear la asignación
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
    OBTENER PERÍODOS ACADÉMICOS
    =============================================*/
    static public function mdlObtenerPeriodos() {
        $sql = "SELECT id, nombre FROM periodo ORDER BY id ASC";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    OBTENER ESTADOS DE ASIGNACIÓN
    =============================================*/
    static public function mdlObtenerEstadosAsignacion() {
        try {
            $sql = "SHOW COLUMNS FROM asignacion_academica LIKE 'estado'";
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch();

            if ($resultado && isset($resultado['Type'])) {
                // Extraer valores del enum
                preg_match_all("/'([^']+)'/", $resultado['Type'], $matches);
                if (!empty($matches[1])) {
                    return $matches[1];
                }
            }
        } catch (Exception $e) {
            error_log("Error obteniendo estados de asignación: " . $e->getMessage());
        }

        // Fallback con los estados reales de la base de datos
        return ['Activa', 'Planeada', 'Finalizada', 'Cancelada'];
    }

    /*=============================================
    OBTENER ASIGNATURAS HABILITADAS DEL DOCENTE
    =============================================*/
    static public function mdlObtenerAsignaturasHabilitadas($cuerpoDocenteId) {
        $sql = "SELECT a.nombre as asignatura
                FROM docente_asignaturas_habilitadas dah
                INNER JOIN estructura_curricular ec ON dah.asignatura_estructura_curricular_id = ec.id
                INNER JOIN asignatura a ON ec.asignatura_id = a.id
                WHERE dah.cuerpo_docente_id = :cuerpo_docente_id
                ORDER BY a.nombre";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    ACTUALIZAR ASIGNACIÓN ESPECÍFICA
    =============================================*/
    static public function mdlActualizarAsignacionEspecifica($cuerpoDocenteId, $estructuraId, $grupoId, $periodos, $estado) {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();

            // Primero, obtener los períodos actuales de esta asignación
            $sqlActuales = "SELECT periodo_academico_id FROM asignacion_academica 
                           WHERE cuerpo_docente_id = ? AND estructura_curricular_id = ? AND grupo_id = ?";
            $stmtActuales = $conexion->prepare($sqlActuales);
            $stmtActuales->execute([$cuerpoDocenteId, $estructuraId, $grupoId]);
            $periodosActuales = $stmtActuales->fetchAll(PDO::FETCH_COLUMN);

            // Eliminar períodos que ya no están seleccionados
            $periodosAEliminar = array_diff($periodosActuales, $periodos);
            if (!empty($periodosAEliminar)) {
                $placeholders = str_repeat('?,', count($periodosAEliminar) - 1) . '?';
                $sqlEliminar = "DELETE FROM asignacion_academica 
                               WHERE cuerpo_docente_id = ? AND estructura_curricular_id = ? AND grupo_id = ? 
                               AND periodo_academico_id IN ($placeholders)";
                $stmtEliminar = $conexion->prepare($sqlEliminar);
                $params = array_merge([$cuerpoDocenteId, $estructuraId, $grupoId], $periodosAEliminar);
                $stmtEliminar->execute($params);
            }

            // Actualizar el estado de los períodos existentes
            $periodosExistentes = array_intersect($periodosActuales, $periodos);
            if (!empty($periodosExistentes)) {
                $placeholders = str_repeat('?,', count($periodosExistentes) - 1) . '?';
                $sqlActualizar = "UPDATE asignacion_academica SET estado = ? 
                                 WHERE cuerpo_docente_id = ? AND estructura_curricular_id = ? AND grupo_id = ? 
                                 AND periodo_academico_id IN ($placeholders)";
                $stmtActualizar = $conexion->prepare($sqlActualizar);
                $params = array_merge([$estado, $cuerpoDocenteId, $estructuraId, $grupoId], $periodosExistentes);
                $stmtActualizar->execute($params);
            }

            // Agregar nuevos períodos si es necesario
            $periodosNuevos = array_diff($periodos, $periodosActuales);
            if (!empty($periodosNuevos)) {
                // Obtener la intensidad horaria de un registro existente
                $sqlIHS = "SELECT intensidad_horaria_semanal FROM asignacion_academica 
                          WHERE cuerpo_docente_id = ? AND estructura_curricular_id = ? AND grupo_id = ? LIMIT 1";
                $stmtIHS = $conexion->prepare($sqlIHS);
                $stmtIHS->execute([$cuerpoDocenteId, $estructuraId, $grupoId]);
                $ihs = $stmtIHS->fetchColumn();

                if ($ihs) {
                    foreach ($periodosNuevos as $periodoId) {
                        $sqlInsertar = "INSERT INTO asignacion_academica 
                                       (cuerpo_docente_id, estructura_curricular_id, grupo_id, periodo_academico_id, intensidad_horaria_semanal, estado) 
                                       VALUES (?, ?, ?, ?, ?, ?)";
                        $stmtInsertar = $conexion->prepare($sqlInsertar);
                        $stmtInsertar->execute([$cuerpoDocenteId, $estructuraId, $grupoId, $periodoId, $ihs, $estado]);
                    }
                }
            }

            $conexion->commit();
            return "ok";

        } catch (Exception $e) {
            if (isset($conexion)) {
                $conexion->rollback();
            }
            error_log("Error actualizando asignación específica: " . $e->getMessage());
            return "error";
        }
    }

    /*=============================================
ELIMINAR ASIGNACIÓN COMPLETA (VERSIÓN FINAL)
=============================================*/
    static public function mdlEliminarAsignacionCompleta($cuerpoDocenteId, $asignaturaId, $grupoId) {
        try {
            $conexion = Conexion::conectar();

            $sqlGrupos = "SELECT id FROM grupo WHERE grupo_padre_id = :grupo_id OR id = :grupo_id";
            $stmtGrupos = $conexion->prepare($sqlGrupos);
            $stmtGrupos->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
            $stmtGrupos->execute();
            $listaIdsGrupos = $stmtGrupos->fetchAll(PDO::FETCH_COLUMN);

            if (empty($listaIdsGrupos)) {
                return "ok";
            }

            $placeholdersGrupos = implode(',', array_fill(0, count($listaIdsGrupos), '?'));

            $sql = "DELETE aa FROM asignacion_academica AS aa
                INNER JOIN estructura_curricular AS ec ON aa.estructura_curricular_id = ec.id
                WHERE aa.cuerpo_docente_id = ?
                  AND ec.asignatura_id = ?
                  AND aa.grupo_id IN ($placeholdersGrupos)";

            $stmt = $conexion->prepare($sql);
            $params = array_merge([$cuerpoDocenteId, $asignaturaId], $listaIdsGrupos);
            $resultado = $stmt->execute($params);

            return $resultado ? "ok" : "error";
        } catch (Exception $e) {
            error_log("Error eliminando asignación completa: " . $e->getMessage());
            return "error";
        }
    }

    /*=============================================
    CALCULAR HORAS ASIGNADAS (CORREGIDO PARA MULTIGRADOS)
    =============================================*/
    static public function mdlCalcularHorasAsignadas($cuerpoDocenteId) {
        $sql = "SELECT SUM(horas_unicas.intensidad_horaria_semanal) as total_horas
                FROM (
                    SELECT 
                        ec.asignatura_id,
                        COALESCE(g.grupo_padre_id, g.id) as grupo_efectivo,
                        MAX(aa.intensidad_horaria_semanal) as intensidad_horaria_semanal
                    FROM asignacion_academica aa
                    INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
                    INNER JOIN grupo g ON aa.grupo_id = g.id
                    WHERE aa.cuerpo_docente_id = :cuerpo_docente_id AND aa.estado = 'Activa'
                    GROUP BY ec.asignatura_id, COALESCE(g.grupo_padre_id, g.id)
                ) as horas_unicas";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":cuerpo_docente_id", $cuerpoDocenteId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado ? (int)$resultado['total_horas'] : 0;
    }
}