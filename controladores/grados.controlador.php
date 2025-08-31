<?php

class ControladorGrado {

    /*=============================================
    MOSTRAR GRADO
    =============================================*/

    static public function ctrMostrarGrado($item, $valor) {
        $tabla = "grado";
        $respuesta = ModeloGrado::mdlMostrarGrado($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR GRADO
    =============================================*/

    public function ctrCrearGrado() {

        // Debug: Verificar si llegan los datos POST
        echo "<script>console.log('POST data:', " . json_encode($_POST) . ");</script>";

        if(isset($_POST["numeroGrado"])) {

            // Validar que todos los campos requeridos estén presentes
            if(preg_match('/^[0-9]{1,5}$/', $_POST["numeroGrado"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["nombreGrado"]) &&
                !empty($_POST["nivelEducativoGrado"])) {

                // Buscar el ID del nivel educativo
                $nombreNivelEducativo = $_POST["nivelEducativoGrado"];
                echo "<script>console.log('Buscando nivel educativo:', '" . $nombreNivelEducativo . "');</script>";

                $idNivelEducativo = self::ctrBuscarNivelEducativoPorNombre($nombreNivelEducativo);
                echo "<script>console.log('ID nivel educativo encontrado:', " . $idNivelEducativo . ");</script>";

                if($idNivelEducativo === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "El nivel educativo especificado no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "grados";
                            }
                        });
                    </script>';
                    return;
                }

                // Verificar que el número de grado no exista
                $verificarNumero = self::ctrVerificarNumeroGrado($_POST["numeroGrado"]);
                if($verificarNumero) {
                    echo '<script>
                        Swal.fire({
                            icon: "error", 
                            title: "¡Error!",
                            text: "El número de grado ya existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "grados";
                            }
                        });
                    </script>';
                    return;
                }

                // Preparar datos para insertar
                $tabla = "grado";
                $datos = array(
                    "numero" => $_POST["numeroGrado"],
                    "nombre" => $_POST["nombreGrado"],
                    "nivel_educativo_id" => $idNivelEducativo
                );

                $respuesta = ModeloGrado::mdlIngresarGrado($tabla, $datos);
                echo "<script>console.log('Respuesta del modelo:', '" . $respuesta . "');</script>";

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El grado ha sido guardado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "grados";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al guardar el grado: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "grados";
                            }
                        });
                    </script>';
                }

            } else {
                echo "<script>console.log('Falló validación de campos');</script>";
                echo "<script>console.log('numeroGrado:', '" . $_POST["numeroGrado"] . "');</script>";
                echo "<script>console.log('nombreGrado:', '" . $_POST["nombreGrado"] . "');</script>";

                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "grados";
                        }
                    });
                </script>';
            }
        } else {
            echo "<script>console.log('No se recibió numeroGrado en POST');</script>";
            echo "<script>console.log('POST completo:', " . json_encode($_POST) . ");</script>";
        }
    }

    /*=============================================
    BUSCAR NIVEL EDUCATIVO POR NOMBRE
    =============================================*/

    static public function ctrBuscarNivelEducativoPorNombre($nombreNivelEducativo) {
        $tabla = "nivel_educativo";
        $respuesta = ModeloGrado::mdlBuscarNivelEducativoPorNombre($tabla, $nombreNivelEducativo);
        return $respuesta;
    }

    /*=============================================
    VERIFICAR NÚMERO DE GRADO
    =============================================*/

    static public function ctrVerificarNumeroGrado($numeroGrado) {
        $tabla = "grado";
        $respuesta = ModeloGrado::mdlVerificarNumeroGrado($tabla, $numeroGrado);
        return $respuesta;
    }

    /*=============================================
    EDITAR GRADO
    =============================================*/

    static public function ctrEditarGrado() {

        if(isset($_POST["editarNumeroGrado"])) {

            if(preg_match('/^[0-9]{1,5}$/', $_POST["editarNumeroGrado"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["editarNombreGrado"]) &&
                !empty($_POST["editarNivelEducativoGrado"])) {

                // Buscar el ID del nivel educativo
                $nombreNivelEducativo = $_POST["editarNivelEducativoGrado"];
                $idNivelEducativo = self::ctrBuscarNivelEducativoPorNombre($nombreNivelEducativo);

                if($idNivelEducativo === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "El nivel educativo especificado no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "grados";
                            }
                        });
                    </script>';
                    return;
                }

                $tabla = "grado";
                $datos = array(
                    "id" => $_POST["idGrado"],
                    "numero" => $_POST["editarNumeroGrado"],
                    "nombre" => $_POST["editarNombreGrado"],
                    "nivel_educativo_id" => $idNivelEducativo
                );

                $respuesta = ModeloGrado::mdlEditarGrado($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El grado ha sido editado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "grados";
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
                            window.location = "grados";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR GRADO
    =============================================*/

    static public function ctrBorrarGrado() {

        if(isset($_GET["idGrado"])) {

            $tabla = "grado";
            $datos = $_GET["idGrado"];

            $respuesta = ModeloGrado::mdlBorrarGrado($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "El grado ha sido borrado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "grados";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    OBTENER NIVELES EDUCATIVOS PARA SELECT
    =============================================*/

    static public function ctrObtenerNivelesEducativos() {
        $respuesta = ModeloGrado::mdlObtenerNivelesEducativos();
        return $respuesta;
    }
}
?>

