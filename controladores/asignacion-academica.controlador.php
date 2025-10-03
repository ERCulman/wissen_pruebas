<?php

require_once __DIR__ . "/../modelos/asignacion-academica.modelo.php";
class ControladorAsignacionAcademica {

    /*=============================================
    VALIDAR ACCESO AL MÓDULO
    =============================================*/
    static public function ctrValidarAcceso() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return ["acceso" => false, "esAdmin" => false];
        }

        $auth = ServicioAutorizacion::getInstance();

        if ($auth->esRolAdmin()) {
            return ["acceso" => true, "esAdmin" => true];
        }

        if ($auth->tieneAlcanceInstitucional()) {
            return ["acceso" => true, "esAdmin" => false];
        }

        return ["acceso" => false, "esAdmin" => false];
    }

    /*=============================================
    OBTENER SEDES
    =============================================*/
    static public function ctrObtenerSedes() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerSedes();
    }

    /*=============================================
    OBTENER DOCENTES POR SEDE
    =============================================*/
    static public function ctrObtenerDocentesPorSede($sedeId) {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerDocentesPorSede($sedeId);
    }

    /*=============================================
    OBTENER GRADOS POR SEDE
    =============================================*/
    static public function ctrObtenerGradosPorSede($sedeId) {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerGradosPorSede($sedeId);
    }

    /*=============================================
    OBTENER GRUPOS POR GRADO Y SEDE
    =============================================*/
    static public function ctrObtenerGruposPorGradoSede($gradoId, $sedeId) {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerGruposPorGradoSede($gradoId, $sedeId);
    }

    /*=============================================
    OBTENER ASIGNATURAS POR GRADO
    =============================================*/
    static public function ctrObtenerAsignaturasPorGrado($gradoId, $sedeId) {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerAsignaturasPorGrado($gradoId, $sedeId);
    }

    /*=============================================
    OBTENER ASIGNACIONES DEL DOCENTE
    =============================================*/
    static public function ctrObtenerAsignacionesDocente($cuerpoDocenteId) {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerAsignacionesDocente($cuerpoDocenteId);
    }

    /*=============================================
    CREAR ASIGNACIÓN ACADÉMICA
    =============================================*/
    static public function ctrCrearAsignacion() {
        if (!BackendProtector::protectController('asignacion_academica_crear')) {
            return "error_permisos";
        }

        if (!isset($_POST["cuerpo_docente_id"]) || !isset($_POST["asignaciones"])) {
            return "error_datos_faltantes";
        }

        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);
        $asignaciones = $_POST["asignaciones"];

        if (!$cuerpoDocenteId || !is_array($asignaciones) || empty($asignaciones)) {
            return "error_datos_invalidos";
        }

        $periodoId = ModeloAsignacionAcademica::mdlObtenerPeriodoActivo();
        if (!$periodoId) {
            return "error_periodo_no_activo";
        }

        $asignacionesCreadas = 0;
        foreach ($asignaciones as $asignacion) {
            $datos = array(
                "cuerpo_docente_id" => $cuerpoDocenteId,
                "estructura_curricular_id" => $asignacion["estructura_curricular_id"],
                "grupo_id" => $asignacion["grupo_id"],
                "periodo_academico_id" => $periodoId,
                "intensidad_horaria_semanal" => $asignacion["intensidad_horaria_semanal"]
            );

            if (ModeloAsignacionAcademica::mdlCrearAsignacion($datos) == "ok") {
                $asignacionesCreadas++;
            }
        }

        return $asignacionesCreadas > 0 ? "ok" : "error";
    }

    /*=============================================
    ELIMINAR ASIGNACIÓN ACADÉMICA
    =============================================*/
    static public function ctrEliminarAsignacion() {
        if (!BackendProtector::protectController('asignacion_academica_eliminar')) {
            return "error_permisos";
        }

        if (!isset($_POST["asignacion_id"])) {
            return "error_datos_faltantes";
        }

        $asignacionId = filter_var($_POST["asignacion_id"], FILTER_VALIDATE_INT);
        if (!$asignacionId) {
            return "error_datos_invalidos";
        }

        return ModeloAsignacionAcademica::mdlEliminarAsignacion($asignacionId);
    }

    /*=============================================
    CALCULAR HORAS ASIGNADAS
    =============================================*/
    static public function ctrCalcularHorasAsignadas($cuerpoDocenteId) {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return 0;
        }
        return ModeloAsignacionAcademica::mdlCalcularHorasAsignadas($cuerpoDocenteId);
    }
}
?>