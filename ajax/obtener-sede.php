<?php
require_once "../controladores/sedes.controlador.php";
require_once "../modelos/sedes.modelo.php";

if(isset($_POST['id'])) {
    $id = $_POST['id'];

    $item = "id";
    $valor = $id;

    $respuesta = ControladorSede::ctrMostrarSede($item, $valor);

    if($respuesta) {
        // Obtener el nombre de la institución
        $nombreInstitucion = ModeloSede::mdlObtenerNombreInstitucion($respuesta["institucion_id"]);
        $respuesta["nombre_institucion"] = $nombreInstitucion;
    }

    echo json_encode($respuesta);
}
?>