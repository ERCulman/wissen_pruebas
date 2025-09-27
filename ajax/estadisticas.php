<?php

require_once "global-protection.php";

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir una constante para la raíz del proyecto para evitar problemas con rutas relativas
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

require_once PROJECT_ROOT . "/controladores/institucion.controlador.php";
require_once PROJECT_ROOT . "/controladores/sedes.controlador.php";
require_once PROJECT_ROOT . "/controladores/usuarios.controlador.php";
require_once PROJECT_ROOT . "/modelos/institucion.modelo.php";
require_once PROJECT_ROOT . "/modelos/sedes.modelo.php";
require_once PROJECT_ROOT . "/modelos/usuarios.modelo.php";
require_once PROJECT_ROOT . "/modelos/conexion.php";

class ApiEstadisticas {

    public function obtenerEstadisticas() {
        try {
            $totalInstituciones = ControladorInstitucion::ctrContarInstituciones();
            $totalSedes = ControladorSede::ctrContarSedes();
            $totalDocentes = ControladorUsuarios::ctrContarUsuariosPorRol("Docente");
            $totalEstudiantes = ControladorUsuarios::ctrContarUsuariosPorRol("Estudiante");
            $totalAcudientes = ControladorUsuarios::ctrContarUsuariosPorRol("Acudiente");

            $datos = array(
                "instituciones" => $totalInstituciones,
                "sedes" => $totalSedes,
                "docentes" => $totalDocentes,
                "estudiantes" => $totalEstudiantes,
                "acudientes" => $totalAcudientes,
                "status" => "ok"
            );

            header('Content-Type: application/json');
            echo json_encode($datos);

        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(array(
                "error" => "Ocurrió un error en el servidor.",
                "message" => $e->getMessage()
            ));
        }
    }
}

$api = new ApiEstadisticas();
$api->obtenerEstadisticas();
