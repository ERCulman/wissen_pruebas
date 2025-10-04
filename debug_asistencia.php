<?php
session_start();

// Simular sesión de usuario si no existe
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['id_usuario'] = 1;
}

echo "<h1>Debug del Módulo de Asistencia</h1>";

// 1. Verificar conexión a base de datos
echo "<h2>1. Verificando conexión a base de datos...</h2>";
try {
    require_once "modelos/conexion.php";
    $pdo = Conexion::conectar();
    echo "✅ Conexión exitosa<br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Verificar que existen grupos
echo "<h2>2. Verificando grupos disponibles...</h2>";
try {
    $sql = "SELECT COUNT(*) as total FROM grupo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Total de grupos: " . $result['total'] . "<br>";
    
    if ($result['total'] > 0) {
        $sql = "SELECT g.id, g.nombre, grd.nombre as grado 
                FROM grupo g 
                INNER JOIN oferta_academica oa ON g.oferta_educativa_id = oa.id
                INNER JOIN grado grd ON oa.grado_id = grd.id
                LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $grupos = $stmt->fetchAll();
        
        echo "Primeros 5 grupos:<br>";
        foreach ($grupos as $grupo) {
            echo "- ID: {$grupo['id']}, Grado: {$grupo['grado']}, Nombre: {$grupo['nombre']}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error consultando grupos: " . $e->getMessage() . "<br>";
}

// 3. Verificar que existen matrículas
echo "<h2>3. Verificando matrículas...</h2>";
try {
    $sql = "SELECT COUNT(*) as total FROM matricula WHERE estado_matricula = 'Matriculado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Total de matrículas activas: " . $result['total'] . "<br>";
} catch (Exception $e) {
    echo "❌ Error consultando matrículas: " . $e->getMessage() . "<br>";
}

// 4. Probar la consulta específica de estudiantes
echo "<h2>4. Probando consulta de estudiantes...</h2>";
try {
    // Obtener el primer grupo que tenga estudiantes
    $sql = "SELECT DISTINCT m.grupo_id 
            FROM matricula m 
            WHERE m.estado_matricula = 'Matriculado' 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $grupo = $stmt->fetch();
    
    if ($grupo) {
        $grupoId = $grupo['grupo_id'];
        echo "Probando con grupo ID: $grupoId<br>";
        
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
        
        echo "Estudiantes encontrados: " . count($estudiantes) . "<br>";
        
        if (count($estudiantes) > 0) {
            echo "Primeros estudiantes:<br>";
            foreach (array_slice($estudiantes, 0, 3) as $estudiante) {
                echo "- {$estudiante['nombres_usuario']} {$estudiante['apellidos_usuario']}<br>";
            }
        }
    } else {
        echo "❌ No se encontraron grupos con estudiantes matriculados<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en consulta de estudiantes: " . $e->getMessage() . "<br>";
}

// 5. Probar el controlador
echo "<h2>5. Probando controlador de asistencia...</h2>";
try {
    require_once "controladores/asistencia.controlador.php";
    require_once "modelos/asistencia.modelo.php";
    
    if ($grupo) {
        $_POST['grupo_id'] = $grupo['grupo_id'];
        
        echo "Llamando al controlador con grupo_id: " . $_POST['grupo_id'] . "<br>";
        
        ob_start();
        ControladorAsistencia::ctrObtenerEstudiantesGrupo();
        $resultado = ob_get_clean();
        
        echo "Respuesta del controlador: " . $resultado . "<br>";
        
        $datos = json_decode($resultado, true);
        if ($datos && !isset($datos['error'])) {
            echo "✅ Controlador funcionando correctamente. Estudiantes: " . count($datos) . "<br>";
        } else {
            echo "❌ Error en controlador: " . ($datos['error'] ?? 'Respuesta inválida') . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error probando controlador: " . $e->getMessage() . "<br>";
}

echo "<h2>Diagnóstico completado</h2>";
?>