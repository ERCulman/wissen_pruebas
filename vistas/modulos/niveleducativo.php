<!DOCTYPE html>
<html>
<!--<head>
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

</head> -->

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Nivel Educativo</h1>
            <ol class="breadcrumb">
                <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
                <li class="active">Nivel Educativo</li>
            </ol>
        </section>

        <!-- CONTENIDO PAGINA NIVEL EDUCATIVO-->

        <section class="content">

            <!-- BOTON NUEVO NIVEL EDUCATIVO -->

            <div class="box">
                <div class="box-header with-border">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarNivelEducativo">
                        <i class="fa fa-plus"></i> Nuevo nivel educativo
                    </button>
                </div>

                <!-- CABECERA INFORMACION NIVEL EDUCATIVO -->

                <div class="box-body">
                    <table class="table table-bordered table-striped dt-responsive tablas" id="tablaNivelEducativo">
                        <thead>
                        <tr>
                            <th style="width: 10%">Id</th>
                            <th style="width: 20%">Código</th>
                            <th style="width: 50%">Nombre</th>
                            <th style="width: 20%">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $item = null;
                        $valor = null;

                        $nivelEducativo = ControladorNivelEducativo::ctrMostrarNivelEducativo($item, $valor);

                        foreach ($nivelEducativo as $key => $value) {

                            echo '<tr>
                        <td>'.$value["id"].'</td>
                        <td>'.$value["codigo"].'</td>
                        <td>'.$value["nombre"].'</td>
                        <td>
                            <button class="btn btn-info btnVerNivelEducativo" 
                            data-id="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-warning btnEditarNivelEducativo" 
                            data-id="'.$value["id"].'" ><i class="fa fa-pencil"></i></button>
                            <a href="index.php?ruta=niveleducativo&idNivelEducativo=' . $value["id"] . '" class="btn btn-danger"><i class="fa fa-trash"></i></a>
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
      MODAL AGREGAR NIVEL EDUCATIVO
    =======================================-->

    <div id="modalAgregarNivelEducativo" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarNivelEducativo">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i>  Agregar Nivel Educativo</h4>
                    </div>

                    <div class="modal-body">
                        <div class="box-body" id="camposFormulario">

                            <!-- CÓDIGO NIVEL EDUCATIVO TITULO-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <label>Código Nivel Educativo:</label>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE NIVEL EDUCATIVO TITULO-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <label>Nombre Nivel Educativo:</label>
                                    </div>
                                </div>
                            </div>

                            <!-- CÓDIGO NIVEL EDUCATIVO CASILLA-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                        <input type="text" class="form-control input-lg" name="codigoNivelEducativo" placeholder="Código Nivel Educativo" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE NIVEL EDUCATIVO CASILLA-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-graduation-cap"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombreNivelEducativo" placeholder="Nombre Nivel Educativo" required>
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

                    $crearNivelEducativo = new ControladorNivelEducativo();
                    $crearNivelEducativo -> ctrCrearNivelEducativo();

                    ?>

                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL EDITAR NIVEL EDUCATIVO
    =======================================-->

    <div id="modalEditarNivelEducativo" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formEditarNivelEducativo">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-pencil"></i>  Editar Nivel Educativo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body" id="camposEditar">

                            <!-- CAMPO OCULTO PARA EL ID -->
                            <input type="hidden" name="idNivelEducativo" id="idNivelEducativo">

                            <!-- CÓDIGO NIVEL EDUCATIVO TITULO-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <label>Código Nivel Educativo:</label>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE NIVEL EDUCATIVO TITULO-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <label>Nombre Nivel Educativo:</label>
                                    </div>
                                </div>
                            </div>

                            <!-- CÓDIGO NIVEL EDUCATIVO CASILLA-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarCodigoNivelEducativo" id="editarCodigoNivelEducativo" placeholder="Código Nivel Educativo" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE NIVEL EDUCATIVO CASILLA-->

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-graduation-cap"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarNombreNivelEducativo" id="editarNombreNivelEducativo" placeholder="Nombre Nivel Educativo" required>
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

                    $editarNivelEducativo = new ControladorNivelEducativo();
                    $editarNivelEducativo -> ctrEditarNivelEducativo();

                    ?>
                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL VER NIVEL EDUCATIVO
    =======================================-->

    <!-- Modal Ver Nivel Educativo -->
    <div id="modalVerNivelEducativo" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVerNivelEducativo">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-search"></i> Ver Nivel Educativo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table style="border: none; width: 100%;">
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold; width: 40%;">Código Nivel Educativo:</td>
                                        <td style="border: none; padding: 10px;" id="verCodigoNivelEducativo"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Nombre Nivel Educativo:</td>
                                        <td style="border: none; padding: 10px;" id="verNombreNivelEducativo"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnEditarNivelEducativo" data-dismiss="modal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php

    $borrarNivelEducativo = new ControladorNivelEducativo();
    $borrarNivelEducativo -> ctrBorrarNivelEducativo();

    ?>

    <script src="/wissen/vistas/js/niveleducativo.js"></script>

</body>
</html>