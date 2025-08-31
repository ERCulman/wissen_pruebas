 <!-- PAGINA USUARIOS -->

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Usuarios y Roles
      </h1>
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

          <button class="btn btn-primary" data-toggle="modal" data-target="#modalVerUsuario">            
            <i class="fa fa-search"></i> Ver Usuario
          </button>

          <button class="btn btn-primary" data-toggle="modal" data-target="#modalVerUsuario">            
            <i class="fa fa-pencil"></i> Editar Usuario
          </button>

          <button class="btn btn-primary" data-toggle="modal" data-target="#modalVerUsuario">            
            <i class="fa fa-trash"></i> Eliminar Usuario
          </button>          
        </div>

        <!-- CABECERA INFORMACION USUARIOS -->

        <div class="box-body">

          <div class="table-responsive">

            <table class="table table-bordered table-striped dt-responsive tablas" style="width: 100%;">
              
              <thead>              
                <tr> 
                  <th>Id</th>
                  <th>No. Documento</th>
                  <th>Tipo Documento</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Genero</th>
                  <th>Telefono</th>
                  <th>Email</th>
                  <th>Fecha Creacion</th>
                  <th>Fecha Actualizacion</th>
                  <th>Estado</th>
                  <th>Rol</th>
                  <th>Foto</th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td>#</td>
                  <td>94090613833</td>
                  <td>TI</td>
                  <td>CATALINA</td>
                  <td>PEREZ FONSECA</td>
                  <td>FEMENINO</td>
                  <td>3208180238</td>
                  <td>catalinaperez@gmail.com</td>
                  <td>1</td>
                  <td>1</td>
                  <td><button class="btn btn-success btn-xs">Activo</button></td>
                  <td>SuperAdmin</td>
                  <td><img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail" width="40px"></td>
                </tr>
                
                <tr>
                  <td>#</td>
                  <td>94090613833</td>
                  <td>TI</td>
                  <td>TOMAS</td>
                  <td>PEREZ FONSECA</td>
                  <td>FEMENINO</td>
                  <td>3208180238</td>
                  <td>catalinaperez@gmail.com</td>
                  <td>1</td>
                  <td>1</td>
                  <td><button class="btn btn-success btn-xs">Activo</button></td>
                  <td>SuperAdmin</td>
                  <td><img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail" width="40px"></td>
                </tr>
              </tbody> 

            </table>
          
          </div>

        </div>

      </div>

    </section>

  </div>

 <!-- MODAL AGREGAR USUARIO -->

 <!-- The Modal -->

<div id="modalAgregarUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role ="form" method="post" enctype="multipart/form-data">  
    
      <!-- Cabecera Modal -->

        <div class="modal-header" style="background: #3c8ebdff; color: white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Usuario</h4>
        </div>

        <!-- Nuemero documento -->

        <div class="modal-body">          
          <div class="box-body">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="numeroDocumento" placeholder="numero_docuemento">             
              </div>              
            </div>

            <!-- Tipo Documento -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="tipoDocumento" placeholder="tipo_documento">                
              </div>              
            </div>

               <!-- Nombre Usuarios -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="nombreUsuario" placeholder="nombres_usuario">                
              </div>              
            </div>

            <!-- Apellido Usuarios -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="apellidoUsuario" placeholder="apellido_usuario">                
              </div>              
            </div>

            <!-- Sexo Usuarios -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="sexoUsuario" placeholder="sexo_usuario">                
              </div>              
            </div>

            <!-- Tipo De Sange -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="rhUsuario" placeholder="rh_usuario">                
              </div>              
            </div>

            <!-- Fecha Nacimiento Usuario -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="fechaNacimiento" placeholder="fecha_nacimiento">                
              </div>              
            </div>

            <!-- Edad Usuarios -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="edadUsuario" placeholder="edad_usuario">                
              </div>              
            </div>

            <!-- Telefono Usuarios -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="telefonoUsuario" placeholder="telefono_usuario">                
              </div>              
            </div>

            <!-- Email Usuarios -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="emailUsuario" placeholder="email_usuario">                
              </div>              
            </div>

            <!-- LOGIN DE USUARIO -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input type="text" class="form-control input-lg" name="loginUsuario" placeholder="usuario">                
              </div>              
            </div>

            <!-- CONTRASEÑA -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="text" class="form-control input-lg" name="Contrasena" placeholder="password">                
              </div>              
            </div>

            <!-- Fecha De Creacion -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="fechaCreacion" placeholder="fecha_creacion">                
              </div>              
            </div>

            <!-- Fecha De Actualizacion -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="fechaActualizacion" placeholder="fecha_actualizacion">                
              </div>              
            </div>

          <!-- Estado -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="idEstado" placeholder="estado_usuario">                
              </div>              
            </div>

            <!-- Rol - Pefiles -->

            <!-- Rol - Perfiles -->
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-user-plus"></i></span>
                </div>
                <select class="form-control input-lg" name="NuevoPerfil">
                  <option value="">Seleccionar Perfil</option>
                  <option value="Superadministrador">Superadministrador</option>
                  <option value="Administrador">Administrador</option>
                  <option value="Rector">Rector</option>
                  <option value="Coordinador">Coordinador</option>
                  <option value="Docente">Docente</option>
                  <option value="Secretario">Secretario</option>
                  <option value="Estudiante">Estudiante</option>
                  <option value="Acudiente">Acudiente</option>
                </select>
              </div>
            </div>

            <!-- Etnia -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                <input type="text" class="form-control input-lg" name="Etnia" placeholder="etnia_usuario">                
              </div>              
            </div>

            <!-- Foto -->

            <div class="form-group">
              <div class="panel">SUBIR FOTO</div>
              <input type="file" id="nuevaFoto" name="nuevaFoto">
              <p class="help-block">Peso Maximo 200 MB</p>
              <img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail" width="100px">
             
            </div>
            
          </div>

        </div>

        <!-- Boton Registrar -->

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Registrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

