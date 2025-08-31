<?php

class ControladorOfertaEducativa {

    /*=============================================
    MOSTRAR OFERTA EDUCATIVA CON GRUPOS
    =============================================*/

    static public function ctrMostrarOfertaEducativaConGrupos($item, $valor) {
        $tabla = "oferta_academica";
        $respuesta = ModeloOfertaEducativa::mdlMostrarOfertaEducativaConGrupos($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    MOSTRAR OFERTA EDUCATIVA (MÉTODO ORIGINAL)
    =============================================*/

    static public function ctrMostrarOfertaEducativa($item, $valor) {
        $tabla = "oferta_academica";
        $respuesta = ModeloOfertaEducativa::mdlMostrarOfertaEducativa($tabla, $item, $valor);
        return $respuesta;
    }

    /*=============================================
    CREAR OFERTA EDUCATIVA CON GRUPOS
    =============================================*/

    public function ctrCrearOfertaEducativa() {

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

                    // Procesar grupos para este grado
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

                                    $datosGrupo = array(
                                        "oferta_educativa_id" => $ofertaEducativaId,
                                        "curso_id" => $cursoId,
                                        "nombre" => $nombreGrupo,
                                        "cupos" => $cupos
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
    OBTENER SEDES
    =============================================*/

    static public function ctrObtenerSedes() {
        $respuesta = ModeloOfertaEducativa::mdlObtenerSedes();
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

        if(isset($_POST["idGrupo"])) {

            if(!empty($_POST["idGrupo"]) &&
                !empty($_POST["editarCursoGrupo"]) &&
                !empty($_POST["editarCuposGrupo"]) &&
                !empty($_POST["editarNombreGrupo"])) {

                $tabla = "grupo";
                $datos = array(
                    "id" => $_POST["idGrupo"],
                    "curso_id" => $_POST["editarCursoGrupo"],
                    "cupos" => $_POST["editarCuposGrupo"],
                    "nombre" => $_POST["editarNombreGrupo"]
                );

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
                }

            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Todos los campos son requeridos para editar el grupo",
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
                    "curso" => $fila["nombre_curso"]
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
    BORRAR GRUPO
    =============================================*/

    static public function ctrBorrarGrupo() {

        if(isset($_GET["idGrupo"])) {

            $tabla = "grupo";
            $datos = $_GET["idGrupo"];

            $respuesta = ModeloOfertaEducativa::mdlBorrarGrupo($tabla, $datos);

            if($respuesta == "ok") {
                echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Correcto",
                    text: "El grupo ha sido borrado correctamente",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result) {
                    if (result.value) {
                        window.location = "oferta";
                    }
                });
            </script>';
            }
        }
    }

}
?>