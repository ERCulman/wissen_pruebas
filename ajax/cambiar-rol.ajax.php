<?php

session_start();

if(isset($_POST["rolSeleccionado"])){
    $rolSeleccionado = $_POST["rolSeleccionado"];
    
    // Guardar el rol activo en la sesión
    $_SESSION["rol_activo"] = $rolSeleccionado;
    
    // Opcional: Guardar información adicional del rol
    $partes = explode('_', $rolSeleccionado);
    $_SESSION["tipo_rol_activo"] = $partes[0]; // 'institucional' o 'sistema'
    $_SESSION["sede_rol_activo"] = isset($partes[1]) ? $partes[1] : null;
    
    echo "ok";
} else {
    echo "error";
}
?>