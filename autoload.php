<?php

// Autoload para middleware y clases del sistema
spl_autoload_register(function ($class) {
    $directories = [
        'middleware/',
        'controladores/',
        'modelos/',
        'servicios/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Incluir middleware automáticamente
require_once 'middleware/AuthMiddleware.php';
require_once 'middleware/RouteProtector.php';
require_once 'middleware/BackendProtector.php';