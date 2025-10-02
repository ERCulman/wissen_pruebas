<?php
// CÓDIGO FINAL Y CORREGIDO para: controladores/asignacion-docente-asignaturas.controlador.php

class ControladorAsignacionDocenteAsignaturas
{

    /**
     * MÉTODO CORREGIDO: Se ha eliminado el 'require_once' redundante.
     * Ahora confía en que index.php ya ha cargado auth.controlador.php.
     */
    static private function ctrObtenerContextoAcceso()
    {
        if (!isset($_SESSION["iniciarSesion"]) || $_SESSION["iniciarSesion"] != "ok") {
            return ["acceso" => false, "esAdmin" => false, "institucionId" => null];
        }

        // La clase ServicioAutorizacion ya existe porque fue cargada por index.php
        $auth = ServicioAutorizacion::getInstance();

        if ($auth->esRolAdmin()) {
            return ["acceso" => true, "esAdmin" => true, "institucionId" => null];
        }

        if ($auth->tieneAlcanceInstitucional()) {
            // Obtener el ID de institución directamente desde la base de datos
            $idUsuarioActual = $_SESSION['id_usuario'] ?? 0;
            $institucionId = ModeloAsignacionDocenteAsignaturas::mdlObtenerInstitucionIdUsuario($idUsuarioActual);
            if ($institucionId) {
                return ["acceso" => true, "esAdmin" => false, "institucionId" => $institucionId];
            }
        }

        return ["acceso" => false, "esAdmin" => false, "institucionId" => null];
    }

    /**
     * Valida el acceso usando el método central.
     */
    static public function ctrValidarAcceso()
    {
        return self::ctrObtenerContextoAcceso();
    }

    /**
     * Obtiene instituciones según el contexto del usuario.
     */
    static public function ctrObtenerInstituciones()
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if ($contexto["acceso"]) {
            return ModeloAsignacionDocenteAsignaturas::mdlObtenerTodasLasInstituciones();
        }
        return [];
    }

    /**
     * Obtiene sedes según el contexto del usuario.
     */
    static public function ctrObtenerSedes()
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if (!$contexto["acceso"]) {
            return [];
        }

        $institucionId = null;

        // Para superadmin, usar el institucion_id enviado por POST
        if ($contexto["esAdmin"]) {
            if (isset($_POST['institucion_id']) && !empty($_POST['institucion_id'])) {
                $institucionId = filter_var($_POST['institucion_id'], FILTER_VALIDATE_INT);
            }
        } else {
            // Para roles institucionales, usar su institución
            $institucionId = $contexto["institucionId"];
        }

        if (!$institucionId) {
            return [];
        }

        return ModeloAsignacionDocenteAsignaturas::mdlObtenerSedesInstitucion($institucionId);
    }

    /**
     * Pasa el contexto de la institución al modelo para un filtrado seguro.
     */
    static public function ctrObtenerDocentes($sedeId)
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if (!$contexto["acceso"]) return [];

        return ModeloAsignacionDocenteAsignaturas::mdlObtenerDocentesPorSede($sedeId, $contexto["institucionId"]);
    }

    /**
     * Pasa el contexto de la institución al modelo.
     */
    static public function ctrObtenerAsignaturas($sedeId)
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if (!$contexto["acceso"]) return [];

        return ModeloAsignacionDocenteAsignaturas::mdlObtenerAsignaturasPorSede($sedeId, $contexto["institucionId"]);
    }

    /**
     * Pasa el contexto de la institución al modelo.
     */
    static public function ctrObtenerAsignaciones($cuerpoDocenteId)
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if (!$contexto["acceso"]) return [];

        return ModeloAsignacionDocenteAsignaturas::mdlObtenerAsignacionesDocente($cuerpoDocenteId, $contexto["institucionId"]);
    }

    /**
     * Lógica de validación de acceso antes de ejecutar la acción.
     */
    static public function ctrAsignarAsignaturas()
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if (!$contexto["acceso"]) {
            return "error_permisos";
        }

        // El resto de la lógica de la función permanece igual...
        try {
            if (!isset($_POST["rol_institucional_id"]) || !isset($_POST["asignaturas"])) {
                return "error_datos_faltantes";
            }

            $rolInstitucionalId = filter_var($_POST["rol_institucional_id"], FILTER_VALIDATE_INT);
            $asignaturas = $_POST["asignaturas"];
            if (!$rolInstitucionalId || !is_array($asignaturas) || empty($asignaturas)) {
                return "error_datos_invalidos";
            }

            $maxHoras = isset($_POST["max_horas"]) ? filter_var($_POST["max_horas"], FILTER_VALIDATE_INT, ["options" => ["default" => 20, "min_range" => 1, "max_range" => 40]]) : 20;
            $observaciones = isset($_POST["observaciones"]) ? trim($_POST["observaciones"]) : "";

            $cuerpoDocenteId = ModeloAsignacionDocenteAsignaturas::mdlObtenerCuerpoDocentePorRol($rolInstitucionalId);

            if (!$cuerpoDocenteId) {
                $cuerpoDocenteId = ModeloAsignacionDocenteAsignaturas::mdlCrearCuerpoDocente($rolInstitucionalId, $maxHoras, $observaciones);
                if (!$cuerpoDocenteId || $cuerpoDocenteId == "error") {
                    return "error_creacion_cuerpo_docente";
                }
            }

            $asignadas = 0;
            foreach ($asignaturas as $estructuraCurricularId) {
                $estructuraId = filter_var($estructuraCurricularId, FILTER_VALIDATE_INT);
                if ($estructuraId && ModeloAsignacionDocenteAsignaturas::mdlAsignarAsignatura($cuerpoDocenteId, $estructuraId) == "ok") {
                    $asignadas++;
                }
            }
            return ($asignadas > 0) ? "ok" : "sin_cambios";
        } catch (Exception $e) {
            return "error_excepcion";
        }
    }

    /**
     * Lógica de validación de acceso.
     */
    static public function ctrEliminarAsignacion()
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if (!$contexto["acceso"] || !isset($_POST["asignacion_id"])) {
            return "error";
        }
        return ModeloAsignacionDocenteAsignaturas::mdlEliminarAsignacion($_POST["asignacion_id"]);
    }

    /**
     * Lógica de validación de acceso.
     */
    static public function ctrActualizarHorasSemanales()
    {
        $contexto = self::ctrObtenerContextoAcceso();
        if (!$contexto["acceso"] || !isset($_POST["cuerpo_docente_id"]) || !isset($_POST["horas_semanales"])) {
            return "error";
        }

        $cuerpoDocenteId = $_POST["cuerpo_docente_id"];
        $horasSemanales = $_POST["horas_semanales"];

        return ModeloAsignacionDocenteAsignaturas::mdlActualizarHorasSemanales($cuerpoDocenteId, $horasSemanales);
    }
}