<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Wissen System | Restablecer Contraseña</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="vistas/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="vistas/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="vistas/dist/css/AdminLTE.css">
  <link rel="stylesheet" href="vistas/plugins/iCheck/square/blue.css">
</head>
<body class="hold-transition login-page">

<?php
require_once "controladores/usuarios.controlador.php";
require_once "modelos/usuarios.modelo.php";

$token = isset($_GET['token']) ? $_GET['token'] : '';
$usuario = null;

if($token) {
    $tabla = "usuarios";
    $usuario = ModeloUsuarios::mdlBuscarUsuarioPorToken($tabla, $token);
}
?>

<div class="login-box">
  <div class="login-logo">
    <b>Wissen</b> System
  </div>
  
  <div class="login-box-body">
    <?php if($usuario): ?>
      <p class="login-box-msg">Ingresa tu nueva contraseña</p>
      
      <form method="post">
        <input type="hidden" name="reset_token" value="<?php echo $token; ?>">
        
        <div class="form-group has-feedback">
          <input type="password" class="form-control" name="new_password" placeholder="Nueva contraseña" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        
        <div class="form-group has-feedback">
          <input type="password" class="form-control" name="confirm_password" placeholder="Confirmar contraseña" required>
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        
        <div class="row">
          <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Actualizar Contraseña</button>
          </div>
        </div>
      </form>
      
      <?php
      $resetPassword = new ControladorUsuarios();
      $resetPassword->ctrResetPassword();
      ?>
      
    <?php else: ?>
      <div class="alert alert-danger">
        <h4><i class="icon fa fa-ban"></i> Token inválido o expirado</h4>
        El enlace de recuperación no es válido o ha expirado. Por favor, solicita un nuevo restablecimiento de contraseña.
      </div>
      <a href="index.php" class="btn btn-primary btn-block">Volver al inicio</a>
    <?php endif; ?>
    
  </div>
</div>

<script src="vistas/bower_components/jquery/dist/jquery.min.js"></script>
<script src="vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>