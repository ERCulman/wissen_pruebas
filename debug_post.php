<?php
// debug_post.php - Para ver qué datos se envían en el formulario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h1>Datos POST recibidos:</h1>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    echo "<h2>Análisis:</h2>";

    // Verificar campos básicos
    $campos_basicos = ['anioLectivo', 'sedeOferta', 'jornadaOferta', 'nivelEducativo'];
    foreach($campos_basicos as $campo) {
        if(isset($_POST[$campo]) && !empty($_POST[$campo])) {
            echo "<p style='color: green;'>✅ $campo: " . $_POST[$campo] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ $campo: No presente o vacío</p>";
        }
    }

    // Verificar grados
    if(isset($_POST['grados']) && is_array($_POST['grados'])) {
        echo "<p style='color: green;'>✅ Grados seleccionados: " . implode(', ', $_POST['grados']) . "</p>";

        // Verificar cursos y cupos para cada grado
        foreach($_POST['grados'] as $gradoId) {
            echo "<h3>Grado ID: $gradoId</h3>";

            $cursosKey = "cursos_" . $gradoId;
            if(isset($_POST[$cursosKey]) && is_array($_POST[$cursosKey])) {
                echo "<p style='color: green;'>✅ Cursos para grado $gradoId: " . implode(', ', $_POST[$cursosKey]) . "</p>";

                foreach($_POST[$cursosKey] as $cursoId) {
                    $cuposKey = "cupos_" . $gradoId . "_" . $cursoId;
                    if(isset($_POST[$cuposKey])) {
                        echo "<p style='color: green;'>✅ Cupos grado $gradoId curso $cursoId: " . $_POST[$cuposKey] . "</p>";
                    } else {
                        echo "<p style='color: red;'>❌ Cupos grado $gradoId curso $cursoId: No encontrado</p>";
                    }
                }
            } else {
                echo "<p style='color: red;'>❌ No se encontraron cursos para grado $gradoId (clave buscada: $cursosKey)</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ No hay grados seleccionados</p>";
    }

} else {
    echo "<h1>Formulario de Prueba</h1>";
    echo "<p>Este formulario simula el envío de datos para debug:</p>";
    ?>

    <form method="POST" action="">
        <h3>Datos básicos:</h3>
        <p>Año Lectivo: <input type="text" name="anioLectivo" value="1"></p>
        <p>Sede: <input type="text" name="sedeOferta" value="1"></p>
        <p>Jornada: <input type="text" name="jornadaOferta" value="1"></p>
        <p>Nivel: <input type="text" name="nivelEducativo" value="1"></p>

        <h3>Grados:</h3>
        <p><input type="checkbox" name="grados[]" value="2" checked> Grado 2 (Segundo)</p>
        <p><input type="checkbox" name="grados[]" value="3" checked> Grado 3 (Tercero)</p>

        <h3>Cursos para Grado 2:</h3>
        <p><input type="checkbox" name="cursos_2[]" value="1" checked> Curso 1</p>
        <p>Cupos: <input type="number" name="cupos_2_1" value="35"></p>

        <p><input type="checkbox" name="cursos_2[]" value="4" checked> Curso 4</p>
        <p>Cupos: <input type="number" name="cupos_2_4" value="30"></p>

        <h3>Cursos para Grado 3:</h3>
        <p><input type="checkbox" name="cursos_3[]" value="1" checked> Curso 1</p>
        <p>Cupos: <input type="number" name="cupos_3_1" value="25"></p>

        <p><input type="submit" value="Enviar Prueba"></p>
    </form>

    <?php
}
?>