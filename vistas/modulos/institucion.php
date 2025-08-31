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
        <h1>Institución</h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
          <li class="active">Institución</li>
        </ol>
      </section>

      <!-- CONTENIDO PAGINA INSTITUCION-->


      <section class="content">

        <!-- BOTON NUEVA INSITUCION -->

        <div class="box">
          <div class="box-header with-border">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarInstitucion">
              <i class="fa fa-user-plus"></i> Nueva institución
            </button>
          </div>

          <!-- CABECERA INFORMACION INSTITUCION -->

          <div class="box-body">
            <table class="table table-bordered table-striped dt-responsive tablas" id="tablaInstitucion">
              <thead>
                <tr>
                  <th style="width: 5%">Id</th>
                    <th style="width: 10%">Institución</th>
                  <th style="width: 10%">Nombre</th>
                  <th style="width: 10%">Código dane </th>
                  <th style="width: 10%">Sede No.</th>
                  <th style="width: 12%">Email</th>
                  <th style="width: 5%">Estado</th>
                  <th style="width: 10%">Acciones</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $item = null;
                $valor = null;

                $institucion = ControladorInstitucion::ctrMostrarInstitucion($item, $valor);

                foreach ($institucion as $key => $value) {

                    echo '<tr>
                        <td>'.$value["id"].'</td>
                        <td>'.$value["nombre"].'</td>
                        <td>'.$value["codigo_dane"].'</td>
                        <td>'.$value["cantidad_sedes"].'</td>
                        <td>'.$value["nombre_representante"].'</td>  <!-- NUEVO CAMPO -->
                        <td>'.$value["email"].'</td>';

                        if($value["estado"] == 1){
                            echo '<td><button class="btn btn-success btn-xs">Activo</button></td>';
                        } else {
                            echo '<td><button class="btn btn-danger btn-xs">Inactivo</button></td>';
                        }

                        echo '<td>
                                <button class="btn btn-info btnVerInstitucion" 
                                data-id="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-warning btnEditarInstitucion" 
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
      MODAL AGREGAR INSTITUCIÓN
    =======================================-->

    <div id="modalAgregarInstitucion" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarInstitucion">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><i class="fa fa-user-plus"></i>  Agregar Institución</h4>
            </div>

            <div class="modal-body">
              <div class="box-body" id="camposFormulario">

                <!-- NOMBRE INSTITUCIÓN TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Nombre Institución:</label>
                    </div>
                  </div>
                </div>

                <!-- CODIGO DANE TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Código Dane:</label>
                    </div>
                  </div>
                </div>

                <!-- NOMBRE INSTITUCIÓN CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" name="nombreInstitucion" placeholder="Nombre Institución" required>
                    </div>
                  </div>
                </div>

                  <!-- NOMBRE INSTITUCIÓN CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="codigoDane" placeholder="Codigo Dane" required>
                          </div>
                      </div>
                  </div>

                <!-- NIT TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Número NIT:</label>
                    </div>
                  </div>
                </div>

                <!-- RESOLUCIÓN CREACIÓN TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Resolución de creación:</label>
                    </div>
                  </div>
                </div>

                <!-- NIT CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" name="NIT" placeholder="Número de NIT" required>
                    </div>
                  </div>
                </div>

                <!-- RESOLUCIÓN CREACIÓN CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" name="resolucionCreacion" placeholder="Resolución de Creación" required>
                    </div>
                  </div>
                </div>

                <!-- DIRECCIÓN TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Dirección:</label>
                    </div>
                  </div>
                </div>

                <!-- EMAIL TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Correo Electrónico:</label>
                    </div>
                  </div>
                </div>

                <!-- DIRECCIÓN CASILLA -->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="direccion" placeholder="Dirección Institución" required>
                          </div>
                      </div>
                  </div>

                <!-- EMAIL CASILLA -->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="email" placeholder="Correo Electrónico Institución" required>
                          </div>
                      </div>
                  </div>

                <!-- TELÉFONO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Número de Teléfono:</label>
                    </div>
                  </div>
                </div>

                <!-- CANTIDAD SEDES TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Cantidad de Sedes:</label>
                    </div>
                  </div>
                </div>

                <!-- TELÉFONO CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" class="form-control input-lg" name="telefono" placeholder="Número de Teléfono" required>
                    </div>
                  </div>
                </div>

                <!-- CANTIDAD SEDES CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" name="cantidadSedes" placeholder="Cantidad de Sedes de la Institución" required>
                    </div>
                  </div>
                </div>

                <!-- USUARIO REPRESENTANTE DE LA INSTITUCIÓN TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Representante de la Institución:</label>
                    </div>
                  </div>
                </div>

                <!-- ESTADO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Estado de la Institución:</label>
                    </div>
                  </div>
                </div>

                <!-- USUARIO REPRESENTANTE DE LA INSTITUCIÓN CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                      <input type="text" class="form-control input-lg" name="usuarioRepresentante" placeholder="Nombre del Representante de la Sede" required>
                    </div>
                  </div>
                </div>

                <!-- ESTADO CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="estadoInstitucion">
                        <option value="">Seleccione...</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Campos como numeroDocumento, tipoDocumento, etc -->
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
              <button type="submit" class="btn btn-primary">Registrar</button>
            </div>

            <?php

              $crearInstitucion = new ControladorInstitucion();
              $crearInstitucion -> ctrCrearInstitucion();

            ?>

          </form>
        </div>
      </div>
    </div>

    <!-- =======================================
      MODAL EDITAR INSTITUCIÓN
    =======================================-->

    <div id="modalEditarInstitucion" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form role ="form" method="post" enctype="multipart/form-data" id="formEditarInstitucion">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><i class="fa fa-pencil"></i>  Editar Institución</h4>
            </div>
            <div class="modal-body">
              <div class="box-body" id="camposEditar">
                  
                  <!-- CAMPO OCULTO PARA EL ID -->
                  <input type="hidden" name="idInstitucion" id="idInstitucion">

                  <!-- NOMBRE INSTITUCIÓN TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Nombre Institución:</label>
                          </div>
                      </div>
                  </div>

                  <!-- CODIGO DANE TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Código Dane:</label>
                          </div>
                      </div>
                  </div>

                  <!-- NOMBRE INSTITUCIÓN CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="editarNombreInstitucion" id="editarNombreInstitucion" placeholder="Nombre Institución" required>
                          </div>
                      </div>
                  </div>

                  <!-- NOMBRE INSTITUCIÓN CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="editarCodigoDane" id="editarCodigoDane" placeholder="Codigo Dane" required>
                          </div>
                      </div>
                  </div>

                  <!-- NIT TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Número NIT:</label>
                          </div>
                      </div>
                  </div>

                  <!-- RESOLUCIÓN CREACIÓN TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Resolución de creación:</label>
                          </div>
                      </div>
                  </div>

                  <!-- NIT CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="editarNIT" id="editarNIT" placeholder="Número de NIT" required>
                          </div>
                      </div>
                  </div>

                  <!-- RESOLUCIÓN CREACIÓN CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="editarResolucionCreacion" id="editarResolucionCreacion" placeholder="Resolución de Creación" required>
                          </div>
                      </div>
                  </div>

                  <!-- DIRECCIÓN TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Dirección:</label>
                          </div>
                      </div>
                  </div>

                  <!-- EMAIL TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Correo Electrónico:</label>
                          </div>
                      </div>
                  </div>

                  <!-- DIRECCIÓN CASILLA -->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="EditarDireccion" id="editarDireccion" placeholder="Dirección Institución" required>
                          </div>
                      </div>
                  </div>

                  <!-- EMAIL CASILLA -->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="editarEmail" id="editarEmail" placeholder="Correo Electrónico Institución" required>
                          </div>
                      </div>
                  </div>

                  <!-- TELÉFONO TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Número de Teléfono:</label>
                          </div>
                      </div>
                  </div>

                  <!-- CANTIDAD SEDES TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Cantidad de Sedes:</label>
                          </div>
                      </div>
                  </div>

                  <!-- TELÉFONO CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              <input type="text" class="form-control input-lg" name="editarTelefono" id="editarTelefono" placeholder="Número de Teléfono" required>
                          </div>
                      </div>
                  </div>

                  <!-- CANTIDAD SEDES CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <input type="text" class="form-control input-lg" name="editarCantidadSedes" id="editarCantidadSedes" placeholder="Cantidad de Sedes de la Institución" required>
                          </div>
                      </div>
                  </div>

                  <!-- USUARIO REPRESENTANTE DE LA INSTITUCIÓN TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Representante de la Institución:</label>
                          </div>
                      </div>
                  </div>

                  <!-- ESTADO TITULO-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <label>Estado de la Institución:</label>
                          </div>
                      </div>
                  </div>

                  <!-- USUARIO REPRESENTANTE DE LA INSTITUCIÓN CASILLA-->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                              <input type="text" class="form-control input-lg" name="editarUsuarioRepresentante" id="editarUsuarioRepresentante" placeholder="Nombre del Representante de la Sede" required>
                          </div>
                      </div>
                  </div>

                  <!-- ESTADO CASILLA -->

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                              <select class="form-control input-lg" name="editarEstadoInstitucion" id="editarEstadoInstitucion">
                                  <option value="">Seleccione...</option>
                                  <option value="1">Activo</option>
                                  <option value="0">Inactivo</option>
                              </select>
                          </div>
                      </div>
                  </div>

                  <!-- Campos como numeroDocumento, tipoDocumento, etc -->
              </div>
            </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                  <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>

            <?php

            $editarInstitucion = new ControladorInstitucion();
            $editarInstitucion -> ctrEditarInstitucion();

            ?>
          </form>
        </div>
      </div>
    </div>

    <!-- =======================================
      MODAL VER INSTITUCIÓN
    =======================================-->

      <!-- Modal Ver Institución -->
      <div id="modalVerInstitucion" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <form id="formVerInstitucion">
                      <div class="modal-header" style="background: #3c8ebdff; color: white;">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title"><i class="fa fa-search"></i> Ver Institución</h4>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                              <div class="col-md-12">
                                  <p><strong>Nombre de la Institución:</strong> <span id="verNombreInstitucion"></span></p>
                                  <p><strong>Código DANE:</strong> <span id="verCodigoDane"></span></p>
                                  <p><strong>NIT:</strong> <span id="verNIT"></span></p>
                                  <p><strong>Resolución de Creación:</strong> <span id="verResolucionCreacion"></span></p>
                                  <p><strong>Dirección:</strong> <span id="verDireccion"></span></p>
                                  <p><strong>Email:</strong> <span id="verEmail"></span></p>
                                  <p><strong>Teléfono:</strong> <span id="verTelefono"></span></p>
                                  <p><strong>Cantidad de Sedes:</strong> <span id="verCantidadSedes"></span></p>
                                  <p><strong>Representante:</strong> <span id="verUsuarioRepresentante"></span></p>
                                  <p><strong>Estado:</strong> <span id="verEstadoInstitucion"></span></p>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-primary btnEditarInstitucion" data-dismiss="modal">
                              <i class="fa fa-edit"></i> Editar
                          </button>
                          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  <script src="/wissen/vistas/js/institucion.js"></script>

</body>
</html> 