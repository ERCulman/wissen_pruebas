<?php

  session_start(); ## Dispara el inicio de sesion

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

   <!-- DataTables -->
  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="vistas/bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">

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

        if(isset($_GET["ruta"])){
            if($_GET["ruta"] == "inicio" ||
               $_GET["ruta"] == "usuarios" ||
               $_GET["ruta"] == "perfil-laboral" ||
               $_GET["ruta"] == "institucion" ||
               $_GET["ruta"] == "sedes" ||
               $_GET["ruta"] == "niveleducativo" ||
               $_GET["ruta"] == "jornadas" ||
               $_GET["ruta"] == "grados" ||
               $_GET["ruta"] == "cursos" ||
               $_GET["ruta"] == "oferta" ||
               $_GET["ruta"] == "periodos" ||
               $_GET["ruta"] == "estructura-curricular" ||
               $_GET["ruta"] == "matricula" ||
               $_GET["ruta"] == "estudiantes" ||
               $_GET["ruta"] == "acudientes" ||
               $_GET["ruta"] == "pension-escolar" ||
               $_GET["ruta"] == "asistencia" ||
               $_GET["ruta"] == "calificaciones" ||
               $_GET["ruta"] == "observaciones-academicas" ||
               $_GET["ruta"] == "observaciones-disciplinarias" ||
               $_GET["ruta"] == "horarios" ||
               $_GET["ruta"] == "salir"){
                include "modulos/".$_GET["ruta"].".php";
            } else {
                include "modulos/404.php";
            }
        } else {
            include "modulos/inicio.php";
        }

        include "modulos/footer.php";
        echo '</div>';
    } else {
        // Mostrar login tanto para ruta /login como para acceso sin sesión
        include "modulos/login.php";
    }

  ?>
    
  
<!-- ./wrapper -->

<script src="vistas/js/plantilla.js"></script>
<script src="vistas/js/usuarios.js"></script>
<script src="vistas/js/periodo.js"></script>

<script src="vistas/js/estructura-curricular.js"></script>
<!--  <script src="vistas/js/contraseña.js"></script> -->
</body>
</html>