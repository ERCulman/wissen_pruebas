$(document).ready(function() {
    // Ruta del archivo AJAX para las peticiones al backend.
    var AJAX_URL = 'ajax/asignacion-academica.ajax.php';

    // Variables para almacenar el estado de la selección actual.
    var docenteSeleccionado = null;
    var sedeSeleccionada = null;
    var gradoSeleccionado = null;
    var grupoSeleccionado = null;

    //==================================================================
    // MANEJADORES DE EVENTOS (SIN CAMBIOS)
    //==================================================================
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
    $('#selectGrupo').change(function() {
        grupoSeleccionado = $(this).val();
    });
    $(document).on('click', '.btnSeleccionarDocente', function() {
        var boton = $(this);
        docenteSeleccionado = {
            id: boton.data('id'),
            cuerpoDocenteId: boton.data('cuerpo'),
            nombre: boton.closest('tr').find('td:first').text()
        };
        $('.btnSeleccionarDocente').removeClass('btn-success').addClass('btn-info').text('Seleccionar');
        $('.asignaturas-habilitadas').hide();
        boton.removeClass('btn-info').addClass('btn-success').text('Seleccionado');
        if (docenteSeleccionado.cuerpoDocenteId) {
            var filaAsignaturas = $(`.asignaturas-habilitadas[data-cuerpo="${docenteSeleccionado.cuerpoDocenteId}"]`);
            filaAsignaturas.show();
            cargarAsignaturasHabilitadas(docenteSeleccionado.cuerpoDocenteId, filaAsignaturas.find('.lista-asignaturas'));
        }
        $('#tituloAsignadas').text('Asignaturas Asignadas a ' + docenteSeleccionado.nombre);
        $('#btnAsignar').show();
        if (docenteSeleccionado.cuerpoDocenteId) {
            cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
            cargarPeriodos();
            $('#contenedorAsignadas').show();
        } else {
            $('#tablaAsignadas tbody').html('<tr><td colspan="5" class="text-center">Este docente aún no está configurado en "Cuerpo Docente".</td></tr>');
            $('#infoHoras').hide();
            $('#contenedorAsignadas').show();
        }
    });
    $('#btnAsignar').click(function() {
        if (!docenteSeleccionado || !docenteSeleccionado.cuerpoDocenteId) {
            Swal.fire('Atención', 'Debe seleccionar un docente válido.', 'warning');
            return;
        }
        if (!grupoSeleccionado) {
            Swal.fire('Atención', 'Debe seleccionar un grupo para asignar las materias.', 'warning');
            return;
        }
        var periodosSeleccionados = [];
        $('input[name="periodos[]"]:checked').each(function() {
            periodosSeleccionados.push($(this).val());
        });
        if (periodosSeleccionados.length === 0) {
            Swal.fire('Atención', 'Debe seleccionar al menos un período.', 'warning');
            return;
        }
        if (periodosSeleccionados.length > 4) {
            Swal.fire('Atención', 'No puede seleccionar más de 4 períodos.', 'warning');
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
                asignaciones: asignaciones,
                periodos: periodosSeleccionados
            },
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta === 'ok') {
                    Swal.fire('¡Éxito!', 'Asignaciones creadas correctamente.', 'success');
                    cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
                    $('input[name="asignaturas[]"]:checked').prop('checked', false).trigger('change');
                } else if (respuesta === 'todas_duplicadas') {
                    Swal.fire('Asignación Duplicada', 'El docente ya tiene asignadas todas las materias seleccionadas para este grupo.', 'warning');
                } else if (respuesta === 'parcial_duplicadas') {
                    Swal.fire('Asignación Parcial', 'Algunas asignaturas se crearon correctamente, pero otras ya estaban asignadas al docente.', 'info');
                    cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
                    $('input[name="asignaturas[]"]:checked').prop('checked', false).trigger('change');
                } else {
                    Swal.fire('Error', 'No se pudieron crear las asignaciones. Verifique los datos e intente nuevamente.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en crearAsignacion:", xhr, status, error);
                Swal.fire('Error de Servidor', 'No se pudo completar la operación. Revise la consola para más detalles.', 'error');
            }
        });
    });
    $(document).on('change', 'input[name="asignaturas[]"]', function() {
        var checkbox = $(this);
        var intensidadInput = checkbox.closest('.checkbox').find('.intensidad-input');
        if (checkbox.is(':checked')) {
            intensidadInput.prop('disabled', false).focus();
        } else {
            intensidadInput.prop('disabled', true).val('');
        }
    });

    //==================================================================
    // FUNCIONES AUXILIARES
    //==================================================================
    function cargarDatos(accion, datos, callbackExito) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: accion,
                ...datos
            },
            dataType: 'json',
            success: callbackExito,
            error: function(xhr, status, error) {
                console.error("Error en la acción '" + accion + "':", xhr.responseText, status, error);
                Swal.fire('Error', 'No se pudieron cargar los datos del servidor.', 'error');
            }
        });
    }
    function cargarDocentes(sedeId) {
        cargarDatos('obtenerDocentes', {
            sede_id: sedeId
        }, function(docentes) {
            var tbody = $('#tablaDocentes tbody').empty();
            if (docentes && docentes.length > 0) {
                docentes.forEach(function(docente) {
                    var nombreCompleto = docente.nombres_usuario + ' ' + docente.apellidos_usuario;
                    var horasMax = docente.max_horas_academicas_semanales || 'N/A';
                    var btn = `<button class="btn btn-sm btn-info btnSeleccionarDocente" data-id="${docente.id}" data-cuerpo="${docente.cuerpo_docente_id}">Seleccionar</button>`;
                    var filaDocente = `<tr><td>${nombreCompleto}</td><td>${horasMax}</td><td>${btn}</td></tr>`;
                    var filaAsignaturas = `<tr class="asignaturas-habilitadas" data-cuerpo="${docente.cuerpo_docente_id}" style="display: none;"><td colspan="3"><small><strong>Asignaturas habilitadas:</strong> <span class="lista-asignaturas" style="color: #333;">Cargando...</span></small></td></tr>`;
                    tbody.append(filaDocente + filaAsignaturas);
                });
            } else {
                tbody.append('<tr><td colspan="3" class="text-center">No hay docentes en esta sede.</td></tr>');
            }
        });
    }
    function cargarGrados(sedeId) {
        cargarDatos('obtenerGrados', {
            sede_id: sedeId
        }, function(grados) {
            var select = $('#selectGrado').html('<option value="">Seleccione un grado...</option>');
            if (grados && grados.length > 0) {
                grados.forEach(function(grado) {
                    select.append(`<option value="${grado.id}">${grado.nombre}</option>`);
                });
            }
        });
    }
    function cargarGrupos(gradoId, sedeId) {
        cargarDatos('obtenerGrupos', {
            grado_id: gradoId,
            sede_id: sedeId
        }, function(grupos) {
            var select = $('#selectGrupo').html('<option value="">Seleccione un grupo...</option>');
            if (grupos && grupos.length > 0) {
                grupos.forEach(function(grupo) {
                    select.append(`<option value="${grupo.id}">${grupo.nombre_completo}</option>`);
                });
            }
        });
    }
    function cargarAsignaturas(gradoId, sedeId) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'obtenerAsignaturas',
                grado_id: gradoId,
                sede_id: sedeId
            },
            dataType: 'json',
            success: function(asignaturas) {
                var contenedor = $('#listaAsignaturas').empty();
                if (asignaturas && asignaturas.length > 0) {
                    asignaturas.forEach(function(asignatura, index) {
                        var ihs = asignatura.intensidad_horaria_semanal || '';
                        var item = `
                            <div class="col-md-6" style="margin-bottom: 10px;">
                                <div class="checkbox" style="margin: 0; display: flex; align-items: center; justify-content: space-between;">
                                    <label style="flex: 1; margin-right: 10px;"><input type="checkbox" name="asignaturas[]" value="${asignatura.id}"> ${asignatura.area} - ${asignatura.asignatura}</label>
                                    <div class="input-group input-group-sm" style="width: 100px;">
                                        <input type="number" class="form-control intensidad-input" placeholder="IHS" min="1" max="10" value="${ihs}" disabled style="text-align: center;">
                                        <span class="input-group-addon">hrs</span>
                                    </div>
                                </div>
                            </div>`;
                        contenedor.append(item);
                    });
                    $('#contenedorAsignaturas').show();
                } else {
                    contenedor.html('<div class="col-md-12"><p class="text-muted">No hay asignaturas para este grado.</p></div>');
                    $('#contenedorAsignaturas').show();
                }
            },
            error: function(xhr, status, error) {
                console.error("Error cargando asignaturas:", xhr.responseText, status, error);
                var contenedor = $('#listaAsignaturas').empty();
                if (xhr.status === 403) {
                    contenedor.html('<div class="col-md-12"><p class="text-danger">No tiene permisos para ver las asignaturas.</p></div>');
                } else {
                    contenedor.html('<div class="col-md-12"><p class="text-danger">Error cargando asignaturas. Revise la consola.</p></div>');
                }
                $('#contenedorAsignaturas').show();
            }
        });
    }

    // =========================================================================
    // == INICIO DE SECCIÓN CORREGIDA ==
    // =========================================================================
    function cargarAsignacionesDocente(cuerpoDocenteId) {
        cargarDatos('obtenerAsignaciones', {
            cuerpo_docente_id: cuerpoDocenteId
        }, function(asignaciones) {
            var tbody = $('#tablaAsignadas tbody').empty();
            if (asignaciones && asignaciones.length > 0) {
                asignaciones.forEach(function(asig) {

                    // CAMBIO: Determinar si es multigrado y configurar botones apropiadamente
                    var esMultigrado = asig.num_agrupados > 1;
                    var estructuraIdParaAcciones = asig.estructura_curricular_id;
                    
                    // Para multigrados, el botón Ver usa datos diferentes
                    var botonVer = '';
                    if (esMultigrado) {
                        botonVer = `<button class="btn btn-xs btn-info btnVerMultigrado" data-asignatura="${asig.asignatura_id}" data-grupo="${asig.grupo_id}" title="Ver multigrado">
                                        <i class="fa fa-eye"></i>
                                    </button>`;
                    } else {
                        botonVer = `<button class="btn btn-xs btn-info btnVerAsignacion" data-estructura="${estructuraIdParaAcciones}" data-grupo="${asig.grupo_id}" title="Ver">
                                        <i class="fa fa-eye"></i>
                                    </button>`;
                    }
                    
                    // Botón Editar ahora habilitado para multigrados
                    var botonEditar = '';
                    if (esMultigrado) {
                        botonEditar = `<button class="btn btn-xs btn-warning btnEditarMultigrado" data-asignatura="${asig.asignatura_id}" data-grupo="${asig.grupo_id}" title="Editar multigrado">
                                           <i class="fa fa-edit"></i>
                                       </button>`;
                    } else {
                        botonEditar = `<button class="btn btn-xs btn-warning btnEditarAsignacion" data-estructura="${estructuraIdParaAcciones}" data-grupo="${asig.grupo_id}" title="Editar">
                                           <i class="fa fa-edit"></i>
                                       </button>`;
                    }

                    var fila = `<tr>
                        <td>${asig.area}</td>
                        <td>${asig.asignatura}</td>
                        <td>${asig.nombre_mostrar}</td>
                        <td>${asig.intensidad_horaria_semanal}</td>
                        <td>
                            ${botonVer}
                            ${botonEditar}
                            <button class="btn btn-xs btn-danger btnEliminarAsignacion" data-asignatura="${asig.asignatura_id}" data-grupo="${asig.grupo_id}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                    tbody.append(fila);
                });
            } else {
                tbody.append('<tr><td colspan="5" class="text-center">No tiene asignaciones.</td></tr>');
            }
            calcularHorasAsignadas(cuerpoDocenteId);
        });
    }

    // Lógica para Ver Multigrado
    $(document).on('click', '.btnVerMultigrado', function() {
        var asignaturaId = $(this).data('asignatura');
        var grupoId = $(this).data('grupo');
        abrirModalVerMultigrado(asignaturaId, grupoId);
    });
    
    // Lógica para Editar Multigrado
    $(document).on('click', '.btnEditarMultigrado', function() {
        var asignaturaId = $(this).data('asignatura');
        var grupoId = $(this).data('grupo');
        abrirModalEditarMultigrado(asignaturaId, grupoId);
    });
    
    // Mantenemos la lógica original para Ver y Editar, que funcionan con data-estructura
    $(document).on('click', '.btnVerAsignacion', function() {
        if ($(this).is('[disabled]')) return;
        var estructuraId = $(this).data('estructura');
        var grupoId = $(this).data('grupo');
        abrirModalVer(estructuraId, grupoId);
    });
    $(document).on('click', '.btnEditarAsignacion', function() {
        if ($(this).is('[disabled]')) return;
        var estructuraId = $(this).data('estructura');
        var grupoId = $(this).data('grupo');
        abrirModalEditar(estructuraId, grupoId);
    });

    // La lógica de Eliminar sigue usando data-asignatura para borrar el grupo completo
    $(document).on('click', '.btnEliminarAsignacion', function() {
        var asignaturaId = $(this).data('asignatura');
        var grupoId = $(this).data('grupo');
        eliminarAsignacionConValidacion(asignaturaId, grupoId);
    });

    function eliminarAsignacionConValidacion(asignaturaId, grupoId) {
        Swal.fire({
            title: '¿Eliminar asignación?',
            text: 'Esta acción eliminará la asignación de esta materia en todos los grados del multigrado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarAsignacionCompleta(asignaturaId, grupoId);
            }
        });
    }

    function eliminarAsignacionCompleta(asignaturaId, grupoId) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'eliminarAsignacionCompleta',
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId,
                asignatura_id: asignaturaId,
                grupo_id: grupoId
            },
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta === 'ok') {
                    Swal.fire('¡Eliminado!', 'La asignación ha sido eliminada.', 'success');
                    cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
                } else {
                    Swal.fire('Error', 'No se pudo eliminar la asignación: ' + respuesta, 'error');
                }
            }
        });
    }

    // Funciones de modales y auxiliares se mantienen como en el original
    function calcularHorasAsignadas(cuerpoDocenteId) {
        cargarDatos('calcularHoras', {
            cuerpo_docente_id: cuerpoDocenteId
        }, function(horas) {
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
    function cargarAsignaturasHabilitadas(cuerpoDocenteId, contenedor) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'obtenerAsignaturasHabilitadas',
                cuerpo_docente_id: cuerpoDocenteId
            },
            dataType: 'json',
            success: function(asignaturas) {
                if (asignaturas && asignaturas.length > 0) {
                    var nombres = asignaturas.map(function(asig) {
                        return asig.asignatura;
                    });
                    contenedor.html(nombres.join(', '));
                } else {
                    contenedor.html('Ninguna asignatura habilitada');
                }
            }
        });
    }
    function cargarPeriodos() {
        cargarDatos('obtenerPeriodos', {}, function(periodos) {
            var contenedor = $('#listaPeriodos').empty();
            if (periodos && periodos.length > 0) {
                periodos.forEach(function(periodo) {
                    var checkbox = `<label class="checkbox-inline" style="margin-right: 15px;">
                                      <input type="checkbox" name="periodos[]" value="${periodo.id}"> ${periodo.nombre}
                                    </label>`;
                    contenedor.append(checkbox);
                });
                $('#contenedorPeriodos').show();
            }
        });
    }
    $('#btnGuardarEdicion').click(function() {
        guardarEdicionAsignacion();
    });
    function abrirModalVerMultigrado(asignaturaId, grupoId) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'obtenerDatosMultigrado',
                asignatura_id: asignaturaId,
                grupo_id: grupoId,
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId
            },
            dataType: 'json',
            success: function(datos) {
                if (datos && datos.asignatura) {
                    $('#verMultigradoAsignatura').text(datos.area + ' - ' + datos.asignatura);
                    $('#verMultigradoGrados').text(datos.grados || 'No disponible');
                    $('#verMultigradoGrupos').text(datos.grupos || 'No disponible');
                    $('#verMultigradoIHS').text(datos.intensidad_horaria_semanal + ' horas');
                    $('#verMultigradoPeriodos').text(datos.periodos || 'No disponible');
                    $('#modalVerMultigrado').modal('show');
                } else {
                    Swal.fire('Error', 'No se pudo cargar la información del multigrado.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error cargando datos del multigrado:", xhr.responseText, status, error);
                Swal.fire('Error', 'No se pudo cargar la información del multigrado.', 'error');
            }
        });
    }
    
    function abrirModalEditarMultigrado(asignaturaId, grupoId) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'obtenerDatosEdicionMultigrado',
                asignatura_id: asignaturaId,
                grupo_id: grupoId,
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId
            },
            dataType: 'json',
            success: function(datos) {
                if (datos && datos.basicos && datos.detalles && datos.periodos) {
                    // Llenar datos básicos
                    $('#editarMultigradoGrupo').text(datos.basicos.grupo_multigrado);
                    $('#editarMultigradoAsignatura').text(datos.basicos.asignatura);
                    $('#editarMultigradoIH').text(datos.basicos.ih_asignatura + ' horas');
                    
                    // Cargar períodos dinámicamente
                    var contenedorPeriodos = $('#contenedorPeriodosMultigrado').empty();
                    datos.periodos.forEach(function(periodo) {
                        var checkbox = `<label class="checkbox-inline" style="margin-right: 15px;">
                                          <input type="checkbox" name="periodos_multigrado[]" value="${periodo.id}"> ${periodo.nombre}
                                        </label>`;
                        contenedorPeriodos.append(checkbox);
                    });
                    
                    // Llenar tabla de detalles (sin duplicados)
                    var tbody = $('#tablaEditarMultigrado tbody').empty();
                    datos.detalles.forEach(function(detalle) {
                        var fila = `<tr data-asignacion-ids="${detalle.asignacion_ids}" data-grupo-id="${detalle.grupo_id}">
                            <td>${detalle.grado}</td>
                            <td>${detalle.grupo}</td>
                            <td><input type="number" class="form-control input-sm ihs-input" value="${detalle.ihs_actual}" min="1" max="10"></td>
                            <td><select class="form-control input-sm estado-select"></select></td>
                            <td><button class="btn btn-xs btn-danger btnEliminarFilaMultigrado" title="Eliminar"><i class="fa fa-times"></i></button></td>
                        </tr>`;
                        tbody.append(fila);
                        
                        // Cargar estados en el select
                        cargarEstadosEnSelect(tbody.find('tr:last .estado-select'), detalle.estado_actual);
                    });
                    
                    $('#modalEditarMultigrado').data('asignatura-id', asignaturaId).data('grupo-id', grupoId).modal('show');
                } else {
                    Swal.fire('Error', 'No se pudo cargar la información para editar el multigrado.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error cargando datos para edición:", xhr.responseText, status, error);
                Swal.fire('Error', 'No se pudo cargar la información para editar el multigrado.', 'error');
            }
        });
    }
    
    function cargarEstadosEnSelect(selectElement, estadoActual) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: { accion: 'obtenerEstadosAsignacion' },
            dataType: 'json',
            success: function(estados) {
                var html = '';
                estados.forEach(function(estado) {
                    var selected = estado === estadoActual ? 'selected' : '';
                    html += `<option value="${estado}" ${selected}>${estado}</option>`;
                });
                selectElement.html(html);
            }
        });
    }
    
    function abrirModalVer(estructuraId, grupoId) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'obtenerDetalleAsignacion',
                estructura_curricular_id: estructuraId,
                grupo_id: grupoId,
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId
            },
            dataType: 'json',
            success: function(detalle) {
                if (detalle && detalle.asignatura) {
                    $('#verGrado').text(detalle.grado_nombre);
                    $('#verGrupo').text(detalle.grupo_nombre);
                    $('#verAsignatura').text(detalle.area + ' - ' + detalle.asignatura);
                    $('#verIHS').text(detalle.intensidad_horaria_semanal + ' horas');
                    var periodosHtml = '';
                    if (detalle.periodos_nombres) {
                        var periodos = detalle.periodos_nombres.split(',');
                        periodos.forEach(function(periodo) {
                            periodosHtml += `<label class="checkbox-inline"><input type="checkbox" checked disabled> ${periodo}</label>`;
                        });
                    }
                    $('#verPeriodos').html(periodosHtml);
                    $('#modalVerAsignacion').modal('show');
                } else {
                    Swal.fire('Error', 'No se pudo cargar la información de la asignación.', 'error');
                }
            }
        });
    }
    function abrirModalEditar(estructuraId, grupoId) {
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'obtenerDetalleAsignacion',
                estructura_curricular_id: estructuraId,
                grupo_id: grupoId,
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId
            },
            dataType: 'json',
            success: function(detalle) {
                if (detalle && detalle.asignatura) {
                    $('#editarGrado').text(detalle.grado_nombre);
                    $('#editarGrupo').text(detalle.grupo_nombre);
                    $('#editarAsignatura').text(detalle.area + ' - ' + detalle.asignatura);
                    $('#editarIHS').text(detalle.intensidad_horaria_semanal + ' horas');
                    var periodosIds = detalle.periodos_ids ? detalle.periodos_ids.split(',') : [];
                    $.ajax({
                        url: AJAX_URL,
                        method: 'POST',
                        data: {
                            accion: 'obtenerEstadosAsignacion'
                        },
                        dataType: 'json',
                        success: function(estados) {
                            var estadosHtml = '';
                            estados.forEach(function(estado) {
                                var selected = estado === detalle.estado ? 'selected' : '';
                                estadosHtml += `<option value="${estado}" ${selected}>${estado}</option>`;
                            });
                            $('#editarEstado').html(estadosHtml);
                            $.ajax({
                                url: AJAX_URL,
                                method: 'POST',
                                data: {
                                    accion: 'obtenerPeriodos'
                                },
                                dataType: 'json',
                                success: function(periodos) {
                                    var periodosHtml = '';
                                    periodos.forEach(function(periodo) {
                                        var checked = periodosIds.includes(periodo.id.toString()) ? 'checked' : '';
                                        periodosHtml += `<label class="checkbox-inline"><input type="checkbox" name="editarPeriodos[]" value="${periodo.id}" ${checked}> ${periodo.nombre}</label>`;
                                    });
                                    $('#editarPeriodos').html(periodosHtml);
                                    $('#modalEditarAsignacion').data('estructura', estructuraId).data('grupo', grupoId).modal('show');
                                }
                            });
                        }
                    });
                }
            }
        });
    }
    function guardarEdicionAsignacion() {
        var periodosSeleccionados = [];
        $('input[name="editarPeriodos[]"]:checked').each(function() {
            periodosSeleccionados.push($(this).val());
        });
        if (periodosSeleccionados.length === 0) {
            Swal.fire('Atención', 'Debe seleccionar al menos un período.', 'warning');
            return;
        }
        var nuevoEstado = $('#editarEstado').val();
        var estructuraId = $('#modalEditarAsignacion').data('estructura');
        var grupoId = $('#modalEditarAsignacion').data('grupo');
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'actualizarAsignacionEspecifica',
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId,
                estructura_curricular_id: estructuraId,
                grupo_id: grupoId,
                periodos: periodosSeleccionados,
                estado: nuevoEstado
            },
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta === 'ok') {
                    Swal.fire('¡Éxito!', 'Asignación actualizada correctamente.', 'success');
                    $('#modalEditarAsignacion').modal('hide');
                    cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
                } else {
                    Swal.fire('Error', 'No se pudo actualizar la asignación.', 'error');
                }
            }
        });
    }
    // Event listeners para edición de multigrado
    $(document).on('click', '.btnEliminarFilaMultigrado', function() {
        var fila = $(this).closest('tr');
        var grupoId = fila.data('grupo-id');
        
        Swal.fire({
            title: '¿Eliminar esta asignación?',
            text: 'Esta acción eliminará la asignación de este grado para los períodos seleccionados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Marcar fila para eliminación
                fila.addClass('fila-eliminada').hide();
                Swal.fire('Marcado para eliminación', 'La asignación será eliminada al guardar los cambios.', 'success');
            }
        });
    });
    
    $('#btnGuardarEdicionMultigrado').click(function() {
        // Verificar que se hayan seleccionado períodos
        var periodosSeleccionados = [];
        $('input[name="periodos_multigrado[]"]:checked').each(function() {
            periodosSeleccionados.push($(this).val());
        });
        
        if (periodosSeleccionados.length === 0) {
            Swal.fire('Atención', 'Debe seleccionar al menos un período para aplicar los cambios.', 'warning');
            return;
        }
        
        var cambios = [];
        var eliminaciones = [];
        
        $('#tablaEditarMultigrado tbody tr').each(function() {
            var fila = $(this);
            var grupoId = fila.data('grupo-id');
            
            if (fila.hasClass('fila-eliminada')) {
                // Fila marcada para eliminación
                eliminaciones.push(grupoId);
            } else {
                // Fila para actualizar
                var nuevaIHS = fila.find('.ihs-input').val();
                var nuevoEstado = fila.find('.estado-select').val();
                
                if (grupoId) {
                    cambios.push({
                        grupo_id: grupoId,
                        ihs: nuevaIHS,
                        estado: nuevoEstado
                    });
                }
            }
        });
        
        var asignaturaId = $('#modalEditarMultigrado').data('asignatura-id');
        
        $.ajax({
            url: AJAX_URL,
            method: 'POST',
            data: {
                accion: 'actualizarMultigrado',
                cambios: cambios,
                eliminaciones: eliminaciones,
                periodos_seleccionados: periodosSeleccionados,
                cuerpo_docente_id: docenteSeleccionado.cuerpoDocenteId,
                asignatura_id: asignaturaId
            },
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta === 'ok') {
                    Swal.fire('¡Éxito!', 'Los cambios han sido guardados para los períodos seleccionados.', 'success');
                    $('#modalEditarMultigrado').modal('hide');
                    cargarAsignacionesDocente(docenteSeleccionado.cuerpoDocenteId);
                } else {
                    Swal.fire('Error', 'No se pudieron guardar los cambios.', 'error');
                }
            }
        });
    });

    function resetearSelecciones() {
        docenteSeleccionado = null;
        gradoSeleccionado = null;
        grupoSeleccionado = null;
        $('.btnSeleccionarDocente').removeClass('btn-success').addClass('btn-info').text('Seleccionar');
        $('.asignaturas-habilitadas').hide();
        $('#selectGrado').val('');
        $('#selectGrupo').val('').html('<option value="">Seleccione un grupo...</option>');
        $('#contenedorAsignaturas').hide();
        $('#listaAsignaturas').empty();
        $('#contenedorAsignadas').hide();
        $('#btnAsignar').hide();
    }
});