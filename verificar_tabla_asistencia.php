<?php
require_once "modelos/conexion.php";

echo "<h1>Verificación de Tabla asistencia_clase</h1>";

try {
    $pdo = Conexion::conectar();
    
    // Verificar estructura de la tabla
    echo "<h2>Estructura de la tabla:</h2>";
    $sql = "DESCRIBE asistencia_clase";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $columnas = $stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columnas as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar índices
    echo "<h2>Índices de la tabla:</h2>";
    $sql = "SHOW INDEX FROM asistencia_clase";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $indices = $stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>Key_name</th><th>Column_name</th><th>Non_unique</th></tr>";
    foreach ($indices as $idx) {
        echo "<tr>";
        echo "<td>{$idx['Key_name']}</td>";
        echo "<td>{$idx['Column_name']}</td>";
        echo "<td>{$idx['Non_unique']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Probar inserción directa
    echo "<h2>Prueba de inserción directa:</h2>";
    
    $testData = [
        'asignacion_academica_id' => 1,
        'matricula_id' => 1,
        'fecha' => date('Y-m-d'),
        'hora_inicio_clase' => '07:00:00',
        'hora_fin_clase' => '08:00:00',
        'estado' => 'Presente',
        'minutos_retraso' => 0,
        'justificacion_estado' => 'No aplica',
        'registrado_por_usuario_id' => 1
    ];
    
    $sql = "INSERT INTO asistencia_clase 
            (asignacion_academica_id, matricula_id, fecha, hora_inicio_clase, hora_fin_clase, 
             estado, minutos_retraso, justificacion_estado, registrado_por_usuario_id)
            VALUES (:asignacion_id, :matricula_id, :fecha, :hora_inicio, :hora_fin, 
                    :estado, :minutos_retraso, :justificacion_estado, :usuario_id)
            ON DUPLICATE KEY UPDATE
            estado = VALUES(estado),
            minutos_retraso = VALUES(minutos_retraso),
            justificacion_estado = VALUES(justificacion_estado)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":asignacion_id", $testData['asignacion_academica_id'], PDO::PARAM_INT);
        $stmt->bindParam(":matricula_id", $testData['matricula_id'], PDO::PARAM_INT);
        $stmt->bindParam(":fecha", $testData['fecha']);
        $stmt->bindParam(":hora_inicio", $testData['hora_inicio_clase']);
        $stmt->bindParam(":hora_fin", $testData['hora_fin_clase']);
        $stmt->bindParam(":estado", $testData['estado']);
        $stmt->bindParam(":minutos_retraso", $testData['minutos_retraso'], PDO::PARAM_INT);
        $stmt->bindParam(":justificacion_estado", $testData['justificacion_estado']);
        $stmt->bindParam(":usuario_id", $testData['registrado_por_usuario_id'], PDO::PARAM_INT);
        
        $resultado = $stmt->execute();
        
        if ($resultado) {
            echo "✅ Inserción exitosa<br>";
            echo "Filas afectadas: " . $stmt->rowCount() . "<br>";
        } else {
            echo "❌ Error en inserción<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
    
    // Verificar si se guardó
    echo "<h2>Verificar registro guardado:</h2>";
    $sql = "SELECT * FROM asistencia_clase WHERE asignacion_academica_id = 1 AND matricula_id = 1 AND fecha = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([date('Y-m-d')]);
    $registro = $stmt->fetch();
    
    if ($registro) {
        echo "✅ Registro encontrado:<br>";
        echo "<pre>" . print_r($registro, true) . "</pre>";
    } else {
        echo "❌ No se encontró el registro<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>