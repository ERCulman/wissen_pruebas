<?php
// test_insert.php - Prueba directa de inserción paso a paso

require_once "modelos/conexion.php";
require_once "modelos/oferta.modelo.php";

echo "<h1>Test Insert Paso a Paso</h1>";

try {
    $conexion = Conexion::conectar();

    // Verificar tablas existentes
    echo "<h2>1. Verificar datos existentes:</h2>";

    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM sede_jornada");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>📊 sede_jornada: " . $result['total'] . " registros</p>";

    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM oferta_academica");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>📊 oferta_academica: " . $result['total'] . " registros</p>";

    $stmt = $conexion->prepare("SELECT COUNT(*) as total FROM grupo");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>📊 grupo: " . $result['total'] . " registros</p>";

    // 1. Crear/obtener sede_jornada
    echo "<h2>2. Crear Sede Jornada:</h2>";
    $datosSedeJornada = array(
        "sede_id" => 1,
        "jornada_id" => 1,
        "anio_lectivo_id" => 1
    );

    $sedeJornadaId = ModeloOfertaEducativa::mdlVerificarCrearSedeJornada("sede_jornada", $datosSedeJornada);
    echo "<p>✅ Sede Jornada ID: $sedeJornadaId</p>";

    // Verificar que existe
    $stmt = $conexion->prepare("SELECT * FROM sede_jornada WHERE id = :id");
    $stmt->bindParam(":id", $sedeJornadaId, PDO::PARAM_INT);
    $stmt->execute();
    $sedeJornada = $stmt->fetch();

    if($sedeJornada) {
        echo "<p>✅ Sede Jornada verificada: sede_id=" . $sedeJornada['sede_id'] . ", jornada_id=" . $sedeJornada['jornada_id'] . "</p>";
    } else {
        echo "<p>❌ Error: Sede Jornada no encontrada</p>";
        exit;
    }

    // 2. Crear oferta educativa
    echo "<h2>3. Crear Oferta Educativa:</h2>";
    $datosOferta = array(
        "grado_id" => 2,  // Segundo
        "sede_jornada_id" => $sedeJornadaId,
        "anio_lectivo_id" => 1
    );

    echo "<p>📝 Datos a insertar:</p>";
    echo "<pre>" . print_r($datosOferta, true) . "</pre>";

    $ofertaId = ModeloOfertaEducativa::mdlIngresarOfertaEducativa("oferta_academica", $datosOferta);
    echo "<p>🔄 Resultado inserción: $ofertaId</p>";

    if($ofertaId && is_numeric($ofertaId)) {
        echo "<p>✅ Oferta Educativa creada con ID: $ofertaId</p>";

        // Verificar que realmente existe
        $stmt = $conexion->prepare("SELECT * FROM oferta_academica WHERE id = :id");
        $stmt->bindParam(":id", $ofertaId, PDO::PARAM_INT);
        $stmt->execute();
        $oferta = $stmt->fetch();

        if($oferta) {
            echo "<p>✅ Oferta verificada en BD:</p>";
            echo "<pre>" . print_r($oferta, true) . "</pre>";

            // 3. Crear grupo
            echo "<h2>4. Crear Grupo:</h2>";
            $datosGrupo = array(
                "oferta_educativa_id" => $ofertaId,
                "curso_id" => 1,  // Curso 001
                "nombre" => "Segundo 001 - 35 Cupos",
                "cupos" => 35
            );

            echo "<p>📝 Datos del grupo:</p>";
            echo "<pre>" . print_r($datosGrupo, true) . "</pre>";

            $resultadoGrupo = ModeloOfertaEducativa::mdlIngresarGrupo("grupo", $datosGrupo);
            echo "<p>🔄 Resultado grupo: $resultadoGrupo</p>";

            if($resultadoGrupo == "ok") {
                echo "<h2 style='color: green;'>🎉 ¡TODO FUNCIONA CORRECTAMENTE!</h2>";

                // Mostrar grupo creado
                $stmt = $conexion->prepare("SELECT * FROM grupo WHERE oferta_educativa_id = :id");
                $stmt->bindParam(":id", $ofertaId, PDO::PARAM_INT);
                $stmt->execute();
                $grupos = $stmt->fetchAll();

                echo "<p>✅ Grupos creados:</p>";
                foreach($grupos as $grupo) {
                    echo "<p>- " . $grupo['nombre'] . " (ID: " . $grupo['id'] . ")</p>";
                }

            } else {
                echo "<h2 style='color: red;'>❌ Error al crear grupo: $resultadoGrupo</h2>";
            }

        } else {
            echo "<p>❌ Error: Oferta no se encuentra en la BD después de insertar</p>";
        }

    } else {
        echo "<p>❌ Error al crear oferta educativa: $ofertaId</p>";
    }

} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}
?>