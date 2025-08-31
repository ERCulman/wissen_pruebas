$(document).ready(function() {

    $(document).on('click', '.btnEditarCurso', function() {

        var id = $(this).data('id');
        console.log('ID obtenido:', id);

        $.ajax({
            url: 'ajax/obtener-curso.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#idCurso').val(data.id);
                $('#editarTipoCurso').val(data.tipo);
                $('#editarNombreCurso').val(data.nombre);

                $('#modalEditarCurso').modal('show');
            }
        });
    });

    // Método para Ver Curso
    $(document).on('click', '.btnVerCurso', function() {

        var id = $(this).data('id');
        console.log('ID obtenido para ver:', id);

        $.ajax({
            url: 'ajax/obtener-curso.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#verTipoCurso').text(data.tipo);
                $('#verNombreCurso').text(data.nombre);

                // Guardar ID para el botón editar del modal ver
                $('.btnEditarCurso[data-dismiss="modal"]').attr('data-id', id);

                $('#modalVerCurso').modal('show');
            }
        });
    });

});