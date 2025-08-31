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
                                <button class="btn btn-success btnVerUsuario" idUsuario="'.$value["id_usuario"].'" data-toggle="modal" data-target="#modalVerUsuario"><i class="fa fa-search"></i></button>
                                <button class="btn btn-warning btnEditarUsuario" idUsuario="'.$value["id_usuario"].'" data-toggle="modal" data-target="#modalEditarUsuario"><i class="fa fa-pencil"></i></button>
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

    <div id="modalAgregarUsuario" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form role ="form" method="post" enctype="multipart/form-data" id="formAgregarUsuario">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><i class="fa fa-user-plus"></i>  Agregar Usuario</h4>
            </div>

            <div class="modal-body">
              <div class="box-body" id="camposFormulario">

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
                      <input type="text" class="form-control input-lg" name="numeroDocumento" placeholder="Numero Documento" required> 
                    </div>
                  </div>
                </div>

                <!-- TIPO DOCUMENTO CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="tipoDocumento">
                        <option value="">Seleccione...</option>
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
                      <input type="text" class="form-control input-lg" name="nombreUsuario" placeholder="Nombres" required> 
                    </div>
                  </div>
                </div>

                <!-- APELLIDOS CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" name="apellidoUsuario" placeholder="Apellidos" required> 
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
                      <select class="form-control input-lg" name="sexoUsuario">
                        <option value="">Seleccione...</option>
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
                      <select class="form-control input-lg" name="rhUsuario">
                        <option value="">Seleccione...</option>
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
                      <input type="date" class="form-control input-lg" name="fechaNacimiento" placeholder="Fecha Nacimiento" required>
                    </div>
                  </div>
                </div>

                <!-- EDAD CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" name="edadUsuario" placeholder="Edad" required> 
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
                      <input type="text" class="form-control input-lg" name="telefonoUsuario" placeholder="Telefono" required> 
                    </div>
                  </div>
                </div>

                <!-- EMAIL CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                      <input type="text" class="form-control input-lg" name="emailUsuario" placeholder="Email" required> 
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
                      <input type="text" class="form-control input-lg" name="loginUsuario" placeholder="Usuario" required> 
                    </div>
                  </div>
                </div>

                <!-- CONTRASEÑA CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      <input type="password" class="form-control input-lg" name="password" placeholder="Contraseña" required>
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
                      <select class="form-control input-lg" name="estadoUsuario">
                        <option value="">Seleccione...</option>
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
                      <select class="form-control input-lg" name="rolUsuario">
                        <option value="">Seleccione...</option>
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


                
                <!-- Campos como numeroDocumento, tipoDocumento, etc -->
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
              <button type="submit" class="btn btn-primary">Registrar</button>
            </div>

            <?php

              $crearUsuario = new ControladorUsuarios();
              $crearUsuario -> ctrCrearUsuario();

            ?>

          </form>
        </div>
      </div>
    </div>

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
              <h4 class="modal-title"><i class="fa fa-search"></i>  Ver Usuario</h4>
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
                      <input type="text" class="form-control input-lg" id="verNumeroDocumento" name="verNumeroDocumento" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- TIPO DOCUMENTO CASILLA -->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <select class="form-control input-lg" name="verTipoDocumento" disabled>
                        <option value="" id="verTipoDocumento"></option>
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
                      <input type="text" class="form-control input-lg" id="verNombreUsuario" name="verNombreUsuario" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- APELLIDOS CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" id="verApellidoUsuario" name="verApellidoUsuario" value="" readonly> 
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
                      <select class="form-control input-lg" name="verSexoUsuario" disabled>
                        <option value="" id="verSexoUsuario"></option>
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
                      <select class="form-control input-lg" name="verRhUsuario" disabled>
                        <option value="" id="verRhUsuario"></option>
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
                      <input type="text" class="form-control input-lg" id="verFechaNacimiento" name="verFechaNacimiento" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- EDAD CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                      <input type="text" class="form-control input-lg" id="verEdadUsuario" name="verEdadUsuario" value="" readonly> 
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
                      <input type="text" class="form-control input-lg" id="verTelefonoUsuario" name="verTelefonoUsuario" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- EMAIL CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                      <input type="text" class="form-control input-lg" id="verEmailUsuario" name="verEmailUsuario" value="" readonly> 
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
                      <input type="text" class="form-control input-lg" id="verLoginUsuario" name="verLoginUsuario" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- CONTRASEÑA CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      <input type="password" class="form-control input-lg" id="verPassword" name="verPassword" value="" readonly> 
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
                      <select class="form-control input-lg" name="verEstadoUsuario" disabled>
                        <option value="" id="verEstadoUsuario"></option>
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
                      <select class="form-control input-lg" name="verRolUsuario" disabled>
                        <option value="" id="verRolUsuario"></option>
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

                <!-- FECHA CREACION TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Fecha Creacion:</label> 
                    </div>
                  </div>
                </div>

                <!-- FECHA ACTUALIZACION TITULO-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <label>Fecha Actualizacion:</label> 
                    </div>
                  </div>
                </div>

                <!-- FECHA CREACION CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      <input type="text" class="form-control input-lg" id="verFechaCreacion" name="verFechaCreacion" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- FECHA ACTUALIZACION CASILLA-->

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                      <input type="text" class="form-control input-lg" id="verFechaActualizacion" name="verFechaActualizacion" value="" readonly> 
                    </div>
                  </div>
                </div>

                <!-- <p>Aquí irán los campos del formulario para editar al usuario seleccionado.</p> -->
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html> 