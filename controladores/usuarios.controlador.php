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

				if ($respuesta){
					// Verificar si el usuario está activo
					if ($respuesta["estado_usuario"] == "Inactivo"){
						echo '<br><div class="alert alert-warning">Su cuenta ha sido desactivada. Por favor, contacte al administrador del sistema para reactivarla.</div>';
						return;
					}

					// Verificar contraseña
					if ($respuesta["usuario"] == $_POST["ingUsuario"] && $respuesta["password"] == $encriptar){

						$_SESSION["iniciarSesion"] = "ok";
						$_SESSION["id_usuario"] = $respuesta["id_usuario"];
						$_SESSION["nombres_usuario"] = $respuesta["nombres_usuario"];
						$_SESSION["apellidos_usuario"] = $respuesta["apellidos_usuario"];
						
						// Establecer rol activo automáticamente
						$rolesUsuario = ModeloAuth::mdlObtenerRolesUsuario($respuesta["id_usuario"]);
						if(!empty($rolesUsuario)){
							$primerRol = $rolesUsuario[0];
							$_SESSION['rol_activo'] = $primerRol['tipo'] . '_' . ($primerRol['tipo'] == 'institucional' ? 'sede_' . ($primerRol['sede_id'] ?? 'unknown') : 'sistema');
						}

						echo '<script> window.location = "inicio"; </script>';

					} else {

						echo '<br><div class="alert alert-danger">Error al ingresar, vuelve a intentar</div>';
					}
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

		// PROTECCIÓN: Verificar permisos antes de crear usuario
		if (!BackendProtector::protectController('usuarios_crear')) {
			return;
		}

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
					"estado_usuario" => "Activo"
				);

				$respuesta = ModeloUsuarios::mdlCrearUsuario($tabla, $datos);

				if($respuesta == "ok"){
					require_once __DIR__ . "/../servicios/email.servicio.php";
					$nombre = $_POST["nombreUsuario"] . " " . $_POST["apellidoUsuario"];
					$email = $_POST["emailUsuario"];
					$usuario = $_POST["loginUsuario"];
					
					$envioEmail = EmailServicio::enviarEmailBienvenida($email, $nombre, $usuario);
				}

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

		// PROTECCIÓN: Verificar permisos antes de mostrar usuario
		if (!BackendProtector::protectController('usuarios_ver')) {
			return false;
		}

		$tabla = "usuarios";
		$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);
		return $respuesta;

	}

	/* =======================================
	   METODO EDITAR USUARIO
	======================================= */

	public function ctrEditarUsuario(){

		// PROTECCIÓN: Verificar permisos antes de editar usuario
		if (!BackendProtector::protectController('usuarios_editar')) {
			return;
		}

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
					"estado_usuario" => $_POST["editarEstadoUsuario"]
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
	   METODO RECUPERAR PASSWORD
	======================================= */

    static public function ctrRecuperarPassword(){

        error_log("[RECUPERAR] Iniciando ctrRecuperarPassword - " . date('Y-m-d H:i:s'));
        error_log("[RECUPERAR] POST data: " . print_r($_POST, true));

        if(isset($_POST["usuarioRecuperar"]) && isset($_POST["emailRecuperar"])){

            if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["usuarioRecuperar"]) &&
               filter_var($_POST["emailRecuperar"], FILTER_VALIDATE_EMAIL)){

                $tabla = "usuarios";
                $usuario = $_POST["usuarioRecuperar"];
                $email = $_POST["emailRecuperar"];

                error_log("[RECUPERAR] Buscando usuario: $usuario, email: $email");
                $respuesta = ModeloUsuarios::mdlBuscarUsuarioPorUsuarioYEmail($tabla, $usuario, $email);

                if($respuesta){

                    error_log("[RECUPERAR] Usuario encontrado, generando token");
                    $token = bin2hex(random_bytes(32));
                    $expiry = new DateTime();
                    $expiry->add(new DateInterval('PT1H'));
                    $expiryDate = $expiry->format('Y-m-d H:i:s');

                    $datos = array(
                        "id_usuario" => $respuesta["id_usuario"],
                        "reset_token" => $token,
                        "reset_token_expiry" => $expiryDate
                    );

                    error_log("[RECUPERAR] Guardando token: $token");
                    $guardarToken = ModeloUsuarios::mdlGuardarTokenReseteo($tabla, $datos);

                    if($guardarToken == "ok"){
                        
                        error_log("[RECUPERAR] Token guardado, enviando email");
                        require_once __DIR__ . "/../servicios/email.servicio.php";
                        $nombre = $respuesta["nombres_usuario"] . " " . $respuesta["apellidos_usuario"];
                        $envioEmail = EmailServicio::enviarEmailRecuperacion($email, $nombre, $token);

                        error_log("[RECUPERAR] Resultado envío email: $envioEmail");
                        if($envioEmail == "ok"){
                            error_log("[RECUPERAR] Proceso completado exitosamente");
                            echo "ok";
                        } else {
                            error_log("[RECUPERAR] Error en envío de email: $envioEmail");
                            echo "error-email";
                        }
                    } else {
                        error_log("[RECUPERAR] Error guardando token: $guardarToken");
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

                        // Corrección: salt bien formado y sin salto de línea
                        $encriptar = crypt($_POST["new_password"], '$1$rasmusle$');

                        $datos = array(
                            "id_usuario" => $usuario["id_usuario"],
                            "password" => $encriptar
                        );

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