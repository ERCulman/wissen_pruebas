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
            <h1>Curso</h1>
            <ol class="breadcrumb">
                <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
                <li class="active">Curso</li>
            </ol>
        </section>

        <!-- CONTENIDO PAGINA CURSO-->

        <section class="content">

            <!-- BOTON NUEVO CURSO -->

            <div class="box">
                <div class="box-header with-border">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCurso">
                        <i class="fa fa-book"></i> Nuevo curso
                    </button>
                </div>

                <!-- CABECERA INFORMACION CURSO -->

                <div class="box-body">
                    <table class="table table-bordered table-striped dt-responsive tablas" id="tablaCurso">
                        <thead>
                        <tr>
                            <th style="width: 10%">Id</th>
                            <th style="width: 30%">Tipo</th>
                            <th style="width: 40%">Nombre</th>
                            <th style="width: 20%">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $item = null;
                        $valor = null;

                        $curso = ControladorCurso::ctrMostrarCurso($item, $valor);

                        foreach ($curso as $key => $value) {

                            echo '<tr>
                        <td>'.$value["id"].'</td>
                        <td>'.$value["tipo"].'</td>
                        <td>'.$value["nombre"].'</td>';

                            echo '<td>
                                <button class="btn btn-info btnVerCurso" 
                                data-id="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-warning btnEditarCurso" 
                                data-id="'.$value["id"].'" ><i class="fa fa-pencil"></i></button>
                                <a href="index.php?ruta=cursos&idCurso=' . $value["id"] . '" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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
      MODAL AGREGAR CURSO
    =======================================-->

    <div id="modalAgregarCurso" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarCurso">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-book"></i>  Agregar Curso</h4>
                    </div>

                    <div class="modal-body">
                        <div class="box-body" id="camposFormulario">

                            <!-- TIPO CURSO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Curso:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-list"></i></span>
                                        <select class="form-control input-lg" name="tipoCurso" required>
                                            <option value="">Seleccione un Tipo...</option>
                                            <option value="Númerico">Númerico</option>
                                            <option value="Alfabético">Alfabético</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE CURSO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del Curso:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-book"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombreCurso" placeholder="Nombre del Curso (máx 10 caracteres)" maxlength="10" required>
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
                    $crearCurso = new ControladorCurso();
                    $crearCurso -> ctrCrearCurso();
                    ?>

                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL EDITAR CURSO
    =======================================-->

    <div id="modalEditarCurso" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formEditarCurso">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-pencil"></i>  Editar Curso</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body" id="camposEditar">

                            <!-- CAMPO OCULTO PARA EL ID -->
                            <input type="hidden" name="idCurso" id="idCurso">

                            <!-- TIPO CURSO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Curso:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-list"></i></span>
                                        <select class="form-control input-lg" name="editarTipoCurso" id="editarTipoCurso" required>
                                            <option value="">Seleccione un Tipo...</option>
                                            <option value="Númerico">Númerico</option>
                                            <option value="Alfabético">Alfabético</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE CURSO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre del Curso:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-book"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarNombreCurso" id="editarNombreCurso" placeholder="Nombre del Curso (máx 10 caracteres)" maxlength="10" required>
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
                    $editarCurso = new ControladorCurso();
                    $editarCurso -> ctrEditarCurso();
                    ?>
                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL VER CURSO
    =======================================-->

    <div id="modalVerCurso" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVerCurso">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-search"></i> Ver Curso</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table style="border: none; width: 100%;">
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold; width: 40%;">Tipo de Curso:</td>
                                        <td style="border: none; padding: 10px;" id="verTipoCurso"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Nombre del Curso:</td>
                                        <td style="border: none; padding: 10px;" id="verNombreCurso"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnEditarCurso" data-dismiss="modal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/wissen/vistas/js/curso.js"></script>

    <?php

    $borrarCurso = new ControladorCurso();
    $borrarCurso -> ctrBorrarCurso();

    ?>

</body>
</html>