<?php

class Conexion {

	static public function conectar(){

		/* Se crea una variable para almacenar la conexion, se utiliza la clase PDO para una conexion segura al servidor local, se intresan los parametros: "la conexion al servidor", "usuario servidor", "contraseña servidor" */

		$link = new PDO("mysql:host=localhost;dbname=wissen2",
						"root",
						"");

		$link->exec("set names utf8"); /*parametro que nos permite utilizar caracteres latinos*/

		return $link; /*retorna la conexion*/

	}

}