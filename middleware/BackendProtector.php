<?php

class BackendProtector {
    
    /*=============================================
    PROTEGER CONTROLADORES AUTOMÁTICAMENTE
    =============================================*/
    public static function protectController($action) {
        // Verificar sesión
        if (!isset($_SESSION["id_usuario"])) {
            self::unauthorized();
            return false;
        }
        
        // Los administradores de sistema tienen acceso total
        if (ControladorAuth::ctrEsAdministradorSistema()) {
            return true;
        }
        
        // Verificar permiso específico
        if (!ControladorAuth::ctrTienePermiso($action)) {
            self::forbidden();
            return false;
        }
        
        return true;
    }
    
    /*=============================================
    PROTEGER AJAX AUTOMÁTICAMENTE
    =============================================*/
    public static function protectAjax($action) {
        if (!isset($_SESSION["id_usuario"])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        // Los administradores de sistema tienen acceso total
        if (ControladorAuth::ctrEsAdministradorSistema()) {
            return true;
        }
        
        if (!ControladorAuth::ctrTienePermiso($action)) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permisos']);
            exit();
        }
        
        return true;
    }
    
    /*=============================================
    RESPUESTAS DE ERROR
    =============================================*/
    private static function unauthorized() {
        http_response_code(401);
        if (self::isAjax()) {
            echo json_encode(['error' => 'No autorizado']);
        } else {
            echo '<script>window.location = "login";</script>';
        }
        exit();
    }
    
    private static function forbidden() {
        http_response_code(403);
        if (self::isAjax()) {
            echo json_encode(['error' => 'Sin permisos']);
        } else {
            echo '<script>window.location = "acceso-denegado";</script>';
        }
        exit();
    }
    
    private static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}