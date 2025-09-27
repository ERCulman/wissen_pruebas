<?php
require_once "global-protection.php";
require_once "../controladores/grados.controlador.php";
require_once "../modelos/grados.modelo.php";
require_once "../middleware/BackendProtector.php";

if(isset($_POST['id'])) {
    // Verificar permisos para ver grados
    if (!BackendProtector::protectAjax('grados_ver')) {
        exit();
    }
    
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