<?php
require_once "global-protection.php";
require_once "../autoload.php";
require_once "../controladores/cursos.controlador.php";
require_once "../modelos/cursos.modelo.php";

if(isset($_POST['id'])) {
    $id = $_POST['id'];

    $item = "id";
    $valor = $id;

    $respuesta = ControladorCurso::ctrMostrarCurso($item, $valor);

    echo json_encode($respuesta);
}
?>