<?php

class ControladorUsuarios {

	/* =======================================
	   METODO INGRESO USUARIO
	======================================= */

	static public function ctrIngresoUsuario(){
        
		if(isset($_POST["ingUsuario"])){

			if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) &&
			   preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,50}$/', $_POST["ingPassword"])){

				try {
					$encriptar = crypt($_POST["ingPassword"], '$1$rasmusle$');
					$tabla = "usuarios";
					$item = "usuario";
					$valor = $_POST["ingUsuario"];

					$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);

					// 1. Primera verificación: ¿Existe el usuario?
					if (!$respuesta) {
						echo '<br><div class="alert alert-danger">El usuario no está registrado en el sistema.</div>';
						return;
					}

					// 2. Segunda verificación: ¿La contraseña es correcta?
					if ($respuesta["password"] != $encriptar) {
						echo '<br><div class="alert alert-warning">Credenciales inválidas. Por favor, verifique su usuario y contraseña.</div>';
						return;
					}

					// 3. Tercera verificación: ¿El usuario está activo?
					if ($respuesta["estado_usuario"] == "Inactivo") {
						echo '<br><div class="alert alert-warning">Su cuenta ha sido desactivada. Por favor, contacte al administrador del sistema.</div>';
						return;
					}

					// 4. Éxito: Si todas las verificaciones pasan, se inicia la sesión.
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

				} catch (Exception $e) {
					// Captura cualquier excepción inesperada (ej. error de base de datos)
					echo '<br><div class="alert alert-danger">Error al ingresar, vuelve a intentar más tarde.</div>';
				}

			} else {
                 // Si los datos de entrada no pasan la validación inicial
                echo '<br><div class="alert alert-danger">Los datos ingresados contienen caracteres no permitidos.</div>';
            }
		}
	}

	/* =======================================
	   METODO CREAR USUARIO (CON SEGURIDAD Y VALIDACIÓN)
	======================================= */
	static public function ctrCrearUsuario()
	{
		// PROTECCIÓN DE ROL
		if (isset($_SESSION["id_usuario"])) {
			if (!BackendProtector::protectController('usuarios_crear')) {
				return "error-permisos"; // Retorna error si no tiene permisos
			}
		}

	    if (isset($_POST["loginUsuario"])) {
	        $datos = [
	            "numeroDocumento" => $_POST["numeroDocumento"] ?? '',
	            "tipoDocumento"   => $_POST["tipoDocumento"] ?? '',
	            "nombreUsuario"   => $_POST["nombreUsuario"] ?? '',
	            "apellidoUsuario" => $_POST["apellidoUsuario"] ?? '',
	            "sexoUsuario"     => $_POST["sexoUsuario"] ?? '',
	            "rhUsuario"       => $_POST["rhUsuario"] ?? '',
	            "fechaNacimiento" => $_POST["fechaNacimiento"] ?? '',
	            "edadUsuario"     => $_POST["edadUsuario"] ?? '',
	            "telefonoUsuario" => $_POST["telefonoUsuario"] ?? '',
	            "emailUsuario"    => $_POST["emailUsuario"] ?? '',
	            "loginUsuario"    => $_POST["loginUsuario"] ?? '',
	            "password"        => $_POST["password"] ?? '',
	        ];

	        $tabla = "usuarios";
	        $respuesta = ModeloUsuarios::mdlCrearUsuario($tabla, $datos);

	        // Detectar si es una llamada AJAX
	        $esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	        
	        if (is_array($respuesta) && isset($respuesta['status']) && $respuesta['status'] === 'error-validacion') {
	            if ($esAjax) {
	                return "error-sintaxis";
	            }
	            $mensajeError = "Se encontraron los siguientes errores: \\n\\n";
	            foreach ($respuesta['errors'] as $campo => $error) {
	                $campoLegible = preg_replace('/(?<!^)[A-Z]/', ' $0', $campo);
	                $mensajeError .= "- " . ucfirst($campoLegible) . ": " . $error . "\\n";
	            }

	            echo '<script>
	                Swal.fire({
	                    icon: "error",
	                    title: "¡Datos incorrectos!",
	                    text: "' . $mensajeError . '",
	                    confirmButtonText: "Corregir"
	                }).then((result) => {
	                    if (result.value) {
	                        window.history.back();
	                    }
	                });
	            </script>';

	        } elseif ($respuesta == "ok") {
	            // Enviar email de bienvenida
	            require_once __DIR__ . "/../servicios/email.servicio.php";
	            $nombreCompleto = $datos["nombreUsuario"] . " " . $datos["apellidoUsuario"];
	            $envioEmail = EmailServicio::enviarEmailBienvenida($datos["emailUsuario"], $nombreCompleto, $datos["loginUsuario"]);
	            
	            if ($esAjax) {
	                return "ok";
	            }
	            echo '<script>
	                Swal.fire({
	                    position: "top-end",
	                    icon: "success",
	                    title: "¡Usuario registrado con éxito!",
	                    showConfirmButton: false,
	                    timer: 1500
	                }).then(() => {
	                    window.location = "usuarios";
	                });
	            </script>';
	        } else {
	            if ($esAjax) {
	                return $respuesta; // Retorna el error específico (error-duplicado, etc.)
	            }
	            $errorMsg = ($respuesta == 'error-duplicado') ? 'El número de documento o el usuario ya están registrados.' : 'No se pudo completar el registro.';
	            echo '<script>
	                Swal.fire({
	                    icon: "error",
	                    title: "¡Error en el Registro!",
	                    text: "' . $errorMsg . '",
	                    confirmButtonText: "Cerrar"
	                }).then((result) => {
	                    if (result.value) {
	                        window.history.back();
	                    }
	                });
	            </script>';
	        }
	    }
	    return "error"; // Fallback
	}


	/* =======================================
	   MOSTRAR USUARIO
	======================================= */

	static public function ctrMostrarUsuario($item, $valor){
		// PROTECCIÓN DE ROL
		if (isset($_SESSION["id_usuario"])) {
			if (!BackendProtector::protectController('usuarios_ver')) {
				return false;
			}
		}
		$tabla = "usuarios";
		$respuesta = ModeloUsuarios::MdlMostrarUsuarios($tabla, $item, $valor);
		return $respuesta;
	}

	/* =======================================
	   METODO EDITAR USUARIO (CON SEGURIDAD Y VALIDACIÓN)
	======================================= */

	public function ctrEditarUsuario(){
		// PROTECCIÓN DE ROL
		if (!BackendProtector::protectController('usuarios_editar')) {
			return; // Detiene la ejecución si no tiene permisos
		}

	    if (isset($_POST["editarLoginUsuario"])) {
	        $datos = [
	            "editarNumeroDocumento" => $_POST["editarNumeroDocumento"] ?? '',
	            "editarTipoDocumento"   => $_POST["editarTipoDocumento"] ?? '',
	            "editarNombreUsuario"   => $_POST["editarNombreUsuario"] ?? '',
	            "editarApellidoUsuario" => $_POST["editarApellidoUsuario"] ?? '',
	            "editarSexoUsuario"     => $_POST["editarSexoUsuario"] ?? '',
	            "editarRhUsuario"       => $_POST["editarRhUsuario"] ?? '',
	            "editarFechaNacimiento" => $_POST["editarFechaNacimiento"] ?? '',
	            "editarEdadUsuario"     => $_POST["editarEdadUsuario"] ?? '',
	            "editarTelefonoUsuario" => $_POST["editarTelefonoUsuario"] ?? '',
	            "editarEmailUsuario"    => $_POST["editarEmailUsuario"] ?? '',
	            "editarPassword"        => $_POST["editarPassword"] ?? '',
	            "editarEstadoUsuario"   => $_POST["editarEstadoUsuario"] ?? '',
	        ];

	        $tabla = "usuarios";
	        $respuesta = ModeloUsuarios::mdlEditarUsuario($tabla, $datos);

	        if (is_array($respuesta) && isset($respuesta['status']) && $respuesta['status'] === 'error-validacion') {
	            $mensajeError = "Se encontraron los siguientes errores: \\n\\n";
	            foreach ($respuesta['errors'] as $campo => $error) {
	                $campoLegible = preg_replace('/(?<!^)[A-Z]/', ' $0', $campo);
	                $mensajeError .= "- " . ucfirst(str_replace("editar ", "", $campoLegible)) . ": " . $error . "\\n";
	            }

	            echo '<script>
	                Swal.fire({
	                    icon: "error",
	                    title: "¡Datos incorrectos!",
	                    text: "' . $mensajeError . '",
	                    confirmButtonText: "Corregir"
	                }).then((result) => {
	                    if (result.value) {
	                        window.history.back();
	                    }
	                });
	            </script>';

	        } elseif ($respuesta == "ok") {
	            echo '<script>
	                Swal.fire({
	                    position: "top-end",
	                    icon: "success",
	                    title: "El usuario se ha actualizado correctamente",
	                    showConfirmButton: false,
	                    timer: 1500
	                }).then(() => {
	                    window.location = "usuarios";
	                });
	            </script>';
	        } else {
	            $errorMsg = ($respuesta == 'error-duplicado') ? 'El email ya está en uso por otro usuario.' : 'No se pudo completar la actualización.';
	            echo '<script>
	                Swal.fire({
	                    icon: "error",
	                    title: "¡Error al Actualizar!",
	                    text: "' . $errorMsg . '",
	                    confirmButtonText: "Cerrar"
	                }).then((result) => {
	                    if (result.value) {
	                        window.history.back();
	                    }
	                });
	            </script>';
	        }
	    }
	}

    /* =======================================
	   METODO RECUPERAR PASSWORD
	======================================= */

    static public function ctrRecuperarPassword(){

        if(isset($_POST["usuarioRecuperar"]) && isset($_POST["emailRecuperar"])){
            if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["usuarioRecuperar"]) &&
               filter_var($_POST["emailRecuperar"], FILTER_VALIDATE_EMAIL)){
                $tabla = "usuarios";
                $usuario = $_POST["usuarioRecuperar"];
                $email = $_POST["emailRecuperar"];
                $respuesta = ModeloUsuarios::mdlBuscarUsuarioPorUsuarioYEmail($tabla, $usuario, $email);
                if($respuesta){
                    $token = bin2hex(random_bytes(32));
                    $expiry = new DateTime();
                    $expiry->add(new DateInterval('PT1H'));
                    $expiryDate = $expiry->format('Y-m-d H:i:s');
                    $datos = array(
                        "id_usuario" => $respuesta["id_usuario"],
                        "reset_token" => $token,
                        "reset_token_expiry" => $expiryDate
                    );
                    $guardarToken = ModeloUsuarios::mdlGuardarTokenReseteo($tabla, $datos);
                    if($guardarToken == "ok"){
                        require_once __DIR__ . "/../servicios/email.servicio.php";
                        $nombre = $respuesta["nombres_usuario"] . " " . $respuesta["apellidos_usuario"];
                        $envioEmail = EmailServicio::enviarEmailRecuperacion($email, $nombre, $token);
                        if($envioEmail == "ok"){
                            echo "ok";
                        } else {
                            echo "error-email";
                        }
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
