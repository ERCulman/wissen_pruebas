<?php

class ControladorAnioLectivo {

    /*=============================================
    MOSTRAR AÑO LECTIVO
    =============================================*/

    static public function ctrMostrarAnioLectivo($item, $valor) {
        $tabla = "anio_lectivo";
        $respuesta = ModeloAnioLectivo::mdlMostrarAnioLectivo($tabla, $item, $valor);
        return $respuesta;
    }

}
?>