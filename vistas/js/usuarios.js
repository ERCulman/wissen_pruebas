import {formatearFecha, formatearFechaParaDB, calcularEdad, configurarCamposDeFecha} from './validaciones/Utilidades.js'

/* =======================================
	   INICIALIZAR TABLA DE USUARIOS
=======================================*/
$(document).ready(function() {
    $("#tablaUsuarios").DataTable({
        ...configuracionGlobalDataTables,
        "deferRender": true,
        "retrieve": true,
        "processing": true
    });
});

/* =======================================
	   EDITAR USUARIO
======================================= */

$(document).on('click', '.btnEditarUsuario', function() {

    console.log("Clic detectado");
  
    var idUsuario = $(this).data("id");
  
    var datos = new FormData();
    datos.append("idUsuario", idUsuario);

    $.ajax({
        url:"ajax/usuarios.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta){

            console.log("respuesta:", respuesta);

            $("#editarNumeroDocumento").val(respuesta["numero_documento"]);
            $("#editarTipoDocumento").html(respuesta["tipo_documento"]);
            $("#editarTipoDocumento").val(respuesta["tipo_documento"]);
            $("#editarNombreUsuario").val(respuesta["nombres_usuario"]);
            $("#editarApellidoUsuario").val(respuesta["apellidos_usuario"]);
            $("#editarSexoUsuario").html(respuesta["sexo_usuario"]);
            $("#editarSexoUsuario").val(respuesta["sexo_usuario"]);
            $("#editarRhUsuario").html(respuesta["rh_usuario"]);
            $("#editarRhUsuario").val(respuesta["rh_usuario"]);
            
            // Formatear fecha usando función utilitaria
            $("#editarFechaNacimiento").val(formatearFecha(respuesta["fecha_nacimiento"]));
            
            $("#editarEdadUsuario").val(respuesta["edad_usuario"]);
            $("#editarTelefonoUsuario").val(respuesta["telefono_usuario"]);
            $("#editarEmailUsuario").val(respuesta["email_usuario"]);
            $("#editarLoginUsuario").val(respuesta["usuario"]);
            $("#passwordActual").val(respuesta["password"]);
            $("#editarEstadoUsuario").html(respuesta["estado_usuario"]);
            $("#editarEstadoUsuario").val(respuesta["estado_usuario"]);

            $('#modalEditarUsuario').modal('show');
        }
    });
});

/*=================================================================
   INICIALIZACIÓN DE DATEPICKERS Y LÓGICA DE FECHAS EN MODALES
=================================================================*/
$(document).ready(function() {
    // 1. Opciones comunes
    const datepickerOptions = {
        format: 'dd/mm/yyyy',
        language: 'es',
        autoclose: true,
        todayHighlight: true,
        endDate: new Date(),
        startDate: '01/01/1900'
    };

    // 2. Selector común para ambos modales
    const modalesConDatepicker = '#modalAgregarUsuario, #modalEditarUsuario';

    // 3. Evento para INICIALIZAR cuando se abre cualquier modal
    $(document).on('shown.bs.modal', modalesConDatepicker, function() {
        const modalActual = $(this);
        const inputDatepicker = modalActual.find('.has-datepicker');

        const idFecha = inputDatepicker.attr('id');
        const idEdad = idFecha.includes('editar') ? 'editarEdadUsuario' : 'edadUsuario';

        // A. Activa el auto-formato y cálculo de edad al escribir manualmente.
        configurarCamposDeFecha({ idFecha, idEdad });

        // B. Inicializa el datepicker y CONECTA el evento 'changeDate'.
        inputDatepicker.datepicker({
            ...datepickerOptions,
            container: '#' + modalActual.attr('id')
        }).on('changeDate', function(e) {
            // C. Calcula la edad usando la función importada cuando se elige una fecha.
            const edad = calcularEdad(e.format());
            const campoEdad = document.getElementById(idEdad);
            if (campoEdad) {
                campoEdad.value = (edad !== null && edad >= 0) ? edad : '';
                campoEdad.dispatchEvent(new Event('input')); // Notifica al validador
            }
        });
    });

    // 4. Evento para DESTRUIR cuando se cierra cualquier modal
    $(document).on('hidden.bs.modal', modalesConDatepicker, function() {
        const inputDatepicker = $(this).find('.has-datepicker');
        if (inputDatepicker.data('datepicker')) {
            inputDatepicker.datepicker('remove');
        }
    });
});


/* =======================================
	   VER USUARIO
======================================= */

$(document).on('click', '.btnVerUsuario', function() {

    var idUsuario = $(this).data("id");
    console.log('ID obtenido para ver:', idUsuario);

    var datos = new FormData();
    datos.append("idUsuario", idUsuario);

    $.ajax({
        url: "ajax/usuarios.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta) {
            console.log("respuesta:", respuesta);

            $("#verNumeroDocumento").text(respuesta["numero_documento"]);
            $("#verTipoDocumento").text(respuesta["tipo_documento"]);
            $("#verNombreUsuario").text(respuesta["nombres_usuario"]);
            $("#verApellidoUsuario").text(respuesta["apellidos_usuario"]);
            $("#verSexoUsuario").text(respuesta["sexo_usuario"]);
            $("#verRhUsuario").text(respuesta["rh_usuario"]);
            // Formatear fecha usando función utilitaria
            $("#verFechaNacimiento").text(formatearFecha(respuesta["fecha_nacimiento"]));
            $("#verEdadUsuario").text(respuesta["edad_usuario"]);
            $("#verTelefonoUsuario").text(respuesta["telefono_usuario"]);
            $("#verEmailUsuario").text(respuesta["email_usuario"]);
            $("#verLoginUsuario").text(respuesta["usuario"]);
            $("#verEstadoUsuario").text(respuesta["estado_usuario"]);

            // Formatear fechas de auditoría usando función utilitaria
            $("#verFechaCreacion").text(formatearFecha(respuesta["fecha_creacion"]));
            $("#verFechaActualizacion").text(formatearFecha(respuesta["fecha_actualizacion"]));

            // Guardar ID para el botón editar del modal ver
            $('.btnEditarUsuario[data-dismiss="modal"]').attr('data-id', idUsuario);

            $('#modalVerUsuario').modal('show');
        }
    });
});

/* =======================================
	   CREAR USUARIO
======================================= */

$(document).on('submit', '#formAgregarUsuario', function(event) {
    event.preventDefault();

    var formData = new FormData($('#formAgregarUsuario')[0]);

    formData = formatearFechaParaDB(formData, 'fechaNacimiento');

    $.ajax({
        url: "ajax/usuarios.ajax.php",
        method: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(respuesta) {
            console.log('Respuesta del servidor:', respuesta);
            console.log('Respuesta trimmed:', respuesta.trim());
            if (respuesta.trim() === "ok") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Usuario registrado!',
                    text: 'El usuario ha sido creado correctamente. Ahora puede iniciar sesión.',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.value) {
                        $('#modalAgregarUsuario').modal('hide');
                        $('#formAgregarUsuario')[0].reset();
                        // Si estamos en la página de usuarios, recargamos para ver la tabla actualizada
                        if(window.location.href.indexOf("usuarios") > -1) {
                            window.location.reload();
                        }
                    }
                });
            } else if (respuesta.trim() === "error-sintaxis") {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error de validación!',
                    text: 'Por favor, revise que todos los campos estén llenos y no contengan caracteres especiales.',
                    confirmButtonText: 'Cerrar'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error al registrar!',
                    text: 'No se pudo crear el usuario. Es posible que el número de documento o el usuario ya existan en el sistema.',
                    confirmButtonText: 'Cerrar'
                });
            }
        }
    });
});

/* =======================================
	   RECUPERAR PASSWORD
======================================= */

$(document).on('submit', '#formNuevaRecuperacion', function(event) {
    event.preventDefault();
    console.log('Formulario enviado');

    var formData = new FormData(this);
    
    console.log('Usuario:', $('input[name="usuarioRecuperar"]').val());
    console.log('Email:', $('input[name="emailRecuperar"]').val());

    $.ajax({
        url: "ajax/usuarios.ajax.php",
        method: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(respuesta) {
            console.log('Respuesta:', respuesta);
            
            if (respuesta.trim() === "ok") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Revisa tu correo!',
                    text: 'Hemos enviado un enlace a tu correo electrónico para que puedas restablecer tu contraseña.',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.value) {
                        $('#modalRecuperarPassword').modal('hide');
                        $('#formNuevaRecuperacion')[0].reset();
                    }
                });
            } else if (respuesta.trim() === "not-found") {
                Swal.fire({
                    icon: 'error',
                    title: '¡Datos no encontrados!',
                    text: 'El usuario y correo electrónico no coinciden en nuestro sistema.',
                    confirmButtonText: 'Cerrar'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Respuesta: ' + respuesta,
                    confirmButtonText: 'Cerrar'
                });
            }
        },
        error: function(xhr, status, error) {
            console.log('Error AJAX:', error);
            alert('Error de conexión');
        }
    });
});



// Manejar envío del formulario de editar usuario
$(document).on('submit', '#formEditarUsuario', function(e) {
    console.log("Enviando formulario de editar usuario");
    
    // Convertir fecha de DD/MM/AAAA a AAAA-MM-DD antes del envío
    var fechaInput = $('#editarFechaNacimiento');
    var fechaValor = fechaInput.val();
    
    if (fechaValor && fechaValor.includes('/')) {
        var partes = fechaValor.split('/');
        if (partes.length === 3) {
            var fechaParaDB = partes[2] + '-' + partes[1] + '-' + partes[0];
            console.log("Convirtiendo fecha de", fechaValor, "a", fechaParaDB);
            fechaInput.val(fechaParaDB);
        }
    }
});