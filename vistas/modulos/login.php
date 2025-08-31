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
        
        <div class="col-xs-4 col-xs-offset-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar</button>
        </div>

      </div>

      <?php

        $login = new ControladorUsuarios();
        $login -> ctrIngresoUsuario();

      ?>
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->