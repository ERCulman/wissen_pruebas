<?php

class GlobalProtector {
    
    /*=============================================
    PROTEGER TODOS LOS AJAX AUTOMÁTICAMENTE
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
        
        // Obtener nombre del archivo actual
        $currentFile = basename($_SERVER['SCRIPT_NAME']);
        
        // Verificar permiso si está mapeado
        if (isset($ajaxPermissions[$currentFile])) {
            $requiredPermission = $ajaxPermissions[$currentFile];
            
            if (!ControladorAuth::ctrVerificarPermiso($requiredPermission) && 
                !ControladorAuth::ctrEsAdministradorSistema()) {
                http_response_code(403);
                echo json_encode(['error' => 'Sin permisos para ' . $currentFile]);
                exit();
            }
        }
    }
    
    /*=============================================
    OBTENER MAPEO DINÁMICO DE PERMISOS AJAX
    =============================================*/
    private static function getAjaxPermissions() {
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
    
    /*=============================================
    PROTEGER CONTROLADORES AUTOMÁTICAMENTE
    =============================================*/
    public static function protectControllerMethod($action) {
        if (!isset($_SESSION["id_usuario"])) {
            return false;
        }
        
        if (!ControladorAuth::ctrVerificarPermiso($action) && 
            !ControladorAuth::ctrEsAdministradorSistema()) {
            return false;
        }
        
        return true;
    }
}