<style>
  .login-box {
    width: 430px; /* Ancho original 360px, aumentado ~20% */
  }

  .login-box-msg {
    font-size: 24px; /* Aumentar tamaño de fuente del título */
    padding-bottom: 20px;
  }

  .form-control, .btn {
    font-size: 16px; /* Aumentar tamaño de fuente base */
  }

  .form-control {
      height: auto; /* Permitir que el padding defina la altura */
      padding: 10px;
  }

  .login-links {
    margin-top: 20px;
  }

  .login-links a {
    font-size: 16px; /* Tamaño de fuente para los enlaces */
    color: #3c8dbc; /* Color azul primario de AdminLTE */
  }

  .login-links a:hover {
    color: #367fa9; /* Azul un poco más oscuro para hover */
    text-decoration: none;
  }

  .login-links .fa {
    margin-right: 5px;
  }
</style>

<!-- PAGINA LOGIN -->

<div id="back"></div>
<div class="login-box">

  <div class="login-logo">
    <img src="vistas/img/plantilla/logo_horizontal.png" class="img-responsive" style="padding: 100px 100px 0px 100px;">
  </div>

  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Ingresar al Sistema</p>

    <form method="post">

      <!-- CAJA DE TEXTO PARA COLOCAR EL USUARIO -->

      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Usuario" name="ingUsuario" required> <!-- VARIABLE NAME="ingUsuario" PARA LA CAPTURA DEL DATO-->
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>

      <!-- CAJA DE TEXTO PARA COLOCAR LA CONTRASEÑA -->

      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Contraseña" name="ingPassword">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="row">

       <!-- BOTON DE INGRESO --> 
        
        <div class="col-xs-6 col-xs-offset-3">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar</button>
        </div>

      </div>

      <?php

        $login = new ControladorUsuarios();
        $login -> ctrIngresoUsuario();

      ?>
    </form>

    <div class="row login-links">
      <div class="col-xs-6">
        <a href="#" data-toggle="modal" data-target="#modalRecuperarPassword"><i class="fa fa-lock"></i> Olvidé mi contraseña</a>
      </div>
      <div class="col-xs-6 text-right">
        <a href="#" data-toggle="modal" data-target="#modalAgregarUsuario"><i class="fa fa-user-plus"></i> Registro</a>
      </div>
    </div>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<?php 
  include 'modales/modal-registro-usuario.php'; 
  include 'modales/modal-recuperar-password.php';
?>