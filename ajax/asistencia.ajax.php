<?php

session_start();
require_once "../modelos/conexion.php";
require_once "../controladores/asistencia.controlador.php";
require_once "../modelos/asistencia.modelo.php";

// Habilitar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de depuración
error_log("AJAX Asistencia - Datos POST recibidos: " . print_r($_POST, true));

class AjaxAsistencia {

    /*=============================================
    OBTENER ASIGNACIONES DOCENTE
    =============================================*/
    public $cuerpo_docente_id;
    public $periodo_id;

    public function ajaxObtenerAsignacionesDocente() {
        $_POST["cuerpo_docente_id"] = $this->cuerpo_docente_id;
        $_POST["periodo_id"] = $this->periodo_id;

        ControladorAsistencia::ctrObtenerAsignacionesDocente();
    }

    /*=============================================
    OBTENER ESTUDIANTES DEL GRUPO
    =============================================*/
    public $grupo_id;

    public function ajaxObtenerEstudiantesGrupo() {
        $_POST["grupo_id"] = $this->grupo_id;
        ControladorAsistencia::ctrObtenerEstudiantesGrupo();
    }

    /*=============================================
    REGISTRAR ASISTENCIA
    =============================================*/
    public $asignacion_id;
    public $fecha;
    public $hora_inicio;
    public $hora_fin;
    public $asistencias;

    public function ajaxRegistrarAsistencia() {
        $_POST["asignacion_id"] = $this->asignacion_id;
        $_POST["fecha"] = $this->fecha;
        $_POST["hora_inicio"] = $this->hora_inicio;
        $_POST["hora_fin"] = $this->hora_fin;
        $_POST["asistencias"] = $this->asistencias;

        $respuesta = ControladorAsistencia::ctrRegistrarAsistencia();
    }

    /*=============================================
    OBTENER ASISTENCIA EXISTENTE
    =============================================*/
    public function ajaxObtenerAsistenciaExistente() {
        $_POST["asignacion_id"] = $this->asignacion_id;
        $_POST["fecha"] = $this->fecha;

        $respuesta = ControladorAsistencia::ctrObtenerAsistenciaExistente();
    }
}

/*=============================================
OBTENER ASIGNACIONES DOCENTE
=============================================*/
if (isset($_POST["cuerpo_docente_id"]) && isset($_POST["periodo_id"])) {
    try {
        error_log("AJAX Asistencia: cuerpo_docente_id=" . $_POST["cuerpo_docente_id"] . ", periodo_id=" . $_POST["periodo_id"]);
        
        $asignaciones = new AjaxAsistencia();
        $asignaciones->cuerpo_docente_id = $_POST["cuerpo_docente_id"];
        $asignaciones->periodo_id = $_POST["periodo_id"];
        $asignaciones->ajaxObtenerAsignacionesDocente();
    } catch (Exception $e) {
        error_log("Error en AJAX Asistencia: " . $e->getMessage());
        echo json_encode(["error" => $e->getMessage()]);
    }
}

/*=============================================
OBTENER ESTUDIANTES DEL GRUPO
=============================================*/
if (isset($_POST["grupo_id"])) {
    try {
        error_log("AJAX Asistencia: Obteniendo estudiantes para grupo_id=" . $_POST["grupo_id"]);
        
        $estudiantes = new AjaxAsistencia();
        $estudiantes->grupo_id = $_POST["grupo_id"];
        $estudiantes->ajaxObtenerEstudiantesGrupo();
    } catch (Exception $e) {
        error_log("Error en AJAX obtener estudiantes: " . $e->getMessage());
        echo json_encode(["error" => $e->getMessage()]);
    }
}

/*=============================================
REGISTRAR ASISTENCIA
=============================================*/
if (isset($_POST["asignacion_id"]) && isset($_POST["asistencias"])) {
    $registrar = new AjaxAsistencia();
    $registrar->asignacion_id = $_POST["asignacion_id"];
    $registrar->fecha = $_POST["fecha"];
    $registrar->hora_inicio = $_POST["hora_inicio"];
    $registrar->hora_fin = $_POST["hora_fin"];
    $registrar->asistencias = $_POST["asistencias"];
    $registrar->ajaxRegistrarAsistencia();
}

/*=============================================
OBTENER ASISTENCIA EXISTENTE
=============================================*/
if (isset($_POST["obtener_asistencia_existente"])) {
    $obtener = new AjaxAsistencia();
    $obtener->asignacion_id = $_POST["asignacion_id"];
    $obtener->fecha = $_POST["fecha"];
    $obtener->ajaxObtenerAsistenciaExistente();
}