<?php

require_once "conexion.php";

class ModeloAuth{

    /*=============================================
    NUEVO MÉTODO CENTRAL v2 - MÁS ROBUSTO Y PRECISO PARA ROL ACTIVO
    =============================================*/
    static public function mdlObtenerPermisosDelRolActivo($usuarioId){
        // Obtenemos el rol activo de la sesión actual.
        $rolActivo = $_SESSION['rol_activo'] ?? null;

        $resultado = [
            'permisos' => [],
            'esRolAdmin' => false
        ];

        if(!$rolActivo){
            return $resultado;
        }

        $partes = explode('_', $rolActivo);
        $tipoRol = $partes[0];

        // --- SI EL ROL ACTIVO ES DE TIPO "SISTEMA" ---
        if($tipoRol == 'sistema'){

            // Primero, necesitamos saber el ID del rol de sistema que está activo.
            // Asumimos que la lógica de cambio de rol también guarda el ID del rol en la sesión.
            // Si no es así, esta consulta lo obtiene.
            $stmt = Conexion::conectar()->prepare(
                "SELECT r.id_rol, r.nombre_rol FROM administradores_sistema ads
                 INNER JOIN roles r ON ads.rol_id = r.id_rol
                 WHERE ads.usuario_id = :usuario_id AND ads.estado = 'Activo'
                 LIMIT 1"
            );
            $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            $rolSistema = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rolSistema) {
                // Verificamos si el rol activo es de tipo Superadministrador/Administrador
                if (in_array($rolSistema['nombre_rol'], ['Superadministrador', 'Administrador'])) {
                    $resultado['esRolAdmin'] = true;
                    // Si es admin, no necesitamos cargar permisos explícitos.
                    return $resultado;
                }

                // Si es otro tipo de rol de sistema, cargamos sus permisos específicos
                $stmt = Conexion::conectar()->prepare(
                    "SELECT a.nombre_accion FROM roles_acciones ra
                     INNER JOIN acciones a ON ra.accion_id = a.id
                     WHERE ra.rol_id = :rol_id"
                );
                $stmt->bindParam(":rol_id", $rolSistema['id_rol'], PDO::PARAM_INT);
                $stmt->execute();
                $resultado['permisos'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
        }
        // --- SI EL ROL ACTIVO ES DE TIPO "INSTITUCIONAL" ---
        else if ($tipoRol == 'institucional') {
            $sedeId = $partes[2] ?? null;
            if($sedeId) {
                // Carga los permisos del rol institucional para la sede específica que está activa
                $stmt = Conexion::conectar()->prepare(
                    "SELECT DISTINCT a.nombre_accion
                     FROM roles_institucionales ri
                     INNER JOIN roles_acciones ra ON ri.rol_id = ra.rol_id
                     INNER JOIN acciones a ON ra.accion_id = a.id
                     WHERE ri.usuario_id = :usuario_id AND ri.sede_id = :sede_id AND ri.estado = 'Activo'"
                );
                $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
                $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
                $stmt->execute();
                $resultado['permisos'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
        }

        return $resultado;
    }

    /*=============================================
    MÉTODOS ANTERIORES - MANTENIDOS POR COMPATIBILIDAD
    =============================================*/

    /*=============================================
    OBTENER ROLES ACTIVOS DEL USUARIO
    =============================================*/
    static public function mdlObtenerRolesUsuario($usuarioId){
        $roles = array();

        // Roles institucionales
        $stmt = Conexion::conectar()->prepare("\n            SELECT r.nombre_rol, s.nombre_sede, s.id as sede_id, 'institucional' as tipo, r.id_rol\n            FROM roles_institucionales ri\n            INNER JOIN roles r ON ri.rol_id = r.id_rol\n            INNER JOIN sede s ON ri.sede_id = s.id\n            WHERE ri.usuario_id = :usuario_id AND ri.estado = 'Activo'\n        ");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $rolesInstitucionales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Roles de sistema
        $stmt = Conexion::conectar()->prepare("\n            SELECT r.nombre_rol, 'Sistema' as nombre_sede, null as sede_id, 'sistema' as tipo, r.id_rol\n            FROM administradores_sistema ads\n            INNER JOIN roles r ON ads.rol_id = r.id_rol\n            WHERE ads.usuario_id = :usuario_id AND ads.estado = 'Activo'\n        ");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $rolesSistema = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_merge($rolesInstitucionales, $rolesSistema);
    }

    // ... (El resto de los métodos como mdlVerificarAccesoModulo, etc., pueden permanecer igual)

    /*=============================================
    VERIFICAR SI USUARIO ES ADMINISTRADOR SISTEMA (EN GENERAL)
    =============================================*/
    static public function mdlEsAdministradorSistema($usuarioId){
        if ($usuarioId === null) return false;
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM administradores_sistema ads INNER JOIN roles r ON ads.rol_id = r.id_rol WHERE ads.usuario_id = :usuario_id AND ads.estado = 'Activo' AND r.nombre_rol IN ('Superadministrador', 'Administrador')");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado['total'] > 0;
    }
}