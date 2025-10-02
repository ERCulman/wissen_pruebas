<?php
// verificar-db.php

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar solo la conexión a la base de datos
require_once "modelos/conexion.php";

session_start();

// Verificar que haya una sesión para obtener el ID de usuario
if (!isset($_SESSION['id_usuario'])) {
    die("Por favor, inicia sesión primero y luego ejecuta este script.");
}
$usuarioId = $_SESSION['id_usuario'];

echo "<h1>Verificación Directa de la Base de Datos</h1>";
echo "<p>Ejecutando la consulta para el <strong>usuario_id = " . htmlspecialchars($usuarioId) . "</strong>...</p><hr>";

try {
    // Esta es la consulta exacta que se ejecuta cuando tu rol activo es de tipo "sistema"
    $sql = "SELECT 
                ads.usuario_id, 
                ads.rol_id, 
                ads.estado,
                r.nombre_rol
            FROM 
                administradores_sistema ads
            INNER JOIN 
                roles r ON ads.rol_id = r.id_rol
            WHERE 
                ads.usuario_id = :usuario_id";

    $stmt = Conexion::conectar()->prepare($sql);
    $stmt->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Resultado de la Consulta:</h2>";

    if (empty($resultados)) {
        echo "<p style='color:red; font-weight:bold;'>LA CONSULTA NO DEVOLVIÓ NINGÚN RESULTADO.</p>";
        echo "<p>Esto confirma que no se encontró una entrada en la tabla 'administradores_sistema' para tu usuario.</p>";
    } else {
        echo "<p style='color:green; font-weight:bold;'>Se encontraron los siguientes registros:</p>";
        echo "<pre>";
        print_r($resultados);
        echo "</pre>";
        echo "<hr><h3>Análisis:</h3>";
        $encontrado = false;
        foreach($resultados as $fila) {
            echo "<ul>";
            echo "<li><strong>Usuario ID:</strong> " . $fila['usuario_id'] . "</li>";
            echo "<li><strong>Rol ID:</strong> " . $fila['rol_id'] . "</li>";
            echo "<li><strong>Estado:</strong> " . $fila['estado'] . "</li>";
            echo "<li><strong>Nombre del Rol:</strong> " . $fila['nombre_rol'] . "</li>";
            echo "</ul>";

            if ($fila['estado'] === 'Activo' && in_array($fila['nombre_rol'], ['Superadministrador', 'Administrador'])) {
                $encontrado = true;
            }
        }
        if ($encontrado) {
            echo "<p style='color:green; font-weight:bold;'>¡ÉXITO! Se encontró un rol de administrador activo. El problema debe ser otro (posiblemente caché).</p>";
        } else {
            echo "<p style='color:red; font-weight:bold;'>¡PROBLEMA DETECTADO! Aunque se encontró un registro, o el estado no es 'Activo', o el nombre del rol no coincide exactamente con 'Superadministrador' o 'Administrador'.</p>";
        }
    }

} catch (Throwable $e) {
    echo "<h2 style='color:red;'>¡ERROR DE CONEXIÓN O SQL!</h2>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
}