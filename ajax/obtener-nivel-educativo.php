<?php

require_once "../controladores/niveleducativo.controlador.php";
require_once "../modelos/niveleducativo.modelo.php";

$id = $_POST["id"];

$respuesta = ControladorNivelEducativo::ctrMostrarNivelEducativo("id", $id);

echo json_encode($respuesta);

?>