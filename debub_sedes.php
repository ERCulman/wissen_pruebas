<?php
// debug_sesion_real.php - Verificar variables de sesión reales

session_start();

echo "<h3>DEBUG - Variables de Sesión Reales</h3>";

echo "<p><strong>Todas las variables de sesión:</strong></p>";
foreach($_SESSION as $key => $value) {
    echo "$key: $value<br>";
}

echo "<p><strong>Variables específicas del login:</strong></p>";
echo "iniciarSesion: " . (isset($_SESSION["iniciarSesion"]) ? $_SESSION["iniciarSesion"] : "No definido") . "<br>";
echo "nombres_usuario: " . (isset($_SESSION["nombres_usuario"]) ? $_SESSION["nombres_usuario"] : "No definido") . "<br>";
echo "apellidos_usuario: " . (isset($_SESSION["apellidos_usuario"]) ? $_SESSION["apellidos_usuario"] : "No definido") . "<br>";
echo "id_rol: " . (isset($_SESSION["id_rol"]) ? $_SESSION["id_rol"] : "No definido") . "<br>";
echo "id: " . (isset($_SESSION["id"]) ? $_SESSION["id"] : "No definido") . "<br>";

// Si hay nombres de usuario, intentar buscar el ID en la BD
if(isset($_SESSION["nombres_usuario"]) && !empty($_SESSION["nombres_usuario"])) {
    echo "<p><strong>Buscando ID del usuario en BD:</strong></p>";

    require_once "modelos/conexion.php";

    try {
        $stmt = Conexion::conectar()->prepare("
            SELECT id_usuario, usuario, nombres_usuario, apellidos_usuario 
            FROM usuarios 
            WHERE nombres_usuario = :nombres AND apellidos_usuario = :apellidos
        ");
        $stmt->bindParam(":nombres", $_SESSION["nombres_usuario"], PDO::PARAM_STR);
        $stmt->bindParam(":apellidos", $_SESSION["apellidos_usuario"], PDO::PARAM_STR);
        $stmt->execute();

        $usuario = $stmt->fetch();
        if($usuario) {
            echo "Usuario encontrado: " . $usuario["usuario"] . " (ID: " . $usuario["id_usuario"] . ")<br>";

            // Buscar sus instituciones
            $stmt2 = Conexion::conectar()->prepare("
                SELECT i.id, i.nombre 
                FROM institucion i 
                WHERE i.id_usuario_representante = :id_usuario AND i.estado = 1
            ");
            $stmt2->bindParam(":id_usuario", $usuario["id_usuario"], PDO::PARAM_INT);
            $stmt2->execute();

            $instituciones = $stmt2->fetchAll();
            echo "Instituciones donde es representante: " . count($instituciones) . "<br>";
            foreach($instituciones as $inst) {
                echo "- " . $inst["nombre"] . " (ID: " . $inst["id"] . ")<br>";
            }
        } else {
            echo "No se encontró usuario con esos nombres en la BD<br>";
        }
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
}
?>

<!--
Este debug nos ayudará a:
1. Ver todas las variables de sesión disponibles
2. Confirmar cuáles están definidas
3. Buscar el ID del usuario en la BD usando los nombres
4. Ver qué instituciones maneja ese usuario
-->