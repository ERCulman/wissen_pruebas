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
                m.id as matricula_id, m.numero_matricula, m.fecha_matricula, m.nuevo, m.estado_matricula,
                u_est.id_usuario as estudiante_usuario_id, u_est.numero_documento as estudiante_documento, u_est.tipo_documento as estudiante_tipo_documento,
                u_est.nombres_usuario as estudiante_nombres, u_est.apellidos_usuario as estudiante_apellidos, u_est.sexo_usuario as estudiante_sexo,
                u_est.rh_usuario as estudiante_rh, u_est.fecha_nacimiento as estudiante_fecha_nacimiento, u_est.edad_usuario as estudiante_edad,
                u_est.telefono_usuario as estudiante_telefono, u_est.email_usuario as estudiante_email, u_est.etnia_usuario as estudiante_etnia,
                e.id as estudiante_id, e.codigo_estudiante, e.fecha_ingreso, e.grado_ingreso, e.estado_anio_anterior, e.estado_actual,
                g.id as grupo_id, g.nombre as grupo_nombre, g.cupos as grupo_cupos,
                c.nombre as curso_nombre,
                gr.id as grado_id, CONCAT(gr.numero, '° - ', gr.nombre) as grado_completo, gr.numero as grado_numero, gr.nombre as grado_nombre,
                ne.nombre as nivel_educativo,
                j.nombre as jornada,
                s.nombre_sede, s.direccion as sede_direccion,
                i.nombre as institucion_nombre
            FROM matricula m
            INNER JOIN estudiante e ON m.estudiante_id = e.id
            INNER JOIN usuarios u_est ON e.usuarios_id = u_est.id_usuario
            INNER JOIN grupo g ON m.grupo_id = g.id
            INNER JOIN curso c ON g.curso_id = c.id
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id  
            INNER JOIN grado gr ON oa.grado_id = gr.id
            INNER JOIN nivel_educativo ne ON gr.nivel_educativo_id = ne.id
            INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
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
                    a.id as acudiente_id, a.parentesco, a.autorizado_recoger, a.observacion,
                    u_acu.id_usuario as acudiente_usuario_id, /* <--- CAMBIO CLAVE PARA JS */
                    u_acu.numero_documento as acudiente_documento, u_acu.tipo_documento as acudiente_tipo_documento,
                    u_acu.nombres_usuario as acudiente_nombres, u_acu.apellidos_usuario as acudiente_apellidos,
                    u_acu.sexo_usuario as acudiente_sexo, u_acu.telefono_usuario as acudiente_telefono,
                    u_acu.email_usuario as acudiente_email
                FROM acudiente a
                INNER JOIN usuarios u_acu ON a.usuarios_id = u_acu.id_usuario
                WHERE a.matricula_id = :matricula_id
                ORDER BY a.parentesco
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
                u.numero_documento as documento_estudiante,
                g.nombre as grupo_nombre, c.nombre as curso,
                CONCAT(gr.numero, '° - ', gr.nombre) as grado, j.nombre as jornada
            FROM matricula m
            INNER JOIN estudiante e ON m.estudiante_id = e.id
            INNER JOIN usuarios u ON e.usuarios_id = u.id_usuario
            INNER JOIN grupo g ON m.grupo_id = g.id
            INNER JOIN curso c ON g.curso_id = c.id
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            INNER JOIN grado gr ON oa.grado_id = gr.id
            INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
            INNER JOIN jornada j ON sj.jornada_id = j.id
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
                u.id_usuario, u.numero_documento, u.tipo_documento, u.nombres_usuario, u.apellidos_usuario, u.id_rol,
                m.estado_matricula,
                s.nombre_sede,
                CONCAT(gr.numero, '° - ', gr.nombre) as grado_completo
            FROM usuarios u
            LEFT JOIN estudiante e ON u.id_usuario = e.usuarios_id
            LEFT JOIN matricula m ON e.id = m.estudiante_id AND m.estado_matricula = 'Activo'
            LEFT JOIN grupo g ON m.grupo_id = g.id
            LEFT JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            LEFT JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
            LEFT JOIN sede s ON sj.sede_id = s.id
            LEFT JOIN grado gr ON oa.grado_id = gr.id
        ";

        if($criterio == "documento") {
            $sql .= " WHERE u.numero_documento = :valor AND u.id_rol = 'Estudiante'";
        } else if($criterio == "nombres") {
            $sql .= " WHERE CONCAT(u.nombres_usuario, ' ', u.apellidos_usuario) LIKE :valor AND u.id_rol = 'Estudiante'";
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
        $stmt = Conexion::conectar()->prepare("SELECT u.id_usuario, u.numero_documento, u.tipo_documento, u.nombres_usuario, u.apellidos_usuario, u.sexo_usuario, u.rh_usuario, u.fecha_nacimiento, u.edad_usuario, u.telefono_usuario, u.email_usuario, u.usuario FROM usuarios u WHERE u.numero_documento = :documento AND u.id_rol = 'Acudiente'");
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
        $stmt = Conexion::conectar()->prepare("SELECT g.id as grupo_id, g.nombre as grupo_nombre, g.cupos, c.nombre as curso_nombre, gr.numero as grado_numero, gr.nombre as grado_nombre, j.nombre as jornada FROM grupo g INNER JOIN curso c ON g.curso_id = c.id INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id INNER JOIN grado gr ON oa.grado_id = gr.id INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id INNER JOIN jornada j ON sj.jornada_id = j.id WHERE gr.id = :grado_id AND sj.sede_id = :sede_id AND g.cupos > 0 ORDER BY j.nombre, c.nombre");
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
            INNER JOIN estudiante e ON m.estudiante_id = e.id
            WHERE e.usuarios_id = :usuarios_id AND m.estado_matricula = 'Activo'
        ");
        $stmt->bindParam(":usuarios_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /*=============================================
    OBTENER ID ESTUDIANTE POR USUARIO
    =============================================*/
    static public function mdlObtenerIdEstudiantePorUsuario($usuarioId) {
        $stmt = Conexion::conectar()->prepare("SELECT id FROM estudiante WHERE usuarios_id = :usuarios_id");
        $stmt->bindParam(":usuarios_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado["id"] : null;
    }

    /*=============================================
    CREAR ESTUDIANTE
    =============================================*/
    static public function mdlCrearEstudiante($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(usuarios_id, codigo_estudiante, fecha_ingreso, grado_ingreso, estado_anio_anterior, estado_actual) VALUES (:usuarios_id, :codigo_estudiante, :fecha_ingreso, :grado_ingreso, :estado_anio_anterior, :estado_actual)");
        $stmt->bindParam(":usuarios_id", $datos["usuarios_id"], PDO::PARAM_INT);
        $stmt->bindParam(":codigo_estudiante", $datos["codigo_estudiante"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_ingreso", $datos["fecha_ingreso"], PDO::PARAM_STR);
        $stmt->bindParam(":grado_ingreso", $datos["grado_ingreso"], PDO::PARAM_STR);
        $stmt->bindParam(":estado_anio_anterior", $datos["estado_anio_anterior"], PDO::PARAM_STR);
        $stmt->bindParam(":estado_actual", $datos["estado_actual"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    /*=============================================
    CREAR MATRÍCULA
    =============================================*/
    static public function mdlCrearMatricula($tabla, $datos) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO $tabla(estudiante_id, grupo_id, fecha_matricula, numero_matricula, nuevo, estado_matricula) VALUES (:estudiante_id, :grupo_id, :fecha_matricula, :numero_matricula, :nuevo, :estado_matricula)");
        $stmt->bindParam(":estudiante_id", $datos["estudiante_id"], PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id", $datos["grupo_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_matricula", $datos["fecha_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":numero_matricula", $datos["numero_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":nuevo", $datos["nuevo"], PDO::PARAM_STR);
        $stmt->bindParam(":estado_matricula", $datos["estado_matricula"], PDO::PARAM_STR);
        return $stmt->execute() ? $conexion->lastInsertId() : "error";
    }

    /*=============================================
    CREAR ACUDIENTE
    =============================================*/
    static public function mdlCrearAcudiente($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(usuarios_id, matricula_id, parentesco, autorizado_recoger, observacion) VALUES (:usuarios_id, :matricula_id, :parentesco, :autorizado_recoger, :observacion)");
        $stmt->bindParam(":usuarios_id", $datos["usuarios_id"], PDO::PARAM_INT);
        $stmt->bindParam(":matricula_id", $datos["matricula_id"], PDO::PARAM_INT);
        $stmt->bindParam(":parentesco", $datos["parentesco"], PDO::PARAM_STR);
        $stmt->bindParam(":autorizado_recoger", $datos["autorizado_recoger"], PDO::PARAM_STR);
        $stmt->bindParam(":observacion", $datos["observacion"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
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
    EDITAR ESTUDIANTE
    =============================================*/
    static public function mdlEditarEstudiante($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET fecha_ingreso = :fecha_ingreso, grado_ingreso = :grado_ingreso, estado_anio_anterior = :estado_anio_anterior WHERE id = :id");
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_ingreso", $datos["fecha_ingreso"], PDO::PARAM_STR);
        $stmt->bindParam(":grado_ingreso", $datos["grado_ingreso"], PDO::PARAM_STR);
        $stmt->bindParam(":estado_anio_anterior", $datos["estado_anio_anterior"], PDO::PARAM_STR);
        return $stmt->execute() ? "ok" : "error";
    }

    /*=============================================
    EDITAR MATRÍCULA
    =============================================*/
    static public function mdlEditarMatricula($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET grupo_id = :grupo_id, fecha_matricula = :fecha_matricula, numero_matricula = :numero_matricula, nuevo = :nuevo, estado_matricula = :estado_matricula WHERE id = :id");
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":grupo_id", $datos["grupo_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_matricula", $datos["fecha_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":numero_matricula", $datos["numero_matricula"], PDO::PARAM_STR);
        $stmt->bindParam(":nuevo", $datos["nuevo"], PDO::PARAM_STR);
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
            $stmtAcudientes = $conexion->prepare("DELETE FROM acudiente WHERE matricula_id = :id");
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