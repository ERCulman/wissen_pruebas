<?php

class RouteProtector {
    
    /*=============================================
    PROTEGER RUTA CON DECORADOR
    =============================================*/
    public static function protect($route, $action = 'ver') {
        return AuthMiddleware::checkAccess($route, $action);
    }
    
    /*=============================================
    DECORADOR PARA MÉTODOS DE CONTROLADOR
    =============================================*/
    public static function method($permission) {
        return function($callback) use ($permission) {
            if (AuthMiddleware::requirePermission($permission)) {
                return $callback();
            }
            return false;
        };
    }
    
    /*=============================================
    DECORADOR PARA AJAX
    =============================================*/
    public static function ajax($permission, $callback) {
        if (!AuthMiddleware::requirePermission($permission)) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permisos']);
            exit();
        }
        return $callback();
    }
    
    /*=============================================
    PROTECCIÓN AUTOMÁTICA BASADA EN URL
    =============================================*/
    public static function autoProtect() {
        if (!isset($_GET['ruta'])) return true;
        
        $ruta = $_GET['ruta'];
        $accion = isset($_GET['accion']) ? $_GET['accion'] : 'ver';
        
        return AuthMiddleware::checkAccess($ruta, $accion);
    }
}