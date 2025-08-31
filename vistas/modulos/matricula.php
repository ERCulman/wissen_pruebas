<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DEBUG: Verificar si llegan datos POST
if(!empty($_POST)) {
    error_log("DEBUG MATRICULA PRINCIPAL - POST recibido: " . print_r($_POST, true));

    // Mostrar mensaje temporal para verificar
    if(isset($_POST["btnRegistrarMatricula"])) {
        echo "<script>console.log('POST detectado en matricula.php');</script>";
    }
}
?>
<?php
require_once "controladores/matricula.controlador.php";
require_once "modelos/matricula.modelo.php";
require_once "controladores/usuarios.controlador.php";
require_once "modelos/usuarios.modelo.php";
?>
<!DOCTYPE html>
<html>

<!--<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sistema de Educacion Wissen</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="bower_components/datatables.net-bs/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/dataTables.responsive.min.js"></script>
    <script src="bower_components/datatables.net-bs/js/responsive.bootstrap.min.js"></script>
    <script src="plugins/sweetalert2/sweetalert2.all.js"></script>
</head> -->

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Matrícula</h1>
            <ol class="breadcrumb">
                <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
                <li class="active">Matrícula</li>
            </ol>
        </section>

        <!-- CONTENIDO PAGINA MATRÍCULA-->

        <section class="content">

            <!-- SECCIÓN BÚSQUEDA DE ESTUDIANTE -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-search"></i> Buscar Estudiante</h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Buscar por Número de Documento:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                    <input type="text" class="form-control input-lg" id="buscarDocumento" placeholder="Número de Documento">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-lg" id="btnBuscarDocumento" type="button">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Buscar por Nombres y Apellidos:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input type="text" class="form-control input-lg" id="buscarNombres" placeholder="Nombres y Apellidos">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-lg" id="btnBuscarNombres" type="button">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RESULTADO DE BÚSQUEDA -->
                    <div id="resultadoBusqueda" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-md-12">

                                <!-- El JS mostrará este DIV si el estudiante NO tiene matrícula activa -->
                                <div id="opcionMatricular" style="display: none;">
                                    <div class="alert alert-info">
                                        <h4><i class="fa fa-user"></i> Estudiante Encontrado:</h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td><strong>Tipo de Documento:</strong></td>
                                                <td id="tipoDocEncontrado"></td>
                                                <td><strong>No. Documento:</strong></td>
                                                <td id="numeroDocEncontrado"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nombres Completos:</strong></td>
                                                <td colspan="3" id="nombresEncontrados"></td>
                                            </tr>
                                        </table>
                                        <button class="btn btn-success btn-lg" id="btnMatricular" type="button">
                                            <i class="fa fa-graduation-cap"></i> Matricular Estudiante
                                        </button>
                                    </div>
                                </div>

                                <!-- El JS mostrará este DIV si el estudiante YA TIENE matrícula activa -->
                                <div id="estudianteYaMatriculado" style="display: none;">
                                    <div class="alert alert-danger">
                                        <h4><i class="fa fa-exclamation-circle"></i> Matrícula Existente</h4>
                                        <p id="mensajeMatriculaExistente"></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ESTUDIANTE NO ENCONTRADO -->
                    <div id="estudianteNoEncontrado" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <h4><i class="fa fa-exclamation-triangle"></i> Estudiante No Encontrado</h4>
                                    <p>El estudiante no se encuentra registrado en el sistema.</p>
                                    <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalRegistrarEstudiante" type="button">
                                        <i class="fa fa-user-plus"></i> Registrar Nuevo Estudiante
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLA DE MATRÍCULAS -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list"></i> Listado de Matrículas</h3>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-striped dt-responsive tablas" id="tablaMatricula">
                        <thead>
                        <tr>
                            <th style="width: 8%">No. Matrícula</th>
                            <th style="width: 15%">Estudiante</th>
                            <th style="width: 10%">Documento</th>
                            <th style="width: 12%">Jornada</th>
                            <th style="width: 10%">Grado</th>
                            <th style="width: 8%">Curso</th>
                            <th style="width: 12%">Fecha Matrícula</th>
                            <th style="width: 10%">Estado</th>
                            <th style="width: 15%">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $item = null;
                        $valor = null;

                        $matriculas = ControladorMatricula::ctrMostrarMatricula($item, $valor);

                        foreach ($matriculas as $key => $value) {

                            echo '<tr>
                        <td>'.$value["numero_matricula"].'</td>
                        <td>'.$value["nombres_estudiante"].'</td>
                        <td>'.$value["documento_estudiante"].'</td>
                        <td>'.$value["jornada"].'</td>
                        <td>'.$value["grado"].'</td>
                        <td>'.$value["curso"].'</td>
                        <td>'.$value["fecha_matricula"].'</td>';

                            if($value["estado_matricula"] == 'Activo'){
                                echo '<td><button class="btn btn-success btn-xs">'.$value["estado_matricula"].'</button></td>';
                            } else {
                                echo '<td><button class="btn btn-danger btn-xs">'.$value["estado_matricula"].'</button></td>';
                            }

                            echo '<td>
                                <button class="btn btn-info btnVerMatricula" 
                                data-id="'.$value["id"].'"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-warning btnEditarMatricula" 
                                data-id="'.$value["id"].'" ><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-danger btnEliminarMatricula" 
                                data-id="'.$value["id"].'"><i class="fa fa-trash"></i></button>
                          </td>
                     </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- =======================================
      MODAL REGISTRAR ESTUDIANTE
    =======================================-->

    <div id="modalRegistrarEstudiante" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role ="form" method="post" enctype="multipart/form-data" id="formRegistrarEstudiante">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-user-plus"></i> Registrar Nuevo Estudiante</h4>
                    </div>

                    <div class="modal-body">
                        <div class="box-body" id="camposFormulario">

                            <!-- NÚMERO DOCUMENTO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Número de Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" class="form-control input-lg" name="numeroDocumentoEstudiante" placeholder="Número de Documento" required>
                                    </div>
                                </div>
                            </div>

                            <!-- TIPO DOCUMENTO -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <select class="form-control input-lg" name="tipoDocumentoEstudiante" required>
                                            <option value="">Seleccione...</option>
                                            <option value="CC">CC - CEDULA CIUDADANIA</option>
                                            <option value="CE">CE - CEDULA EXTRANJERIA</option>
                                            <option value="TI">TI - TARJETA DE IDENTIDAD</option>
                                            <option value="RC">RC - REGISTRO CIVIL</option>
                                            <option value="PTE">PTE - PASAPORTE</option>
                                            <option value="TE">TE - TARJETA EXTRANJERIA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- NOMBRES Y APELLIDOS -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nombres:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombresEstudiante" placeholder="Nombres" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Apellidos:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control input-lg" name="apellidosEstudiante" placeholder="Apellidos" required>
                                    </div>
                                </div>
                            </div>

                            <!-- GÉNERO Y RH -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Género:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <select class="form-control input-lg" name="generoEstudiante" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Masculino">MASCULINO</option>
                                            <option value="Femenino">FEMENINO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Sangre:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tint"></i></span>
                                        <select class="form-control input-lg" name="rhEstudiante" required>
                                            <option value="">Seleccione...</option>
                                            <option value="A+">A+</option>
                                            <option value="AB+">AB+</option>
                                            <option value="B+">B+</option>
                                            <option value="O+">O+</option>
                                            <option value="A-">A-</option>
                                            <option value="AB-">AB-</option>
                                            <option value="B-">B-</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- FECHA NACIMIENTO Y EDAD -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Nacimiento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="date" class="form-control input-lg" name="fechaNacimientoEstudiante" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Edad:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="number" class="form-control input-lg" name="edadEstudiante" placeholder="Edad" required>
                                    </div>
                                </div>
                            </div>

                            <!-- TELÉFONO Y EMAIL -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Teléfono:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" class="form-control input-lg" name="telefonoEstudiante" placeholder="Teléfono" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Email:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="email" class="form-control input-lg" name="emailEstudiante" placeholder="Email" required>
                                    </div>
                                </div>
                            </div>

                            <!-- USUARIO Y CONTRASEÑA -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Usuario:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                        <input type="text" class="form-control input-lg" name="usuarioEstudiante" placeholder="Usuario" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Contraseña:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        <input type="password" class="form-control input-lg" name="passwordEstudiante" placeholder="Contraseña" required>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Estudiante</button>
                    </div>

                    <?php
                    $crearEstudiante = new ControladorMatricula();
                    $crearEstudiante -> ctrCrearEstudiante();
                    ?>

                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL MATRICULAR ESTUDIANTE
    =======================================-->
    <div id="modalMatricular" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role="form" method="post" enctype="multipart/form-data" id="formMatricular">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-graduation-cap"></i> Matricular Estudiante</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <input type="hidden" name="idUsuarioEstudiante" id="idUsuarioEstudiante">
                            <input type="hidden" name="acudientesData" class="acudientes-data-input">

                            <h4><i class="fa fa-graduation-cap"></i> Datos del Estudiante</h4>
                            <hr>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Sede:</label>
                                    <select class="form-control input-lg" name="sedeMatricula" id="sedeMatricula" required>
                                        <option value="">Seleccione una Sede...</option>
                                        <?php
                                        $sedes = ControladorMatricula::ctrObtenerSedesUsuario();
                                        if($sedes) {
                                            foreach ($sedes as $value) {
                                                echo '<option value="'.$value["id"].'">'.$value["nombre_sede"].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha de Ingreso:</label>
                                    <input type="date" class="form-control input-lg" name="fechaIngreso" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>¿Es estudiante nuevo?:</label>
                                    <select class="form-control input-lg" name="estudianteNuevo" required>
                                        <option value="">Seleccione...</option><option value="Si">Sí</option><option value="No">No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Estado Año Anterior:</label>
                                    <select class="form-control input-lg" name="estadoAnioAnterior" required>
                                        <option value="">Seleccione...</option><option value="repitente">Repitente</option><option value="promovido">Promovido</option><option value="retirado">Retirado</option>
                                    </select>
                                </div>
                            </div>

                            <h4><i class="fa fa-book"></i> Datos de Matrícula</h4>
                            <hr>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Grado:</label>
                                    <select class="form-control input-lg" name="gradoMatricula" id="gradoMatricula" required>
                                        <option value="">Primero seleccione una sede...</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Seleccione Grupo:</label>
                                    <select class="form-control input-lg" name="grupoMatricula" id="grupoMatricula" required>
                                        <option value="">Primero seleccione un grado...</option>
                                    </select>
                                </div>
                            </div>
                            <div id="infoGrupo" style="display: none;">
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Número de Matrícula:</label>
                                    <input type="text" class="form-control input-lg" name="numeroMatricula" placeholder="Número de Matrícula" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha de Matrícula:</label>
                                    <input type="date" class="form-control input-lg" name="fechaMatricula" required>
                                </div>
                            </div>

                            <h4><i class="fa fa-user-plus"></i> Datos del Acudiente</h4>
                            <hr>

                            <div class="row">
                                <div class="form-group col-md-8">
                                    <label>Buscar Acudiente por Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                        <input type="text" class="form-control input-lg buscar-acudiente-doc" placeholder="Número de Documento del Acudiente">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-info btn-block btn-lg btn-buscar-acudiente">Buscar</button>
                                </div>
                            </div>

                            <div class="acudiente-encontrado-container" style="display: none; margin-top:10px;"></div>
                            <div class="lista-acudientes-container well" style="min-height: 50px; padding: 10px; background-color: #f9f9f9; margin-top:15px;">
                            </div>

                            <div class="formulario-acudiente-container" style="display: none; background-color:#eef; padding:15px; border-radius:5px;">
                                <h5><i class="fa fa-user-plus"></i> Complete los datos del acudiente</h5>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Parentesco:</label>
                                        <select class="form-control input-lg parentesco-acudiente">
                                            <option value="">Seleccione...</option><option value="Padre">Padre</option><option value="Madre">Madre</option><option value="Tio">Tío</option><option value="Tia">Tía</option><option value="Abuelo">Abuelo</option><option value="Abuela">Abuela</option><option value="Hermano">Hermano</option><option value="Hermana">Hermana</option><option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>¿Autorizado para recoger?:</label>
                                        <select class="form-control input-lg autorizado-recoger-acudiente">
                                            <option value="">Seleccione...</option><option value="Si">Sí</option><option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Observaciones:</label>
                                        <input type="text" class="form-control input-lg observacion-acudiente" placeholder="Observaciones adicionales (opcional)">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-agregar-acudiente"><i class="fa fa-plus"></i> Agregar Acudiente a la Lista</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" name="btnRegistrarMatricula">Registrar Matrícula</button>
                    </div>
                    <?php
                    // Este bloque de PHP se ejecuta al enviar el formulario
                    $crearMatricula = new ControladorMatricula();
                    $crearMatricula -> ctrCrearMatricula();
                    ?>
                </form>
            </div>
        </div>
    </div>



    <!-- =======================================
      MODAL REGISTRAR ACUDIENTE
    =======================================-->

    <div id="modalRegistrarAcudiente" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form role="form" method="post" id="formRegistrarAcudiente">

                    <div class="modal-header" style="background: #00a65a; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-user-plus"></i> Registrar Nuevo Acudiente</h4>
                    </div>

                    <div class="modal-body">
                        <div class="box-body">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Número de Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                        <input type="text" class="form-control input-lg" name="numeroDocumentoAcudienteNuevo" id="numeroDocumentoAcudienteNuevo" readonly required>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Tipo de Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                                        <select class="form-control input-lg" name="tipoDocumentoAcudienteNuevo" required>
                                            <option value="">Seleccione...</option>
                                            <option value="CC">CC - CÉDULA DE CIUDADANÍA</option>
                                            <option value="CE">CE - CÉDULA DE EXTRANJERÍA</option>
                                            <option value="TI">TI - TARJETA DE IDENTIDAD</option>
                                            <option value="RC">RC - REGISTRO CIVIL</option>
                                            <option value="PTE">PTE - PASAPORTE</option>
                                            <option value="TE">TE - TARJETA DE EXTRANJERÍA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Nombres:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control input-lg" name="nombresAcudienteNuevo" placeholder="Nombres del acudiente" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Apellidos:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input type="text" class="form-control input-lg" name="apellidosAcudienteNuevo" placeholder="Apellidos del acudiente" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Género:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
                                        <select class="form-control input-lg" name="generoAcudienteNuevo" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Masculino">MASCULINO</option>
                                            <option value="Femenino">FEMENINO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tipo de Sangre:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tint"></i></span>
                                        <select class="form-control input-lg" name="rhAcudienteNuevo" required>
                                            <option value="">Seleccione...</option>
                                            <option value="A+">A+</option> <option value="A-">A-</option>
                                            <option value="B+">B+</option> <option value="B-">B-</option>
                                            <option value="AB+">AB+</option> <option value="AB-">AB-</option>
                                            <option value="O+">O+</option> <option value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Fecha de Nacimiento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="date" class="form-control input-lg" name="fechaNacimientoAcudienteNuevo" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Edad:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-birthday-cake"></i></span>
                                        <input type="number" class="form-control input-lg" name="edadAcudienteNuevo" placeholder="Edad" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Teléfono:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                        <input type="text" class="form-control input-lg" name="telefonoAcudienteNuevo" placeholder="Teléfono de contacto" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Email:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                        <input type="email" class="form-control input-lg" name="emailAcudienteNuevo" placeholder="Correo electrónico">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Usuario:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                        <input type="text" class="form-control input-lg" name="usuarioAcudienteNuevo" placeholder="Nombre de usuario" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Contraseña:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        <input type="password" class="form-control input-lg" name="passwordAcudienteNuevo" placeholder="Contraseña" required>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Registrar Acudiente</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
    MODAL VER MATRÍCULA COMPLETO
    =======================================-->

    <div id="modalVerMatricula" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formVerMatricula">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-search"></i> Ver Matrícula Completa</h4>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto;">

                        <!-- ENCABEZADO - INSTITUCIÓN Y SEDE -->
                        <div class="text-center" style="margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                            <h4 style="margin: 0; color: #2c3e50;" id="verInstitucionEncabezado"></h4>
                            <h5 style="margin: 5px 0 0 0; color: #7f8c8d;" id="verSedeEncabezado"></h5>
                        </div>

                        <!-- INFORMACIÓN DE MATRÍCULA -->
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h5><i class="fa fa-graduation-cap"></i> Información de Matrícula</h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Número de Matrícula:</strong>
                                        <p id="verNumeroMatricula"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Fecha de Matrícula:</strong>
                                        <p id="verFechaMatricula"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>¿Es Estudiante Nuevo?:</strong>
                                        <p id="verEstudianteNuevo"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Estado de Matrícula:</strong>
                                        <p id="verEstadoMatricula"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMACIÓN DEL ESTUDIANTE -->
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h5><i class="fa fa-user"></i> Información del Estudiante</h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong>Nombres Completos:</strong>
                                        <p id="verEstudianteNombres"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Sexo:</strong>
                                        <p id="verEstudianteSexo"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Tipo de Documento:</strong>
                                        <p id="verEstudianteTipoDoc"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Número de Documento:</strong>
                                        <p id="verEstudianteNumeroDoc"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>RH:</strong>
                                        <p id="verEstudianteRH"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Fecha de Nacimiento:</strong>
                                        <p id="verEstudianteFechaNac"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Edad:</strong>
                                        <p id="verEstudianteEdad"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Teléfono:</strong>
                                        <p id="verEstudianteTelefono"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Email:</strong>
                                        <p id="verEstudianteEmail"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Código Estudiante:</strong>
                                        <p id="verCodigoEstudiante"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Fecha de Ingreso:</strong>
                                        <p id="verFechaIngreso"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Estado Año Anterior:</strong>
                                        <p id="verEstadoAnioAnterior"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMACIÓN ACADÉMICA -->
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h5><i class="fa fa-book"></i> Información Académica</h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Jornada:</strong>
                                        <p id="verJornada"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Grado:</strong>
                                        <p id="verGrado"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Curso:</strong>
                                        <p id="verCurso"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Grupo:</strong>
                                        <p id="verGrupoMatricula"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Cupos del Grupo:</strong>
                                        <p id="verCuposGrupo"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMACIÓN DE ACUDIENTES -->
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h5><i class="fa fa-users"></i> Acudientes</h5>
                            </div>
                            <div class="panel-body">
                                <div id="verAcudientes">
                                    <!-- Los acudientes se cargarán aquí dinámicamente -->
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btnEditarMatricula" data-dismiss="modal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-danger" id="btnDescargarPDF">
                            <i class="fa fa-file-pdf-o"></i> Generar PDF
                        </button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- =======================================
      MODAL EDITAR MATRÍCULA COMPLETO
    =======================================-->
    <div id="modalEditarMatricula" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form role="form" method="post" enctype="multipart/form-data" id="formEditarMatricula">
                    <div class="modal-header" style="background: #3c8ebdff; color: white;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-pencil"></i> Editar Matrícula</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <input type="hidden" name="idMatricula" id="idMatricula">
                            <input type="hidden" name="acudientesData" class="acudientes-data-input">

                            <div class="alert alert-info" style="margin-bottom: 20px;">
                                <h4 style="margin-top: 0;"><i class="fa fa-user"></i> Estudiante</h4>
                                <div class="row">
                                    <div class="col-md-6"><strong>Nombre:</strong><p id="editarEstudianteNombre" style="margin: 0;"></p></div>
                                    <div class="col-md-6"><strong>Documento:</strong><p id="editarEstudianteNumeroDoc" style="margin: 0;"></p></div>
                                </div>
                            </div>

                            <h4><i class="fa fa-graduation-cap"></i> Datos del Estudiante</h4>
                            <hr>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Sede:</label>
                                    <select class="form-control input-lg" name="editarSedeMatricula" id="editarSedeMatricula" required>
                                        <option value="">Seleccione una Sede...</option>
                                        <?php
                                        $sedes = ControladorMatricula::ctrObtenerSedesUsuario();
                                        if($sedes) {
                                            foreach ($sedes as $value) {
                                                echo '<option value="'.$value["id"].'">'.$value["nombre_sede"].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha de Ingreso:</label>
                                    <input type="date" class="form-control input-lg" name="editarFechaIngreso" id="editarFechaIngreso" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>¿Es estudiante nuevo?:</label>
                                    <select class="form-control input-lg" name="editarEstudianteNuevo" id="editarEstudianteNuevo" required>
                                        <option value="">Seleccione...</option><option value="Si">Sí</option><option value="No">No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Estado Año Anterior:</label>
                                    <select class="form-control input-lg" name="editarEstadoAnioAnterior" id="editarEstadoAnioAnterior" required>
                                        <option value="">Seleccione...</option><option value="repitente">Repitente</option><option value="promovido">Promovido</option><option value="retirado">Retirado</option>
                                    </select>
                                </div>
                            </div>

                            <h4><i class="fa fa-book"></i> Datos de Matrícula</h4>
                            <hr>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Grado:</label>
                                    <select class="form-control input-lg" name="editarGradoMatricula" id="editarGradoMatricula" required>
                                        <option value="">Primero seleccione una sede...</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Seleccione Grupo:</label>
                                    <select class="form-control input-lg" name="editarGrupoMatricula" id="editarGrupoMatricula" required>
                                        <option value="">Primero seleccione un grado...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Número de Matrícula:</label>
                                    <input type="text" class="form-control input-lg" name="editarNumeroMatricula" id="editarNumeroMatricula" placeholder="Número de Matrícula" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha de Matrícula:</label>
                                    <input type="date" class="form-control input-lg" name="editarFechaMatricula" id="editarFechaMatricula" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label><span class="text-danger">*</span> Estado de Matrícula:</label>
                                    <select class="form-control input-lg" name="editarEstadoMatricula" id="editarEstadoMatricula" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                        <option value="Retirado">Retirado</option>
                                    </select>
                                </div>
                            </div>

                            <h4><i class="fa fa-user-plus"></i> Datos del Acudiente</h4>
                            <hr>

                            <div class="row">
                                <div class="form-group col-md-8">
                                    <label>Buscar Acudiente por Documento:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                        <input type="text" class="form-control input-lg buscar-acudiente-doc" placeholder="Número de Documento del Acudiente">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-info btn-block btn-lg btn-buscar-acudiente">Buscar</button>
                                </div>
                            </div>

                            <div class="acudiente-encontrado-container" style="display: none; margin-top:10px;"></div>
                            <label>Acudientes Asignados:</label>
                            <div class="lista-acudientes-container well" style="min-height: 50px; padding: 10px; background-color: #f9f9f9; margin-top:15px;">
                            </div>

                            <div class="formulario-acudiente-container" style="display: none; background-color:#eef; padding:15px; border-radius:5px;">
                                <h5><i class="fa fa-user-plus"></i> Complete los datos del acudiente</h5>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Parentesco:</label>
                                        <select class="form-control input-lg parentesco-acudiente">
                                            <option value="">Seleccione...</option><option value="Padre">Padre</option><option value="Madre">Madre</option><option value="Tio">Tío</option><option value="Tia">Tía</option><option value="Abuelo">Abuelo</option><option value="Abuela">Abuela</option><option value="Hermano">Hermano</option><option value="Hermana">Hermana</option><option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>¿Autorizado para recoger?:</label>
                                        <select class="form-control input-lg autorizado-recoger-acudiente">
                                            <option value="">Seleccione...</option><option value="Si">Sí</option><option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Observaciones:</label>
                                        <input type="text" class="form-control input-lg observacion-acudiente" placeholder="Observaciones adicionales (opcional)">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-agregar-acudiente"><i class="fa fa-plus"></i> Agregar Acudiente a la Lista</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                    <?php
                    // Este bloque de PHP se ejecuta al enviar el formulario de edición
                    $editarMatricula = new ControladorMatricula();
                    $editarMatricula -> ctrEditarMatricula();
                    ?>
                </form>
            </div>
        </div>
    </div>


    <script src="/wissen/vistas/js/matricula.js"></script>

    <?php

    $borrarMatricula = new ControladorMatricula();
    $borrarMatricula -> ctrBorrarMatricula();

    ?>
</body>
</html>