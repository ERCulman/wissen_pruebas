<?php

class ControladorPeriodo {

    /*=============================================
    MOSTRAR PERIODO
    =============================================*/

    static public function ctrMostrarPeriodo($item, $valor) {
        $tabla = "periodo";
        $respuesta = ModeloPeriodo::mdlMostrarPeriodo($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR PERIODO
    =============================================*/

    public function ctrCrearPeriodo() {

        // Debug: Verificar si llegan los datos POST
        echo "<script>console.log('POST data:', " . json_encode($_POST) . ");</script>";

        if(isset($_POST["nombrePeriodo"])) {

            // Validar que todos los campos requeridos estén presentes
            if(mb_ereg_match('^[a-zA-Z0-9ñÑáéíóúüÁÉÍÓÚÜ\\s\\-]+$', $_POST["nombrePeriodo"]) &&
               !empty($_POST["fechaInicio"]) &&
               !empty($_POST["fechaFin"]) &&
               !empty($_POST["anioLectivo"])) {

                // Buscar el ID del año lectivo
                $nombreAnioLectivo = $_POST["anioLectivo"];
                echo "<script>console.log('Buscando año lectivo:', '" . $nombreAnioLectivo . "');</script>";

                $idAnioLectivo = self::ctrBuscarAnioLectivoPorNombre($nombreAnioLectivo);
                echo "<script>console.log('ID año lectivo encontrado:', " . $idAnioLectivo . ");</script>";

                if($idAnioLectivo === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "El año lectivo especificado no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "periodos";
                            }
                        });
                    </script>';
                    return;
                }

                // Verificar que el nombre del periodo no exista
                $verificarNombre = self::ctrVerificarNombrePeriodo($_POST["nombrePeriodo"]);
                if($verificarNombre) {
                    echo '<script>
                        Swal.fire({
                            icon: "error", 
                            title: "¡Error!",
                            text: "El nombre del periodo ya existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "periodos";
                            }
                        });
                    </script>';
                    return;
                }

                // Preparar datos para insertar
                $tabla = "periodo";
                $datos = array(
                    "nombre" => $_POST["nombrePeriodo"],
                    "fecha_inicio" => $_POST["fechaInicio"],
                    "fecha_fin" => $_POST["fechaFin"],
                    "anio_lectivo_id" => $idAnioLectivo
                );

                $respuesta = ModeloPeriodo::mdlIngresarPeriodo($tabla, $datos);
                echo "<script>console.log('Respuesta del modelo:', '" . $respuesta . "');</script>";

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El periodo ha sido guardado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "periodos";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al guardar el periodo: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "periodos";
                            }
                        });
                    </script>';
                }

            } else {
                echo "<script>console.log('Falló validación de campos');</script>";
                echo "<script>console.log('nombrePeriodo:', '" . $_POST["nombrePeriodo"] . "');</script>";

                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "periodos";
                        }
                    });
                </script>';
            }
        } else {
            echo "<script>console.log('No se recibió nombrePeriodo en POST');</script>";
            echo "<script>console.log('POST completo:', " . json_encode($_POST) . ");</script>";
        }
    }

    /*=============================================
    BUSCAR AÑO LECTIVO POR NOMBRE
    =============================================*/

    static public function ctrBuscarAnioLectivoPorNombre($nombreAnioLectivo) {
        $tabla = "anio_lectivo";
        $respuesta = ModeloPeriodo::mdlBuscarAnioLectivoPorNombre($tabla, $nombreAnioLectivo);
        return $respuesta;
    }

    /*=============================================
    VERIFICAR NOMBRE DE PERIODO
    =============================================*/

    static public function ctrVerificarNombrePeriodo($nombrePeriodo) {
        $tabla = "periodo";
        $respuesta = ModeloPeriodo::mdlVerificarNombrePeriodo($tabla, $nombrePeriodo);
        return $respuesta;
    }

    /*=============================================
    EDITAR PERIODO
    =============================================*/

    static public function ctrEditarPeriodo() {

        if(isset($_POST["editarNombrePeriodo"])) {

            if(mb_ereg_match('^[a-zA-Z0-9ñÑáéíóúüÁÉÍÓÚÜ\\s\\-]+$', $_POST["editarNombrePeriodo"]) &&
               !empty($_POST["editarFechaInicio"]) &&
               !empty($_POST["editarFechaFin"]) &&
               !empty($_POST["editarAnioLectivo"])) {

                // Buscar el ID del año lectivo
                $nombreAnioLectivo = $_POST["editarAnioLectivo"];
                $idAnioLectivo = self::ctrBuscarAnioLectivoPorNombre($nombreAnioLectivo);

                if($idAnioLectivo === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "El año lectivo especificado no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "periodos";
                            }
                        });
                    </script>';
                    return;
                }

                $tabla = "periodo";
                $datos = array(
                    "id" => $_POST["idPeriodo"],
                    "nombre" => $_POST["editarNombrePeriodo"],
                    "fecha_inicio" => $_POST["editarFechaInicio"],
                    "fecha_fin" => $_POST["editarFechaFin"],
                    "anio_lectivo_id" => $idAnioLectivo
                );

                $respuesta = ModeloPeriodo::mdlEditarPeriodo($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El periodo ha sido editado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "periodos";
                            }
                        });
                    </script>';
                }

            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "periodos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR PERIODO
    =============================================*/

    static public function ctrBorrarPeriodo() {

        if(isset($_GET["idPeriodo"])) {

            $tabla = "periodo";
            $datos = $_GET["idPeriodo"];

            $respuesta = ModeloPeriodo::mdlBorrarPeriodo($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "El periodo ha sido borrado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "periodos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    OBTENER AÑOS LECTIVOS PARA SELECT
    =============================================*/

    static public function ctrObtenerAniosLectivos() {
        $respuesta = ModeloPeriodo::mdlObtenerAniosLectivos();
        return $respuesta;
    }
}
?>