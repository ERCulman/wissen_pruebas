<?php
// PROTECCIÓN GLOBAL PARA TODOS LOS AJAX
session_start();
require_once "../controladores/auth.controlador.php";
require_once "../modelos/auth.modelo.php";
require_once "../modelos/conexion.php";

// Verificación básica de sesión solamente
if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}
?>