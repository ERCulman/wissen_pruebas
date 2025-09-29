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
        <h1>Usuarios y Roles</h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
          <li class="active">Usuarios y Roles</li>
        </ol>
      </section>

      <!-- CONTENIDO PAGINA USUARIOS -->


      <section class="content">

        <!-- BOTON NUEVO USUARIO -->

        <div class="box">
          <div class="box-header with-border">
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarUsuario">
              <i class="fa fa-user-plus"></i> Nuevo Usuario
            </button>
          </div>

          <!-- CABECERA INFORMACION USUARIOS -->

          <div class="box-body">
            <table class="table table-bordered table-striped dt-responsive tablas" id="tablaUsuarios">
              <thead>
                <tr>
                  <th style="width: 5%">Id</th>
                  <th style="width: 12%">No. Documento</th>
                  <th style="width: 12%">Nombres</th>
                  <th style="width: 12%">Apellidos</th>
                  <th style="width: 12%">Telefono</th>
                  <th style="width: 15%">Email</th>
                  <th style="width: 8%">Estado</th>
                  <th style="width: 14%">Acciones</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $item = null;
                $valor = null;

                $usuarios = ControladorUsuarios::ctrMostrarUsuario($item, $valor);

                foreach ($usuarios as $key => $value) {

                  echo '<tr>
                          <td>#</td>
                          <td>'.$value["numero_documento"].'</td>
                          <td>'.$value["nombres_usuario"].'</td>
                          <td>'.$value["apellidos_usuario"].'</td>
                          <td>'.$value["telefono_usuario"].'</td>
                          <td>'.$value["email_usuario"].'</td>';

                          if($value["estado_usuario"] == 'Activo'){

                            echo '<td><button class="btn btn-success btn-xs">'.$value["estado_usuario"].'</button></td>';

                          } elseif($value["estado_usuario"] == 'Pendiente'){

                            echo '<td><button class="btn btn-warning btn-xs">'.$value["estado_usuario"].'</button></td>';

                          }else{

                            echo '<td><button class="btn btn-danger btn-xs">'.$value["estado_usuario"].'</button></td>';

                          }

                          echo'<td>
                                <button class="btn btn-info btnVerUsuario" data-id="'.$value["id_usuario"].'"><i class="fa fa-search"></i></button>
                                <button class="btn btn-warning btnEditarUsuario" data-id="'.$value["id_usuario"].'"><i class="fa fa-pencil"></i></button>
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
      MODAL AGREGAR USUARIO
    =======================================-->

    <?php include 'modales/modal-registro-usuario.php'; ?>

    <!-- =======================================
      MODAL EDITAR USUARIO
    =======================================-->

      <div id="modalEditarUsuario" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <form role="form" method="post" enctype="multipart/form-data" id="formEditarUsuario" data-validacion-universal>
                      <div class="modal-header" style="background: #3c8ebdff; color: white;">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title"><i class="fa fa-pencil"></i> Editar Usuario</h4>
                      </div>

                      <div class="modal-body">
                          <div class="box-body">

                              <div class="row">
                                  <!-- CAMPO NÚMERO DE DOCUMENTO -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Número de Documento:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                              <input type="text" class="form-control input-lg" id="editarNumeroDocumento" name="editarNumeroDocumento" readonly>
                                          </div>
                                      </div>
                                      <!-- Este campo es readonly, no necesita validación ni contenedor de error -->
                                  </div>

                                  <!-- CAMPO TIPO DE DOCUMENTO -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Tipo de Documento:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                                              <select class="form-control input-lg" name="editarTipoDocumento" data-reglas="requerido">
                                                  <option value="" id="editarTipoDocumento"></option>
                                                  <option value="CC">CC - CEDULA CIUDADANIA</option>
                                                  <option value="CE">CE - CEDULA EXTRANJERIA</option>
                                                  <option value="TI">TI - TARJETA DE IDENTIDAD</option>
                                                  <option value="RC">RC - REGISTRO CIVIL</option>
                                                  <option value="PTE">PTE - PASAPORTE</option>
                                                  <option value="TE">TE - TARJETA EXTRANJERIA</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- CAMPO NOMBRES -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Nombre(s):</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                              <input type="text" class="form-control input-lg" id="editarNombreUsuario" name="editarNombreUsuario" data-reglas="requerido|texto|min:3|max:20">
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>

                                  <!-- CAMPO APELLIDOS -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Apellidos:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                              <input type="text" class="form-control input-lg" id="editarApellidoUsuario" name="editarApellidoUsuario" data-reglas="requerido|texto|min:3|max:20">
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- CAMPO GÉNERO -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Género:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
                                              <select class="form-control input-lg" name="editarSexoUsuario" data-reglas="requerido">
                                                  <option value="" id="editarSexoUsuario"></option>
                                                  <option value="Masculino">MASCULINO</option>
                                                  <option value="Femenino">FEMENINO</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>

                                  <!-- CAMPO RH -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Tipo de Sangre (RH):</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-tint"></i></span>
                                              <select class="form-control input-lg" name="editarRhUsuario" data-reglas="requerido">
                                                  <option value="" id="editarRhUsuario"></option>
                                                  <option value="A+">A+</option>
                                                  <option value="AB+">AB+</option>
                                                  <option value="B+">B+</option>
                                                  <option value="O+">O+</option>
                                                  <option value="A-">A-</option>
                                                  <option value="AB-">AB-</option>
                                                  <option value="B-">B-</option>
                                                  <option value="O-">O-</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- CAMPO FECHA DE NACIMIENTO -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Fecha de Nacimiento:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                              <input type="text" class="form-control input-lg has-datepicker" id="editarFechaNacimiento" name="editarFechaNacimiento" placeholder="dd/mm/aaaa" data-reglas="requerido|fechaUsuario">
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>

                                  <!-- CAMPO EDAD -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Edad:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-birthday-cake"></i></span>
                                              <input type="number" class="form-control input-lg" id="editarEdadUsuario" name="editarEdadUsuario" data-reglas="requerido|minValor:1|maxValor:120" readonly>
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- CAMPO TELÉFONO -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Teléfono:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                              <input type="text" class="form-control input-lg" id="editarTelefonoUsuario" name="editarTelefonoUsuario" data-reglas="requerido|numeros|min:10|max:15">
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>

                                  <!-- CAMPO EMAIL -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Correo Electrónico:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                              <input type="email" class="form-control input-lg" id="editarEmailUsuario" name="editarEmailUsuario" data-reglas="requerido|email">
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- CAMPO USUARIO -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Usuario:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                              <input type="text" class="form-control input-lg" id="editarLoginUsuario" name="editarLoginUsuario" readonly>
                                          </div>
                                      </div>
                                  </div>

                                  <!-- CAMPO CONTRASEÑA -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Nueva Contraseña (Opcional):</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                              <input type="password" class="form-control input-lg" name="editarPassword" placeholder="Dejar en blanco para no cambiar" data-reglas="passwordFuerte">
                                              <input type="hidden" id="passwordActual" name="passwordActual">
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>
                              </div>

                              <div class="row">
                                  <!-- CAMPO ESTADO -->
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Estado:</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="fa fa-check-circle"></i></span>
                                              <select class="form-control input-lg" name="editarEstadoUsuario" data-reglas="requerido">
                                                  <option value="" id="editarEstadoUsuario"></option>
                                                  <option value="Activo">Activo</option>
                                                  <option value="Inactivo">Inactivo</option>
                                                  <option value="Pendiente">Pendiente</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="validation-error-container"></div>
                                  </div>
                              </div>

                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                      </div>

                      <?php

                      $editarUsuario = new ControladorUsuarios();
                      $editarUsuario -> ctrEditarUsuario();

                      ?>
                  </form>
              </div>
          </div>
      </div>

    <!-- =======================================
      MODAL VER USUARIO
    =======================================-->

    <div id="modalVerUsuario" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVerUsuario">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-search"></i> Ver Usuario</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table style="border: none; width: 100%;">
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold; width: 40%;">Número de Documento:</td>
                                        <td style="border: none; padding: 10px;" id="verNumeroDocumento"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Tipo de Documento:</td>
                                        <td style="border: none; padding: 10px;" id="verTipoDocumento"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Nombres:</td>
                                        <td style="border: none; padding: 10px;" id="verNombreUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Apellidos:</td>
                                        <td style="border: none; padding: 10px;" id="verApellidoUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Género:</td>
                                        <td style="border: none; padding: 10px;" id="verSexoUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Tipo de Sangre:</td>
                                        <td style="border: none; padding: 10px;" id="verRhUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Fecha de Nacimiento:</td>
                                        <td style="border: none; padding: 10px;" id="verFechaNacimiento"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Edad:</td>
                                        <td style="border: none; padding: 10px;" id="verEdadUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Teléfono:</td>
                                        <td style="border: none; padding: 10px;" id="verTelefonoUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Email:</td>
                                        <td style="border: none; padding: 10px;" id="verEmailUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Usuario (Login):</td>
                                        <td style="border: none; padding: 10px;" id="verLoginUsuario"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Estado:</td>
                                        <td style="border: none; padding: 10px;" id="verEstadoUsuario"></td>
                                    </tr>

                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Fecha de Creación:</td>
                                        <td style="border: none; padding: 10px;" id="verFechaCreacion"></td>
                                    </tr>
                                    <tr style="border: none;">
                                        <td style="border: none; padding: 10px; font-weight: bold;">Última Actualización:</td>
                                        <td style="border: none; padding: 10px;" id="verFechaActualizacion"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnEditarUsuario" data-dismiss="modal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </div>
</body>
</html>