<?php

require_once "global-protection.php";
require_once "../autoload.php";
require_once "../controladores/jornadas.controlador.php";
require_once "../modelos/jornadas.modelo.php";

$id = $_POST["id"];

$respuesta = ControladorJornada::ctrMostrarJornada("id", $id);

echo json_encode($respuesta);

?>