/*=============================================
VARIABLES GLOBALES
=============================================*/
let periodoSeleccionado = null;
let asignacionSeleccionada = null;
let estudiantesData = [];

/*=============================================
INICIALIZACIÓN
=============================================*/
$(document).ready(function() {
    // Obtener cuerpo docente del usuario actual
    obtenerCuerpoDocenteUsuario();
    
    // Event listeners
    $('.periodo-radio').on('change', manejarSeleccionPeriodo);
    $('#grado-grupo-select').on('change', manejarSeleccionGradoGrupo);
    $('#asignatura-select').on('change', manejarSeleccionAsignatura);
    $('#cargar-estudiantes').on('click', cargarEstudiantes);
    $('#marcar-todos-ausentes').on('click', marcarTodosAusentes);
    $('#guardar-asistencia').on('click', guardarAsistencia);
});

/*=============================================
OBTENER CUERPO DOCENTE DEL USUARIO ACTUAL
=============================================*/
function obtenerCuerpoDocenteUsuario() {
    // Obtener desde variable global inyectada por PHP
    window.cuerpoDocenteId = window.cuerpoDocenteIdGlobal || null;
    
    if (!window.cuerpoDocenteId) {
        Swal.fire("Error", "No se pudo identificar el docente. Contacte al administrador.", "error");
    }
}

/*=============================================
MANEJAR SELECCIÓN DE PERÍODO
=============================================*/
function manejarSeleccionPeriodo() {
    if ($(this).is(':checked')) {
        periodoSeleccionado = $(this).val();
        cargarGradosGrupos();
        // Limpiar selecciones dependientes
        $('#grado-grupo-select').html('<option value="">Seleccione grado y grupo...</option>');
        $('#asignatura-select').html('<option value="">Seleccione asignatura...</option>');
        $('#segunda-fila').hide();
        limpiarTablaEstudiantes();
        $('#registro-asistencia').hide();
        asignacionSeleccionada = null;
    } else {
        periodoSeleccionado = null;
        $('#grado-grupo-select').html('<option value="">Seleccione grado y grupo...</option>');
        $('#asignatura-select').html('<option value="">Seleccione asignatura...</option>');
        $('#segunda-fila').hide();
        limpiarTablaEstudiantes();
        $('#registro-asistencia').hide();
        asignacionSeleccionada = null;
    }
}

/*=============================================
CARGAR GRADOS Y GRUPOS
=============================================*/
function cargarGradosGrupos() {
    console.log('Cargando grados y grupos...');
    console.log('Cuerpo Docente ID:', window.cuerpoDocenteId);
    console.log('Período Seleccionado:', periodoSeleccionado);
    
    if (!window.cuerpoDocenteId || !periodoSeleccionado) {
        console.error('Faltan datos:', {cuerpoDocenteId: window.cuerpoDocenteId, periodoSeleccionado});
        return;
    }
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: {
            cuerpo_docente_id: window.cuerpoDocenteId,
            periodo_id: periodoSeleccionado
        },
        dataType: "json",
        success: function(respuesta) {
            console.log('Respuesta AJAX:', respuesta);
            
            let options = '<option value="">Seleccione grado y grupo...</option>';
            let gruposUnicos = {};
            
            if (respuesta && respuesta.length > 0) {
                // Agrupar por grado y grupo para evitar duplicados
                respuesta.forEach(function(asignacion) {
                    let key = asignacion.grupo_id;
                    if (!gruposUnicos[key]) {
                        gruposUnicos[key] = {
                            grupo_id: asignacion.grupo_id,
                            nombre_grupo: asignacion.nombre_grupo,
                            grado: asignacion.grado,
                            grado_numero: asignacion.grado_numero
                        };
                    }
                });
                
                // Convertir a array y ordenar
                let gruposArray = Object.values(gruposUnicos);
                gruposArray.sort((a, b) => a.grado_numero - b.grado_numero);
                
                gruposArray.forEach(function(grupo) {
                    options += `<option value="${grupo.grupo_id}">
                               ${grupo.grado} - ${grupo.nombre_grupo}
                               </option>`;
                });
            } else {
                console.warn('No se encontraron grupos');
                options += '<option value="">No hay grupos disponibles</option>';
            }
            
            $('#grado-grupo-select').html(options);
        },
        error: function(xhr, status, error) {
            console.error("Error cargando grupos:", error);
            console.error("Respuesta del servidor:", xhr.responseText);
            Swal.fire("Error", "No se pudieron cargar los grupos", "error");
        }
    });
}

/*=============================================
MANEJAR SELECCIÓN DE GRADO Y GRUPO
=============================================*/
function manejarSeleccionGradoGrupo() {
    const grupoSeleccionado = $(this).val();
    
    // Limpiar tabla de estudiantes cuando cambie el grupo
    $('#registro-asistencia').hide();
    estudiantesData = [];
    
    if (grupoSeleccionado) {
        cargarAsignaturas(grupoSeleccionado);
    } else {
        $('#asignatura-select').html('<option value="">Seleccione asignatura...</option>');
        $('#segunda-fila').hide();
    }
}

/*=============================================
CARGAR ASIGNATURAS DEL GRUPO
=============================================*/
function cargarAsignaturas(grupoId) {
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: {
            cuerpo_docente_id: window.cuerpoDocenteId,
            periodo_id: periodoSeleccionado
        },
        dataType: "json",
        success: function(respuesta) {
            let options = '<option value="">Seleccione asignatura...</option>';
            
            if (respuesta && respuesta.length > 0) {
                // Filtrar asignaturas del grupo seleccionado
                const asignaturasGrupo = respuesta.filter(a => a.grupo_id == grupoId);
                
                asignaturasGrupo.forEach(function(asignacion) {
                    options += `<option value="${asignacion.asignacion_id}" 
                               data-grupo-id="${asignacion.grupo_id}">
                               ${asignacion.asignatura} (${asignacion.area})
                               </option>`;
                });
                
                // Si solo hay una asignatura, seleccionarla automáticamente
                if (asignaturasGrupo.length === 1) {
                    setTimeout(function() {
                        $('#asignatura-select').val(asignaturasGrupo[0].asignacion_id).trigger('change');
                    }, 100);
                }
            }
            
            $('#asignatura-select').html(options);
        },
        error: function(xhr, status, error) {
            console.error("Error cargando asignaturas:", error);
            Swal.fire("Error", "No se pudieron cargar las asignaturas", "error");
        }
    });
}

/*=============================================
MANEJAR SELECCIÓN DE ASIGNATURA
=============================================*/
function manejarSeleccionAsignatura() {
    asignacionSeleccionada = $(this).val();
    
    if (asignacionSeleccionada) {
        $('#segunda-fila').show();
        // Cargar estudiantes automáticamente si hay grupo seleccionado
        const grupoId = $('#grado-grupo-select').val();
        if (grupoId) {
            setTimeout(function() {
                cargarEstudiantes();
            }, 100);
        }
    } else {
        $('#segunda-fila').hide();
        $('#registro-asistencia').hide();
        estudiantesData = [];
    }
}

/*=============================================
CARGAR ASIGNACIONES DEL DOCENTE (DEPRECATED)
=============================================*/
function cargarAsignacionesDocente() {
    console.log('Cargando asignaciones...');
    console.log('Cuerpo Docente ID:', window.cuerpoDocenteId);
    console.log('Período Seleccionado:', periodoSeleccionado);
    
    if (!window.cuerpoDocenteId || !periodoSeleccionado) {
        console.error('Faltan datos:', {cuerpoDocenteId: window.cuerpoDocenteId, periodoSeleccionado});
        return;
    }
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: {
            cuerpo_docente_id: window.cuerpoDocenteId,
            periodo_id: periodoSeleccionado
        },
        dataType: "json",
        success: function(respuesta) {
            console.log('Respuesta AJAX:', respuesta);
            
            let options = '<option value="">Seleccione una asignatura y grupo...</option>';
            
            if (respuesta && respuesta.length > 0) {
                respuesta.forEach(function(asignacion) {
                    options += `<option value="${asignacion.asignacion_id}" 
                               data-grupo-id="${asignacion.grupo_id}">
                               ${asignacion.asignatura} - ${asignacion.nombre_grupo} (${asignacion.grado})
                               </option>`;
                });
            } else {
                console.warn('No se encontraron asignaciones');
                options += '<option value="">No hay asignaciones disponibles</option>';
            }
            
            $('#asignacion-select').html(options);
        },
        error: function(xhr, status, error) {
            console.error("Error cargando asignaciones:", error);
            console.error("Respuesta del servidor:", xhr.responseText);
            Swal.fire("Error", "No se pudieron cargar las asignaciones", "error");
        }
    });
}

/*=============================================
MANEJAR SELECCIÓN DE ASIGNACIÓN
=============================================*/
function manejarSeleccionAsignacion() {
    asignacionSeleccionada = $(this).val();
    
    if (asignacionSeleccionada) {
        $('#configuracion-horario').show();
    } else {
        $('#configuracion-horario').hide();
        $('#registro-asistencia').hide();
    }
}

/*=============================================
CARGAR ESTUDIANTES
=============================================*/
function cargarEstudiantes() {
    if (!asignacionSeleccionada) {
        console.log('No hay asignación seleccionada, no se cargan estudiantes');
        return;
    }
    
    const grupoId = $('#grado-grupo-select').val();
    
    if (!grupoId) {
        console.log('No hay grupo seleccionado, no se cargan estudiantes');
        return;
    }
    
    console.log('Cargando estudiantes para grupo ID:', grupoId);
    
    // Limpiar tabla anterior
    limpiarTablaEstudiantes();
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: { grupo_id: grupoId },
        dataType: "json",
        success: function(estudiantes) {
            console.log('Estudiantes recibidos:', estudiantes);
            
            // Verificar si hay error en la respuesta
            if (estudiantes && estudiantes.error) {
                Swal.fire("Error", "Error del servidor: " + estudiantes.error, "error");
                return;
            }
            
            if (!estudiantes || estudiantes.length === 0) {
                Swal.fire("Información", "No se encontraron estudiantes matriculados en este grupo", "info");
                $('#registro-asistencia').hide();
                return;
            }
            
            estudiantesData = estudiantes;
            actualizarTituloTabla();
            renderizarTablaEstudiantes();
            $('#registro-asistencia').show();
            
            // Verificar si ya existe asistencia para esta fecha
            verificarAsistenciaExistente();
        },
        error: function(xhr, status, error) {
            console.error("Error cargando estudiantes:", error);
            console.error("Respuesta del servidor:", xhr.responseText);
            Swal.fire("Error", "No se pudieron cargar los estudiantes: " + error, "error");
        }
    });
}

/*=============================================
RENDERIZAR TABLA DE ESTUDIANTES
=============================================*/
function renderizarTablaEstudiantes() {
    let html = '';
    
    estudiantesData.forEach(function(estudiante, index) {
        html += `
        <tr id="estudiante-${estudiante.matricula_id}">
            <td>${index + 1}</td>
            <td>${estudiante.nombres_usuario} ${estudiante.apellidos_usuario}</td>
            <td>${estudiante.tipo_documento} ${estudiante.numero_documento}</td>
            <td>
                <div class="checkbox-container">
                    <input type="checkbox" data-matricula="${estudiante.matricula_id}" 
                           value="Pendiente" class="checkbox-pendiente">
                    <input type="checkbox" data-matricula="${estudiante.matricula_id}" 
                           value="Presente" class="checkbox-presente" checked>
                    <input type="checkbox" data-matricula="${estudiante.matricula_id}" 
                           value="Ausente" class="checkbox-ausente">
                </div>
            </td>
            <td class="estado-visual">
                <span class="btn btn-estado btn-presente">Presente</span>
            </td>
            <td class="justificacion-estado">No Aplica</td>
            <td>
                <button class="btn btn-warning btn-sm btnGestionarJustificacion" data-matricula="${estudiante.matricula_id}">
                    <i class="fa fa-edit"></i>
                </button>
            </td>
        </tr>`;
    });
    
    $('#lista-estudiantes').html(html);
    
    // Inicializar DataTables
    if ($.fn.DataTable.isDataTable('#tabla-asistencia')) {
        $('#tabla-asistencia').DataTable().destroy();
    }
    
    $('#tabla-asistencia').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 10,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
    
    // Event listeners para cambios de estado
    $('.checkbox-container input[type="checkbox"]').on('change', function() {
        manejarCambioCheckbox($(this));
    });
    
    // Inicializar atributos data para cada estudiante
    estudiantesData.forEach(function(estudiante) {
        const $fila = $(`#estudiante-${estudiante.matricula_id}`);
        $fila.attr('data-estado-final', 'Presente');
        $fila.attr('data-minutos-retraso', 0);
    });
    
    // Event listener para botones de gestionar justificación
    $('.btnGestionarJustificacion').on('click', function() {
        const matriculaId = $(this).data('matricula');
        abrirModalJustificacion(matriculaId);
    });
    
    // Iniciar verificación automática de retrasos
    iniciarVerificacionAutomatica();
}

/*=============================================
ACTUALIZAR TÍTULO DE LA TABLA
=============================================*/
function actualizarTituloTabla() {
    const grupoSeleccionado = $('#grado-grupo-select option:selected').text();
    const tituloCompleto = `Registro de Asistencia - ${grupoSeleccionado}`;
    
    $('.box-title').text(tituloCompleto);
}

/*=============================================
LIMPIAR TABLA DE ESTUDIANTES
=============================================*/
function limpiarTablaEstudiantes() {
    // Destruir DataTable si existe
    if ($.fn.DataTable.isDataTable('#tabla-asistencia')) {
        $('#tabla-asistencia').DataTable().destroy();
    }
    
    // Limpiar contenido
    $('#lista-estudiantes').html('');
    estudiantesData = [];
    
    // Restaurar título original
    $('.box-title').text('Registro de Asistencia');
}

/*=============================================
INICIAR VERIFICACIÓN AUTOMÁTICA DE RETRASOS
=============================================*/
function iniciarVerificacionAutomatica() {
    // Esta función puede implementarse para verificar retrasos automáticamente
    // Por ahora solo es un placeholder para evitar errores
    console.log('Verificación automática de retrasos iniciada');
}

/*=============================================
MANEJAR CAMBIO DE CHECKBOX
=============================================*/
function manejarCambioCheckbox($checkbox) {
    const matriculaId = $checkbox.data('matricula');
    const estado = $checkbox.val();
    const $fila = $(`#estudiante-${matriculaId}`);
    
    // Verificar si la asistencia está bloqueada
    if ($fila.attr('data-asistencia-bloqueada') === 'true') {
        // Revertir el cambio si está bloqueada
        $checkbox.prop('checked', !$checkbox.prop('checked'));
        return;
    }
    
    if ($checkbox.is(':checked')) {
        // Desmarcar otros checkboxes del mismo estudiante
        $(`.checkbox-container input[data-matricula="${matriculaId}"]`).not($checkbox).prop('checked', false);
        
        // Actualizar estado visual
        actualizarEstadoVisual(matriculaId, estado);
    } else {
        // Si se desmarca, volver a Presente (estado por defecto)
        $(`.checkbox-container input[data-matricula="${matriculaId}"][value="Presente"]`).prop('checked', true);
        actualizarEstadoVisual(matriculaId, 'Presente');
    }
}

/*=============================================
ACTUALIZAR ESTADO VISUAL
=============================================*/
function actualizarEstadoVisual(matriculaId, estado) {
    const $fila = $(`#estudiante-${matriculaId}`);
    
    // Actualizar estado visual
    let estadoHtml = '';
    let justificacionHtml = 'No Aplica';
    let estadoFinal = estado;
    let minutosRetraso = 0;
    
    if (estado === 'Presente') {
        // Calcular si hay retraso
        const minutos = calcularMinutosRetraso();
        const retrasoPermitido = parseInt($('#retraso-permitido').val()) || 15;
        
        if (minutos > 0) {
            if (minutos <= retrasoPermitido) {
                // Retraso dentro del límite permitido
                estadoHtml = `<span class="btn btn-estado btn-retraso">Retraso: ${minutos} min</span>`;
                estadoFinal = 'Retraso';
                minutosRetraso = minutos;
            } else {
                // Retraso supera el límite - marcar como Ausente automáticamente
                estadoHtml = '<span class="btn btn-estado btn-ausente">Ausente (Retraso excesivo)</span>';
                estadoFinal = 'Ausente';
                
                // Cambiar el checkbox a Ausente
                $(`.checkbox-container input[data-matricula="${matriculaId}"]`).prop('checked', false);
                $(`.checkbox-container input[data-matricula="${matriculaId}"][value="Ausente"]`).prop('checked', true);
                
                // Guardar automáticamente este estudiante
                guardarEstudianteAutomatico(matriculaId, 'Ausente', 0);
            }
        } else {
            estadoHtml = '<span class="btn btn-estado btn-presente">Presente</span>';
        }
    } else {
        switch(estado) {
            case 'Ausente':
                estadoHtml = '<span class="btn btn-estado btn-ausente">Ausente</span>';
                justificacionHtml = 'Sin Justificar';
                break;
            case 'Pendiente':
                estadoHtml = '<span class="btn btn-estado btn-pendiente">Pendiente</span>';
                justificacionHtml = 'No Aplica';
                break;
        }
    }
    
    // Guardar el estado final y minutos calculados en atributos data
    $fila.attr('data-estado-final', estadoFinal);
    $fila.attr('data-minutos-retraso', minutosRetraso);
    
    $fila.find('.estado-visual').html(estadoHtml);
    $fila.find('.justificacion-estado').text(justificacionHtml);
}

/*=============================================
CALCULAR MINUTOS DE RETRASO
=============================================*/
function calcularMinutosRetraso() {
    const horaInicio = $('#hora-inicio').val();
    if (!horaInicio) return 0;
    
    const ahora = new Date();
    const horaActual = ahora.getHours().toString().padStart(2, '0') + ':' + 
                      ahora.getMinutes().toString().padStart(2, '0');
    
    // Limpiar formato de hora inicio (quitar segundos si los tiene)
    const horaInicioLimpia = horaInicio.substring(0, 5);
    
    console.log('Hora inicio:', horaInicioLimpia);
    console.log('Hora actual:', horaActual);
    
    const [horaIni, minIni] = horaInicioLimpia.split(':').map(Number);
    const [horaAct, minAct] = horaActual.split(':').map(Number);
    
    const minutosInicio = horaIni * 60 + minIni;
    const minutosActual = horaAct * 60 + minAct;
    
    console.log('Minutos inicio:', minutosInicio);
    console.log('Minutos actual:', minutosActual);
    
    const diferencia = minutosActual - minutosInicio;
    console.log('Diferencia calculada:', diferencia);
    
    return Math.max(0, diferencia);
}

/*=============================================
MARCAR TODOS COMO AUSENTES
=============================================*/
function marcarTodosAusentes() {
    estudiantesData.forEach(function(estudiante) {
        const $checkbox = $(`.checkbox-container input[data-matricula="${estudiante.matricula_id}"][value="Ausente"]`);
        $checkbox.prop('checked', true).trigger('change');
    });
    
    Swal.fire({
        title: "¡Asistencia Marcada!",
        text: "Todos los estudiantes han sido marcados como ausentes.",
        icon: "warning",
        timer: 2000,
        showConfirmButton: false
    });
}

/*=============================================
VERIFICAR ASISTENCIA EXISTENTE
=============================================*/
function verificarAsistenciaExistente() {
    const fecha = $('#fecha-clase').val();
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: {
            obtener_asistencia_existente: true,
            asignacion_id: asignacionSeleccionada,
            fecha: fecha
        },
        dataType: "json",
        success: function(asistenciaExistente) {
            if (asistenciaExistente.length > 0) {
                // Cargar asistencia existente
                asistenciaExistente.forEach(function(registro) {
                    const $fila = $(`#estudiante-${registro.matricula_id}`);
                    const $checkboxes = $(`.checkbox-container input[data-matricula="${registro.matricula_id}"]`);
                    
                    // Limpiar todos los checkboxes primero
                    $checkboxes.prop('checked', false);
                    
                    // Marcar el estado correspondiente según la base de datos
                    let estadoCheckbox = registro.estado;
                    let estadoHtml = '';
                    let justificacionHtml = registro.justificacion_estado || 'No Aplica';
                    
                    if (registro.estado === 'Retraso') {
                        estadoCheckbox = 'Presente';
                        estadoHtml = `<span class="btn btn-estado btn-retraso">Retraso: ${registro.minutos_retraso} min</span>`;
                        $fila.attr('data-estado-final', 'Retraso');
                        $fila.attr('data-minutos-retraso', registro.minutos_retraso);
                    } else if (registro.estado === 'Ausente') {
                        estadoHtml = '<span class="btn btn-estado btn-ausente">Ausente</span>';
                        justificacionHtml = registro.justificacion_estado || 'Sin Justificar';
                        $fila.attr('data-estado-final', 'Ausente');
                        $fila.attr('data-minutos-retraso', 0);
                    } else if (registro.estado === 'Presente') {
                        estadoHtml = '<span class="btn btn-estado btn-presente">Presente</span>';
                        $fila.attr('data-estado-final', 'Presente');
                        $fila.attr('data-minutos-retraso', 0);
                    }
                    
                    // Actualizar estado visual y justificación
                    $fila.find('.estado-visual').html(estadoHtml);
                    $fila.find('.justificacion-estado').text(justificacionHtml);
                    
                    // Marcar el checkbox correcto
                    const $checkbox = $(`.checkbox-container input[data-matricula="${registro.matricula_id}"][value="${estadoCheckbox}"]`);
                    $checkbox.prop('checked', true);
                    
                    // Deshabilitar checkboxes según reglas de negocio
                    if (registro.estado === 'Presente' || registro.estado === 'Ausente') {
                        $checkboxes.prop('disabled', true);
                        $fila.attr('data-asistencia-bloqueada', 'true');
                    } else if (registro.estado === 'Retraso') {
                        $checkboxes.not('[value="Presente"]').prop('disabled', true);
                        $fila.attr('data-asistencia-modificable', 'true');
                    }
                });
                
                // Actualizar horarios si existen
                if (asistenciaExistente[0].hora_inicio_clase) {
                    $('#hora-inicio').val(asistenciaExistente[0].hora_inicio_clase.substring(0, 5));
                }
                if (asistenciaExistente[0].hora_fin_clase) {
                    $('#hora-fin').val(asistenciaExistente[0].hora_fin_clase.substring(0, 5));
                }
            }
        }
    });
}

/*=============================================
GUARDAR ASISTENCIA
=============================================*/
function guardarAsistencia() {
    const fecha = $('#fecha-clase').val();
    const horaInicio = $('#hora-inicio').val();
    const horaFin = $('#hora-fin').val();
    
    if (!fecha || !horaInicio || !horaFin) {
        Swal.fire("Error", "Debe completar todos los campos de horario", "error");
        return;
    }
    
    // Recopilar asistencias - Enviar todos los estudiantes
    const asistencias = [];
    estudiantesData.forEach(function(estudiante) {
        const $fila = $(`#estudiante-${estudiante.matricula_id}`);
        const estadoFinal = $fila.attr('data-estado-final') || 'Presente';
        const minutosRetraso = parseInt($fila.attr('data-minutos-retraso')) || 0;
        
        // Buscar información del estudiante
        const estudianteInfo = estudiantesData.find(e => e.matricula_id == estudiante.matricula_id);
        const nombreCompleto = estudianteInfo ? `${estudianteInfo.nombres_usuario} ${estudianteInfo.apellidos_usuario}` : 'Desconocido';
        
        console.log(`Enviando estudiante ${estudiante.matricula_id} (${nombreCompleto}):`, {
            estado: estadoFinal,
            minutos_retraso: minutosRetraso
        });
        
        asistencias.push({
            matricula_id: estudiante.matricula_id,
            estado: estadoFinal,
            minutos_retraso: minutosRetraso
        });
    });
    
    // Validar que tenemos datos válidos
    if (!asignacionSeleccionada || asistencias.length === 0) {
        Swal.fire("Error", "No hay datos válidos para guardar", "error");
        return;
    }
    
    console.log('Datos completos a enviar:', {
        asignacion_id: asignacionSeleccionada,
        fecha: fecha,
        hora_inicio: horaInicio,
        hora_fin: horaFin,
        asistencias: asistencias,
        total_estudiantes: asistencias.length,
        grupo_id: $('#grado-grupo-select').val()
    });
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: {
            asignacion_id: asignacionSeleccionada,
            fecha: fecha,
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            asistencias: JSON.stringify(asistencias)
        },
        dataType: "json",
        success: function(respuesta) {
            console.log('Respuesta del servidor:', respuesta);
            
            if (respuesta.respuesta === "ok") {
                Swal.fire({
                    title: "¡Éxito!",
                    text: "La asistencia ha sido guardada correctamente",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Recargar la asistencia existente para reflejar los cambios
                setTimeout(function() {
                    verificarAsistenciaExistente();
                }, 500);
            } else {
                let mensaje = "No se pudo guardar la asistencia";
                if (respuesta.mensaje) {
                    mensaje += ": " + respuesta.mensaje;
                }
                Swal.fire("Error", mensaje, "error");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error guardando asistencia:", error);
            Swal.fire("Error", "Error de conexión al guardar", "error");
        }
    });
}

/*=============================================
ABRIR MODAL DE JUSTIFICACIÓN
=============================================*/
function abrirModalJustificacion(matriculaId) {
    // Buscar datos del estudiante
    const estudiante = estudiantesData.find(e => e.matricula_id == matriculaId);
    if (!estudiante) return;
    
    // Llenar datos del modal
    $('#modalJustificacionEstudiante').text(`${estudiante.nombres_usuario} ${estudiante.apellidos_usuario}`);
    $('#modalJustificacionMatricula').val(matriculaId);
    
    // Limpiar campos
    $('#justificacionTexto').val('');
    $('#comentarioDocente').val('');
    $('#estadoJustificacion').val('Pendiente');
    
    // Mostrar modal
    $('#modalGestionarJustificacion').modal('show');
}

/*=============================================
GUARDAR JUSTIFICACIÓN
=============================================*/
function guardarJustificacion() {
    const matriculaId = $('#modalJustificacionMatricula').val();
    const justificacion = $('#justificacionTexto').val();
    const comentario = $('#comentarioDocente').val();
    const estado = $('#estadoJustificacion').val();
    
    // Actualizar la tabla
    const $fila = $(`#estudiante-${matriculaId}`);
    let textoJustificacion = 'No Aplica';
    
    if (justificacion.trim() !== '') {
        textoJustificacion = estado === 'Aceptada' ? 'Justificada' : 
                           estado === 'Rechazada' ? 'No Justificada' : 'Pendiente';
    }
    
    $fila.find('.justificacion-estado').text(textoJustificacion);
    
    // Cerrar modal
    $('#modalGestionarJustificacion').modal('hide');
    
    Swal.fire({
        title: "¡Éxito!",
        text: "La justificación ha sido procesada correctamente",
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
}

/*=============================================
GUARDAR ESTUDIANTE AUTOMÁTICO
=============================================*/
function guardarEstudianteAutomatico(matriculaId, estado, minutosRetraso) {
    const fecha = $('#fecha-clase').val();
    const horaInicio = $('#hora-inicio').val();
    const horaFin = $('#hora-fin').val();
    
    if (!fecha || !horaInicio || !horaFin || !asignacionSeleccionada) {
        console.error('Faltan datos para guardar automáticamente');
        return;
    }
    
    const asistenciaAutomatica = [{
        matricula_id: matriculaId,
        estado: estado,
        minutos_retraso: minutosRetraso
    }];
    
    console.log('Guardando automáticamente estudiante:', asistenciaAutomatica);
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: {
            asignacion_id: asignacionSeleccionada,
            fecha: fecha,
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            asistencias: JSON.stringify(asistenciaAutomatica)
        },
        dataType: "json",
        success: function(respuesta) {
            if (respuesta.respuesta === "ok") {
                console.log('Estudiante guardado automáticamente por retraso excesivo');
                
                // Deshabilitar checkboxes del estudiante
                const $checkboxes = $(`.checkbox-container input[data-matricula="${matriculaId}"]`);
                $checkboxes.prop('disabled', true);
                const $fila = $(`#estudiante-${matriculaId}`);
                $fila.attr('data-asistencia-bloqueada', 'true');
                
                // Mostrar notificación
                Swal.fire({
                    title: "Ausencia Automática",
                    text: "Estudiante marcado como ausente por retraso excesivo",
                    icon: "warning",
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("Error guardando estudiante automáticamente:", error);
        }
    });
}