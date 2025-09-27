<?php

require_once "conexion.php";

class ModeloSincronizacion {
    
    /*=============================================
    SINCRONIZAR ACCIONES CON MÓDULOS - MEJORADO
    =============================================*/
    static public function mdlSincronizarAccionesModulos($accionesPlanas) {
        $pdo = Conexion::conectar();
        $pdo->beginTransaction();
        
        try {
            // Verificar si existe la columna modulo_asociado
            $stmt = $pdo->prepare("SHOW COLUMNS FROM acciones LIKE 'modulo_asociado'");
            $stmt->execute();
            $columnExists = $stmt->fetch();
            
            // Si no existe la columna, crearla
            if (!$columnExists) {
                $stmt = $pdo->prepare("ALTER TABLE acciones ADD COLUMN modulo_asociado VARCHAR(100) NULL AFTER modulo");
                $stmt->execute();
            }
            
            // Obtener acciones existentes
            $stmt = $pdo->prepare("SELECT nombre_accion, modulo, modulo_asociado FROM acciones");
            $stmt->execute();
            $accionesExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Crear un índice por nombre de acción
            $indiceExistentes = array();
            foreach ($accionesExistentes as $accion) {
                $indiceExistentes[$accion['nombre_accion']] = $accion;
            }
            
            $accionesCreadas = 0;
            $accionesActualizadas = 0;
            
            foreach ($accionesPlanas as $accionData) {
                $nombreAccion = $accionData['nombre_accion'];
                $descripcion = $accionData['descripcion'];
                $modulo = $accionData['modulo'];
                $moduloAsociado = $accionData['modulo_asociado'];
                
                if (!isset($indiceExistentes[$nombreAccion])) {
                    // Crear nueva acción
                    $stmt = $pdo->prepare("
                        INSERT INTO acciones (nombre_accion, descripcion, modulo, modulo_asociado, estado) 
                        VALUES (:nombre_accion, :descripcion, :modulo, :modulo_asociado, 'Activo')
                    ");
                    $stmt->bindParam(":nombre_accion", $nombreAccion, PDO::PARAM_STR);
                    $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
                    $stmt->bindParam(":modulo", $modulo, PDO::PARAM_STR);
                    $stmt->bindParam(":modulo_asociado", $moduloAsociado, PDO::PARAM_STR);
                    $stmt->execute();
                    $accionesCreadas++;
                } else {
                    // Verificar si necesita actualización
                    $accionExistente = $indiceExistentes[$nombreAccion];
                    $necesitaActualizacion = false;
                    
                    if (empty($accionExistente['modulo_asociado']) || $accionExistente['modulo_asociado'] != $moduloAsociado) {
                        $necesitaActualizacion = true;
                    }
                    
                    if ($necesitaActualizacion) {
                        $stmt = $pdo->prepare("
                            UPDATE acciones 
                            SET modulo = :modulo, modulo_asociado = :modulo_asociado, descripcion = :descripcion
                            WHERE nombre_accion = :nombre_accion
                        ");
                        $stmt->bindParam(":modulo", $modulo, PDO::PARAM_STR);
                        $stmt->bindParam(":modulo_asociado", $moduloAsociado, PDO::PARAM_STR);
                        $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
                        $stmt->bindParam(":nombre_accion", $nombreAccion, PDO::PARAM_STR);
                        $stmt->execute();
                        $accionesActualizadas++;
                    }
                }
            }
            
            $pdo->commit();
            
            // Log del resultado
            error_log("Sincronización completada: $accionesCreadas acciones creadas, $accionesActualizadas actualizadas");
            
            return "ok";
            
        } catch (Exception $e) {
            $pdo->rollback();
            error_log("Error en sincronización: " . $e->getMessage());
            return "error";
        }
    }
    
    /*=============================================
    AGREGAR NUEVO MÓDULO
    =============================================*/
    static public function mdlAgregarModulo($nombreModulo, $acciones) {
        $pdo = Conexion::conectar();
        $pdo->beginTransaction();
        
        try {
            foreach ($acciones as $nombreAccion => $descripcion) {
                // Verificar si ya existe
                $stmt = $pdo->prepare("SELECT id FROM acciones WHERE nombre_accion = :nombre_accion");
                $stmt->bindParam(":nombre_accion", $nombreAccion, PDO::PARAM_STR);
                $stmt->execute();
                
                if (!$stmt->fetch()) {
                    // Crear nueva acción
                    $stmt = $pdo->prepare("
                        INSERT INTO acciones (nombre_accion, descripcion, modulo, modulo_asociado, estado) 
                        VALUES (:nombre_accion, :descripcion, :modulo, :modulo_asociado, 'Activo')
                    ");
                    $stmt->bindParam(":nombre_accion", $nombreAccion, PDO::PARAM_STR);
                    $stmt->bindParam(":descripcion", $descripcion, PDO::PARAM_STR);
                    $stmt->bindParam(":modulo", $nombreModulo, PDO::PARAM_STR);
                    $stmt->bindParam(":modulo_asociado", $nombreModulo, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
            
            $pdo->commit();
            return "ok";
            
        } catch (Exception $e) {
            $pdo->rollback();
            error_log("Error agregando módulo $nombreModulo: " . $e->getMessage());
            return "error";
        }
    }
    
    /*=============================================
    VERIFICAR INTEGRIDAD DEL SISTEMA
    =============================================*/
    static public function mdlVerificarIntegridad() {
        try {
            $problemas = array();
            
            // 1. Verificar acciones sin módulo asociado
            $stmt = Conexion::conectar()->prepare("
                SELECT COUNT(*) as total 
                FROM acciones 
                WHERE (modulo_asociado IS NULL OR modulo_asociado = '') 
                AND estado = 'Activo'
            ");
            $stmt->execute();
            $sinModulo = $stmt->fetch()['total'];
            
            if ($sinModulo > 0) {
                $problemas[] = "$sinModulo acciones sin módulo asociado";
            }
            
            // 2. Verificar roles sin permisos
            $stmt = Conexion::conectar()->prepare("
                SELECT r.nombre_rol, COUNT(ra.accion_id) as total_permisos
                FROM roles r
                LEFT JOIN roles_acciones ra ON r.id_rol = ra.rol_id
                GROUP BY r.id_rol, r.nombre_rol
                HAVING total_permisos = 0
            ");
            $stmt->execute();
            $rolesSinPermisos = $stmt->fetchAll();
            
            if (count($rolesSinPermisos) > 0) {
                $nombres = array_column($rolesSinPermisos, 'nombre_rol');
                $problemas[] = "Roles sin permisos: " . implode(', ', $nombres);
            }
            
            // 3. Verificar usuarios sin roles activos
            $stmt = Conexion::conectar()->prepare("
                SELECT u.nombres_usuario, u.apellidos_usuario
                FROM usuarios u
                LEFT JOIN roles_institucionales ri ON u.id_usuario = ri.usuario_id AND ri.estado = 'Activo'
                LEFT JOIN administradores_sistema ads ON u.id_usuario = ads.usuario_id AND ads.estado = 'Activo'
                WHERE u.estado_usuario = 'Activo'
                AND ri.id IS NULL AND ads.id IS NULL
            ");
            $stmt->execute();
            $usuariosSinRoles = $stmt->fetchAll();
            
            if (count($usuariosSinRoles) > 0) {
                $problemas[] = count($usuariosSinRoles) . " usuarios activos sin roles";
            }
            
            return array(
                'estado' => empty($problemas) ? 'ok' : 'problemas_encontrados',
                'problemas' => $problemas,
                'total_problemas' => count($problemas)
            );
            
        } catch (Exception $e) {
            error_log("Error verificando integridad: " . $e->getMessage());
            return array(
                'estado' => 'error',
                'mensaje' => $e->getMessage()
            );
        }
    }
    
    /*=============================================
    OBTENER ESTADÍSTICAS DEL SISTEMA
    =============================================*/
    static public function mdlObtenerEstadisticas() {
        try {
            $stats = array();
            
            // Total de acciones
            $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM acciones WHERE estado = 'Activo'");
            $stmt->execute();
            $stats['total_acciones'] = $stmt->fetch()['total'];
            
            // Total de roles
            $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM roles");
            $stmt->execute();
            $stats['total_roles'] = $stmt->fetch()['total'];
            
            // Total de usuarios activos
            $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM usuarios WHERE estado_usuario = 'Activo'");
            $stmt->execute();
            $stats['total_usuarios'] = $stmt->fetch()['total'];
            
            // Módulos únicos
            $stmt = Conexion::conectar()->prepare("SELECT COUNT(DISTINCT modulo_asociado) as total FROM acciones WHERE modulo_asociado IS NOT NULL");
            $stmt->execute();
            $stats['total_modulos'] = $stmt->fetch()['total'];
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            return array();
        }
    }
}