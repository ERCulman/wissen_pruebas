<?php

class ControladorJornada {

    /*=============================================
    MOSTRAR JORNADA
    =============================================*/

    static public function ctrMostrarJornada($item, $valor) {
        if (!BackendProtector::protectController('jornadas_ver')) {
            return false;
        }
        $tabla = "jornada";
        $respuesta = ModeloJornada::mdlMostrarJornada($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR JORNADA
    =============================================*/

    public function ctrCrearJornada() {

        if (!BackendProtector::protectController('jornadas_crear')) {
            return;
        }

        // Debug: Verificar si llegan los datos POST
        echo "<script>console.log('POST data:', " . json_encode($_POST) . ");</script>";

        if(isset($_POST["codigoJornada"])) {

            // Validar que todos los campos requeridos estén presentes
            if(preg_match('/^[a-zA-Z0-9\-_]+$/', $_POST["codigoJornada"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["nombreJornada"])) {

                // Verificar que el código no exista
                $verificarCodigo = self::ctrVerificarCodigoJornada($_POST["codigoJornada"]);
                if($verificarCodigo) {
                    echo '<script>
                        Swal.fire({
                            icon: "error", 
                            title: "¡Error!",
                            text: "El código ya existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "jornadas";
                            }
                        });
                    </script>';
                    return;
                }

                // Preparar datos para insertar
                $tabla = "jornada";
                $datos = array(
                    "codigo" => $_POST["codigoJornada"],
                    "nombre" => $_POST["nombreJornada"]
                );

                $respuesta = ModeloJornada::mdlIngresarJornada($tabla, $datos);
                echo "<script>console.log('Respuesta del modelo:', '" . $respuesta . "');</script>";

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La jornada ha sido guardada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "jornadas";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al guardar la jornada: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "jornadas";
                            }
                        });
                    </script>';
                }

            } else {
                echo "<script>console.log('Falló validación de campos');</script>";
                echo "<script>console.log('codigoJornada:', '" . $_POST["codigoJornada"] . "');</script>";
                echo "<script>console.log('nombreJornada:', '" . $_POST["nombreJornada"] . "');</script>";

                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "jornadas";
                        }
                    });
                </script>';
            }
        } else {
            echo "<script>console.log('No se recibió codigoJornada en POST');</script>";
            echo "<script>console.log('POST completo:', " . json_encode($_POST) . ");</script>";
        }
    }

    /*=============================================
    VERIFICAR CÓDIGO JORNADA
    =============================================*/

    static public function ctrVerificarCodigoJornada($codigo) {
        $tabla = "jornada";
        $respuesta = ModeloJornada::mdlVerificarCodigoJornada($tabla, $codigo);
        return $respuesta;
    }

    /*=============================================
    EDITAR JORNADA
    =============================================*/

    static public function ctrEditarJornada() {

        if (!BackendProtector::protectController('jornadas_editar')) {
            return;
        }

        if(isset($_POST["editarCodigoJornada"])) {

            if(preg_match('/^[a-zA-Z0-9\-_]+$/', $_POST["editarCodigoJornada"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["editarNombreJornada"])) {

                $tabla = "jornada";
                $datos = array(
                    "id" => $_POST["idJornada"],
                    "codigo" => $_POST["editarCodigoJornada"],
                    "nombre" => $_POST["editarNombreJornada"]
                );

                $respuesta = ModeloJornada::mdlEditarJornada($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La jornada ha sido editada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "jornadas";
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
                            window.location = "jornadas";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR JORNADA
    =============================================*/

    static public function ctrBorrarJornada() {

        if (!BackendProtector::protectController('jornadas_eliminar')) {
            return;
        }

        if(isset($_GET["idJornada"])) {

            $jornadaId = $_GET["idJornada"];
            
            // Verificar referencias
            $referencias = ModeloJornada::mdlVerificarReferenciasJornada($jornadaId);
            
            if(!empty($referencias)) {
                $listaReferencias = implode(", ", $referencias);
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "No se puede eliminar",
                    text: "Esta jornada tiene referencias activas en: ' . $listaReferencias . '. No es posible eliminarla.",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    window.location = "jornadas";
                });
                </script>';
                return;
            }

            $tabla = "jornada";
            $respuesta = ModeloJornada::mdlBorrarJornada($tabla, $jornadaId);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "La jornada ha sido borrada correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "jornadas";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudo eliminar la jornada",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    window.location = "jornadas";
                });
                </script>';
            }
        }
    }
}
?>
