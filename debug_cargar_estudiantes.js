// Función de prueba para cargar estudiantes con depuración extendida
function debugCargarEstudiantes() {
    console.log('=== DEBUG: Iniciando carga de estudiantes ===');
    
    // Verificar elementos del DOM
    const asignacionSelect = $('#asignatura-select');
    const grupoSelect = $('#grado-grupo-select');
    
    console.log('Elemento asignatura-select encontrado:', asignacionSelect.length > 0);
    console.log('Elemento grado-grupo-select encontrado:', grupoSelect.length > 0);
    
    const asignacionSeleccionada = asignacionSelect.val();
    const grupoId = grupoSelect.val();
    
    console.log('Asignación seleccionada:', asignacionSeleccionada);
    console.log('Grupo ID seleccionado:', grupoId);
    
    if (!asignacionSeleccionada) {
        console.error('No hay asignación seleccionada');
        Swal.fire("Advertencia", "Debe seleccionar una asignatura primero", "warning");
        return;
    }
    
    if (!grupoId) {
        console.error('No hay grupo seleccionado');
        Swal.fire("Advertencia", "Debe seleccionar un grado y grupo primero", "warning");
        return;
    }
    
    console.log('Enviando petición AJAX...');
    console.log('URL:', 'ajax/asistencia.ajax.php');
    console.log('Datos:', { grupo_id: grupoId });
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: { grupo_id: grupoId },
        dataType: "json",
        beforeSend: function() {
            console.log('Petición AJAX enviada');
        },
        success: function(estudiantes) {
            console.log('=== RESPUESTA AJAX RECIBIDA ===');
            console.log('Tipo de respuesta:', typeof estudiantes);
            console.log('Contenido completo:', estudiantes);
            
            // Verificar si hay error en la respuesta
            if (estudiantes && estudiantes.error) {
                console.error('Error del servidor:', estudiantes.error);
                Swal.fire("Error", "Error del servidor: " + estudiantes.error, "error");
                return;
            }
            
            // Verificar si es un array
            if (!Array.isArray(estudiantes)) {
                console.error('La respuesta no es un array:', estudiantes);
                Swal.fire("Error", "Formato de respuesta inválido", "error");
                return;
            }
            
            console.log('Número de estudiantes:', estudiantes.length);
            
            if (estudiantes.length === 0) {
                console.warn('No se encontraron estudiantes');
                Swal.fire("Información", "No se encontraron estudiantes matriculados en este grupo", "info");
                return;
            }
            
            // Mostrar algunos estudiantes para verificar
            console.log('Primeros estudiantes:');
            estudiantes.slice(0, 3).forEach((est, index) => {
                console.log(`${index + 1}:`, est);
            });
            
            // Simular renderizado (sin crear la tabla real)
            console.log('✅ Estudiantes cargados correctamente');
            Swal.fire("Éxito", `Se cargaron ${estudiantes.length} estudiantes correctamente`, "success");
            
        },
        error: function(xhr, status, error) {
            console.log('=== ERROR AJAX ===');
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("Response Text:", xhr.responseText);
            console.error("Status Code:", xhr.status);
            
            let mensajeError = "Error de conexión";
            if (xhr.responseText) {
                try {
                    const errorObj = JSON.parse(xhr.responseText);
                    mensajeError = errorObj.error || xhr.responseText;
                } catch (e) {
                    mensajeError = xhr.responseText;
                }
            }
            
            Swal.fire("Error", "No se pudieron cargar los estudiantes: " + mensajeError, "error");
        },
        complete: function() {
            console.log('Petición AJAX completada');
        }
    });
}

// Función para probar con un grupo específico
function debugCargarEstudiantesConGrupo(grupoId) {
    console.log('=== DEBUG: Probando con grupo específico ===');
    console.log('Grupo ID:', grupoId);
    
    $.ajax({
        url: "ajax/asistencia.ajax.php",
        method: "POST",
        data: { grupo_id: grupoId },
        dataType: "json",
        success: function(estudiantes) {
            console.log('Respuesta para grupo', grupoId, ':', estudiantes);
        },
        error: function(xhr, status, error) {
            console.error('Error para grupo', grupoId, ':', error);
            console.error('Response:', xhr.responseText);
        }
    });
}

// Agregar al objeto window para poder llamar desde la consola
window.debugCargarEstudiantes = debugCargarEstudiantes;
window.debugCargarEstudiantesConGrupo = debugCargarEstudiantesConGrupo;

console.log('Funciones de debug cargadas. Usa debugCargarEstudiantes() o debugCargarEstudiantesConGrupo(ID) en la consola.');