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
                  <th style="width: 10%">No. Documento</th>
                  <th style="width: 10%">Nombres</th>
                  <th style="width: 10%">Apellidos</th>
                  <th style="width: 10%">Telefono</th>
                  <th style="width: 12%">Email</th>
                  <th style="width: 5%">Estado</th>
                  <th style="width: 10%">Rol</th>
                  <th style="width: 10%">Acciones</th>
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

                          echo'<td>'.$value["id_rol"].'</td>
                              <td>
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

    <?php include 'modales/modal-registro-usuario.php'; ?>

    <!-- =======================================
      MODAL EDITAR USUARIO
    =======================================-->

    <div id="modalEditarUsuario" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form role ="form" method="post" enctype="multipart/form-data" id="formEditarUsuario">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><i class="fa fa-pencil"></i>  Editar Usuario</h4>
            </div>
            <div class="modal-body">
              <div class="box-body" id="camposEditar">

                <!-- NUMERO DOCUMENTO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Número de Documento:</label> 
                    </div>
                  </div>
                </div>

                <!-- TIPO DOCUMENTO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Seleccione Tipo de Documento:</label> 
                    </div>
                  </div>
                </div>

                <!-- NUMERO DOCUMENTO CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" id="editarNumeroDocumento" name="editarNumeroDocumento" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- TIPO DOCUMENTO CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="editarTipoDocumento">
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
                </div>

                <!-- NOMBRES TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Nombre(s):</label> 
                    </div>
                  </div>
                </div>

                <!-- APELLIDOS TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Apellidos:</label> 
                    </div>
                  </div>
                </div>

                <!-- NOMBRE CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" id="editarNombreUsuario" name="editarNombreUsuario" value="" required> 
                    </div>
                  </div>
                </div>

                <!-- APELLIDOS CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" id="editarApellidoUsuario" name="editarApellidoUsuario" value="" required> 
                    </div>
                  </div>
                </div>

                <!-- GENERO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Seleccione el Genero del Usuario:</label> 
                    </div>
                  </div>
                </div>

                <!-- TIPO SANGRE TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Seleccione el Tipo de Sangre del Usuario:</label> 
                    </div>
                  </div>
                </div>

                <!-- GENERO CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="editarSexoUsuario">
                        <option value="" id="editarSexoUsuario"></option>
                        <option value="Masculino">MASCULINO</option>
                        <option value="Femenino">FEMENINO</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- TIPO SANGRE CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="editarRhUsuario">
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
                </div>

                <!-- FECHA NACIMIENTO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Fecha de Nacimiento:</label> 
                    </div>
                  </div>
                </div>

                <!-- EDAD TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Edad:</label> 
                    </div>
                  </div>
                </div>

                <!-- FECHA NACIMIENTO CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" class="form-control input-lg" id="editarFechaNacimiento" name="editarFechaNacimiento" value="" required> 
                    </div>
                  </div>
                </div>

                <!-- EDAD CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" id="editarEdadUsuario" name="editarEdadUsuario" value="" required> 
                    </div>
                  </div>
                </div>

                <!-- TELEFONO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Telefono:</label> 
                    </div>
                  </div>
                </div>

                <!-- EMAIL TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Email:</label> 
                    </div>
                  </div>
                </div>

                <!-- TELEFONO CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                      <input type="text" class="form-control input-lg" id="editarTelefonoUsuario" name="editarTelefonoUsuario" value="" required> 
                    </div>
                  </div>
                </div>

                <!-- EMAIL CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                      <input type="text" class="form-control input-lg" id="editarEmailUsuario" name="editarEmailUsuario" value="" required> 
                    </div>
                  </div>
                </div>

                <!-- USUARIO LOGIN TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Usuario:</label> 
                    </div>
                  </div>
                </div>

                <!-- CONTRASEÑA TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Contraseña:</label> 
                    </div>
                  </div>
                </div>

                <!-- USUARIO CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-key"></i></span>
                      <input type="text" class="form-control input-lg" id="editarLoginUsuario" name="editarLoginUsuario" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- CONTRASEÑA CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      <input type="password" class="form-control input-lg" id="editarPassword" name="editarPassword" placeholder="Nueva Contraseña">
                      <input type="hidden" id="passwordActual" name="passwordActual">
                    </div>
                  </div>
                </div>

                <!-- ESTADO TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Estado:</label> 
                    </div>
                  </div>
                </div>

                <!-- ROL TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Rol:</label> 
                    </div>
                  </div>
                </div>

                <!-- ESTADO CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="editarEstadoUsuario">
                        <option value="" id="editarEstadoUsuario"></option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                        <option value="Pendiente">Pendiente</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- ROL CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="editarRolUsuario">
                        <option value="" id="editarRolUsuario"></option>
                        <option value="Superadministrador">Superadministrador</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Rector">Rector</option>
                        <option value="Coordinador">Coordinador</option>
                        <option value="Docente">Docente</option>
                        <option value="Asistente Administrativo">Asistente Administrativo</option>
                        <option value="Estudiante">Estudiante</option>
                        <option value="Acudiente">Acudiente</option>
                        <option value="Exalumno">Exalumno</option>
                      </select>
                    </div>
                  </div>
                </div>


                <!-- <p>Aquí irán los campos del formulario para editar al usuario seleccionado.</p> -->
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
                                        <td style="border: none; padding: 10px; font-weight: bold;">Rol:</td>
                                        <td style="border: none; padding: 10px;" id="verRolUsuario"></td>
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