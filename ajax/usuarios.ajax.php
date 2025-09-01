<?php

require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

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
      MÉTODO PARA OLVIDO DE PASSWORD
    =======================================*/

    public function ajaxOlvidoPassword(){

        $respuesta = ControladorUsuarios::ctrOlvidoPassword();

        echo $respuesta;
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
    LÓGICA PARA OLVIDO DE PASSWORD
=======================================*/

if(isset($_POST["emailRecuperar"])){

    $olvidoPassword = new AjaxUsuarios();
    $olvidoPassword -> ajaxOlvidoPassword();

}

