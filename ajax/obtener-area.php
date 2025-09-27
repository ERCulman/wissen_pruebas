<?php

require_once "global-protection.php";
require_once "../controladores/estructura-curricular.controlador.php";
require_once "../modelos/estructura-curricular.modelo.php";
require_once "../modelos/conexion.php";

if(isset($_POST["id"])) {
    $item = "id";
    $valor = $_POST["id"];
    
    $respuesta = ControladorEstructuraCurricular::ctrMostrarAreas($item, $valor);
    
    echo json_encode($respuesta);
}

?>