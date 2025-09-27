<?php
require_once "global-protection.php";
require_once "../controladores/periodos.controlador.php";
require_once "../modelos/periodos.modelo.php";

if(isset($_POST['id'])) {
    $id = $_POST['id'];

    $item = "id";
    $valor = $id;

    $respuesta = ControladorPeriodo::ctrMostrarPeriodo($item, $valor);

    if($respuesta) {
        // Obtener el nombre del año lectivo
        $nombreAnioLectivo = ModeloPeriodo::mdlObtenerNombreAnioLectivo($respuesta["anio_lectivo_id"]);
        $respuesta["nombre_anio_lectivo"] = $nombreAnioLectivo;
    }

    echo json_encode($respuesta);
}
?>