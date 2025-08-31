<?php
// Mostrar todos los errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer header JSON
header('Content-Type: application/json');

try {
    echo json_encode(array("debug" => "Archivo ejecutado correctamente"));

    // Verificar si llega el POST
    if(!isset($_POST["eliminarMatricula"])) {
        echo json_encode(array("error" => "No se recibió eliminarMatricula"));
        exit();
    }

    $id = $_POST["eliminarMatricula"];
    echo json_encode(array("debug" => "ID recibido: " . $id));

    // Intentar incluir archivos
    require_once "../controladores/matricula.controlador.php";
    echo json_encode(array("debug" => "Controlador cargado"));

    require_once "../modelos/matricula.modelo.php";
    echo json_encode(array("debug" => "Modelo cargado"));

    // Verificar si existe la clase
    if(!class_exists('ControladorMatricula')) {
        echo json_encode(array("error" => "Clase ControladorMatricula no existe"));
        exit();
    }

    // Verificar si existe el método
    if(!method_exists('ControladorMatricula', 'ctrBorrarMatriculaAjax')) {
        echo json_encode(array("error" => "Método ctrBorrarMatriculaAjax no existe"));
        exit();
    }

    // Intentar llamar al método
    $respuesta = ControladorMatricula::ctrBorrarMatriculaAjax($id);
    echo json_encode($respuesta);

} catch(Exception $e) {
    echo json_encode(array("error" => "Excepción: " . $e->getMessage()));
} catch(Error $e) {
    echo json_encode(array("error" => "Error fatal: " . $e->getMessage()));
}
?>