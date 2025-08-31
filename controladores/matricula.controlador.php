<?php

class ControladorMatricula {

    /*=============================================
    MOSTRAR MATRÍCULA
    =============================================*/
    static public function ctrMostrarMatricula($item, $valor) {
        $tabla = "matricula";
        $respuesta = ModeloMatricula::mdlMostrarMatricula($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR ESTUDIANTE (REUTILIZANDO MÓDULO USUARIOS)
    =============================================*/
    public function ctrCrearEstudiante() {
        if(isset($_POST["numeroDocumentoEstudiante"])) {
            if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["numeroDocumentoEstudiante"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["nombresEstudiante"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["apellidosEstudiante"])) {

                $datosUsuario = array(
                    "numero_documento" => $_POST["numeroDocumentoEstudiante"],
                    "tipo_documento" => $_POST["tipoDocumentoEstudiante"],
                    "nombres_usuario" => $_POST["nombresEstudiante"],
                    "apellidos_usuario" => $_POST["apellidosEstudiante"],
                    "sexo_usuario" => $_POST["generoEstudiante"],
                    "rh_usuario" => $_POST["rhEstudiante"],
                    "fecha_nacimiento" => $_POST["fechaNacimientoEstudiante"],
                    "edad_usuario" => $_POST["edadEstudiante"],
                    "telefono_usuario" => $_POST["telefonoEstudiante"],
                    "email_usuario" => $_POST["emailEstudiante"],
                    "usuario" => $_POST["usuarioEstudiante"],
                    "password" => crypt($_POST["passwordEstudiante"], '$1$rasmusle$'),
                    "estado_usuario" => "Activo",
                    "id_rol" => "Estudiante"
                );

                $respuesta = ModeloUsuarios::mdlCrearUsuario("usuarios", $datosUsuario);
                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({ icon: "success", title: "¡Correcto!", text: "El estudiante ha sido registrado."})
                        .then(r => r.value && (window.location = "matricula"));
                    </script>';
                } else {
                    echo '<script>Swal.fire({ icon: "error", title: "¡Error!", text: "Error al registrar el estudiante."});</script>';
                }
            } else {
                echo '<script>Swal.fire({ icon: "error", title: "¡Error!", text: "¡Los campos no pueden llevar caracteres especiales!"});</script>';
            }
        }
    }

    /*=============================================
    CREAR ACUDIENTE NUEVO
    =============================================*/
    public function ctrCrearAcudienteNuevo() {
        if(isset($_POST["numeroDocumentoAcudienteNuevo"])) {
            if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["numeroDocumentoAcudienteNuevo"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["nombresAcudienteNuevo"]) &&
                mb_ereg_match('^[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ\s]+$', $_POST["apellidosAcudienteNuevo"])) {

                $datosUsuario = array(
                    "numero_documento" => $_POST["numeroDocumentoAcudienteNuevo"],
                    "tipo_documento" => $_POST["tipoDocumentoAcudienteNuevo"],
                    "nombres_usuario" => $_POST["nombresAcudienteNuevo"],
                    "apellidos_usuario" => $_POST["apellidosAcudienteNuevo"],
                    "sexo_usuario" => $_POST["generoAcudienteNuevo"],
                    "rh_usuario" => $_POST["rhAcudienteNuevo"],
                    "fecha_nacimiento" => $_POST["fechaNacimientoAcudienteNuevo"],
                    "edad_usuario" => $_POST["edadAcudienteNuevo"],
                    "telefono_usuario" => $_POST["telefonoAcudienteNuevo"],
                    "email_usuario" => $_POST["emailAcudienteNuevo"],
                    "usuario" => $_POST["usuarioAcudienteNuevo"],
                    "password" => crypt($_POST["passwordAcudienteNuevo"], '$1$rasmusle$'),
                    "estado_usuario" => "Activo",
                    "id_rol" => "Acudiente"
                );

                $respuesta = ModeloUsuarios::mdlCrearUsuario("usuarios", $datosUsuario);
                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({ icon: "success", title: "¡Correcto!", text: "El acudiente ha sido registrado."})
                        .then(r => { if(r.value){ $("#modalRegistrarAcudiente").modal("hide"); } });
                    </script>';
                } else {
                    echo '<script>Swal.fire({ icon: "error", title: "¡Error!", text: "Error al registrar el acudiente."});</script>';
                }
            } else {
                echo '<script>Swal.fire({ icon: "error", title: "¡Error!", text: "¡Los campos no pueden llevar caracteres especiales!"});</script>';
            }
        }
    }

    /*=============================================
    CREAR MATRÍCULA COMPLETA
    =============================================*/
    public function ctrCrearMatricula() {
        if(isset($_POST["btnRegistrarMatricula"])) {
            if(!empty($_POST["grupoMatricula"]) && !empty($_POST["idUsuarioEstudiante"]) && !empty($_POST["acudientesData"])) {

                // =============================================
                // NUEVA VALIDACIÓN EN SERVIDOR
                // =============================================
                $idUsuarioEstudiante = $_POST["idUsuarioEstudiante"];
                $matriculaExistente = ModeloMatricula::mdlVerificarMatriculaActivaPorUsuario($idUsuarioEstudiante);

                if ($matriculaExistente) {
                    echo '<script>Swal.fire({ icon: "error", title: "¡Acción no permitida!", text: "El estudiante seleccionado ya tiene una matrícula activa en el sistema."});</script>';
                    return; // Detiene la ejecución para evitar el registro
                }
                // =============================================

                $conexion = null;
                try {
                    $conexion = Conexion::conectar();
                    $conexion->beginTransaction();

                    //$idUsuarioEstudiante = $_POST["idUsuarioEstudiante"]; // Ya está definido arriba

                    $estudianteId = ModeloMatricula::mdlObtenerIdEstudiantePorUsuario($idUsuarioEstudiante);

                    if(!$estudianteId) {
                        $datosEstudiante = array(
                            "usuarios_id" => $idUsuarioEstudiante,
                            "codigo_estudiante" => "EST" . date("Y") . str_pad($idUsuarioEstudiante, 4, "0", STR_PAD_LEFT),
                            "fecha_ingreso" => $_POST["fechaIngreso"],
                            "grado_ingreso" => $_POST["gradoMatricula"],
                            "estado_anio_anterior" => $_POST["estadoAnioAnterior"],
                            "estado_actual" => "matriculado"
                        );
                        if (ModeloMatricula::mdlCrearEstudiante("estudiante", $datosEstudiante) != "ok") {
                            throw new Exception("Error al crear el registro del estudiante.");
                        }
                        $estudianteId = ModeloMatricula::mdlObtenerIdEstudiantePorUsuario($idUsuarioEstudiante);
                    }

                    $datosMatricula = array(
                        "estudiante_id" => $estudianteId,
                        "grupo_id" => $_POST["grupoMatricula"],
                        "fecha_matricula" => $_POST["fechaMatricula"],
                        "numero_matricula" => $_POST["numeroMatricula"],
                        "nuevo" => $_POST["estudianteNuevo"],
                        "estado_matricula" => "Activo"
                    );
                    $matriculaId = ModeloMatricula::mdlCrearMatricula("matricula", $datosMatricula);
                    if (!is_numeric($matriculaId)) {
                        throw new Exception("Error al crear la matrícula.");
                    }

                    $acudientes = json_decode($_POST["acudientesData"], true);
                    if ($acudientes && is_array($acudientes)) {
                        foreach ($acudientes as $acudiente) {
                            $datosAcudiente = array(
                                "usuarios_id" => $acudiente["id_usuario"],
                                "matricula_id" => $matriculaId,
                                "parentesco" => $acudiente["parentesco"],
                                "autorizado_recoger" => $acudiente["autorizado_recoger"],
                                "observacion" => isset($acudiente["observacion"]) ? $acudiente["observacion"] : ""
                            );
                            if (ModeloMatricula::mdlCrearAcudiente("acudiente", $datosAcudiente) != "ok") {
                                throw new Exception("Error al asignar el acudiente.");
                            }
                        }
                    } else {
                        throw new Exception("No se recibieron datos de acudientes.");
                    }

                    $conexion->commit();
                    echo '<script>
                        Swal.fire({ icon: "success", title: "¡Correcto!", text: "La matrícula ha sido registrada.", allowOutsideClick: false })
                        .then(r => r.value && (window.location.href = "matricula"));
                    </script>';
                } catch(Exception $e) {
                    if(isset($conexion)) $conexion->rollback();
                    echo '<script>Swal.fire({ icon: "error", title: "¡Error en el Proceso!", text: "'.addslashes($e->getMessage()).'"});</script>';
                }
            } else {
                echo '<script>Swal.fire({ icon: "warning", title: "¡Campos Incompletos!", text: "Faltan campos obligatorios."});</script>';
            }
        }
    }

    /*=============================================
    EDITAR MATRÍCULA
    =============================================*/
    static public function ctrEditarMatricula() {
        if (isset($_POST["idMatricula"]) && !empty($_POST["idMatricula"])) {

            if (empty($_POST["editarGrupoMatricula"]) || empty($_POST["acudientesData"])) {
                echo '<script>Swal.fire("¡Campos Incompletos!", "Debe seleccionar un grupo y asignar al menos un acudiente.", "warning");</script>';
                return;
            }

            $conexion = null;
            try {
                $conexion = Conexion::conectar();
                $conexion->beginTransaction();

                $datosMatricula = array(
                    "id" => $_POST["idMatricula"],
                    "grupo_id" => $_POST["editarGrupoMatricula"],
                    "fecha_matricula" => $_POST["editarFechaMatricula"],
                    "numero_matricula" => $_POST["editarNumeroMatricula"],
                    "nuevo" => $_POST["editarEstudianteNuevo"],
                    "estado_matricula" => $_POST["editarEstadoMatricula"]
                );
                if (ModeloMatricula::mdlEditarMatricula("matricula", $datosMatricula) !== "ok") {
                    throw new Exception("Error al actualizar los datos de la matrícula.");
                }

                $infoMatricula = self::ctrMostrarMatricula("id", $_POST["idMatricula"]);
                if (!$infoMatricula || !isset($infoMatricula["estudiante_id"])) {
                    throw new Exception("No se pudo encontrar la información del estudiante asociado.");
                }

                $datosEstudiante = array(
                    "id" => $infoMatricula["estudiante_id"],
                    "fecha_ingreso" => $_POST["editarFechaIngreso"],
                    "grado_ingreso" => $_POST["editarGradoMatricula"],
                    "estado_anio_anterior" => $_POST["editarEstadoAnioAnterior"]
                );
                if (ModeloMatricula::mdlEditarEstudiante("estudiante", $datosEstudiante) !== "ok") {
                    throw new Exception("Error al actualizar los datos del estudiante.");
                }

                $matriculaId = $_POST["idMatricula"];
                ModeloMatricula::mdlEliminarAcudientesPorMatricula("acudiente", $matriculaId);

                $acudientes = json_decode($_POST["acudientesData"], true);
                if (is_array($acudientes) && !empty($acudientes)) {
                    foreach ($acudientes as $acudiente) {
                        $datosAcudiente = array(
                            "usuarios_id" => $acudiente["id_usuario"],
                            "matricula_id" => $matriculaId,
                            "parentesco" => $acudiente["parentesco"],
                            "autorizado_recoger" => $acudiente["autorizado_recoger"],
                            "observacion" => isset($acudiente["observacion"]) ? $acudiente["observacion"] : ""
                        );
                        if (ModeloMatricula::mdlCrearAcudiente("acudiente", $datosAcudiente) !== "ok") {
                            throw new Exception("Error al reasignar el acudiente: " . $acudiente['nombres_completos']);
                        }
                    }
                } else {
                    throw new Exception("La lista de acudientes no puede estar vacía.");
                }

                $conexion->commit();
                echo '<script>
                    Swal.fire({ icon: "success", title: "¡Actualizado!", text: "La matrícula ha sido modificada." })
                    .then(r => r.value && (window.location = "matricula"));
                </script>';
            } catch (Exception $e) {
                if ($conexion) $conexion->rollBack();
                echo '<script>Swal.fire({ icon: "error", title: "¡Error en el Proceso!", text: "'.addslashes($e->getMessage()).'" });</script>';
            }
        }
    }

    /*=============================================
    AJAX: CREAR ACUDIENTE NUEVO
    =============================================*/
    public function ajaxCrearAcudiente() {

        $datosUsuario = array(
            "numero_documento" => $_POST["numeroDocumentoAcudienteNuevo"],
            "tipo_documento" => $_POST["tipoDocumentoAcudienteNuevo"],
            "nombres_usuario" => $_POST["nombresAcudienteNuevo"],
            "apellidos_usuario" => $_POST["apellidosAcudienteNuevo"],
            "sexo_usuario" => $_POST["generoAcudienteNuevo"],
            "rh_usuario" => $_POST["rhAcudienteNuevo"],
            "fecha_nacimiento" => $_POST["fechaNacimientoAcudienteNuevo"],
            "edad_usuario" => $_POST["edadAcudienteNuevo"],
            "telefono_usuario" => $_POST["telefonoAcudienteNuevo"],
            "email_usuario" => $_POST["emailAcudienteNuevo"],
            "usuario" => $_POST["usuarioAcudienteNuevo"],
            "password" => crypt($_POST["passwordAcudienteNuevo"], '$1$rasmusle$'),
            "estado_usuario" => "Activo",
            "id_rol" => "Acudiente"
        );

        // Se reutiliza el modelo de usuarios para la creación
        $respuesta = ModeloUsuarios::mdlCrearUsuario("usuarios", $datosUsuario);

        if($respuesta == "ok"){
            // Si todo sale bien, se devuelve una respuesta de éxito
            echo json_encode(["success" => true, "message" => "Acudiente registrado con éxito."]);
        } else {
            // Si hay un error, se devuelve un mensaje de error
            echo json_encode(["success" => false, "message" => "Error al registrar el acudiente."]);
        }
    }

    /*=============================================
    BORRAR MATRÍCULA
    =============================================*/
    static public function ctrBorrarMatricula() {
        if(isset($_GET["idMatricula"])) {
            $respuesta = ModeloMatricula::mdlBorrarMatricula("matricula", $_GET["idMatricula"]);
            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({ icon: "success", title: "¡Eliminado!", text: "La matrícula ha sido eliminada." })
                    .then(r => r.value && (window.location = "matricula"));
                </script>';
            }
        }
    }

    /*=============================================
    MÉTODOS DE BÚSQUEDA Y DATOS (Helpers)
    =============================================*/
    static public function ctrBuscarEstudiante($criterio, $valor) {
        return ModeloMatricula::mdlBuscarEstudiante($criterio, $valor);
    }

    static public function ctrBuscarAcudiente($documento) {
        return ModeloMatricula::mdlBuscarAcudiente($documento);
    }

    static public function ctrObtenerSedesUsuario() {
        return ModeloMatricula::mdlObtenerTodasLasSedesActivas();
    }

    static public function ctrObtenerGradosPorSede($sedeId) {
        return ModeloMatricula::mdlObtenerGradosPorSede($sedeId);
    }

    static public function ctrObtenerGruposPorGrado($gradoId, $sedeId) {
        return ModeloMatricula::mdlObtenerGruposPorGrado($gradoId, $sedeId);
    }

    static public function ctrObtenerGrupos() {
        return ModeloMatricula::mdlObtenerGrupos();
    }

    static public function ctrBorrarMatriculaAjax($idMatricula) {
        $respuesta = ModeloMatricula::mdlBorrarMatricula("matricula", $idMatricula);
        if($respuesta == "ok") {
            return array("success" => true, "message" => "Matrícula eliminada correctamente");
        } else {
            return array("success" => false, "message" => "Error al eliminar la matrícula");
        }
    }
}
?>