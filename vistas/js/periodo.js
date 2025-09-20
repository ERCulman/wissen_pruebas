$(document).ready(function() {

    $(document).on('click', '.btnEditarPeriodo', function() {

        var id = $(this).data('id');
        console.log('ID obtenido:', id);

        $.ajax({
            url: 'ajax/obtener-periodo.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#idPeriodo').val(data.id);
                $('#editarNombrePeriodo').val(data.nombre);
                $('#editarFechaInicio').val(data.fecha_inicio);
                $('#editarFechaFin').val(data.fecha_fin);
                $('#editarAnioLectivo').val(data.nombre_anio_lectivo);

                $('#modalEditarPeriodo').modal('show');
            }
        });
    });

    // Método para Ver Periodo
    $(document).on('click', '.btnVerPeriodo', function() {

        var id = $(this).data('id');
        console.log('ID obtenido para ver:', id);

        $.ajax({
            url: 'ajax/obtener-periodo.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#verNombrePeriodo').text(data.nombre);
                $('#verFechaInicio').text(data.fecha_inicio);
                $('#verFechaFin').text(data.fecha_fin);
                $('#verAnioLectivo').text(data.nombre_anio_lectivo);

                // Guardar ID para el botón editar del modal ver
                $('.btnEditarPeriodo[data-dismiss="modal"]').attr('data-id', id);

                $('#modalVerPeriodo').modal('show');
            }
        });
    });

});