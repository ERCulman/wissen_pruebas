<?php

// Se utiliza __DIR__ para asegurar que la ruta sea siempre correcta.
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/validaciones/MotorValidaciones.php";
require_once __DIR__ . "/validaciones/ReglasUsuario.php";

class ModeloUsuarios
{

    /*=======================================
	METODO MOSTRAR USUARIOS
	=======================================*/
    static public function mdlMostrarUsuarios($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt = null;
    }

    /*=======================================
	METODO REGISTRO DE USUARIO CON VALIDACIÓN
	=======================================*/
    static public function mdlCrearUsuario($tabla, $datos)
    {
        // --- INICIO DE LA VALIDACIÓN ---
        $validador = new MotorValidaciones();
        $reglas = ReglasUsuario::reglasCreacion();
        $errores = $validador->validar($datos, $reglas);

        if (!empty($errores)) {
            // Si hay errores, se devuelve el array para ser manejado por el controlador.
            return ['status' => 'error-validacion', 'errors' => $errores];
        }
        // --- FIN DE LA VALIDACIÓN ---

        try {
            // Verificar si el número de documento ya existe
            $stmtCheckDoc = Conexion::conectar()->prepare("SELECT numero_documento FROM $tabla WHERE numero_documento = :numero_documento");
            $stmtCheckDoc->bindParam(":numero_documento", $datos["numeroDocumento"], PDO::PARAM_STR);
            $stmtCheckDoc->execute();
            
            if ($stmtCheckDoc->fetch()) {
                return "error-duplicado"; // Documento duplicado
            }
            
            // Verificar si el usuario ya existe
            $stmtCheckUser = Conexion::conectar()->prepare("SELECT usuario FROM $tabla WHERE usuario = :usuario");
            $stmtCheckUser->bindParam(":usuario", $datos["loginUsuario"], PDO::PARAM_STR);
            $stmtCheckUser->execute();
            
            if ($stmtCheckUser->fetch()) {
                return "error-duplicado"; // Usuario duplicado
            }
            
            // La contraseña debe ser encriptada.
            $passwordEncriptada = password_hash($datos['password'], PASSWORD_DEFAULT);
            $estadoInicial = 'Pendiente'; // El estado por defecto para nuevos usuarios.

            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(numero_documento, tipo_documento, nombres_usuario, apellidos_usuario, sexo_usuario, rh_usuario, fecha_nacimiento, edad_usuario, telefono_usuario, email_usuario, usuario, password, estado_usuario) VALUES (:numero_documento, :tipo_documento, :nombres_usuario, :apellidos_usuario, :sexo_usuario, :rh_usuario, :fecha_nacimiento, :edad_usuario, :telefono_usuario, :email_usuario, :usuario, :password, :estado_usuario)");

            // Se usan las llaves del array $datos que vienen del formulario (camelCase).
            $stmt->bindParam(":numero_documento", $datos["numeroDocumento"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo_documento", $datos["tipoDocumento"], PDO::PARAM_STR);
            $stmt->bindParam(":nombres_usuario", $datos["nombreUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos_usuario", $datos["apellidoUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":sexo_usuario", $datos["sexoUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":rh_usuario", $datos["rhUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_nacimiento", $datos["fechaNacimiento"], PDO::PARAM_STR);
            $stmt->bindParam(":edad_usuario", $datos["edadUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono_usuario", $datos["telefonoUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":email_usuario", $datos["emailUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":usuario", $datos["loginUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":password", $passwordEncriptada, PDO::PARAM_STR);
            $stmt->bindParam(":estado_usuario", $estadoInicial, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            return "error";
        }
        $stmt = null;
    }

    /*=======================================
	METODO EDITAR USUARIO CON VALIDACIÓN
	=======================================*/
    static public function mdlEditarUsuario($tabla, $datos)
    {
        // --- INICIO DE LA VALIDACIÓN ---
        $validador = new MotorValidaciones();
        $reglas = ReglasUsuario::reglasEdicion();
        $errores = $validador->validar($datos, $reglas);

        if (!empty($errores)) {
            return ['status' => 'error-validacion', 'errors' => $errores];
        }
        // --- FIN DE LA VALIDACIÓN ---

        try {
            $sql = "UPDATE $tabla SET tipo_documento = :tipo_documento, nombres_usuario = :nombres_usuario, apellidos_usuario = :apellidos_usuario, sexo_usuario = :sexo_usuario, rh_usuario = :rh_usuario, fecha_nacimiento = :fecha_nacimiento, edad_usuario = :edad_usuario, telefono_usuario = :telefono_usuario, email_usuario = :email_usuario, estado_usuario = :estado_usuario";

            // Si se proporciona una nueva contraseña, se encripta y se añade a la consulta.
            if (!empty($datos['editarPassword'])) {
                $passwordEncriptada = password_hash($datos['editarPassword'], PASSWORD_DEFAULT);
                $sql .= ", password = :password";
            }

            $sql .= " WHERE numero_documento = :numero_documento";
            $stmt = Conexion::conectar()->prepare($sql);

            // Bind de los parámetros.
            $stmt->bindParam(":tipo_documento", $datos["editarTipoDocumento"], PDO::PARAM_STR);
            $stmt->bindParam(":nombres_usuario", $datos["editarNombreUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos_usuario", $datos["editarApellidoUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":sexo_usuario", $datos["editarSexoUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":rh_usuario", $datos["editarRhUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_nacimiento", $datos["editarFechaNacimiento"], PDO::PARAM_STR);
            $stmt->bindParam(":edad_usuario", $datos["editarEdadUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono_usuario", $datos["editarTelefonoUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":email_usuario", $datos["editarEmailUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":estado_usuario", $datos["editarEstadoUsuario"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_documento", $datos["editarNumeroDocumento"], PDO::PARAM_STR);

            if (!empty($datos['editarPassword'])) {
                $stmt->bindParam(":password", $passwordEncriptada, PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return "error-duplicado";
            }
            return "error";
        }
        $stmt = null;
    }

    /*=============================================
	BUSCAR USUARIO POR USUARIO Y EMAIL
	=============================================*/
    static public function mdlBuscarUsuarioPorUsuarioYEmail($tabla, $usuario, $email)
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE usuario = :usuario AND email_usuario = :email_usuario");
        $stmt->bindParam(":usuario", $usuario, PDO::PARAM_STR);
        $stmt->bindParam(":email_usuario", $email, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;
        return $resultado;
    }

    /*=============================================
	BUSCAR USUARIO POR TOKEN DE RESETEO
	=============================================*/
    static public function mdlBuscarUsuarioPorToken($tabla, $token)
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE reset_token = :reset_token AND reset_token_expiry > NOW()");
        $stmt->bindParam(":reset_token", $token, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;
        return $resultado;
    }

    /*  =======================================
  	METODO GUARDAR TOKEN DE RESETEO
	======================================= */

    static public function mdlGuardarTokenReseteo($tabla, $datos){

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET reset_token = :reset_token, reset_token_expiry = :reset_token_expiry WHERE id_usuario = :id_usuario");

        $stmt -> bindParam(":reset_token", $datos["reset_token"], PDO::PARAM_STR);
        $stmt -> bindParam(":reset_token_expiry", $datos["reset_token_expiry"], PDO::PARAM_STR);
        $stmt -> bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);

        if ($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }

        $stmt = null;
    }

    /*  =======================================
  	METODO ACTUALIZAR PASSWORD
	======================================= */

    static public function mdlActualizarPassword($tabla, $datos){

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id_usuario = :id_usuario");

        $stmt -> bindParam(":password", $datos["password"], PDO::PARAM_STR);
        $stmt -> bindParam(":id_usuario", $datos["id_usuario"], PDO::PARAM_INT);

        if ($stmt->execute()){
            return "ok";
        }else{
            return "error";
        }

        $stmt = null;
    }



}
