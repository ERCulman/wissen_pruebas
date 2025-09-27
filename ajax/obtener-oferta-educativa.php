<?php
require_once "global-protection.php";
require_once "../controladores/oferta.controlador.php";
require_once "../modelos/oferta.modelo.php";
require_once "../modelos/conexion.php";

// OBTENER GRADOS POR NIVEL EDUCATIVO
if(isset($_POST['accion']) && $_POST['accion'] == 'obtenerGrados') {
    $nivelId = $_POST['nivelId'];
    $respuesta = ControladorOfertaEducativa::ctrObtenerGradosPorNivel($nivelId);
    echo json_encode($respuesta);
}

// OBTENER CURSOS
if(isset($_POST['accion']) && $_POST['accion'] == 'obtenerCursos') {
    $respuesta = ControladorOfertaEducativa::ctrObtenerCursos();
    echo json_encode($respuesta);
}

// OBTENER CURSOS OCUPADOS POR GRADO
if(isset($_POST['accion']) && $_POST['accion'] == 'obtenerCursosOcupados') {
    $gradoId = $_POST['gradoId'];
    $sedeId = $_POST['sedeId'];
    $jornadaId = $_POST['jornadaId'];
    $anioLectivoId = $_POST['anioLectivoId'];

    // Primero obtener o crear sede_jornada_id
    $datosSedeJornada = array(
        "sede_id" => $sedeId,
        "jornada_id" => $jornadaId,
        "anio_lectivo_id" => $anioLectivoId
    );

    $sedeJornadaId = ModeloOfertaEducativa::mdlVerificarCrearSedeJornada("sede_jornada", $datosSedeJornada);

    if($sedeJornadaId) {
        $cursosOcupados = ControladorOfertaEducativa::ctrObtenerCursosOcupados($gradoId, $sedeJornadaId, $anioLectivoId);
        echo json_encode($cursosOcupados);
    } else {
        echo json_encode(array());
    }
}

// OBTENER GRADOS OCUPADOS
if(isset($_POST['accion']) && $_POST['accion'] == 'obtenerGradosOcupados') {
    $sedeId = $_POST['sedeId'];
    $jornadaId = $_POST['jornadaId'];
    $anioLectivoId = $_POST['anioLectivoId'];
    $respuesta = ControladorOfertaEducativa::ctrObtenerGradosOcupados($sedeId, $jornadaId, $anioLectivoId);
    echo json_encode($respuesta);
}

// OBTENER DATOS COMPLETOS DE UN GRUPO
if(isset($_POST['accion']) && $_POST['accion'] == 'obtenerDatosGrupo') {
    $grupoId = $_POST['grupoId'];
    $ofertaId = $_POST['ofertaId'];

    $stmt = Conexion::conectar()->prepare("SELECT 
        g.id as grupo_id, g.nombre as nombre_grupo, g.cupos,
        oa.id as oferta_id, oa.grado_id, oa.anio_lectivo_id,
        sj.id as sede_jornada_id, sj.sede_id, sj.jornada_id,
        al.anio, s.nombre_sede, j.nombre as nombre_jornada,
        ne.nombre as nombre_nivel, gr.nombre as nombre_grado,
        c.id as curso_id, c.nombre as nombre_curso
        FROM grupo g
        INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
        INNER JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
        INNER JOIN anio_lectivo al ON oa.anio_lectivo_id = al.id
        INNER JOIN sede s ON sj.sede_id = s.id
        INNER JOIN jornada j ON sj.jornada_id = j.id
        INNER JOIN grado gr ON oa.grado_id = gr.id
        INNER JOIN nivel_educativo ne ON gr.nivel_educativo_id = ne.id
        INNER JOIN curso c ON g.curso_id = c.id
        WHERE g.id = :grupo_id AND oa.id = :oferta_id");

    $stmt->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
    $stmt->bindParam(":oferta_id", $ofertaId, PDO::PARAM_INT);
    $stmt->execute();

    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);

    if($respuesta) {
        echo json_encode($respuesta);
    } else {
        echo json_encode(array("error" => "No se encontraron datos del grupo"));
    }
}

// OBTENER CURSOS PARA EDICIÓN DE GRUPO
if(isset($_POST['accion']) && $_POST['accion'] == 'obtenerCursosParaEdicion') {
    $gradoId = $_POST['gradoId'];
    $sedeJornadaId = $_POST['sedeJornadaId'];
    $anioLectivoId = $_POST['anioLectivoId'];
    $grupoActualId = $_POST['grupoActualId'];

    // Obtener cursos ocupados (excluyendo el grupo actual)
    $stmt = Conexion::conectar()->prepare("SELECT g.curso_id 
                                           FROM grupo g
                                           INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                                           WHERE oa.grado_id = :grado_id 
                                           AND oa.sede_jornada_id = :sede_jornada_id 
                                           AND oa.anio_lectivo_id = :anio_lectivo_id
                                           AND g.id != :grupo_actual_id");
    $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
    $stmt->bindParam(":sede_jornada_id", $sedeJornadaId, PDO::PARAM_INT);
    $stmt->bindParam(":anio_lectivo_id", $anioLectivoId, PDO::PARAM_INT);
    $stmt->bindParam(":grupo_actual_id", $grupoActualId, PDO::PARAM_INT);
    $stmt->execute();

    $cursosOcupados = array();
    while ($row = $stmt->fetch()) {
        $cursosOcupados[] = $row["curso_id"];
    }

    // Obtener todos los cursos
    $stmt = Conexion::conectar()->prepare("SELECT id, nombre, tipo FROM curso ORDER BY tipo ASC, nombre ASC");
    $stmt->execute();
    $todosCursos = $stmt->fetchAll();

    // Filtrar cursos disponibles
    $cursosDisponibles = array();
    foreach ($todosCursos as $curso) {
        if (!in_array($curso["id"], $cursosOcupados)) {
            $cursosDisponibles[] = $curso;
        }
    }

    echo json_encode($cursosDisponibles);
}

// VERIFICAR SI GRUPO PUEDE SER ELIMINADO
if(isset($_POST['accion']) && $_POST['accion'] == 'verificarEliminacionGrupo') {
    $grupoId = $_POST['grupoId'];
    
    // Verificar estudiantes matriculados
    $tieneEstudiantes = ModeloOfertaEducativa::mdlVerificarEstudiantesEnGrupo($grupoId);
    
    // Obtener información del grupo
    $stmt = Conexion::conectar()->prepare("SELECT oferta_educativa_id FROM grupo WHERE id = :id");
    $stmt->bindParam(":id", $grupoId, PDO::PARAM_INT);
    $stmt->execute();
    $grupoInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $esUltimoGrupo = false;
    if($grupoInfo) {
        $totalGrupos = ModeloOfertaEducativa::mdlContarGruposEnOferta($grupoInfo['oferta_educativa_id']);
        $esUltimoGrupo = ($totalGrupos == 1);
    }
    
    echo json_encode(array(
        'puedeEliminar' => !$tieneEstudiantes,
        'tieneEstudiantes' => $tieneEstudiantes,
        'esUltimoGrupo' => $esUltimoGrupo
    ));
}

// OBTENER DATOS ESPECÍFICOS DE OFERTA EDUCATIVA (COMPATIBILIDAD)
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $item = "oa.id";
    $valor = $id;

    // Consulta completa con JOINS para obtener todos los datos
    $stmt = Conexion::conectar()->prepare("SELECT oa.*, al.anio, s.nombre_sede, j.nombre as nombre_jornada, 
                                       ne.nombre as nombre_nivel, g.nombre as nombre_grado,
                                       s.id as sede_id, j.id as jornada_id, ne.id as nivel_educativo_id
                                       FROM oferta_academica oa 
                                       LEFT JOIN sede_jornada sj ON oa.sede_jornada_id = sj.id
                                       LEFT JOIN anio_lectivo al ON oa.anio_lectivo_id = al.id
                                       LEFT JOIN sede s ON sj.sede_id = s.id
                                       LEFT JOIN jornada j ON sj.jornada_id = j.id
                                       LEFT JOIN grado g ON oa.grado_id = g.id
                                       LEFT JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id
                                       WHERE oa.id = :id");
    $stmt->bindParam(":id", $valor, PDO::PARAM_INT);
    $stmt->execute();

    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($respuesta) {
        echo json_encode($respuesta);
    } else {
        echo json_encode(array("error" => "No se encontraron datos"));
    }
}
?>