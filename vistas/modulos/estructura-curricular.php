<div class="content-wrapper">
    <section class="content-header">
        <h1>Estructura Curricular</h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
            <li class="active">Estructura Curricular</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <!-- PESTAÑAS -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#areas" aria-controls="areas" role="tab" data-toggle="tab">
                            <i class="fa fa-folder"></i> Áreas
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#asignaturas" aria-controls="asignaturas" role="tab" data-toggle="tab">
                            <i class="fa fa-book"></i> Asignaturas
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#curriculo" aria-controls="curriculo" role="tab" data-toggle="tab">
                            <i class="fa fa-cogs"></i> Crear Currículo
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#ver-curriculo" aria-controls="ver-curriculo" role="tab" data-toggle="tab">
                            <i class="fa fa-list"></i> Ver Currículo
                        </a>
                    </li>
                </ul>
            </div>

            <div class="box-body">
                <div class="tab-content">
                    <!-- PESTAÑA ÁREAS -->
                    <div role="tabpanel" class="tab-pane active" id="areas">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarArea">
                            <i class="fa fa-plus"></i> Nueva Área
                        </button>
                        <br><br>
                        <table class="table table-bordered table-striped" id="tablaAreas" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $areas = ControladorEstructuraCurricular::ctrMostrarAreas(null, null);
                                foreach ($areas as $area) {
                                    echo '<tr>
                                        <td>'.$area["id"].'</td>
                                        <td>'.$area["nombre"].'</td>
                                        <td>
                                            <button class="btn btn-info btnVerArea" data-id="'.$area["id"].'">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning btnEditarArea" data-id="'.$area["id"].'">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <a href="index.php?ruta=estructura-curricular&idArea='.$area["id"].'" class="btn btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PESTAÑA ASIGNATURAS -->
                    <div role="tabpanel" class="tab-pane" id="asignaturas">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarAsignatura">
                            <i class="fa fa-plus"></i> Nueva Asignatura
                        </button>
                        <br><br>
                        <table class="table table-bordered table-striped" id="tablaAsignaturas" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Área</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $asignaturas = ControladorEstructuraCurricular::ctrMostrarAsignaturas(null, null);
                                foreach ($asignaturas as $asignatura) {
                                    echo '<tr>
                                        <td>'.$asignatura["id"].'</td>
                                        <td>'.$asignatura["nombre"].'</td>
                                        <td>'.$asignatura["nombre_area"].'</td>
                                        <td>
                                            <button class="btn btn-info btnVerAsignatura" data-id="'.$asignatura["id"].'">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning btnEditarAsignatura" data-id="'.$asignatura["id"].'">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <a href="index.php?ruta=estructura-curricular&idAsignatura='.$asignatura["id"].'" class="btn btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PESTAÑA CURRÍCULO -->
                    <div role="tabpanel" class="tab-pane" id="curriculo">
                        <div class="row">
                            <div class="col-md-3">
                                <h4>Áreas Disponibles</h4>
                                <div id="areasDisponibles">
                                    <?php
                                    $areas = ControladorEstructuraCurricular::ctrMostrarAreas(null, null);
                                    foreach ($areas as $area) {
                                        echo '<div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="areaCheckbox" value="'.$area["id"].'"> '.$area["nombre"].'
                                            </label>
                                        </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h4>Asignaturas del Área</h4>
                                <div id="asignaturasArea"></div>
                            </div>
                            <div class="col-md-6">
                                <h4>Asignaturas Asignadas</h4>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row" style="font-weight: bold; border-bottom: 2px solid #ddd; margin-bottom: 10px; padding-bottom: 5px;">
                                            <div class="col-md-5">Asignatura</div>
                                            <div class="col-md-4">IHS</div>
                                            <div class="col-md-3">Acciones</div>
                                        </div>
                                        <div id="asignaturasAsignadas">
                                            <p class="text-muted" id="mensajeVacio">No hay asignaturas asignadas</p>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <h4>Grados Disponibles</h4>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?php
                                        $grados = ControladorOfertaEducativa::ctrMostrarOfertaPorUsuario($_SESSION["id_usuario"]);
                                        $gradosUnicos = array();
                                        
                                        // Filtrar para obtener grados únicos
                                        foreach ($grados as $grado) {
                                            $key = $grado["id"] . '_' . $grado["nombre_grado"] . '_' . $grado["nombre_nivel"];
                                            if (!isset($gradosUnicos[$key])) {
                                                $gradosUnicos[$key] = $grado;
                                            }
                                        }
                                        
                                        $contador = 0;
                                        foreach ($gradosUnicos as $grado) {
                                            if($contador % 3 == 0) echo '<div class="row">';
                                            echo '<div class="col-md-4">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="gradoCheckbox" value="'.$grado["id"].'"> '.$grado["nombre_grado"].' - '.$grado["nombre_nivel"].'
                                                    </label>
                                                </div>
                                            </div>';
                                            $contador++;
                                            if($contador % 3 == 0) echo '</div>';
                                        }
                                        if($contador % 3 != 0) echo '</div>';
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <button type="button" class="btn btn-success btn-lg" id="btnGuardarCurriculo">
                                    <i class="fa fa-save"></i> Guardar Currículo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PESTAÑA VER CURRÍCULO -->
                    <div role="tabpanel" class="tab-pane" id="ver-curriculo">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Seleccionar Grado</h4>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?php
                                        $grados = ControladorOfertaEducativa::ctrMostrarOfertaPorUsuario($_SESSION["id_usuario"]);
                                        $gradosUnicos = array();
                                        
                                        // Filtrar para obtener grados únicos
                                        foreach ($grados as $grado) {
                                            $key = $grado["id"] . '_' . $grado["nombre_grado"] . '_' . $grado["nombre_nivel"];
                                            if (!isset($gradosUnicos[$key])) {
                                                $gradosUnicos[$key] = $grado;
                                            }
                                        }
                                        
                                        foreach ($gradosUnicos as $grado) {
                                            echo '<div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="verGradoCheckbox" value="'.$grado["id"].'"> '.$grado["nombre_grado"].' - '.$grado["nombre_nivel"].'
                                                </label>
                                            </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div id="curriculoGrado">
                                    <h4>Currículo del Grado</h4>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> Selecciona un grado para ver su currículo
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODALES -->
<?php include "modales/modal-areas.php"; ?>
<?php include "modales/modal-asignaturas.php"; ?>
<?php include "modales/modal-editar-curriculo.php"; ?>

<?php
$borrarArea = new ControladorEstructuraCurricular();
$borrarArea->ctrBorrarArea();

$borrarAsignatura = new ControladorEstructuraCurricular();
$borrarAsignatura->ctrBorrarAsignatura();

$borrarEstructura = new ControladorEstructuraCurricular();
$borrarEstructura->ctrBorrarEstructuraCurricular();
?>

