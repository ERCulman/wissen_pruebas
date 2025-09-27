<?php

require_once "global-protection.php";
require_once "../controladores/roles.controlador.php";
require_once "../modelos/roles.modelo.php";
require_once "../middleware/BackendProtector.php";

class AjaxRoles{

    /*=============================================
    OBTENER PERMISOS DE UN ROL
    =============================================*/
    public $rolId;
    public $accion;
    public $idRolInstitucional;

    public function ajaxObtenerPermisos(){
        if($this->accion == "obtenerPermisos"){
            // Verificar permisos para gestionar roles
            if (!BackendProtector::protectAjax('roles_ver')) {
                return;
            }
            
            $permisos = ControladorRoles::ctrMostrarPermisosRol($this->rolId);
            echo json_encode($permisos);
        }
    }

    /*=============================================
    OBTENER ROL INSTITUCIONAL PARA EDITAR
    =============================================*/
    public function ajaxObtenerRolInstitucional(){
        if($this->accion == "obtenerRolInstitucional"){
            $respuesta = ModeloRoles::mdlMostrarRolInstitucional($this->idRolInstitucional);
            echo json_encode($respuesta);
        }
    }
}

/*=============================================
OBTENER PERMISOS DE ROL
=============================================*/
if(isset($_POST["rolId"]) && isset($_POST["accion"])){
    $roles = new AjaxRoles();
    $roles -> rolId = $_POST["rolId"];
    $roles -> accion = $_POST["accion"];
    $roles -> ajaxObtenerPermisos();
}

/*=============================================
OBTENER ROL INSTITUCIONAL PARA EDITAR
=============================================*/
if(isset($_POST["idRolInstitucional"]) && isset($_POST["accion"])){
    $roles = new AjaxRoles();
    $roles -> idRolInstitucional = $_POST["idRolInstitucional"];
    $roles -> accion = $_POST["accion"];
    $roles -> ajaxObtenerRolInstitucional();
}

/*=============================================
ASIGNAR ROLES MASIVAMENTE CON VALIDACIONES
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "asignarRolesMasivo"){
    $asignaciones = json_decode($_POST["asignaciones"], true);
    $errores = 0;
    $erroresEstudiante = 0;
    
    foreach($asignaciones as $asignacion){
        $datos = array(
            "usuario_id" => $asignacion["usuario_id"],
            "rol_id" => $asignacion["rol_id"],
            "sede_id" => $asignacion["sede_id"],
            "fecha_inicio" => $asignacion["fecha_inicio"],
            "fecha_fin" => null,
            "estado" => "Activo"
        );
        
        $resultado = ModeloRoles::mdlAsignarRolInstitucional($datos);
        
        if($resultado == "error_estudiante_no_puede_otros_roles" || $resultado == "error_ya_es_estudiante"){
            $erroresEstudiante++;
        } else if($resultado != "ok"){
            $errores++;
        }
    }
    
    if($errores == 0 && $erroresEstudiante == 0){
        echo "ok";
    } else if($erroresEstudiante > 0){
        echo "error_estudiantes";
    } else {
        echo "error";
    }
}

/*=============================================
OBTENER ROLES DE UN USUARIO (INSTITUCIONALES + SISTEMA)
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "obtenerRolesUsuario"){
    $usuarioId = $_POST["usuarioId"];
    
    $stmt = Conexion::conectar()->prepare("
        SELECT 
            ri.id,
            r.nombre_rol,
            s.nombre_sede,
            ri.fecha_inicio,
            ri.fecha_fin,
            ri.estado,
            'institucional' as tipo_rol
        FROM roles_institucionales ri
        INNER JOIN roles r ON ri.rol_id = r.id_rol
        INNER JOIN sede s ON ri.sede_id = s.id
        WHERE ri.usuario_id = :usuario_id
        
        UNION ALL
        
        SELECT 
            ads.id,
            r.nombre_rol,
            'Sistema' as nombre_sede,
            ads.fecha_inicio,
            ads.fecha_fin,
            ads.estado,
            'sistema' as tipo_rol
        FROM administradores_sistema ads
        INNER JOIN roles r ON ads.rol_id = r.id_rol
        WHERE ads.usuario_id = :usuario_id2
        
        ORDER BY fecha_inicio DESC
    ");
    $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
    $stmt->bindParam(":usuario_id2", $usuarioId, PDO::PARAM_INT);
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($roles);
}

/*=============================================
AGREGAR ROL INDIVIDUAL CON VALIDACIONES
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "agregarRolIndividual"){
    $esRolSistema = isset($_POST["esRolSistema"]) && $_POST["esRolSistema"] === 'true';
    
    // Debug - remover después
    error_log("esRolSistema: " . ($esRolSistema ? 'true' : 'false'));
    error_log("POST data: " . print_r($_POST, true));
    
    if($esRolSistema){
        // Insertar en administradores_sistema
        try {
            $stmt = Conexion::conectar()->prepare("
                INSERT INTO administradores_sistema (usuario_id, rol_id, fecha_inicio, estado, autorizado_por, fecha_autorizacion) 
                VALUES (:usuario_id, :rol_id, :fecha_inicio, :estado, :autorizado_por, NOW())
            ");
            
            $usuarioId = $_POST["usuarioId"];
            $rolId = $_POST["rolId"];
            $fechaInicio = $_POST["fecha"];
            $estado = "Activo";
            $autorizadoPor = $_POST["autorizadoPorId"];
            
            $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(":rol_id", $rolId, PDO::PARAM_INT);
            $stmt->bindParam(":fecha_inicio", $fechaInicio, PDO::PARAM_STR);
            $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
            $stmt->bindParam(":autorizado_por", $autorizadoPor, PDO::PARAM_INT);
            
            if($stmt->execute()){
                echo "ok";
            } else {
                echo "error";
            }
        } catch (Exception $e) {
            echo "error";
        }
    } else {
        // Insertar en roles_institucionales con validaciones
        $datos = array(
            "usuario_id" => $_POST["usuarioId"],
            "rol_id" => $_POST["rolId"],
            "sede_id" => $_POST["sedeId"],
            "fecha_inicio" => $_POST["fecha"],
            "fecha_fin" => null,
            "estado" => "Activo"
        );
        
        $resultado = ModeloRoles::mdlAsignarRolInstitucional($datos);
        
        if($resultado == "error_estudiante_no_puede_otros_roles"){
            echo "error_estudiante";
        } else if($resultado == "error_ya_es_estudiante"){
            echo "error_ya_estudiante";
        } else {
            echo $resultado;
        }
    }
}

/*=============================================
ELIMINAR ROL INDIVIDUAL
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "eliminarRolIndividual"){
    $rolId = $_POST["rolId"];
    $tipoRol = $_POST["tipoRol"];
    
    // Verificar si tiene relaciones activas
    $tieneRelaciones = ModeloRoles::mdlVerificarRelacionesActivas($rolId);
    
    if($tieneRelaciones){
        // Solo inactivar
        $resultado = ModeloRoles::mdlInactivarRolInstitucional($rolId, $tipoRol);
        if($resultado == "ok"){
            echo "inactivado";
        } else {
            echo "error";
        }
    } else {
        // Eliminar completamente
        if($tipoRol == "sistema"){
            $stmt = Conexion::conectar()->prepare("DELETE FROM administradores_sistema WHERE id = :id");
        } else {
            $stmt = Conexion::conectar()->prepare("DELETE FROM roles_institucionales WHERE id = :id");
        }
        
        $stmt->bindParam(":id", $rolId, PDO::PARAM_INT);
        
        if($stmt->execute()){
            echo "ok";
        } else {
            echo "error";
        }
    }
}

/*=============================================
EDITAR SOLO ESTADO DE ROL
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "editarEstadoRol"){
    $rolId = $_POST["rolId"];
    $tipoRol = $_POST["tipoRol"];
    $estado = $_POST["estado"];
    
    // Si se inactiva, agregar fecha fin automáticamente
    $fechaFin = ($estado == "Inactivo") ? date('Y-m-d') : null;
    
    if($tipoRol == "sistema"){
        $stmt = Conexion::conectar()->prepare("
            UPDATE administradores_sistema 
            SET estado = :estado, fecha_fin = :fecha_fin
            WHERE id = :id
        ");
    } else {
        $stmt = Conexion::conectar()->prepare("
            UPDATE roles_institucionales 
            SET estado = :estado, fecha_fin = :fecha_fin
            WHERE id = :id
        ");
    }
    
    $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
    $stmt->bindParam(":fecha_fin", $fechaFin, PDO::PARAM_STR);
    $stmt->bindParam(":id", $rolId, PDO::PARAM_INT);
    
    if($stmt->execute()){
        echo "ok";
    } else {
        echo "error";
    }
}

/*=============================================
OBTENER PERMISOS COMPLETOS DE UN USUARIO
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "obtenerPermisosUsuario"){
    try {
        $usuarioId = $_POST["usuarioId"];
        
        // Obtener roles del usuario (simplificado)
        $stmt = Conexion::conectar()->prepare("
            SELECT 
                ri.id,
                ri.sede_id,
                r.id_rol,
                r.nombre_rol,
                s.nombre_sede,
                ri.estado
            FROM roles_institucionales ri
            INNER JOIN roles r ON ri.rol_id = r.id_rol
            INNER JOIN sede s ON ri.sede_id = s.id
            WHERE ri.usuario_id = :usuario_id
        ");
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener permisos heredados de roles
        $permisosHeredados = array();
        foreach($roles as $rol){
            if($rol['estado'] === 'Activo'){
                // Verificar si existe la tabla roles_acciones, si no, usar datos de ejemplo
                try {
                    $stmtPermisos = Conexion::conectar()->prepare("
                        SELECT a.nombre_accion
                        FROM roles_acciones ra
                        INNER JOIN acciones a ON ra.accion_id = a.id
                        WHERE ra.rol_id = :rol_id
                    ");
                    $stmtPermisos->bindParam(":rol_id", $rol['id_rol'], PDO::PARAM_INT);
                    $stmtPermisos->execute();
                    $acciones = $stmtPermisos->fetchAll(PDO::FETCH_COLUMN);
                } catch (Exception $e) {
                    // Si no existe la tabla, usar permisos de ejemplo
                    $acciones = array('usuarios_ver', 'estudiantes_ver', 'calificaciones_crear');
                }
                
                $permisosHeredados[] = array(
                    'rol' => $rol['nombre_rol'],
                    'sede' => $rol['nombre_sede'],
                    'acciones' => $acciones
                );
            }
        }
        
        // Obtener permisos especiales
        $permisosEspeciales = array();
        try {
            $stmtEspeciales = Conexion::conectar()->prepare("
                SELECT 
                    pe.usuario_id,
                    pe.accion_id,
                    pe.sede_jornada_id,
                    a.nombre_accion,
                    a.descripcion,
                    s.nombre_sede
                FROM permisos_especiales pe
                INNER JOIN acciones a ON pe.accion_id = a.id
                INNER JOIN sede_jornada sj ON pe.sede_jornada_id = sj.id
                INNER JOIN sede s ON sj.sede_id = s.id
                WHERE pe.usuario_id = :usuario_id
            ");
            $stmtEspeciales->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
            $stmtEspeciales->execute();
            $especiales = $stmtEspeciales->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($especiales as $especial){
                $permisosEspeciales[] = array(
                    'usuario_id' => $especial['usuario_id'],
                    'accion_id' => $especial['accion_id'],
                    'sede_jornada_id' => $especial['sede_jornada_id'],
                    'accion' => $especial['nombre_accion'],
                    'descripcion' => $especial['descripcion'],
                    'sede' => $especial['nombre_sede']
                );
            }
        } catch (Exception $e) {
            // Si hay error, mantener array vacío
        }
        
        // Crear resumen consolidado
        $resumen = array();
        foreach($permisosHeredados as $grupo){
            foreach($grupo['acciones'] as $accion){
                $resumen[] = array('accion' => $accion, 'origen' => 'rol');
            }
        }
        foreach($permisosEspeciales as $especial){
            $resumen[] = array('accion' => $especial['accion'], 'origen' => 'especial');
        }
        
        echo json_encode(array(
            'roles' => $roles,
            'permisosHeredados' => $permisosHeredados,
            'permisosEspeciales' => $permisosEspeciales,
            'resumen' => $resumen
        ));
        
    } catch (Exception $e) {
        echo json_encode(array(
            'error' => 'Error al cargar permisos: ' . $e->getMessage(),
            'roles' => array(),
            'permisosHeredados' => array(),
            'permisosEspeciales' => array(),
            'resumen' => array()
        ));
    }
}

/*=============================================
OBTENER ACCIONES DISPONIBLES (QUE NO TIENE POR ROL)
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "obtenerAccionesDisponibles"){
    $usuarioId = $_POST["usuarioId"];
    $sedeId = $_POST["sedeId"];
    
    // Obtener todas las acciones
    $stmtTodas = Conexion::conectar()->prepare("SELECT id, nombre_accion, descripcion FROM acciones ORDER BY nombre_accion");
    $stmtTodas->execute();
    $todasAcciones = $stmtTodas->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener acciones que ya tiene por rol
    $accionesPorRol = array();
    try {
        $stmtRol = Conexion::conectar()->prepare("
            SELECT DISTINCT a.nombre_accion
            FROM roles_institucionales ri
            INNER JOIN roles_acciones ra ON ri.rol_id = ra.rol_id
            INNER JOIN acciones a ON ra.accion_id = a.id
            WHERE ri.usuario_id = :usuario_id AND ri.sede_id = :sede_id AND ri.estado = 'Activo'
        ");
        $stmtRol->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmtRol->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmtRol->execute();
        $accionesPorRol = $stmtRol->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        // Si no existe la tabla roles_acciones, todas están disponibles
    }
    
    // Filtrar acciones que NO tiene por rol
    $accionesDisponibles = array();
    foreach($todasAcciones as $accion){
        if(!in_array($accion['nombre_accion'], $accionesPorRol)){
            $accionesDisponibles[] = $accion;
        }
    }
    
    echo json_encode($accionesDisponibles);
}

/*=============================================
AGREGAR PERMISO ESPECIAL
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "agregarPermisoEspecial"){
    try {
        $usuarioId = $_POST["usuarioId"];
        $accionId = $_POST["accionId"];
        $sedeId = $_POST["sedeId"];
        
        // Obtener sede_jornada_id basado en sede_id
        $stmtSedeJornada = Conexion::conectar()->prepare("
            SELECT id FROM sede_jornada 
            WHERE sede_id = :sede_id 
            ORDER BY id DESC LIMIT 1
        ");
        $stmtSedeJornada->bindParam(":sede_id", $sedeId, PDO::PARAM_INT);
        $stmtSedeJornada->execute();
        $sedeJornada = $stmtSedeJornada->fetch();
        
        if(!$sedeJornada){
            echo "error: No se encontró sede_jornada para la sede seleccionada";
            return;
        }
        
        $sedeJornadaId = $sedeJornada['id'];
        
        // Verificar si ya existe el permiso
        $stmtCheck = Conexion::conectar()->prepare("
            SELECT COUNT(*) as total FROM permisos_especiales 
            WHERE usuario_id = :usuario_id AND accion_id = :accion_id AND sede_jornada_id = :sede_jornada_id
        ");
        $stmtCheck->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmtCheck->bindParam(":accion_id", $accionId, PDO::PARAM_INT);
        $stmtCheck->bindParam(":sede_jornada_id", $sedeJornadaId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $existe = $stmtCheck->fetch();
        
        if($existe['total'] > 0){
            echo "error: El usuario ya tiene este permiso especial";
            return;
        }
        
        // Insertar permiso especial
        $stmt = Conexion::conectar()->prepare("
            INSERT INTO permisos_especiales (usuario_id, accion_id, sede_jornada_id)
            VALUES (:usuario_id, :accion_id, :sede_jornada_id)
        ");
        
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(":accion_id", $accionId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $sedeJornadaId, PDO::PARAM_INT);
        
        if($stmt->execute()){
            echo "ok";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "error_sql: " . $errorInfo[2];
        }
        
    } catch (Exception $e) {
        echo "error_exception: " . $e->getMessage();
    }
}

/*=============================================
ELIMINAR PERMISO ESPECIAL
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "eliminarPermisoEspecial"){
    try {
        $usuarioId = $_POST["usuarioId"];
        $accionId = $_POST["accionId"];
        $sedeJornadaId = $_POST["sedeJornadaId"];
        
        $stmt = Conexion::conectar()->prepare("
            DELETE FROM permisos_especiales 
            WHERE usuario_id = :usuario_id AND accion_id = :accion_id AND sede_jornada_id = :sede_jornada_id
        ");
        
        $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(":accion_id", $accionId, PDO::PARAM_INT);
        $stmt->bindParam(":sede_jornada_id", $sedeJornadaId, PDO::PARAM_INT);
        
        if($stmt->execute()){
            echo "ok";
        } else {
            echo "error";
        }
        
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
    }
}