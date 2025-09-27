<?php

class ControladorEstructuraCurricular {

    /*=============================================
    MOSTRAR ÁREAS
    =============================================*/
    static public function ctrMostrarAreas($item, $valor) {
        $tabla = "area";
        $respuesta = ModeloEstructuraCurricular::mdlMostrarAreas($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR ÁREA
    =============================================*/
    public function ctrCrearArea() {
        if(isset($_POST["nombreArea"])) {
            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nombreArea"])) {
                $tabla = "area";
                $datos = array("nombre" => $_POST["nombreArea"]);
                $respuesta = ModeloEstructuraCurricular::mdlIngresarArea($tabla, $datos);
                
                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El área ha sido guardada correctamente"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "estructura-curricular";
                            }
                        });
                    </script>';
                }
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "¡El área no puede ir vacía o llevar caracteres especiales!"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "estructura-curricular";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR ÁREA
    =============================================*/
    public function ctrEditarArea() {
        if(isset($_POST["editarNombreArea"])) {
            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombreArea"])) {
                $tabla = "area";
                $datos = array(
                    "id" => $_POST["idArea"],
                    "nombre" => $_POST["editarNombreArea"]
                );
                $respuesta = ModeloEstructuraCurricular::mdlEditarArea($tabla, $datos);
                
                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "El área ha sido editada correctamente"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "estructura-curricular";
                            }
                        });
                    </script>';
                }
            }
        }
    }

    /*=============================================
    BORRAR ÁREA
    =============================================*/
    public function ctrBorrarArea() {
        if(isset($_GET["idArea"])) {
            $tabla = "area";
            $datos = $_GET["idArea"];
            $respuesta = ModeloEstructuraCurricular::mdlBorrarArea($tabla, $datos);
            
            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "El área ha sido borrada correctamente"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "estructura-curricular";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    MOSTRAR ASIGNATURAS
    =============================================*/
    static public function ctrMostrarAsignaturas($item, $valor) {
        $tabla = "asignatura";
        $respuesta = ModeloEstructuraCurricular::mdlMostrarAsignaturas($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR ASIGNATURA
    =============================================*/
    public function ctrCrearAsignatura() {
        if(isset($_POST["nombreAsignatura"])) {
            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nombreAsignatura"]) &&
               !empty($_POST["areaAsignatura"])) {
                $tabla = "asignatura";
                $datos = array(
                    "nombre" => $_POST["nombreAsignatura"],
                    "area_id" => $_POST["areaAsignatura"]
                );
                $respuesta = ModeloEstructuraCurricular::mdlIngresarAsignatura($tabla, $datos);
                
                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La asignatura ha sido guardada correctamente"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "estructura-curricular#asignaturas";
                            }
                        });
                    </script>';
                }
            }
        }
    }

    /*=============================================
    EDITAR ASIGNATURA
    =============================================*/
    public function ctrEditarAsignatura() {
        if(isset($_POST["editarNombreAsignatura"])) {
            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombreAsignatura"]) &&
               !empty($_POST["editarAreaAsignatura"])) {
                $tabla = "asignatura";
                $datos = array(
                    "id" => $_POST["idAsignatura"],
                    "nombre" => $_POST["editarNombreAsignatura"],
                    "area_id" => $_POST["editarAreaAsignatura"]
                );
                $respuesta = ModeloEstructuraCurricular::mdlEditarAsignatura($tabla, $datos);
                
                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡Correcto!",
                            text: "La asignatura ha sido editada correctamente"
                        }).then(function(result) {
                            if (result.value) {
                                window.location = "estructura-curricular#asignaturas";
                            }
                        });
                    </script>';
                }
            }
        }
    }

    /*=============================================
    BORRAR ASIGNATURA
    =============================================*/
    public function ctrBorrarAsignatura() {
        if(isset($_GET["idAsignatura"])) {
            $tabla = "asignatura";
            $datos = $_GET["idAsignatura"];
            $respuesta = ModeloEstructuraCurricular::mdlBorrarAsignatura($tabla, $datos);
            
            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "La asignatura ha sido borrada correctamente"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "estructura-curricular#asignaturas";
                        }
                    });
                </script>';
            }
        }
    }

    /*=============================================
    OBTENER ASIGNATURAS POR ÁREA
    =============================================*/
    static public function ctrObtenerAsignaturasPorArea($areaId) {
        $tabla = "asignatura";
        $respuesta = ModeloEstructuraCurricular::mdlMostrarAsignaturas($tabla, "area_id", $areaId);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR ESTRUCTURA CURRICULAR
    =============================================*/
    static public function ctrMostrarEstructuraCurricular($idUsuario) {
        $respuesta = ModeloEstructuraCurricular::mdlMostrarEstructuraCurricular($idUsuario);
        return $respuesta;
    }

    /*=============================================
    BORRAR ESTRUCTURA CURRICULAR
    =============================================*/
    public function ctrBorrarEstructuraCurricular() {
        if(isset($_GET["idEstructura"])) {
            $tabla = "estructura_curricular";
            $datos = $_GET["idEstructura"];
            $respuesta = ModeloEstructuraCurricular::mdlBorrarEstructuraCurricular($tabla, $datos);
            
            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Correcto!",
                        text: "El registro ha sido eliminado correctamente"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "estructura-curricular#ver-curriculo";
                        }
                    });
                </script>';
            }
        }
    }
}

?>