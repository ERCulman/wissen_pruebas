<?php

class ControladorAsignacionDocenteAsignaturas {

    static public function ctrValidarAcceso() {
        if(isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {
            $accesos = ModeloAsignacionDocenteAsignaturas::mdlValidarAccesoUsuario($_SESSION["id_usuario"]);
            
            $tieneAcceso = false;
            $institucionId = null;
            
            foreach($accesos as $acceso) {
                // Verificar si es rector en sede principal
                if($acceso["nombre_rol"] == "Rector" && $acceso["tipo_sede"] == "Principal") {
                    $tieneAcceso = true;
                    $institucionId = $acceso["institucion_id"];
                    break;
                }
                
                // Verificar si es usuario representante de la institución en sede principal
                if($acceso["id_usuario_representante"] == $_SESSION["id_usuario"] && $acceso["tipo_sede"] == "Principal") {
                    $tieneAcceso = true;
                    $institucionId = $acceso["institucion_id"];
                    break;
                }
            }
            
            if($tieneAcceso) {
                return array("acceso" => true, "institucion_id" => $institucionId);
            }
        }
        
        return array("acceso" => false);
    }

    static public function ctrObtenerSedes() {
        $validacion = self::ctrValidarAcceso();
        
        if($validacion["acceso"]) {
            return ModeloAsignacionDocenteAsignaturas::mdlObtenerSedesInstitucion($validacion["institucion_id"]);
        }
        
        return array();
    }

    static public function ctrObtenerDocentes($sedeId) {
        $validacion = self::ctrValidarAcceso();
        
        if($validacion["acceso"]) {
            return ModeloAsignacionDocenteAsignaturas::mdlObtenerDocentesPorSede($sedeId);
        }
        
        return array();
    }

    static public function ctrObtenerAsignaturas($sedeId) {
        $validacion = self::ctrValidarAcceso();
        
        if($validacion["acceso"]) {
            return ModeloAsignacionDocenteAsignaturas::mdlObtenerAsignaturasPorSede($sedeId);
        }
        
        return array();
    }

    static public function ctrAsignarAsignaturas() {
        try {
            // Validar que existan los datos requeridos
            if(!isset($_POST["rol_institucional_id"]) || !isset($_POST["asignaturas"])) {
                error_log("Datos faltantes en ctrAsignarAsignaturas: " . json_encode($_POST));
                return "error";
            }
            
            $validacion = self::ctrValidarAcceso();
            
            if(!$validacion["acceso"]) {
                error_log("Acceso denegado en ctrAsignarAsignaturas para usuario: " . ($_SESSION["id_usuario"] ?? 'no definido'));
                return "error";
            }
            
            // Validar y sanitizar datos
            $rolInstitucionalId = filter_var($_POST["rol_institucional_id"], FILTER_VALIDATE_INT);
            if($rolInstitucionalId === false) {
                error_log("rol_institucional_id inválido: " . $_POST["rol_institucional_id"]);
                return "error";
            }
            
            $asignaturas = $_POST["asignaturas"];
            if(!is_array($asignaturas) || empty($asignaturas)) {
                error_log("Asignaturas inválidas: " . json_encode($asignaturas));
                return "error";
            }
            
            $maxHoras = isset($_POST["max_horas"]) ? filter_var($_POST["max_horas"], FILTER_VALIDATE_INT) : 20;
            if($maxHoras === false || $maxHoras < 1 || $maxHoras > 40) {
                $maxHoras = 20;
            }
            
            $observaciones = isset($_POST["observaciones"]) ? trim($_POST["observaciones"]) : "";
        
            // Verificar si ya existe cuerpo docente para este rol
            $cuerpoDocenteId = ModeloAsignacionDocenteAsignaturas::mdlObtenerCuerpoDocentePorRol($rolInstitucionalId);
            
            if(!$cuerpoDocenteId) {
                $cuerpoDocenteId = ModeloAsignacionDocenteAsignaturas::mdlCrearCuerpoDocente($rolInstitucionalId, $maxHoras, $observaciones);
                if(!$cuerpoDocenteId || $cuerpoDocenteId == "error") {
                    error_log("Error creando cuerpo docente para rol: " . $rolInstitucionalId);
                    return "error";
                }
            }
            
            $asignadas = 0;
            $errores = 0;
            
            foreach($asignaturas as $estructuraCurricularId) {
                // Validar que sea un ID válido
                $estructuraId = filter_var($estructuraCurricularId, FILTER_VALIDATE_INT);
                if($estructuraId === false) {
                    error_log("ID de estructura curricular inválido: " . $estructuraCurricularId);
                    $errores++;
                    continue;
                }
                
                $resultado = ModeloAsignacionDocenteAsignaturas::mdlAsignarAsignatura($cuerpoDocenteId, $estructuraId);
                if($resultado == "ok") {
                    $asignadas++;
                } else {
                    error_log("Error asignando asignatura " . $estructuraId . " a cuerpo docente " . $cuerpoDocenteId);
                    $errores++;
                }
            }
            
            if($errores > 0 && $asignadas == 0) {
                return "error";
            }
            
            return ($asignadas > 0) ? "ok" : "sin_cambios";
            
        } catch(Exception $e) {
            error_log("Excepción en ctrAsignarAsignaturas: " . $e->getMessage());
            return "error";
        }
    }

    static public function ctrObtenerAsignaciones($cuerpoDocenteId) {
        $validacion = self::ctrValidarAcceso();
        
        if($validacion["acceso"]) {
            return ModeloAsignacionDocenteAsignaturas::mdlObtenerAsignacionesDocente($cuerpoDocenteId);
        }
        
        return array();
    }

    static public function ctrEliminarAsignacion() {
        if(isset($_POST["asignacion_id"])) {
            $validacion = self::ctrValidarAcceso();
            
            if($validacion["acceso"]) {
                return ModeloAsignacionDocenteAsignaturas::mdlEliminarAsignacion($_POST["asignacion_id"]);
            }
        }
        
        return "error";
    }

    static public function ctrActualizarHorasSemanales() {
        if(!isset($_POST["cuerpo_docente_id"]) || !isset($_POST["horas_semanales"])) {
            return "error";
        }
        
        $validacion = self::ctrValidarAcceso();
        
        if(!$validacion["acceso"]) {
            return "error";
        }
        
        $cuerpoDocenteId = $_POST["cuerpo_docente_id"];
        $horasSemanales = $_POST["horas_semanales"];
        
        return ModeloAsignacionDocenteAsignaturas::mdlActualizarHorasSemanales($cuerpoDocenteId, $horasSemanales);
    }
}