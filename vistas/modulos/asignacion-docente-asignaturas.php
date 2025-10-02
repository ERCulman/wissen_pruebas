<div class="content-wrapper">
    <section class="content-header">
        <h1>Asignación Docente - Asignaturas</h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Asignación Docente - Asignaturas</li>
        </ol>
    </section>

    <section class="content">
        <?php
        try {
            if (!isset($_SESSION["iniciarSesion"]) || $_SESSION["iniciarSesion"] != "ok") {
                echo '<script>window.location = "inicio";</script>';
                return;
            }

            // Esta lógica asume que los controladores y modelos ya fueron cargados por index.php
            $contexto = ControladorAsignacionDocenteAsignaturas::ctrValidarAcceso();

            if (!$contexto["acceso"]) {
                echo '<div class="alert alert-danger">No tiene permiso para acceder a este módulo.</div>';
                return;
            }

            $esAdmin = $contexto["esAdmin"];
            $sedes = [];
            $instituciones = [];

            if ($esAdmin) {
                $instituciones = ControladorAsignacionDocenteAsignaturas::ctrObtenerInstituciones();
            } else {
                $sedes = ControladorAsignacionDocenteAsignaturas::ctrObtenerSedes();
            }

            ?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Gestión de Asignaciones</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <?php if ($esAdmin) : ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Seleccionar Institución:</label>
                                    <select class="form-control" id="selectInstitucion">
                                        <option value="">Seleccione una institución</option>
                                        <?php foreach ($instituciones as $institucion) : ?>
                                            <option value="<?php echo $institucion['id']; ?>"><?php echo htmlspecialchars($institucion['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Seleccionar Sede:</label>
                                <select class="form-control" id="selectSede" <?php if ($esAdmin) echo 'disabled'; ?>>
                                    <option value="">Seleccione una sede</option>
                                    <?php if (!$esAdmin) : ?>
                                        <?php foreach ($sedes as $sede) : ?>
                                            <option value="<?php echo $sede['id']; ?>"><?php echo htmlspecialchars($sede['nombre_sede']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="contenidoDocentes" style="display: none;">
                        <div class="row">
                            <div class="col-md-8 col-sm-12">
                                <h4>Docentes de la Sede</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tablaDocentes">
                                        <thead>
                                            <tr>
                                                <th>Documento</th>
                                                <th>Nombre Completo</th>
                                                <th>Horas</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <h4 class="pull-left" style="margin: 0;">Asignaturas Disponibles</h4>
                                    <button type="button" class="btn btn-success btn-sm pull-right" id="btnAsignar" style="display: none;">Asignar Seleccionadas</button>
                                </div>
                                <div id="listaAsignaturas" style="max-height: calc(100vh - 300px); overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="contenidoAsignaciones" style="display: none; margin-top: 20px;">
                        <h4 id="tituloAsignaciones">Asignaturas Asignadas</h4>
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

            <?php
        } catch (Throwable $e) {
            // -- CAMBIO IMPORTANTE AQUÍ --
            // Ahora mostraremos el error técnico detallado para saber exactamente qué está fallando.
            echo '<div class="box"><div class="box-body"><div class="alert alert-danger">';
            echo '<h4><i class="icon fa fa-ban"></i> Error Crítico en el Módulo</h4>';
            // ESTA LÍNEA AHORA ESTÁ ACTIVA:
            echo '<pre><strong>Detalles del error:</strong><br>' . htmlspecialchars($e->getMessage()) . '<br><br><strong>Archivo:</strong> ' . $e->getFile() . '<br><strong>Línea:</strong> ' . $e->getLine() . '</pre>';
            echo '</div></div></div>';
        }
        ?>
    </section>
</div>
<script src="vistas/js/asignacion-docente-asignaturas.js"></script>