<?php

session_start();
require_once "../controladores/usuarios.controlador.php";
require_once "../modelos/usuarios.modelo.php";
require_once "../modelos/conexion.php";
require_once "../controladores/auth.controlador.php";
require_once "../modelos/auth.modelo.php";
require_once "../middleware/BackendProtector.php";

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
            error_log("[RECUPERAR] Iniciando ajaxRecuperarPassword - " . date('Y-m-d H:i:s'));
            $respuesta = ControladorUsuarios::ctrRecuperarPassword();
            error_log("[RECUPERAR] Respuesta del controlador: " . $respuesta);
            echo $respuesta;
        } catch (Exception $e) {
            error_log("[RECUPERAR] Exception: " . $e->getMessage());
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

    // PROTECCIÓN: Verificar permisos para ver usuario
    if (!BackendProtector::protectAjax('usuarios_ver')) {
        exit();
    }

    $editar = new AjaxUsuarios();
    $editar -> idUsuario = $_POST["idUsuario"];
    $editar -> ajaxEditarUsuario();

}

/* =======================================
    LÓGICA PARA CREAR USUARIO
=======================================*/

if(isset($_POST["loginUsuario"])){

    // PROTECCIÓN: Permitir registro público (sin sesión) o verificar permisos si hay sesión
    if (!BackendProtector::protectAjaxWithPublicException('usuarios_crear', true)) {
        exit();
    }

    $crearUsuario = new AjaxUsuarios();
    $crearUsuario -> ajaxCrearUsuario();

}

/* =======================================
    LÓGICA PARA RECUPERAR PASSWORD
=======================================*/

if(isset($_POST["usuarioRecuperar"]) && isset($_POST["emailRecuperar"])){

    // Crear identificador único para esta solicitud
    $solicitudId = md5($_POST["usuarioRecuperar"] . $_POST["emailRecuperar"] . time());
    
    // Verificar si ya se está procesando esta solicitud
    if(isset($_SESSION["procesando_recuperacion"]) && 
       (time() - $_SESSION["procesando_recuperacion"]) < 5) {
        echo "ok"; // Simular éxito para evitar doble procesamiento
        exit;
    }
    
    $_SESSION["procesando_recuperacion"] = time();
    
    $recuperarPassword = new AjaxUsuarios();
    $recuperarPassword -> ajaxRecuperarPassword();
    
    unset($_SESSION["procesando_recuperacion"]);

} else {
    // Debug: verificar qué datos llegan
    error_log("POST data: " . print_r($_POST, true));
    if(isset($_POST["usuarioRecuperar"]) || isset($_POST["emailRecuperar"])){
        echo "error-datos-incompletos";
    }
}

