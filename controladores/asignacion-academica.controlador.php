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

        if (!isset($_POST["cuerpo_docente_id"]) || !isset($_POST["asignaciones"]) || !isset($_POST["periodos"])) {
            return "error_datos_faltantes";
        }

        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);
        $asignaciones = $_POST["asignaciones"];
        $periodos = $_POST["periodos"];

        if (!$cuerpoDocenteId || !is_array($asignaciones) || empty($asignaciones) || !is_array($periodos) || empty($periodos)) {
            return "error_datos_invalidos";
        }

        if (count($periodos) < 1 || count($periodos) > 4) {
            return "error_periodos_invalidos";
        }

        $asignacionesCreadas = 0;
        $asignacionesDuplicadas = 0;
        $asignacionesError = 0;
        
        foreach ($asignaciones as $asignacion) {
            foreach ($periodos as $periodoId) {
                $datos = array(
                    "cuerpo_docente_id" => $cuerpoDocenteId,
                    "estructura_curricular_id" => $asignacion["estructura_curricular_id"],
                    "grupo_id" => $asignacion["grupo_id"],
                    "periodo_academico_id" => $periodoId,
                    "intensidad_horaria_semanal" => $asignacion["intensidad_horaria_semanal"]
                );

                $resultado = ModeloAsignacionAcademica::mdlCrearAsignacion($datos);
                if ($resultado == "ok") {
                    $asignacionesCreadas++;
                } elseif ($resultado == "asignacion_duplicada") {
                    $asignacionesDuplicadas++;
                } else {
                    $asignacionesError++;
                }
            }
        }

        // Devolver resultado detallado
        if ($asignacionesCreadas > 0 && $asignacionesDuplicadas == 0 && $asignacionesError == 0) {
            return "ok";
        } elseif ($asignacionesDuplicadas > 0 && $asignacionesCreadas == 0) {
            return "todas_duplicadas";
        } elseif ($asignacionesDuplicadas > 0 && $asignacionesCreadas > 0) {
            return "parcial_duplicadas";
        } else {
            return "error";
        }
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
    OBTENER PERÍODOS ACADÉMICOS
    =============================================*/
    static public function ctrObtenerPeriodos() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerPeriodos();
    }

    /*=============================================
    OBTENER ASIGNATURAS HABILITADAS DEL DOCENTE
    =============================================*/
    static public function ctrObtenerAsignaturasHabilitadas($cuerpoDocenteId) {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerAsignaturasHabilitadas($cuerpoDocenteId);
    }

    /*=============================================
    ACTUALIZAR ESTADO DE ASIGNACIONES
    =============================================*/
    static public function ctrActualizarEstadoAsignaciones() {
        if (!BackendProtector::protectController('asignacion_academica_editar')) {
            return "error_permisos";
        }

        if (!isset($_POST["cuerpo_docente_id"]) || !isset($_POST["periodos"]) || !isset($_POST["estado"])) {
            return "error_datos_faltantes";
        }

        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);
        $periodos = $_POST["periodos"];
        $estado = $_POST["estado"];

        if (!$cuerpoDocenteId || !is_array($periodos) || empty($periodos)) {
            return "error_datos_invalidos";
        }

        return ModeloAsignacionAcademica::mdlActualizarEstadoAsignaciones($cuerpoDocenteId, $periodos, $estado);
    }

    /*=============================================
    OBTENER DETALLE DE ASIGNACIÓN
    =============================================*/
    static public function ctrObtenerDetalleAsignacion() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }

        if (!isset($_POST["estructura_curricular_id"]) || !isset($_POST["grupo_id"]) || !isset($_POST["cuerpo_docente_id"])) {
            return array();
        }

        return ModeloAsignacionAcademica::mdlObtenerDetalleAsignacion(
            $_POST["estructura_curricular_id"], $_POST["grupo_id"], $_POST["cuerpo_docente_id"]
        );
    }

    /*=============================================
    VERIFICAR RELACIONES ACTIVAS
    =============================================*/
    static public function ctrVerificarRelacionesActivas() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }

        if (!isset($_POST["estructura_curricular_id"]) || !isset($_POST["grupo_id"]) || !isset($_POST["cuerpo_docente_id"])) {
            return array();
        }

        return ModeloAsignacionAcademica::mdlVerificarRelacionesActivas(
            $_POST["estructura_curricular_id"], $_POST["grupo_id"], $_POST["cuerpo_docente_id"]
        );
    }

    /*=============================================
    ELIMINAR ASIGNACIÓN POR PERÍODO
    =============================================*/
    static public function ctrEliminarAsignacionPorPeriodo() {
        if (!BackendProtector::protectController('asignacion_academica_eliminar')) {
            return "error_permisos";
        }

        if (!isset($_POST["cuerpo_docente_id"]) || !isset($_POST["estructura_curricular_id"]) || 
            !isset($_POST["grupo_id"]) || !isset($_POST["periodo_id"])) {
            return "error_datos_faltantes";
        }

        return ModeloAsignacionAcademica::mdlEliminarAsignacionesPorPeriodo(
            $_POST["cuerpo_docente_id"], $_POST["estructura_curricular_id"], 
            $_POST["grupo_id"], $_POST["periodo_id"]
        );
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

    /*=============================================
    OBTENER ESTADOS DE ASIGNACIÓN
    =============================================*/
    static public function ctrObtenerEstadosAsignacion() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }
        return ModeloAsignacionAcademica::mdlObtenerEstadosAsignacion();
    }

    /*=============================================
    ACTUALIZAR ASIGNACIÓN ESPECÍFICA
    =============================================*/
    static public function ctrActualizarAsignacionEspecifica() {
        if (!BackendProtector::protectController('asignacion_academica_editar')) {
            return "error_permisos";
        }

        if (!isset($_POST["cuerpo_docente_id"]) || !isset($_POST["estructura_curricular_id"]) || 
            !isset($_POST["grupo_id"]) || !isset($_POST["periodos"]) || !isset($_POST["estado"])) {
            return "error_datos_faltantes";
        }

        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);
        $estructuraId = filter_var($_POST["estructura_curricular_id"], FILTER_VALIDATE_INT);
        $grupoId = filter_var($_POST["grupo_id"], FILTER_VALIDATE_INT);
        $periodos = $_POST["periodos"];
        $estado = $_POST["estado"];

        if (!$cuerpoDocenteId || !$estructuraId || !$grupoId || !is_array($periodos) || empty($periodos)) {
            return "error_datos_invalidos";
        }

        return ModeloAsignacionAcademica::mdlActualizarAsignacionEspecifica(
            $cuerpoDocenteId, $estructuraId, $grupoId, $periodos, $estado
        );
    }

    /*=============================================
ELIMINAR ASIGNACIÓN COMPLETA
=============================================*/
    static public function ctrEliminarAsignacionCompleta() {
        if (!BackendProtector::protectController('asignacion_academica_eliminar')) {
            return "error_permisos";
        }

        // CAMBIO: Esperamos 'asignatura_id' en lugar de 'estructura_curricular_id'
        if (!isset($_POST["cuerpo_docente_id"]) || !isset($_POST["asignatura_id"]) || !isset($_POST["grupo_id"])) {
            return "error_datos_faltantes";
        }

        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);
        $asignaturaId = filter_var($_POST["asignatura_id"], FILTER_VALIDATE_INT);
        $grupoId = filter_var($_POST["grupo_id"], FILTER_VALIDATE_INT);

        if (!$cuerpoDocenteId || !$asignaturaId || !$grupoId) {
            return "error_datos_invalidos";
        }

        // Pasamos el asignaturaId al modelo
        return ModeloAsignacionAcademica::mdlEliminarAsignacionCompleta($cuerpoDocenteId, $asignaturaId, $grupoId);
    }

    /*=============================================
    OBTENER DATOS DEL MULTIGRADO
    =============================================*/
    static public function ctrObtenerDatosMultigrado() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }

        if (!isset($_POST["grupo_id"]) || !isset($_POST["asignatura_id"]) || !isset($_POST["cuerpo_docente_id"])) {
            return array();
        }

        $grupoId = filter_var($_POST["grupo_id"], FILTER_VALIDATE_INT);
        $asignaturaId = filter_var($_POST["asignatura_id"], FILTER_VALIDATE_INT);
        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);

        if (!$grupoId || !$asignaturaId || !$cuerpoDocenteId) {
            return array();
        }

        return ModeloAsignacionAcademica::mdlObtenerDatosMultigrado($grupoId, $asignaturaId, $cuerpoDocenteId);
    }

    /*=============================================
    OBTENER DATOS PARA EDICIÓN DEL MULTIGRADO
    =============================================*/
    static public function ctrObtenerDatosEdicionMultigrado() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }

        if (!isset($_POST["grupo_id"]) || !isset($_POST["asignatura_id"]) || !isset($_POST["cuerpo_docente_id"])) {
            return array();
        }

        $grupoId = filter_var($_POST["grupo_id"], FILTER_VALIDATE_INT);
        $asignaturaId = filter_var($_POST["asignatura_id"], FILTER_VALIDATE_INT);
        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);

        if (!$grupoId || !$asignaturaId || !$cuerpoDocenteId) {
            return array();
        }

        return ModeloAsignacionAcademica::mdlObtenerDatosEdicionMultigrado($grupoId, $asignaturaId, $cuerpoDocenteId);
    }

    /*=============================================
    ACTUALIZAR MULTIGRADO
    =============================================*/
    static public function ctrActualizarMultigrado() {
        if (!BackendProtector::protectController('asignacion_academica_editar')) {
            return "error_permisos";
        }

        if (!isset($_POST["cambios"]) || !isset($_POST["periodos_seleccionados"]) || 
            !isset($_POST["cuerpo_docente_id"]) || !isset($_POST["asignatura_id"])) {
            return "error_datos_faltantes";
        }

        $cambios = $_POST["cambios"];
        $eliminaciones = $_POST["eliminaciones"] ?? [];
        $periodosSeleccionados = $_POST["periodos_seleccionados"];
        $cuerpoDocenteId = filter_var($_POST["cuerpo_docente_id"], FILTER_VALIDATE_INT);
        $asignaturaId = filter_var($_POST["asignatura_id"], FILTER_VALIDATE_INT);

        if (!is_array($cambios) || !is_array($eliminaciones) || !is_array($periodosSeleccionados) || 
            !$cuerpoDocenteId || !$asignaturaId || empty($periodosSeleccionados)) {
            return "error_datos_invalidos";
        }

        return ModeloAsignacionAcademica::mdlActualizarMultigrado($cambios, $eliminaciones, $periodosSeleccionados, $cuerpoDocenteId, $asignaturaId);
    }

    /*=============================================
    VERIFICAR RELACIONES DE ASIGNACIÓN
    =============================================*/
    static public function ctrVerificarRelacionesAsignacion() {
        if (!BackendProtector::protectController('asignacion_academica_ver')) {
            return array();
        }

        if (!isset($_POST["asignacion_id"])) {
            return array();
        }

        $asignacionId = filter_var($_POST["asignacion_id"], FILTER_VALIDATE_INT);

        if (!$asignacionId) {
            return array();
        }

        return ModeloAsignacionAcademica::mdlVerificarRelacionesAsignacion($asignacionId);
    }
}
?>