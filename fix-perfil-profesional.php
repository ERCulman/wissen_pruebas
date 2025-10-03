<?php
require_once "modelos/conexion.php";

echo "<h2>Reparar Permisos - Perfil Profesional</h2>";

try {
    $conexion = Conexion::conectar();
    
    // 1. Verificar si existe la acción
    $stmt = $conexion->prepare("SELECT * FROM acciones WHERE nombre_accion = 'perfil_profesional_ver'");
    $stmt->execute();
    $existe = $stmt->fetch();
    
    if (!$existe) {
        // 2. Crear la acción faltante
        $stmt = $conexion->prepare("INSERT INTO acciones (nombre_accion, descripcion) VALUES ('perfil_profesional_ver', 'Ver perfiles profesionales')");
        if ($stmt->execute()) {
            echo "<p>✅ Acción 'perfil_profesional_ver' creada exitosamente</p>";
            
            // 3. Obtener el ID de la acción recién creada
            $accionId = $conexion->lastInsertId();
            
            // 4. Asignar la acción al rol Superadministrador
            $stmt = $conexion->prepare("SELECT id_rol FROM roles WHERE nombre_rol = 'Superadministrador'");
            $stmt->execute();
            $rol = $stmt->fetch();
            
            if ($rol) {
                $stmt = $conexion->prepare("INSERT INTO roles_acciones (rol_id, accion_id) VALUES (?, ?)");
                if ($stmt->execute([$rol['id_rol'], $accionId])) {
                    echo "<p>✅ Permiso asignado al rol Superadministrador</p>";
                } else {
                    echo "<p>❌ Error asignando permiso al rol</p>";
                }
            }
        } else {
            echo "<p>❌ Error creando la acción</p>";
        }
    } else {
        echo "<p>ℹ️ La acción ya existe en la BD</p>";
    }
    
    echo "<p><strong>Ahora recarga la página de perfil-profesional</strong></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>