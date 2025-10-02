<?php

// Asegúrate de que el servicio de autorización esté disponible
require_once __DIR__ . '/../servicios/ServicioAutorizacion.php';

class AuthMiddleware {

    // Las propiedades y métodos para cargar rutas se mantienen por ahora
    // para no romper la compatibilidad con el sistema actual.
    private static $routePermissions = [];
    private static $initialized = false;

    public static function init() {
        if (self::$initialized) return;
        self::loadLegacyMappings(); // Simplificado por ahora
        self::$initialized = true;
    }

    private static function loadLegacyMappings() {
        self::$routePermissions = [
            'usuarios' => ['usuarios_ver'],
            'roles' => ['roles_ver'],
            'institucion' => ['institucion_ver'],
            'sedes' => ['sedes_ver'],
            'grados' => ['grados_ver'],
            'cursos' => ['cursos_ver'],
            'matricula' => ['matricula_ver'],
            'gestionar-acciones' => ['permisos_ver'],
            'gestionar-permisos' => ['permisos_asignar'],
            'asignar-roles' => ['roles_ver']
        ];
    }

    /*=============================================
    MIDDLEWARE PRINCIPAL - VERIFICAR ACCESO (AHORA CENTRALIZADO)
    =============================================*/
    public static function checkAccess($route, $action = 'ver') {
        self::init();

        if (!isset($_SESSION["id_usuario"])) {
            self::redirectToLogin();
            return false;
        }

        $auth = ServicioAutorizacion::getInstance();
        $requiredPermission = $route . '_' . $action;

        // La lógica ahora es simple: preguntar al servicio si el usuario puede.
        if (isset(self::$routePermissions[$route]) && $auth->noPuede($requiredPermission)) {
            self::accessDenied();
            return false;
        }

        return true;
    }

    /*=============================================
    DECORADOR PARA PROTEGER FUNCIONES (AHORA CENTRALIZADO)
    =============================================*/
    public static function requirePermission($permission, $callback = null) {
        if (!isset($_SESSION["id_usuario"])) {
            self::redirectToLogin();
            return false;
        }

        $auth = ServicioAutorizacion::getInstance();

        if ($auth->noPuede($permission)) {
            if ($callback && is_callable($callback)) {
                return $callback();
            }
            self::accessDenied();
            return false;
        }

        return true;
    }

    /*=============================================
    VERIFICAR MÚLTIPLES PERMISOS (OR) (AHORA CENTRALIZADO)
    =============================================*/
    public static function requireAnyPermission($permissions) {
        if (!isset($_SESSION["id_usuario"])) {
            self::redirectToLogin();
            return false;
        }

        $auth = ServicioAutorizacion::getInstance();

        foreach ($permissions as $permission) {
            if ($auth->puede($permission)) {
                return true; // Si puede al menos uno, retorna true.
            }
        }

        self::accessDenied(); // Si no pudo con ninguno, deniega el acceso.
        return false;
    }

    /*=============================================
    HELPER PARA VISTAS (AHORA CENTRALIZADO)
    =============================================*/
    public static function canAccess($permission) {
        if (!isset($_SESSION["id_usuario"])) {
            return false;
        }

        $auth = ServicioAutorizacion::getInstance();
        return $auth->puede($permission);
    }

    /*=============================================
    HELPER PARA BOTONES/ENLACES CONDICIONALES
    =============================================*/
    public static function renderIfAllowed($permission, $content, $alternative = '') {
        if (self::canAccess($permission)) {
            return $content;
        }
        return $alternative;
    }

    /*=============================================
    REDIRECCIONAR A LOGIN (Sin cambios)
    =============================================*/
    private static function redirectToLogin() {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado', 'redirect' => 'login']);
        } else {
            echo '<script>window.location = "login";</script>';
        }
        exit();
    }

    /*=============================================
    ACCESO DENEGADO (Sin cambios)
    =============================================*/
    public static function accessDenied() {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
        } else {
            echo '<script>window.location = "acceso-denegado";</script>';
        }
        exit();
    }
}