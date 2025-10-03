<?php
session_start();

require_once "../controladores/asignacion-academica.controlador.php";
require_once "../modelos/asignacion-academica.modelo.php";
require_once "../modelos/conexion.php";
require_once "../middleware/BackendProtector.php";
require_once "../servicios/ServicioAutorizacion.php";

class AjaxAsignacionAcademica {
    // Los métodos llaman al controlador, que a su vez usa el modelo corregido
    public function ajaxObtenerDocentes() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerDocentesPorSede($_POST["sede_id"]));
    }
    public function ajaxObtenerGrados() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerGradosPorSede($_POST["sede_id"]));
    }
    public function ajaxObtenerGrupos() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerGruposPorGradoSede($_POST["grado_id"], $_POST["sede_id"]));
    }
    public function ajaxObtenerAsignaturas() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerAsignaturasPorGrado($_POST["grado_id"], $_POST["sede_id"]));
    }
    public function ajaxObtenerAsignaciones() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerAsignacionesDocente($_POST["cuerpo_docente_id"]));
    }
    public function ajaxCrearAsignacion() {
        echo json_encode(ControladorAsignacionAcademica::ctrCrearAsignacion());
    }
    public function ajaxEliminarAsignacion() {
        echo json_encode(ControladorAsignacionAcademica::ctrEliminarAsignacion());
    }
    public function ajaxObtenerPeriodos() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerPeriodos());
    }
    public function ajaxObtenerAsignaturasHabilitadas() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerAsignaturasHabilitadas($_POST["cuerpo_docente_id"]));
    }
    public function ajaxActualizarEstadoAsignaciones() {
        echo json_encode(ControladorAsignacionAcademica::ctrActualizarEstadoAsignaciones());
    }
    public function ajaxObtenerDetalleAsignacion() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerDetalleAsignacion());
    }
    public function ajaxVerificarRelacionesActivas() {
        echo json_encode(ControladorAsignacionAcademica::ctrVerificarRelacionesActivas());
    }
    public function ajaxEliminarAsignacionPorPeriodo() {
        echo json_encode(ControladorAsignacionAcademica::ctrEliminarAsignacionPorPeriodo());
    }
    public function ajaxCalcularHoras() {
        echo json_encode(ControladorAsignacionAcademica::ctrCalcularHorasAsignadas($_POST["cuerpo_docente_id"]));
    }
    public function ajaxObtenerEstadosAsignacion() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerEstadosAsignacion());
    }
    public function ajaxActualizarAsignacionEspecifica() {
        echo json_encode(ControladorAsignacionAcademica::ctrActualizarAsignacionEspecifica());
    }
    public function ajaxEliminarAsignacionCompleta() {
        echo json_encode(ControladorAsignacionAcademica::ctrEliminarAsignacionCompleta());
    }
    public function ajaxObtenerDatosMultigrado() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerDatosMultigrado());
    }
    public function ajaxObtenerDatosEdicionMultigrado() {
        echo json_encode(ControladorAsignacionAcademica::ctrObtenerDatosEdicionMultigrado());
    }
    public function ajaxActualizarMultigrado() {
        echo json_encode(ControladorAsignacionAcademica::ctrActualizarMultigrado());
    }
    public function ajaxVerificarRelacionesAsignacion() {
        echo json_encode(ControladorAsignacionAcademica::ctrVerificarRelacionesAsignacion());
    }
}

if (isset($_POST["accion"])) {
    $ajax = new AjaxAsignacionAcademica();
    $nombreMetodo = 'ajax' . ucfirst($_POST["accion"]);

    if (method_exists($ajax, $nombreMetodo)) {
        // La protección de permisos se mantiene dentro de cada método del controlador
        $ajax->$nombreMetodo();
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No se especificó acción']);
}