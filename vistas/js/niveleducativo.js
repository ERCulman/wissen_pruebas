$(document).ready(function() {

    $(document).on('click', '.btnEditarNivelEducativo', function() {

        var id = $(this).data('id');
        console.log('ID obtenido:', id);

        $.ajax({
            url: 'ajax/obtener-nivel-educativo.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#idNivelEducativo').val(data.id);
                $('#editarCodigoNivelEducativo').val(data.codigo);
                $('#editarNombreNivelEducativo').val(data.nombre);

                $('#modalEditarNivelEducativo').modal('show');
            }
        });
    });

    // Método para Ver Nivel Educativo
    $(document).on('click', '.btnVerNivelEducativo', function() {

        var id = $(this).data('id');
        console.log('ID obtenido para ver:', id);

        $.ajax({
            url: 'ajax/obtener-nivel-educativo.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#verCodigoNivelEducativo').text(data.codigo);
                $('#verNombreNivelEducativo').text(data.nombre);

                // Guardar ID para el botón editar del modal ver
                $('.btnEditarNivelEducativo[data-dismiss="modal"]').attr('data-id', id);

                $('#modalVerNivelEducativo').modal('show');
            }
        });
    });



});