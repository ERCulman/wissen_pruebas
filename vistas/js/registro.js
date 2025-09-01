$(document).ready(function(){
    $('#formAgregarUsuario').submit(function(e) {
        e.preventDefault();

        var datos = new FormData(this);

        $.ajax({
            url: 'ajax/usuarios.ajax.php',
            method: 'POST',
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.status === 'success') {
                    Swal.fire(
                        '¡Hecho!',
                        respuesta.message,
                        'success'
                    ).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire(
                        '¡Error!',
                        respuesta.message,
                        'error'
                    );
                }
            }
        });
    });
});
