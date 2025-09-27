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
                // Ordenar cursos: primero numéricos, luego alfabéticos
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
                            html += '<div class="grado-container" data-grado-id="' + grado.id + '">';
                            html += '<div class="checkbox">';
                            html += '<label>';
                            html += '<input type="checkbox" name="grados[]" value="' + grado.id + '" id="grado_' + grado.id + '" class="grado-checkbox"> ' + grado.nombre;
                            html += '</label>';
                            html += '</div>';
                            html += '<div class="cursos-container" id="cursos_' + grado.id + '" style="display: none; margin-left: 30px; margin-top: 10px;"></div>';
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
        var cursosContainer = $('#cursos_' + gradoId);

        if($(this).is(':checked')) {
            // Mostrar contenedor de cursos
            cursosContainer.show();
            cargarCursosParaGrado(gradoId);
        } else {
            // Ocultar contenedor de cursos y limpiar
            cursosContainer.hide().html('');
        }
    });

    // FUNCIÓN PARA CARGAR CURSOS PARA UN GRADO ESPECÍFICO
    function cargarCursosParaGrado(gradoId) {
        var sedeId = $('#sedeOferta').val();
        var jornadaId = $('#jornadaOferta').val();
        var anioLectivoId = $('#anioLectivo').val();

        if(sedeId && jornadaId && anioLectivoId) {
            // Obtener cursos ocupados para este grado
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

    // FUNCIÓN PARA MOSTRAR EL SIGUIENTE CURSO DISPONIBLE
    function mostrarSiguienteCursoDisponible(gradoId, cursosOcupados) {
        var html = '<div class="panel panel-default">';
        html += '<div class="panel-heading" style="background-color: #f5f5f5;"><strong>Cursos y Cupos</strong></div>';
        html += '<div class="panel-body" id="panel_body_' + gradoId + '">';

        // Obtener cursos ya agregados manualmente (para no perderlos al recargar)
        var cursosAgregados = [];
        var contenedorActual = $('#cursos_' + gradoId);

        // Buscar inputs hidden de cursos ya guardados
        contenedorActual.find('input[type="hidden"][name="cursos_' + gradoId + '[]"]').each(function() {
            var cursoId = $(this).val();
            var cupos = contenedorActual.find('input[type="hidden"][name="cupos_' + gradoId + '_' + cursoId + '"]').val();

            // Encontrar información del curso
            var cursoInfo = cursosDisponibles.find(function(curso) {
                return curso.id == cursoId;
            });

            if(cursoInfo && cupos) {
                var nombreGrado = $('#grado_' + gradoId).parent().text().trim();
                var nombreGrupo = nombreGrado + ' ' + cursoInfo.nombre + ' - ' + cupos + ' Cupos';

                cursosAgregados.push({
                    id: cursoId,
                    nombre: cursoInfo.nombre,
                    cupos: cupos,
                    nombreGrupo: nombreGrupo
                });
            }
        });

        // Combinar cursos ocupados con cursos ya agregados
        var cursosNoDisponibles = cursosOcupados.slice(); // Clonar array
        cursosAgregados.forEach(function(curso) {
            cursosNoDisponibles.push(curso.id.toString());
        });

        // Buscar el siguiente curso disponible
        var siguienteCurso = null;
        for(var i = 0; i < cursosDisponibles.length; i++) {
            var curso = cursosDisponibles[i];
            var cursoIdStr = curso.id.toString();

            if(!cursosNoDisponibles.includes(cursoIdStr) && !cursosNoDisponibles.includes(parseInt(curso.id))) {
                siguienteCurso = curso;
                break;
            }
        }

        // Mostrar cursos ya agregados
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
            // Mostrar el siguiente curso disponible
            html += '<h5>Agregar Nuevo Curso:</h5>';
            html += '<div class="row curso-row" style="margin-bottom: 10px; border: 2px dashed #5bc0de; padding: 15px; border-radius: 4px;">';

            // Checkbox del curso
            html += '<div class="col-md-3">';
            html += '<div class="checkbox">';
            html += '<label>';
            html += '<input type="checkbox" name="curso_temp_' + gradoId + '" value="' + siguienteCurso.id + '" class="curso-checkbox" data-grado="' + gradoId + '" data-curso="' + siguienteCurso.id + '"> ';
            html += '<strong>' + siguienteCurso.nombre + '</strong>';
            html += '</label>';
            html += '</div>';
            html += '</div>';

            // Input de cupos
            html += '<div class="col-md-3">';
            html += '<div class="input-group">';
            html += '<span class="input-group-addon">Cupos:</span>';
            html += '<input type="number" name="cupos_temp_' + gradoId + '_' + siguienteCurso.id + '" class="form-control cupos-input" min="1" max="50" disabled data-grado="' + gradoId + '" data-curso="' + siguienteCurso.id + '">';
            html += '</div>';
            html += '</div>';

            // Nombre del grupo (automático)
            html += '<div class="col-md-6">';
            html += '<div class="input-group">';
            html += '<span class="input-group-addon">Grupo:</span>';
            html += '<input type="text" class="form-control grupo-nombre" readonly id="grupo_temp_' + gradoId + '_' + siguienteCurso.id + '" placeholder="Seleccione curso y cupos">';
            html += '</div>';
            html += '</div>';

            html += '</div>';
        } else {
            // No hay más cursos disponibles
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

    // MANEJAR SELECCIÓN DE CURSOS
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

    // ACTUALIZAR NOMBRE DEL GRUPO
    $(document).on('input', '.cupos-input', function() {
        var gradoId = $(this).data('grado');
        var cursoId = $(this).data('curso');
        var cupos = $(this).val();

        if(cupos && cupos > 0) {
            // Obtener nombre del grado
            var nombreGrado = $('#grado_' + gradoId).parent().text().trim();

            // Obtener nombre del curso
            var nombreCurso = '';
            $.each(cursosDisponibles, function(index, curso) {
                if(curso.id == cursoId) {
                    nombreCurso = curso.nombre;
                    return false;
                }
            });

            var nombreGrupo = nombreGrado + ' ' + nombreCurso + ' - ' + cupos + ' Cupos';
            $('#grupo_temp_' + gradoId + '_' + cursoId).val(nombreGrupo);
        } else {
            $('#grupo_temp_' + gradoId + '_' + cursoId).val('');
        }
    });

    // CONFIRMAR CURSO AL PRESIONAR ENTER O AL SALIR DEL CAMPO DE CUPOS
    $(document).on('keypress blur', '.cupos-input', function(e) {
        if(e.type === 'keypress' && e.which !== 13) return; // Solo Enter o blur

        var gradoId = $(this).data('grado');
        var cursoId = $(this).data('curso');
        var cupos = $(this).val();
        var checkbox = $('input[name="curso_temp_' + gradoId + '"][value="' + cursoId + '"]');

        if(checkbox.is(':checked') && cupos && parseInt(cupos) > 0) {
            // Agregar el curso a la lista permanente
            agregarCursoConfirmado(gradoId, cursoId, cupos);
        }
    });

    // FUNCIÓN PARA AGREGAR CURSO CONFIRMADO
    function agregarCursoConfirmado(gradoId, cursoId, cupos) {
        // Crear inputs hidden para el envío del formulario
        var contenedor = $('#cursos_' + gradoId);

        // Agregar inputs hidden si no existen
        if(contenedor.find('input[name="cursos_' + gradoId + '[]"][value="' + cursoId + '"]').length === 0) {
            contenedor.append('<input type="hidden" name="cursos_' + gradoId + '[]" value="' + cursoId + '">');
            contenedor.append('<input type="hidden" name="cupos_' + gradoId + '_' + cursoId + '" value="' + cupos + '">');

            // Recargar la vista para mostrar el siguiente curso
            setTimeout(function() {
                cargarCursosParaGrado(gradoId);
            }, 300);
        }
    }

    // VERIFICAR CURSOS CUANDO CAMBIAN LOS SELECTORES
    $('#sedeOferta, #jornadaOferta, #anioLectivo').change(function() {
        // Recargar cursos para todos los grados seleccionados
        $('.grado-checkbox:checked').each(function() {
            var gradoId = $(this).val();
            cargarCursosParaGrado(gradoId);
        });
    });

    // CARGAR GRADOS PARA EDITAR CUANDO CAMBIA EL NIVEL
    $('#editarNivelEducativo').change(function() {
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
                    var html = '<option value="">Seleccione un Grado...</option>';

                    $.each(data, function(index, grado) {
                        html += '<option value="' + grado.id + '">' + grado.nombre + '</option>';
                    });

                    $('#editarGradoOferta').html(html);
                }
            });
        } else {
            $('#editarGradoOferta').html('<option value="">Seleccione un Grado...</option>');
        }
    });

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
                $('#verGrupoCurso').text(data.nombre_curso || 'No disponible');
                $('#verGrupoCupos').text(data.cupos || 'No disponible');
                $('#verGrupoNombre').text(data.nombre_grupo || 'No disponible');

                // Guardar IDs para el botón editar
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

    // MÉTODO PARA EDITAR GRUPO
    $(document).on('click', '.btnEditarGrupo, .btnEditarGrupoDesdeVer', function() {
        var grupoId = $(this).data('grupo-id');
        var ofertaId = $(this).data('oferta-id');

        // Cerrar modal de ver si está abierto
        $('#modalVerGrupo').modal('hide');

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
                // Llenar campos de contexto (solo lectura)
                $('#idGrupo').val(grupoId);
                $('#ofertaEducativaId').val(ofertaId);
                $('#editarGrupoAnio').val(data.anio || '');
                $('#editarGrupoSede').val(data.nombre_sede || '');
                $('#editarGrupoJornada').val(data.nombre_jornada || '');
                $('#editarGrupoNivel').val(data.nombre_nivel || '');
                $('#editarGrupoGrado').val(data.nombre_grado || '');
                $('#editarCuposGrupo').val(data.cupos || '');

                // Cargar cursos disponibles
                cargarCursosParaEdicion(data.grado_id, data.curso_id, data.sede_jornada_id, data.anio_lectivo_id, grupoId);

                $('#modalEditarGrupo').modal('show');
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

    // FUNCIÓN PARA CARGAR CURSOS EN EDICIÓN
    function cargarCursosParaEdicion(gradoId, cursoActualId, sedeJornadaId, anioLectivoId, grupoActualId) {
        $.ajax({
            url: 'ajax/obtener-oferta-educativa.php',
            type: 'POST',
            data: {
                accion: 'obtenerCursosParaEdicion',
                gradoId: gradoId,
                sedeJornadaId: sedeJornadaId,
                anioLectivoId: anioLectivoId,
                grupoActualId: grupoActualId
            },
            dataType: 'json',
            success: function(cursosDisponibles) {
                var html = '<option value="">Seleccione un Curso...</option>';

                $.each(cursosDisponibles, function(index, curso) {
                    var selected = (curso.id == cursoActualId) ? 'selected' : '';
                    html += '<option value="' + curso.id + '" ' + selected + '>' + curso.nombre + '</option>';
                });

                $('#editarCursoGrupo').html(html);

                // Actualizar nombre del grupo
                actualizarNombreGrupoEdicion();
            }
        });
    }

    // ACTUALIZAR NOMBRE DEL GRUPO EN EDICIÓN
    $(document).on('change input', '#editarCursoGrupo, #editarCuposGrupo', function() {
        actualizarNombreGrupoEdicion();
    });

    function actualizarNombreGrupoEdicion() {
        var grado = $('#editarGrupoGrado').val();
        var cursoId = $('#editarCursoGrupo').val();
        var cupos = $('#editarCuposGrupo').val();

        if(grado && cursoId && cupos) {
            var cursoNombre = $('#editarCursoGrupo option:selected').text();
            var nombreGrupo = grado + ' ' + cursoNombre + ' - ' + cupos + ' Cupos';
            $('#editarNombreGrupo').val(nombreGrupo);
        } else {
            $('#editarNombreGrupo').val('');
        }
    }

    // ELIMINAR GRUPO
    $(document).on('click', '.btnEliminarGrupo', function() {
        var grupoId = $(this).data('grupo-id');
        var grupoNombre = $(this).data('grupo-nombre');

        // Verificar si el grupo puede ser eliminado
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
                    var mensaje = '';
                    if (data.tieneEstudiantes) {
                        mensaje = 'Este grupo tiene estudiantes matriculados. No es posible eliminarlo.';
                    } else if (data.esUltimoGrupo && data.tieneReferencias) {
                        mensaje = 'Este es el último grupo del grado y la oferta educativa tiene referencias activas en: ' + data.referencias.join(', ') + '. No es posible eliminarlo.';
                    }
                    
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

    // VALIDACIÓN ANTES DE ENVIAR EL FORMULARIO
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

        // Validar que cada grado tenga al menos un curso con cupos
        $('input[name="grados[]"]:checked').each(function() {
            var gradoId = $(this).val();

            // Buscar inputs hidden de cursos confirmados
            var cursosDelGrado = $('input[name="cursos_' + gradoId + '[]"]');

            cursosDelGrado.each(function() {
                var cursoId = $(this).val();
                var cuposInput = $('input[name="cupos_' + gradoId + '_' + cursoId + '"]');
                var cupos = cuposInput.val();

                if(cupos && parseInt(cupos) > 0) {
                    gruposValidos++;
                }
            });
        });

        if(gruposValidos == 0) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                title: "¡Error!",
                text: "Debe confirmar al menos un curso con cupos válidos (presione Enter después de escribir los cupos)",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
            });
            return false;
        }
    });

    // ELIMINAR OFERTA EDUCATIVA
    $(document).on('click', '.btnEliminarOfertaEducativa', function() {
        var idOferta = $(this).data('id');
        var anio = $(this).data('anio');
        var sede = $(this).data('sede');
        var grado = $(this).data('grado');

        Swal.fire({
            title: '¿Está seguro de eliminar esta oferta educativa?',
            text: anio + " - " + sede + " - " + grado,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No, cancelar',
            confirmButtonText: 'Sí, eliminar'
        }).then(function(result) {
            if (result.value) {
                window.location = "index.php?ruta=oferta&idOferta=" + idOferta;
            }
        });
    });

});