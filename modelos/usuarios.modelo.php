<?php

// Se utiliza __DIR__ para asegurar que la ruta al archivo de conexión sea siempre correcta.
require_once __DIR__ . "/conexion.php";

class ModeloUsuarios{

	/*  =======================================
  	METODO MOSTRAR USUARIOS
	======================================= */

	/* Se crea el metodo mostrar usuarios, se ingresan como parametros las variables que vienen del controlador ingresar usuarios*/

	static public function mdlMostrarUsuarios($tabla, $item, $valor){

		if($item != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item"); /*Variable que me realice la consulta del usuario en la base de datos*/

			$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR); /*Usamos el metodo bindParam que significa enlace al parametro, trayendo solo las variables en tipo string*/

			$stmt -> execute(); /*Se ejecuta la consulta*/

			return $stmt -> fetch(); /*Retormanos la consulta*/

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla"); /*Variable que me realice la consulta del usuario en la base de datos*/

			$stmt -> execute(); /*Se ejecuta la consulta*/

			return $stmt->fetchAll(PDO::FETCH_ASSOC);  /*Retormanos la consulta*/
		}

		
		$stmt -> close(); /*Cierre la conexion del retunro*/

		$stmt -> null; /*No almacena la instancia*/
	}

	/*  =======================================
  	METODO REGISTRO DE USUARIO
	======================================= */

	static public function mdlCrearUsuario($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(numero_documento, tipo_documento, nombres_usuario, apellidos_usuario, sexo_usuario, rh_usuario, fecha_nacimiento, edad_usuario, telefono_usuario, email_usuario, usuario, password, estado_usuario)VALUES(:numero_documento, :tipo_documento, :nombres_usuario, :apellidos_usuario, :sexo_usuario, :rh_usuario, :fecha_nacimiento, :edad_usuario, :telefono_usuario, :email_usuario, :usuario, :password, :estado_usuario)"); /*Variable que me realice la consulta para insertar datos del usuario en la base de datos*/

		/*Usamos el metodo bindParam que significa enlace al parametro, trayendo solo las variables en tipo string*/

		$stmt -> bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR); 
		$stmt -> bindParam(":tipo_documento", $datos["tipo_documento"], PDO::PARAM_STR);
		$stmt -> bindParam(":nombres_usuario", $datos["nombres_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":apellidos_usuario", $datos["apellidos_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":sexo_usuario", $datos["sexo_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":rh_usuario", $datos["rh_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":fecha_nacimiento", $datos["fecha_nacimiento"], PDO::PARAM_STR);
		$stmt -> bindParam(":edad_usuario", $datos["edad_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono_usuario", $datos["telefono_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":email_usuario", $datos["email_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":password", $datos["password"], PDO::PARAM_STR);
		$stmt -> bindParam(":estado_usuario", $datos["estado_usuario"], PDO::PARAM_STR);

		if ($stmt->execute()){

			return "ok";
		}else{
			return "error";
		}

		$stmt -> close(); /*Cierre la conexion del retunro*/

		$stmt -> null; /*No almacena la instancia*/

	}

	/*  =======================================
  	METODO EDITAR USUARIO
	======================================= */

	static public function mdlEditarUsuario($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET numero_documento = :numero_documento, tipo_documento = :tipo_documento, nombres_usuario = :nombres_usuario, apellidos_usuario = :apellidos_usuario, sexo_usuario = :sexo_usuario, rh_usuario = :rh_usuario, fecha_nacimiento = :fecha_nacimiento, edad_usuario = :edad_usuario, telefono_usuario = :telefono_usuario, email_usuario = :email_usuario, usuario = :usuario, password = :password, estado_usuario = :estado_usuario WHERE usuario = :usuario"); /*Variable que me realice la actualizacion del usuario en la base de datos*/

		/*Usamos el metodo bindParam que significa enlace al parametro, trayendo solo las variables en tipo string*/

		$stmt -> bindParam(":numero_documento", $datos["numero_documento"], PDO::PARAM_STR); 
		$stmt -> bindParam(":tipo_documento", $datos["tipo_documento"], PDO::PARAM_STR);
		$stmt -> bindParam(":nombres_usuario", $datos["nombres_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":apellidos_usuario", $datos["apellidos_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":sexo_usuario", $datos["sexo_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":rh_usuario", $datos["rh_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":fecha_nacimiento", $datos["fecha_nacimiento"], PDO::PARAM_STR);
		$stmt -> bindParam(":edad_usuario", $datos["edad_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":telefono_usuario", $datos["telefono_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":email_usuario", $datos["email_usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
		$stmt -> bindParam(":password", $datos["password"], PDO::PARAM_STR);
		$stmt -> bindParam(":estado_usuario", $datos["estado_usuario"], PDO::PARAM_STR);

		if ($stmt->execute()){

			return "ok";
		}else{
			return "error";
		}

		$stmt -> close(); /*Cierre la conexion del retunro*/

		$stmt -> null; /*No almacena la instancia*/


	}

    /*=============================================
	BUSCAR USUARIO POR USUARIO Y EMAIL
	=============================================*/
	static public function mdlBuscarUsuarioPorUsuarioYEmail($tabla, $usuario, $email){
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
	static public function mdlBuscarUsuarioPorToken($tabla, $token){
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
