$(document).ready(function() {
    // Ruta del archivo AJAX para las peticiones al backend.
    var AJAX_URL = 'ajax/asignacion-academica.ajax.php';

    // Variables para almacenar el estado de la selección actual.
    var docenteSeleccionado = null;
    var sedeSeleccionada = null;
    var gradoSeleccionado = null;
    var grupoSeleccionado = null;

    //==================================================================
    // MANEJADORES DE EVENTOS
    //==================================================================

    // Detecta el cambio en el selector de Sede.
    $('#selectSede').change(function() {
        sedeSeleccionada = $(this).val();
        if (sedeSeleccionada) {
            $('#contenidoPrincipal').show();
            cargarDocentes(sedeSeleccionada);
            cargarGrados(sedeSeleccionada);
            resetearSelecciones();
        } else {
            $('#contenidoPrincipal').hide();
        }
    });

    // Detecta el cambio en el selector de Grado.
    $('#selectGrado').change(function() {
        gradoSeleccionado = $(this).val();
        $('#selectGrupo').html('<option value="">Seleccione un grupo...</option>');
        
        if (gradoSeleccionado && sedeSeleccionada) {
            cargarGrupos(gradoSeleccionado, sedeSeleccionada);
            cargarAsignaturas(gradoSeleccionado, sedeSeleccionada);
        } else {
            $('#contenedorAsignaturas').hide();
        }
    });

    // Detecta el cambio en el selector de Grupo.
    $('#selectGrupo').change(function() {
        grupoSeleccionado = $(this).val();
        // Las asignaturas ya se muestran por grado, no necesitamos recargarlas por grupo
    });

    // Gestiona la selección de un docente de la tabla.
    $(document).on('click', '.btnSeleccionarDocente', function() {
        var boton = $(this);
        docenteSeleccionado = {
            id: boton.data('id'),
            cuerpoDocenteId: boton.data('cuerpo'),
            nombre: boton.closest('tr').find('td:first').text()
        };

        $('.btnSeleccionarDocente').removeClass('btn-success').addClass('btn-info').text('Seleccionar');
        boton.removeClass('btn-info').addClass('btn-success').text('Seleccionado');

        $('#tituloAsignadas').text('Asignaturas Asignadas a ' + docenteSeleccionado.nombre);
        $('#btnAsignar').show();

        if (docenteSeleccionado.cuerpoDocenteId) {
            cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
            $('#contenedorAsignadas').show();
        } else {
            $('#tablaAsignadas tbody').html('<tr><td colspan="5" class="text-center">Este docente aún no está configurado en "Cuerpo Docente".</td></tr>');
            $('#infoHoras').hide();
            $('#contenedorAsignadas').show();
        }
    });

    // Envía las asignaturas seleccionadas para ser asignadas al docente.
    $('#btnAsignar').click(function() {
        if (!docenteSeleccionado || !docenteSeleccionado.cuerpoDocenteId) {
            Swal.fire('Atención', 'Debe seleccionar un docente válido.', 'warning');
            return;
        }
        if (!grupoSeleccionado) {
            Swal.fire('Atención', 'Debe seleccionar un grupo para asignar las materias.', 'warning');
            return;
        }

        var asignaciones = [];
        $('input[name="asignaturas[]"]:checked').each(function() {
            var intensidad = $(this).closest('.checkbox').find('.intensidad-input').val();
            if (intensidad && parseInt(intensidad) > 0) {
                asignaciones.push({
                    estructura_curricular_id: $(this).val(),
                    grupo_id: grupoSeleccionado,
                    intensidad_horaria_semanal: parseInt(intensidad)
                });
            }
        });

        if (asignaciones.length === 0) {
            Swal.fire('Atención', 'Seleccione al menos una asignatura e ingrese su intensidad horaria.', 'warning');
            return;
        }

        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'crearAsignacion',
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId,
                asignaciones: asignaciones
            },
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta === 'ok') {
                    Swal.fire('¡Éxito!', 'Asignaciones creadas correctamente.', 'success');
                    cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
                    $('input[name="asignaturas[]"]:checked').prop('checked', false).trigger('change');
                } else {
                    Swal.fire('Error', 'No se pudieron crear las asignaciones. (' + respuesta + ')', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en crearAsignacion:", xhr, status, error);
                Swal.fire('Error de Servidor', 'No se pudo completar la operación. Revise la consola para más detalles.', 'error');
            }
        });
    });

    // Envía la petición para eliminar una asignación académica.
    $(document).on('click', '.btnEliminarAsignacion', function() {
        var asignacionId = $(this).data('id');
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción eliminará la asignación permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, ¡eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: AJAX_URL,
                    method: 'POST',
                    data: {
                        accion: 'eliminarAsignacion',
                        asignacion_id: asignacionId
                    },
                    dataType: 'json',
                    success: function(respuesta) {
                        if (respuesta === 'ok') {
                            Swal.fire('Eliminado', 'La asignación ha sido eliminada.', 'success');
                            cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
                        } else {
                            Swal.fire('Error', 'No se pudo eliminar la asignación. (' + respuesta + ')', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en eliminarAsignacion:", xhr, status, error);
                        Swal.fire('Error de Servidor', 'No se pudo completar la operación. Revise la consola para más detalles.', 'error');
                    }
                });
            }
        });
    });

    // Habilita o deshabilita el input de intensidad horaria.
    $(document).on('change', 'input[name="asignaturas[]"]', function() {
        var intensidadInput = $(this).closest('.checkbox').find('.intensidad-input');
        if ($(this).is(':checked')) {
            intensidadInput.prop('disabled', false).focus();
        } else {
            intensidadInput.prop('disabled', true).val('');
        }
    });

    //==================================================================
    // FUNCIONES AUXILIARES
    //==================================================================

    // Centraliza las peticiones AJAX al backend para cargar datos.
    function cargarDatos(accion, datos, callbackExito) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: { accion: accion, ...datos },
            dataType: 'json',
            success: callbackExito,
            error: function(xhr, status, error) {
                console.error("Error en la acción '" + accion + "':", xhr.responseText, status, error);
                Swal.fire('Error', 'No se pudieron cargar los datos del servidor.', 'error');
            }
        });
    }

    // Carga la lista de docentes según la sede seleccionada.
    function cargarDocentes(sedeId) {
        cargarDatos('obtenerDocentes', { sede_id: sedeId }, function(docentes) {
            var tbody = $('#tablaDocentes tbody').empty();
            if (docentes && docentes.length > 0) {
                docentes.forEach(function(docente) {
                    var nombreCompleto = docente.nombres_usuario + ' ' + docente.apellidos_usuario;
                    var horasMax = docente.max_horas_academicas_semanales || 'N/A';
                    var btn = `<button class="btn btn-sm btn-info btnSeleccionarDocente" data-id="${docente.id}" data-cuerpo="${docente.cuerpo_docente_id}">Seleccionar</button>`;
                    tbody.append(`<tr><td>${nombreCompleto}</td><td>${horasMax}</td><td>${btn}</td></tr>`);
                });
            } else {
                tbody.append('<tr><td colspan="3" class="text-center">No hay docentes en esta sede.</td></tr>');
            }
        });
    }

    // Carga la lista de grados según la sede seleccionada.
    function cargarGrados(sedeId) {
        cargarDatos('obtenerGrados', { sede_id: sedeId }, function(grados) {
            var select = $('#selectGrado').html('<option value="">Seleccione un grado...</option>');
            if (grados && grados.length > 0) {
                grados.forEach(function(grado) {
                    select.append(`<option value="${grado.id}">${grado.nombre}</option>`);
                });
            }
        });
    }

    // Carga la lista de grupos según el grado y sede.
    function cargarGrupos(gradoId, sedeId) {
        cargarDatos('obtenerGrupos', { grado_id: gradoId, sede_id: sedeId }, function(grupos) {
            var select = $('#selectGrupo').html('<option value="">Seleccione un grupo...</option>');
            if (grupos && grupos.length > 0) {
                grupos.forEach(function(grupo) {
                    select.append(`<option value="${grupo.id}">${grupo.nombre_completo}</option>`);
                });
            }
        });
    }

    // Carga las asignaturas disponibles para un grado y sede.
    function cargarAsignaturas(gradoId, sedeId) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: { accion: 'obtenerAsignaturas', grado_id: gradoId, sede_id: sedeId },
            dataType: 'json',
            success: function(asignaturas) {
                var contenedor = $('#listaAsignaturas').empty();
                if (asignaturas && asignaturas.length > 0) {
                    asignaturas.forEach(function(asignatura) {
                        var ihs = asignatura.intensidad_horaria_semanal || '';
                        var item = `
                            <div class="checkbox">
                                <label><input type="checkbox" name="asignaturas[]" value="${asignatura.id}"> ${asignatura.area} - ${asignatura.asignatura}</label>
                                <div class="input-group" style="width:120px; margin-top:5px;">
                                    <input type="number" class="form-control input-sm intensidad-input" placeholder="IHS" min="1" max="10" value="${ihs}" disabled>
                                    <span class="input-group-addon">hrs</span>
                                </div>
                            </div>`;
                        contenedor.append(item);
                    });
                    $('#contenedorAsignaturas').show();
                } else {
                    contenedor.html('<p class="text-muted">No hay asignaturas para este grado.</p>');
                    $('#contenedorAsignaturas').show();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error cargando asignaturas:", xhr.responseText, status, error);
                var contenedor = $('#listaAsignaturas').empty();
                if (xhr.status === 403) {
                    contenedor.html('<p class="text-danger">No tiene permisos para ver las asignaturas.</p>');
                } else {
                    contenedor.html('<p class="text-danger">Error cargando asignaturas. Revise la consola.</p>');
                }
                $('#contenedorAsignaturas').show();
            }
        });
    }

    // Carga la tabla con las asignaturas ya asignadas a un docente.
    function cargarAsignacionesDocente(cuerpoDocenteId) {
        cargarDatos('obtenerAsignaciones', { cuerpo_docente_id: cuerpoDocenteId }, function(asignaciones) {
            var tbody = $('#tablaAsignadas tbody').empty();
            if (asignaciones && asignaciones.length > 0) {
                asignaciones.forEach(function(asig) {
                    var fila = `<tr>
                        <td>${asig.area}</td>
                        <td>${asig.asignatura}</td>
                        <td>${asig.nombre_mostrar}</td>
                        <td>${asig.intensidad_horaria_semanal}</td>
                        <td><button class="btn btn-xs btn-danger btnEliminarAsignacion" data-id="${asig.id}"><i class="fa fa-trash"></i></button></td>
                    </tr>`;
                    tbody.append(fila);
                });
            } else {
                tbody.append('<tr><td colspan="5" class="text-center">No tiene asignaciones.</td></tr>');
            }
            calcularHorasAsignadas(cuerpoDocenteId);
        });
    }

    // Calcula y muestra el total de horas asignadas a un docente.
    function calcularHorasAsignadas(cuerpoDocenteId) {
        cargarDatos('calcularHoras', { cuerpo_docente_id: cuerpoDocenteId }, function(horas) {
            var horasMaxText = $(`.btnSeleccionarDocente[data-cuerpo="${cuerpoDocenteId}"]`).closest('tr').find('td:nth-child(2)').text();
            var horasMax = parseInt(horasMaxText) || 0;

            $('#horasAsignadas').text(horas);
            $('#horasMaximas').text(horasMaxText);
            $('#infoHoras').show();

            if (horas > horasMax && horasMax > 0) {
                $('#infoHoras').removeClass('alert-info').addClass('alert-danger');
            } else {
                $('#infoHoras').removeClass('alert-danger').addClass('alert-info');
            }
        });
    }

    // Reinicia las selecciones y la interfaz a su estado inicial.
    function resetearSelecciones() {
        docenteSeleccionado = null;
        gradoSeleccionado = null;
        grupoSeleccionado = null;

        $('.btnSeleccionarDocente').removeClass('btn-success').addClass('btn-info').text('Seleccionar');

        $('#selectGrado').val('');
        $('#selectGrupo').val('').html('<option value="">Seleccione un grupo...</option>');
        $('#contenedorAsignaturas').hide();
        $('#listaAsignaturas').empty();
        $('#contenedorAsignadas').hide();
        $('#btnAsignar').hide();
    }
});