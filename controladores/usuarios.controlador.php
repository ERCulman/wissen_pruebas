<?php

class ControladorUsuarios {

	/* =======================================
	   METODO INGRESO USUARIO
	======================================= */

	static public function ctrIngresoUsuario(){

		if(isset($_POST["ingUsuario"])){

			if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) && 
			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])){

				$encriptar = crypt($_POST["ingPassword"], '$1$rasmusle$');
				$tabla = "usuarios"; // Variable que me consulte la tabla Usuarios
				$item = "usuario"; // Variable que me consulte el campo Usuario
				$valor = $_POST["ingUsuario"]; // Variable que me consulte el usuario ingresado en el login, si este existe

				$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor); // Solicitar una respuesta del modelo a través del método mostrar usuarios

				// Muestra la respuesta cuando se ejecute
				if ($respuesta["usuario"] == $_POST["ingUsuario"] && $respuesta["password"] == $encriptar){

					$_SESSION["iniciarSesion"] = "ok";
					$_SESSION["nombres_usuario"] = $respuesta["nombres_usuario"];
					$_SESSION["apellidos_usuario"] = $respuesta["apellidos_usuario"];
                    $_SESSION["id_rol"] = $respuesta["id_rol"];

					echo '<script>
						window.location = "inicio";
					</script>';

				} else {

					echo '<br><div class="alert alert-danger">Error al ingresar, vuelve a intentar</div>';
				}
			}
		}
	}

	/* =======================================
	   METODO NUEVO USUARIO - CREAR USUARIO
	======================================= */

	static public function ctrCrearUsuario(){

		if(isset($_POST["loginUsuario"])){

			if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["numeroDocumento"]) &&
			   preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nombreUsuario"]) &&
			   preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["apellidoUsuario"]) &&
			   preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["fechaNacimiento"]) &&
			   preg_match('/^\d{1,2}$/', $_POST["edadUsuario"]) &&
			   preg_match('/^[0-9]+$/', $_POST["telefonoUsuario"]) &&
			   filter_var($_POST["emailUsuario"], FILTER_VALIDATE_EMAIL) &&
			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["loginUsuario"]) &&
			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["password"])){

				$tabla = "usuarios";

				$encriptar = crypt($_POST["password"], '$1$rasmusle$');

				$datos = array(
					"numero_documento" => $_POST["numeroDocumento"],
					"tipo_documento" => $_POST["tipoDocumento"],
					"nombres_usuario" => $_POST["nombreUsuario"],
					"apellidos_usuario" => $_POST["apellidoUsuario"],
					"sexo_usuario" => $_POST["sexoUsuario"],
					"rh_usuario" => $_POST["rhUsuario"],
					"fecha_nacimiento" => $_POST["fechaNacimiento"],
					"edad_usuario" => $_POST["edadUsuario"],
					"telefono_usuario" => $_POST["telefonoUsuario"],
					"email_usuario" => $_POST["emailUsuario"],
					"usuario" => $_POST["loginUsuario"],
					"password" => $encriptar,
					"estado_usuario" => $_POST["estadoUsuario"],
					"id_rol" => $_POST["rolUsuario"]
				);

				$respuesta = ModeloUsuarios::mdlCrearUsuario($tabla, $datos);

				if ($respuesta == "ok") {

					echo '<script>
						Swal.fire({
							position: "top-end",
							icon: "success",
							title: "El usuario se ha creado correctamente",
							showConfirmButton: false,
							timer: 1500
						}).then ((result)=>{
							if(result.value){
								window.location="usuarios";
							}
						});
					</script>';
				}else {
                    echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al registrar!",
                        text: "No se pudo crear el usuario. Es posible que el número de documento o el email ya existan en el sistema."
                    }).then((result) => {
                        if(result.value){
                            window.location = "usuarios";
                        }
                    });
                </script>';
                }

			} else {

				echo '<script>
					Swal.fire({
						icon: "error",
						title: "¡El usuario no puede ir vacio o llevar caracteres especiales en los campos diligenciados!"
					}).then ((result)=>{
						if(result.value){
							window.location="usuarios";
						}
					});
				</script>';
			}
		}	
	}

	/* =======================================
	   MOSTRAR USUARIO
	======================================= */

	static public function ctrMostrarUsuario($item, $valor){

		$tabla = "usuarios";
		$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor); // Solicitar una respuesta del modelo a través del método mostrar usuarios

		return $respuesta;

	}


	/* =======================================
	   METODO EDITAR USUARIO
	======================================= */

	public function ctrEditarUsuario(){

		if(isset($_POST["editarLoginUsuario"])){

			if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarNumeroDocumento"]) &&
			   preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarNombreUsuario"]) &&
			   preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarApellidoUsuario"]) &&
			   preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["editarFechaNacimiento"]) &&
			   preg_match('/^\d{1,2}$/', $_POST["editarEdadUsuario"]) &&
			   preg_match('/^[0-9]+$/', $_POST["editarTelefonoUsuario"]) &&
			   filter_var($_POST["editarEmailUsuario"], FILTER_VALIDATE_EMAIL)){

			   	$tabla = "usuarios";

			   	if($_POST["editarPassword"] != ""){

			   		if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarPassword"])){

			   			$encriptar = crypt($_POST["editarPassword"], '$1$rasmusle$');

			   		} else{

			   			echo '<script>
									Swal.fire({
										icon: "error",
										title: "¡La contraseña no puede ir vacio o llevar caracteres especiales en los campos diligenciados!"
									}).then ((result)=>{
										if(result.value){
											window.location="usuarios";
										}
									});
						</script>';

			   		}
			   		
			   	} else {

			   		$encriptar = crypt($_POST["passwordActual"], '$1$rasmusle$');
			   	}

			   	$datos = array(
					"numero_documento" => $_POST["editarNumeroDocumento"],
					"tipo_documento" => $_POST["editarTipoDocumento"],
					"nombres_usuario" => $_POST["editarNombreUsuario"],
					"apellidos_usuario" => $_POST["editarApellidoUsuario"],
					"sexo_usuario" => $_POST["editarSexoUsuario"],
					"rh_usuario" => $_POST["editarRhUsuario"],
					"fecha_nacimiento" => $_POST["editarFechaNacimiento"],
					"edad_usuario" => $_POST["editarEdadUsuario"],
					"telefono_usuario" => $_POST["editarTelefonoUsuario"],
					"email_usuario" => $_POST["editarEmailUsuario"],
					"usuario" => $_POST["editarLoginUsuario"],
					"password" => $encriptar,
					"estado_usuario" => $_POST["editarEstadoUsuario"],
					"id_rol" => $_POST["editarRolUsuario"]
				);

				$respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

				if ($respuesta == "ok") {

					echo '<script>
						Swal.fire({
							position: "top-end",
							icon: "success",
							title: "El usuario se ha actualizado correctamente",
							showConfirmButton: false,
							timer: 1500
						}).then ((result)=>{
							if(result.value){
								window.location="usuarios";
							}
						});
					</script>';
				}

			} else {

				echo '<script>
					Swal.fire({
						icon: "error",
						title: "¡El usuario no puede ir vacio o llevar caracteres especiales en los campos diligenciados!"
					}).then ((result)=>{
						if(result.value){
							window.location="usuarios";
						}
					});
				</script>';
			}  	
		}
	}

}
