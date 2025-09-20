console.log('Archivo estructura-curricular.js cargado');

$(document).ready(function() {
    console.log('Document ready ejecutado');

    // Inicializar DataTables
    $('#tablaAreas').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });
    
    // Activar pestaña según hash en URL
    if(window.location.hash) {
        $('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
    }
    
    // Inicializar DataTable de asignaturas cuando se muestre la pestaña
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if($(e.target).attr('href') === '#asignaturas') {
            if(!$.fn.DataTable.isDataTable('#tablaAsignaturas')) {
                $('#tablaAsignaturas').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false
                });
            }
        }
        


    });

    /*=============================================
    EDITAR ÁREA
    =============================================*/
    $(document).on('click', '.btnEditarArea', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/obtener-area.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                $('#idArea').val(data.id);
                $('#editarNombreArea').val(data.nombre);
                $('#modalEditarArea').modal('show');
            }
        });
    });

    /*=============================================
    VER ÁREA
    =============================================*/
    $(document).on('click', '.btnVerArea', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/obtener-area.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                $('#verNombreArea').text(data.nombre);
                $('.btnEditarArea[data-dismiss="modal"]').attr('data-id', id);
                $('#modalVerArea').modal('show');
            }
        });
    });

    /*=============================================
    EDITAR ASIGNATURA
    =============================================*/
    $(document).on('click', '.btnEditarAsignatura', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/obtener-asignatura.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                $('#idAsignatura').val(data.id);
                $('#editarNombreAsignatura').val(data.nombre);
                $('#editarAreaAsignatura').val(data.area_id);
                $('#modalEditarAsignatura').modal('show');
            }
        });
    });

    /*=============================================
    VER ASIGNATURA
    =============================================*/
    $(document).on('click', '.btnVerAsignatura', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/obtener-asignatura.php',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                $('#verNombreAsignatura').text(data.nombre);
                $('#verAreaAsignatura').text(data.nombre_area);
                $('.btnEditarAsignatura[data-dismiss="modal"]').attr('data-id', id);
                $('#modalVerAsignatura').modal('show');
            }
        });
    });

    /*=============================================
    FUNCIONALIDAD CURRÍCULO - CHECKBOX ÁREAS
    =============================================*/
    $(document).on('change', '.areaCheckbox', function() {
        var areaId = $(this).val();
        
        if($(this).is(':checked')) {
            // Cargar asignaturas del área
            $.ajax({
                url: 'ajax/obtener-asignatura.php',
                type: 'POST',
                data: { accion: 'obtener_por_area', area_id: areaId },
                dataType: 'json',
                success: function(data) {
                    var html = '';
                    $.each(data, function(index, asignatura) {
                        html += '<div class="checkbox">';
                        html += '<label>';
                        html += '<input type="checkbox" class="asignaturaCheckbox" value="' + asignatura.id + '" data-nombre="' + asignatura.nombre + '"> ';
                        html += asignatura.nombre;
                        html += '</label>';
                        html += '</div>';
                    });
                    $('#asignaturasArea').html(html);
                }
            });
        } else {
            $('#asignaturasArea').html('');
        }
    });

    /*=============================================
    MOVER ASIGNATURAS A ASIGNADAS
    =============================================*/
    $(document).on('change', '.asignaturaCheckbox', function() {
        var asignaturaId = $(this).val();
        var asignaturaNombre = $(this).data('nombre');
        
        if($(this).is(':checked')) {
            // Ocultar mensaje vacío
            $('#mensajeVacio').hide();
            
            // Agregar a asignadas como fila
            var html = '<div class="row" data-id="' + asignaturaId + '" style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">';
            html += '<div class="col-md-5">';
            html += '<strong>' + asignaturaNombre + '</strong>';
            html += '</div>';
            html += '<div class="col-md-4">';
            html += '<input type="number" class="form-control input-sm intensidadHoraria" min="1" max="10" value="1" placeholder="IHS">';
            html += '</div>';
            html += '<div class="col-md-3">';
            html += '<button type="button" class="btn btn-danger btn-xs btnRemoverAsignatura">Remover</button>';
            html += '</div>';
            html += '</div>';
            
            $('#asignaturasAsignadas').append(html);
        } else {
            // Remover de asignadas
            $('#asignaturasAsignadas').find('[data-id="' + asignaturaId + '"]').remove();
            
            // Mostrar mensaje vacío si no hay asignaturas
            if($('#asignaturasAsignadas .row').length === 0) {
                $('#mensajeVacio').show();
            }
        }
    });

    /*=============================================
    REMOVER ASIGNATURA ASIGNADA
    =============================================*/
    $(document).on('click', '.btnRemoverAsignatura', function() {
        var row = $(this).closest('.row');
        var asignaturaId = row.data('id');
        
        // Desmarcar checkbox correspondiente
        $('.asignaturaCheckbox[value="' + asignaturaId + '"]').prop('checked', false);
        
        // Remover fila
        row.remove();
        
        // Mostrar mensaje vacío si no hay asignaturas
        if($('#asignaturasAsignadas .row').length === 0) {
            $('#mensajeVacio').show();
        }
    });

    /*=============================================
    CREAR ASIGNATURA CON AJAX
    =============================================*/
    $(document).on('submit', '#formAgregarAsignatura', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: 'ajax/obtener-asignatura.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.trim() === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Correcto!',
                        text: 'La asignatura ha sido guardada correctamente'
                    }).then(function() {
                        $('#modalAgregarAsignatura').modal('hide');
                        $('#formAgregarAsignatura')[0].reset();
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Error al guardar la asignatura'
                    });
                }
            }
        });
    });

    /*=============================================
    GUARDAR CURRÍCULO
    =============================================*/
    $(document).on('click', '#btnGuardarCurriculo', function() {
        
        var asignaturas = [];
        var grados = [];
        
        // Obtener asignaturas asignadas con intensidad horaria
        $('#asignaturasAsignadas .row[data-id]').each(function() {
            var asignaturaId = $(this).data('id');
            var intensidad = $(this).find('.intensidadHoraria').val();
            
            if(intensidad && intensidad > 0) {
                asignaturas.push({
                    id: asignaturaId,
                    intensidad: intensidad
                });
            }
        });
        
        // Obtener grados seleccionados
        $('.gradoCheckbox:checked').each(function() {
            grados.push($(this).val());
        });
        
        if(asignaturas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe asignar al menos una asignatura'
            });
            return;
        }
        
        if(grados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe seleccionar al menos un grado'
            });
            return;
        }
        
        // Verificar si ya existe currículo para los grados seleccionados
        $.ajax({
            url: 'ajax/obtener-curriculo.php',
            type: 'POST',
            data: { accion: 'verificar', grados: grados },
            dataType: 'json',
            success: function(gradosExistentes) {
                if(gradosExistentes.length > 0) {
                    var nombresGrados = gradosExistentes.map(function(g) { return g.nombre; }).join(', ');
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Currículo ya existe',
                        html: 'Los siguientes grados ya tienen currículo asignado:<br><strong>' + nombresGrados + '</strong><br><br>¿Desea editarlo?',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, editar',
                        cancelButtonText: 'Cancelar'
                    }).then(function(result) {
                        if(result.isConfirmed) {
                            // Cambiar a pestaña Ver Currículo
                            $('.nav-tabs a[href="#ver-curriculo"]').tab('show');
                            
                            // Seleccionar el primer grado y abrir modal
                            setTimeout(function() {
                                $('.verGradoCheckbox[value="' + gradosExistentes[0].id + '"]').prop('checked', true).trigger('change');
                                setTimeout(function() {
                                    $('.btnEditarCurriculoGrado').click();
                                }, 1000);
                            }, 500);
                        }
                    });
                    return;
                }
                
                // Si no hay duplicados, proceder a guardar
                guardarCurriculo(asignaturas, grados);
            }
        });
    });
    
    function guardarCurriculo(asignaturas, grados) {
        $.ajax({
            url: 'ajax/obtener-curriculo.php',
            type: 'POST',
            data: {
                asignaturas: asignaturas,
                grados: grados
            },
            success: function(response) {
                if(response.trim() === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Correcto!',
                        text: 'Currículo guardado correctamente'
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Error al guardar el currículo: ' + response
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Error de conexión'
                });
            }
        });
    }

    /*=============================================
    VER CURRÍCULO - CHECKBOX GRADOS
    =============================================*/
    $(document).on('change', '.verGradoCheckbox', function() {
        var gradoId = $(this).val();
        
        if($(this).is(':checked')) {
            // Desmarcar otros checkboxes
            $('.verGradoCheckbox').not(this).prop('checked', false);
            
            // Cargar currículo del grado seleccionado
            $.ajax({
                url: 'ajax/obtener-curriculo.php',
                type: 'POST',
                data: { accion: 'obtener_simple', grado_id: gradoId },
                dataType: 'json',
                success: function(response) {
                    if(response.success && response.curriculo.length > 0) {
                        var html = '<h4 style="display: inline-block;">Currículo del Grado</h4>';
                        html += '<button class="btn btn-primary btn-sm pull-right btnEditarCurriculoGrado" data-grado="' + response.grado_id + '" data-toggle="modal" data-target="#modalEditarCurriculo">';
                        html += '<i class="fa fa-edit"></i> Editar</button>';
                        html += '<div class="clearfix"></div><br>';
                        html += '<table class="table table-bordered table-striped">';
                        html += '<thead><tr><th>Área</th><th>Asignatura</th><th>IHS</th></tr></thead><tbody>';
                        
                        response.curriculo.forEach(function(item) {
                            html += '<tr>';
                            html += '<td>' + item.nombre_area + '</td>';
                            html += '<td>' + item.nombre_asignatura + '</td>';
                            html += '<td>' + item.intensidad_horaria_semanal + '</td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table>';
                        $('#curriculoGrado').html(html);
                    } else {
                        $('#curriculoGrado').html('<h4>Currículo del Grado</h4><div class="alert alert-info">No hay currículo registrado para este grado</div>');
                    }
                }
            });
        } else {
            $('#curriculoGrado').html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Selecciona un grado para ver su currículo</div>');
        }
    });

    /*=============================================
    EDITAR CURRÍCULO - ABRIR MODAL
    =============================================*/
    $(document).on('click', '.btnEditarCurriculoGrado', function() {
        var gradoId = $(this).data('grado');
        $('#gradoEditando').val(gradoId);
        
        // Limpiar modal
        $('.areaCheckboxModal').prop('checked', false);
        $('#asignaturasAreaModal').html('');
        $('#asignaturasAsignadasModal').html('<p class="text-muted" id="mensajeVacioModal">No hay asignaturas asignadas</p>');
        
        // Cargar asignaturas actuales del grado
        $.ajax({
            url: 'ajax/obtener-curriculo.php',
            type: 'POST',
            data: { accion: 'obtener_json', grado_id: gradoId },
            dataType: 'json',
            success: function(curriculo) {
                if(curriculo.length > 0) {
                    $('#mensajeVacioModal').hide();
                    curriculo.forEach(function(item) {
                        var html = '<div class="row" data-id="' + item.asignatura_id + '" style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">';
                        html += '<div class="col-md-5"><strong>' + item.nombre_asignatura + '</strong></div>';
                        html += '<div class="col-md-4"><input type="number" class="form-control input-sm intensidadHorariaModal" min="1" max="10" value="' + item.intensidad_horaria_semanal + '" placeholder="IHS"></div>';
                        html += '<div class="col-md-3"><button type="button" class="btn btn-danger btn-xs btnRemoverAsignaturaModal">Remover</button></div>';
                        html += '</div>';
                        $('#asignaturasAsignadasModal').append(html);
                    });
                }
            }
        });
    });

    /*=============================================
    MODAL - CHECKBOX ÁREAS
    =============================================*/
    $(document).on('change', '.areaCheckboxModal', function() {
        var areaId = $(this).val();
        
        if($(this).is(':checked')) {
            $.ajax({
                url: 'ajax/obtener-asignatura.php',
                type: 'POST',
                data: { accion: 'obtener_por_area', area_id: areaId },
                dataType: 'json',
                success: function(data) {
                    var html = '';
                    $.each(data, function(index, asignatura) {
                        html += '<div class="checkbox">';
                        html += '<label>';
                        html += '<input type="checkbox" class="asignaturaCheckboxModal" value="' + asignatura.id + '" data-nombre="' + asignatura.nombre + '"> ';
                        html += asignatura.nombre;
                        html += '</label>';
                        html += '</div>';
                    });
                    $('#asignaturasAreaModal').html(html);
                }
            });
        } else {
            $('#asignaturasAreaModal').html('');
        }
    });

    /*=============================================
    MODAL - MOVER ASIGNATURAS A ASIGNADAS
    =============================================*/
    $(document).on('change', '.asignaturaCheckboxModal', function() {
        var asignaturaId = $(this).val();
        var asignaturaNombre = $(this).data('nombre');
        
        if($(this).is(':checked')) {
            $('#mensajeVacioModal').hide();
            
            var html = '<div class="row" data-id="' + asignaturaId + '" style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">';
            html += '<div class="col-md-5"><strong>' + asignaturaNombre + '</strong></div>';
            html += '<div class="col-md-4"><input type="number" class="form-control input-sm intensidadHorariaModal" min="1" max="10" value="1" placeholder="IHS"></div>';
            html += '<div class="col-md-3"><button type="button" class="btn btn-danger btn-xs btnRemoverAsignaturaModal">Remover</button></div>';
            html += '</div>';
            
            $('#asignaturasAsignadasModal').append(html);
        } else {
            $('#asignaturasAsignadasModal').find('[data-id="' + asignaturaId + '"]').remove();
            
            if($('#asignaturasAsignadasModal .row').length === 0) {
                $('#mensajeVacioModal').show();
            }
        }
    });

    /*=============================================
    MODAL - REMOVER ASIGNATURA
    =============================================*/
    $(document).on('click', '.btnRemoverAsignaturaModal', function() {
        var row = $(this).closest('.row');
        var asignaturaId = row.data('id');
        var gradoId = $('#gradoEditando').val();
        
        // Eliminar de la base de datos
        $.ajax({
            url: 'ajax/obtener-curriculo.php',
            type: 'POST',
            data: {
                accion: 'eliminar',
                grado_id: gradoId,
                asignatura_id: asignaturaId
            },
            success: function(response) {
                if(response.trim() === 'ok') {
                    // Eliminar visualmente
                    $('.asignaturaCheckboxModal[value="' + asignaturaId + '"]').prop('checked', false);
                    row.remove();
                    
                    if($('#asignaturasAsignadasModal .row[data-id]').length === 0) {
                        $('#mensajeVacioModal').show();
                    }
                    
                    // Recargar currículo mostrado
                    $('.verGradoCheckbox:checked').trigger('change');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar la asignatura'
                    });
                }
            }
        });
    });

    /*=============================================
    GUARDAR CAMBIOS CURRÍCULO
    =============================================*/
    $(document).on('click', '#btnGuardarCambiosCurriculo', function() {
        var asignaturas = [];
        var gradoId = $('#gradoEditando').val();
        
        // Obtener asignaturas asignadas con intensidad horaria
        $('#asignaturasAsignadasModal .row[data-id]').each(function() {
            var asignaturaId = $(this).data('id');
            var intensidad = $(this).find('.intensidadHorariaModal').val();
            
            if(intensidad && intensidad > 0) {
                asignaturas.push({
                    id: asignaturaId,
                    intensidad: intensidad
                });
            }
        });
        
        if(asignaturas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe asignar al menos una asignatura'
            });
            return;
        }
        
        $.ajax({
            url: 'ajax/obtener-curriculo.php',
            type: 'POST',
            data: {
                asignaturas: asignaturas,
                grados: [gradoId]
            },
            success: function(response) {
                if(response.trim() === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Correcto!',
                        text: 'Currículo actualizado correctamente'
                    }).then(function() {
                        $('#modalEditarCurriculo').modal('hide');
                        // Recargar currículo mostrado
                        $('.verGradoCheckbox:checked').trigger('change');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Error al actualizar el currículo: ' + response
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Error de conexión'
                });
            }
        });
    });



});