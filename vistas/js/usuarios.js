/* =======================================
	   EDITAR USUARIO
======================================= */

$(".btnEditarUsuario").click(function() {

	console.log("Clic detectado");
  
  var idUsuario = $(this).attr("idUsuario");
  
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
      $("#editarFechaNacimiento").val(respuesta["fecha_nacimiento"]);
      $("#editarEdadUsuario").val(respuesta["edad_usuario"]);
      $("#editarTelefonoUsuario").val(respuesta["telefono_usuario"]);
      $("#editarEmailUsuario").val(respuesta["email_usuario"]);
      $("#editarLoginUsuario").val(respuesta["usuario"]);
      $("#passwordActual").val(respuesta["password"]);
      $("#editarEstadoUsuario").html(respuesta["estado_usuario"]);
      $("#editarEstadoUsuario").val(respuesta["estado_usuario"]);
      $("#editarRolUsuario").html(respuesta["id_rol"]);
      $("#editarRolUsuario").val(respuesta["id_rol"]);



    }

   });

});

/* =======================================
	   VER USUARIO
======================================= */

$(".btnVerUsuario").click(function() {

	console.log("Clic detectado");
  
  var idUsuario = $(this).attr("idUsuario");
  
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

      $("#verNumeroDocumento").val(respuesta["numero_documento"]);
      $("#verTipoDocumento").html(respuesta["tipo_documento"]);
      $("#verNombreUsuario").val(respuesta["nombres_usuario"]);
      $("#verApellidoUsuario").val(respuesta["apellidos_usuario"]);
      $("#verSexoUsuario").html(respuesta["sexo_usuario"]);
      $("#verRhUsuario").html(respuesta["rh_usuario"]);
      $("#verFechaNacimiento").val(respuesta["fecha_nacimiento"]);
      $("#verEdadUsuario").val(respuesta["edad_usuario"]);
      $("#verTelefonoUsuario").val(respuesta["telefono_usuario"]);
      $("#verEmailUsuario").val(respuesta["email_usuario"]);
      $("#verLoginUsuario").val(respuesta["usuario"]);
      $("#verPassword").val(respuesta["password"]);
      $("#verEstadoUsuario").html(respuesta["estado_usuario"]);
      $("#verRolUsuario").html(respuesta["id_rol"]);
      $("#verFechaCreacion").val(respuesta["fecha_creacion"]);
      $("#verFechaActualizacion").val(respuesta["fecha_actualizacion"]);


    }

   });

});