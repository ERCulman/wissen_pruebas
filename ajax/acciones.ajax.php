<?php

require_once "global-protection.php";
require_once "../controladores/acciones.controlador.php";
require_once "../modelos/acciones.modelo.php";
require_once "../middleware/BackendProtector.php";

class AjaxAcciones{

    /*=============================================
    EDITAR ACCIÓN
    =============================================*/
    public $idAccion;

    public function ajaxEditarAccion(){
        // Verificar permisos específicos
        if (!BackendProtector::protectAjax('permisos_ver')) {
            return;
        }
        
        $item = "id";
        $valor = $this->idAccion;
        $respuesta = ControladorAcciones::ctrMostrarAcciones($item, $valor);
        echo json_encode($respuesta);
    }
}

/*=============================================
EDITAR ACCIÓN
=============================================*/
if(isset($_POST["idAccion"])){
    $accion = new AjaxAcciones();
    $accion -> idAccion = $_POST["idAccion"];
    $accion -> ajaxEditarAccion();
}