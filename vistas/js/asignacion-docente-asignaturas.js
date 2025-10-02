$(document).ready(function() {
    var docenteSeleccionado = null;
    var sedeSeleccionada = null;
    var institucionSeleccionada = null;

    // Cargar instituciones automáticamente si existe el select de instituciones (superadmin)
    if ($("#selectInstitucion").length > 0) {
        cargarInstituciones();
    }

    function cargarInstituciones() {
        $.ajax({
            url: "ajax/asignacion-docente-asignaturas.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerInstituciones"
            },
            dataType: "json",
            success: function(instituciones) {
                var selectInstitucion = $("#selectInstitucion");
                selectInstitucion.html('<option value="">Seleccione una institución</option>');
                if (instituciones && instituciones.length > 0) {
                    $.each(instituciones, function(index, institucion) {
                        selectInstitucion.append('<option value="' + institucion.id + '">' + institucion.nombre + '</option>');
                    });
                }
            },
            error: function() {
                console.error("Error al cargar las instituciones.");
            }
        });
    }

    // NUEVO: Manejador para el dropdown de instituciones (solo existe para admins)
    $("#selectInstitucion").change(function() {
        var institucionId = $(this).val();
        institucionSeleccionada = institucionId;
        var selectSede = $("#selectSede");

        // Limpiar todo al cambiar de institución
        selectSede.html('<option value="">Seleccione una sede</option>');
        $("#contenidoDocentes").hide();
        $("#contenidoAsignaciones").hide();

        if (institucionId) {
            $.ajax({
                url: "ajax/asignacion-docente-asignaturas.ajax.php",
                method: "POST",
                // Esta nueva acción debe ser manejada en tu archivo AJAX
                data: {
                    accion: "obtenerSedesPorInstitucion",
                    institucion_id: institucionId
                },
                dataType: "json",
                beforeSend: function() {
                    selectSede.prop("disabled", true);
                },
                success: function(sedes) {
                    if (sedes && sedes.length > 0) {
                        $.each(sedes, function(index, sede) {
                            selectSede.append('<option value="' + sede.id + '">' + sede.nombre_sede + '</option>');
                        });
                        selectSede.prop("disabled", false);
                    }
                },
                error: function() {
                    console.error("Error al cargar las sedes de la institución.");
                    selectSede.prop("disabled", true);
                }
            });
        } else {
            selectSede.prop("disabled", true);
        }
    });


    // MODIFICADO: Este manejador ahora funciona tanto para admin como para rol institucional
    $("#selectSede").change(function() {
        var sedeId = $(this).val();
        sedeSeleccionada = sedeId;

        if (sedeId) {
            cargarDocentes(sedeId);
            cargarAsignaturas(sedeId);
            $("#contenidoDocentes").show();
            // Resetear la selección de docente y las asignaciones al cambiar de sede
            $("#contenidoAsignaciones").hide();
            docenteSeleccionado = null;
        } else {
            $("#contenidoDocentes").hide();
            $("#contenidoAsignaciones").hide();
        }
    });

    function cargarDocentes(sedeId) {
        $.ajax({
            url: "ajax/asignacion-docente-asignaturas.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerDocentes",
                sede_id: sedeId
            },
            dataType: "json",
            success: function(respuesta) {
                var tbody = $("#tablaDocentes tbody");
                tbody.empty();
                if (respuesta && respuesta.length > 0) {
                    $.each(respuesta, function(index, docente) {
                        var horasActuales = docente.max_horas_academicas_semanales || 20;
                        var botones = '<button class="btn btn-sm btn-info btnSeleccionarDocente" data-id="' + docente.id + '" data-cuerpo="' + docente.cuerpo_docente_id + '">Seleccionar</button>';
                        if (docente.cuerpo_docente_id) {
                            botones += ' <button class="btn btn-sm btn-warning btnActualizarHoras" data-cuerpo="' + docente.cuerpo_docente_id + '" data-docente="' + docente.id + '">Actualizar Horas</button>';
                        }
                        var fila = '<tr>' +
                            '<td>' + docente.numero_documento + '</td>' +
                            '<td>' + docente.nombres_usuario + ' ' + docente.apellidos_usuario + '</td>' +
                            '<td><input type="number" class="form-control input-sm horasSemanales" min="1" max="40" value="' + horasActuales + '" data-docente="' + docente.id + '" style="width: 70px;"></td>' +
                            '<td>' + botones + '</td>' +
                            '</tr>';
                        tbody.append(fila);
                    });
                } else {
                    tbody.append('<tr><td colspan="4" class="text-center">No hay docentes registrados en esta sede</td></tr>');
                }
            },
            error: function(xhr) {
                console.error("Error cargando docentes:", xhr.responseText);
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
                if (respuesta && respuesta.length > 0) {
                    $.each(respuesta, function(index, asignatura) {
                        var checkbox = '<div class="checkbox">' +
                            '<label><input type="checkbox" name="asignaturas[]" value="' + asignatura.id + '"> ' + asignatura.area + ' - ' + asignatura.asignatura + '</label>' +
                            '</div>';
                        contenedor.append(checkbox);
                    });
                } else {
                    contenedor.html('<p class="text-muted">No hay asignaturas disponibles para esta sede.</p>');
                }
            }
        });
    }

    $(document).on("click", ".btnSeleccionarDocente", function() {
        docenteSeleccionado = $(this).data("id");
        var cuerpoDocenteId = $(this).data("cuerpo");
        
        // Obtener el nombre del docente de la fila
        var nombreDocente = $(this).closest("tr").find("td:eq(1)").text();
        
        $(".btnSeleccionarDocente").removeClass("btn-success").addClass("btn-info").text("Seleccionar");
        $(this).removeClass("btn-info").addClass("btn-success").text("Seleccionado");

        // Habilitar y desmarcar todos los checkboxes antes de cargar las nuevas asignaciones
        $("input[name='asignaturas[]']").prop("disabled", false).prop("checked", false).closest(".checkbox").removeClass("text-muted");

        $("#btnAsignar").show();
        
        // Actualizar el título con el nombre del docente
        $("#tituloAsignaciones").text("Asignaturas Asignadas a " + nombreDocente);

        if (cuerpoDocenteId) {
            cargarAsignaciones(cuerpoDocenteId);
            $("#contenidoAsignaciones").show();
        } else {
            $("#contenidoAsignaciones").hide();
            $("#tablaAsignaciones tbody").empty(); // Limpiar tabla si no hay cuerpo docente
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
                var asignaturasAsignadasIds = [];

                if (respuesta && respuesta.length > 0) {
                    $.each(respuesta, function(index, asignacion) {
                        var fila = '<tr>' +
                            '<td>' + asignacion.area + '</td>' +
                            '<td>' + asignacion.asignatura + '</td>' +
                            '<td><button class="btn btn-sm btn-danger btnEliminarAsignacion" data-id="' + asignacion.id + '">Eliminar</button></td>' +
                            '</tr>';
                        tbody.append(fila);
                        asignaturasAsignadasIds.push(asignacion.estructura_curricular_id.toString());
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">Este docente aún no tiene asignaturas asignadas.</td></tr>');
                }

                // Deshabilitar checkboxes de asignaturas ya asignadas
                $("input[name='asignaturas[]']").each(function() {
                    if (asignaturasAsignadasIds.includes($(this).val())) {
                        $(this).prop("disabled", true).closest(".checkbox").addClass("text-muted");
                    }
                });
            }
        });
    }

    // El resto de los manejadores de eventos (btnAsignar, btnEliminarAsignacion, btnActualizarHoras)
    // permanecen exactamente iguales, ya que su lógica no se ve afectada por los cambios de roles.

    $("#btnAsignar").click(function() {
        var asignaturasSeleccionadas = [];
        $("input[name='asignaturas[]']:checked").each(function() {
            asignaturasSeleccionadas.push($(this).val());
        });

        if (asignaturasSeleccionadas.length === 0) {
            Swal.fire('Error', 'Debe seleccionar al menos una asignatura para asignar.', 'error');
            return;
        }

        Swal.fire({
            title: '¿Está seguro?',
            text: 'Se asignarán las asignaturas seleccionadas al docente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
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
                        if (respuesta == "ok") {
                            Swal.fire('¡Éxito!', 'Asignaturas asignadas correctamente.', 'success');
                            cargarDocentes(sedeSeleccionada);
                            var cuerpoDocenteId = $(".btnSeleccionarDocente.btn-success").data("cuerpo");
                            if (cuerpoDocenteId) {
                                cargarAsignaciones(cuerpoDocenteId);
                            }
                        } else {
                            Swal.fire('Error', 'Ocurrió un error al asignar las asignaturas. ' + (respuesta.error || ''), 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error de Servidor', 'No se pudo comunicar con el servidor: ' + xhr.responseText, 'error');
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
            text: 'Esta acción no se puede revertir.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "ajax/asignacion-docente-asignaturas.ajax.php",
                    method: "POST",
                    data: {
                        accion: "eliminarAsignacion",
                        asignacion_id: asignacionId
                    },
                    dataType: "json",
                    success: function(respuesta) {
                        if (respuesta == "ok") {
                            fila.remove();
                            Swal.fire('Eliminado', 'La asignación ha sido eliminada.', 'success');
                            // Volver a cargar las asignaturas disponibles para reactivar el checkbox
                            cargarAsignaturas(sedeSeleccionada);
                            // Seleccionar de nuevo el docente para refrescar su estado
                            var cuerpoDocenteId = $(".btnSeleccionarDocente.btn-success").data("cuerpo");
                            if (cuerpoDocenteId) {
                                cargarAsignaciones(cuerpoDocenteId);
                            }
                        } else {
                            Swal.fire('Error', 'No se pudo eliminar la asignación.', 'error');
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

        if (!horasSemanales || horasSemanales < 1 || horasSemanales > 40) {
            Swal.fire('Dato inválido', 'Las horas semanales deben estar entre 1 y 40.', 'error');
            return;
        }

        Swal.fire({
            title: 'Actualizar Horas',
            text: '¿Desea establecer ' + horasSemanales + ' horas semanales para este docente?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {
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
                        if (respuesta == "ok") {
                            Swal.fire('Éxito', 'Horas semanales actualizadas.', 'success');
                        } else {
                            Swal.fire('Error', 'No se pudieron actualizar las horas.', 'error');
                        }
                    }
                });
            }
        });
    });
});