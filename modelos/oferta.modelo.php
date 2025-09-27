<?php

require_once "conexion.php";

class ModeloOfertaEducativa {

    /*=============================================
    MOSTRAR OFERTA EDUCATIVA CON GRUPOS
    =============================================*/

    static public function mdlMostrarOfertaEducativaConGrupos($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT oa.*, al.anio, s.nombre_sede, j.nombre as nombre_jornada, 
                                                   ne.nombre as nombre_nivel, g.nombre as nombre_grado,
                                                   gr.id as grupo_id, gr.nombre as nombre_grupo, gr.cupos, 
                                                   c.nombre as nombre_curso
                                                   FROM $tabla oa 
                                                   INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                                   INNER JOIN anio_lectivo al ON oa.anio_lectivo_id = al.id
                                                   INNER JOIN sede s ON sj.sede_id = s.id
                                                   INNER JOIN jornada j ON sj.jornada_id = j.id
                                                   INNER JOIN grado g ON oa.grado_id = g.id
                                                   INNER JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                                   INNER JOIN grupo gr ON oa.id = gr.oferta_educativa_id
                                                   INNER JOIN curso c ON gr.curso_id = c.id
                                                   WHERE oa.$item = :$item
                                                   ORDER BY oa.id, gr.id");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetchAll();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT oa.*, al.anio, s.nombre_sede, j.nombre as nombre_jornada, 
                                                   ne.nombre as nombre_nivel, g.nombre as nombre_grado,
                                                   gr.id as grupo_id, gr.nombre as nombre_grupo, gr.cupos, 
                                                   c.nombre as nombre_curso, s.id as sede_id, j.id as jornada_id,
                                                   ne.id as nivel_educativo_id
                                                   FROM $tabla oa 
                                                   INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                                   INNER JOIN anio_lectivo al ON oa.anio_lectivo_id = al.id
                                                   INNER JOIN sede s ON sj.sede_id = s.id
                                                   INNER JOIN jornada j ON sj.jornada_id = j.id
                                                   INNER JOIN grado g ON oa.grado_id = g.id
                                                   INNER JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                                   INNER JOIN grupo gr ON oa.id = gr.oferta_educativa_id
                                                   INNER JOIN curso c ON gr.curso_id = c.id
                                                   ORDER BY al.anio DESC, s.nombre_sede, j.nombre, ne.nombre, g.numero, c.nombre");
            $stmt -> execute();

            return $stmt -> fetchAll();
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    MOSTRAR OFERTA EDUCATIVA (MÉTODO ORIGINAL)
    =============================================*/

    static public function mdlMostrarOfertaEducativa($tabla, $item, $valor) {

        if($item != null) {

            $stmt = Conexion::conectar()->prepare("SELECT oa.*, al.anio, s.nombre_sede, j.nombre as nombre_jornada, 
                                                   ne.nombre as nombre_nivel, g.nombre as nombre_grado
                                                   FROM $tabla oa 
                                                   LEFT JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                                   LEFT JOIN anio_lectivo al ON oa.anio_lectivo_id = al.id
                                                   LEFT JOIN sede s ON sj.sede_id = s.id
                                                   LEFT JOIN jornada j ON sj.jornada_id = j.id
                                                   LEFT JOIN grado g ON oa.grado_id = g.id
                                                   LEFT JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                                   WHERE oa.$item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();

            return $stmt -> fetch();

        } else {

            $stmt = Conexion::conectar()->prepare("SELECT oa.*, al.anio, s.nombre_sede, j.nombre as nombre_jornada, 
                                                   ne.nombre as nombre_nivel, g.nombre as nombre_grado
                                                   FROM $tabla oa 
                                                   LEFT JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                                   LEFT JOIN anio_lectivo al ON oa.anio_lectivo_id = al.id
                                                   LEFT JOIN sede s ON sj.sede_id = s.id
                                                   LEFT JOIN jornada j ON sj.jornada_id = j.id
                                                   LEFT JOIN grado g ON oa.grado_id = g.id
                                                   LEFT JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                                   ORDER BY oa.id DESC");
            $stmt -> execute();

            return $stmt -> fetchAll();
        }

        $stmt -> close();
        $stmt = null;
    }

    /*=============================================
    REGISTRAR OFERTA EDUCATIVA
    =============================================*/

    static public function mdlIngresarOfertaEducativa($tabla, $datos) {

        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("INSERT INTO $tabla(grado_id, sede_jornada_id, anio_lectivo_id) VALUES (:grado_id, :sede_jornada_id, :anio_lectivo_id)");

            $stmt->bindParam(":grado_id", $datos["grado_id"], PDO::PARAM_INT);
            $stmt->bindParam(":sede_jornada_id", $datos["sede_jornada_id"], PDO::PARAM_INT);
            $stmt->bindParam(":anio_lectivo_id", $datos["anio_lectivo_id"], PDO::PARAM_INT);

            if($stmt->execute()) {
                $insertId = $conexion->lastInsertId();
                $stmt->closeCursor();
                return $insertId;
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL Oferta: " . print_r($errorInfo, true));
                $stmt->closeCursor();
                return false;
            }

        } catch(PDOException $e) {
            error_log("Error PDO Oferta: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    REGISTRAR GRUPO
    =============================================*/

    static public function mdlIngresarGrupo($tabla, $datos) {

        try {
            $conexion = Conexion::conectar();

            // Verificar que la oferta educativa existe
            $stmtVerify = $conexion->prepare("SELECT id FROM oferta_academica WHERE id = :id");
            $stmtVerify->bindParam(":id", $datos["oferta_educativa_id"], PDO::PARAM_INT);
            $stmtVerify->execute();

            if(!$stmtVerify->fetch()) {
                error_log("Error: oferta_educativa_id " . $datos["oferta_educativa_id"] . " no existe");
                return "error: oferta_educativa_id no existe";
            }
            $stmtVerify->closeCursor();

            $stmt = $conexion->prepare("INSERT INTO $tabla(oferta_educativa_id, curso_id, nombre, cupos) VALUES (:oferta_educativa_id, :curso_id, :nombre, :cupos)");

            $stmt->bindParam(":oferta_educativa_id", $datos["oferta_educativa_id"], PDO::PARAM_INT);
            $stmt->bindParam(":curso_id", $datos["curso_id"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":cupos", $datos["cupos"], PDO::PARAM_INT);

            if($stmt->execute()) {
                $stmt->closeCursor();
                return "ok";
            } else {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL Grupo: " . print_r($errorInfo, true));
                $stmt->closeCursor();
                return "error: " . $errorInfo[2];
            }

        } catch(PDOException $e) {
            error_log("Error PDO Grupo: " . $e->getMessage());
            return "error: " . $e->getMessage();
        }
    }

    /*=============================================
    VERIFICAR/CREAR SEDE JORNADA
    =============================================*/

    static public function mdlVerificarCrearSedeJornada($tabla, $datos) {

        try {
            // Verificar si ya existe
            $stmt = Conexion::conectar()->prepare("SELECT id FROM $tabla WHERE sede_id = :sede_id AND jornada_id = :jornada_id AND anio_lectivo_id = :anio_lectivo_id");
            $stmt->bindParam(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
            $stmt->bindParam(":jornada_id", $datos["jornada_id"], PDO::PARAM_INT);
            $stmt->bindParam(":anio_lectivo_id", $datos["anio_lectivo_id"], PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch();

            if($resultado) {
                return $resultado["id"];
            } else {
                // Crear nuevo registro
                $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(sede_id, jornada_id, anio_lectivo_id) VALUES (:sede_id, :jornada_id, :anio_lectivo_id)");
                $stmt->bindParam(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
                $stmt->bindParam(":jornada_id", $datos["jornada_id"], PDO::PARAM_INT);
                $stmt->bindParam(":anio_lectivo_id", $datos["anio_lectivo_id"], PDO::PARAM_INT);

                if($stmt->execute()) {
                    return Conexion::conectar()->lastInsertId();
                } else {
                    return false;
                }
            }

        } catch(PDOException $e) {
            error_log("Error en sede_jornada: " . $e->getMessage());
            return false;
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR DUPLICADO OFERTA EDUCATIVA
    =============================================*/

    static public function mdlVerificarDuplicadoOferta($tabla, $gradoId, $sedeJornadaId, $anioLectivoId) {

        $stmt = Conexion::conectar()->prepare("SELECT id FROM $tabla WHERE grado_id = :grado_id AND sede_jornada_id = :sede_jornada_id AND anio_lectivo_id = :anio_lectivo_id");
        $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $sedeJornadaId, PDO::PARAM_INT);
        $stmt->bindParam(":anio_lectivo_id", $anioLectivoId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();

        if($resultado) {
            return $resultado["id"];
        } else {
            return false;
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR CURSO OCUPADO EN GRADO
    =============================================*/

    static public function mdlVerificarCursoOcupado($gradoId, $cursoId, $sedeJornadaId, $anioLectivoId) {

        $stmt = Conexion::conectar()->prepare("SELECT g.id 
                                               FROM grupo g
                                               INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                                               WHERE oa.grado_id = :grado_id 
                                               AND g.curso_id = :curso_id 
                                               AND oa.sede_jornada_id = :sede_jornada_id 
                                               AND oa.anio_lectivo_id = :anio_lectivo_id");
        $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
        $stmt->bindParam(":curso_id", $cursoId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $sedeJornadaId, PDO::PARAM_INT);
        $stmt->bindParam(":anio_lectivo_id", $anioLectivoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch() ? true : false;

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER CURSOS OCUPADOS POR GRADO
    =============================================*/

    static public function mdlObtenerCursosOcupados($gradoId, $sedeJornadaId, $anioLectivoId) {

        $stmt = Conexion::conectar()->prepare("SELECT g.curso_id 
                                               FROM grupo g
                                               INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                                               WHERE oa.grado_id = :grado_id 
                                               AND oa.sede_jornada_id = :sede_jornada_id 
                                               AND oa.anio_lectivo_id = :anio_lectivo_id");
        $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $sedeJornadaId, PDO::PARAM_INT);
        $stmt->bindParam(":anio_lectivo_id", $anioLectivoId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetchAll();
        $cursosOcupados = array();

        foreach($resultado as $fila) {
            $cursosOcupados[] = $fila["curso_id"];
        }

        return $cursosOcupados;

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER AÑOS LECTIVOS
    =============================================*/

    static public function mdlObtenerAniosLectivos() {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id, anio FROM anio_lectivo ORDER BY anio DESC");
            $stmt->execute();

            $resultado = $stmt->fetchAll();

            if(empty($resultado)) {
                error_log("No hay años lectivos en la base de datos");
            }

            return $resultado;

        } catch(PDOException $e) {
            error_log("Error obteniendo años lectivos: " . $e->getMessage());
            return array();
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER SEDES
    =============================================*/

    static public function mdlObtenerSedes() {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre_sede FROM sede WHERE estado = 1 ORDER BY nombre_sede ASC");
            $stmt->execute();

            $resultado = $stmt->fetchAll();

            if(empty($resultado)) {
                error_log("No hay sedes activas en la base de datos");
            }

            return $resultado;

        } catch(PDOException $e) {
            error_log("Error obteniendo sedes: " . $e->getMessage());
            return array();
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER JORNADAS
    =============================================*/

    static public function mdlObtenerJornadas() {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM jornada ORDER BY nombre ASC");
            $stmt->execute();

            $resultado = $stmt->fetchAll();

            if(empty($resultado)) {
                error_log("No hay jornadas en la base de datos");
            }

            return $resultado;

        } catch(PDOException $e) {
            error_log("Error obteniendo jornadas: " . $e->getMessage());
            return array();
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER NIVELES EDUCATIVOS
    =============================================*/

    static public function mdlObtenerNivelesEducativos() {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM nivel_educativo ORDER BY nombre ASC");
            $stmt->execute();

            $resultado = $stmt->fetchAll();

            if(empty($resultado)) {
                error_log("No hay niveles educativos en la base de datos");
            }

            return $resultado;

        } catch(PDOException $e) {
            error_log("Error obteniendo niveles educativos: " . $e->getMessage());
            return array();
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER CURSOS
    =============================================*/

    static public function mdlObtenerCursos() {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id, tipo, nombre FROM curso ORDER BY tipo ASC, nombre ASC");
            $stmt->execute();

            $resultado = $stmt->fetchAll();

            if(empty($resultado)) {
                error_log("No hay cursos en la base de datos");
            }

            return $resultado;

        } catch(PDOException $e) {
            error_log("Error obteniendo cursos: " . $e->getMessage());
            return array();
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER GRADOS POR NIVEL
    =============================================*/

    static public function mdlObtenerGradosPorNivel($nivelId) {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM grado WHERE nivel_educativo_id = :nivel_id ORDER BY numero ASC");
            $stmt->bindParam(":nivel_id", $nivelId, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll();

            if(empty($resultado)) {
                error_log("No hay grados para el nivel educativo ID: " . $nivelId);
            }

            return $resultado;

        } catch(PDOException $e) {
            error_log("Error obteniendo grados por nivel: " . $e->getMessage());
            return array();
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    OBTENER GRADOS OCUPADOS
    =============================================*/

    static public function mdlObtenerGradosOcupados($sedeId, $jornadaId, $anioLectivoId) {

        $stmt = Conexion::conectar()->prepare("SELECT oa.grado_id 
                                               FROM oferta_academica oa
                                               LEFT JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                               WHERE sj.sede_id = :sede_id 
                                               AND sj.jornada_id = :jornada_id 
                                               AND oa.anio_lectivo_id = :anio_lectivo_id");
        $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmt->bindParam(":jornada_id", $jornadaId, PDO::PARAM_INT);
        $stmt->bindParam(":anio_lectivo_id", $anioLectivoId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetchAll();
        $gradosOcupados = array();

        foreach($resultado as $fila) {
            $gradosOcupados[] = $fila["grado_id"];
        }

        return $gradosOcupados;

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    EDITAR OFERTA EDUCATIVA
    =============================================*/

    static public function mdlEditarOfertaEducativa($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET grado_id = :grado_id, sede_jornada_id = :sede_jornada_id, anio_lectivo_id = :anio_lectivo_id WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":grado_id", $datos["grado_id"], PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $datos["sede_jornada_id"], PDO::PARAM_INT);
        $stmt->bindParam(":anio_lectivo_id", $datos["anio_lectivo_id"], PDO::PARAM_INT);

        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
   EDITAR GRUPO
   =============================================*/

    static public function mdlEditarGrupo($tabla, $datos) {

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET curso_id = :curso_id, cupos = :cupos, nombre = :nombre WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":curso_id", $datos["curso_id"], PDO::PARAM_INT);
        $stmt->bindParam(":cupos", $datos["cupos"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);

        if($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    BORRAR OFERTA EDUCATIVA
    =============================================*/

    static public function mdlBorrarOfertaEducativa($tabla, $datos) {

        // Primero eliminar los grupos asociados
        $stmt = Conexion::conectar()->prepare("DELETE FROM grupo WHERE oferta_educativa_id = :id");
        $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
        $stmt->execute();

        // Luego eliminar la oferta educativa
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
    MOSTRAR OFERTA POR INSTITUCIÓN DEL USUARIO
    =============================================*/

    static public function mdlMostrarOfertaPorInstitucion($idUsuario) {

        $stmt = Conexion::conectar()->prepare("SELECT oa.*, al.anio, s.nombre_sede, j.nombre as nombre_jornada, 
                                               ne.nombre as nombre_nivel, g.nombre as nombre_grado,
                                               gr.id as grupo_id, gr.nombre as nombre_grupo, gr.cupos, 
                                               c.nombre as nombre_curso, s.id as sede_id, j.id as jornada_id,
                                               ne.id as nivel_educativo_id
                                               FROM oferta_academica oa 
                                               INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                               INNER JOIN anio_lectivo al ON oa.anio_lectivo_id = al.id
                                               INNER JOIN sede s ON sj.sede_id = s.id
                                               INNER JOIN institucion i ON s.institucion_id = i.id
                                               INNER JOIN jornada j ON sj.jornada_id = j.id
                                               INNER JOIN grado g ON oa.grado_id = g.id
                                               INNER JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                               INNER JOIN grupo gr ON oa.id = gr.oferta_educativa_id
                                               INNER JOIN curso c ON gr.curso_id = c.id
                                               WHERE i.id_usuario_representante = :id_usuario
                                               ORDER BY al.anio DESC, s.nombre_sede, j.nombre, ne.nombre, g.numero, c.nombre");
        $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();

        $stmt->close();
        $stmt = null;
    }

    /*=============================================
    VERIFICAR SI GRUPO TIENE ESTUDIANTES MATRICULADOS
    =============================================*/

    static public function mdlVerificarEstudiantesEnGrupo($grupoId) {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM matricula WHERE grupo_id = :grupo_id AND estado_matricula = 'Matriculado'");
        $stmt->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    }

    /*=============================================
    CONTAR GRUPOS EN OFERTA EDUCATIVA
    =============================================*/

    static public function mdlContarGruposEnOferta($ofertaId) {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM grupo WHERE oferta_educativa_id = :oferta_id");
        $stmt->bindParam(":oferta_id", $ofertaId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    /*=============================================
    VERIFICAR REFERENCIAS DE OFERTA EDUCATIVA
    =============================================*/

    static public function mdlVerificarReferenciasOferta($ofertaId) {
        $referencias = array();
        
        // Verificar estructura_curricular
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM estructura_curricular WHERE oferta_academica_id = :oferta_id");
        $stmt->bindParam(":oferta_id", $ofertaId, PDO::PARAM_INT);
        $stmt->execute();
        $estructuraCurricular = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($estructuraCurricular > 0) {
            $referencias[] = "Estructura Curricular ($estructuraCurricular registros)";
        }
        
        return $referencias;
    }

    /*=============================================
    BORRAR GRUPO (CON LÓGICA DE OFERTA)
    =============================================*/

    static public function mdlBorrarGrupo($tabla, $datos) {
        $db = Conexion::conectar();
        
        try {
            $db->beginTransaction();
            
            // Obtener oferta_educativa_id antes de borrar
            $stmt = $db->prepare("SELECT oferta_educativa_id FROM grupo WHERE id = :id");
            $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultado) {
                $db->rollBack();
                return "error";
            }
            
            $ofertaId = $resultado['oferta_educativa_id'];
            
            // Contar grupos antes de eliminar
            $totalGrupos = self::mdlContarGruposEnOferta($ofertaId);
            
            // Eliminar el grupo
            $stmt = $db->prepare("DELETE FROM grupo WHERE id = :id");
            $stmt->bindParam(":id", $datos, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                $db->rollBack();
                return "error";
            }
            
            // Si era el último grupo, eliminar la oferta educativa
            if ($totalGrupos == 1) {
                $stmt = $db->prepare("DELETE FROM oferta_academica WHERE id = :oferta_id");
                $stmt->bindParam(":oferta_id", $ofertaId, PDO::PARAM_INT);
                if (!$stmt->execute()) {
                    $db->rollBack();
                    return "error";
                }
            }
            
            $db->commit();
            return "ok";
            
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return "error";
        }
    }
}
?>