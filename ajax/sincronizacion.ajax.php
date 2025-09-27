<?php

require_once "../controladores/sincronizacion.controlador.php";
require_once "../modelos/sincronizacion.modelo.php";
require_once "../controladores/auth.controlador.php";
require_once "../modelos/auth.modelo.php";
require_once "../modelos/conexion.php";

session_start();

// Verificar que el usuario esté logueado y sea administrador
if(!isset($_SESSION["iniciarSesion"]) || $_SESSION["iniciarSesion"] != "ok"){
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if(!ControladorAuth::ctrEsAdministradorSistema()){
    http_response_code(403);
    echo json_encode(['error' => 'Sin permisos de administrador']);
    exit;
}

if(isset($_POST['accion'])){
    
    switch($_POST['accion']){
        
        case 'sincronizar':
            $resultado = ControladorSincronizacion::ctrSincronizarAccionesModulos();
            
            if($resultado == "ok"){
                echo json_encode([
                    'estado' => 'ok',
                    'mensaje' => 'Sistema sincronizado correctamente'
                ]);
            } else {
                echo json_encode([
                    'estado' => 'error',
                    'mensaje' => 'Error durante la sincronización'
                ]);
            }
            break;
            
        case 'verificar':
            $integridad = ControladorSincronizacion::ctrVerificarIntegridad();
            echo json_encode($integridad);
            break;
            
        case 'reparar':
            $resultado = ControladorSincronizacion::ctrRepararPermisos();
            echo json_encode($resultado);
            break;
            
        case 'estadisticas':
            $stats = ModeloSincronizacion::mdlObtenerEstadisticas();
            echo json_encode([
                'estado' => 'ok',
                'estadisticas' => $stats
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No se especificó acción']);
}

?>