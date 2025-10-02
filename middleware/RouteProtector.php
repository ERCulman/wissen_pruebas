<?php

// Asegúrate de que el servicio de autorización esté disponible
require_once __DIR__ . '/../servicios/ServicioAutorizacion.php';
// También necesita AuthMiddleware para la respuesta de error, por ahora.
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class RouteProtector {

    /*=============================================
    PROTEGER RUTA CON DECORADOR (AHORA CENTRALIZADO)
    =============================================*/
    public static function protect($route, $action = 'ver') {
        // En lugar de llamar a AuthMiddleware, ahora podemos usarlo directamente.
        // AuthMiddleware ya fue refactorizado para usar el servicio.
        return AuthMiddleware::checkAccess($route, $action);
    }

    /*=============================================
    DECORADOR PARA MÉTODOS DE CONTROLADOR (AHORA CENTRALIZADO)
    =============================================*/
    public static function method($permission) {
        return function($callback) use ($permission) {
            // Obtenemos la instancia de nuestro servicio
            $auth = ServicioAutorizacion::getInstance();

            // Delegamos la decisión al servicio
            if ($auth->puede($permission)) {
                return $callback();
            }

            // Si no tiene permiso, la ejecución se detiene
            // Podemos usar la respuesta estandarizada de AuthMiddleware
            AuthMiddleware::accessDenied();
            return false;
        };
    }

    /*=============================================
    DECORADOR PARA AJAX (AHORA CENTRALIZADO)
    =============================================*/
    public static function ajax($permission, $callback) {
        $auth = ServicioAutorizacion::getInstance();

        if ($auth->noPuede($permission)) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permisos']);
            exit();
        }

        return $callback();
    }

    /*=============================================
    PROTECCIÓN AUTOMÁTICA BASADA EN URL (AHORA CENTRALIZADO)
    =============================================*/
    public static function autoProtect() {
        if (!isset($_GET['ruta'])) return true;

        $ruta = $_GET['ruta'];
        $accion = isset($_GET['accion']) ? $_GET['accion'] : 'ver';

        // Delegamos a la lógica ya refactorizada de AuthMiddleware
        return AuthMiddleware::checkAccess($ruta, $accion);
    }
}