<?php
require_once "modelos/conexion.php";

echo "<h1>Verificación de IDs Válidos</h1>";

try {
    $pdo = Conexion::conectar();
    
    // Verificar asignaciones académicas disponibles
    echo "<h2>Asignaciones Académicas Disponibles:</h2>";
    $sql = "SELECT aa.id, aa.cuerpo_docente_id, aa.grupo_id, aa.periodo_academico_id,
                   a.nombre as asignatura, g.nombre as grupo, grd.nombre as grado
            FROM asignacion_academica aa
            INNER JOIN estructura_curricular ec ON aa.estructura_curricular_id = ec.id
            INNER JOIN asignatura a ON ec.asignatura_id = a.id
            INNER JOIN grupo g ON aa.grupo_id = g.id
            INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
            INNER JOIN grado grd ON oa.grado_id = grd.id
            WHERE aa.estado = 'Activa'
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $asignaciones = $stmt->fetchAll();
    
    if (count($asignaciones) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Asignatura</th><th>Grado</th><th>Grupo</th><th>Docente ID</th><th>Período ID</th></tr>";
        foreach ($asignaciones as $asig) {
            echo "<tr>";
            echo "<td>{$asig['id']}</td>";
            echo "<td>{$asig['asignatura']}</td>";
            echo "<td>{$asig['grado']}</td>";
            echo "<td>{$asig['grupo']}</td>";
            echo "<td>{$asig['cuerpo_docente_id']}</td>";
            echo "<td>{$asig['periodo_academico_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No hay asignaciones académicas activas<br>";
    }
    
    // Verificar matrículas disponibles
    echo "<h2>Matrículas Disponibles (primeras 10):</h2>";
    $sql = "SELECT m.id, m.grupo_id, u.nombres_usuario, u.apellidos_usuario, g.nombre as grupo
            FROM matricula m
            INNER JOIN roles_institucionales ri ON m.roles_institucionales_id = ri.id
            INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
            INNER JOIN grupo g ON m.grupo_id = g.id
            WHERE m.estado_matricula = 'Matriculado'
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $matriculas = $stmt->fetchAll();
    
    if (count($matriculas) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Matrícula ID</th><th>Estudiante</th><th>Grupo</th><th>Grupo ID</th></tr>";
        foreach ($matriculas as $mat) {
            echo "<tr>";
            echo "<td>{$mat['id']}</td>";
            echo "<td>{$mat['nombres_usuario']} {$mat['apellidos_usuario']}</td>";
            echo "<td>{$mat['grupo']}</td>";
            echo "<td>{$mat['grupo_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No hay matrículas activas<br>";
    }
    
    // Verificar cuerpo docente
    echo "<h2>Cuerpo Docente Disponible:</h2>";
    $sql = "SELECT cd.id, u.nombres_usuario, u.apellidos_usuario
            FROM cuerpo_docente cd
            INNER JOIN roles_institucionales ri ON cd.rol_institucional_id = ri.id
            INNER JOIN usuarios u ON ri.usuario_id = u.id_usuario
            WHERE ri.estado = 'Activo'
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $docentes = $stmt->fetchAll();
    
    if (count($docentes) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Docente ID</th><th>Nombre</th></tr>";
        foreach ($docentes as $doc) {
            echo "<tr>";
            echo "<td>{$doc['id']}</td>";
            echo "<td>{$doc['nombres_usuario']} {$doc['apellidos_usuario']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No hay docentes activos<br>";
    }
    
    // Verificar períodos
    echo "<h2>Períodos Disponibles:</h2>";
    $sql = "SELECT id, nombre, fecha_inicio, fecha_fin FROM periodo ORDER BY fecha_inicio DESC LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $periodos = $stmt->fetchAll();
    
    if (count($periodos) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Período ID</th><th>Nombre</th><th>Fecha Inicio</th><th>Fecha Fin</th></tr>";
        foreach ($periodos as $per) {
            echo "<tr>";
            echo "<td>{$per['id']}</td>";
            echo "<td>{$per['nombre']}</td>";
            echo "<td>{$per['fecha_inicio']}</td>";
            echo "<td>{$per['fecha_fin']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No hay períodos disponibles<br>";
    }
    
    // Sugerir datos de prueba válidos
    if (count($asignaciones) > 0 && count($matriculas) > 0) {
        $asigPrueba = $asignaciones[0];
        $matPrueba = $matriculas[0];
        
        echo "<h2>Datos de Prueba Sugeridos:</h2>";
        echo "<strong>Asignación ID:</strong> {$asigPrueba['id']}<br>";
        echo "<strong>Matrícula ID:</strong> {$matPrueba['id']}<br>";
        echo "<strong>Docente ID:</strong> {$asigPrueba['cuerpo_docente_id']}<br>";
        echo "<strong>Período ID:</strong> {$asigPrueba['periodo_academico_id']}<br>";
        echo "<strong>Grupo ID:</strong> {$asigPrueba['grupo_id']}<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>