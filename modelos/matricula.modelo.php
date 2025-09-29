<?php

require_once "conexion.php";

class ModeloMatricula {

    /*=============================================
    MOSTRAR MATRÍCULA (CON AJUSTE)
    =============================================*/
    static public function mdlMostrarMatricula($tabla, $item, $valor) {
        if($item != null) {
            $stmt = Conexion::conectar()->prepare("
            SELECT 
                m.id as matricula_id, m.numero_matricula, m.fecha_matricula, m.nuevo, m.repitente, m.estado_matricula,
                u_est.id_usuario as estudiante_usuario_id, u_est.numero_documento as estudiante_documento, u_est.tipo_documento as estudiante_tipo_documento,
                u_est.nombres_usuario as estudiante_nombres, u_est.apellidos_usuario as estudiante_apellidos, u_est.sexo_usuario as estudiante_sexo,
                u_est.rh_usuario as estudiante_rh, u_est.fecha_nacimiento as estudiante_fecha_nacimiento, u_est.edad_usuario as estudiante_edad,
                u_est.telefono_usuario as estudiante_telefono, u_est.email_usuario as estudiante_email, u_est.etnia_usuario as estudiante_etnia,
                ri_est.id as roles_institucionales_id, ri_est.fecha_inicio as fecha_inicio, ri_est.fecha_fin, ri_est.estado as estado_rol,
                g.id as grupo_id, g.nombre as grupo_nombre, g.cupos as grupo_cupos,
                c.nombre as curso_nombre,
                gr.id as grado_id, CONCAT(gr.numero, '° - ', gr.nombre) as grado_completo, gr.numero as grado_numero, gr.nombre as grado_nombre,
                ne.nombre as nivel_educativo,
                j.nombre as jornada,
                s.nombre_sede, s.direccion as sede_direccion,
                i.nombre as institucion_nombre
            FROM matricula m
            INNER JOIN roles_institucionales ri_est ON m.roles_institucionales_id = ri_est.id
            INNER JOIN usuarios u_est ON ri_est.usuario_id = u_est.id_usuario
            INNER JOIN grupo g ON m.grupo_id = g.id
            INNER JOIN curso c ON g.curso_id = c.id
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id  
            INNER JOIN grado gr ON oa.grado_id = gr.id
            INNER JOIN nivel_educativo ne ON gr.nivel_educativo_id = ne.id
            INNER JOIN sede_jornada sj ON m.sede_jornada_id = sj.id
            INNER JOIN jornada j ON sj.jornada_id = j.id
            INNER JOIN sede s ON sj.sede_id = s.id
            INNER JOIN institucion i ON s.institucion_id = i.id
            WHERE m.$item = :$item
        ");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();
            $resultado = $stmt -> fetch(PDO::FETCH_ASSOC);

            if($resultado) {
                $stmt_acudientes = Conexion::conectar()->prepare("
                SELECT 
                    aa.id as acudiente_id, aa.parentesco, aa.autorizado_recoger, aa.es_firmante_principal, aa.observaciones,
                    u_acu.id_usuario as acudiente_usuario_id,
                    u_acu.numero_documento as acudiente_documento, u_acu.tipo_documento as acudiente_tipo_documento,
                    u_acu.nombres_usuario as acudiente_nombres, u_acu.apellidos_usuario as acudiente_apellidos,
                    u_acu.sexo_usuario as acudiente_sexo, u_acu.telefono_usuario as acudiente_telefono,
                    u_acu.email_usuario as acudiente_email
                FROM asignacion_acudiente aa
                INNER JOIN roles_institucionales ri_acu ON aa.roles_institucionales_id = ri_acu.id
                INNER JOIN usuarios u_acu ON ri_acu.usuario_id = u_acu.id_usuario
                WHERE aa.matricula_id = :matricula_id
                ORDER BY aa.parentesco
            ");
                $stmt_acudientes->bindParam(":matricula_id", $resultado['matricula_id'], PDO::PARAM_INT);
                $stmt_acudientes->execute();
                $resultado['acudientes'] = $stmt_acudientes->fetchAll(PDO::FETCH_ASSOC);
            }
            return $resultado;
        } else {
            $stmt = Conexion::conectar()->prepare("
            SELECT 
                m.id, m.numero_matricula, m.fecha_matricula, m.nuevo, m.estado_matricula,
                CONCAT(u.nombres_usuario, ' ', u.apellidos_usuario) as nombres_estudiante,
                u.numero_documento as documento_estudiante, u.tipo_documento as tipo_documento_estudiante,
                g.nombre as grupo_nombre, c.nombre as curso,
                CONCAT(gr.numero, '° - ', gr.nombre) as grado, j.nombre as jornada,
                s.nombre_sede
            FROM matricula m
            INNER JOIN roles_institucionales ri ON m.roles_institucionales_id = ri.id
            INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
            INNER JOIN grupo g ON m.grupo_id = g.id
            INNER JOIN curso c ON g.curso_id = c.id
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            INNER JOIN grado gr ON oa.grado_id = gr.id
            INNER JOIN sede_jornada sj ON m.sede_jornada_id = sj.id
            INNER JOIN jornada j ON sj.jornada_id = j.id
            INNER JOIN sede s ON sj.sede_id = s.id
            ORDER BY m.id DESC
            ");
            $stmt -> execute();
            return $stmt -> fetchAll();
        }
        $stmt = null;
    }

    /*=============================================
    BUSCAR ESTUDIANTE (MODIFICADO)
    =============================================*/
    static public function mdlBuscarEstudiante($criterio, $valor) {

        $sql = "
            SELECT 
                u.id_usuario, u.numero_documento, u.tipo_documento, u.nombres_usuario, u.apellidos_usuario,
                m.estado_matricula,
                s.nombre_sede,
                CONCAT(gr.numero, '° - ', gr.nombre) as grado_completo
            FROM usuarios u
            LEFT JOIN roles_institucionales ri ON u.id_usuario = ri.usuario_id AND ri.rol_id = (SELECT id_rol FROM roles WHERE nombre_rol = 'Estudiante') AND ri.estado = 'Activo'
            LEFT JOIN matricula m ON ri.id = m.roles_institucionales_id AND m.estado_matricula = 'Matriculado'
            LEFT JOIN grupo g ON m.grupo_id = g.id
            LEFT JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            LEFT JOIN sede_jornada sj ON m.sede_jornada_id = sj.id
            LEFT JOIN sede s ON sj.sede_id = s.id
            LEFT JOIN grado gr ON oa.grado_id = gr.id
        ";

        if($criterio == "documento") {
            $sql .= " WHERE u.numero_documento = :valor";
        } else if($criterio == "nombres") {
            $sql .= " WHERE CONCAT(u.nombres_usuario, ' ', u.apellidos_usuario) LIKE :valor";
            $valor = "%".$valor."%";
        } else {
            return null;
        }

        $sql .= " ORDER BY m.fecha_matricula DESC LIMIT 1";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*=============================================
    BUSCAR ACUDIENTE
    =============================================*/
    static public function mdlBuscarAcudiente($documento) {
        $stmt = Conexion::conectar()->prepare("SELECT u.id_usuario, u.numero_documento, u.tipo_documento, u.nombres_usuario, u.apellidos_usuario, u.sexo_usuario, u.rh_usuario, u.fecha_nacimiento, u.edad_usuario, u.telefono_usuario, u.email_usuario, u.usuario FROM usuarios u WHERE u.numero_documento = :documento");
        $stmt->bindParam(":documento", $documento, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /*=============================================
    OBTENER SEDES, GRADOS, GRUPOS
    =============================================*/
    static public function mdlObtenerTodasLasSedesActivas() {
        $stmt = Conexion::conectar()->prepare("SELECT s.id, s.nombre_sede, s.codigo_dane, i.nombre as institucion_nombre FROM sede s INNER JOIN institucion i ON s.institucion_id = i.id WHERE s.estado = 1 AND i.estado = 1 ORDER BY i.nombre, s.nombre_sede");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlObtenerGradosPorSede($sedeId) {
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT gr.id, gr.numero, gr.nombre FROM grado gr INNER JOIN oferta_academica oa ON gr.id = oa.grado_id INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id WHERE sj.sede_id = :sede_id ORDER BY gr.numero");
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlObtenerGruposPorGrado($gradoId, $sedeId) {
        $stmt = Conexion::conectar()->prepare("SELECT g.id as grupo_id, g.nombre as grupo_nombre, g.cupos, c.nombre as curso_nombre, gr.numero as grado_numero, gr.nombre as grado_nombre, j.nombre as jornada, sj.id as sede_jornada_id FROM grupo g INNER JOIN curso c ON g.curso_id = c.id INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id INNER JOIN grado gr ON oa.grado_id = gr.id INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id INNER JOIN jornada j ON sj.jornada_id = j.id WHERE gr.id = :grado_id AND sj.sede_id = :sede_id AND g.cupos > 0 ORDER BY j.nombre, c.nombre");
        $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlObtenerGrupos() {
        $stmt = Conexion::conectar()->prepare("SELECT g.id as grupo_id, g.nombre as grupo_nombre, g.cupos, c.nombre as curso_nombre, gr.numero as grado_numero, gr.nombre as grado_nombre, j.nombre as jornada FROM grupo g INNER JOIN curso c ON g.curso_id = c.id INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id INNER JOIN grado gr ON oa.grado_id = gr.id INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id INNER JOIN jornada j ON sj.jornada_id = j.id WHERE g.cupos > 0 ORDER BY j.nombre, gr.numero, c.nombre");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /*=============================================
    NUEVO: VERIFICAR MATRÍCULA ACTIVA POR USUARIO
    =============================================*/
    static public function mdlVerificarMatriculaActivaPorUsuario($usuarioId) {
        $stmt = Conexion::conectar()->prepare("
            SELECT m.id 
            FROM matricula m
            INNER JOIN roles_institucionales ri ON m.roles_institucionales_id = ri.id
            WHERE ri.usuario_id = :usuarios_id AND m.estado_matricula = 'Matriculado' AND ri.estado = 'Activo'
        ");
        $stmt->bindParam(":usuarios_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /*=============================================
    CREAR ROL INSTITUCIONAL ESTUDIANTE
    =============================================*/
    static public function mdlCrearRolInstitucionalEstudiante($datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO roles_institucionales(usuario_id, rol_id, sede_id, fecha_inicio, estado) VALUES (:usuario_id, (SELECT id_rol FROM roles WHERE nombre_rol = 'Estudiante'), :sede_id, :fecha_inicio, 'Activo')");
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
        if($stmt->execute()) {
            return Conexion::conectar()->lastInsertId();
        }
        return "error";
    }

    /*=============================================
    CREAR ROL INSTITUCIONAL ACUDIENTE
    =============================================*/
    static public function mdlCrearRolInstitucionalAcudiente($datos) {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO roles_institucionales(usuario_id, rol_id, sede_id, fecha_inicio, estado) VALUES (:usuario_id, (SELECT id_rol FROM roles WHERE nombre_rol = 'Acudiente'), :sede_id, :fecha_inicio, 'Activo')");
            $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
            $stmt->bindParam(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
            $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
            if($stmt->execute()) {
                return Conexion::conectar()->lastInsertId();
            }
            return "error";
        } catch(Exception $e) {
            return "error: " . $e->getMessage();
        }
    }

    /*=============================================
    CREAR MATRÍCULA
    =============================================*/
    static public function mdlCrearMatricula($tabla, $datos) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO $tabla(roles_institucionales_id, grupo_id, sede_jornada_id, numero_matricula, fecha_matricula, nuevo, repitente, estado_matricula) VALUES (:roles_institucionales_id, :grupo_id, :sede_jornada_id, :numero_matricula, :fecha_matricula, :nuevo, :repitente, :estado_matricula)");
        $stmt->bindParam(":roles_institucionales_id", $datos["roles_institucionales_id"], PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id", $datos["grupo_id"], PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $datos["sede_jornada_id"], PDO::PARAM_INT);
        $stmt->bindParam(":numero_matricula", $datos["numero_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_matricula", $datos["fecha_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":nuevo", $datos["nuevo"], PDO::PARAM_STR);
        $stmt->bindParam(":repitente", $datos["repitente"], PDO::PARAM_STR);
        $stmt->bindParam(":estado_matricula", $datos["estado_matricula"], PDO::PARAM_STR);
        return $stmt->execute() ? $conexion->lastInsertId() : "error";
    }

    /*=============================================
    CREAR ASIGNACIÓN ACUDIENTE
    =============================================*/
    static public function mdlCrearAsignacionAcudiente($tabla, $datos) {
        try {
            // Asegurar que es_firmante_principal tenga un valor válido
            if (empty($datos["es_firmante_principal"]) || !in_array($datos["es_firmante_principal"], ['Si', 'No'])) {
                $datos["es_firmante_principal"] = 'No';
            }
            
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(matricula_id, roles_institucionales_id, parentesco, es_firmante_principal, autorizado_recoger, observaciones) VALUES (:matricula_id, :roles_institucionales_id, :parentesco, :es_firmante_principal, :autorizado_recoger, :observaciones)");
            $stmt->bindParam(":matricula_id", $datos["matricula_id"], PDO::PARAM_INT);
            $stmt->bindParam(":roles_institucionales_id", $datos["roles_institucionales_id"], PDO::PARAM_INT);
            $stmt->bindParam(":parentesco", $datos["parentesco"], PDO::PARAM_STR);
            $stmt->bindParam(":es_firmante_principal", $datos["es_firmante_principal"], PDO::PARAM_STR);
            $stmt->bindParam(":autorizado_recoger", $datos["autorizado_recoger"], PDO::PARAM_STR);
            $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
            return $stmt->execute() ? "ok" : "error";
        } catch (PDOException $e) {
            error_log("Error en mdlCrearAsignacionAcudiente: " . $e->getMessage());
            return "error: " . $e->getMessage();
        }
    }

    /*=============================================
    NUEVA FUNCIÓN: ELIMINAR ACUDIENTES POR MATRÍCULA
    =============================================*/
    static public function mdlEliminarAcudientesPorMatricula($tabla, $matriculaId) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE matricula_id = :matricula_id");
        $stmt->bindParam(":matricula_id", $matriculaId, PDO::PARAM_INT);
        return $stmt->execute() ? "ok" : "error";
    }

    /*=============================================
    EDITAR ROL INSTITUCIONAL
    =============================================*/
    static public function mdlEditarRolInstitucional($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET fecha_inicio = :fecha_inicio WHERE id = :id");
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    /*=============================================
    EDITAR MATRÍCULA
    =============================================*/
    static public function mdlEditarMatricula($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET grupo_id = :grupo_id, sede_jornada_id = :sede_jornada_id, numero_matricula = :numero_matricula, fecha_matricula = :fecha_matricula, nuevo = :nuevo, repitente = :repitente, estado_matricula = :estado_matricula WHERE id = :id");
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id", $datos["grupo_id"], PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $datos["sede_jornada_id"], PDO::PARAM_INT);
        $stmt->bindParam(":numero_matricula", $datos["numero_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_matricula", $datos["fecha_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":nuevo", $datos["nuevo"], PDO::PARAM_STR);
        $stmt->bindParam(":repitente", $datos["repitente"], PDO::PARAM_STR);
        $stmt->bindParam(":estado_matricula", $datos["estado_matricula"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    /*=============================================
    BORRAR MATRÍCULA
    =============================================*/
    static public function mdlBorrarMatricula($tabla, $datos) {
        try {
            $conexion = Conexion::conectar();
            $conexion->beginTransaction();
            $stmtAcudientes = $conexion->prepare("DELETE FROM asignacion_acudiente WHERE matricula_id = :id");
            $stmtAcudientes->bindParam(":id", $datos, PDO::PARAM_INT);
            $stmtAcudientes->execute();
            $stmtMatricula = $conexion->prepare("DELETE FROM $tabla WHERE id = :id");
            $stmtMatricula->bindParam(":id", $datos, PDO::PARAM_INT);
            $stmtMatricula->execute();

            if($stmtMatricula->rowCount() > 0) {
                $conexion->commit();
                return "ok";
            } else {
                $conexion->rollback();
                return "error";
            }
        } catch(Exception $e) {
            if(isset($conexion)) $conexion->rollback();
            return "error: " . $e->getMessage();
        }
    }
}
?>