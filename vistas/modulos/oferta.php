<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sistema de Educacion Wissen</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.all.js"></script>
</head>

<?php

require_once "controladores/oferta.controlador.php";
require_once "modelos/oferta.modelo.php";

?>

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
  <div class="wrapper">
    <div class="content-wrapper">
      <section class="content-header">
        <h1>Oferta Educativa</h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
          <li class="active">Oferta Educativa</li>
        </ol>
      </section>

      <!-- CONTENIDO PAGINA OFERTA EDUCATIVA-->

      <section class="content">

        <!-- BOTON NUEVA OFERTA EDUCATIVA -->

        <div class="box">
          <div class="box-header with-border">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarOfertaEducativa">
              <i class="fa fa-graduation-cap"></i> Nueva oferta educativa
            </button>
          </div>

          <!-- CABECERA INFORMACION OFERTA EDUCATIVA -->

          <div class="box-body">
            <div class="alert alert-info" style="margin-bottom: 15px;">
              <strong>Leyenda:</strong> 
              <i class="fa fa-users text-success"></i> = Grupo con estudiantes matriculados (no se puede eliminar)
            </div>
            <table class="table table-bordered table-striped dt-responsive tablas" id="tablaOfertaEducativa">
              <thead>
                <tr>
                  <th style="width: 8%">Año Lectivo</th>
                  <th style="width: 15%">Sede</th>
                  <th style="width: 12%">Jornada</th>
                  <th style="width: 15%">Nivel Educativo</th>
                  <th style="width: 15%">Grado</th>
                  <th style="width: 25%">Grupos</th>
                  <th style="width: 10%">Acciones</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $datosAgrupados = ControladorOfertaEducativa::ctrProcesarDatosParaVista();

                if(!empty($datosAgrupados)) {
                    $anioAnterior = '';
                    $sedeAnterior = '';
                    $jornadaAnterior = '';
                    $nivelAnterior = '';
                    $gradoAnterior = '';

                    foreach ($datosAgrupados as $key => $value) {

                        // Calcular si mostrar datos básicos
                        $mostrarAnio = ($anioAnterior != $value["anio"]);
                        $mostrarSede = ($sedeAnterior != $value["nombre_sede"] || $mostrarAnio);
                        $mostrarJornada = ($jornadaAnterior != $value["nombre_jornada"] || $mostrarSede);
                        $mostrarNivel = ($nivelAnterior != $value["nombre_nivel"] || $mostrarJornada);
                        $mostrarGrado = ($gradoAnterior != $value["nombre_grado"] || $mostrarNivel);

                        // Procesar grupos
                        if(!empty($value["grupos"])) {
                            foreach($value["grupos"] as $index => $grupo) {
                                echo '<tr>';

                                // Año Lectivo
                                if($mostrarAnio && $index == 0) {
                                    $contadorAnio = 0;
                                    foreach($datosAgrupados as $contador) {
                                        if($contador["anio"] == $value["anio"]) {
                                            $contadorAnio += count($contador["grupos"]);
                                        }
                                    }
                                    echo '<td rowspan="'.$contadorAnio.'" style="vertical-align: middle; text-align: center;">'.$value["anio"].'</td>';
                                }

                                // Sede
                                if($mostrarSede && $index == 0) {
                                    $contadorSede = 0;
                                    foreach($datosAgrupados as $contador) {
                                        if($contador["anio"] == $value["anio"] && $contador["nombre_sede"] == $value["nombre_sede"]) {
                                            $contadorSede += count($contador["grupos"]);
                                        }
                                    }
                                    echo '<td rowspan="'.$contadorSede.'" style="vertical-align: middle;">'.$value["nombre_sede"].'</td>';
                                }

                                // Jornada
                                if($mostrarJornada && $index == 0) {
                                    $contadorJornada = 0;
                                    foreach($datosAgrupados as $contador) {
                                        if($contador["anio"] == $value["anio"] &&
                                            $contador["nombre_sede"] == $value["nombre_sede"] &&
                                            $contador["nombre_jornada"] == $value["nombre_jornada"]) {
                                            $contadorJornada += count($contador["grupos"]);
                                        }
                                    }
                                    echo '<td rowspan="'.$contadorJornada.'" style="vertical-align: middle; text-align: center;">'.$value["nombre_jornada"].'</td>';
                                }

                                // Nivel
                                if($mostrarNivel && $index == 0) {
                                    $contadorNivel = 0;
                                    foreach($datosAgrupados as $contador) {
                                        if($contador["anio"] == $value["anio"] &&
                                            $contador["nombre_sede"] == $value["nombre_sede"] &&
                                            $contador["nombre_jornada"] == $value["nombre_jornada"] &&
                                            $contador["nombre_nivel"] == $value["nombre_nivel"]) {
                                            $contadorNivel += count($contador["grupos"]);
                                        }
                                    }
                                    echo '<td rowspan="'.$contadorNivel.'" style="vertical-align: middle;">'.$value["nombre_nivel"].'</td>';
                                }

                                // Grado
                                if($mostrarGrado && $index == 0) {
                                    $contadorGrupos = count($value["grupos"]);
                                    echo '<td rowspan="'.$contadorGrupos.'" style="vertical-align: middle; text-align: center;">'.$value["nombre_grado"].'</td>';
                                }

                                // Grupo individual con indicador de estudiantes
                                $tieneEstudiantes = ModeloOfertaEducativa::mdlVerificarEstudiantesEnGrupo($grupo["id"]);
                                $indicador = $tieneEstudiantes ? '<i class="fa fa-users text-success" title="Tiene estudiantes matriculados"></i> ' : '';
                                echo '<td>'.$indicador.$grupo["nombre"].'</td>';

                                // Acciones por grupo
                                echo '<td style="text-align: center; white-space: nowrap;">
                                    <button class="btn btn-info btn-sm btnVerGrupo" style="margin-right: 2px;" 
                                    data-grupo-id="'.$grupo["id"].'" 
                                    data-oferta-id="'.$value["id"].'"
                                    data-toggle="tooltip" title="Ver">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm btnEditarGrupo" style="margin-right: 2px;" 
                                    data-grupo-id="'.$grupo["id"].'" 
                                    data-oferta-id="'.$value["id"].'"
                                    data-toggle="tooltip" title="Editar">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btnEliminarGrupo" 
                                    data-grupo-id="'.$grupo["id"].'" 
                                    data-grupo-nombre="'.$grupo["nombre"].'"
                                    data-toggle="tooltip" title="Eliminar">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>';

                                echo '</tr>';
                            }
                        }

                        // Actualizar variables de control
                        if($mostrarAnio) $anioAnterior = $value["anio"];
                        if($mostrarSede) $sedeAnterior = $value["nombre_sede"];
                        if($mostrarJornada) $jornadaAnterior = $value["nombre_jornada"];
                        if($mostrarNivel) $nivelAnterior = $value["nombre_nivel"];
                        if($mostrarGrado) $gradoAnterior = $value["nombre_grado"];
                    }
                }
                ?>

              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>

    <!-- =======================================
      MODAL AGREGAR OFERTA EDUCATIVA
    =======================================-->

    <div id="modalAgregarOfertaEducativa" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarOfertaEducativa">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><i class="fa fa-graduation-cap"></i>  Agregar Oferta Educativa</h4>
            </div>

            <div class="modal-body">
              <div class="box-body" id="camposFormulario">

                <!-- PRIMERA FILA: AÑO LECTIVO Y SEDE -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Año Lectivo:</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <select class="form-control input-lg" name="anioLectivo" id="anioLectivo" required>
                          <option value="">Seleccione un Año Lectivo...</option>
                          <?php
                          $aniosLectivos = ControladorOfertaEducativa::ctrObtenerAniosLectivos();
                          foreach ($aniosLectivos as $key => $value) {
                              echo '<option value="'.$value["id"].'">'.$value["anio"].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Sede:</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                        <select class="form-control input-lg" name="sedeOferta" id="sedeOferta" required>
                          <option value="">Seleccione una Sede...</option>
                          <?php
                          $sedes = ControladorOfertaEducativa::ctrObtenerSedes();
                          foreach ($sedes as $key => $value) {
                              echo '<option value="'.$value["id"].'">'.$value["nombre_sede"].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- SEGUNDA FILA: JORNADA Y NIVEL EDUCATIVO -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Jornada:</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        <select class="form-control input-lg" name="jornadaOferta" id="jornadaOferta" required>
                          <option value="">Seleccione una Jornada...</option>
                          <?php
                          $jornadas = ControladorOfertaEducativa::ctrObtenerJornadas();
                          foreach ($jornadas as $key => $value) {
                              echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nivel Educativo:</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-graduation-cap"></i></span>
                        <select class="form-control input-lg" name="nivelEducativo" id="nivelEducativo" required>
                          <option value="">Seleccione un Nivel Educativo...</option>
                          <?php
                          $nivelesEducativos = ControladorOfertaEducativa::ctrObtenerNivelesEducativos();
                          foreach ($nivelesEducativos as $key => $value) {
                              echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- TERCERA FILA: GRADOS Y GRUPOS -->
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Grados, Cursos y Grupos:</label>
                      <div id="contenedorGrados" style="max-height: 400px; overflow-y: auto;">
                        <p class="text-muted">Seleccione un nivel educativo para ver los grados disponibles</p>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
              <button type="submit" class="btn btn-primary">Registrar</button>
            </div>

            <?php
            $crearOfertaEducativa = new ControladorOfertaEducativa();
            $crearOfertaEducativa -> ctrCrearOfertaEducativa();
            ?>

          </form>
        </div>
      </div>
    </div>

    <!-- =======================================
      MODAL EDITAR GRUPO (CORREGIDO)
    =======================================-->

    <div id="modalEditarGrupo" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form role="form" method="post" enctype="multipart/form-data" id="formEditarGrupo">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><i class="fa fa-pencil"></i> Editar Grupo</h4>
            </div>
            <div class="modal-body">
              <div class="box-body">

                <!-- CAMPOS OCULTOS -->
                <input type="hidden" name="idGrupo" id="idGrupo">
                <input type="hidden" name="ofertaEducativaId" id="ofertaEducativaId">
                <input type="hidden" name="editarTipoGrupo" id="editarTipoGrupo">

                <!-- INFORMACIÓN DE CONTEXTO (SOLO LECTURA) -->
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Año Lectivo:</label>
                      <input type="text" class="form-control input-lg" id="editarGrupoAnio" readonly>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Sede:</label>
                      <input type="text" class="form-control input-lg" id="editarGrupoSede" readonly>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Jornada:</label>
                      <input type="text" class="form-control input-lg" id="editarGrupoJornada" readonly>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nivel Educativo:</label>
                      <input type="text" class="form-control input-lg" id="editarGrupoNivel" readonly>
                    </div>
                  </div>
                </div>

                <hr>

                <!-- CAMPOS EDITABLES -->
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Grado:</label>
                          <input type="text" class="form-control input-lg" id="editarGrupoGrado" readonly>
                      </div>
                  </div>
                  <div class="col-md-6" id="contenedorEditarCurso">
                      <div class="form-group">
                          <label>Curso:</label>
                          <input type="text" class="form-control input-lg" id="editarCursoGrupo" name="editarCursoGrupo" readonly>
                      </div>
                  </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cupos:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                                <input type="number" class="form-control input-lg" name="editarCuposGrupo" id="editarCuposGrupo" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre del Grupo:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                <input type="text" class="form-control input-lg" name="editarNombreGrupo" id="editarNombreGrupo" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN PARA ASOCIAR A GRUPO MULTIGRADO -->
                <div id="seccionAsociarMultigrado" style="display: none;">
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="grupoMultigrado" id="grupoMultigrado"> Asociar a Grupo Multigrado
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="contenedorGrupoPadre" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Grupos Padre:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-sitemap"></i></span>
                                    <select class="form-control input-lg" name="editarGrupoPadre" id="editarGrupoPadre">
                                        <option value="">Seleccione un Grupo Padre...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>

            <?php
            $editarGrupo = new ControladorOfertaEducativa();
            $editarGrupo -> ctrEditarGrupo();
            ?>
          </form>
        </div>
      </div>
    </div>

    <!-- =======================================
      MODAL VER GRUPO
    =======================================-->

    <div id="modalVerGrupo" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background: #3c8ebdff; color: white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-search"></i> Ver Grupo</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <table style="border: none; width: 100%;">
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold; width: 40%;">Año Lectivo:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoAnio"></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold;">Sede:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoSede"></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold;">Jornada:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoJornada"></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold;">Nivel Educativo:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoNivel"></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold;">Grado:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoGrado"></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold;">Curso:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoCurso"></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold;">Cupos:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoCupos"></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none; padding: 10px; font-weight: bold;">Nombre del Grupo:</td>
                    <td style="border: none; padding: 10px;" id="verGrupoNombre"></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btnEditarGrupoDesdeVer">
              <i class="fa fa-edit"></i> Editar
            </button>
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

<script src="vistas/js/ofertaeducativa.js"></script>

<?php

$borrarOfertaEducativa = new ControladorOfertaEducativa();
$borrarOfertaEducativa -> ctrBorrarOfertaEducativa();

$borrarGrupo = new ControladorOfertaEducativa();
$borrarGrupo -> ctrBorrarGrupo();

?>