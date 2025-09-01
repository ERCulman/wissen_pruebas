<?php
require_once "controladores/usuarios.controlador.php";
require_once "modelos/usuarios.modelo.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Wissen | Restablecer Contraseña</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="vistas/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="vistas/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="vistas/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-logo">
    <img src="vistas/img/plantilla/logo_horizontal.png" class="img-responsive" style="padding: 100px 100px 0px 100px;">
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Restablecer tu Contraseña</p>

    <form method="post">

      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="reset_token" placeholder="Token de recuperación" required value="<?php echo isset($_GET['token']) ? $_GET['token'] : ''; ?>">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="new_password" placeholder="Nueva contraseña" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="confirm_password" placeholder="Confirmar nueva contraseña" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Restablecer</button>
        </div>
      </div>

      <?php
        $reset = new ControladorUsuarios();
        $reset -> ctrResetPassword();
      ?>

    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="vistas/bower_components/jquery/dist/jquery.min.js"></script>
<script src="vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
