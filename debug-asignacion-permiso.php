<?php
require_once "modelos/conexion.php";

echo "<h2>Debug Asignación de Permisos</h2>";

try {
    $conexion = Conexion::conectar();
    
    // 1. Verificar la acción
    echo "<h3>1. Verificar Acción:</h3>";
    $stmt = $conexion->prepare("SELECT * FROM acciones WHERE nombre_accion = 'perfil_profesional_ver'");
    $stmt->execute();
    $accion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($accion) {
        echo "<p>✅ Acción encontrada - ID: " . $accion['id'] . "</p>";
        $accionId = $accion['id'];
        
        // 2. Verificar rol Superadministrador
        echo "<h3>2. Verificar Rol Superadministrador:</h3>";
        $stmt = $conexion->prepare("SELECT * FROM roles WHERE nombre_rol = 'Superadministrador'");
        $stmt->execute();
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($rol) {
            echo "<p>✅ Rol encontrado - ID: " . $rol['id_rol'] . "</p>";
            $rolId = $rol['id_rol'];
            
            // 3. Verificar si está asignado
            echo "<h3>3. Verificar Asignación:</h3>";
            $stmt = $conexion->prepare("SELECT * FROM roles_acciones WHERE rol_id = ? AND accion_id = ?");
            $stmt->execute([$rolId, $accionId]);
            $asignacion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($asignacion) {
                echo "<p>✅ Permiso YA está asignado al rol</p>";
                echo "<pre>";
                print_r($asignacion);
                echo "</pre>";
            } else {
                echo "<p>❌ Permiso NO está asignado al rol</p>";
                echo "<p><strong>Asignando permiso...</strong></p>";
                
                // 4. Asignar el permiso
                $stmt = $conexion->prepare("INSERT INTO roles_acciones (rol_id, accion_id) VALUES (?, ?)");
                if ($stmt->execute([$rolId, $accionId])) {
                    echo "<p>✅ Permiso asignado exitosamente</p>";
                } else {
                    echo "<p>❌ Error asignando permiso</p>";
                    print_r($stmt->errorInfo());
                }
            }
        } else {
            echo "<p>❌ Rol Superadministrador no encontrado</p>";
        }
    } else {
        echo "<p>❌ Acción no encontrada</p>";
    }
    
    // 5. Mostrar todos los permisos del rol
    echo "<h3>4. Todos los permisos del Superadministrador:</h3>";
    $stmt = $conexion->prepare("
        SELECT a.nombre_accion, a.descripcion 
        FROM roles_acciones ra 
        INNER JOIN acciones a ON ra.accion_id = a.id 
        INNER JOIN roles r ON ra.rol_id = r.id_rol 
        WHERE r.nombre_rol = 'Superadministrador' 
        AND a.nombre_accion LIKE '%perfil%'
        ORDER BY a.nombre_accion
    ");
    $stmt->execute();
    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Permisos relacionados con 'perfil':</p>";
    echo "<pre>";
    print_r($permisos);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><strong>Después de esto, cierra sesión y vuelve a iniciar para refrescar los permisos</strong></p>";
?>