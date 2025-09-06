<?php

class ControladorInstitucion {

    /*=============================================
    MOSTRAR INSTITUCIÓN
    =============================================*/

    static public function ctrMostrarInstitucion($item, $valor) {
        $tabla = "institucion";
        $respuesta = ModeloInstitucion::mdlMostrarInstitucion($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR INSTITUCIÓN
    =============================================*/

    public function ctrCrearInstitucion() {

        // Debug: Verificar si llegan los datos POST
        echo "<script>console.log('POST data:', " . json_encode($_POST) . ");</script>";

        if(isset($_POST["nombreInstitucion"])) {

            // Validar que todos los campos requeridos estén presentes
            if(mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["nombreInstitucion"]) &&
                preg_match('/^[0-9]+$/', $_POST["codigoDane"]) &&
                preg_match('/^[0-9\-]+$/', $_POST["NIT"]) &&
                !empty($_POST["resolucionCreacion"]) &&
                !empty($_POST["direccion"]) &&
                filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) &&
                preg_match('/^[0-9\+\-\s]+$/', $_POST["telefono"]) &&
                preg_match('/^[0-9]+$/', $_POST["cantidadSedes"]) &&
                !empty($_POST["usuarioRepresentante"]) &&
                ($_POST["estadoInstitucion"] == "1" || $_POST["estadoInstitucion"] == "0")) {

                // Buscar el ID del usuario representante
                $nombreCompleto = $_POST["usuarioRepresentante"];
                echo "<script>console.log('Buscando usuario:', '" . $nombreCompleto . "');</script>";

                $idUsuario = self::ctrBuscarUsuarioPorNombre($nombreCompleto);
                echo "<script>console.log('ID usuario encontrado:', " . $idUsuario . ");</script>";

                if($idUsuario === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "El representante especificado no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "institucion";
                            }
                        });
                    </script>';
                    return;
                }

                // Verificar que el código DANE no exista
                $verificarDane = self::ctrVerificarCodigoDane($_POST["codigoDane"]);
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
                                window.location = "institucion";
                            }
                        });
                    </script>';
                    return;
                }

                // Preparar datos para insertar
                $tabla = "institucion";
                $datos = array(
                    "nombre" => $_POST["nombreInstitucion"],
                    "codigo_dane" => $_POST["codigoDane"],
                    "nit" => $_POST["NIT"],
                    "resolucion_creacion" => $_POST["resolucionCreacion"],
                    "direccion" => $_POST["direccion"],
                    "email" => $_POST["email"],
                    "telefono" => $_POST["telefono"],
                    "cantidad_sedes" => $_POST["cantidadSedes"],
                    "id_usuario_representante" => $idUsuario,
                    "estado" => $_POST["estadoInstitucion"] // Enviar 1 o 0 directamente
                );

                $respuesta = ModeloInstitucion::mdlIngresarInstitucion($tabla, $datos);
                echo "<script>console.log('Respuesta del modelo:', '" . $respuesta . "');</script>";

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La institución ha sido guardada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "institucion";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al guardar la institución: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "institucion";
                            }
                        });
                    </script>';
                }

            } else {
                echo "<script>console.log('Falló validación de campos');</script>";
                echo "<script>console.log('nombreInstitucion:', '" . $_POST["nombreInstitucion"] . "');</script>";
                echo "<script>console.log('codigoDane:', '" . $_POST["codigoDane"] . "');</script>";
                echo "<script>console.log('email:', '" . $_POST["email"] . "');</script>";

                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "institucion";
                        }
                    });
                </script>';
            }
        } else {
            echo "<script>console.log('No se recibió nombreInstitucion en POST');</script>";
            echo "<script>console.log('POST completo:', " . json_encode($_POST) . ");</script>";
        }
    }

    /*=============================================
    BUSCAR USUARIO POR NOMBRE COMPLETO
    =============================================*/

    static public function ctrBuscarUsuarioPorNombre($nombreCompleto) {
        $tabla = "usuarios";
        $respuesta = ModeloInstitucion::mdlBuscarUsuarioPorNombre($tabla, $nombreCompleto);
        return $respuesta;
    }

    /*=============================================
    VERIFICAR CÓDIGO DANE
    =============================================*/

    static public function ctrVerificarCodigoDane($codigoDane) {
        $tabla = "institucion";
        $respuesta = ModeloInstitucion::mdlVerificarCodigoDane($tabla, $codigoDane);
        return $respuesta;
    }

    /*=============================================
    EDITAR INSTITUCIÓN
    =============================================*/

    static public function ctrEditarInstitucion() {

        if(isset($_POST["editarNombreInstitucion"])) {

            if(mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["editarNombreInstitucion"])&&
                preg_match('/^[0-9]+$/', $_POST["editarCodigoDane"]) &&
                preg_match('/^[0-9\-]+$/', $_POST["editarNIT"]) &&
                !empty($_POST["editarResolucionCreacion"]) &&
                !empty($_POST["EditarDireccion"]) &&
                filter_var($_POST["editarEmail"], FILTER_VALIDATE_EMAIL) &&
                preg_match('/^[0-9\+\-\s]+$/', $_POST["editarTelefono"]) &&
                preg_match('/^[0-9]+$/', $_POST["editarCantidadSedes"]) &&
                !empty($_POST["editarUsuarioRepresentante"]) &&
                ($_POST["editarEstadoInstitucion"] == "1" || $_POST["editarEstadoInstitucion"] == "0")) {

                // Buscar el ID del usuario representante
                $nombreCompleto = $_POST["editarUsuarioRepresentante"];
                $idUsuario = self::ctrBuscarUsuarioPorNombre($nombreCompleto);

                if($idUsuario === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "El representante especificado no existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "institucion";
                            }
                        });
                    </script>';
                    return;
                }

                $tabla = "institucion";
                $datos = array(
                    "id" => $_POST["idInstitucion"],
                    "nombre" => $_POST["editarNombreInstitucion"],
                    "codigo_dane" => $_POST["editarCodigoDane"],
                    "nit" => $_POST["editarNIT"],
                    "resolucion_creacion" => $_POST["editarResolucionCreacion"],
                    "direccion" => $_POST["EditarDireccion"],
                    "email" => $_POST["editarEmail"],
                    "telefono" => $_POST["editarTelefono"],
                    "cantidad_sedes" => $_POST["editarCantidadSedes"],
                    "id_usuario_representante" => $idUsuario,
                    "estado" => $_POST["editarEstadoInstitucion"]
                );

                $respuesta = ModeloInstitucion::mdlEditarInstitucion($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La institución ha sido editada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "institucion";
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
                            window.location = "institucion";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR INSTITUCIÓN
    =============================================*/

    static public function ctrBorrarInstitucion() {

        if(isset($_GET["idInstitucion"])) {

            $tabla = "institucion";
            $datos = $_GET["idInstitucion"];

            $respuesta = ModeloInstitucion::mdlBorrarInstitucion($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "La institución ha sido borrada correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "institucion";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    CONTAR INSTITUCIONES
    =============================================*/

    static public function ctrContarInstituciones() {
        $tabla = "institucion";
        $respuesta = ModeloInstitucion::mdlContarInstituciones($tabla);
        return $respuesta['total'];
    }
}
?>