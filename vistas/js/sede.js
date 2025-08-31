$(document).ready(function() {

    $(document).on('click', '.btnEditarSede', function() {

        var id = $(this).data('id');
        console.log('ID obtenido:', id);

        $.ajax({
            url: 'ajax/obtener-sede.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#idSede').val(data.id);
                $('#editarNumeroSede').val(data.numero_sede);
                $('#editarTipoSede').val(data.tipo_sede);
                $('#editarNombreSede').val(data.nombre_sede);
                $('#editarCodigoDaneSede').val(data.codigo_dane);
                $('#editarConsecutivoDane').val(data.consecutivo_dane);
                $('#editarResolucionCreacionSede').val(data.resolucion_creacion);
                $('#editarFechaCreacionSede').val(data.fecha_creacion_sede);
                $('#editarDireccionSede').val(data.direccion);
                $('#editarTelefonoSede').val(data.telefono_sede);
                $('#editarCelularSede').val(data.celular_sede);
                $('#editarInstitucionSede').val(data.nombre_institucion);
                $('#editarEstadoSede').val(data.estado);

                $('#modalEditarSede').modal('show');
            }
        });
    });

    // Método para Ver Sede
    $(document).on('click', '.btnVerSede', function() {

        var id = $(this).data('id');
        console.log('ID obtenido para ver:', id);

        $.ajax({
            url: 'ajax/obtener-sede.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#verNumeroSede').text(data.numero_sede);
                $('#verTipoSede').text(data.tipo_sede);
                $('#verNombreSede').text(data.nombre_sede);
                $('#verCodigoDaneSede').text(data.codigo_dane);
                $('#verConsecutivoDane').text(data.consecutivo_dane || 'No especificado');
                $('#verResolucionCreacionSede').text(data.resolucion_creacion);
                $('#verFechaCreacionSede').text(data.fecha_creacion_sede);
                $('#verDireccionSede').text(data.direccion);
                $('#verTelefonoSede').text(data.telefono_sede || 'No especificado');
                $('#verCelularSede').text(data.celular_sede || 'No especificado');
                $('#verInstitucionSede').text(data.nombre_institucion);

                // Formatear el estado
                var estadoTexto = data.estado == "1" ? "Activo" : "Inactivo";
                $('#verEstadoSede').text(estadoTexto);

                // Guardar ID para el botón editar del modal ver
                $('.btnEditarSede[data-dismiss="modal"]').attr('data-id', id);

                $('#modalVerSede').modal('show');
            }
        });
    });

});