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
                    <div id="listaAsignaturas" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                    </div>
                  </div>

                  <!-- ASIGNATURAS ASIGNADAS -->
                  <div id="contenedorAsignadas" style="display: none; margin-top: 20px;">
                    <h5 id="tituloAsignadas">Asignaturas Asignadas</h5>
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
</body>
</html>

<script src="vistas/js/asignacion-academica.js"></script>