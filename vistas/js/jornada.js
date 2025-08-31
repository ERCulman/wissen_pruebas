$(document).ready(function() {

    $(document).on('click', '.btnEditarJornada', function() {

        var id = $(this).data('id');
        console.log('ID obtenido:', id);

        $.ajax({
            url: 'ajax/obtener-jornada.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#idJornada').val(data.id);
                $('#editarCodigoJornada').val(data.codigo);
                $('#editarNombreJornada').val(data.nombre);

                $('#modalEditarJornada').modal('show');
            }
        });
    });

    // Método para Ver Jornada
    $(document).on('click', '.btnVerJornada', function() {

        var id = $(this).data('id');
        console.log('ID obtenido para ver:', id);

        $.ajax({
            url: 'ajax/obtener-jornada.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#verCodigoJornada').text(data.codigo);
                $('#verNombreJornada').text(data.nombre);

                // Guardar ID para el botón editar del modal ver
                $('.btnEditarJornada[data-dismiss="modal"]').attr('data-id', id);

                $('#modalVerJornada').modal('show');
            }
        });
    });

});