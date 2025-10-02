$(document).ready(function() {
    var docenteSeleccionado = null;
    var sedeSeleccionada = null;

    $("#selectSede").change(function() {
        var sedeId = $(this).val();
        sedeSeleccionada = sedeId;
        
        if(sedeId) {
            cargarDocentes(sedeId);
            cargarAsignaturas(sedeId);
            $("#contenidoDocentes").show();
        } else {
            $("#contenidoDocentes").hide();
            $("#contenidoAsignaciones").hide();
        }
    });

    function cargarDocentes(sedeId) {
        console.log("Cargando docentes para sede:", sedeId);
        
        $.ajax({
            url: "ajax/asignacion-docente-asignaturas.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerDocentes",
                sede_id: sedeId
            },
            dataType: "json",
            success: function(respuesta) {
                console.log("Respuesta docentes:", respuesta);
                
                var tbody = $("#tablaDocentes tbody");
                tbody.empty();
                
                if(respuesta && respuesta.length > 0) {
                    $.each(respuesta, function(index, docente) {
                        var horasActuales = docente.max_horas_academicas_semanales || 20;
                        var botones = '<button class="btn btn-sm btn-info btnSeleccionarDocente" data-id="' + docente.id + '" data-cuerpo="' + docente.cuerpo_docente_id + '">Seleccionar</button>';
                        
                        if(docente.cuerpo_docente_id) {
                            botones += ' <button class="btn btn-sm btn-warning btnActualizarHoras" data-cuerpo="' + docente.cuerpo_docente_id + '" data-docente="' + docente.id + '">Actualizar Horas</button>';
                        }
                        
                        var fila = '<tr>' +
                            '<td>' + docente.numero_documento + '</td>' +
                            '<td>' + docente.nombres_usuario + ' ' + docente.apellidos_usuario + '</td>' +
                            '<td><input type="number" class="form-control input-sm horasSemanales" min="1" max="40" value="' + horasActuales + '" data-docente="' + docente.id + '" style="width: 80px;"></td>' +
                            '<td>' + botones + '</td>' +
                            '</tr>';
                        tbody.append(fila);
                    });
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center">No hay docentes registrados en esta sede</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error cargando docentes:", error);
                console.error("Respuesta:", xhr.responseText);
            }
        });
    }

    function cargarAsignaturas(sedeId) {
        $.ajax({
            url: "ajax/asignacion-docente-asignaturas.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerAsignaturas",
                sede_id: sedeId
            },
            dataType: "json",
            success: function(respuesta) {
                var contenedor = $("#listaAsignaturas");
                contenedor.empty();
                
                $.each(respuesta, function(index, asignatura) {
                    var checkbox = '<div class="checkbox">' +
                        '<label><input type="checkbox" name="asignaturas[]" value="' + asignatura.id + '"> ' + asignatura.area + ' - ' + asignatura.asignatura + '</label>' +
                        '</div>';
                    contenedor.append(checkbox);
                });
            }
        });
    }

    $(document).on("click", ".btnSeleccionarDocente", function() {
        docenteSeleccionado = $(this).data("id");
        var cuerpoDocenteId = $(this).data("cuerpo");
        
        $(".btnSeleccionarDocente").removeClass("btn-success").addClass("btn-info").text("Seleccionar");
        $(this).removeClass("btn-info").addClass("btn-success").text("Seleccionado");
        
        // Habilitar todos los checkboxes primero
        $("input[name='asignaturas[]']").prop("disabled", false);
        
        $("#btnAsignar").show();
        
        if(cuerpoDocenteId) {
            cargarAsignaciones(cuerpoDocenteId);
            $("#contenidoAsignaciones").show();
        } else {
            $("#contenidoAsignaciones").hide();
        }
    });

    function cargarAsignaciones(cuerpoDocenteId) {
        $.ajax({
            url: "ajax/asignacion-docente-asignaturas.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerAsignaciones",
                cuerpo_docente_id: cuerpoDocenteId
            },
            dataType: "json",
            success: function(respuesta) {
                var tbody = $("#tablaAsignaciones tbody");
                tbody.empty();
                
                var asignaturasAsignadas = [];
                
                $.each(respuesta, function(index, asignacion) {
                    var fila = '<tr>' +
                        '<td>' + asignacion.area + '</td>' +
                        '<td>' + asignacion.asignatura + '</td>' +
                        '<td><button class="btn btn-sm btn-danger btnEliminarAsignacion" data-id="' + asignacion.id + '">Eliminar</button></td>' +
                        '</tr>';
                    tbody.append(fila);
                    
                    // Guardar ID de estructura curricular para deshabilitar checkbox
                    asignaturasAsignadas.push(asignacion.estructura_curricular_id);
                });
                
                // Deshabilitar checkboxes de asignaturas ya asignadas
                $.each(asignaturasAsignadas, function(index, estructuraId) {
                    $("input[name='asignaturas[]'][value='" + estructuraId + "']").prop("disabled", true).closest(".checkbox").addClass("text-muted");
                });
            }
        });
    }

    $("#btnAsignar").click(function() {
        var asignaturasSeleccionadas = [];
        $("input[name='asignaturas[]']:checked").each(function() {
            asignaturasSeleccionadas.push($(this).val());
        });
        
        if(asignaturasSeleccionadas.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar al menos una asignatura',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Desea asignar las asignaturas seleccionadas al docente?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.value) {
                // Obtener las horas del input correspondiente al docente seleccionado
                var horasSemanales = $("input.horasSemanales[data-docente='" + docenteSeleccionado + "']").val();
                
                $.ajax({
                    url: "ajax/asignacion-docente-asignaturas.ajax.php",
                    method: "POST",
                    data: {
                        accion: "asignarAsignaturas",
                        rol_institucional_id: docenteSeleccionado,
                        asignaturas: asignaturasSeleccionadas,
                        max_horas: horasSemanales
                    },
                    dataType: "json",
                    success: function(respuesta) {
                        console.log("Respuesta asignación:", respuesta);
                        
                        if(respuesta == "ok") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Asignaturas asignadas correctamente',
                                confirmButtonText: 'Aceptar'
                            });
                            $("input[name='asignaturas[]']").prop("checked", false);
                            cargarDocentes(sedeSeleccionada);
                            // Recargar asignaciones si hay un docente con cuerpo docente
                            var cuerpoDocenteId = $(".btnSeleccionarDocente.btn-success").data("cuerpo");
                            if(cuerpoDocenteId) {
                                cargarAsignaciones(cuerpoDocenteId);
                            }
                        } else if(respuesta == "sin_cambios") {
                            Swal.fire({
                                icon: 'info',
                                title: 'Sin cambios',
                                text: 'Las asignaturas seleccionadas ya están asignadas al docente',
                                confirmButtonText: 'Aceptar'
                            });
                        } else if(respuesta && respuesta.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: respuesta.error,
                                confirmButtonText: 'Aceptar'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al asignar asignaturas. Verifique los datos e inténtelo nuevamente.',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error AJAX:", error);
                        console.error("Respuesta del servidor:", xhr.responseText);
                        
                        var mensajeError = "Error al asignar asignaturas";
                        try {
                            var respuestaError = JSON.parse(xhr.responseText);
                            if(respuestaError.error) {
                                mensajeError = respuestaError.error;
                            }
                        } catch(e) {
                            // Si no se puede parsear la respuesta, usar mensaje genérico
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: mensajeError,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });
    });

    $(document).on("click", ".btnEliminarAsignacion", function() {
        var asignacionId = $(this).data("id");
        var fila = $(this).closest("tr");
        
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Desea eliminar esta asignación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.value) {
                $.ajax({
                    url: "ajax/asignacion-docente-asignaturas.ajax.php",
                    method: "POST",
                    data: {
                        accion: "eliminarAsignacion",
                        asignacion_id: asignacionId
                    },
                    dataType: "json",
                    success: function(respuesta) {
                        if(respuesta == "ok") {
                            fila.remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'Asignación eliminada correctamente',
                                confirmButtonText: 'Aceptar'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al eliminar la asignación',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    }
                });
            }
        });
    });

    $(document).on("click", ".btnActualizarHoras", function() {
        var cuerpoDocenteId = $(this).data("cuerpo");
        var docenteId = $(this).data("docente");
        var horasSemanales = $("input.horasSemanales[data-docente='" + docenteId + "']").val();
        
        if(!horasSemanales || horasSemanales < 1 || horasSemanales > 40) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las horas semanales deben estar entre 1 y 40',
                confirmButtonText: 'Aceptar'
            });
            return;
        }
        
        Swal.fire({
            title: '¿Actualizar horas?',
            text: '¿Desea actualizar las horas semanales a ' + horasSemanales + ' horas?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.value) {
                $.ajax({
                    url: "ajax/asignacion-docente-asignaturas.ajax.php",
                    method: "POST",
                    data: {
                        accion: "actualizarHorasSemanales",
                        cuerpo_docente_id: cuerpoDocenteId,
                        horas_semanales: horasSemanales
                    },
                    dataType: "json",
                    success: function(respuesta) {
                        if(respuesta == "ok") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Horas semanales actualizadas correctamente',
                                confirmButtonText: 'Aceptar'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al actualizar las horas semanales',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    }
                });
            }
        });
    });
});