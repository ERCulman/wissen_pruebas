/*=============================================
CARGAR PERMISOS AL SELECCIONAR ROL
=============================================*/
$("#selectRol").change(function(){
    var rolId = $(this).val();
    
    if(rolId){
        $("#matrizPermisos").show();
        
        // Limpiar checkboxes
        $(".accion-checkbox").prop('checked', false);
        
        // Cargar permisos del rol
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: {rolId: rolId, accion: "obtenerPermisos"},
            dataType: "json",
            success: function(permisos){
                permisos.forEach(function(accionId){
                    $('input[value="' + accionId + '"]').prop('checked', true);
                });
            }
        });
    } else {
        $("#matrizPermisos").hide();
    }
});

/*=============================================
SELECCIONAR/DESELECCIONAR TODAS LAS ACCIONES
=============================================*/
$("#btnSeleccionarTodas").click(function(){
    $(".accion-checkbox").prop('checked', true);
});

$("#btnDeseleccionarTodas").click(function(){
    $(".accion-checkbox").prop('checked', false);
});

/*=============================================
SELECCIONAR/DESELECCIONAR POR MÓDULO
=============================================*/
$(document).on("click", ".btn-modulo-all", function(){
    var modulo = $(this).data("modulo");
    $('input[data-modulo="' + modulo + '"]').prop('checked', true);
});

$(document).on("click", ".btn-modulo-none", function(){
    var modulo = $(this).data("modulo");
    $('input[data-modulo="' + modulo + '"]').prop('checked', false);
});

/*=============================================
ELIMINAR ROL INSTITUCIONAL
=============================================*/
$(document).on("click", ".btnEliminarRol", function(){
    var idRol = $(this).attr("data-id");
    var usuario = $(this).attr("data-usuario");
    
    Swal.fire({
        title: '¿Está seguro de eliminar el rol de "' + usuario + '"?',
        text: "¡Si no lo está puede cancelar la acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar rol!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?ruta=asignar-roles&idRolInstitucional=" + idRol;
        }
    });
});

/*=============================================
FUNCIÓN PARA ASIGNAR ROL A USUARIO ESPECÍFICO
=============================================*/
function asignarRolUsuario(usuarioId){
    $("#usuarioId").val(usuarioId);
    $("#modalAsignarRol").modal('show');
}