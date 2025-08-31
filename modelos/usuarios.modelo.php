<?php

require_once "conexion.php";

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

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(numero_documento, tipo_documento, nombres_usuario, apellidos_usuario, sexo_usuario, rh_usuario, fecha_nacimiento, edad_usuario, telefono_usuario, email_usuario, usuario, password, estado_usuario, id_rol)VALUES(:numero_documento, :tipo_documento, :nombres_usuario, :apellidos_usuario, :sexo_usuario, :rh_usuario, :fecha_nacimiento, :edad_usuario, :telefono_usuario, :email_usuario, :usuario, :password, :estado_usuario, :id_rol)"); /*Variable que me realice la consulta para insertar datos del usuario en la base de datos*/

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
		$stmt -> bindParam(":id_rol", $datos["id_rol"], PDO::PARAM_STR);

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

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET numero_documento = :numero_documento, tipo_documento = :tipo_documento, nombres_usuario = :nombres_usuario, apellidos_usuario = :apellidos_usuario, sexo_usuario = :sexo_usuario, rh_usuario = :rh_usuario, fecha_nacimiento = :fecha_nacimiento, edad_usuario = :edad_usuario, telefono_usuario = :telefono_usuario, email_usuario = :email_usuario, usuario = :usuario, password = :password, estado_usuario = :estado_usuario, id_rol = :id_rol WHERE usuario = :usuario"); /*Variable que me realice la actualizacion del usuario en la base de datos*/

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
		$stmt -> bindParam(":id_rol", $datos["id_rol"], PDO::PARAM_STR);

		if ($stmt->execute()){

			return "ok";
		}else{
			return "error";
		}

		$stmt -> close(); /*Cierre la conexion del retunro*/

		$stmt -> null; /*No almacena la instancia*/


	}

}