$(document).ready(function() {

    // ===================================================================
    //  SOLUCIÓN PROFESIONAL PARA MODALES ANIDADOS (APILAMIENTO)
    // ===================================================================
    $(document).on('show.bs.modal', '.modal', function() {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    $(document).on('hidden.bs.modal', '.modal', function () {
        $('.modal:visible').length && $(document.body).addClass('modal-open');
    });
    // ===================================================================

    // Variable global para almacenar el acudiente encontrado temporalmente
    var acudienteEncontradoTemporal = null;

    /*=============================================
    REGISTRAR ACUDIENTE NUEVO CON AJAX
    =============================================*/
    $(document).on('submit', '#formRegistrarAcudiente', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var documentoNuevo = $('input[name="numeroDocumentoAcudienteNuevo"]').val();

        Swal.fire({
            title: 'Registrando acudiente...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'ajax/obtener-matricula.php',
            method: 'POST',
            data: formData + "&accion=registrarAcudienteNuevo",
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalRegistrarAcudiente').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: '¡Registrado!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // MEJORA UX: Buscar automáticamente al acudiente recién registrado
                    var modalActivo = $('#modalMatricular').is(':visible')
                        ? $('#modalMatricular')
                        : $('#modalEditarMatricula');

                    if(modalActivo.length){
                        modalActivo.find('.buscar-acudiente-doc').val(documentoNuevo);
                        modalActivo.find('.btn-buscar-acudiente').click();
                    }

                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error del Servidor', text: 'No se pudo completar la solicitud.' });
            }
        });
    });

    /*=============================================
    FUNCIÓN REUTILIZABLE PARA RENDERIZAR LA LISTA DE ACUDIENTES
    =============================================*/
    function actualizarListaAcudientes(modal) {
        var acudientesArray = $(modal).data('acudientes') || [];
        var container = $(modal).find('.lista-acudientes-container');
        var hiddenInput = $(modal).find('.acudientes-data-input');
        var html = '';

        if (acudientesArray.length === 0) {
            html = '<p class="text-muted">No hay acudientes asignados.</p>';
        } else {
            $.each(acudientesArray, function(index, acudiente) {
                var obsHtml = acudiente.observacion ? `<br><small><em>Obs: ${acudiente.observacion}</em></small>` : '';
                var autorizadoHtml = acudiente.autorizado_recoger === 'Si' ? '<span class="label label-success">Autorizado</span>' : '<span class="label label-danger">No Autorizado</span>';
                html += `<div class="acudiente-item" style="padding: 8px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <strong>${acudiente.nombres_completos}</strong>
                                <br><small class="text-muted">${acudiente.parentesco} - ${acudiente.tipo_documento || 'N/A'} ${acudiente.numero_documento || 'N/A'}</small>
                                ${obsHtml}
                            </div>
                            <div>
                                ${autorizadoHtml}
                                <button type="button" class="btn btn-warning btn-xs btn-editar-acudiente" data-index="${index}" style="margin-left: 5px;" title="Editar Acudiente"><i class="fa fa-edit"></i></button>
                                <button type="button" class="btn btn-danger btn-xs btn-eliminar-acudiente" data-index="${index}" style="margin-left: 5px;" title="Eliminar Acudiente"><i class="fa fa-times"></i></button>
                            </div>
                        </div>`;
            });
        }
        container.html(html);
        $(hiddenInput).val(JSON.stringify(acudientesArray));
    }

    $('#modalMatricular, #modalEditarMatricula').on('show.bs.modal', function () {
        if ($(this).data('loaded')) return;
        $(this).find('form')[0].reset();
        $(this).data('acudientes', []);
        actualizarListaAcudientes(this);
        $(this).find('.formulario-acudiente-container, .acudiente-encontrado-container').hide();
        $(this).find('.buscar-acudiente-doc').val('');
        acudienteEncontradoTemporal = null;
    });

    /*=============================================
    LÓGICA DE BÚSQUEDA DE ESTUDIANTE (MODIFICADO)
    =============================================*/
    $(document).on('click', '#btnBuscarDocumento, #btnBuscarNombres', function() {
        var esPorDoc = $(this).attr('id') === 'btnBuscarDocumento';
        var valor = esPorDoc ? $('#buscarDocumento').val() : $('#buscarNombres').val();
        var dataToSend = esPorDoc ? { buscarEstudianteDoc: valor } : { buscarEstudianteNombres: valor };

        if (!valor || valor.length < 3) {
            Swal.fire("Atención", "Debe ingresar un criterio de búsqueda válido (mínimo 3 caracteres).", "warning");
            return;
        }

        $('#resultadoBusqueda, #estudianteNoEncontrado, #opcionMatricular, #estudianteYaMatriculado').hide();

        Swal.fire({ title: 'Buscando Estudiante...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: 'ajax/obtener-matricula.php',
            method: 'POST',
            data: dataToSend,
            dataType: 'json',
            success: function(data) {
                Swal.close();
                if (data && data.id_usuario) {
                    $('#resultadoBusqueda').show();

                    if (data.estado_matricula === 'Matriculado') {
                        let mensaje = `El estudiante <strong>${data.nombres_usuario} ${data.apellidos_usuario}</strong> ya se encuentra matriculado en la <strong>Sede ${data.nombre_sede}</strong> en el grado <strong>${data.grado_completo}</strong>.`;
                        $('#mensajeMatriculaExistente').html(mensaje);
                        $('#estudianteYaMatriculado').show();
                        $('#opcionMatricular').hide();

                    } else {
                        $('#tipoDocEncontrado').text(data.tipo_documento);
                        $('#numeroDocEncontrado').text(data.numero_documento);
                        $('#nombresEncontrados').text(data.nombres_usuario + ' ' + data.apellidos_usuario);
                        $('#btnMatricular').attr('data-id-usuario', data.id_usuario);
                        $('#opcionMatricular').show();
                        $('#estudianteYaMatriculado').hide();
                    }
                    $('#estudianteNoEncontrado').hide();
                } else {
                    $('#resultadoBusqueda').hide();
                    $('#estudianteNoEncontrado').show();
                }
            },
            error: function() {
                Swal.close();
                Swal.fire("Error del Servidor", "No se pudo completar la búsqueda. Inténtelo de nuevo.", "error");
            }
        });
    });

    $(document).on('click', '#btnMatricular', function() {
        var idUsuario = $(this).attr('data-id-usuario');
        $('#idUsuarioEstudiante').val(idUsuario);
        $('#modalMatricular').modal('show');
    });

    $(document).on('click', '.btn-buscar-acudiente', function() {
        var modal = $(this).closest('.modal');
        var documento = modal.find('.buscar-acudiente-doc').val();
        if (!documento || documento.length < 5) { Swal.fire("Atención", "Debe ingresar un número de documento válido.", "warning"); return; }
        Swal.fire({ title: 'Buscando Acudiente...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            url: 'ajax/obtener-matricula.php', method: 'POST', data: { buscarAcudiente: documento }, dataType: 'json',
            success: function(data) {
                Swal.close();
                if (data && data.id_usuario) {
                    acudienteEncontradoTemporal = data;
                    var container = modal.find('.acudiente-encontrado-container');
                    container.html(`<div class="alert alert-info"><i class="fa fa-check"></i> Acudiente: <strong>${data.nombres_usuario} ${data.apellidos_usuario}</strong></div>`);
                    container.show();
                    modal.find('.formulario-acudiente-container').show().find('select, input').val('');
                } else {
                    $('#numeroDocumentoAcudienteNuevo').val(documento);
                    $('#modalRegistrarAcudiente').modal('show');
                }
            }
        });
    });

    $(document).on('click', '.btn-agregar-acudiente', function() {
        var modal = $(this).closest('.modal');
        var acudientesArray = $(modal).data('acudientes') || [];
        var parentesco = modal.find('.parentesco-acudiente').val();
        var esFirmante = modal.find('.es-firmante-acudiente').val();
        var autorizado = modal.find('.autorizado-recoger-acudiente').val();
        var editingIndex = $(this).data('editing-index');
        
        if (!parentesco || !esFirmante || !autorizado) { Swal.fire("Atención", "Debe completar todos los campos requeridos.", "warning"); return; }
        
        if (acudienteEncontradoTemporal) {
            var acudienteData = {
                id_usuario: acudienteEncontradoTemporal.id_usuario,
                tipo_documento: acudienteEncontradoTemporal.tipo_documento,
                numero_documento: acudienteEncontradoTemporal.numero_documento,
                nombres_completos: `${acudienteEncontradoTemporal.nombres_usuario} ${acudienteEncontradoTemporal.apellidos_usuario}`,
                parentesco: parentesco, es_firmante_principal: esFirmante, autorizado_recoger: autorizado,
                observacion: modal.find('.observacion-acudiente').val()
            };
            
            if (editingIndex !== undefined) {
                // Actualizar acudiente existente
                acudientesArray[editingIndex] = acudienteData;
                $(this).text('Agregar Acudiente a la Lista').removeClass('btn-warning').addClass('btn-success').removeData('editing-index');
            } else {
                // Agregar nuevo acudiente
                if (acudientesArray.some(item => item.id_usuario === acudienteEncontradoTemporal.id_usuario)) { Swal.fire("Duplicado", "Este acudiente ya está en la lista.", "info"); return; }
                acudientesArray.push(acudienteData);
            }
            
            $(modal).data('acudientes', acudientesArray);
            actualizarListaAcudientes(modal);
            acudienteEncontradoTemporal = null;
            modal.find('.buscar-acudiente-doc').val('');
            modal.find('.formulario-acudiente-container, .acudiente-encontrado-container').hide();
        } else { Swal.fire("Error", "Primero debe buscar y encontrar un acudiente válido.", "error"); }
    });

    $(document).on('click', '.btn-editar-acudiente', function() {
        var modal = $(this).closest('.modal');
        var index = $(this).data('index');
        var acudientesArray = $(modal).data('acudientes') || [];
        var acudiente = acudientesArray[index];
        
        // Cargar datos del acudiente en el formulario
        modal.find('.parentesco-acudiente').val(acudiente.parentesco);
        modal.find('.es-firmante-acudiente').val(acudiente.es_firmante_principal || 'No');
        modal.find('.autorizado-recoger-acudiente').val(acudiente.autorizado_recoger);
        modal.find('.observacion-acudiente').val(acudiente.observacion || '');
        
        // Mostrar información del acudiente
        var container = modal.find('.acudiente-encontrado-container');
        container.html(`<div class="alert alert-warning"><i class="fa fa-edit"></i> Editando: <strong>${acudiente.nombres_completos}</strong></div>`);
        container.show();
        
        // Mostrar formulario y cambiar botón
        modal.find('.formulario-acudiente-container').show();
        modal.find('.btn-agregar-acudiente').text('Actualizar Acudiente').removeClass('btn-success').addClass('btn-warning').data('editing-index', index);
        
        // Simular acudiente encontrado para el proceso de actualización
        acudienteEncontradoTemporal = {
            id_usuario: acudiente.id_usuario,
            nombres_usuario: acudiente.nombres_completos.split(' ')[0],
            apellidos_usuario: acudiente.nombres_completos.split(' ').slice(1).join(' ')
        };
    });

    $(document).on('click', '.btn-eliminar-acudiente', function() {
        var modal = $(this).closest('.modal');
        var index = $(this).data('index');
        var acudientesArray = $(modal).data('acudientes') || [];
        acudientesArray.splice(index, 1);
        $(modal).data('acudientes', acudientesArray);
        actualizarListaAcudientes(modal);
    });

    $(document).on('click', '.btnEditarMatricula', function() {
        var idMatricula = $(this).data("id");
        var modal = $('#modalEditarMatricula');
        if ($('#modalVerMatricula').is(':visible')) { $('#modalVerMatricula').modal('hide'); }
        modal.data('loaded', true);
        modal.modal('show');
        Swal.fire({ title: 'Cargando datos...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            url: "ajax/obtener-matricula.php", method: "POST", data: { idMatricula: idMatricula }, dataType: "json",
            success: function(respuesta) {
                Swal.close();
                if(!respuesta){ Swal.fire("Error", "No se pudo cargar la información.", "error"); modal.modal('hide'); return; }
                $("#editarEstudianteNombre").text(respuesta.estudiante_nombres + " " + respuesta.estudiante_apellidos);
                $("#editarEstudianteTipoDoc").text(respuesta.estudiante_tipo_documento);
                $("#editarEstudianteNumeroDoc").text(respuesta.estudiante_documento);
                $("#idMatricula").val(respuesta.matricula_id);
                $("#editarNumeroMatricula").val(respuesta.numero_matricula);
                $("#editarFechaMatricula").val(respuesta.fecha_matricula);
                $("#editarEstudianteNuevo").val(respuesta.nuevo);
                $("#editarEsRepitente").val(respuesta.repitente);
                $("#editarEstadoMatricula").val(respuesta.estado_matricula);
                $("#editarFechaIngreso").val(respuesta.fecha_inicio);
                $("#editarEstadoAnioAnterior").val("promovido"); // Valor por defecto
                var acudientesParaEditar = respuesta.acudientes.map(function(acu) {
                    return { id_usuario: acu.acudiente_usuario_id, tipo_documento: acu.acudiente_tipo_documento, numero_documento: acu.acudiente_documento, nombres_completos: `${acu.acudiente_nombres} ${acu.acudiente_apellidos}`, parentesco: acu.parentesco, es_firmante_principal: acu.es_firmante_principal, autorizado_recoger: acu.autorizado_recoger, observacion: acu.observaciones };
                });
                $(modal).data('acudientes', acudientesParaEditar);
                actualizarListaAcudientes(modal);
                cargarSelectsDependientes(respuesta.grupo_id);
            },
            complete: function() { modal.data('loaded', false); }
        });
    });

    function cargarSelectsDependientes(grupoId) {
        $.ajax({
            url: "ajax/obtener-matricula.php", method: "POST", data: { obtenerSedeDeGrupo: grupoId }, dataType: "json",
            success: function(sedeData) {
                if (sedeData && sedeData.sede_id) {
                    $('#editarSedeMatricula').val(sedeData.sede_id);
                    cargarGrados('#editarSedeMatricula', $('#editarGradoMatricula'), sedeData.grado_id, function() {
                        cargarGrupos($('#editarGradoMatricula'), $('#editarGrupoMatricula'), $('#editarSedeMatricula'), grupoId);
                    });
                }
            }
        });
    }

    function cargarGrados(selectSede, selectGrado, gradoIdSeleccionar, callback) {
        var sedeId = $(selectSede).val();
        $(selectGrado).html('<option value="">Cargando...</option>');
        if (sedeId) {
            $.post("ajax/obtener-matricula.php", { obtenerGradosPorSede: sedeId }, function(grados) {
                $(selectGrado).html('<option value="">Seleccione un grado</option>');
                $.each(grados, function(i, grado) { $(selectGrado).append(`<option value="${grado.id}">${grado.numero}° - ${grado.nombre}</option>`); });
                if (gradoIdSeleccionar) $(selectGrado).val(gradoIdSeleccionar);
                if (callback) callback();
            }, 'json');
        } else { $(selectGrado).html('<option value="">Seleccione una sede</option>'); }
    }

    function cargarGrupos(selectGrado, selectGrupo, selectSede, grupoIdSeleccionar) {
        var gradoId = $(selectGrado).val();
        var sedeId = $(selectSede).val();
        $(selectGrupo).html('<option value="">Cargando...</option>');
        if (gradoId && sedeId) {
            $.post("ajax/obtener-matricula.php", { obtenerGruposPorGrado: gradoId, sedeId: sedeId }, function(grupos) {
                $(selectGrupo).html('<option value="">Seleccione un grupo</option>');
                $.each(grupos, function(i, grupo) { $(selectGrupo).append(`<option value="${grupo.grupo_id}">${grupo.grupo_nombre} (${grupo.jornada})</option>`); });
                if (grupoIdSeleccionar) $(selectGrupo).val(grupoIdSeleccionar);
            }, 'json');
        } else { $(selectGrupo).html('<option value="">Seleccione un grado</option>'); }
    }

    $('#sedeMatricula, #editarSedeMatricula').on('change', function() {
        var esEdicion = $(this).attr('id').includes('editar');
        cargarGrados(this, esEdicion ? '#editarGradoMatricula' : '#gradoMatricula');
        $(esEdicion ? '#editarGrupoMatricula' : '#grupoMatricula').html('<option value="">Seleccione un grado</option>');
    });

    $('#gradoMatricula, #editarGradoMatricula').on('change', function() {
        var esEdicion = $(this).attr('id').includes('editar');
        cargarGrupos(this, esEdicion ? '#editarGrupoMatricula' : '#grupoMatricula', esEdicion ? '#editarSedeMatricula' : '#sedeMatricula');
    });

    /*=============================================
    VER MATRÍCULA COMPLETA
    =============================================*/
    $(document).on('click', '.btnVerMatricula', function() {
        var idMatricula = $(this).data('id');
        var modal = $('#modalVerMatricula');
        Swal.fire({ title: 'Cargando Información...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: 'ajax/obtener-matricula.php',
            method: 'POST',
            data: { idMatricula: idMatricula },
            dataType: 'json',
            success: function(data) {
                Swal.close();
                if (!data) { Swal.fire("Error", "No se pudo cargar la información.", "error"); return; }

                modal.find('#verInstitucionEncabezado').text(data.institucion_nombre || 'N/A');
                modal.find('#verSedeEncabezado').text(data.nombre_sede || 'N/A');
                modal.find('#verNumeroMatricula').text(data.numero_matricula || 'N/A');
                modal.find('#verFechaMatricula').text(data.fecha_matricula || 'N/A');
                modal.find('#verEstudianteNuevo').text(data.nuevo || 'N/A');
                modal.find('#verEsRepitente').text(data.repitente || 'N/A');
                modal.find('#verEstadoMatricula').text(data.estado_matricula || 'N/A');
                modal.find('#verEstudianteNombres').text(data.estudiante_nombres + ' ' + data.estudiante_apellidos);
                modal.find('#verEstudianteSexo').text(data.estudiante_sexo || 'N/A');
                modal.find('#verEstudianteTipoDoc').text(data.estudiante_tipo_documento || 'N/A');
                modal.find('#verEstudianteNumeroDoc').text(data.estudiante_documento || 'N/A');
                modal.find('#verEstudianteRH').text(data.estudiante_rh || 'N/A');
                modal.find('#verEstudianteFechaNac').text(data.estudiante_fecha_nacimiento || 'N/A');
                modal.find('#verEstudianteEdad').text(data.estudiante_edad ? data.estudiante_edad + ' años' : 'N/A');
                modal.find('#verEstudianteTelefono').text(data.estudiante_telefono || 'N/A');
                modal.find('#verEstudianteEmail').text(data.estudiante_email || 'N/A');
                modal.find('#verCodigoEstudiante').text(data.codigo_estudiante || 'N/A');
                modal.find('#verFechaIngreso').text(data.fecha_ingreso || 'N/A');
                modal.find('#verEstadoAnioAnterior').text(data.estado_anio_anterior || 'N/A');
                modal.find('#verJornada').text(data.jornada || 'N/A');
                modal.find('#verGrado').text(data.grado_completo || 'N/A');
                modal.find('#verCurso').text(data.curso_nombre || 'N/A');
                modal.find('#verGrupoMatricula').text(data.grupo_nombre || 'N/A');
                modal.find('#verCuposGrupo').text(data.grupo_cupos || 'N/A');

                var acudientesHtml = '';
                if (data.acudientes && data.acudientes.length > 0) {
                    $.each(data.acudientes, function(index, acudiente) {
                        acudientesHtml += `
                        <div class="well well-sm" style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-xs-8">
                                    <strong>${acudiente.acudiente_nombres || ''} ${acudiente.acudiente_apellidos || ''}</strong>
                                    <br><small><strong>Documento:</strong> ${acudiente.acudiente_tipo_documento || 'N/A'} - ${acudiente.acudiente_documento || 'N/A'}</small>
                                    <br><small><strong>Parentesco:</strong> ${acudiente.parentesco} | <strong>Tel:</strong> ${acudiente.acudiente_telefono || 'N/A'} | <strong>Email:</strong> ${acudiente.acudiente_email || 'N/A'}</small>
                                    ${acudiente.observaciones ? `<br><small><strong>Observación:</strong> ${acudiente.observaciones}</small>` : ''}
                                </div>
                                <div class="col-xs-4 text-right">
                                    <small><strong>¿Autorizado para recoger?</strong></small><br>
                                    <span class="label ${acudiente.autorizado_recoger === 'Si' ? 'label-success' : 'label-danger'}">
                                        ${acudiente.autorizado_recoger === 'Si' ? 'Sí' : 'No'}
                                    </span>
                                </div>
                            </div>
                        </div>`;
                    });
                } else {
                    acudientesHtml = '<p class="text-muted">No hay acudientes registrados.</p>';
                }

                modal.find('#verAcudientes').html(acudientesHtml);
                modal.find('.btnEditarMatricula').data('id', idMatricula);
                modal.modal('show');
            }
        });
    });

    $('#formMatricular, #formEditarMatricula').on('submit', function(e){
        var acudientes = $(this).find('.acudientes-data-input').val();
        if(acudientes === '[]' || acudientes === ''){ e.preventDefault(); Swal.fire("Atención", "Debe agregar al menos un acudiente a la matrícula.", "warning"); }
    });

    $(document).on("click", ".btnEliminarMatricula", function(){
        var idMatricula = $(this).data("id");
        Swal.fire({
            title: '¿Está seguro de borrar la matrícula?', text: "¡Esta acción no se puede deshacer!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar', confirmButtonText: '¡Sí, borrar!'
        }).then((result) => {
            if (result.isConfirmed) { window.location = "index.php?ruta=matricula&idMatricula=" + idMatricula; }
        });
    });

    /*=============================================
    GENERAR REPORTE PDF (SERVIDOR)
    =============================================*/
    $(document).on("click", "#btnDescargarPDF", function() {
        var idMatricula = $("#modalVerMatricula").find(".btnEditarMatricula").data("id");

        if (idMatricula) {
            window.open("ajax/reportes/generar-reporte-matricula.php?id=" + idMatricula, "_blank");
        } else {
            Swal.fire("Error", "No se pudo obtener el ID de la matrícula.", "error");
        }
    });
});