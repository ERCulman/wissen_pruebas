<?php
require_once "global-protection.php";
require_once "../controladores/sedes.controlador.php";
require_once "../modelos/sedes.modelo.php";
require_once "../middleware/BackendProtector.php";

if(isset($_POST['id'])) {
    // Verificar permisos para ver sedes
    if (!BackendProtector::protectAjax('sedes_ver')) {
        exit();
    }
    
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