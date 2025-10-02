<?php

require_once "conexion.php";

class ModeloAuth{

    /*=============================================
MÉTODO CENTRAL PARA OBTENER PERMISOS DEL ROL ACTIVO (CORREGIDO)
=============================================*/
    static public function mdlObtenerPermisosDelRolActivo($usuarioId){
        $rolActivo = $_SESSION['rol_activo'] ?? null;

        $resultado = [
            'permisos' => [],
            'esRolAdmin' => false,
            'nombre_rol' => '' // Inicializamos el nombre del rol
        ];

        if(!$rolActivo){
            return $resultado;
        }

        $partes = explode('_', $rolActivo);
        $tipoRol = $partes[0];

        // --- SI EL ROL ACTIVO ES DE TIPO "SISTEMA" ---
        if($tipoRol == 'sistema'){
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
                if (in_array($rolSistema['nombre_rol'], ['Superadministrador', 'Administrador'])) {
                    $resultado['esRolAdmin'] = true;
                }

                // AÑADIMOS EL NOMBRE DEL ROL AL RESULTADO
                $resultado['nombre_rol'] = $rolSistema['nombre_rol'];

                $stmtPermisos = Conexion::conectar()->prepare(
                    "SELECT a.nombre_accion FROM roles_acciones ra
                 INNER JOIN acciones a ON ra.accion_id = a.id
                 WHERE ra.rol_id = :rol_id"
                );
                $stmtPermisos->bindParam(":rol_id", $rolSistema['id_rol'], PDO::PARAM_INT);
                $stmtPermisos->execute();
                $resultado['permisos'] = $stmtPermisos->fetchAll(PDO::FETCH_COLUMN);
            }
        }
        // --- SI EL ROL ACTIVO ES DE TIPO "INSTITUCIONAL" ---
        else if ($tipoRol == 'institucional') {
            $sedeId = $partes[2] ?? null;
            if($sedeId) {
                // Modificamos la consulta para obtener también el nombre del rol
                $stmt = Conexion::conectar()->prepare(
                    "SELECT DISTINCT a.nombre_accion, r.nombre_rol
                 FROM roles_institucionales ri
                 INNER JOIN roles r ON ri.rol_id = r.id_rol
                 INNER JOIN roles_acciones ra ON ri.rol_id = ra.rol_id
                 INNER JOIN acciones a ON ra.accion_id = a.id
                 WHERE ri.usuario_id = :usuario_id AND ri.sede_id = :sede_id AND ri.estado = 'Activo'"
                );
                $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
                $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if(!empty($data)){
                    // AÑADIMOS EL NOMBRE DEL ROL AL RESULTADO
                    $resultado['nombre_rol'] = $data[0]['nombre_rol'];
                    // Extraemos solo la columna de permisos para el array de permisos
                    $resultado['permisos'] = array_column($data, 'nombre_accion');
                }
            }
        }

        return $resultado;
    }

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