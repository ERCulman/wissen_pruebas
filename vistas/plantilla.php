<?php

  session_start(); ## Dispara el inicio de sesion
  require_once "controladores/auth.controlador.php";
  require_once "modelos/auth.modelo.php";

?>

<!DOCTYPE html>
<html>
<head>


  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>Sistema de Educacion Wissen</title> <!--TITULO DEL SISTEMA-->

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<!-- =======================================
  PLUGINS DE CSS BOOTSTRAP
=======================================-->

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="vistas/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="vistas/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="vistas/dist/css/AdminLTE.css">

  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="vistas/dist/css/skins/_all-skins.min.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- Sistema de Validación -->
  <link rel="stylesheet" href="vistas/css/validaciones.css">



   <!-- DataTables -->
  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">

  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<!-- =======================================
  PLUGINS DE JAVASCRIPT
=======================================-->

  <!-- jQuery 3 -->
  <script src="vistas/bower_components/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="vistas/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="vistas/bower_components/fastclick/lib/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="vistas/dist/js/adminlte.min.js"></script>

  <!-- DataTables -->
  <script src="vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
  <script src="vistas/bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>

  <!-- bootstrap datepicker -->
  <script src="vistas/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="vistas/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>

  <!-- SweetAler2 -->

<script src="vistas/plugins/sweetalert2/sweetalert2.all.js"></script>
  <!-- By default SweetAlert2 doesn't support IE. To enable IE 11 support, include Promise polyfill:
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>-->

</head>

<!-- =======================================
  CUERPO DE LA PLANTILLA
=======================================-->

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">


<?php
if(isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok"){
    echo '<div class="wrapper">';
    include "modulos/cabezote.php";
    include "modulos/menu.php";

    // =============================================
    // NUEVA LÓGICA DE ENRUTAMIENTO BASADA EN PERMISOS
    // =============================================

    // 1. Cargar el mapa de rutas y el servicio de autorización
    $rutasMapa = include "routes-vistas.php"; // Asegúrate de que la ruta sea correcta
    $auth = ServicioAutorizacion::getInstance();



    if(isset($_GET["ruta"])){
        $rutaActual = $_GET["ruta"];

        // 2. Verificar si la ruta existe en nuestro mapa
        if(array_key_exists($rutaActual, $rutasMapa)){

            // 3. Obtener el permiso necesario para esta ruta
            $permisoRequerido = $rutasMapa[$rutaActual]['permiso'];

            // 4. Verificar el permiso
            // El acceso se concede si la ruta es pública o si el usuario tiene el permiso requerido.
            if($permisoRequerido === 'publico' || $auth->puede($permisoRequerido)){
                include "modulos/".$rutaActual.".php";
            } else {
                // Si no tiene el permiso, se muestra la página de acceso denegado.
                error_log("Acceso DENEGADO para usuario " . $_SESSION["id_usuario"] . " a la ruta: " . $rutaActual);
                include "modulos/acceso-denegado.php";
            }

        } else {
            // Si la ruta no está en el mapa, es un 404.
            include "modulos/404.php";
        }
    } else {
        // Si no se especifica ruta, cargar el inicio.
        include "modulos/inicio.php";
    }

    include "modulos/footer.php";
    echo '</div>';

} else {
    // Mostrar login si no hay sesión activa.
    include "modulos/login.php";
}
?>

<script src="vistas/js/plantilla.js"></script>
<?php
if(isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok"){
    // Reutilizamos la instancia del servicio de autorización que ya cargamos arriba.
    $infoPermisos = $auth->debugInfo();
    $permisosUsuario = $infoPermisos['listaPermisos'];

    // Convertimos el array de permisos de PHP a un formato JSON para JavaScript.
    $permisosJson = json_encode($permisosUsuario);
}
?>

<script type="module">
    // Importamos nuestro nuevo módulo de control de vistas.
    import PermisosVista from './vistas/js/permisos-vista.js';

    // Obtenemos la lista de permisos inyectada desde PHP.
    const permisosUsuario = <?php echo $permisosJson ?? '[]'; ?>;

    // Inicializamos el sistema de permisos de la vista.
    PermisosVista.init(permisosUsuario);
</script>


<!-- ./wrapper -->

<script src="vistas/js/plantilla.js"></script>
<script  type="module" src="vistas/js/usuarios.js"></script>
<script src="vistas/js/periodo.js"></script>
<script src="vistas/js/asignar-roles.js"></script>
<script src="vistas/js/validaciones-permisos.js"></script>


<script type="module" src="vistas/js/validaciones/Inicializador.js"></script>

<script src="vistas/js/estructura-curricular.js"></script>
<!--  <script src="vistas/js/contraseña.js"></script> -->
</body>
</html>