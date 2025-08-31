$(document).ready(function() {

    $(document).on('click', '.btnEditarInstitucion', function() {

        var id = $(this).data('id');
        console.log('ID obtenido:', id);

        $.ajax({
            url: 'ajax/obtener-institucion.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#idInstitucion').val(data.id);
                $('#editarNombreInstitucion').val(data.nombre);
                $('#editarCodigoDane').val(data.codigo_dane);
                $('#editarNIT').val(data.nit);
                $('#editarResolucionCreacion').val(data.resolucion_creacion);
                $('#editarDireccion').val(data.direccion);
                $('#editarEmail').val(data.email);
                $('#editarTelefono').val(data.telefono);
                $('#editarCantidadSedes').val(data.cantidad_sedes);
                $('#editarUsuarioRepresentante').val(data.nombre_representante);
                $('#editarEstadoInstitucion').val(data.estado);

                $('#modalEditarInstitucion').modal('show');
            }
        });
    });

    // Método para Ver Institución
    $(document).on('click', '.btnVerInstitucion', function() {

        var id = $(this).data('id');
        console.log('ID obtenido para ver:', id);

        $.ajax({
            url: 'ajax/obtener-institucion.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {

                $('#verNombreInstitucion').text(data.nombre);
                $('#verCodigoDane').text(data.codigo_dane);
                $('#verNIT').text(data.nit);
                $('#verResolucionCreacion').text(data.resolucion_creacion);
                $('#verDireccion').text(data.direccion);
                $('#verEmail').text(data.email);
                $('#verTelefono').text(data.telefono);
                $('#verCantidadSedes').text(data.cantidad_sedes);
                $('#verUsuarioRepresentante').text(data.nombre_representante);

                // Formatear el estado
                var estadoTexto = data.estado == "1" ? "Activo" : "Inactivo";
                $('#verEstadoInstitucion').text(estadoTexto);

                // Guardar ID para el botón editar del modal ver
                $('.btnEditarInstitucion[data-dismiss="modal"]').attr('data-id', id);

                $('#modalVerInstitucion').modal('show');
            }
        });
    });

});

