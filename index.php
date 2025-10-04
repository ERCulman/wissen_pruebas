<?php

// Cargar autoloader y middleware
require_once "autoload.php";

require_once "controladores/plantilla.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/perfil-laboral.controlador.php";
require_once "controladores/institucion.controlador.php";
require_once "controladores/sedes.controlador.php";
require_once "controladores/jornadas.controlador.php";
require_once "controladores/niveleducativo.controlador.php";
require_once "controladores/grados.controlador.php";
require_once "controladores/cursos.controlador.php";
require_once "controladores/oferta.controlador.php";
require_once "controladores/periodos.controlador.php";
require_once "controladores/estructura-curricular.controlador.php";
require_once "controladores/acciones.controlador.php";
require_once "controladores/roles.controlador.php";
require_once "controladores/auth.controlador.php";
require_once "controladores/asignacion-docente-asignaturas.controlador.php";
require_once "controladores/asignacion-academica.controlador.php";
require_once "controladores/asistencia.controlador.php";


require_once "modelos/usuarios.modelo.php";
require_once "modelos/perfil-laboral.modelo.php";
require_once "modelos/institucion.modelo.php";
require_once "modelos/sedes.modelo.php";
require_once "modelos/niveleducativo.modelo.php";
require_once "modelos/jornadas.modelo.php";
require_once "modelos/grados.modelo.php";
require_once "modelos/cursos.modelo.php";
require_once "modelos/oferta.modelo.php";
require_once "modelos/periodos.modelo.php";
require_once "modelos/estructura-curricular.modelo.php";
require_once "modelos/acciones.modelo.php";
require_once "modelos/roles.modelo.php";
require_once "modelos/auth.modelo.php";
require_once "modelos/asignacion-docente-asignaturas.modelo.php";
require_once "modelos/asignacion-academica.modelo.php";
require_once "modelos/asistencia.modelo.php";



$plantilla = new ControladorPlantilla(); /*objeto plantilla instanciada con la clase controlador plantilla que viene de la carpeta controladores*/
$plantilla -> ctrPlantilla(); /* metodo de la clase controlador plantilla, carpeta controladores */