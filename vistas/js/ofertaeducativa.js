$(document).ready(function() {

    var cursosDisponibles = [];

    // CARGAR CURSOS AL INICIAR LA PÁGINA
    cargarCursos();

    // FUNCIÓN PARA CARGAR CURSOS
    function cargarCursos() {
        $.ajax({
            url: 'ajax/obtener-oferta-educativa.php',
            type: 'POST',
            data: {
                accion: 'obtenerCursos'
            },
            dataType: 'json',
            success: function(data) {
                cursosDisponibles = data.sort(function(a, b) {
                    if (a.tipo !== b.tipo) {
                        return a.tipo === 'Numérico' ? -1 : 1;
                    }
                    return a.nombre.localeCompare(b.nombre);
                });
            }
        });
    }

    // CARGAR GRADOS CUANDO CAMBIA EL NIVEL EDUCATIVO
    $('#nivelEducativo').change(function() {
        var nivelId = $(this).val();

        if(nivelId != '') {
            $.ajax({
                url: 'ajax/obtener-oferta-educativa.php',
                type: 'POST',
                data: {
                    accion: 'obtenerGrados',
                    nivelId: nivelId
                },
                dataType: 'json',
                success: function(data) {
                    var html = '';

                    if(data.length > 0) {
                        $.each(data, function(index, grado) {
                            html += '<div class="grado-container" data-grado-id="' + grado.id + '" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">';
                            html += '<div class="checkbox">';
                            html += '<label style="font-weight: bold; font-size: 16px;">';
                            html += '<input type="checkbox" name="grados[]" value="' + grado.id + '" id="grado_' + grado.id + '" class="grado-checkbox"> ' + grado.nombre;
                            html += '</label>';
                            html += '</div>';

                            html += '<div class="grado-opciones" id="grado_opciones_' + grado.id + '" style="display: none; margin-top: 15px;">';

                            html += '<div class="checkbox" style="margin-bottom: 15px;">';
                            html += '<label style="color: #337ab7; font-weight: bold;">';
                            html += '<input type="checkbox" name="multigrado_' + grado.id + '" value="1" id="multigrado_' + grado.id + '" class="multigrado-checkbox"> Grupo Multigrado';
                            html += '</label>';
                            html += '</div>';

                            html += '<div class="campos-multigrado" id="campos_multigrado_' + grado.id + '" style="display: none;">';

                            html += '<div class="row" style="margin-bottom: 10px;">';
                            html += '<div class="col-md-6">';
                            html += '<div class="input-group">';
                            html += '<span class="input-group-addon">Nombre del Grupo:</span>';
                            html += '<input type="text" name="nombre_manual_' + grado.id + '" class="form-control" placeholder="Ej: Multigrado Primaria A">';
                            html += '</div>';
                            html += '</div>';

                            html += '<div class="col-md-3">';
                            html += '<div class="input-group">';
                            html += '<span class="input-group-addon">Cupos:</span>';
                            html += '<input type="number" name="cupos_multigrado_' + grado.id + '" class="form-control" min="1" max="50">';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';

                            html += '</div>';

                            html += '<div class="cursos-container" id="cursos_' + grado.id + '" style="display: none;"></div>';

                            html += '</div>';
                            html += '</div>';
                        });
                    } else {
                        html = '<p class="text-muted">No hay grados disponibles para este nivel educativo</p>';
                    }

                    $('#contenedorGrados').html(html);
                }
            });
        } else {
            $('#contenedorGrados').html('<p class="text-muted">Seleccione un nivel educativo para ver los grados disponibles</p>');
        }
    });

    // MANEJAR SELECCIÓN DE GRADOS
    $(document).on('change', '.grado-checkbox', function() {
        var gradoId = $(this).val();
        var gradoOpciones = $('#grado_opciones_' + gradoId);

        if($(this).is(':checked')) {
            gradoOpciones.show();
            $('#cursos_' + gradoId).show();
            cargarCursosParaGrado(gradoId);
        } else {
            gradoOpciones.hide();
            $('#multigrado_' + gradoId).prop('checked', false);
            $('#campos_multigrado_' + gradoId).hide();
            $('#cursos_' + gradoId).hide().html('');
        }
    });

    // MANEJAR CHECKBOX MULTIGRADO
    $(document).on('change', '.multigrado-checkbox', function() {
        var gradoId = $(this).attr('id').replace('multigrado_', '');
        var camposMultigrado = $('#campos_multigrado_' + gradoId);
        var cursosContainer = $('#cursos_' + gradoId);

        if($(this).is(':checked')) {
            camposMultigrado.show();
            cursosContainer.hide().html('');
        } else {
            camposMultigrado.hide();
            cursosContainer.show();
            cargarCursosParaGrado(gradoId);
        }
    });

    function cargarCursosParaGrado(gradoId) {
        var sedeId = $('#sedeOferta').val();
        var jornadaId = $('#jornadaOferta').val();
        var anioLectivoId = $('#anioLectivo').val();

        if(sedeId && jornadaId && anioLectivoId) {
            $.ajax({
                url: 'ajax/obtener-oferta-educativa.php',
                type: 'POST',
                data: {
                    accion: 'obtenerCursosOcupados',
                    gradoId: gradoId,
                    sedeId: sedeId,
                    jornadaId: jornadaId,
                    anioLectivoId: anioLectivoId
                },
                dataType: 'json',
                success: function(cursosOcupados) {
                    mostrarSiguienteCursoDisponible(gradoId, cursosOcupados);
                }
            });
        } else {
            mostrarSiguienteCursoDisponible(gradoId, []);
        }
    }

    function mostrarSiguienteCursoDisponible(gradoId, cursosOcupados) {
        var html = '<div class="panel panel-default">';
        html += '<div class="panel-heading" style="background-color: #f5f5f5;"><strong>Cursos y Cupos</strong></div>';
        html += '<div class="panel-body" id="panel_body_' + gradoId + '">';
        var cursosAgregados = [];
        var contenedorActual = $('#cursos_' + gradoId);
        contenedorActual.find('input[type="hidden"][name="cursos_' + gradoId + '[]"]').each(function() {
            var cursoId = $(this).val();
            var cupos = contenedorActual.find('input[type="hidden"][name="cupos_' + gradoId + '_' + cursoId + '"]').val();
            var cursoInfo = cursosDisponibles.find(function(curso) {
                return curso.id == cursoId;
            });
            if(cursoInfo && cupos) {
                var nombreGrado = $('#grado_' + gradoId).parent().text().trim();
                var nombreGrupo = nombreGrado + ' ' + cursoInfo.nombre;
                cursosAgregados.push({
                    id: cursoId,
                    nombre: cursoInfo.nombre,
                    cupos: cupos,
                    nombreGrupo: nombreGrupo
                });
            }
        });
        var cursosNoDisponibles = cursosOcupados.slice();
        cursosAgregados.forEach(function(curso) {
            cursosNoDisponibles.push(curso.id.toString());
        });
        var siguienteCurso = null;
        for(var i = 0; i < cursosDisponibles.length; i++) {
            var curso = cursosDisponibles[i];
            var cursoIdStr = curso.id.toString();
            if(!cursosNoDisponibles.includes(cursoIdStr) && !cursosNoDisponibles.includes(parseInt(curso.id))) {
                siguienteCurso = curso;
                break;
            }
        }
        if(cursosAgregados.length > 0) {
            html += '<h5>Cursos Agregados:</h5>';
            cursosAgregados.forEach(function(curso) {
                html += '<div class="row" style="margin-bottom: 10px; background-color: #f9f9f9; padding: 10px; border-radius: 4px;">';
                html += '<div class="col-md-12">';
                html += '<i class="fa fa-check-circle text-success"></i> ';
                html += '<strong>' + curso.nombreGrupo + '</strong>';
                html += '<input type="hidden" name="cursos_' + gradoId + '[]" value="' + curso.id + '">';
                html += '<input type="hidden" name="cupos_' + gradoId + '_' + curso.id + '" value="' + curso.cupos + '">';
                html += '</div>';
                html += '</div>';
            });
        }
        if(siguienteCurso) {
            html += '<h5>Agregar Nuevo Curso:</h5>';
            html += '<div class="row curso-row" style="margin-bottom: 10px; border: 2px dashed #5bc0de; padding: 15px; border-radius: 4px;">';
            html += '<div class="col-md-3">';
            html += '<div class="checkbox">';
            html += '<label>';
            html += '<input type="checkbox" name="curso_temp_' + gradoId + '" value="' + siguienteCurso.id + '" class="curso-checkbox" data-grado="' + gradoId + '" data-curso="' + siguienteCurso.id + '"> ';
            html += '<strong>' + siguienteCurso.nombre + '</strong>';
            html += '</label>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-3">';
            html += '<div class="input-group">';
            html += '<span class="input-group-addon">Cupos:</span>';
            html += '<input type="number" name="cupos_temp_' + gradoId + '_' + siguienteCurso.id + '" class="form-control cupos-input" min="1" max="50" disabled data-grado="' + gradoId + '" data-curso="' + siguienteCurso.id + '">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<div class="input-group">';
            html += '<span class="input-group-addon">Grupo:</span>';
            html += '<input type="text" class="form-control grupo-nombre" readonly id="grupo_temp_' + gradoId + '_' + siguienteCurso.id + '" placeholder="Seleccione curso y cupos">';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        } else {
            if(cursosAgregados.length === 0) {
                html += '<div class="alert alert-info">';
                html += '<i class="fa fa-info-circle"></i> ';
                html += '<strong>Todos los cursos están ocupados para este grado.</strong>';
                html += '</div>';
            } else {
                html += '<div class="alert alert-warning">';
                html += '<i class="fa fa-exclamation-triangle"></i> ';
                html += '<strong>No hay más cursos disponibles.</strong><br>';
                html += 'Debe agregar más cursos al sistema para crear nuevos grupos.';
                html += '</div>';
            }
        }
        html += '</div>';
        html += '</div>';
        $('#cursos_' + gradoId).html(html);
    }
    $(document).on('change', '.curso-checkbox', function() {
        var gradoId = $(this).data('grado');
        var cursoId = $(this).data('curso');
        var cuposInput = $('input[name="cupos_temp_' + gradoId + '_' + cursoId + '"]');
        if($(this).is(':checked')) {
            cuposInput.prop('disabled', false).focus();
        } else {
            cuposInput.prop('disabled', true).val('');
            $('#grupo_temp_' + gradoId + '_' + cursoId).val('');
        }
    });
    $(document).on('input', '.cupos-input', function() {
        var gradoId = $(this).data('grado');
        var cursoId = $(this).data('curso');
        var cupos = $(this).val();
        if(cupos && cupos > 0) {
            var nombreGrado = $('#grado_' + gradoId).parent().text().trim();
            var nombreCurso = '';
            $.each(cursosDisponibles, function(index, curso) {
                if(curso.id == cursoId) {
                    nombreCurso = curso.nombre;
                    return false;
                }
            });
            var nombreGrupo = nombreGrado + ' ' + nombreCurso;
            $('#grupo_temp_' + gradoId + '_' + cursoId).val(nombreGrupo);
        } else {
            $('#grupo_temp_' + gradoId + '_' + cursoId).val('');
        }
    });
    $(document).on('keypress blur', '.cupos-input', function(e) {
        if(e.type === 'keypress' && e.which !== 13) return;
        var gradoId = $(this).data('grado');
        var cursoId = $(this).data('curso');
        var cupos = $(this).val();
        var checkbox = $('input[name="curso_temp_' + gradoId + '"][value="' + cursoId + '"]');
        if(checkbox.is(':checked') && cupos && parseInt(cupos) > 0) {
            agregarCursoConfirmado(gradoId, cursoId, cupos);
        }
    });
    function agregarCursoConfirmado(gradoId, cursoId, cupos) {
        var contenedor = $('#cursos_' + gradoId);
        if(contenedor.find('input[name="cursos_' + gradoId + '[]"][value="' + cursoId + '"]').length === 0) {
            contenedor.append('<input type="hidden" name="cursos_' + gradoId + '[]" value="' + cursoId + '">');
            contenedor.append('<input type="hidden" name="cupos_' + gradoId + '_' + cursoId + '" value="' + cupos + '">');
            setTimeout(function() {
                cargarCursosParaGrado(gradoId);
            }, 300);
        }
    }
    $('#sedeOferta, #jornadaOferta, #anioLectivo').change(function() {
        $('.grado-checkbox:checked').each(function() {
            var gradoId = $(this).val();
            cargarCursosParaGrado(gradoId);
        });
    });

    // FUNCIÓN PARA CARGAR GRUPOS PADRE (CORREGIDA)
    function cargarGruposPadre(selectElement, selectedId) {
        $.ajax({
            url: 'ajax/obtener-oferta-educativa.php',
            type: 'POST',
            data: {
                accion: 'obtenerGruposMultigrado'
            },
            dataType: 'json',
            success: function(data) {
                var html = '<option value="">Seleccione un Grupo Padre...</option>';
                
                if (data && data.length > 0) {
                    $.each(data, function(index, grupo) {
                        var selected = (grupo.id == selectedId) ? 'selected' : '';
                        html += '<option value="' + grupo.id + '" ' + selected + '>' + grupo.nombre + '</option>';
                    });
                } else {
                    html += '<option value="" disabled>No hay grupos multigrado disponibles</option>';
                }
                
                selectElement.html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', xhr.responseText);
                selectElement.html('<option value="">Error cargando grupos</option>');
            }
        });
    }

    // MÉTODO PARA VER GRUPO
    $(document).on('click', '.btnVerGrupo', function() {
        var grupoId = $(this).data('grupo-id');
        var ofertaId = $(this).data('oferta-id');

        $.ajax({
            url: 'ajax/obtener-oferta-educativa.php',
            type: 'POST',
            data: {
                accion: 'obtenerDatosGrupo',
                grupoId: grupoId,
                ofertaId: ofertaId
            },
            dataType: 'json',
            success: function(data) {
                $('#verGrupoAnio').text(data.anio || 'No disponible');
                $('#verGrupoSede').text(data.nombre_sede || 'No disponible');
                $('#verGrupoJornada').text(data.nombre_jornada || 'No disponible');
                $('#verGrupoNivel').text(data.nombre_nivel || 'No disponible');
                $('#verGrupoGrado').text(data.nombre_grado || 'No disponible');
                $('#verGrupoCurso').text(data.nombre_curso || 'Multigrado');
                $('#verGrupoCupos').text(data.cupos || 'No disponible');
                $('#verGrupoNombre').text(data.nombre_grupo || 'No disponible');

                $('.btnEditarGrupoDesdeVer').attr('data-grupo-id', grupoId).attr('data-oferta-id', ofertaId);

                $('#modalVerGrupo').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar los datos del grupo",
                    confirmButtonText: "Cerrar"
                });
            }
        });
    });

    // =========================================================================
    // LÓGICA DE EDICIÓN DE GRUPOS (CORREGIDA)
    // =========================================================================

    $(document).on('click', '.btnEditarGrupo, .btnEditarGrupoDesdeVer', function() {
        var grupoId = $(this).data('grupo-id');
        var ofertaId = $(this).data('oferta-id');

        $('#modalVerGrupo').modal('hide');

        // Resetear estado del formulario a un estado base
        $('#formEditarGrupo')[0].reset();
        $('#contenedorEditarCurso').show();
        $('#seccionAsociarMultigrado').hide();
        $('#contenedorGrupoPadre').hide();
        $('#grupoMultigrado').prop('checked', false);

        $.ajax({
            url: 'ajax/obtener-oferta-educativa.php',
            type: 'POST',
            data: {
                accion: 'obtenerDatosGrupo',
                grupoId: grupoId,
                ofertaId: ofertaId
            },
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    Swal.fire('Error', data.error, 'error');
                    return;
                }

                // Llenar campos comunes y de contexto
                $('#idGrupo').val(grupoId);
                $('#ofertaEducativaId').val(ofertaId);
                $('#editarTipoGrupo').val(data.tipo);
                $('#editarGrupoAnio').val(data.anio || '');
                $('#editarGrupoSede').val(data.nombre_sede || '');
                $('#editarGrupoJornada').val(data.nombre_jornada || '');
                $('#editarGrupoNivel').val(data.nombre_nivel || '');
                $('#editarGrupoGrado').val(data.nombre_grado || '');
                $('#editarCuposGrupo').val(data.cupos || '');
                $('#editarNombreGrupo').val(data.nombre_grupo || '');

                // Lógica condicional basada en el tipo de grupo
                if (data.tipo === 'Multigrado') {
                    $('#contenedorEditarCurso').hide();
                    $('#seccionAsociarMultigrado').hide();
                    $('#editarNombreGrupo').prop('readonly', false); // Editable
                    $('#editarCuposGrupo').prop('readonly', false); // Editable
                } else { // Tipo 'Regular'
                    $('#contenedorEditarCurso').show();
                    $('#editarCursoGrupo').val(data.nombre_curso || 'N/A');
                    $('#seccionAsociarMultigrado').show();
                    $('#editarNombreGrupo').prop('readonly', true); // No editable
                    $('#editarCuposGrupo').prop('readonly', false); // Editable

                    // Comprobar si está asociado a un padre
                    if (data.grupo_padre_id) {
                        $('#grupoMultigrado').prop('checked', true);
                        $('#contenedorGrupoPadre').show();
                        cargarGruposPadre($('#editarGrupoPadre'), data.grupo_padre_id);
                    } else {
                        $('#grupoMultigrado').prop('checked', false);
                        $('#contenedorGrupoPadre').hide();
                    }
                }

                $('#modalEditarGrupo').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'No se pudieron cargar los datos del grupo.', 'error');
            }
        });
    });

    // MANEJAR CAMBIO DE CHECKBOX "ASOCIAR A GRUPO MULTIGRADO"
    $(document).on('change', '#grupoMultigrado', function() {
        if ($(this).is(':checked')) {
            $('#contenedorGrupoPadre').show();
            cargarGruposPadre($('#editarGrupoPadre'), null);
        } else {
            $('#contenedorGrupoPadre').hide();
            $('#editarGrupoPadre').val('');
        }
    });

    // LÓGICA DE ELIMINACIÓN DE GRUPO (SIN CAMBIOS)
    $(document).on('click', '.btnEliminarGrupo', function() {
        var grupoId = $(this).data('grupo-id');
        var grupoNombre = $(this).data('grupo-nombre');
        $.ajax({
            url: 'ajax/obtener-oferta-educativa.php',
            type: 'POST',
            data: {
                accion: 'verificarEliminacionGrupo',
                grupoId: grupoId
            },
            dataType: 'json',
            success: function(data) {
                if (!data.puedeEliminar) {
                    var mensaje = 'Este grupo tiene estudiantes matriculados. No es posible eliminarlo.';
                    Swal.fire({
                        icon: 'error',
                        title: 'No se puede eliminar',
                        text: mensaje,
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
                var mensaje = grupoNombre;
                var textoAdicional = '';
                if (data.esUltimoGrupo) {
                    textoAdicional = '\n\nADVERTENCIA: Este es el último grupo del grado. Al eliminarlo también se eliminará la oferta educativa completa.';
                }
                Swal.fire({
                    title: '¿Está seguro de eliminar este grupo?',
                    text: mensaje + textoAdicional,
                    icon: data.esUltimoGrupo ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'No, cancelar',
                    confirmButtonText: 'Sí, eliminar'
                }).then(function(result) {
                    if (result.value) {
                        window.location = "index.php?ruta=oferta&idGrupo=" + grupoId;
                    }
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo verificar el estado del grupo',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    });

    // VALIDACIÓN DEL FORMULARIO DE AGREGAR OFERTA (SIN CAMBIOS)
    $('#formAgregarOfertaEducativa').submit(function(e) {
        var gradosSeleccionados = $('input[name="grados[]"]:checked').length;
        var gruposValidos = 0;
        if(gradosSeleccionados == 0) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "¡Error!",
                text: "Debe seleccionar al menos un grado",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            });
            return false;
        }
        $('input[name="grados[]"]:checked').each(function() {
            var gradoId = $(this).val();
            var esMultigrado = $('#multigrado_' + gradoId).is(':checked');
            if(esMultigrado) {
                var nombreManual = $('input[name="nombre_manual_' + gradoId + '"]').val();
                var cuposMultigrado = $('input[name="cupos_multigrado_' + gradoId + '"]').val();
                if(nombreManual && nombreManual.trim() !== '' && cuposMultigrado && parseInt(cuposMultigrado) > 0) {
                    gruposValidos++;
                }
            } else {
                var cursosDelGrado = $('input[name="cursos_' + gradoId + '[]"]');
                cursosDelGrado.each(function() {
                    var cursoId = $(this).val();
                    var cuposInput = $('input[name="cupos_' + gradoId + '_' + cursoId + '"]');
                    var cupos = cuposInput.val();
                    if(cupos && parseInt(cupos) > 0) {
                        gruposValidos++;
                    }
                });
            }
        });
        if(gruposValidos == 0) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "¡Error!",
                text: "Debe configurar al menos un grupo válido (con cursos y cupos o multigrado con nombre y cupos)",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            });
            return false;
        }
    });
});