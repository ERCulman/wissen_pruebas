<?php

require_once "../controladores/estructura-curricular.controlador.php";
require_once "../modelos/estructura-curricular.modelo.php";
require_once "../modelos/conexion.php";

session_start();

// Determinar la acción basada en los parámetros
if(isset($_POST["accion"])) {
    $accion = $_POST["accion"];
} elseif(isset($_POST["asignaturas"]) && isset($_POST["grados"])) {
    $accion = "guardar";
} elseif(isset($_POST["grado_id"]) && isset($_POST["asignatura_id"])) {
    $accion = "eliminar";
} elseif(isset($_POST["grado_id"]) && !isset($_POST["asignatura_id"])) {
    // Determinar si es JSON o simple basado en si se espera JSON
    $accion = "obtener_simple";
} elseif(isset($_POST["grados"]) && !isset($_POST["asignaturas"]) && !isset($_POST["grado_id"])) {
    // Por defecto obtener_grado para compatibilidad con archivo original
    $accion = "obtener_grado";
} else {
    echo "error-parametros";
    exit;
}

switch($accion) {
    
    case "guardar":
        $asignaturas = $_POST["asignaturas"];
        $grados = $_POST["grados"];
        
        if(count($asignaturas) > 0 && count($grados) > 0) {
            $respuesta = ModeloEstructuraCurricular::mdlGuardarEstructuraCurricular($asignaturas, $grados);
            echo $respuesta;
        } else {
            echo "error-datos";
        }
        break;
        
    case "eliminar":
        $gradoId = $_POST["grado_id"];
        $asignaturaId = $_POST["asignatura_id"];
        
        try {
            $conexion = Conexion::conectar();
            $stmt = $conexion->prepare("DELETE FROM estructura_curricular WHERE oferta_academica_id = :grado_id AND asignatura_id = :asignatura_id");
            $stmt->bindParam(":grado_id", $gradoId, PDO::PARAM_INT);
            $stmt->bindParam(":asignatura_id", $asignaturaId, PDO::PARAM_INT);
            
            if($stmt->execute()) {
                echo "ok";
            } else {
                echo "error";
            }
        } catch(Exception $e) {
            echo "error: " . $e->getMessage();
        }
        break;
        
    case "verificar":
        $gradosConCurriculo = array();
        $curriculoCompleto = ControladorEstructuraCurricular::ctrMostrarEstructuraCurricular($_SESSION["id_usuario"]);
        
        foreach($_POST["grados"] as $gradoId) {
            foreach($curriculoCompleto as $item) {
                if($item["oferta_academica_id"] == $gradoId) {
                    $gradosConCurriculo[] = array(
                        'id' => $gradoId,
                        'nombre' => $item["nombre_grado"] . ' - ' . $item["nombre_nivel"]
                    );
                    break;
                }
            }
        }
        
        echo json_encode($gradosConCurriculo);
        break;
        
    case "obtener_grado":
        if(is_array($_POST["grados"])) {
            $grados = $_POST["grados"];
        } else {
            $grados = [$_POST["grados"]];
        }
        
        $curriculoCompleto = ControladorEstructuraCurricular::ctrMostrarEstructuraCurricular($_SESSION["id_usuario"]);
        
        $curriculo = array();
        foreach($curriculoCompleto as $item) {
            if(in_array($item["oferta_academica_id"], $grados)) {
                $curriculo[] = $item;
            }
        }
        
        echo json_encode([
            'success' => true,
            'grados' => $grados,
            'curriculo' => $curriculo
        ]);
        break;
        
    case "obtener_simple":
        $gradoId = $_POST["grado_id"];
        $curriculoCompleto = ControladorEstructuraCurricular::ctrMostrarEstructuraCurricular($_SESSION["id_usuario"]);
        
        $curriculo = array();
        foreach($curriculoCompleto as $item) {
            if($item["oferta_academica_id"] == $gradoId) {
                $curriculo[] = $item;
            }
        }
        
        echo json_encode([
            'success' => true,
            'grado_id' => $gradoId,
            'curriculo' => $curriculo
        ]);
        break;
        
    case "obtener_json":
        $gradoId = $_POST["grado_id"];
        $curriculoCompleto = ControladorEstructuraCurricular::ctrMostrarEstructuraCurricular($_SESSION["id_usuario"]);
        
        $curriculo = array();
        foreach($curriculoCompleto as $item) {
            if($item["oferta_academica_id"] == $gradoId) {
                $curriculo[] = $item;
            }
        }
        
        echo json_encode($curriculo);
        break;
        
    default:
        echo "error-accion";
        break;
}

?>