/*=============================================
INICIALIZAR DATATABLES CON BÚSQUEDA
=============================================*/
$(document).ready(function(){
    
    // DataTable para usuarios sin rol
    var tablaSinRol = $("#tablaUsuariosSinRol").DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });
    
    // DataTable para usuarios con rol
    var tablaConRol = $("#tablaUsuariosConRol").DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });
    
    // Búsqueda personalizada para usuarios sin rol
    $("#buscarUsuario").on('keyup', function(){
        tablaSinRol.search(this.value).draw();
    });
    
    // Búsqueda personalizada para usuarios con rol
    $("#buscarUsuarioConRol").on('keyup', function(){
        tablaConRol.search(this.value).draw();
    });
    
});

/*=============================================
SELECCIONAR TODOS LOS USUARIOS
=============================================*/
$("#selectAllUsuarios").change(function(){
    $(".usuario-checkbox").prop('checked', $(this).prop("checked"));
    actualizarBotonAsignarMasivo();
});

$(".usuario-checkbox").change(function(){
    actualizarBotonAsignarMasivo();
});

$(document).on('change', '.usuario-checkbox', function(){
    actualizarBotonAsignarMasivo();
});

function actualizarBotonAsignarMasivo(){
    var seleccionados = $(".usuario-checkbox:checked").length;
    var rolSeleccionado = $("#rolMasivo").val();
    var sedeSeleccionada = $("#sedeMasiva").val();
    
    if(seleccionados > 0 && rolSeleccionado && sedeSeleccionada){
        $("#btnAsignarMasivo").prop('disabled', false);
    } else {
        $("#btnAsignarMasivo").prop('disabled', true);
    }
}

// Actualizar botón cuando cambien los selects
$("#rolMasivo, #sedeMasiva").change(function(){
    actualizarBotonAsignarMasivo();
});

/*=============================================
ASIGNAR ROL INDIVIDUAL
=============================================*/
$(document).on("click", ".btnAsignarIndividual", function(){
    var usuarioId = $(this).attr("data-id");
    var nombreUsuario = $(this).attr("data-nombre");
    
    $("#usuarioIdModal").val(usuarioId);
    $("#nombreUsuario").text(nombreUsuario);
    $("#modalAsignarRol").modal('show');
});

/*=============================================
ASIGNAR ROLES MASIVAMENTE
=============================================*/
$("#btnAsignarMasivo").click(function(){
    var usuariosSeleccionados = [];
    var rolId = $("#rolMasivo").val();
    var sedeId = $("#sedeMasiva").val();
    
    $(".usuario-checkbox:checked").each(function(){
        usuariosSeleccionados.push($(this).val());
    });
    
    if(usuariosSeleccionados.length === 0){
        Swal.fire("Advertencia", "Debe seleccionar al menos un usuario", "warning");
        return;
    }
    
    if(!rolId || !sedeId){
        Swal.fire("Advertencia", "Debe seleccionar rol y sede", "warning");
        return;
    }
    
    Swal.fire({
        title: '¿Asignar rol a ' + usuariosSeleccionados.length + ' usuarios?',
        text: "Se asignará el rol seleccionado a todos los usuarios marcados",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, asignar'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append('usuariosSeleccionados', JSON.stringify(usuariosSeleccionados));
            formData.append('rolMasivoId', rolId);
            formData.append('sedeMasivaId', sedeId);
            
            $.ajax({
                url: window.location.href,
                method: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(respuesta){
                    window.location.reload();
                },
                error: function(){
                    Swal.fire("Error", "Error al asignar los roles", "error");
                }
            });
        }
    });
});

/*=============================================
EDITAR ROL
=============================================*/
$(document).on("click", ".btnEditarRol", function(){
    var idRol = $(this).attr("data-id");
    
    $.ajax({
        url: "ajax/roles.ajax.php",
        method: "POST",
        data: {idRolInstitucional: idRol, accion: "obtenerRolInstitucional"},
        dataType: "json",
        success: function(respuesta){
            $("#editarRolId").val(respuesta["id"]);
            $("#editarRolSelect").val(respuesta["rol_id"]);
            $("#editarSedeSelect").val(respuesta["sede_id"]);
            $("#editarFechaInicio").val(respuesta["fecha_inicio"]);
            $("#editarFechaFin").val(respuesta["fecha_fin"]);
            $("#editarEstado").val(respuesta["estado"]);
            
            $("#modalEditarRol").modal('show');
        }
    });
});

/*=============================================
ELIMINAR ROL
=============================================*/
$(document).on("click", ".btnEliminarRol", function(){
    var idRol = $(this).attr("data-id");
    var usuario = $(this).attr("data-usuario");
    
    Swal.fire({
        title: '¿Eliminar rol de "' + usuario + '"?',
        text: "Si tiene relaciones activas solo se inactivará",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, proceder'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?ruta=asignar-roles&idRolInstitucional=" + idRol + "&accion=eliminar";
        }
    });
});