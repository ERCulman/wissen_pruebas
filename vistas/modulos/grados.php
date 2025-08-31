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

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Grado</h1>
            <ol class="breadcrumb">
                <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
                <li class="active">Grado</li>
            </ol>
        </section>

        <!-- CONTENIDO PAGINA GRADO-->

        <section class="content">

            <!-- BOTON NUEVO GRADO -->

            <div class="box">
                <div class="box-header with-border">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarGrado">
                        <i class="fa fa-graduation-cap"></i> Nuevo grado
                    </button>
                </div>

                <!-- CABECERA INFORMACION GRADO -->

                <div class="box-body">
                    <table class="table table-bordered table-striped dt-responsive tablas" id="tablaGrado">
                        <thead>
                        <tr>
                            <th style="width: 10%">Id</th>
                            <th style="width: 15%">Número</th>
                            <th style="width: 35%">Nombre</th>
                            <th style="width: 25%">Nivel Educativo</th>
                            <th style="width: 15%">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $item = null;
                        $valor = null;

                        $grado = ControladorGrado::ctrMostrarGrado($item, $valor);

                        foreach ($grado as $key => $value) {

                            echo '<tr>
                        <td>'.$value["id"].'</td>
                        <td>'.$value["numero"].'</td>
                        <td>'.$value["nombre"].'</td>
                        <td>'.$value["nombre_nivel_educativo"].'</td>';

                            echo '<td>
                                <button class="btn btn-info btnVerGrado" 
                                data-id="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-warning btnEditarGrado" 
                                data-id="'.$value["id"].'" ><i class="fa fa-pencil"></i></button>
                                <a href="index.php?ruta=grados&idGrado=' . $value["id"] . '" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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
      MODAL AGREGAR GRADO
    =======================================-->

    <div id="modalAgregarGrado" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarGrado">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-graduation-cap"></i>  Agregar Grado</h4>
                    </div>

                    <div class="modal-body">
                        <div class="box-body" id="camposFormulario">

                            <!-- NÚMERO GRADO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Número de Grado:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-sort-numeric-asc"></i></span>
                                        <input type="text" class="form-control input-lg" name="numeroGrado" placeholder="Número de Grado" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE GRADO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del Grado:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-graduation-cap"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombreGrado" placeholder="Nombre del Grado" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NIVEL EDUCATIVO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nivel Educativo:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-book"></i></span>
                                        <select class="form-control input-lg" name="nivelEducativoGrado" required>
                                            <option value="">Seleccione un Nivel Educativo...</option>
                                            <?php
                                            $nivelesEducativos = ControladorGrado::ctrObtenerNivelesEducativos();
                                            foreach ($nivelesEducativos as $key => $value) {
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
                    $crearGrado = new ControladorGrado();
                    $crearGrado -> ctrCrearGrado();
                    ?>

                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL EDITAR GRADO
    =======================================-->

    <div id="modalEditarGrado" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formEditarGrado">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-pencil"></i>  Editar Grado</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body" id="camposEditar">

                            <!-- CAMPO OCULTO PARA EL ID -->
                            <input type="hidden" name="idGrado" id="idGrado">

                            <!-- NÚMERO GRADO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Número de Grado:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-sort-numeric-asc"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarNumeroGrado" id="editarNumeroGrado" placeholder="Número de Grado" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE GRADO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del Grado:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-graduation-cap"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarNombreGrado" id="editarNombreGrado" placeholder="Nombre del Grado" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NIVEL EDUCATIVO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nivel Educativo:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-book"></i></span>
                                        <select class="form-control input-lg" name="editarNivelEducativoGrado" id="editarNivelEducativoGrado" required>
                                            <option value="">Seleccione un Nivel Educativo...</option>
                                            <?php
                                            $nivelesEducativos = ControladorGrado::ctrObtenerNivelesEducativos();
                                            foreach ($nivelesEducativos as $key => $value) {
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
                    $editarGrado = new ControladorGrado();
                    $editarGrado -> ctrEditarGrado();
                    ?>
                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL VER GRADO
    =======================================-->

    <div id="modalVerGrado" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVerGrado">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-search"></i> Ver Grado</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table style="border: none; width: 100%;">
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold; width: 40%;">Número de Grado:</td>
                                        <td style="border: none; padding: 10px;" id="verNumeroGrado"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Nombre del Grado:</td>
                                        <td style="border: none; padding: 10px;" id="verNombreGrado"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Nivel Educativo:</td>
                                        <td style="border: none; padding: 10px;" id="verNivelEducativoGrado"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnEditarGrado" data-dismiss="modal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/wissen/vistas/js/grado.js"></script>

    <?php

    $borrarGrado = new ControladorGrado();
    $borrarGrado -> ctrBorrarGrado();

    ?>

</body>
</html>