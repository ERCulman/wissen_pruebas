<?php
// debug_matricula.php - Para probar el envío del formulario

if($_POST) {
    echo "<h3>DATOS RECIBIDOS POR POST:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    echo "<h3>VERIFICACIONES:</h3>";
    echo "¿Existe sedeMatricula? " . (isset($_POST["sedeMatricula"]) ? "SÍ: " . $_POST["sedeMatricula"] : "NO") . "<br>";
    echo "¿Existe acudientes? " . (isset($_POST["acudientes"]) ? "SÍ: " . $_POST["acudientes"] : "NO") . "<br>";
    echo "¿Existe idUsuarioEstudiante? " . (isset($_POST["idUsuarioEstudiante"]) ? "SÍ: " . $_POST["idUsuarioEstudiante"] : "NO") . "<br>";

    if(isset($_POST["acudientes"]) && !empty($_POST["acudientes"])) {
        $acudientes = json_decode($_POST["acudientes"], true);
        echo "<h3>ACUDIENTES DECODIFICADOS:</h3>";
        echo "<pre>";
        print_r($acudientes);
        echo "</pre>";
    }
} else {
    echo "<h3>NO HAY DATOS POST - El formulario no se está enviando</h3>";
}
?>

<!--
INSTRUCCIONES:
1. Crea este archivo como debug_matricula.php
2. En matricula.php, cambia temporalmente la action del formulario:
   <form action="debug_matricula.php" method="post" ...>
3. Intenta registrar la matrícula
4. Verás exactamente qué datos están llegando
5. Luego restaura la action original
-->