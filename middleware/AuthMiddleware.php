<?php

class AuthMiddleware {
    
    private static $routePermissions = [];
    private static $initialized = false;
    
    /*=============================================
    INICIALIZAR MIDDLEWARE - CARGA AUTOMÁTICA DE PERMISOS
    =============================================*/
    public static function init() {
        if (self::$initialized) return;
        
        self::loadRoutePermissions();
        self::$initialized = true;
    }
    
    /*=============================================
    CARGAR PERMISOS DE RUTAS AUTOMÁTICAMENTE
    =============================================*/
    private static function loadRoutePermissions() {
        try {
            // Cargar desde BD si existe la columna modulo_asociado
            $stmt = Conexion::conectar()->prepare("SHOW COLUMNS FROM acciones LIKE 'modulo_asociado'");
            $stmt->execute();
            $columnExists = $stmt->fetch();
            
            if ($columnExists) {
                $stmt = Conexion::conectar()->prepare("
                    SELECT nombre_accion, modulo_asociado 
                    FROM acciones 
                    WHERE modulo_asociado IS NOT NULL 
                    AND (estado = 'Activo' OR estado IS NULL)
                ");
                $stmt->execute();
                $acciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($acciones as $accion) {
                    $modulo = $accion['modulo_asociado'];
                    $permiso = $accion['nombre_accion'];
                    
                    if (!isset(self::$routePermissions[$modulo])) {
                        self::$routePermissions[$modulo] = [];
                    }
                    self::$routePermissions[$modulo][] = $permiso;
                }
            }
            
            // Mapeo de compatibilidad para sistema existente
            self::loadLegacyMappings();
            
        } catch (Exception $e) {
            error_log("Error cargando permisos de rutas: " . $e->getMessage());
            // Cargar solo mapeo legacy si hay error
            self::loadLegacyMappings();
        }
    }
    
    /*=============================================
    MAPEO DE COMPATIBILIDAD CON SISTEMA EXISTENTE
    =============================================*/
    private static function loadLegacyMappings() {
        // Mapeo exacto del sistema actual en auth.modelo.php
        $legacyMappings = [
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
        
        foreach ($legacyMappings as $ruta => $permisos) {
            if (!isset(self::$routePermissions[$ruta])) {
                self::$routePermissions[$ruta] = $permisos;
            }
        }
    }
    
    /*=============================================
    MIDDLEWARE PRINCIPAL - VERIFICAR ACCESO
    =============================================*/
    public static function checkAccess($route, $action = 'ver') {
        self::init();
        
        if (!isset($_SESSION["id_usuario"])) {
            self::redirectToLogin();
            return false;
        }
        
        // Administradores de sistema tienen acceso total
        if (ControladorAuth::ctrEsAdministradorSistema()) {
            return true;
        }
        
        // Verificar permisos específicos de la ruta
        if (isset(self::$routePermissions[$route])) {
            $requiredPermission = $route . '_' . $action;
            
            // Buscar el permiso exacto o cualquier permiso del módulo
            foreach (self::$routePermissions[$route] as $permission) {
                if ($permission === $requiredPermission || 
                    ControladorAuth::ctrVerificarPermiso($permission)) {
                    return true;
                }
            }
            
            self::accessDenied();
            return false;
        }
        
        // FALLBACK: Denegar acceso si no hay permisos definidos
        self::accessDenied();
        return false;
    }
    
    /*=============================================
    DECORADOR PARA PROTEGER FUNCIONES
    =============================================*/
    public static function requirePermission($permission, $callback = null) {
        if (!isset($_SESSION["id_usuario"])) {
            self::redirectToLogin();
            return false;
        }
        
        if (!ControladorAuth::ctrVerificarPermiso($permission) && 
            !ControladorAuth::ctrEsAdministradorSistema()) {
            
            if ($callback && is_callable($callback)) {
                return $callback();
            }
            
            self::accessDenied();
            return false;
        }
        
        return true;
    }
    
    /*=============================================
    VERIFICAR MÚLTIPLES PERMISOS (OR)
    =============================================*/
    public static function requireAnyPermission($permissions) {
        if (!isset($_SESSION["id_usuario"])) {
            self::redirectToLogin();
            return false;
        }
        
        if (ControladorAuth::ctrEsAdministradorSistema()) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            if (ControladorAuth::ctrVerificarPermiso($permission)) {
                return true;
            }
        }
        
        self::accessDenied();
        return false;
    }
    
    /*=============================================
    VERIFICAR MÚLTIPLES PERMISOS (AND)
    =============================================*/
    public static function requireAllPermissions($permissions) {
        if (!isset($_SESSION["id_usuario"])) {
            self::redirectToLogin();
            return false;
        }
        
        if (ControladorAuth::ctrEsAdministradorSistema()) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            if (!ControladorAuth::ctrVerificarPermiso($permission)) {
                self::accessDenied();
                return false;
            }
        }
        
        return true;
    }
    
    /*=============================================
    HELPER PARA VISTAS - MOSTRAR/OCULTAR ELEMENTOS
    =============================================*/
    public static function canAccess($permission) {
        if (!isset($_SESSION["id_usuario"])) {
            return false;
        }
        
        return ControladorAuth::ctrVerificarPermiso($permission) || 
               ControladorAuth::ctrEsAdministradorSistema();
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
    REGISTRAR NUEVA RUTA DINÁMICAMENTE
    =============================================*/
    public static function registerRoute($route, $permissions) {
        self::$routePermissions[$route] = is_array($permissions) ? $permissions : [$permissions];
    }
    
    /*=============================================
    OBTENER PERMISOS DE UNA RUTA
    =============================================*/
    public static function getRoutePermissions($route) {
        self::init();
        return isset(self::$routePermissions[$route]) ? self::$routePermissions[$route] : [];
    }
    
    /*=============================================
    REDIRECCIONAR A LOGIN
    =============================================*/
    private static function redirectToLogin() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Respuesta AJAX
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado', 'redirect' => 'login']);
        } else {
            echo '<script>window.location = "login";</script>';
        }
        exit();
    }
    
    /*=============================================
    ACCESO DENEGADO
    =============================================*/
    private static function accessDenied() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Respuesta AJAX
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
        } else {
            echo '<script>
                swal({
                    type: "error",
                    title: "Acceso Denegado",
                    text: "No tienes permisos para realizar esta acción",
                    showConfirmButton: true,
                    confirmButtonText: "Entendido"
                }).then(function(result){
                    if (result.value) {
                        window.location = "inicio";
                    }
                })
            </script>';
        }
        exit();
    }
}