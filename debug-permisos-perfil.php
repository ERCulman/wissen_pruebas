<?php
session_start();
require_once "servicios/ServicioAutorizacion.php";

echo "<h2>Debug Permisos - Perfil Profesional</h2>";

// 1. Verificar sesión
echo "<h3>1. Información de Sesión:</h3>";
echo "<p>Usuario ID: " . ($_SESSION['id_usuario'] ?? 'No definido') . "</p>";
echo "<p>Rol Activo: " . ($_SESSION['rol_activo'] ?? 'No definido') . "</p>";

// 2. Verificar ServicioAutorizacion
echo "<h3>2. ServicioAutorizacion Debug:</h3>";
$auth = ServicioAutorizacion::getInstance();
$debugInfo = $auth->debugInfo();
echo "<pre>";
print_r($debugInfo);
echo "</pre>";

// 3. Verificar si puede acceder al permiso específico
echo "<h3>3. Verificación de Permisos:</h3>";
echo "<p>¿Puede 'perfil_profesional_ver'? " . ($auth->puede('perfil_profesional_ver') ? 'SÍ' : 'NO') . "</p>";
echo "<p>¿Es admin? " . ($auth->esRolAdmin() ? 'SÍ' : 'NO') . "</p>";

// 4. Verificar configuración
echo "<h3>4. Configuración de Permisos:</h3>";
require_once "config/permisos.config.php";
$modulos = PermisosConfig::getModulosDelSistema();
if (isset($modulos['perfil-profesional'])) {
    echo "<p>✅ Módulo 'perfil-profesional' encontrado en configuración</p>";
    echo "<pre>";
    print_r($modulos['perfil-profesional']);
    echo "</pre>";
} else {
    echo "<p>❌ Módulo 'perfil-profesional' NO encontrado en configuración</p>";
}

// 5. Verificar rutas
echo "<h3>5. Verificación de Rutas:</h3>";
$rutas = include "vistas/routes-vistas.php";
if (isset($rutas['perfil-profesional'])) {
    echo "<p>✅ Ruta 'perfil-profesional' encontrada</p>";
    echo "<pre>";
    print_r($rutas['perfil-profesional']);
    echo "</pre>";
} else {
    echo "<p>❌ Ruta 'perfil-profesional' NO encontrada</p>";
}

// 6. Verificar base de datos
echo "<h3>6. Verificación en Base de Datos:</h3>";
require_once "modelos/conexion.php";
try {
    $stmt = Conexion::conectar()->prepare("SELECT * FROM acciones WHERE nombre_accion = 'perfil_profesional_ver'");
    $stmt->execute();
    $accion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($accion) {
        echo "<p>✅ Acción 'perfil_profesional_ver' encontrada en BD</p>";
        echo "<pre>";
        print_r($accion);
        echo "</pre>";
    } else {
        echo "<p>❌ Acción 'perfil_profesional_ver' NO encontrada en BD</p>";
        echo "<p><strong>SOLUCIÓN:</strong> Necesitas sincronizar los permisos</p>";
    }
} catch (Exception $e) {
    echo "<p>Error consultando BD: " . $e->getMessage() . "</p>";
}
?>