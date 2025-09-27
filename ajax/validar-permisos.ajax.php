<?php

require_once "global-protection.php";
require_once "../controladores/auth.controlador.php";
require_once "../modelos/auth.modelo.php";

/*=============================================
VALIDAR PERMISO ESPECÍFICO
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "validarPermiso"){
    $permiso = $_POST["permiso"];
    $sedeId = isset($_POST["sedeId"]) ? $_POST["sedeId"] : null;
    
    $tienePermiso = ControladorAuth::ctrVerificarPermiso($permiso, $sedeId);
    $esAdmin = ControladorAuth::ctrEsAdministradorSistema();
    
    echo json_encode([
        'tienePermiso' => $tienePermiso || $esAdmin,
        'esAdmin' => $esAdmin,
        'permiso' => $permiso
    ]);
}

/*=============================================
VALIDAR ACCESO A MÓDULO
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "validarModulo"){
    $modulo = $_POST["modulo"];
    
    $puedeAcceder = ControladorAuth::ctrVerificarAccesoModulo($modulo);
    $esAdmin = ControladorAuth::ctrEsAdministradorSistema();
    
    echo json_encode([
        'puedeAcceder' => $puedeAcceder || $esAdmin,
        'esAdmin' => $esAdmin,
        'modulo' => $modulo
    ]);
}

/*=============================================
OBTENER PERMISOS DEL USUARIO ACTUAL
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "obtenerPermisos"){
    $sedeId = isset($_POST["sedeId"]) ? $_POST["sedeId"] : null;
    
    $permisos = ControladorAuth::ctrObtenerPermisosUsuario($sedeId);
    $roles = ControladorAuth::ctrObtenerRolesUsuario();
    $esAdmin = ControladorAuth::ctrEsAdministradorSistema();
    
    echo json_encode([
        'permisos' => $permisos,
        'roles' => $roles,
        'esAdmin' => $esAdmin,
        'totalPermisos' => count($permisos)
    ]);
}

/*=============================================
VALIDAR MÚLTIPLES PERMISOS
=============================================*/
if(isset($_POST["accion"]) && $_POST["accion"] == "validarMultiples"){
    $permisos = json_decode($_POST["permisos"], true);
    $sedeId = isset($_POST["sedeId"]) ? $_POST["sedeId"] : null;
    
    $resultados = [];
    $esAdmin = ControladorAuth::ctrEsAdministradorSistema();
    
    foreach($permisos as $permiso){
        $tienePermiso = ControladorAuth::ctrVerificarPermiso($permiso, $sedeId);
        $resultados[$permiso] = $tienePermiso || $esAdmin;
    }
    
    echo json_encode([
        'resultados' => $resultados,
        'esAdmin' => $esAdmin
    ]);
}

?>