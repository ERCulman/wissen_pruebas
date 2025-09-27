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
        // Solo verificar permisos cuando se envía el formulario
        if(isset($_POST["btnRegistrarMatricula"])) {
            if (!ControladorAuth::ctrVerificarPermiso('matricula_crear') && !ControladorAuth::ctrEsAdministradorSistema()) {
                echo '<script>Swal.fire({ icon: "error", title: "Sin permisos", text: "No tiene permisos para crear matrículas."});</script>';
                return;
            }
        }
        if(isset($_POST["btnRegistrarMatricula"])) {
            if(!empty($_POST["grupoMatricula"]) && !empty($_POST["idUsuarioEstudiante"]) && !empty($_POST["acudientesData"])) {

                // =============================================
                // NUEVA VALIDACIÓN EN SERVIDOR
                // =============================================
                $idUsuarioEstudiante = $_POST["idUsuarioEstudiante"];
                $matriculaExistente = ModeloMatricula::mdlVerificarMatriculaActivaPorUsuario($idUsuarioEstudiante);

                if ($matriculaExistente) {
                    echo '<script>Swal.fire({ icon: "error", title: "¡Acción no permitida!", text: "El estudiante seleccionado ya tiene una matrícula activa en el sistema."});</script>';
                    return;
                }
                // =============================================

                $conexion = null;
                try {
                    $conexion = Conexion::conectar();
                    $conexion->beginTransaction();

                    // Obtener sede_jornada_id del grupo seleccionado
                    $stmt = $conexion->prepare("SELECT oa.sede_jornada_id FROM grupo g INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id WHERE g.id = :grupo_id");
                    $stmt->bindParam(":grupo_id", $_POST["grupoMatricula"], PDO::PARAM_INT);
                    $stmt->execute();
                    $grupoInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if(!$grupoInfo) {
                        throw new Exception("No se pudo obtener la información del grupo.");
                    }

                    // Obtener sede_id del sede_jornada_id
                    $stmt = $conexion->prepare("SELECT sede_id FROM sede_jornada WHERE id = :sede_jornada_id");
                    $stmt->bindParam(":sede_jornada_id", $grupoInfo['sede_jornada_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $sedeInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Verificar si ya existe rol institucional de estudiante para este usuario en esta sede
                    $stmt = $conexion->prepare("SELECT id FROM roles_institucionales WHERE usuario_id = :usuario_id AND rol_id = (SELECT id_rol FROM roles WHERE nombre_rol = 'Estudiante') AND sede_id = :sede_id AND estado = 'Activo'");
                    $stmt->bindParam(":usuario_id", $idUsuarioEstudiante, PDO::PARAM_INT);
                    $stmt->bindParam(":sede_id", $sedeInfo['sede_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $rolExistente = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($rolExistente) {
                        $rolEstudianteId = $rolExistente['id'];
                    } else {
                        // Crear rol institucional para el estudiante
                        $datosRolEstudiante = array(
                            "usuario_id" => $idUsuarioEstudiante,
                            "sede_id" => $sedeInfo['sede_id'],
                            "fecha_inicio" => $_POST["fechaIngreso"]
                        );
                        $rolEstudianteId = ModeloMatricula::mdlCrearRolInstitucionalEstudiante($datosRolEstudiante);
                        if (!is_numeric($rolEstudianteId)) {
                            throw new Exception("Error al crear el rol institucional del estudiante.");
                        }
                    }

                    // Crear matrícula
                    $datosMatricula = array(
                        "roles_institucionales_id" => $rolEstudianteId,
                        "grupo_id" => $_POST["grupoMatricula"],
                        "sede_jornada_id" => $grupoInfo['sede_jornada_id'],
                        "numero_matricula" => $_POST["numeroMatricula"],
                        "fecha_matricula" => $_POST["fechaMatricula"],
                        "nuevo" => $_POST["estudianteNuevo"],
                        "repitente" => $_POST["esRepitente"],
                        "estado_matricula" => "Matriculado"
                    );
                    $matriculaId = ModeloMatricula::mdlCrearMatricula("matricula", $datosMatricula);
                    if (!is_numeric($matriculaId)) {
                        throw new Exception("Error al crear la matrícula.");
                    }

                    // Crear roles institucionales y asignaciones para acudientes
                    $acudientes = json_decode($_POST["acudientesData"], true);
                    if ($acudientes && is_array($acudientes)) {
                        foreach ($acudientes as $acudiente) {
                            // Buscar o crear rol institucional de acudiente
                            $stmt = $conexion->prepare("
                                INSERT INTO roles_institucionales (usuario_id, rol_id, sede_id, fecha_inicio, estado) 
                                SELECT :usuario_id, r.id_rol, :sede_id, :fecha_inicio, 'Activo'
                                FROM roles r 
                                WHERE r.nombre_rol = 'Acudiente'
                                AND NOT EXISTS (
                                    SELECT 1 FROM roles_institucionales ri 
                                    WHERE ri.usuario_id = :usuario_id2 
                                    AND ri.rol_id = r.id_rol 
                                    AND ri.sede_id = :sede_id2 
                                    AND ri.estado = 'Activo'
                                )
                            ");
                            $stmt->bindParam(":usuario_id", $acudiente["id_usuario"], PDO::PARAM_INT);
                            $stmt->bindParam(":usuario_id2", $acudiente["id_usuario"], PDO::PARAM_INT);
                            $stmt->bindParam(":sede_id", $sedeInfo['sede_id'], PDO::PARAM_INT);
                            $stmt->bindParam(":sede_id2", $sedeInfo['sede_id'], PDO::PARAM_INT);
                            $stmt->bindParam(":fecha_inicio", $_POST["fechaMatricula"], PDO::PARAM_STR);
                            $stmt->execute();

                            // Obtener el ID del rol institucional (existente o recién creado)
                            $stmt = $conexion->prepare("SELECT id FROM roles_institucionales WHERE usuario_id = :usuario_id AND rol_id = (SELECT id_rol FROM roles WHERE nombre_rol = 'Acudiente') AND sede_id = :sede_id AND estado = 'Activo'");
                            $stmt->bindParam(":usuario_id", $acudiente["id_usuario"], PDO::PARAM_INT);
                            $stmt->bindParam(":sede_id", $sedeInfo['sede_id'], PDO::PARAM_INT);
                            $stmt->execute();
                            $rolResult = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if (!$rolResult) {
                                throw new Exception("No se pudo obtener el rol institucional del acudiente.");
                            }
                            $rolAcudienteId = $rolResult['id'];

                            // Crear asignación de acudiente
                            $datosAsignacion = array(
                                "matricula_id" => $matriculaId,
                                "roles_institucionales_id" => $rolAcudienteId,
                                "parentesco" => $acudiente["parentesco"],
                                "es_firmante_principal" => $acudiente["es_firmante_principal"],
                                "autorizado_recoger" => $acudiente["autorizado_recoger"],
                                "observaciones" => isset($acudiente["observacion"]) ? $acudiente["observacion"] : ""
                            );
                            if (ModeloMatricula::mdlCrearAsignacionAcudiente("asignacion_acudiente", $datosAsignacion) != "ok") {
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
        // Solo verificar permisos cuando se envía el formulario
        if (isset($_POST["idMatricula"]) && !empty($_POST["idMatricula"])) {
            if (!ControladorAuth::ctrVerificarPermiso('matricula_editar') && !ControladorAuth::ctrEsAdministradorSistema()) {
                echo '<script>Swal.fire({ icon: "error", title: "Sin permisos", text: "No tiene permisos para actualizar matrículas."});</script>';
                return;
            }
        }
        if (isset($_POST["idMatricula"]) && !empty($_POST["idMatricula"])) {

            if (empty($_POST["editarGrupoMatricula"]) || empty($_POST["acudientesData"])) {
                echo '<script>Swal.fire("¡Campos Incompletos!", "Debe seleccionar un grupo y asignar al menos un acudiente.", "warning");</script>';
                return;
            }

            $conexion = null;
            try {
                $conexion = Conexion::conectar();
                $conexion->beginTransaction();

                // Obtener sede_jornada_id del grupo seleccionado
                $stmt = $conexion->prepare("SELECT oa.sede_jornada_id FROM grupo g INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id WHERE g.id = :grupo_id");
                $stmt->bindParam(":grupo_id", $_POST["editarGrupoMatricula"], PDO::PARAM_INT);
                $stmt->execute();
                $grupoInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                $datosMatricula = array(
                    "id" => $_POST["idMatricula"],
                    "grupo_id" => $_POST["editarGrupoMatricula"],
                    "sede_jornada_id" => $grupoInfo['sede_jornada_id'],
                    "numero_matricula" => $_POST["editarNumeroMatricula"],
                    "fecha_matricula" => $_POST["editarFechaMatricula"],
                    "nuevo" => $_POST["editarEstudianteNuevo"],
                    "repitente" => $_POST["editarEsRepitente"],
                    "estado_matricula" => $_POST["editarEstadoMatricula"]
                );
                if (ModeloMatricula::mdlEditarMatricula("matricula", $datosMatricula) !== "ok") {
                    throw new Exception("Error al actualizar los datos de la matrícula.");
                }

                $infoMatricula = self::ctrMostrarMatricula("id", $_POST["idMatricula"]);
                if (!$infoMatricula || !isset($infoMatricula["roles_institucionales_id"])) {
                    throw new Exception("No se pudo encontrar la información del rol institucional asociado.");
                }

                // Actualizar fecha de inicio en rol institucional
                $datosRol = array(
                    "id" => $infoMatricula["roles_institucionales_id"],
                    "fecha_inicio" => $_POST["editarFechaIngreso"]
                );
                if (ModeloMatricula::mdlEditarRolInstitucional("roles_institucionales", $datosRol) !== "ok") {
                    throw new Exception("Error al actualizar los datos del rol institucional.");
                }

                $matriculaId = $_POST["idMatricula"];
                ModeloMatricula::mdlEliminarAcudientesPorMatricula("asignacion_acudiente", $matriculaId);

                // Obtener sede_id para los acudientes
                $stmt = $conexion->prepare("SELECT sede_id FROM sede_jornada WHERE id = :sede_jornada_id");
                $stmt->bindParam(":sede_jornada_id", $grupoInfo['sede_jornada_id'], PDO::PARAM_INT);
                $stmt->execute();
                $sedeInfo = $stmt->fetch(PDO::FETCH_ASSOC);

                $acudientes = json_decode($_POST["acudientesData"], true);
                if (is_array($acudientes) && !empty($acudientes)) {
                    foreach ($acudientes as $acudiente) {
                        // Buscar o crear rol institucional de acudiente
                        $stmt = $conexion->prepare("
                            INSERT INTO roles_institucionales (usuario_id, rol_id, sede_id, fecha_inicio, estado) 
                            SELECT :usuario_id, r.id_rol, :sede_id, :fecha_inicio, 'Activo'
                            FROM roles r 
                            WHERE r.nombre_rol = 'Acudiente'
                            AND NOT EXISTS (
                                SELECT 1 FROM roles_institucionales ri 
                                WHERE ri.usuario_id = :usuario_id2 
                                AND ri.rol_id = r.id_rol 
                                AND ri.sede_id = :sede_id2 
                                AND ri.estado = 'Activo'
                            )
                        ");
                        $stmt->bindParam(":usuario_id", $acudiente["id_usuario"], PDO::PARAM_INT);
                        $stmt->bindParam(":usuario_id2", $acudiente["id_usuario"], PDO::PARAM_INT);
                        $stmt->bindParam(":sede_id", $sedeInfo['sede_id'], PDO::PARAM_INT);
                        $stmt->bindParam(":sede_id2", $sedeInfo['sede_id'], PDO::PARAM_INT);
                        $stmt->bindParam(":fecha_inicio", $_POST["editarFechaMatricula"], PDO::PARAM_STR);
                        $stmt->execute();

                        // Obtener el ID del rol institucional
                        $stmt = $conexion->prepare("SELECT id FROM roles_institucionales WHERE usuario_id = :usuario_id AND rol_id = (SELECT id_rol FROM roles WHERE nombre_rol = 'Acudiente') AND sede_id = :sede_id AND estado = 'Activo'");
                        $stmt->bindParam(":usuario_id", $acudiente["id_usuario"], PDO::PARAM_INT);
                        $stmt->bindParam(":sede_id", $sedeInfo['sede_id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $rolResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$rolResult) {
                            throw new Exception("No se pudo obtener el rol institucional del acudiente.");
                        }
                        $rolAcudienteId = $rolResult['id'];

                        $datosAsignacion = array(
                            "matricula_id" => $matriculaId,
                            "roles_institucionales_id" => $rolAcudienteId,
                            "parentesco" => $acudiente["parentesco"],
                            "es_firmante_principal" => $acudiente["es_firmante_principal"],
                            "autorizado_recoger" => $acudiente["autorizado_recoger"],
                            "observaciones" => isset($acudiente["observacion"]) ? $acudiente["observacion"] : ""
                        );
                        if (ModeloMatricula::mdlCrearAsignacionAcudiente("asignacion_acudiente", $datosAsignacion) !== "ok") {
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
        // Solo verificar permisos cuando se intenta eliminar
        if(isset($_GET["idMatricula"])) {
            if (!ControladorAuth::ctrVerificarPermiso('matricula_eliminar') && !ControladorAuth::ctrEsAdministradorSistema()) {
                echo '<script>Swal.fire({ icon: "error", title: "Sin permisos", text: "No tiene permisos para eliminar matrículas."});</script>';
                return;
            }
        }
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