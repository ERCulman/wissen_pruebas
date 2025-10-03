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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.all.js"></script>
</head>

<?php
require_once "controladores/asignacion-academica.controlador.php";
require_once "modelos/asignacion-academica.modelo.php";

$contexto = ControladorAsignacionAcademica::ctrValidarAcceso();

if (!$contexto["acceso"]) {
    echo '<script>window.location = "inicio";</script>';
    return;
}
?>

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
  <div class="wrapper">
    <div class="content-wrapper">
      <section class="content-header">
        <h1>Asignación Académica</h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
          <li class="active">Asignación Académica</li>
        </ol>
      </section>

      <section class="content">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Gestión de Asignaciones Académicas</h3>
          </div>

          <div class="box-body">
            <!-- SELECTOR DE SEDE -->
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Seleccionar Sede:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-building"></i></span>
                    <select class="form-control input-lg" id="selectSede">
                      <option value="">Seleccione una sede...</option>
                      <?php
                      $sedes = ControladorAsignacionAcademica::ctrObtenerSedes();
                      foreach ($sedes as $sede) {
                          echo '<option value="'.$sede["id"].'">'.$sede["nombre_sede"].'</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <!-- CONTENIDO PRINCIPAL -->
            <div id="contenidoPrincipal" style="display: none;">
              <div class="row">
                <!-- COLUMNA IZQUIERDA: DOCENTES (30%) -->
                <div class="col-md-4">
                  <h4>Docentes de la Sede</h4>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaDocentes">
                      <thead>
                        <tr>
                          <th>Docente</th>
                          <th>Horas</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>

                <!-- COLUMNA DERECHA: ASIGNACIONES (70%) -->
                <div class="col-md-8">
                  <!-- CONTROLES SUPERIORES -->
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Grado:</label>
                        <select class="form-control" id="selectGrado">
                          <option value="">Seleccione un grado...</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Grupo:</label>
                        <select class="form-control" id="selectGrupo">
                          <option value="">Seleccione un grupo...</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-success btn-block" id="btnAsignar" style="display: none;">
                          <i class="fa fa-plus"></i> Asignar Seleccionadas
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- ASIGNATURAS DISPONIBLES -->
                  <div id="contenedorAsignaturas" style="display: none;">
                    <h5>Asignaturas Disponibles</h5>
                    <div id="listaAsignaturas" class="row" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                    </div>
                  </div>

                  <!-- ASIGNATURAS ASIGNADAS -->
                  <div id="contenedorAsignadas" style="display: none; margin-top: 20px;">
                    <div class="row">
                      <div class="col-md-6">
                        <h5 id="tituloAsignadas">Asignaturas Asignadas</h5>
                      </div>
                      <div class="col-md-6">
                        <div id="contenedorPeriodos" style="display: none;">
                          <small><strong>Períodos:</strong></small>
                          <div id="listaPeriodos" style="display: inline-block; margin-left: 10px;"></div>
                        </div>
                      </div>
                    </div>
                    <div class="alert alert-info" id="infoHoras" style="display: none;">
                      <strong>Horas asignadas:</strong> <span id="horasAsignadas">0</span> / <span id="horasMaximas">0</span>
                    </div>
                    <table class="table table-bordered table-striped" id="tablaAsignadas">
                      <thead>
                        <tr>
                          <th>Área</th>
                          <th>Asignatura</th>
                          <th>Grupo</th>
                          <th>IHS</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
<!-- MODAL VER ASIGNACIÓN -->
<div class="modal fade" id="modalVerAsignacion" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #3c8ebdff; color: white;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-eye"></i> Ver Asignación Académica</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table style="border: none; width: 100%; font-size: 16px;">
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold; width: 30%;">Grado:</td>
                <td style="border: none; padding: 12px;" id="verGrado"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">Grupo:</td>
                <td style="border: none; padding: 12px;" id="verGrupo"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">Asignatura:</td>
                <td style="border: none; padding: 12px;" id="verAsignatura"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">IHS Asignada:</td>
                <td style="border: none; padding: 12px;" id="verIHS"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">Períodos Asignados:</td>
                <td style="border: none; padding: 12px;" id="verPeriodos"></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL VER MULTIGRADO -->
<div class="modal fade" id="modalVerMultigrado" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #3c8ebdff; color: white;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-eye"></i> Ver Asignación Multigrado</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table style="border: none; width: 100%; font-size: 16px;">
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold; width: 30%;">Asignatura:</td>
                <td style="border: none; padding: 12px;" id="verMultigradoAsignatura"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">Grados:</td>
                <td style="border: none; padding: 12px;" id="verMultigradoGrados"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">Grupos:</td>
                <td style="border: none; padding: 12px;" id="verMultigradoGrupos"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">IHS:</td>
                <td style="border: none; padding: 12px;" id="verMultigradoIHS"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 12px; font-weight: bold;">Períodos Asignados:</td>
                <td style="border: none; padding: 12px;" id="verMultigradoPeriodos"></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL EDITAR MULTIGRADO -->
<div class="modal fade" id="modalEditarMultigrado" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #3c8ebdff; color: white;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-edit"></i> Editar Asignación Multigrado</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table style="border: none; width: 100%; margin-bottom: 20px; font-size: 16px;">
              <tr style="border: none;">
                <td style="border: none; padding: 10px; font-weight: bold; width: 25%;">Grupo Multigrado:</td>
                <td style="border: none; padding: 10px;" id="editarMultigradoGrupo"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 10px; font-weight: bold;">Asignatura:</td>
                <td style="border: none; padding: 10px;" id="editarMultigradoAsignatura"></td>
              </tr>
              <tr style="border: none;">
                <td style="border: none; padding: 10px; font-weight: bold;">IH:</td>
                <td style="border: none; padding: 10px;" id="editarMultigradoIH"></td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <h5>Grados del Multigrado:</h5>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Aplicar cambios a períodos:</strong></label>
                  <div id="contenedorPeriodosMultigrado" style="margin-top: 5px;">
                    <!-- Los checkboxes se cargan dinámicamente -->
                  </div>
                </div>
              </div>
            </div>
            <table class="table table-bordered" id="tablaEditarMultigrado">
              <thead>
                <tr>
                  <th>Grado</th>
                  <th>Grupo</th>
                  <th>IHS</th>
                  <th>Estado</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarEdicionMultigrado">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL EDITAR ASIGNACIÓN -->
<div class="modal fade" id="modalEditarAsignacion" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #3c8ebdff; color: white;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-edit"></i> Editar Asignación Académica</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <!-- INFORMACIÓN DE SOLO LECTURA -->
          <div class="row">
            <div class="col-md-12">
              <table style="border: none; width: 100%; margin-bottom: 20px; font-size: 16px;">
                <tr style="border: none;">
                  <td style="border: none; padding: 10px; font-weight: bold; width: 25%;">Grado:</td>
                  <td style="border: none; padding: 10px;" id="editarGrado"></td>
                </tr>
                <tr style="border: none;">
                  <td style="border: none; padding: 10px; font-weight: bold;">Grupo:</td>
                  <td style="border: none; padding: 10px;" id="editarGrupo"></td>
                </tr>
                <tr style="border: none;">
                  <td style="border: none; padding: 10px; font-weight: bold;">Asignatura:</td>
                  <td style="border: none; padding: 10px;" id="editarAsignatura"></td>
                </tr>
                <tr style="border: none;">
                  <td style="border: none; padding: 10px; font-weight: bold;">IHS Asignada:</td>
                  <td style="border: none; padding: 10px;" id="editarIHS"></td>
                </tr>
              </table>
            </div>
          </div>
          
          <!-- CAMPOS EDITABLES -->
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Estado:</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                <select class="form-control input-lg" id="editarEstado">
                  <!-- Las opciones se cargan dinámicamente desde la base de datos -->
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group col-md-12">
              <label>Períodos:</label>
              <div id="editarPeriodos" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; font-size: 15px;"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarEdicion">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>

<script src="vistas/js/asignacion-academica.js"></script>