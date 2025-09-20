<!-- =======================================
      MODAL RECUPERAR CONTRASEÑA
    =======================================-->

<div id="modalRecuperarPassword" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" id="formNuevaRecuperacion">
        <div class="modal-header" style="background: #3c8ebd; color: white;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-key"></i> Recuperar Contraseña</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <p>Por favor, ingrese su nombre de usuario y su dirección de correo electrónico para restablecer su contraseña.</p>
            
            <!-- CAMPO PARA EL USUARIO -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control input-lg" name="usuarioRecuperar" placeholder="Ingrese su usuario" required>
              </div>
            </div>

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
          <button type="button" class="btn btn-primary" id="btnEnviarRecuperacion">Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    var procesando = false;
    
    $('#btnEnviarRecuperacion').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        if(procesando) return false;
        
        var $form = $('#formNuevaRecuperacion');
        var $btn = $(this);
        
        // Validar formulario
        if(!$form[0].checkValidity()) {
            $form[0].reportValidity();
            return false;
        }
        
        procesando = true;
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
        
        var formData = new FormData($form[0]);
        
        $.ajax({
            url: "ajax/usuarios.ajax.php",
            method: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                if (respuesta.trim() === "ok") {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Revisa tu correo!',
                        text: 'Hemos enviado un enlace a tu correo electrónico para que puedas restablecer tu contraseña.',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        $('#modalRecuperarPassword').modal('hide');
                        $form[0].reset();
                    });
                } else if (respuesta.trim() === "not-found") {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Datos no encontrados!',
                        text: 'El usuario y correo electrónico no coinciden en nuestro sistema.',
                        confirmButtonText: 'Cerrar'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Error: ' + respuesta,
                        confirmButtonText: 'Cerrar'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error de conexión!',
                    text: 'No se pudo conectar con el servidor.',
                    confirmButtonText: 'Cerrar'
                });
            },
            complete: function() {
                procesando = false;
                $btn.prop('disabled', false).html('Enviar');
            }
        });
    });
});
</script>
