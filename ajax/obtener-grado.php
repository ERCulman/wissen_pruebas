<?php
require_once "../controladores/grados.controlador.php";
require_once "../modelos/grados.modelo.php";

if(isset($_POST['id'])) {
    $id = $_POST['id'];

    $item = "id";
    $valor = $id;

    $respuesta = ControladorGrado::ctrMostrarGrado($item, $valor);

    if($respuesta) {
        // Obtener el nombre del nivel educativo
        $nombreNivelEducativo = ModeloGrado::mdlObtenerNombreNivelEducativo($respuesta["nivel_educativo_id"]);
        $respuesta["nombre_nivel_educativo"] = $nombreNivelEducativo;
    }

    echo json_encode($respuesta);
}
?>