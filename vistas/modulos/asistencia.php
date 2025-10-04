<?php
require_once "controladores/asistencia.controlador.php";
require_once "modelos/asistencia.modelo.php";

$periodos = ControladorAsistencia::ctrObtenerPeriodos();

// Obtener cuerpo docente del usuario actual
$cuerpoDocenteId = null;
if (isset($_SESSION['id_usuario'])) {
    $stmt = Conexion::conectar()->prepare(
        "SELECT cd.id FROM cuerpo_docente cd 
         INNER JOIN roles_institucionales ri ON cd.rol_institucional_id = ri.id 
         WHERE ri.usuario_id = ? AND ri.estado = 'Activo'"
    );
    $stmt->execute([$_SESSION['id_usuario']]);
    $resultado = $stmt->fetch();
    $cuerpoDocenteId = $resultado ? $resultado['id'] : null;
}
?>

<!-- PAGINA ASISTENCIA -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Asistencia de Clase</h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Seguimiento Académico</li>
      <li class="active">Asistencia</li>
    </ol>
  </section>

  <section class="content">
    <!-- SELECCIÓN DE CONTEXTO -->
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Configuración de Clase</h3>
      </div>
      <div class="box-body">
        <div class="row">
          <!-- COLUMNA IZQUIERDA 25% - PERÍODOS -->
          <div class="col-md-3">
            <div class="form-group">
              <label>Período Académico:</label>
              <?php foreach($periodos as $index => $periodo): ?>
              <div class="radio">
                <label>
                  <input type="radio" name="periodo" value="<?php echo $periodo['id']; ?>" class="periodo-radio">
                  <?php echo $periodo['nombre']; ?>
                </label>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          
          <!-- COLUMNA DERECHA 75% -->
          <div class="col-md-9">
            <!-- PRIMERA FILA: GRADO, ASIGNATURA, FECHA -->
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Grado y Grupo:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-users"></i></span>
                    <select class="form-control" id="grado-grupo-select">
                      <option value="">Seleccione grado y grupo...</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="form-group">
                  <label>Asignatura:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-book"></i></span>
                    <select class="form-control" id="asignatura-select">
                      <option value="">Seleccione asignatura...</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="form-group">
                  <label>Fecha de Clase:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="date" id="fecha-clase" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                  </div>
                </div>
              </div>
            </div>
            
            <!-- SEGUNDA FILA: HORAS, RETRASO Y BOTÓN -->
            <div class="row" id="segunda-fila" style="display: none;">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Hora Inicio:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    <input type="time" id="hora-inicio" class="form-control" value="07:00">
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  <label>Hora Fin:</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    <input type="time" id="hora-fin" class="form-control" value="08:00">
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  <label>Retraso Permitido (min):</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-hourglass-half"></i></span>
                    <input type="number" id="retraso-permitido" class="form-control" value="15" min="1" max="120">
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="cargar-estudiantes" class="btn btn-primary btn-block">
                    <i class="fa fa-users"></i> Cargar Estudiantes
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>



    <!-- REGISTRO DE ASISTENCIA -->
    <div class="box" id="registro-asistencia" style="display: none;">
      <div class="box-header with-border">
        <h3 class="box-title">Registro de Asistencia</h3>
        <div class="box-tools pull-right">
          <button type="button" id="marcar-todos-ausentes" class="btn" style="background-color: #f56565; color: white; border-color: #f56565;">
            <i class="fa fa-times-circle"></i> Marcar Todos como Ausentes
          </button>
          <button type="button" id="guardar-asistencia" class="btn btn-primary" style="margin-left: 10px;">
            <i class="fa fa-save"></i> Guardar Asistencia
          </button>
        </div>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped dt-responsive tablas" id="tabla-asistencia" style="font-size: 1.3em;">
            <thead>
              <tr>
                <th style="width: 5%; vertical-align: middle; text-align: left;">No.</th>
                <th style="width: 35%; vertical-align: middle; text-align: left;">Estudiante</th>
                <th style="width: 10%; vertical-align: middle; text-align: left;">Documento</th>
                <th style="width: 20%; vertical-align: middle; text-align: center;">Estado Asistencia</th>
                <th style="width: 10%; vertical-align: middle; text-align: left;">Estado</th>
                <th style="width: 13%; vertical-align: middle; text-align: left;">Justificación</th>
                <th style="width: 7%; vertical-align: middle; text-align: left;">Acciones</th>
              </tr>
            </thead>
            <tbody id="lista-estudiantes">
              <!-- Se carga dinámicamente -->
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </section>
</div>

<!-- MODAL GESTIONAR JUSTIFICACIÓN -->
<div id="modalGestionarJustificacion" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: #3c8ebdff; color: white;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-edit"></i> Gestionar Justificación</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <h4>Estudiante: <span id="modalJustificacionEstudiante"></span></h4>
            <input type="hidden" id="modalJustificacionMatricula">
          </div>
        </div>
        
        <div class="row" style="margin-top: 15px;">
          <div class="col-md-12">
            <div class="form-group">
              <label>Justificación del Estudiante:</label>
              <textarea class="form-control" id="justificacionTexto" rows="3" placeholder="Ingrese la justificación..."></textarea>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Estado de la Justificación:</label>
              <select class="form-control" id="estadoJustificacion">
                <option value="Pendiente">Pendiente</option>
                <option value="Aceptada">Aceptada</option>
                <option value="Rechazada">Rechazada</option>
              </select>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label>Comentario del Docente:</label>
              <textarea class="form-control" id="comentarioDocente" rows="3" placeholder="Comentario adicional del docente..."></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarJustificacion()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="vistas/css/asistencia-checkboxes.css">

<style>
/* Estilos específicos para tabla de asistencia */
#tabla-asistencia td {
  vertical-align: middle !important;
  text-align: left;
  padding: 6px 8px !important;
  line-height: 1.2;
}

#tabla-asistencia th {
  padding: 8px !important;
  line-height: 1.2;
}

/* Solo Estado Asistencia centrado */
#tabla-asistencia td:nth-child(4) {
  text-align: center;
}

/* Permitir que AdminLTE maneje los colores de la tabla */
</style>

<script>
// Variable global para el ID del cuerpo docente
window.cuerpoDocenteIdGlobal = <?php echo $cuerpoDocenteId ? $cuerpoDocenteId : 'null'; ?>;
console.log('Cuerpo Docente ID:', window.cuerpoDocenteIdGlobal);
<?php if (!$cuerpoDocenteId): ?>
console.error('No se encontró cuerpo docente para el usuario:', <?php echo $_SESSION['id_usuario'] ?? 'null'; ?>);
<?php endif; ?>
</script>