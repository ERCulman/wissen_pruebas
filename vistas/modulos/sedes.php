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
            <h1>Sede</h1>
            <ol class="breadcrumb">
                <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
                <li class="active">Sede</li>
            </ol>
        </section>

        <!-- CONTENIDO PAGINA SEDE-->

        <section class="content">

            <!-- BOTON NUEVA SEDE -->

            <div class="box">
                <div class="box-header with-border">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarSede">
                        <i class="fa fa-building"></i> Nueva sede
                    </button>
                </div>

                <!-- CABECERA INFORMACION SEDE -->

                <div class="box-body">
                    <table class="table table-bordered table-striped dt-responsive tablas" id="tablaSede">
                        <thead>
                        <tr>
                            <th style="width: 5%">Id</th>
                            <th style="width: 15%">Institución</th>
                            <th style="width: 5%">No. Sede</th>
                            <th style="width: 20%">Nombre Sede</th>
                            <th style="width: 10%">Tipo Sede</th>
                            <th style="width: 15%">Código DANE</th>
                            <th style="width: 10%">Estado</th>
                            <th style="width: 10%">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $item = null;
                        $valor = null;

                        $sede = ControladorSede::ctrMostrarSede($item, $valor);

                        foreach ($sede as $key => $value) {

                            echo '<tr>
                        <td>#</td>
                        <td>'.$value["nombre_institucion"].'</td>
                        <td>'.$value["numero_sede"].'</td>
                        <td>'.$value["nombre_sede"].'</td>
                        <td>'.$value["tipo_sede"].'</td>
                        <td>'.$value["codigo_dane"].'</td>';

                            if($value["estado"] == 1){
                                echo '<td><button class="btn btn-success btn-xs">Activo</button></td>';
                            } else {
                                echo '<td><button class="btn btn-danger btn-xs">Inactivo</button></td>';
                            }

                            echo '<td>
                                <button class="btn btn-info btnVerSede" 
                                data-id="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-warning btnEditarSede" 
                                data-id="'.$value["id"].'" ><i class="fa fa-pencil"></i></button>
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
      MODAL AGREGAR SEDE
    =======================================-->

    <div id="modalAgregarSede" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarSede">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-building"></i>  Agregar Sede</h4>
                    </div>

                    <div class="modal-body">
                        <div class="box-body" id="camposFormulario">

                            <!-- NÚMERO SEDE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Institución:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-university"></i></span>
                                        <select class="form-control input-lg" name="institucionSede" required>
                                            <option value="">Seleccione una Institución...</option>
                                            <?php
                                            $instituciones = ControladorSede::ctrObtenerInstituciones();
                                            foreach ($instituciones as $key => $value) {
                                                echo '<option value="'.$value["nombre"].'">'.$value["nombre"].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <!-- TIPO SEDE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                        <input type="text" class="form-control input-lg" name="tipoSede" placeholder="Tipo de Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE SEDE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre de la Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombreSede" placeholder="Nombre de la Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- CÓDIGO DANE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Código DANE:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" class="form-control input-lg" name="codigoDaneSede" placeholder="Código DANE" required>
                                    </div>
                                </div>
                            </div>

                            <!-- CONSECUTIVO DANE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Consecutivo DANE:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" class="form-control input-lg" name="consecutivoDane" placeholder="Consecutivo DANE (Opcional)">
                                    </div>
                                </div>
                            </div>

                            <!-- RESOLUCIÓN CREACIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Resolución de Creación:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                                        <input type="text" class="form-control input-lg" name="resolucionCreacionSede" placeholder="Resolución de Creación" required>
                                    </div>
                                </div>
                            </div>

                            <!-- FECHA CREACIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Creación:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="date" class="form-control input-lg" name="fechaCreacionSede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- DIRECCIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Dirección:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                        <input type="text" class="form-control input-lg" name="direccionSede" placeholder="Dirección de la Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- TELÉFONO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Teléfono:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" class="form-control input-lg" name="telefonoSede" placeholder="Teléfono (Opcional)">
                                    </div>
                                </div>
                            </div>

                            <!-- CELULAR -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Celular:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                        <input type="text" class="form-control input-lg" name="celularSede" placeholder="Celular (Opcional)">
                                    </div>
                                </div>
                            </div>

                            <!-- INSTITUCIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Número de Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                        <input type="text" class="form-control input-lg" name="numeroSede" placeholder="Número de Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- ESTADO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Estado de la Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-check"></i></span>
                                        <select class="form-control input-lg" name="estadoSede" required>
                                            <option value="">Seleccione...</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
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
                    $crearSede = new ControladorSede();
                    $crearSede -> ctrCrearSede();
                    ?>

                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL EDITAR SEDE
    =======================================-->

    <div id="modalEditarSede" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formEditarSede">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-pencil"></i>  Editar Sede</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body" id="camposEditar">

                            <!-- CAMPO OCULTO PARA EL ID -->
                            <input type="hidden" name="idSede" id="idSede">


                            <!-- INSTITUCIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Institución:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-university"></i></span>
                                        <select class="form-control input-lg" name="editarInstitucionSede" id="editarInstitucionSede" required>
                                            <option value="">Seleccione una Institución...</option>
                                            <?php
                                            $instituciones = ControladorSede::ctrObtenerInstituciones();
                                            foreach ($instituciones as $key => $value) {
                                                echo '<option value="'.$value["nombre"].'">'.$value["nombre"].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- TIPO SEDE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarTipoSede" id="editarTipoSede" placeholder="Tipo de Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRE SEDE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombre de la Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarNombreSede" id="editarNombreSede" placeholder="Nombre de la Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- CÓDIGO DANE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Código DANE:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarCodigoDaneSede" id="editarCodigoDaneSede" placeholder="Código DANE" required>
                                    </div>
                                </div>
                            </div>

                            <!-- CONSECUTIVO DANE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Consecutivo DANE:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarConsecutivoDane" id="editarConsecutivoDane" placeholder="Consecutivo DANE (Opcional)">
                                    </div>
                                </div>
                            </div>

                            <!-- RESOLUCIÓN CREACIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Resolución de Creación:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarResolucionCreacionSede" id="editarResolucionCreacionSede" placeholder="Resolución de Creación" required>
                                    </div>
                                </div>
                            </div>

                            <!-- FECHA CREACIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Creación:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="date" class="form-control input-lg" name="editarFechaCreacionSede" id="editarFechaCreacionSede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- DIRECCIÓN -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Dirección:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarDireccionSede" id="editarDireccionSede" placeholder="Dirección de la Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- TELÉFONO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Teléfono:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarTelefonoSede" id="editarTelefonoSede" placeholder="Teléfono (Opcional)">
                                    </div>
                                </div>
                            </div>

                            <!-- CELULAR -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Celular:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarCelularSede" id="editarCelularSede" placeholder="Celular (Opcional)">
                                    </div>
                                </div>
                            </div>

                            <!-- NÚMERO SEDE -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Número de Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-building"></i></span>
                                        <input type="text" class="form-control input-lg" name="editarNumeroSede" id="editarNumeroSede" placeholder="Número de Sede" required>
                                    </div>
                                </div>
                            </div>

                            <!-- ESTADO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Estado de la Sede:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-check"></i></span>
                                        <select class="form-control input-lg" name="editarEstadoSede" id="editarEstadoSede" required>
                                            <option value="">Seleccione...</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
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
                    $editarSede = new ControladorSede();
                    $editarSede -> ctrEditarSede();
                    ?>
                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL VER SEDE
    =======================================-->

    <div id="modalVerSede" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVerSede">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-search"></i> Ver Sede</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p><strong>Número de Sede:</strong> <span id="verNumeroSede"></span></p>
                                <p><strong>Tipo de Sede:</strong> <span id="verTipoSede"></span></p>
                                <p><strong>Nombre de la Sede:</strong> <span id="verNombreSede"></span></p>
                                <p><strong>Código DANE:</strong> <span id="verCodigoDaneSede"></span></p>
                                <p><strong>Consecutivo DANE:</strong> <span id="verConsecutivoDane"></span></p>
                                <p><strong>Resolución de Creación:</strong> <span id="verResolucionCreacionSede"></span></p>
                                <p><strong>Fecha de Creación:</strong> <span id="verFechaCreacionSede"></span></p>
                                <p><strong>Dirección:</strong> <span id="verDireccionSede"></span></p>
                                <p><strong>Teléfono:</strong> <span id="verTelefonoSede"></span></p>
                                <p><strong>Celular:</strong> <span id="verCelularSede"></span></p>
                                <p><strong>Institución:</strong> <span id="verInstitucionSede"></span></p>
                                <p><strong>Estado:</strong> <span id="verEstadoSede"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnEditarSede" data-dismiss="modal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/wissen/vistas/js/sede.js"></script>

</body>
</html>