<?php
/*
 * SCRIPT DE DIAGNÓSTICO Y VERIFICACIÓN DE SINTAXIS PHP
 * =======================================================
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta raíz de tu proyecto.
 * 2. AJUSTA LAS RUTAS en la sección "$files_to_check" para que coincidan con tu estructura.
 * 3. Abre este archivo en tu navegador (ej: https://tusitio.com/debug_checker.php).
 * 4. Copia y pega TODO el resultado para compartirlo.
 * 5. ¡¡¡BORRA ESTE ARCHIVO DE TU SERVIDOR CUANDO TERMINES!!!
 */

// Forzar la visualización de errores para el propio script de debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- CONFIGURACIÓN: AJUSTA LAS RUTAS DE LOS ARCHIVOS AQUÍ ---
$files_to_check = [
    // Ajusta la ruta si tus controladores están en otra carpeta.
    'controladores/asignacion-docente-asignaturas.controlador.php',

    // Ajusta la ruta si tus modelos están en otra carpeta.
    'modelos/asignacion-docente-asignaturas.modelo.php',

    // Ajusta la ruta a tu vista. Puede ser 'vistas/paginas/', 'vistas/modulos/', etc.
    'vistas/modulos/asignacion-docente-asignaturas.php',
];
// -----------------------------------------------------------

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificador de Errores PHP</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #555; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .result { padding: 15px; margin-bottom: 15px; border-left-width: 5px; border-left-style: solid; }
        .success { background-color: #e9f7ef; border-color: #2ecc71; color: #222; }
        .error { background-color: #fbeae5; border-color: #e74c3c; color: #222; white-space: pre-wrap; word-wrap: break-word; }
        .warning { background-color: #fcf8e3; border-color: #f1c40f; color: #222; }
        code { background: #eee; padding: 2px 5px; border-radius: 3px; }
        pre { background: #2d2d2d; color: #f1f1f1; padding: 15px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
<div class="container">
    <h1>Verificador de Errores PHP</h1>
    <div class="result warning">
        <strong>¡ADVERTENCIA DE SEGURIDAD!</strong> Por favor, borra este archivo de tu servidor tan pronto como hayas terminado de usarlo.
    </div>

    <h2>1. Verificación del Entorno del Servidor</h2>
    <?php
    echo "<div class='result ".(function_exists('ini_set') ? 'success' : 'error')."'>La función <code>ini_set()</code> está: ".(function_exists('ini_set') ? 'Habilitada' : 'Deshabilitada (esto puede impedir mostrar errores)')."</div>";
    echo "<div class='result ".(function_exists('shell_exec') ? 'success' : 'error')."'>La función <code>shell_exec()</code> para análisis automático está: ".(function_exists('shell_exec') ? 'Habilitada' : 'Deshabilitada (no se podrá hacer el análisis automático)')."</div>";
    ?>

    <h2>2. Análisis de Sintaxis de Archivos (usando <code>php -l</code>)</h2>
    <?php
    if (function_exists('shell_exec')) {
        foreach ($files_to_check as $file) {
            echo "<h4>Analizando: <code>" . htmlspecialchars($file) . "</code></h4>";
            if (file_exists($file) && is_readable($file)) {
                // Usamos realpath para obtener la ruta absoluta y evitar problemas
                $command = 'php -l ' . escapeshellarg(realpath($file));
                // Capturamos la salida estándar y de error
                $output = shell_exec($command . ' 2>&1');

                if (strpos($output, 'No syntax errors detected') !== false) {
                    echo "<div class='result success'><strong>OK:</strong> " . htmlspecialchars($output) . "</div>";
                } else {
                    echo "<div class='result error'><strong>ERROR:</strong><br>" . htmlspecialchars($output) . "</div>";
                }
            } else {
                echo "<div class='result error'><strong>ERROR:</strong> El archivo no existe o no se puede leer en la ruta especificada. Verifica la configuración de rutas al inicio de este script.</div>";
            }
        }
    } else {
        echo "<div class='result warning'>El análisis automático no es posible porque <code>shell_exec</code> está deshabilitado en tu servidor. Revisa el contenido de los archivos manualmente en la siguiente sección.</div>";
    }
    ?>

    <h2>3. Contenido de los Archivos (para revisión manual)</h2>
    <?php
    foreach ($files_to_check as $file) {
        echo "<h4>Contenido de: <code>" . htmlspecialchars($file) . "</code></h4>";
        if (file_exists($file) && is_readable($file)) {
            echo "<pre><code>" . htmlspecialchars(file_get_contents($file)) . "</code></pre>";
        } else {
            echo "<div class='result error'>No se pudo leer el contenido del archivo.</div>";
        }
    }
    ?>
</div>
</body>
</html>