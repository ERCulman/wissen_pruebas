<?php

// Asegúrate de incluir el nuevo servicio al principio del archivo
require_once __DIR__ . '/../servicios/ServicioAutorizacion.php';

class BackendProtector {

    /*=============================================
    PROTEGER CONTROLADORES AUTOMÁTICAMENTE (AHORA CENTRALIZADO)
    =============================================*/
    public static function protectController($action) {
        // La lógica ahora es mucho más simple y delega al servicio
        $auth = ServicioAutorizacion::getInstance();

        if ($auth->noPuede($action)) {
            // La lógica de respuesta (forbidden) se mantiene aquí por ahora
            self::forbidden();
            return false;
        }

        return true;
    }

    /*=============================================
    PROTEGER AJAX AUTOMÁTICAMENTE (AHORA CENTRALIZADO)
    =============================================*/
    public static function protectAjax($action) {
        // Verificación de sesión primero
        if (!isset($_SESSION["id_usuario"])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }

        // De nuevo, delegamos la lógica de permisos
        $auth = ServicioAutorizacion::getInstance();

        if ($auth->noPuede($action)) {
            http_response_code(403);
            echo json_encode(['error' => 'Sin permisos']);
            exit();
        }

        return true;
    }

    /*=============================================
    PROTEGER AJAX CON EXCEPCIÓN PÚBLICA (AHORA CENTRALIZADO)
    =============================================*/
    public static function protectAjaxWithPublicException($action, $allowPublic = false) {
        if ($allowPublic && !isset($_SESSION["id_usuario"])) {
            return true;
        }

        // Si no es público o hay sesión, usa la protección normal que ya está centralizada.
        return self::protectAjax($action);
    }

    /*=============================================
    RESPUESTAS DE ERROR (Sin cambios)
    =============================================*/
    private static function forbidden() {
        http_response_code(403);
        if (self::isAjax()) {
            echo json_encode(['error' => 'Sin permisos']);
        } else {
            // Idealmente, esto debería redirigir a una página de error genérica.
            echo '<script>window.location = "acceso-denegado";</script>';
        }
        exit();
    }

    private static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Deprecado: Ya no necesitamos este método aquí.
     * La lógica de "es admin" ahora vive y debe ser consultada
     * a través de ServicioAutorizacion.
     * Se mantiene por si otro código antiguo lo usa, pero debería ser eliminado.
     */
    private static function esAdministradorSistema() {
        // Lógica antigua... debería ser eliminada a futuro
        return ControladorAuth::ctrEsAdministradorSistema();
    }
}