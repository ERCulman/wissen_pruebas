<?php

require_once "conexion.php";

class ModeloRoles{

    /*=============================================
    MOSTRAR ROLES
    =============================================*/
    static public function mdlMostrarRoles($tabla, $item, $valor){
        if($item != null){
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt -> execute();
            return $stmt -> fetch();
        }else{
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY nombre_rol");
            $stmt -> execute();
            return $stmt -> fetchAll();
        }
    }

    /*=============================================
    MOSTRAR ROL INSTITUCIONAL POR ID
    =============================================*/
    static public function mdlMostrarRolInstitucional($id){
        $stmt = Conexion::conectar()->prepare("SELECT * FROM roles_institucionales WHERE id = :id");
        $stmt -> bindParam(":id", $id, PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> fetch();
    }

    /*=============================================
    MOSTRAR PERMISOS DE UN ROL
    =============================================*/
    static public function mdlMostrarPermisosRol($rolId){
        $stmt = Conexion::conectar()->prepare("SELECT accion_id FROM roles_acciones WHERE rol_id = :rol_id");
        $stmt -> bindParam(":rol_id", $rolId, PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_COLUMN);
    }

    /*=============================================
    ACTUALIZAR PERMISOS DE ROL
    =============================================*/
    static public function mdlActualizarPermisosRol($rolId, $acciones){
        $pdo = Conexion::conectar();
        $pdo->beginTransaction();
        
        try {
            // Eliminar permisos existentes
            $stmt = $pdo->prepare("DELETE FROM roles_acciones WHERE rol_id = :rol_id");
            $stmt->bindParam(":rol_id", $rolId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Insertar nuevos permisos
            if(!empty($acciones)){
                $stmt = $pdo->prepare("INSERT INTO roles_acciones (rol_id, accion_id) VALUES (:rol_id, :accion_id)");
                foreach($acciones as $accionId){
                    $stmt->bindParam(":rol_id", $rolId, PDO::PARAM_INT);
                    $stmt->bindParam(":accion_id", $accionId, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
            
            $pdo->commit();
            return "ok";
        } catch (Exception $e) {
            $pdo->rollback();
            return "error";
        }
    }

    /*=============================================
    MOSTRAR TODOS LOS USUARIOS
    =============================================*/
    static public function mdlMostrarTodosUsuarios(){
        $stmt = Conexion::conectar()->prepare("
            SELECT 
                u.id_usuario,
                u.numero_documento,
                u.nombres_usuario,
                u.apellidos_usuario,
                u.email_usuario,
                u.estado_usuario
            FROM usuarios u
            WHERE u.estado_usuario = 'Activo'
            ORDER BY u.nombres_usuario, u.apellidos_usuario
        ");
        $stmt -> execute();
        return $stmt -> fetchAll();
    }

    /*=============================================
    MOSTRAR USUARIOS CON ROLES (INSTITUCIONALES + SISTEMA)
    =============================================*/
    static public function mdlMostrarUsuariosConRoles(){
        $stmt = Conexion::conectar()->prepare("
            SELECT 
                u.id_usuario,
                u.numero_documento,
                u.nombres_usuario,
                u.apellidos_usuario,
                u.email_usuario,
                ri.id as rol_institucional_id,
                r.id_rol,
                r.nombre_rol,
                s.nombre_sede,
                ri.fecha_inicio,
                ri.fecha_fin,
                ri.estado,
                'institucional' as tipo_rol
            FROM usuarios u
            INNER JOIN roles_institucionales ri ON u.id_usuario = ri.usuario_id
            INNER JOIN roles r ON ri.rol_id = r.id_rol
            INNER JOIN sede s ON ri.sede_id = s.id
            
            UNION ALL
            
            SELECT 
                u.id_usuario,
                u.numero_documento,
                u.nombres_usuario,
                u.apellidos_usuario,
                u.email_usuario,
                ads.id as rol_institucional_id,
                r.id_rol,
                r.nombre_rol,
                'Sistema' as nombre_sede,
                ads.fecha_inicio,
                ads.fecha_fin,
                ads.estado,
                'sistema' as tipo_rol
            FROM usuarios u
            INNER JOIN administradores_sistema ads ON u.id_usuario = ads.usuario_id
            INNER JOIN roles r ON ads.rol_id = r.id_rol
            
            ORDER BY nombres_usuario, apellidos_usuario
        ");
        $stmt -> execute();
        return $stmt -> fetchAll();
    }

    /*=============================================
    VERIFICAR SI USUARIO ES ESTUDIANTE
    =============================================*/
    static public function mdlVerificarEsEstudiante($usuarioId){
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) as total FROM roles_institucionales ri
            INNER JOIN roles r ON ri.rol_id = r.id_rol
            WHERE ri.usuario_id = :usuario_id AND r.nombre_rol = 'Estudiante' AND ri.estado = 'Activo'
        ");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado['total'] > 0;
    }

    /*=============================================
    VERIFICAR SI ROL ES ESTUDIANTE
    =============================================*/
    static public function mdlVerificarRolEsEstudiante($rolId){
        $stmt = Conexion::conectar()->prepare("SELECT nombre_rol FROM roles WHERE id_rol = :rol_id");
        $stmt->bindParam(":rol_id", $rolId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado && $resultado['nombre_rol'] == 'Estudiante';
    }

    /*=============================================
    ASIGNAR ROL INSTITUCIONAL CON VALIDACIONES
    =============================================*/
    static public function mdlAsignarRolInstitucional($datos){
        // Validar reglas de negocio
        $esEstudiante = self::mdlVerificarEsEstudiante($datos["usuario_id"]);
        $rolEsEstudiante = self::mdlVerificarRolEsEstudiante($datos["rol_id"]);
        
        if($esEstudiante && !$rolEsEstudiante){
            return "error_estudiante_no_puede_otros_roles";
        }
        
        if($rolEsEstudiante && $esEstudiante){
            return "error_ya_es_estudiante";
        }
        
        $stmt = Conexion::conectar()->prepare("
            INSERT INTO roles_institucionales (usuario_id, rol_id, sede_id, fecha_inicio, fecha_fin, estado) 
            VALUES (:usuario_id, :rol_id, :sede_id, :fecha_inicio, :fecha_fin, :estado)
        ");
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":rol_id", $datos["rol_id"], PDO::PARAM_INT);
        $stmt->bindParam(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_fin", $datos["fecha_fin"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        
        if($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    VERIFICAR SI USUARIO TIENE RELACIONES ACTIVAS
    =============================================*/
    static public function mdlVerificarRelacionesActivas($rolInstitucionalId){
        $stmt = Conexion::conectar()->prepare("
            SELECT COUNT(*) as total FROM (
                SELECT 1 FROM matricula WHERE roles_institucionales_id = :rol_id
                UNION ALL
                SELECT 1 FROM asignacion_acudiente WHERE roles_institucionales_id = :rol_id
                UNION ALL
                SELECT 1 FROM cuerpo_docente WHERE rol_institucional_id = :rol_id
            ) as relaciones
        ");
        $stmt -> bindParam(":rol_id", $rolInstitucionalId, PDO::PARAM_INT);
        $stmt -> execute();
        $resultado = $stmt -> fetch();
        return $resultado['total'] > 0;
    }

    /*=============================================
    INACTIVAR ROL INSTITUCIONAL CON FECHA FIN
    =============================================*/
    static public function mdlInactivarRolInstitucional($id, $tipoRol = 'institucional'){
        $fechaFin = date('Y-m-d');
        
        if($tipoRol == 'sistema'){
            $stmt = Conexion::conectar()->prepare("UPDATE administradores_sistema SET estado = 'Inactivo', fecha_fin = :fecha_fin WHERE id = :id");
        } else {
            $stmt = Conexion::conectar()->prepare("UPDATE roles_institucionales SET estado = 'Inactivo', fecha_fin = :fecha_fin WHERE id = :id");
        }
        
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_fin", $fechaFin, PDO::PARAM_STR);
        
        if($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    ELIMINAR ROL INSTITUCIONAL
    =============================================*/
    static public function mdlEliminarRolInstitucional($id){
        $stmt = Conexion::conectar()->prepare("DELETE FROM roles_institucionales WHERE id = :id");
        $stmt -> bindParam(":id", $id, PDO::PARAM_INT);
        if($stmt -> execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    EDITAR ROL INSTITUCIONAL
    =============================================*/
    static public function mdlEditarRolInstitucional($datos){
        $stmt = Conexion::conectar()->prepare("
            UPDATE roles_institucionales 
            SET rol_id = :rol_id, sede_id = :sede_id, fecha_inicio = :fecha_inicio, 
                fecha_fin = :fecha_fin, estado = :estado 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":rol_id", $datos["rol_id"], PDO::PARAM_INT);
        $stmt->bindParam(":sede_id", $datos["sede_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_inicio", $datos["fecha_inicio"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_fin", $datos["fecha_fin"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        
        if($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }
    }

    /*=============================================
    ASIGNAR ROLES MASIVAMENTE
    =============================================*/
    static public function mdlAsignarRolesMasivo($usuarios, $rolId, $sedeId){
        $pdo = Conexion::conectar();
        $pdo->beginTransaction();
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO roles_institucionales (usuario_id, rol_id, sede_id, fecha_inicio, estado) 
                VALUES (:usuario_id, :rol_id, :sede_id, CURDATE(), 'Activo')
            ");
            
            foreach($usuarios as $usuarioId){
                $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
                $stmt->bindParam(":rol_id", $rolId, PDO::PARAM_INT);
                $stmt->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $pdo->commit();
            return "ok";
        } catch (Exception $e) {
            $pdo->rollback();
            return "error";
        }
    }
}