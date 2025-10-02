<?php

// Asegúrate de que el servicio de autorización esté disponible
require_once __DIR__ . '/../servicios/ServicioAutorizacion.php';

class GlobalProtector {

    /*=============================================
    PROTEGER TODOS LOS AJAX AUTOMÁTICAMENTE (AHORA CENTRALIZADO)
    =============================================*/
    public static function protectAllAjax() {
        // Solo ejecutar en archivos AJAX
        if (strpos($_SERVER['REQUEST_URI'], '/ajax/') === false) {
            return;
        }

        // Verificar sesión
        if (!isset($_SESSION["id_usuario"])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }

        // Mapeo dinámico de archivos AJAX a permisos
        $ajaxPermissions = self::getAjaxPermissions();
        $currentFile = basename($_SERVER['SCRIPT_NAME']);

        // Si el archivo actual requiere un permiso específico...
        if (isset($ajaxPermissions[$currentFile])) {
            $requiredPermission = $ajaxPermissions[$currentFile];

            // Obtenemos la instancia de nuestro servicio centralizado
            $auth = ServicioAutorizacion::getInstance();

            // Delegamos la decisión al servicio
            if ($auth->noPuede($requiredPermission)) {
                http_response_code(403);
                echo json_encode(['error' => 'Sin permisos para ' . $currentFile]);
                exit();
            }
        }
    }

    /*=============================================
    OBTENER MAPEO DINÁMICO DE PERMISOS AJAX (Sin cambios)
    =============================================*/
    private static function getAjaxPermissions() {
        // Este mapeo sigue siendo necesario en esta fase de transición.
        // El objetivo final es eliminar la dependencia de nombres de archivo.
        return [
            'usuarios.ajax.php' => 'usuarios_ver',
            'roles.ajax.php' => 'roles_ver',
            'acciones.ajax.php' => 'permisos_ver',
            'obtener-sede.php' => 'sedes_ver',
            'obtener-grado.php' => 'grados_ver',
            'obtener-curso.php' => 'cursos_ver',
            'obtener-matricula.php' => 'matricula_ver',
            'obtener-sede-rol.ajax.php' => 'usuarios_ver',
            'cambiar-rol.ajax.php' => 'usuarios_ver',
            'sincronizacion.ajax.php' => 'sistema_sincronizar',
            'obtener-jornada.php' => 'jornadas_ver',
            'obtener-nivel-educativo.php' => 'niveles_ver',
            'obtener-oferta-educativa.php' => 'oferta_ver',
            'obtener-periodo.php' => 'periodos_ver',
            'obtener-area.php' => 'estructura-curricular_ver',
            'obtener-asignatura.php' => 'estructura-curricular_ver',
            'obtener-curriculo.php' => 'estructura-curricular_ver',
            'obtener-institucion.php' => 'institucion_ver'
        ];
    }

    /**
     * @deprecated Este método es redundante. Usar ServicioAutorizacion.
     */
    public static function protectControllerMethod($action) {
        if (!isset($_SESSION["id_usuario"])) {
            return false;
        }

        $auth = ServicioAutorizacion::getInstance();
        return $auth->puede($action);
    }
}