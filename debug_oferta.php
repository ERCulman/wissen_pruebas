<?php
// debug_oferta.php - Coloca este archivo en la raíz de tu proyecto

require_once "modelos/conexion.php";

echo "<h1>Debug Oferta Educativa</h1>";

// 1. Verificar conexión
try {
    $conexion = Conexion::conectar();
    echo "<p style='color: green;'>✅ Conexión a BD: OK</p>";
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error conexión: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Verificar tabla curso
echo "<h2>Tabla CURSO</h2>";
try {
    $stmt = $conexion->prepare("SELECT * FROM curso");
    $stmt->execute();
    $cursos = $stmt->fetchAll();

    if(count($cursos) > 0) {
        echo "<p style='color: green;'>✅ Cursos encontrados: " . count($cursos) . "</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Tipo</th><th>Nombre</th></tr>";
        foreach($cursos as $curso) {
            echo "<tr><td>" . $curso['id'] . "</td><td>" . $curso['tipo'] . "</td><td>" . $curso['nombre'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No hay cursos en la tabla</p>";
        echo "<p><strong>SOLUCIÓN:</strong> Ejecuta este SQL:</p>";
        echo "<pre>INSERT INTO curso (tipo, nombre) VALUES 
('Alfabético', 'A'),
('Alfabético', 'B'), 
('Alfabético', 'C'),
('Numérico', '001'),
('Numérico', '002');</pre>";
    }
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error en tabla curso: " . $e->getMessage() . "</p>";
}

// 3. Verificar tabla grado
echo "<h2>Tabla GRADO</h2>";
try {
    $stmt = $conexion->prepare("SELECT g.*, ne.nombre as nivel FROM grado g LEFT JOIN nivel_educativo ne ON g.nivel_educativo_id = ne.id");
    $stmt->execute();
    $grados = $stmt->fetchAll();

    if(count($grados) > 0) {
        echo "<p style='color: green;'>✅ Grados encontrados: " . count($grados) . "</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Nivel</th></tr>";
        foreach($grados as $grado) {
            echo "<tr><td>" . $grado['id'] . "</td><td>" . $grado['nombre'] . "</td><td>" . $grado['nivel'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No hay grados en la tabla</p>";
    }
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error en tabla grado: " . $e->getMessage() . "</p>";
}

// 4. Verificar otras tablas necesarias
$tablas = ['anio_lectivo', 'sede', 'jornada', 'nivel_educativo'];
foreach($tablas as $tabla) {
    try {
        $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM $tabla");
        $stmt->execute();
        $result = $stmt->fetch();
        echo "<p>📊 $tabla: " . $result['total'] . " registros</p>";
    } catch(Exception $e) {
        echo "<p style='color: red;'>❌ Error en $tabla: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Instrucciones</h2>";
echo "<ol>";
echo "<li>Si ves ❌ en cursos, ejecuta el SQL que aparece arriba</li>";
echo "<li>Si ves ❌ en grados, revisa que tengas datos en nivel_educativo y grado</li>";
echo "<li>Si todo está ✅, el problema está en el JavaScript o en el envío del formulario</li>";
echo "</ol>";
?>