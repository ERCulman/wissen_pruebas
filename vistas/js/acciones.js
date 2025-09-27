/*=============================================
EVITAR CONFLICTO CON DATATABLES
=============================================*/
// No inicializar DataTables aquí porque ya se hace en plantilla.js

/*=============================================
SELECCIONAR TODAS LAS ACCIONES
=============================================*/
$(document).ready(function(){
    
    $("#selectAll").change(function(){
        $(".accion-checkbox:not(:disabled)").prop('checked', $(this).prop("checked"));
    });
    
    $("#btnSeleccionarTodas").click(function(){
        $(".accion-checkbox:not(:disabled)").prop('checked', true);
        updateSelectAllState();
    });
    
    $(".accion-checkbox").change(function(){
        updateSelectAllState();
    });
    
    function updateSelectAllState(){
        var totalCheckboxes = $(".accion-checkbox:not(:disabled)").length;
        var checkedCheckboxes = $(".accion-checkbox:not(:disabled):checked").length;
        $("#selectAll").prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    }
    
});

/*=============================================
INSERTAR ACCIONES SELECCIONADAS
=============================================*/
$("#btnInsertarSeleccionadas").click(function(){
    var accionesSeleccionadas = [];
    
    $(".accion-checkbox:checked").each(function(){
        var index = $(this).val();
        var fila = $(this).closest('tr');
        
        var accion = {
            nombre_accion: fila.find('.nombre-accion').val(),
            modulo: fila.find('.modulo-accion').val(),
            descripcion: fila.find('.descripcion-accion').val()
        };
        
        accionesSeleccionadas.push(accion);
    });
    
    if(accionesSeleccionadas.length === 0){
        Swal.fire("Advertencia", "Debe seleccionar al menos una acción que no exista ya en la base de datos", "warning");
        return;
    }
    
    Swal.fire({
        title: '¿Está seguro de insertar las acciones seleccionadas?',
        text: "Se insertarán " + accionesSeleccionadas.length + " acciones",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, insertar'
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append('insertarAccionesPrecargadas', true);
            formData.append('accionesSeleccionadas', JSON.stringify(accionesSeleccionadas));
            
            $.ajax({
                url: window.location.href,
                method: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(respuesta){
                    console.log('Respuesta:', respuesta);
                    window.location.reload();
                },
                error: function(xhr, status, error){
                    console.log('Error:', error);
                    Swal.fire("Error", "Error al insertar las acciones. Por favor intente nuevamente", "error");
                }
            });
        }
    });
});

/*=============================================
EDITAR ACCIÓN
=============================================*/
$(document).on("click", ".btnEditarAccion", function(){
    var idAccion = $(this).attr("data-id");
    
    var datos = new FormData();
    datos.append("idAccion", idAccion);
    
    $.ajax({
        url: "ajax/acciones.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta){
            $("#idAccion").val(respuesta["id"]);
            $("#editarNombreAccion").val(respuesta["nombre_accion"]);
            $("#editarModulo").val(respuesta["modulo"]);
            $("#editarDescripcion").val(respuesta["descripcion"]);
            
            $("#modalEditarAccion").modal('show');
        }
    });
});

/*=============================================
ELIMINAR ACCIÓN
=============================================*/
$(document).on("click", ".btnEliminarAccion", function(){
    var idAccion = $(this).attr("data-id");
    var nombreAccion = $(this).attr("data-nombre");
    
    Swal.fire({
        title: '¿Está seguro de borrar la acción "' + nombreAccion + '"?',
        text: "¡Si no lo está puede cancelar la acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, borrar acción!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = "index.php?ruta=gestionar-acciones&idAccion=" + idAccion;
        }
    });
});