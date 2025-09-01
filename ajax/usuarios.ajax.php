 <?php

require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";

class AjaxUsuarios{

    /* =======================================
      EDITAR USUARIO
    =======================================*/

    public $idUsuario;

    public function ajaxEditarUsuario(){

        $item = "id_usuario";
        $valor = $this->idUsuario;

        $respuesta = ControladorUsuarios::ctrMostrarUsuario($item, $valor);

        echo json_encode($respuesta);


    }
    
    /* =======================================
      CREAR USUARIO
    =======================================*/

    public function ajaxCrearUsuario(){

        $respuesta = ControladorUsuarios::ctrCrearUsuario();

        echo $respuesta;

    }
    
    /* =======================================
      OLVIDO PASSWORD
    =======================================*/

    public function ajaxOlvidoPassword(){

        $respuesta = ControladorUsuarios::ctrOlvidoPassword();

        echo $respuesta;

    }

}

/* =======================================
    EDITAR USUARIO
=======================================*/

if(isset($_POST["idUsuario"])){

    $editar = new AjaxUsuarios();
    $editar -> idUsuario = $_POST["idUsuario"];
    $editar -> ajaxEditarUsuario();


}

/* =======================================
    CREAR USUARIO
=======================================*/

if(isset($_POST["loginUsuario"])){

    $crearUsuario = new AjaxUsuarios();
    $crearUsuario -> ajaxCrearUsuario();

}

/* =======================================
    OLVIDO PASSWORD
=======================================*/

if(isset($_POST["emailRecuperar"])){

    $olvidoPassword = new AjaxUsuarios();
    $olvidoPassword -> ajaxOlvidoPassword();

}

