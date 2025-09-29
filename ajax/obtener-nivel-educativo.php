<?php

require_once "global-protection.php";
require_once "../autoload.php";
require_once "../controladores/niveleducativo.controlador.php";
require_once "../modelos/niveleducativo.modelo.php";

$id = $_POST["id"];

$respuesta = ControladorNivelEducativo::ctrMostrarNivelEducativo("id", $id);

echo json_encode($respuesta);

?>