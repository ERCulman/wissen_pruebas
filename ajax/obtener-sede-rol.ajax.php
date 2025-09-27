<?php

require_once "global-protection.php";
require_once "../controladores/auth.controlador.php";
require_once "../modelos/auth.modelo.php";
require_once "../modelos/conexion.php";

if(isset($_POST["rolSeleccionado"])){
    $rolSeleccionado = $_POST["rolSeleccionado"];
    $usuarioId = $_SESSION["id_usuario"];
    
    // Obtener roles del usuario
    $rolesUsuario = ModeloAuth::mdlObtenerRolesUsuario($usuarioId);
    
    foreach($rolesUsuario as $rol){
        $valorRol = $rol['tipo'] . '_' . ($rol['tipo'] == 'institucional' ? 'sede_' . ($rol['sede_id'] ?? 'unknown') : 'sistema');
        if($valorRol == $rolSeleccionado){
            echo $rol['nombre_sede'];
            break;
        }
    }
} else {
    echo "SISTEMA";
}
?>