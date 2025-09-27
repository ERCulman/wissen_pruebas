<?php

class ControladorCurso {

    /*=============================================
    MOSTRAR CURSO
    =============================================*/

    static public function ctrMostrarCurso($item, $valor) {
        // PROTECCIÓN: Verificar permisos antes de mostrar curso
        if (!BackendProtector::protectController('cursos_ver')) {
            return false;
        }
        
        $tabla = "curso";
        $respuesta = ModeloCurso::mdlMostrarCurso($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR CURSO
    =============================================*/

    public function ctrCrearCurso() {

        // PROTECCIÓN: Verificar permisos antes de crear curso
        if (!BackendProtector::protectController('cursos_crear')) {
            return;
        }

        // Debug: Verificar si llegan los datos POST
        echo "<script>console.log('POST data:', " . json_encode($_POST) . ");</script>";

        if(isset($_POST["tipoCurso"])) {

            // Validar que todos los campos requeridos estén presentes
            if(($_POST["tipoCurso"] == "Númerico" || $_POST["tipoCurso"] == "Alfabético") &&
                !empty($_POST["nombreCurso"]) &&
                strlen($_POST["nombreCurso"]) <= 10 &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ0-9\s]+$', $_POST["nombreCurso"])) {

                // Verificar que el nombre del curso no exista
                $verificarNombre = self::ctrVerificarNombreCurso($_POST["nombreCurso"]);
                if($verificarNombre) {
                    echo '<script>
                        Swal.fire({
                            icon: "error", 
                            title: "¡Error!",
                            text: "El nombre del curso ya existe en el sistema",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "cursos";
                            }
                        });
                    </script>';
                    return;
                }

                // Preparar datos para insertar
                $tabla = "curso";
                $datos = array(
                    "tipo" => $_POST["tipoCurso"],
                    "nombre" => $_POST["nombreCurso"]
                );

                $respuesta = ModeloCurso::mdlIngresarCurso($tabla, $datos);
                echo "<script>console.log('Respuesta del modelo:', '" . $respuesta . "');</script>";

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El curso ha sido guardado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "cursos";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al guardar el curso: ' . $respuesta . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "cursos";
                            }
                        });
                    </script>';
                }

            } else {
                echo "<script>console.log('Falló validación de campos');</script>";
                echo "<script>console.log('tipoCurso:', '" . $_POST["tipoCurso"] . "');</script>";
                echo "<script>console.log('nombreCurso:', '" . $_POST["nombreCurso"] . "');</script>";

                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos, el nombre no puede exceder 10 caracteres o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "cursos";
                        }
                    });
                </script>';
            }
        } else {
            echo "<script>console.log('No se recibió tipoCurso en POST');</script>";
            echo "<script>console.log('POST completo:', " . json_encode($_POST) . ");</script>";
        }
    }

    /*=============================================
    VERIFICAR NOMBRE DE CURSO
    =============================================*/

    static public function ctrVerificarNombreCurso($nombreCurso) {
        $tabla = "curso";
        $respuesta = ModeloCurso::mdlVerificarNombreCurso($tabla, $nombreCurso);
        return $respuesta;
    }

    /*=============================================
    EDITAR CURSO
    =============================================*/

    static public function ctrEditarCurso() {

        // PROTECCIÓN: Verificar permisos antes de editar curso
        if (!BackendProtector::protectController('cursos_editar')) {
            return;
        }

        if(isset($_POST["editarTipoCurso"])) {

            if(($_POST["editarTipoCurso"] == "Númerico" || $_POST["editarTipoCurso"] == "Alfabético") &&
                !empty($_POST["editarNombreCurso"]) &&
                strlen($_POST["editarNombreCurso"]) <= 10 &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ0-9\s]+$', $_POST["editarNombreCurso"])) {

                $tabla = "curso";
                $datos = array(
                    "id" => $_POST["idCurso"],
                    "tipo" => $_POST["editarTipoCurso"],
                    "nombre" => $_POST["editarNombreCurso"]
                );

                $respuesta = ModeloCurso::mdlEditarCurso($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El curso ha sido editado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "cursos";
                            }
                        });
                    </script>';
                }

            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡Los campos no pueden ir vacíos, el nombre no puede exceder 10 caracteres o llevar caracteres especiales!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "cursos";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR CURSO
    =============================================*/

    static public function ctrBorrarCurso() {

        // PROTECCIÓN: Verificar permisos antes de eliminar curso
        if (!BackendProtector::protectController('cursos_eliminar')) {
            return;
        }

        if(isset($_GET["idCurso"])) {

            $cursoId = $_GET["idCurso"];
            
            // Verificar si el curso tiene referencias en otras tablas
            $referencias = ModeloCurso::mdlVerificarReferenciasCurso($cursoId);
            
            if(!empty($referencias)) {
                $listaReferencias = implode(", ", $referencias);
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "No se puede eliminar",
                    text: "Este curso tiene referencias activas en: ' . $listaReferencias . '. No es posible eliminarlo.",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    window.location = "cursos";
                });
                </script>';
                return;
            }

            $tabla = "curso";
            $respuesta = ModeloCurso::mdlBorrarCurso($tabla, $cursoId);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "El curso ha sido borrado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "cursos";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudo eliminar el curso",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    window.location = "cursos";
                });
                </script>';
            }
        }
    }
}
?>