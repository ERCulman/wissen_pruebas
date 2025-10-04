<?php

class ControladorAsistencia {

    /*=============================================
    OBTENER PERÍODOS
    =============================================*/
    static public function ctrObtenerPeriodos() {
        $respuesta = ModeloAsistencia::mdlObtenerPeriodos();
        return $respuesta;
    }

    /*=============================================
    OBTENER ASIGNACIONES DOCENTE
    =============================================*/
    static public function ctrObtenerAsignacionesDocente() {
        if (isset($_POST["cuerpo_docente_id"]) && isset($_POST["periodo_id"])) {
            $cuerpoDocenteId = $_POST["cuerpo_docente_id"];
            $periodoId = $_POST["periodo_id"];
            
            $respuesta = ModeloAsistencia::mdlObtenerAsignacionesDocente($cuerpoDocenteId, $periodoId);
            echo json_encode($respuesta);
        }
    }

    /*=============================================
    OBTENER ESTUDIANTES DEL GRUPO
    =============================================*/
    static public function ctrObtenerEstudiantesGrupo() {
        if (isset($_POST["grupo_id"])) {
            $grupoId = $_POST["grupo_id"];
            
            try {
                $estudiantes = ModeloAsistencia::mdlObtenerEstudiantesGrupo($grupoId);
                
                if ($estudiantes === false) {
                    echo json_encode(["error" => "Error al consultar la base de datos"]);
                    return;
                }
                
                echo json_encode($estudiantes);
            } catch (Exception $e) {
                error_log("Error en ctrObtenerEstudiantesGrupo: " . $e->getMessage());
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["error" => "Falta el parámetro grupo_id"]);
        }
    }

    /*=============================================
    REGISTRAR ASISTENCIA
    =============================================*/
    static public function ctrRegistrarAsistencia() {
        if (isset($_POST["asignacion_id"]) && isset($_POST["fecha"]) && 
            isset($_POST["hora_inicio"]) && isset($_POST["hora_fin"]) && 
            isset($_POST["asistencias"])) {
            
            $asignacionId = $_POST["asignacion_id"];
            $fecha = $_POST["fecha"];
            $horaInicio = $_POST["hora_inicio"];
            $horaFin = $_POST["hora_fin"];
            $asistencias = json_decode($_POST["asistencias"], true);
            $usuarioId = $_SESSION["id_usuario"];
            
            error_log("Controlador - Datos recibidos:");
            error_log("Asignacion ID: $asignacionId");
            error_log("Fecha: $fecha");
            error_log("Hora inicio: $horaInicio");
            error_log("Hora fin: $horaFin");
            error_log("Usuario ID: $usuarioId");
            error_log("Asistencias: " . print_r($asistencias, true));

            $respuesta = ModeloAsistencia::mdlRegistrarAsistenciaMasiva(
                $asignacionId, $fecha, $horaInicio, $horaFin, $asistencias, $usuarioId
            );
            
            error_log("Respuesta del modelo: $respuesta");

            echo json_encode(array("respuesta" => $respuesta));
        } else {
            error_log("Faltan parámetros en ctrRegistrarAsistencia");
            echo json_encode(array("respuesta" => "error", "mensaje" => "Faltan parámetros"));
        }
    }

    /*=============================================
    OBTENER ASISTENCIA EXISTENTE
    =============================================*/
    static public function ctrObtenerAsistenciaExistente() {
        if (isset($_POST["asignacion_id"]) && isset($_POST["fecha"])) {
            $asignacionId = $_POST["asignacion_id"];
            $fecha = $_POST["fecha"];
            
            $respuesta = ModeloAsistencia::mdlObtenerAsistenciaExistente($asignacionId, $fecha);
            echo json_encode($respuesta);
        }
    }
}