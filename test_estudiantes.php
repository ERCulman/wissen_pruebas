<?php
// Archivo de prueba para verificar la consulta de estudiantes
require_once "modelos/conexion.php";

// Obtener todos los grupos disponibles
try {
    $pdo = Conexion::conectar();
    
    echo "<h2>Grupos disponibles:</h2>";
    $sql = "SELECT g.id, g.nombre, grd.nombre as grado 
            FROM grupo g 
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            INNER JOIN grado grd ON oa.grado_id = grd.id
            ORDER BY grd.numero, g.nombre";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($grupos as $grupo) {
        echo "ID: {$grupo['id']} - {$grupo['grado']} - {$grupo['nombre']}<br>";
    }
    
    // Probar con el primer grupo disponible
    if (!empty($grupos)) {
        $grupoId = $grupos[0]['id'];
        echo "<h2>Estudiantes del grupo ID: $grupoId</h2>";
        
        $sql = "SELECT 
                    m.id as matricula_id,
                    u.nombres_usuario,
                    u.apellidos_usuario,
                    u.tipo_documento,
                    u.numero_documento
                FROM matricula m
                INNER JOIN roles_institucionales ri ON m.roles_institucionales_id = ri.id
                INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
                WHERE m.grupo_id = :grupo_id 
                  AND m.estado_matricula = 'Matriculado'
                ORDER BY u.apellidos_usuario, u.nombres_usuario";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Número de estudiantes encontrados: " . count($estudiantes) . "<br><br>";
        
        foreach ($estudiantes as $estudiante) {
            echo "Matrícula ID: {$estudiante['matricula_id']} - {$estudiante['nombres_usuario']} {$estudiante['apellidos_usuario']} - {$estudiante['tipo_documento']} {$estudiante['numero_documento']}<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>