<?php

require_once "conexion.php";

class ModeloAuth{

    /*=============================================
    OBTENER PERMISOS CONSOLIDADOS DE UN USUARIO - MEJORADO
    =============================================*/
    static public function mdlObtenerPermisosUsuario($usuarioId, $sedeId = null){
        try {
            // Verificar si hay un rol activo seleccionado
            $rolActivo = isset($_SESSION['rol_activo']) ? $_SESSION['rol_activo'] : null;
            
            if($rolActivo){
                $partes = explode('_', $rolActivo);
                $tipoRol = $partes[0];
                
                if($tipoRol == 'sistema'){
                    // Solo permisos de administrador sistema
                    $stmt = Conexion::conectar()->prepare("
                        SELECT DISTINCT a.nombre_accion
                        FROM administradores_sistema ads
                        INNER JOIN roles_acciones ra ON ads.rol_id = ra.rol_id
                        INNER JOIN acciones a ON ra.accion_id = a.id
                        WHERE ads.usuario_id = :usuario_id 
                        AND ads.estado = 'Activo'
                        AND (a.estado = 'Activo' OR a.estado IS NULL)
                    ");
                    $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
                    $stmt->execute();
                    return $stmt->fetchAll(PDO::FETCH_COLUMN);
                } else {
                    // Solo permisos del rol institucional activo
                    $sedeRol = isset($partes[2]) ? $partes[2] : null;
                    if($sedeRol) {
                        $stmt = Conexion::conectar()->prepare("
                            SELECT DISTINCT a.nombre_accion
                            FROM roles_institucionales ri
                            INNER JOIN roles_acciones ra ON ri.rol_id = ra.rol_id
                            INNER JOIN acciones a ON ra.accion_id = a.id
                            WHERE ri.usuario_id = :usuario_id 
                            AND ri.estado = 'Activo'
                            AND ri.sede_id = :sede_id
                            AND (a.estado = 'Activo' OR a.estado IS NULL)
                        ");
                        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
                        $stmt->bindParam(":sede_id", $sedeRol, PDO::PARAM_INT);
                        $stmt->execute();
                        $permisosRoles = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        // Permisos especiales para esa sede
                        $stmt = Conexion::conectar()->prepare("
                            SELECT DISTINCT a.nombre_accion
                            FROM permisos_especiales pe
                            INNER JOIN acciones a ON pe.accion_id = a.id
                            INNER JOIN sede_jornada sj ON pe.sede_jornada_id = sj.id
                            WHERE pe.usuario_id = :usuario_id
                            AND sj.sede_id = :sede_id
                            AND (a.estado = 'Activo' OR a.estado IS NULL)
                        ");
                        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
                        $stmt->bindParam(":sede_id", $sedeRol, PDO::PARAM_INT);
                        $stmt->execute();
                        $permisosEspeciales = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        
                        return array_unique(array_merge($permisosRoles, $permisosEspeciales));
                    }
                }
            }
            
            // Si no hay rol activo, retornar array vacío por seguridad
            return array();
            
        } catch(Exception $e) {
            error_log("Error obteniendo permisos para usuario $usuarioId: " . $e->getMessage());
            return array();
        }
    }

    /*=============================================
    VERIFICAR SI USUARIO TIENE PERMISO ESPECÍFICO
    =============================================*/
    static public function mdlVerificarPermiso($usuarioId, $accion, $sedeId = null){
        $permisos = self::mdlObtenerPermisosUsuario($usuarioId, $sedeId);
        return in_array($accion, $permisos);
    }

    /*=============================================
    VERIFICAR SI USUARIO ES ADMINISTRADOR SISTEMA
    =============================================*/
    static public function mdlEsAdministradorSistema($usuarioId){
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) as total
            FROM administradores_sistema ads
            INNER JOIN roles r ON ads.rol_id = r.id_rol
            WHERE ads.usuario_id = :usuario_id 
            AND ads.estado = 'Activo'
            AND r.nombre_rol IN ('Superadministrador', 'Administrador')
        ");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado['total'] > 0;
    }

    /*=============================================
    OBTENER ROLES ACTIVOS DEL USUARIO
    =============================================*/
    static public function mdlObtenerRolesUsuario($usuarioId){
        $roles = array();
        
        // Roles institucionales
        $stmt = Conexion::conectar()->prepare("
            SELECT r.nombre_rol, s.nombre_sede, s.id as sede_id, 'institucional' as tipo
            FROM roles_institucionales ri
            INNER JOIN roles r ON ri.rol_id = r.id_rol
            INNER JOIN sede s ON ri.sede_id = s.id
            WHERE ri.usuario_id = :usuario_id AND ri.estado = 'Activo'
        ");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $rolesInstitucionales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Roles de sistema
        $stmt = Conexion::conectar()->prepare("
            SELECT r.nombre_rol, 'Sistema' as nombre_sede, null as sede_id, 'sistema' as tipo
            FROM administradores_sistema ads
            INNER JOIN roles r ON ads.rol_id = r.id_rol
            WHERE ads.usuario_id = :usuario_id AND ads.estado = 'Activo'
        ");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $rolesSistema = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_merge($rolesInstitucionales, $rolesSistema);
    }

    /*=============================================
    VERIFICAR ACCESO A MÓDULO - SISTEMA ESCALABLE
    =============================================*/
    static public function mdlVerificarAccesoModulo($usuarioId, $modulo){
        // Mapeo directo para módulos existentes
        $accionesModulo = array(
            'usuarios' => 'usuarios_ver',
            'roles' => 'roles_ver', 
            'institucion' => 'institucion_ver',
            'sedes' => 'sedes_ver',
            'grados' => 'grados_ver',
            'cursos' => 'cursos_ver',
            'matricula' => 'matricula_ver',
            'estructura-curricular' => 'estructura-curricular_ver',
            'gestionar-acciones' => 'permisos_ver',
            'gestionar-permisos' => 'permisos_asignar',
            'asignar-roles' => 'roles_ver',
            'niveleducativo' => 'niveles_ver',
            'jornadas' => 'jornadas_ver',
            'oferta' => 'oferta_ver',
            'periodos' => 'periodos_ver'
        );
        
        if(isset($accionesModulo[$modulo])){
            return self::mdlVerificarPermiso($usuarioId, $accionesModulo[$modulo]);
        }
        
        return false;
    }

    /*=============================================
    OBTENER ACCIÓN REQUERIDA PARA UN MÓDULO (ESCALABLE)
    =============================================*/
    static public function mdlObtenerAccionModulo($modulo){
        try {
            // Buscar acción específica para el módulo
            $stmt = Conexion::conectar()->prepare("
                SELECT nombre_accion 
                FROM acciones 
                WHERE modulo_asociado = :modulo 
                AND nombre_accion LIKE '%_ver' 
                AND (estado = 'Activo' OR estado IS NULL)
                ORDER BY nombre_accion 
                LIMIT 1
            ");
            $stmt->bindParam(":modulo", $modulo, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch();
            
            if($resultado){
                return $resultado['nombre_accion'];
            }
            
            // Si no encuentra con modulo_asociado, buscar por patrón de nombre
            $patronBusqueda = $modulo . '_ver';
            $stmt = Conexion::conectar()->prepare("
                SELECT nombre_accion 
                FROM acciones 
                WHERE nombre_accion = :patron
                AND (estado = 'Activo' OR estado IS NULL)
                LIMIT 1
            ");
            $stmt->bindParam(":patron", $patronBusqueda, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch();
            
            return $resultado ? $resultado['nombre_accion'] : null;
            
        } catch(Exception $e) {
            error_log("Error obteniendo acción para módulo $modulo: " . $e->getMessage());
            return null;
        }
    }
}