<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    require_once dirname(__DIR__) . "/controladores/asignacion-docente-asignaturas.controlador.php";
    require_once dirname(__DIR__) . "/modelos/asignacion-docente-asignaturas.modelo.php";
    require_once dirname(__DIR__) . "/modelos/conexion.php";
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error cargando archivos: ' . $e->getMessage()));
    exit;
}

class AjaxAsignacionDocenteAsignaturas {

    public function ajaxObtenerDocentes() {
        try {
            $sedeId = $this->sedeId;
            $docentes = ControladorAsignacionDocenteAsignaturas::ctrObtenerDocentes($sedeId);
            echo json_encode($docentes);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function ajaxObtenerAsignaturas() {
        try {
            $sedeId = $this->sedeId;
            $asignaturas = ControladorAsignacionDocenteAsignaturas::ctrObtenerAsignaturas($sedeId);
            echo json_encode($asignaturas);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function ajaxObtenerAsignaciones() {
        try {
            $cuerpoDocenteId = $this->cuerpoDocenteId;
            $asignaciones = ControladorAsignacionDocenteAsignaturas::ctrObtenerAsignaciones($cuerpoDocenteId);
            echo json_encode($asignaciones);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function ajaxAsignarAsignaturas() {
        try {
            // Validar datos requeridos antes de procesar
            if(!isset($_POST["rol_institucional_id"]) || !isset($_POST["asignaturas"])) {
                http_response_code(400);
                echo json_encode(array('error' => 'Datos requeridos faltantes'));
                return;
            }
            
            $resultado = ControladorAsignacionDocenteAsignaturas::ctrAsignarAsignaturas();
            
            if($resultado === "error") {
                http_response_code(500);
                echo json_encode(array('error' => 'Error al asignar asignaturas. Verifique los datos e inténtelo nuevamente.'));
            } else {
                echo json_encode($resultado);
            }
        } catch(Exception $e) {
            error_log("Excepción en ajaxAsignarAsignaturas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(array('error' => 'Error interno del servidor'));
        }
    }

    public function ajaxEliminarAsignacion() {
        try {
            $resultado = ControladorAsignacionDocenteAsignaturas::ctrEliminarAsignacion();
            echo json_encode($resultado);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function ajaxActualizarHorasSemanales() {
        try {
            $resultado = ControladorAsignacionDocenteAsignaturas::ctrActualizarHorasSemanales();
            echo json_encode($resultado);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function ajaxObtenerSedesPorInstitucion() {
        try {
            // Asegurar que el institucion_id esté disponible en $_POST para el controlador
            if (!isset($_POST['institucion_id'])) {
                echo json_encode(array('error' => 'ID de institución requerido'));
                return;
            }
            $sedes = ControladorAsignacionDocenteAsignaturas::ctrObtenerSedes();
            echo json_encode($sedes);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function ajaxObtenerInstituciones() {
        try {
            $instituciones = ControladorAsignacionDocenteAsignaturas::ctrObtenerInstituciones();
            echo json_encode($instituciones);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(array('error' => $e->getMessage()));
        }
    }
}

if(isset($_POST["accion"])) {
    try {
        $asignacion = new AjaxAsignacionDocenteAsignaturas();
        
        if($_POST["accion"] == "obtenerDocentes") {
            $asignacion->sedeId = $_POST["sede_id"];
            $asignacion->ajaxObtenerDocentes();
        }
        
        if($_POST["accion"] == "obtenerAsignaturas") {
            $asignacion->sedeId = $_POST["sede_id"];
            $asignacion->ajaxObtenerAsignaturas();
        }
        
        if($_POST["accion"] == "obtenerAsignaciones") {
            $asignacion->cuerpoDocenteId = $_POST["cuerpo_docente_id"];
            $asignacion->ajaxObtenerAsignaciones();
        }
        
        if($_POST["accion"] == "asignarAsignaturas") {
            $asignacion->ajaxAsignarAsignaturas();
        }
        
        if($_POST["accion"] == "eliminarAsignacion") {
            $asignacion->ajaxEliminarAsignacion();
        }
        
        if($_POST["accion"] == "actualizarHorasSemanales") {
            $asignacion->ajaxActualizarHorasSemanales();
        }
        
        if($_POST["accion"] == "obtenerSedesPorInstitucion") {
            $asignacion->ajaxObtenerSedesPorInstitucion();
        }
        
        if($_POST["accion"] == "obtenerInstituciones") {
            $asignacion->ajaxObtenerInstituciones();
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Error general: ' . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'Acción no especificada'));
}