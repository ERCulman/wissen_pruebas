<?php

require_once "../controladores/estructura-curricular.controlador.php";
require_once "../modelos/estructura-curricular.modelo.php";
require_once "../modelos/conexion.php";

// Determinar la acción basada en los parámetros
if(isset($_POST["accion"])) {
    $accion = $_POST["accion"];
} elseif(isset($_POST["nombreAsignatura"]) && isset($_POST["areaAsignatura"])) {
    $accion = "crear";
} elseif(isset($_POST["id"])) {
    $accion = "obtener_por_id";
} elseif(isset($_POST["area_id"])) {
    $accion = "obtener_por_area";
} else {
    echo json_encode(["error" => "parametros-faltantes"]);
    exit;
}

switch($accion) {
    
    case "obtener_por_id":
        $item = "id";
        $valor = $_POST["id"];
        
        $respuesta = ControladorEstructuraCurricular::ctrMostrarAsignaturas($item, $valor);
        
        echo json_encode($respuesta[0]); // Devolver solo el primer elemento ya que es por ID
        break;
        
    case "obtener_por_area":
        $respuesta = ControladorEstructuraCurricular::ctrObtenerAsignaturasPorArea($_POST["area_id"]);
        
        echo json_encode($respuesta);
        break;
        
    case "crear":
        if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nombreAsignatura"]) &&
           !empty($_POST["areaAsignatura"])) {
            
            $tabla = "asignatura";
            $datos = array(
                "nombre" => $_POST["nombreAsignatura"],
                "area_id" => $_POST["areaAsignatura"]
            );
            
            $respuesta = ModeloEstructuraCurricular::mdlIngresarAsignatura($tabla, $datos);
            
            echo $respuesta;
            
        } else {
            echo "error-validacion";
        }
        break;
        
    default:
        echo json_encode(["error" => "accion-no-valida"]);
        break;
}

?>