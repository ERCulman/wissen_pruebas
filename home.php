<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Bienvenido a Wissen</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- Dependencias de la Plantilla -->
  <link rel="stylesheet" href="vistas/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="vistas/bower_components/font-awesome/css/font-awesome.min.css">
  
  <!-- Hoja de Estilos Personalizada -->
  <link rel="stylesheet" href="vistas/css/home.css?v=<?php echo time(); ?>">

</head>

<body>

<!-- Barra de Navegación -->
<nav class="navbar navbar-custom navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
        <a class="navbar-brand logo page-scroll" href="#page-top">Wissen System</a>
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-main">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-bars"></i>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="navbar-collapse-main">
      <ul class="nav navbar-nav navbar-right">
        <li><a class="page-scroll" href="#quienes-somos">Sobre Wissen</a></li>
          <li><a class="page-scroll" href="#impacto">Nuestro Propósito</a></li>
        <li><a class="page-scroll" href="#servicios">Servicios</a></li>
        <li><a class="page-scroll" href="#mision-vision">Misión y Visión</a></li>
            <li style="display:flex; align-items:center;"><a href="login" class="btn btn-ingresar">Ingresar</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Sección Hero -->
<section id="page-top" class="hero">
  <div class="container">
    <h1>W i s s e n</h1>
      <p>Bienvenidos a la <br> transformación de la <br>GESTIÓN EDUCATIVA.</p>
  </div>
</section>

<!-- Sección Sobre Wissen -->
<section id="quienes-somos" class="section">
  <div class="container">
    <div class="section-title">
      <h2>Sobre Wissen</h2>
      <p>Conoce nuestro compromiso con la educación.</p>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="content-box">
          <h3><i class="fa fa-building-o"></i> Nuestra Esencia</h3>
          <p>Wissen nace como una solución tecnológica concebida para transformar la gestión y la comunicación de los procesos educativos. Nuestro equipo combina experiencia en desarrollo, gestión educativa y diseño de producto para ofrecer una plataforma única, eficiente y segura dirigida a directivos, docentes, estudiantes, padres de familia y autoridades.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sección de Estadísticas -->
<section id="stats" class="stats-section">
  <div class="container">
    <div class="row">
      <div class="col-md-2 col-md-offset-1 col-sm-4 col-xs-6">
        <div class="stat-box">
          <i class="fa fa-building icon-stat"></i>
          <span id="total-instituciones" class="count" data-count="0">0</span>
          <p>Instituciones</p>
        </div>
      </div>
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="stat-box">
          <i class="fa fa-university icon-stat"></i>
          <span id="total-sedes" class="count" data-count="0">0</span>
          <p>Sedes</p>
        </div>
      </div>
      <div class="col-md-2 col-sm-4 col-xs-6">
        <div class="stat-box">
          <i class="fa fa-graduation-cap icon-stat"></i>
          <span id="total-docentes" class="count" data-count="0">0</span>
          <p>Docentes</p>
        </div>
      </div>
      <div class="col-md-2 col-sm-6 col-xs-6">
        <div class="stat-box">
          <i class="fa fa-users icon-stat"></i>
          <span id="total-estudiantes" class="count" data-count="0">0</span>
          <p>Estudiantes</p>
        </div>
      </div>
      <div class="col-md-2 col-sm-6 col-xs-12">
        <div class="stat-box">
          <i class="fa fa-user-plus icon-stat"></i>
          <span id="total-acudientes" class="count" data-count="0">0</span>
          <p>Acudientes</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sección Nuestro Propósito -->
<section id="impacto" class="section">
  <div class="container">
    <div class="section-title">
      <h2>Nuestro Propósito</h2>
      <p>Reducimos la carga operativa y potenciamos la participación de toda la comunidad.</p>
    </div>
      <div class="row equal-height">
      <div class="col-md-6">
        <div class="content-box">
          <h3><i class="fa fa-lightbulb-o"></i> Problemas que Resolvemos</h3>
          <p>Wissen enfrenta la fragmentación de sistemas y la prevalencia de registros manuales, que generan reprocesos, errores y baja visibilidad institucional. Al centralizar la información, la plataforma mejora la comunicación con las familias, agiliza la operación diaria y entrega datos confiables para la toma de decisiones.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="content-box">
          <h3><i class="fa fa-users"></i> Impacto en los Usuarios</h3>
          <ul>
            <li><b>Docentes:</b> menor carga administrativa y mayor foco pedagógico.</li>
            <li><b>Directivos:</b> visión ejecutiva y soporte para rendición de cuentas.</li>
            <li><b>Padres y acudientes:</b> acceso oportuno a la información.</li>
            <li><b>Estudiantes:</b> autonomía para consultar su historial académico.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sección Servicios y Características -->
<section id="servicios" class="section">
  <div class="container">
    <div class="section-title">
      <h2>Una Solución Integral</h2>
      <p>Desde la matrícula hasta los reportes oficiales, todo en un solo lugar.</p>
    </div>
      <div class="row equal-height">
      <div class="col-md-4">
        <div class="feature-card">
          <div class="icon"><i class="fa fa-cogs"></i></div>
          <h4>Servicios Principales</h4>
          <p>Plataforma en la nube que articula matrícula, horarios, seguimiento académico, gestión disciplinaria, administración de personal y reportes oficiales.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div class="icon"><i class="fa fa-check-square-o"></i></div>
          <h4>Características Clave</h4>
          <p>Arquitectura modular para configurar, automatizar y gestionar todos los aspectos de la vida escolar, garantizando trazabilidad y facilitando auditorías.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <div class="icon"><i class="fa fa-shield"></i></div>
          <h4>Ventajas Competitivas</h4>
          <p>Aceleramos la operación, minimizamos errores y cumplimos con la normativa colombiana. Una solución escalable, accesible y segura.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sección Misión y Visión -->
<section id="mision-vision" class="section">
  <div class="container">
    <div class="section-title">
      <h2>Misión y Visión</h2>
      <p>Construyendo el futuro de la educación con compromiso y excelencia.</p>
    </div>
      <div class="row equal-height">
      <div class="col-md-6">
        <div class="content-box">
          <h3><i class="fa fa-bullseye"></i> Nuestra Misión</h3>
          <p>Contribuir al mejoramiento sostenido de la educación mediante un sistema que centraliza, automatiza y optimiza procesos académicos y administrativos, fortalece la comunicación entre actores institucionales y habilita la toma de decisiones basada en datos confiables.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="content-box">
          <h3><i class="fa fa-eye"></i> Nuestra Visión</h3>
          <p>Para 2030, ser el ecosistema digital de referencia en gestión educativa en Latinoamérica, reconocido por su capacidad de integrar a toda la comunidad educativa, su aporte a la transparencia institucional y la mejora continua de la calidad educativa.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sección Transforma Tu Institución -->
<section id="transforma" class="section">
  <div class="container">
    <div class="section-title">
      <h2>Transforma Tu Institución con Wissen</h2>
      <p>Eleva la gestión educativa con una solución ágil, transparente y centrada en resultados.</p>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="content-box">
          <h3><i class="fa fa-briefcase"></i> Revoluciona la Gestión Educativa</h3>
          <p>Wissen es más que un sistema: es una propuesta de transformación digital que permite a las instituciones evolucionar hacia una gestión transparente, ágil y centrada en resultados pedagógicos. Con Wissen, las decisiones se toman con datos, las familias participan con información y las instituciones optimizan sus recursos.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <h4>Wissen System</h4>
        <p>Transformando la educación, un proceso a la vez.</p>
      </div>
      <div class="col-md-4">
        <h4>Contacto</h4>
        <p><i class="fa fa-map-marker"></i> Bogotá, Colombia</p>
        <p><i class="fa fa-envelope"></i> <a href="mailto:contacto@wissen.com">contacto@wissen.com</a></p>
      </div>
      <div class="col-md-4">
        <h4>Síguenos</h4>
        <div class="social-icons">
          <a href="#"><i class="fa fa-x-official"></i></a>
          <a href="#"><i class="fa fa-facebook"></i></a>
          <a href="#"><i class="fa fa-linkedin"></i></a>
        </div>
      </div>
    </div>
    <div class="copyright">
      Copyright &copy; 2025 Wissen Systems. Todos los derechos reservados.
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="vistas/bower_components/jquery/dist/jquery.min.js"></script>
<script src="vistas/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="vistas/js/home.js"></script>

</body>
</html>