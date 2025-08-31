<?php
// Activar reporte de errores para ver cualquier problema
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- RUTAS CORREGIDAS ---
// Desde 'ajax/', subimos un nivel a la raíz del proyecto para encontrar estas carpetas.
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../../controladores/matricula.controlador.php";
require_once __DIR__ . "/../../modelos/matricula.modelo.php";

use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET["id"])) {

    $idMatricula = $_GET["id"];
    $data = ControladorMatricula::ctrMostrarMatricula("id", $idMatricula);

    if (!$data) {
        die("Error: No se encontraron datos para la matrícula con ID: " . htmlspecialchars($idMatricula));
    }

    ob_start();
    // Esta ruta ya la tenías bien, la mantenemos.
    include __DIR__ . "/plantillas/plantilla-reporte-matricula.php";
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $nombreArchivo = "matricula-" . $data["estudiante_documento"] . ".pdf";
    // Lo dejamos en 'false' para que muestre el PDF en el navegador y sea fácil ver si funciona.
    // Cuando estés seguro de que funciona, puedes cambiarlo a 'true' para que se descargue directamente.
    $dompdf->stream($nombreArchivo, ["Attachment" => false]);

} else {
    echo "Error: ID de matrícula no proporcionado.";
}
?>