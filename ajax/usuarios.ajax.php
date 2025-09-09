<?php

require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";
require_once "../modelos/conexion.php";

class AjaxUsuarios{

    /* =======================================
      MÉTODO PARA EDITAR USUARIO
    =======================================*/

    public $idUsuario;

    public function ajaxEditarUsuario(){

        $item = "id_usuario";
        $valor = $this->idUsuario;

        $respuesta = ControladorUsuarios::ctrMostrarUsuario($item, $valor);

        echo json_encode($respuesta);
    }
    
    /* =======================================
      MÉTODO PARA CREAR USUARIO
    =======================================*/

    public function ajaxCrearUsuario(){

        $respuesta = ControladorUsuarios::ctrCrearUsuario();

        echo $respuesta;
    }
    
    /* =======================================
      MÉTODO PARA RECUPERAR PASSWORD
    =======================================*/

    public function ajaxRecuperarPassword(){
        try {
            $respuesta = ControladorUsuarios::ctrRecuperarPassword();
            echo $respuesta;
        } catch (Exception $e) {
            echo "error-exception: " . $e->getMessage();
        }
    }

}

/* ===================================================================================
   SECCIÓN DE EJECUCIÓN (DISPATCHER)
   Aquí se crean los objetos y se llaman a los métodos según la petición POST
======================================================================================*/

/* =======================================
    LÓGICA PARA EDITAR USUARIO
=======================================*/

if(isset($_POST["idUsuario"])){

    $editar = new AjaxUsuarios();
    $editar -> idUsuario = $_POST["idUsuario"];
    $editar -> ajaxEditarUsuario();

}

/* =======================================
    LÓGICA PARA CREAR USUARIO
=======================================*/

if(isset($_POST["loginUsuario"])){

    $crearUsuario = new AjaxUsuarios();
    $crearUsuario -> ajaxCrearUsuario();

}

/* =======================================
    LÓGICA PARA RECUPERAR PASSWORD
=======================================*/

if(isset($_POST["usuarioRecuperar"]) && isset($_POST["emailRecuperar"])){

    $recuperarPassword = new AjaxUsuarios();
    $recuperarPassword -> ajaxRecuperarPassword();

} else {
    // Debug: verificar qué datos llegan
    error_log("POST data: " . print_r($_POST, true));
    if(isset($_POST["usuarioRecuperar"]) || isset($_POST["emailRecuperar"])){
        echo "error-datos-incompletos";
    }
}

