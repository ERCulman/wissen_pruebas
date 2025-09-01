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
				$tabla = "usuarios";
				$item = "usuario";
				$valor = $_POST["ingUsuario"];

				$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

				if ($respuesta["usuario"] == $_POST["ingUsuario"] && $respuesta["password"] == $encriptar){

					$_SESSION["iniciarSesion"] = "ok";
					$_SESSION["nombres_usuario"] = $respuesta["nombres_usuario"];
					$_SESSION["apellidos_usuario"] = $respuesta["apellidos_usuario"];
                    $_SESSION["id_rol"] = $respuesta["id_rol"];

					echo '<script> window.location = "inicio"; </script>';

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

				echo $respuesta;

			} else {

				echo "error-sintaxis";
			}
		}
	}

	/* =======================================
	   MOSTRAR USUARIO
	======================================= */

	static public function ctrMostrarUsuario($item, $valor){

		$tabla = "usuarios";
		$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);
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

			   			echo '<script> Swal.fire({ icon: "error", title: "¡La contraseña no puede ir vacio o llevar caracteres especiales en los campos diligenciados!"}).then ((result)=>{{if(result.value){window.location="usuarios";}}});</script>';

			   		}
			   		
			   	} else {

			   		$encriptar = $_POST["passwordActual"];
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

					echo '<script>Swal.fire({position: "top-end",icon: "success",title: "El usuario se ha actualizado correctamente",showConfirmButton: false,timer: 1500}).then ((result)=>{{if(result.value){window.location="usuarios";}}});</script>';
				}

			} else {

				echo '<script>Swal.fire({icon: "error",title: "¡El usuario no puede ir vacio o llevar caracteres especiales en los campos diligenciados!"}).then ((result)=>{{if(result.value){window.location="usuarios";}}});</script>';
			}  	
		}
	}
    
    /* =======================================
	   METODO OLVIDO PASSWORD
	======================================= */

    static public function ctrOlvidoPassword(){

        if(isset($_POST["emailRecuperar"])){

            if(filter_var($_POST["emailRecuperar"], FILTER_VALIDATE_EMAIL)){

                $tabla = "usuarios";
                $item = "email_usuario";
                $valor = $_POST["emailRecuperar"];

                $usuario = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

                if($usuario){

                    $token = bin2hex(random_bytes(16));
                    $expiry = new DateTime();
                    $expiry->add(new DateInterval('PT1H'));
                    $expiryDate = $expiry->format('Y-m-d H:i:s');

                    $datos = array("id_usuario" => $usuario["id_usuario"],
                                   "reset_token" => $token,
                                   "reset_token_expiry" => $expiryDate);

                    $respuesta = ModeloUsuarios::mdlActualizarToken($tabla, $datos);

                    if($respuesta == "ok"){
                        // Aquí se enviaría el email en un caso real
                        echo "ok";
                    } else {
                        echo "error-db";
                    }

                } else {
                    echo "not-found";
                }

            } else {
                echo "error-sintaxis";
            }
        }
    }

    /* =======================================
	   METODO RESET PASSWORD
	======================================= */

    static public function ctrResetPassword(){

        if(isset($_POST["reset_token"])){

            if(isset($_POST["new_password"]) && isset($_POST["confirm_password"]) &&
               !empty($_POST["new_password"]) && $_POST["new_password"] === $_POST["confirm_password"]){

                $tabla = "usuarios";
                $item = "reset_token";
                $valor = $_POST["reset_token"];

                $usuario = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

                if($usuario){

                    $now = new DateTime();
                    $expiry = new DateTime($usuario["reset_token_expiry"]);

                    if($now < $expiry){

                        $encriptar = crypt($_POST["new_password"], '$1$rasmusle$');

                        $datos = array("id_usuario" => $usuario["id_usuario"],
                                       "password" => $encriptar);

                        $respuesta = ModeloUsuarios::mdlActualizarPassword($tabla, $datos);

                        if($respuesta == "ok"){
                            echo '<div class="alert alert-success">Contraseña actualizada correctamente. Ya puedes <a href="index.php">iniciar sesión</a>.</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error al actualizar la contraseña.</div>';
                        }

                    } else {
                        echo '<div class="alert alert-danger">El token ha expirado. Por favor, solicita un nuevo restablecimiento.</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">Token inválido.</div>';
                }

            } else {
                echo '<div class="alert alert-danger">Las contraseñas no coinciden o están vacías.</div>';
            }
        }
    }

}
