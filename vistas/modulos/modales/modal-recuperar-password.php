<!-- =======================================
      MODAL RECUPERAR CONTRASEÑA
    =======================================-->

<div id="modalRecuperarPassword" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" id="formRecuperarPassword">
        <div class="modal-header" style="background: #3c8ebd; color: white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-key"></i> Recuperar Contraseña</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <p>Por favor, ingrese su dirección de correo electrónico y le enviaremos un enlace para restablecer su contraseña.</p>
            <!-- CAMPO PARA EL CORREO -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control input-lg" name="emailRecuperar" placeholder="Ingrese su correo electrónico" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>
