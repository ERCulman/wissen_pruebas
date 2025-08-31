$(document).ready(function() {

    $(document).on('click', '.btnEditarGrado', function() {

        var id = $(this).data('id');
        console.log('ID obtenido:', id);

        $.ajax({
            url: 'ajax/obtener-grado.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#idGrado').val(data.id);
                $('#editarNumeroGrado').val(data.numero);
                $('#editarNombreGrado').val(data.nombre);
                $('#editarNivelEducativoGrado').val(data.nombre_nivel_educativo);

                $('#modalEditarGrado').modal('show');
            }
        });
    });

    // Método para Ver Grado
    $(document).on('click', '.btnVerGrado', function() {

        var id = $(this).data('id');
        console.log('ID obtenido para ver:', id);

        $.ajax({
            url: 'ajax/obtener-grado.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#verNumeroGrado').text(data.numero);
                $('#verNombreGrado').text(data.nombre);
                $('#verNivelEducativoGrado').text(data.nombre_nivel_educativo);

                // Guardar ID para el botón editar del modal ver
                $('.btnEditarGrado[data-dismiss="modal"]').attr('data-id', id);

                $('#modalVerGrado').modal('show');
            }
        });
    });

});