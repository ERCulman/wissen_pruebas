<?php

class ControladorOfertaEducativa {

    /*=============================================
    MOSTRAR OFERTA EDUCATIVA CON GRUPOS
    =============================================*/

    static public function ctrMostrarOfertaEducativaConGrupos($item, $valor) {
        if (!BackendProtector::protectController('oferta_ver')) {
            return false;
        }
        $tabla = "oferta_academica";
        $respuesta = ModeloOfertaEducativa::mdlMostrarOfertaEducativaConGrupos($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR OFERTA EDUCATIVA (MÉTODO ORIGINAL)
    =============================================*/

    static public function ctrMostrarOfertaEducativa($item, $valor) {
        if (!BackendProtector::protectController('oferta_ver')) {
            return false;
        }
        $tabla = "oferta_academica";
        $respuesta = ModeloOfertaEducativa::mdlMostrarOfertaEducativa($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    VALIDAR ACCESO AL MÓDULO
    =============================================*/

    static public function ctrValidarAcceso() {
        if (!isset($_SESSION["iniciarSesion"]) || $_SESSION["iniciarSesion"] != "ok") {
            return ["acceso" => false, "esAdmin" => false];
        }

        $auth = ServicioAutorizacion::getInstance();

        if ($auth->esRolAdmin()) {
            return ["acceso" => true, "esAdmin" => true];
        }

        if ($auth->tieneAlcanceInstitucional()) {
            return ["acceso" => true, "esAdmin" => false];
        }

        return ["acceso" => false, "esAdmin" => false];
    }

    /*=============================================
    CREAR OFERTA EDUCATIVA CON GRUPOS
    =============================================*/

    public function ctrCrearOfertaEducativa() {

        if (!BackendProtector::protectController('oferta_crear')) {
            return;
        }

        if(isset($_POST["anioLectivo"])) {

            if(!empty($_POST["anioLectivo"]) &&
                !empty($_POST["sedeOferta"]) &&
                !empty($_POST["jornadaOferta"]) &&
                !empty($_POST["nivelEducativo"]) &&
                isset($_POST["grados"]) &&
                is_array($_POST["grados"]) &&
                count($_POST["grados"]) > 0) {

                $datosSedeJornada = array(
                    "sede_id" => $_POST["sedeOferta"],
                    "jornada_id" => $_POST["jornadaOferta"],
                    "anio_lectivo_id" => $_POST["anioLectivo"]
                );

                $sedeJornadaId = ModeloOfertaEducativa::mdlVerificarCrearSedeJornada("sede_jornada", $datosSedeJornada);

                if($sedeJornadaId === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error al crear sede-jornada",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            window.location = "oferta";
                        });
                    </script>';
                    return;
                }

                $ofertasCreadas = 0;
                $gruposCreados = 0;
                $errores = 0;

                foreach($_POST["grados"] as $gradoId) {

                    // Verificar si ya existe la oferta educativa
                    $ofertaExistente = ModeloOfertaEducativa::mdlVerificarDuplicadoOferta("oferta_academica", $gradoId, $sedeJornadaId, $_POST["anioLectivo"]);

                    $ofertaEducativaId = null;

                    if($ofertaExistente) {
                        $ofertaEducativaId = $ofertaExistente;
                    } else {
                        // Crear nueva oferta educativa
                        $datosOferta = array(
                            "grado_id" => $gradoId,
                            "sede_jornada_id" => $sedeJornadaId,
                            "anio_lectivo_id" => $_POST["anioLectivo"]
                        );

                        $ofertaEducativaId = ModeloOfertaEducativa::mdlIngresarOfertaEducativa("oferta_academica", $datosOferta);

                        if($ofertaEducativaId) {
                            $ofertasCreadas++;
                        } else {
                            $errores++;
                            continue;
                        }
                    }

                    // =========================================================================
                    // INICIO DE LA LÓGICA CORREGIDA
                    // =========================================================================

                    // Procesar grupos para este grado
                    // Verificamos si se marcó la casilla "multigrado" para este grado
                    if (isset($_POST["multigrado_" . $gradoId]) && $_POST["multigrado_" . $gradoId] == '1') {

                        // Recogemos los datos de los campos de multigrado
                        $nombreManual = $_POST["nombre_manual_" . $gradoId] ?? '';
                        $cuposMultigrado = $_POST["cupos_multigrado_" . $gradoId] ?? 0;

                        if (!empty($nombreManual) && $cuposMultigrado > 0) {
                            // Preparamos los datos para enviar al modelo
                            $datosGrupo = array(
                                "oferta_educativa_id" => $ofertaEducativaId,
                                "curso_id"          => null, // El curso es nulo
                                "nombre"            => $nombreManual,
                                "cupos"             => $cuposMultigrado,
                                "tipo"              => "Multigrado", // Campo nuevo para identificarlo
                                "grupo_padre_id"    => null      // El grupo padre es nulo
                            );

                            $respuestaGrupo = ModeloOfertaEducativa::mdlIngresarGrupo("grupo", $datosGrupo);

                            if ($respuestaGrupo == "ok") {
                                $gruposCreados++;
                            } else {
                                $errores++;
                            }
                        }

                    } else { // Si no es multigrado, ejecuta la lógica original

                        $cursosKey = "cursos_" . $gradoId;

                        if(isset($_POST[$cursosKey]) && is_array($_POST[$cursosKey])) {

                            foreach($_POST[$cursosKey] as $cursoId) {

                                $cuposKey = "cupos_" . $gradoId . "_" . $cursoId;
                                $cupos = isset($_POST[$cuposKey]) ? intval($_POST[$cuposKey]) : 0;

                                if($cupos > 0) {
                                    // Usar conexión directa para obtener información
                                    $conexion = Conexion::conectar();

                                    // Obtener información del grado
                                    $stmt = $conexion->prepare("SELECT nombre FROM grado WHERE id = :id");
                                    $stmt->bindParam(":id", $gradoId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $gradoInfo = $stmt->fetch();

                                    // Obtener información del curso
                                    $stmt = $conexion->prepare("SELECT nombre FROM curso WHERE id = :id");
                                    $stmt->bindParam(":id", $cursoId, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $cursoInfo = $stmt->fetch();

                                    if($gradoInfo && $cursoInfo) {
                                        $nombreGrupo = $gradoInfo["nombre"] . " " . $cursoInfo["nombre"] . " - " . $cupos . " Cupos";

                                        // Se añaden los nuevos campos para mantener consistencia
                                        $datosGrupo = array(
                                            "oferta_educativa_id" => $ofertaEducativaId,
                                            "curso_id"          => $cursoId,
                                            "nombre"            => $nombreGrupo,
                                            "cupos"             => $cupos,
                                            "tipo"              => "Regular",
                                            "grupo_padre_id"    => null
                                        );

                                        $respuestaGrupo = ModeloOfertaEducativa::mdlIngresarGrupo("grupo", $datosGrupo);

                                        if($respuestaGrupo == "ok") {
                                            $gruposCreados++;
                                        } else {
                                            $errores++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // =========================================================================
                    // FIN DE LA LÓGICA CORREGIDA
                    // =========================================================================
                }

                if($gruposCreados > 0) {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Correcto",
                            text: "Oferta educativa guardada correctamente. Grupos creados: ' . $gruposCreados . '",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            window.location = "oferta";
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "warning",
                            title: "Atención",
                            text: "No se crearon grupos. Verifique los datos ingresados.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            window.location = "oferta";
                        });
                    </script>';
                }

            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Todos los campos son requeridos",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        window.location = "oferta";
                    });
                </script>';
            }
        }
    }

    /*=============================================
    OBTENER AÑOS LECTIVOS
    =============================================*/

    static public function ctrObtenerAniosLectivos() {
        $respuesta = ModeloOfertaEducativa::mdlObtenerAniosLectivos();
        return $respuesta;
    }

    /*=============================================
    OBTENER SEDES CON VALIDACIÓN DE ACCESO
    =============================================*/

    static public function ctrObtenerSedes() {
        if (!BackendProtector::protectController('oferta_ver')) {
            return array();
        }
        $respuesta = ModeloOfertaEducativa::mdlObtenerSedes();
        return $respuesta;
    }

    /*=============================================
    OBTENER GRUPOS MULTIGRADO
    =============================================*/

    static public function ctrObtenerGruposMultigrado() {
        // Log para debug
        error_log("ctrObtenerGruposMultigrado - Iniciando");
        
        if (!BackendProtector::protectController('oferta_ver')) {
            error_log("ctrObtenerGruposMultigrado - Sin permisos");
            return array();
        }
        
        $respuesta = ModeloOfertaEducativa::mdlObtenerGruposMultigrado();
        error_log("ctrObtenerGruposMultigrado - Respuesta: " . json_encode($respuesta));
        
        return $respuesta;
    }

    /*=============================================
    OBTENER JORNADAS
    =============================================*/

    static public function ctrObtenerJornadas() {
        $respuesta = ModeloOfertaEducativa::mdlObtenerJornadas();
        return $respuesta;
    }

    /*=============================================
    OBTENER NIVELES EDUCATIVOS
    =============================================*/

    static public function ctrObtenerNivelesEducativos() {
        $respuesta = ModeloOfertaEducativa::mdlObtenerNivelesEducativos();
        return $respuesta;
    }

    /*=============================================
    OBTENER CURSOS
    =============================================*/

    static public function ctrObtenerCursos() {
        $respuesta = ModeloOfertaEducativa::mdlObtenerCursos();
        return $respuesta;
    }

    /*=============================================
    OBTENER GRADOS POR NIVEL
    =============================================*/

    static public function ctrObtenerGradosPorNivel($nivelId) {
        $respuesta = ModeloOfertaEducativa::mdlObtenerGradosPorNivel($nivelId);
        return $respuesta;
    }

    /*=============================================
    OBTENER GRADOS OCUPADOS
    =============================================*/

    static public function ctrObtenerGradosOcupados($sedeId, $jornadaId, $anioLectivoId) {
        $respuesta = ModeloOfertaEducativa::mdlObtenerGradosOcupados($sedeId, $jornadaId, $anioLectivoId);
        return $respuesta;
    }

    /*=============================================
    OBTENER CURSOS OCUPADOS POR GRADO
    =============================================*/

    static public function ctrObtenerCursosOcupados($gradoId, $sedeJornadaId, $anioLectivoId) {
        $respuesta = ModeloOfertaEducativa::mdlObtenerCursosOcupados($gradoId, $sedeJornadaId, $anioLectivoId);
        return $respuesta;
    }

    /*=============================================
    EDITAR OFERTA EDUCATIVA
    =============================================*/

    static public function ctrEditarOfertaEducativa() {

        if (!BackendProtector::protectController('oferta_editar')) {
            return;
        }

        if(isset($_POST["editarAnioLectivo"])) {

            if(!empty($_POST["editarAnioLectivo"]) &&
                !empty($_POST["editarSedeOferta"]) &&
                !empty($_POST["editarJornadaOferta"]) &&
                !empty($_POST["editarNivelEducativo"]) &&
                !empty($_POST["editarGradoOferta"])) {

                $datosSedeJornada = array(
                    "sede_id" => $_POST["editarSedeOferta"],
                    "jornada_id" => $_POST["editarJornadaOferta"],
                    "anio_lectivo_id" => $_POST["editarAnioLectivo"]
                );

                $sedeJornadaId = ModeloOfertaEducativa::mdlVerificarCrearSedeJornada("sede_jornada", $datosSedeJornada);

                if($sedeJornadaId === false) {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error al verificar sede-jornada",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            window.location = "oferta";
                        });
                    </script>';
                    return;
                }

                $tabla = "oferta_academica";
                $datos = array(
                    "id" => $_POST["idOferta"],
                    "grado_id" => $_POST["editarGradoOferta"],
                    "sede_jornada_id" => $sedeJornadaId,
                    "anio_lectivo_id" => $_POST["editarAnioLectivo"]
                );

                $respuesta = ModeloOfertaEducativa::mdlEditarOfertaEducativa($tabla, $datos);

                if($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Correcto",
                            text: "Oferta educativa editada correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            window.location = "oferta";
                        });
                    </script>';
                }

            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Todos los campos son requeridos",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        window.location = "oferta";
                    });
                </script>';
            }
        }
    }

    /*=============================================
    EDITAR GRUPO
    =============================================*/

    static public function ctrEditarGrupo() {

        if (!BackendProtector::protectController('oferta_editar')) {
            return;
        }

        if(isset($_POST["idGrupo"])) {

            if(empty($_POST["editarNombreGrupo"]) || !isset($_POST["editarCuposGrupo"]) || empty($_POST["editarTipoGrupo"])) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Faltan datos requeridos para editar el grupo.",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        window.location = "oferta";
                    });
                </script>';
                return;
            }

            $datos = array(
                "id" => $_POST["idGrupo"],
                "nombre" => $_POST["editarNombreGrupo"],
                "cupos" => $_POST["editarCuposGrupo"],
                "tipo" => $_POST["editarTipoGrupo"],
                "grupo_padre_id" => null
            );

            if ($datos["tipo"] == 'Regular') {
                if (isset($_POST["grupoMultigrado"]) && !empty($_POST["editarGrupoPadre"])) {
                    $datos["grupo_padre_id"] = $_POST["editarGrupoPadre"];
                } else {
                    $datos["grupo_padre_id"] = null;
                    $datos["tipo"] = 'Regular';
                }
            }

            $tabla = "grupo";
            $respuesta = ModeloOfertaEducativa::mdlEditarGrupo($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Correcto",
                        text: "El grupo ha sido actualizado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        if (result.value) {
                            window.location = "oferta";
                        }
                    });
                </script>';
            } else {
                 echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo actualizar el grupo. Verifique los logs.",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        window.location = "oferta";
                    });
                </script>';
            }
        }
    }

    /*=============================================
    BORRAR OFERTA EDUCATIVA
    =============================================*/

    static public function ctrBorrarOfertaEducativa() {

        if (!BackendProtector::protectController('oferta_eliminar')) {
            return;
        }

        if(isset($_GET["idOferta"])) {

            $tabla = "oferta_academica";
            $datos = $_GET["idOferta"];

            $respuesta = ModeloOfertaEducativa::mdlBorrarOfertaEducativa($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Correcto",
                        text: "Oferta educativa borrada correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result) {
                        window.location = "oferta";
                    });
                </script>';
            }
        }
    }

    /*=============================================
    PROCESAR DATOS PARA VISTA CON ROWSPAN
    =============================================*/

    static public function ctrProcesarDatosParaVista() {
        if (!BackendProtector::protectController('oferta_ver')) {
            return array();
        }

        $datos = self::ctrMostrarOfertaEducativaConGrupos(null, null);

        if(empty($datos)) {
            return array();
        }

        $resultado = array();
        $agrupacion = array();

        foreach($datos as $fila) {
            $key = $fila["anio"] . "|" . $fila["nombre_sede"] . "|" . $fila["nombre_jornada"] . "|" . $fila["nombre_nivel"] . "|" . $fila["nombre_grado"];

            if(!isset($agrupacion[$key])) {
                $agrupacion[$key] = array(
                    "id" => $fila["id"],
                    "anio" => $fila["anio"],
                    "nombre_sede" => $fila["nombre_sede"],
                    "nombre_jornada" => $fila["nombre_jornada"],
                    "nombre_nivel" => $fila["nombre_nivel"],
                    "nombre_grado" => $fila["nombre_grado"],
                    "grupos" => array()
                );
            }

            if($fila["grupo_id"] && $fila["nombre_grupo"]) {
                $agrupacion[$key]["grupos"][] = array(
                    "id" => $fila["grupo_id"],
                    "nombre" => $fila["nombre_grupo"],
                    "cupos" => $fila["cupos"],
                    "curso" => $fila["nombre_curso"],
                    "tipo" => $fila["tipo"] ?? "Regular",
                    "grupo_padre_id" => $fila["grupo_padre_id"]
                );
            }
        }

        // Convertir a array indexado y calcular rowspans
        $resultado = array_values($agrupacion);

        // Calcular rowspans para la vista
        $filasProcesadas = array();
        foreach($resultado as $index => $item) {
            $cantidadGrupos = count($item["grupos"]);
            $item["rowspan"] = $cantidadGrupos > 0 ? $cantidadGrupos : 1;
            $filasProcesadas[] = $item;
        }

        return $filasProcesadas;
    }

    /*=============================================
    MOSTRAR OFERTA POR USUARIO
    =============================================*/

    static public function ctrMostrarOfertaPorUsuario($idUsuario) {
        $respuesta = ModeloOfertaEducativa::mdlMostrarOfertaPorInstitucion($idUsuario);
        return $respuesta;
    }

    /*=============================================
    BORRAR GRUPO
    =============================================*/

    static public function ctrBorrarGrupo() {

        if(isset($_GET["idGrupo"])) {

            $grupoId = $_GET["idGrupo"];

            // Verificar si el grupo tiene estudiantes matriculados
            $tieneEstudiantes = ModeloOfertaEducativa::mdlVerificarEstudiantesEnGrupo($grupoId);

            if($tieneEstudiantes) {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "No se puede eliminar",
                    text: "Este grupo tiene estudiantes matriculados. No es posible eliminarlo.",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    window.location = "oferta";
                });
                </script>';
                return;
            }

            // Verificar si es el último grupo y tiene referencias
            $stmt = Conexion::conectar()->prepare("SELECT oferta_educativa_id FROM grupo WHERE id = :id");
            $stmt->bindParam(":id", $grupoId, PDO::PARAM_INT);
            $stmt->execute();
            $grupoInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            $totalGrupos = 1;
            $esUltimoGrupo = false;

            if($grupoInfo) {
                $ofertaId = $grupoInfo['oferta_educativa_id'];
                $totalGrupos = ModeloOfertaEducativa::mdlContarGruposEnOferta($ofertaId);
                $esUltimoGrupo = ($totalGrupos == 1);

                if($esUltimoGrupo) {
                    $referencias = ModeloOfertaEducativa::mdlVerificarReferenciasOferta($ofertaId);

                    if(!empty($referencias)) {
                        $listaReferencias = implode(", ", $referencias);
                        echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "No se puede eliminar",
                            text: "Este es el último grupo del grado y la oferta educativa tiene referencias activas en: ' . $listaReferencias . '. No es posible eliminarlo.",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result) {
                            window.location = "oferta";
                        });
                        </script>';
                        return;
                    }
                }
            }

            $tabla = "grupo";
            $respuesta = ModeloOfertaEducativa::mdlBorrarGrupo($tabla, $grupoId);

            if($respuesta == "ok") {
                $mensaje = "El grupo ha sido eliminado correctamente";
                if($esUltimoGrupo) {
                    $mensaje .= " y la oferta educativa también fue eliminada por no tener más grupos";
                }

                echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Correcto",
                    text: "'.$mensaje.'",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    if (result.value) {
                        window.location = "oferta";
                    }
                });
                </script>';
            } else {
                error_log("Error al eliminar grupo ID: " . $grupoId);
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudo eliminar el grupo. Verifique los logs para más detalles.",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    window.location = "oferta";
                });
                </script>';
            }
        }
    }

}
?>