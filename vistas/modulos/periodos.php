    <div class="content-wrapper">
        <section class="content-header">
            <h1>Periodo Académico</h1>
            <ol class="breadcrumb">
                <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
                <li class="active">Periodo Académico</li>
            </ol>
        </section>

        <!-- CONTENIDO PAGINA PERIODO-->

        <section class="content">

            <!-- BOTON NUEVO PERIODO -->

            <div class="box">
                <div class="box-header with-border">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarPeriodo">
                        <i class="fa fa-calendar"></i> Nuevo Periodo
                    </button>
                </div>

                <!-- CABECERA INFORMACION PERIODO -->

                <div class="box-body">
                    <table class="table table-bordered table-striped dt-responsive tablas" id="tablaPeriodo">
                        <thead>
                        <tr>
                            <th style="width: 10%">Id</th>
                            <th style="width: 25%">Nombre</th>
                            <th style="width: 20%">Fecha Inicio</th>
                            <th style="width: 20%">Fecha Fin</th>
                            <th style="width: 10%">Año Lectivo</th>
                            <th style="width: 15%">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $item = null;
                        $valor = null;

                        $periodo = ControladorPeriodo::ctrMostrarPeriodo($item, $valor);

                        foreach ($periodo as $key => $value) {

                            echo '<tr>
                                <td>'.$value["id"].'</td>
                                <td>'.$value["nombre"].'</td>
                                <td>'.$value["fecha_inicio"].'</td>
                                <td>'.$value["fecha_fin"].'</td>
                                <td>'.$value["nombre_anio_lectivo"].'</td>';

                            echo '<td>
                                <button class="btn btn-info btnVerPeriodo" 
                                data-id="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-warning btnEditarPeriodo" 
                                data-id="'.$value["id"].'" ><i class="fa fa-pencil"></i></button>
                                <a href="index.php?ruta=periodos&idPeriodo=' . $value["id"] . '" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                              </td>
                     </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- =======================================
      MODAL AGREGAR PERIODO
    =======================================-->

    <div id="modalAgregarPeriodo" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarPeriodo">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-calendar"></i>  Agregar Periodo</h4>
                    </div>

                    <div class="modal-body">
                        <div class="box-body" id="camposFormulario">

                            <!-- NOMBRE PERIODO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del Periodo:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-text-width"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombrePeriodo" placeholder="Nombre del Periodo" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FECHA INICIO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Inicio:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i></span>
                                        <input type="date" class="form-control input-lg" name="fechaInicio" required>
                                    </div>
                                </div>
                            </div>

                            <!-- FECHA FIN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Fin:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar-times-o"></i></span>
                                        <input type="date" class="form-control input-lg" name="fechaFin" required>
                                    </div>
                                </div>
                            </div>

                            <!-- AÑO LECTIVO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Año Lectivo:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <select class="form-control input-lg" name="anioLectivo" required>
                                            <option value="">Seleccione un Año Lectivo...</option>
                                            <?php
                                            $aniosLectivos = ControladorPeriodo::ctrObtenerAniosLectivos();
                                            foreach ($aniosLectivos as $key => $value) {
                                                echo '<option value="'.$value["nombre"].'">'.$value["nombre"].'</option>';
                                            }
                                            ?>
                                        </select>
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
                    $crearPeriodo = new ControladorPeriodo();
                    $crearPeriodo -> ctrCrearPeriodo();
                    ?>

                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL EDITAR PERIODO
    =======================================-->

    <div id="modalEditarPeriodo" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formEditarPeriodo">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-pencil"></i>  Editar Periodo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body" id="camposEditar">

                            <!-- CAMPO OCULTO PARA EL ID -->
                            <input type="hidden" name="idPeriodo" id="idPeriodo">

                            <!-- NOMBRE PERIODO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del Periodo:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-text-width"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarNombrePeriodo" id="editarNombrePeriodo" placeholder="Nombre del Periodo" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FECHA INICIO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Inicio:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i></span>
                                        <input type="date" class="form-control input-lg" name="editarFechaInicio" id="editarFechaInicio" required>
                                    </div>
                                </div>
                            </div>

                            <!-- FECHA FIN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Fin:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar-times-o"></i></span>
                                        <input type="date" class="form-control input-lg" name="editarFechaFin" id="editarFechaFin" required>
                                    </div>
                                </div>
                            </div>

                            <!-- AÑO LECTIVO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Año Lectivo:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <select class="form-control input-lg" name="editarAnioLectivo" id="editarAnioLectivo" required>
                                            <option value="">Seleccione un Año Lectivo...</option>
                                            <?php
                                            $aniosLectivos = ControladorPeriodo::ctrObtenerAniosLectivos();
                                            foreach ($aniosLectivos as $key => $value) {
                                                echo '<option value="'.$value["nombre"].'">'.$value["nombre"].'</option>';
                                            }
                                            ?>
                                        </select>
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
                    $editarPeriodo = new ControladorPeriodo();
                    $editarPeriodo -> ctrEditarPeriodo();
                    ?>
                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL VER PERIODO
    =======================================-->

    <div id="modalVerPeriodo" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVerPeriodo">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-search"></i> Ver Periodo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table style="border: none; width: 100%;">
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold; width: 40%;">Nombre del Periodo:</td>
                                        <td style="border: none; padding: 10px;" id="verNombrePeriodo"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Fecha de Inicio:</td>
                                        <td style="border: none; padding: 10px;" id="verFechaInicio"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Fecha de Fin:</td>
                                        <td style="border: none; padding: 10px;" id="verFechaFin"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Año Lectivo:</td>
                                        <td style="border: none; padding: 10px;" id="verAnioLectivo"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnEditarPeriodo" data-dismiss="modal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php

    $borrarPeriodo = new ControladorPeriodo();
    $borrarPeriodo -> ctrBorrarPeriodo();

    ?>
