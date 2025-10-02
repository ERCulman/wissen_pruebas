<?php

if($_SESSION["iniciarSesion"] != "ok"){
    echo '<script>window.location = "inicio";</script>';
    return;
}

require_once "controladores/asignacion-docente-asignaturas.controlador.php";
require_once "modelos/asignacion-docente-asignaturas.modelo.php";

// Validación específica del módulo (rector o representante en sede principal)
$validacion = ControladorAsignacionDocenteAsignaturas::ctrValidarAcceso();

if(!$validacion["acceso"]) {
    echo '<script>window.location = "acceso-denegado";</script>';
    return;
}

$sedes = ControladorAsignacionDocenteAsignaturas::ctrObtenerSedes();

?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Asignación Docente Asignaturas</h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Asignación Docente Asignaturas</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Gestión de Asignaciones</h3>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Seleccionar Sede:</label>
                            <select class="form-control" id="selectSede">
                                <option value="">Seleccione una sede</option>
                                <?php foreach($sedes as $sede): ?>
                                    <option value="<?php echo $sede['id']; ?>"><?php echo $sede['nombre_sede']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="contenidoDocentes" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Docentes de la Sede</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tablaDocentes">
                                    <thead>
                                        <tr>
                                            <th>Documento</th>
                                            <th>Nombre</th>
                                            <th>Horas Semanales</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Asignaturas Disponibles</h4>
                            <div id="listaAsignaturas"></div>
                            <button type="button" class="btn btn-primary" id="btnAsignar" style="display: none;">Asignar Asignaturas</button>
                        </div>
                    </div>
                </div>

                <div id="contenidoAsignaciones" style="display: none;">
                    <h4>Asignaciones del Docente</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tablaAsignaciones">
                            <thead>
                                <tr>
                                    <th>Área</th>
                                    <th>Asignatura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="vistas/js/asignacion-docente-asignaturas.js"></script>