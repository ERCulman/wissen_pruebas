<?php

class ControladorSede {

    /*=============================================
    MOSTRAR SEDE
    =============================================*/

    static public function ctrMostrarSede($item, $valor) {
        $tabla = "sede";
        $respuesta = ModeloSede::mdlMostrarSede($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR SEDE
    =============================================*/

    public function ctrCrearSede() {

        // Debug: Verificar si llegan los datos POST
        echo "<script>console.log('POST data:', " . json_encode($_POST) . ");</script>";

        if(isset($_POST["numeroSede"])) {

            // Validar que todos los campos requeridos estén presentes
            if(preg_match('/^[0-9]{1,2}$/', $_POST["numeroSede"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["tipoSede"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["nombreSede"]) &&
                preg_match('/^[0-9]+$/', $_POST["codigoDaneSede"]) &&
                !empty($_POST["resolucionCreacionSede"]) &&
                !empty($_POST["fechaCreacionSede"]) &&
                !empty($_POST["direccionSede"]) &&
                !empty($_POST["institucionSede"]) &&
                ($_POST["estadoSede"] == "1" || $_POST["estadoSede"] == "0")) {

                // Buscar el ID de la institución
                $nombreInstitucion = $_POST["institucionSede"];
                echo "<script>console.log('Buscando institución:', '" . $nombreInstitucion . "');</script>";

                $idInstitucion = self::ctrBuscarInstitucionPorNombre($nombreInstitucion);
                echo "<script>console.log('ID institución encontrado:', " . $idInstitucion . ");</script>";

                if($idInstitucion === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "La institución especificada no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "sedes";
                            }
                        });
                    </script>';
                    return;
                }

                // Verificar que el código DANE no exista
                $verificarDane = self::ctrVerificarCodigoDane($_POST["codigoDaneSede"]);
                if($verificarDane) {
                    echo '<script>
                        Swal.fire({
                            icon: "error", 
                            title: "¡Error!",
                            text: "El código DANE ya existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "sedes";
                            }
                        });
                    </script>';
                    return;
                }

                // Preparar datos para insertar
                $tabla = "sede";
                $fechaActual = date("Y-m-d");
                $datos = array(
                    "numero_sede" => $_POST["numeroSede"],
                    "tipo_sede" => $_POST["tipoSede"],
                    "nombre_sede" => $_POST["nombreSede"],
                    "codigo_dane" => $_POST["codigoDaneSede"],
                    "consecutivo_dane" => isset($_POST["consecutivoDane"]) ? $_POST["consecutivoDane"] : null,
                    "resolucion_creacion" => $_POST["resolucionCreacionSede"],
                    "fecha_creacion_sede" => $_POST["fechaCreacionSede"],
                    "telefono_sede" => isset($_POST["telefonoSede"]) ? $_POST["telefonoSede"] : null,
                    "celular_sede" => isset($_POST["celularSede"]) ? $_POST["celularSede"] : null,
                    "fecha_registro" => $fechaActual,
                    "fecha_actualizacion" => $fechaActual,
                    "institucion_id" => $idInstitucion,
                    "direccion" => $_POST["direccionSede"],
                    "estado" => $_POST["estadoSede"]
                );

                $respuesta = ModeloSede::mdlIngresarSede($tabla, $datos);
                echo "<script>console.log('Respuesta del modelo:', '" . $respuesta . "');</script>";

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La sede ha sido guardada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "sedes";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al guardar la sede: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "sedes";
                            }
                        });
                    </script>';
                }

            } else {
                echo "<script>console.log('Falló validación de campos');</script>";
                echo "<script>console.log('numeroSede:', '" . $_POST["numeroSede"] . "');</script>";
                echo "<script>console.log('tipoSede:', '" . $_POST["tipoSede"] . "');</script>";
                echo "<script>console.log('nombreSede:', '" . $_POST["nombreSede"] . "');</script>";

                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "sedes";
                        }
                    });
                </script>';
            }
        } else {
            echo "<script>console.log('No se recibió numeroSede en POST');</script>";
            echo "<script>console.log('POST completo:', " . json_encode($_POST) . ");</script>";
        }
    }

    /*=============================================
    BUSCAR INSTITUCIÓN POR NOMBRE
    =============================================*/

    static public function ctrBuscarInstitucionPorNombre($nombreInstitucion) {
        $tabla = "institucion";
        $respuesta = ModeloSede::mdlBuscarInstitucionPorNombre($tabla, $nombreInstitucion);
        return $respuesta;
    }

    /*=============================================
    VERIFICAR CÓDIGO DANE
    =============================================*/

    static public function ctrVerificarCodigoDane($codigoDane) {
        $tabla = "sede";
        $respuesta = ModeloSede::mdlVerificarCodigoDane($tabla, $codigoDane);
        return $respuesta;
    }

    /*=============================================
    EDITAR SEDE
    =============================================*/

    static public function ctrEditarSede() {

        if(isset($_POST["editarNumeroSede"])) {

            if(preg_match('/^[0-9]{1,2}$/', $_POST["editarNumeroSede"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["editarTipoSede"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["editarNombreSede"]) &&
                preg_match('/^[0-9]+$/', $_POST["editarCodigoDaneSede"]) &&
                !empty($_POST["editarResolucionCreacionSede"]) &&
                !empty($_POST["editarFechaCreacionSede"]) &&
                !empty($_POST["editarDireccionSede"]) &&
                !empty($_POST["editarInstitucionSede"]) &&
                ($_POST["editarEstadoSede"] == "1" || $_POST["editarEstadoSede"] == "0")) {

                // Buscar el ID de la institución
                $nombreInstitucion = $_POST["editarInstitucionSede"];
                $idInstitucion = self::ctrBuscarInstitucionPorNombre($nombreInstitucion);

                if($idInstitucion === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "La institución especificada no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "sedes";
                            }
                        });
                    </script>';
                    return;
                }

                $tabla = "sede";
                $fechaActual = date("Y-m-d");
                $datos = array(
                    "id" => $_POST["idSede"],
                    "numero_sede" => $_POST["editarNumeroSede"],
                    "tipo_sede" => $_POST["editarTipoSede"],
                    "nombre_sede" => $_POST["editarNombreSede"],
                    "codigo_dane" => $_POST["editarCodigoDaneSede"],
                    "consecutivo_dane" => isset($_POST["editarConsecutivoDane"]) ? $_POST["editarConsecutivoDane"] : null,
                    "resolucion_creacion" => $_POST["editarResolucionCreacionSede"],
                    "fecha_creacion_sede" => $_POST["editarFechaCreacionSede"],
                    "telefono_sede" => isset($_POST["editarTelefonoSede"]) ? $_POST["editarTelefonoSede"] : null,
                    "celular_sede" => isset($_POST["editarCelularSede"]) ? $_POST["editarCelularSede"] : null,
                    "fecha_actualizacion" => $fechaActual,
                    "institucion_id" => $idInstitucion,
                    "direccion" => $_POST["editarDireccionSede"],
                    "estado" => $_POST["editarEstadoSede"]
                );

                $respuesta = ModeloSede::mdlEditarSede($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La sede ha sido editada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "sedes";
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
                            window.location = "sedes";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR SEDE
    =============================================*/

    static public function ctrBorrarSede() {

        if(isset($_GET["idSede"])) {

            $tabla = "sede";
            $datos = $_GET["idSede"];

            $respuesta = ModeloSede::mdlBorrarSede($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "La sede ha sido borrada correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "sedes";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    OBTENER INSTITUCIONES PARA SELECT
    =============================================*/

    static public function ctrObtenerInstituciones() {
        $respuesta = ModeloSede::mdlObtenerInstituciones();
        return $respuesta;
    }

    /*=============================================
    CONTAR SEDES
    =============================================*/

    static public function ctrContarSedes() {
        $tabla = "sede";
        $respuesta = ModeloSede::mdlContarSedes($tabla);
        return $respuesta['total'];
    }
}
?>

