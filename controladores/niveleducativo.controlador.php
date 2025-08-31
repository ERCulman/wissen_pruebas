<?php

class ControladorNivelEducativo {

    /*=============================================
    MOSTRAR NIVEL EDUCATIVO
    =============================================*/

    static public function ctrMostrarNivelEducativo($item, $valor) {
        $tabla = "nivel_educativo";
        $respuesta = ModeloNivelEducativo::mdlMostrarNivelEducativo($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR NIVEL EDUCATIVO
    =============================================*/

    public function ctrCrearNivelEducativo() {

        // Debug: Verificar si llegan los datos POST
        echo "<script>console.log('POST data:', " . json_encode($_POST) . ");</script>";

        if(isset($_POST["codigoNivelEducativo"])) {

            // Validar que todos los campos requeridos estén presentes
            if(preg_match('/^[a-zA-Z0-9\-_]+$/', $_POST["codigoNivelEducativo"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["nombreNivelEducativo"])) {

                // Verificar que el código no exista
                $verificarCodigo = self::ctrVerificarCodigoNivelEducativo($_POST["codigoNivelEducativo"]);
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
                                window.location = "niveleducativo";
                            }
                        });
                    </script>';
                    return;
                }

                // Preparar datos para insertar
                $tabla = "nivel_educativo";
                $datos = array(
                    "codigo" => $_POST["codigoNivelEducativo"],
                    "nombre" => $_POST["nombreNivelEducativo"]
                );

                $respuesta = ModeloNivelEducativo::mdlIngresarNivelEducativo($tabla, $datos);
                echo "<script>console.log('Respuesta del modelo:', '" . $respuesta . "');</script>";

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El nivel educativo ha sido guardado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "niveleducativo";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al guardar el nivel educativo: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "niveleducativo";
                            }
                        });
                    </script>';
                }

            } else {
                echo "<script>console.log('Falló validación de campos');</script>";
                echo "<script>console.log('codigoNivelEducativo:', '" . $_POST["codigoNivelEducativo"] . "');</script>";
                echo "<script>console.log('nombreNivelEducativo:', '" . $_POST["nombreNivelEducativo"] . "');</script>";

                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "niveleducativo";
                        }
                    });
                </script>';
            }
        } else {
            echo "<script>console.log('No se recibió codigoNivelEducativo en POST');</script>";
            echo "<script>console.log('POST completo:', " . json_encode($_POST) . ");</script>";
        }
    }

    /*=============================================
    VERIFICAR CÓDIGO NIVEL EDUCATIVO
    =============================================*/

    static public function ctrVerificarCodigoNivelEducativo($codigo) {
        $tabla = "nivel_educativo";
        $respuesta = ModeloNivelEducativo::mdlVerificarCodigoNivelEducativo($tabla, $codigo);
        return $respuesta;
    }

    /*=============================================
    EDITAR NIVEL EDUCATIVO
    =============================================*/

    static public function ctrEditarNivelEducativo() {

        if(isset($_POST["editarCodigoNivelEducativo"])) {

            if(preg_match('/^[a-zA-Z0-9\-_]+$/', $_POST["editarCodigoNivelEducativo"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["editarNombreNivelEducativo"])) {

                $tabla = "nivel_educativo";
                $datos = array(
                    "id" => $_POST["idNivelEducativo"],
                    "codigo" => $_POST["editarCodigoNivelEducativo"],
                    "nombre" => $_POST["editarNombreNivelEducativo"]
                );

                $respuesta = ModeloNivelEducativo::mdlEditarNivelEducativo($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El nivel educativo ha sido editado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "niveleducativo";
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
                            window.location = "niveleducativo";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR NIVEL EDUCATIVO
    =============================================*/

    static public function ctrBorrarNivelEducativo() {

        if(isset($_GET["idNivelEducativo"])) {

            $tabla = "nivel_educativo";
            $datos = $_GET["idNivelEducativo"];

            $respuesta = ModeloNivelEducativo::mdlBorrarNivelEducativo($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "El nivel educativo ha sido borrado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "niveleducativo";
                        }
                    });
                </script>';
            }
        }
    }
}
?>

