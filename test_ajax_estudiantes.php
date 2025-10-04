<?php
// Archivo de prueba para simular la llamada AJAX de estudiantes
session_start();

// Simular datos de sesión si es necesario
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['id_usuario'] = 1; // Usar un ID de usuario válido para pruebas
}

// Simular la llamada POST
$_POST['grupo_id'] = 1; // Cambiar por un ID de grupo válido

// Incluir los archivos necesarios
require_once "modelos/conexion.php";
require_once "controladores/asistencia.controlador.php";
require_once "modelos/asistencia.modelo.php";

echo "<h2>Prueba de obtención de estudiantes</h2>";
echo "Grupo ID: " . $_POST['grupo_id'] . "<br><br>";

// Llamar directamente al controlador
echo "<h3>Resultado del controlador:</h3>";
ob_start();
ControladorAsistencia::ctrObtenerEstudiantesGrupo();
$resultado = ob_get_clean();

echo "Respuesta JSON: " . $resultado . "<br><br>";

// Decodificar y mostrar de forma legible
$datos = json_decode($resultado, true);
if ($datos) {
    echo "<h3>Datos decodificados:</h3>";
    echo "<pre>" . print_r($datos, true) . "</pre>";
} else {
    echo "Error al decodificar JSON o respuesta vacía";
}
?>