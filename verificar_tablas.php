<?php
require_once "modelos/conexion.php";

echo "<h1>Verificación de Tablas de Base de Datos</h1>";

try {
    $pdo = Conexion::conectar();
    
    // Tablas necesarias para el módulo de asistencia
    $tablasNecesarias = [
        'usuarios',
        'roles_institucionales', 
        'matricula',
        'grupo',
        'oferta_academica',
        'grado',
        'asignacion_academica',
        'cuerpo_docente',
        'periodo',
        'asistencia_clase'
    ];
    
    echo "<h2>Verificando existencia de tablas:</h2>";
    
    foreach ($tablasNecesarias as $tabla) {
        $sql = "SHOW TABLES LIKE '$tabla'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $existe = $stmt->fetch();
        
        if ($existe) {
            echo "✅ Tabla '$tabla' existe<br>";
            
            // Contar registros
            $sql = "SELECT COUNT(*) as total FROM $tabla";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $count = $stmt->fetch();
            echo "&nbsp;&nbsp;&nbsp;Registros: " . $count['total'] . "<br>";
        } else {
            echo "❌ Tabla '$tabla' NO existe<br>";
        }
    }
    
    // Verificar estructura específica de la tabla matricula
    echo "<h2>Verificando estructura de tabla 'matricula':</h2>";
    $sql = "DESCRIBE matricula";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $columnas = $stmt->fetchAll();
    
    $columnasEsperadas = ['id', 'grupo_id', 'roles_institucionales_id', 'estado_matricula'];
    
    foreach ($columnasEsperadas as $columna) {
        $encontrada = false;
        foreach ($columnas as $col) {
            if ($col['Field'] == $columna) {
                $encontrada = true;
                break;
            }
        }
        
        if ($encontrada) {
            echo "✅ Columna '$columna' existe en matricula<br>";
        } else {
            echo "❌ Columna '$columna' NO existe en matricula<br>";
        }
    }
    
    // Verificar relaciones
    echo "<h2>Verificando relaciones:</h2>";
    
    // Verificar si hay usuarios con roles institucionales
    $sql = "SELECT COUNT(*) as total 
            FROM usuarios u 
            INNER JOIN roles_institucionales ri ON u.id_usuario = ri.usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Usuarios con roles institucionales: " . $result['total'] . "<br>";
    
    // Verificar si hay matrículas con usuarios
    $sql = "SELECT COUNT(*) as total 
            FROM matricula m 
            INNER JOIN roles_institucionales ri ON m.roles_institucionales_id = ri.id
            INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Matrículas con usuarios válidos: " . $result['total'] . "<br>";
    
    // Verificar si hay grupos con oferta académica
    $sql = "SELECT COUNT(*) as total 
            FROM grupo g 
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Grupos con oferta académica: " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>