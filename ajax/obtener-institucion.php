<?php

require_once "global-protection.php";
require_once "../controladores/institucion.controlador.php";
require_once "../modelos/institucion.modelo.php";

$id = $_POST["id"];

$respuesta = ControladorInstitucion::ctrMostrarInstitucion("id", $id);

if($respuesta) {
    $nombreRepresentante = ModeloInstitucion::mdlObtenerNombreUsuario($respuesta["id_usuario_representante"]);
    $respuesta["nombre_representante"] = $nombreRepresentante;
}

echo json_encode($respuesta);

?>