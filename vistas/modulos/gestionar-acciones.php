<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
  <div class="wrapper">
    <div class="content-wrapper">
      <section class="content-header">
        <h1>Gestionar Acciones del Sistema</h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
          <li class="active">Gestionar Acciones</li>
        </ol>
      </section>

      <section class="content">

        <!-- ACCIONES PRECARGADAS -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Acciones Precargadas del Sistema</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-success" id="btnInsertarSeleccionadas">
                <i class="fa fa-plus"></i> Insertar Seleccionadas
              </button>
              <button class="btn btn-info" id="btnSeleccionarTodas">
                <i class="fa fa-check-square-o"></i> Seleccionar Todas
              </button>
            </div>
          </div>

          <div class="box-body">
            <div class="alert alert-info">
              <strong>Instrucciones:</strong> Selecciona las acciones que deseas insertar en la base de datos. Puedes editar los datos antes de insertar.
            </div>

            <form id="formAccionesPrecargadas">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tablaAccionesPrecargadas">
                  <thead>
                    <tr>
                      <th style="width: 5%">
                        <input type="checkbox" id="selectAll">
                      </th>
                      <th style="width: 25%">Nombre Acción</th>
                      <th style="width: 20%">Módulo</th>
                      <th style="width: 50%">Descripción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $accionesPrecargadas = ControladorAcciones::ctrObtenerAccionesPrecargadas();
                    $moduloAnterior = '';
                    
                    foreach($accionesPrecargadas as $index => $accion):
                        if($moduloAnterior != $accion['modulo']):
                            if($moduloAnterior != ''):
                                echo '<tr class="separator"><td colspan="4" style="background-color: #f9f9f9; height: 10px;"></td></tr>';
                            endif;
                            $moduloAnterior = $accion['modulo'];
                        endif;
                    ?>
                    <tr<?php echo isset($accion['existe']) && $accion['existe'] ? ' class="disabled-row" style="background-color: #f9f9f9; opacity: 0.6;"' : ''; ?>>
                      <td>
                        <?php if(isset($accion['existe']) && $accion['existe']): ?>
                          <i class="fa fa-check text-success" title="Ya existe en la base de datos"></i>
                        <?php else: ?>
                          <input type="checkbox" class="accion-checkbox" value="<?php echo $index; ?>" checked>
                        <?php endif; ?>
                      </td>
                      <td>
                        <input type="text" class="form-control input-sm nombre-accion" 
                               value="<?php echo $accion['nombre_accion']; ?>" 
                               data-index="<?php echo $index; ?>"
                               <?php echo isset($accion['existe']) && $accion['existe'] ? 'readonly' : ''; ?>>
                      </td>
                      <td>
                        <input type="text" class="form-control input-sm modulo-accion" 
                               value="<?php echo $accion['modulo']; ?>" 
                               data-index="<?php echo $index; ?>"
                               <?php echo isset($accion['existe']) && $accion['existe'] ? 'readonly' : ''; ?>>
                      </td>
                      <td>
                        <input type="text" class="form-control input-sm descripcion-accion" 
                               value="<?php echo $accion['descripcion']; ?>" 
                               data-index="<?php echo $index; ?>"
                               <?php echo isset($accion['existe']) && $accion['existe'] ? 'readonly' : ''; ?>>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </form>
          </div>
        </div>

        <!-- ACCIONES EXISTENTES -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Acciones Existentes en Base de Datos</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarAccion">
                <i class="fa fa-plus"></i> Nueva Acción Manual
              </button>
            </div>
          </div>

          <div class="box-body">
            <table class="table table-bordered table-striped dt-responsive" id="tablaAcciones">
              <thead>
                <tr>
                  <th style="width: 5%">ID</th>
                  <th style="width: 25%">Nombre Acción</th>
                  <th style="width: 20%">Módulo</th>
                  <th style="width: 40%">Descripción</th>
                  <th style="width: 10%">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $acciones = ControladorAcciones::ctrMostrarAcciones(null, null);
                if($acciones):
                  foreach($acciones as $key => $value):
                ?>
                <tr>
                  <td><?php echo $value["id"]; ?></td>
                  <td><?php echo $value["nombre_accion"]; ?></td>
                  <td><?php echo $value["modulo"]; ?></td>
                  <td><?php echo $value["descripcion"]; ?></td>
                  <td>
                    <button class="btn btn-warning btn-sm btnEditarAccion" data-id="<?php echo $value["id"]; ?>">
                      <i class="fa fa-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btnEliminarAccion" data-id="<?php echo $value["id"]; ?>" data-nombre="<?php echo $value["nombre_accion"]; ?>">
                      <i class="fa fa-trash"></i>
                    </button>
                  </td>
                </tr>
                <?php 
                  endforeach;
                else:
                ?>
                <tr>
                  <td colspan="5" class="text-center">No hay acciones registradas</td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </section>
    </div>

    <!-- MODAL AGREGAR ACCIÓN -->
    <div id="modalAgregarAccion" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <form role="form" method="post">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Agregar Nueva Acción</h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Nombre de la Acción:</label>
                <input type="text" class="form-control" name="nuevoNombreAccion" placeholder="Ej: usuarios_crear" required>
              </div>
              <div class="form-group">
                <label>Módulo:</label>
                <input type="text" class="form-control" name="nuevoModulo" placeholder="Ej: Usuarios" required>
              </div>
              <div class="form-group">
                <label>Módulo Asociado (Ruta):</label>
                <input type="text" class="form-control" name="nuevoModuloAsociado" placeholder="Ej: usuarios (para protección automática)">
                <small class="text-muted">Opcional: Ruta del sistema para protección automática</small>
              </div>
              <div class="form-group">
                <label>Descripción:</label>
                <input type="text" class="form-control" name="nuevaDescripcion" placeholder="Ej: Crear nuevos usuarios" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            <?php
            $crearAccion = new ControladorAcciones();
            $crearAccion -> ctrCrearAccion();
            ?>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL EDITAR ACCIÓN -->
    <div id="modalEditarAccion" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <form role="form" method="post">
            <div class="modal-header" style="background: #3c8ebdff; color: white;">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Editar Acción</h4>
            </div>
            <div class="modal-body">
              <input type="hidden" name="idAccion" id="idAccion">
              <div class="form-group">
                <label>Nombre de la Acción:</label>
                <input type="text" class="form-control" name="editarNombreAccion" id="editarNombreAccion" required>
              </div>
              <div class="form-group">
                <label>Módulo:</label>
                <input type="text" class="form-control" name="editarModulo" id="editarModulo" required>
              </div>
              <div class="form-group">
                <label>Módulo Asociado (Ruta):</label>
                <input type="text" class="form-control" name="editarModuloAsociado" id="editarModuloAsociado" placeholder="Ej: usuarios">
                <small class="text-muted">Opcional: Ruta del sistema para protección automática</small>
              </div>
              <div class="form-group">
                <label>Descripción:</label>
                <input type="text" class="form-control" name="editarDescripcion" id="editarDescripcion" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
            <?php
            $editarAccion = new ControladorAcciones();
            $editarAccion -> ctrEditarAccion();
            ?>
          </form>
        </div>
      </div>
    </div>

  </div>
</body>

<script src="vistas/js/acciones.js"></script>

<?php
$insertarAcciones = new ControladorAcciones();
$insertarAcciones -> ctrInsertarAccionesPrecargadas();

$borrarAccion = new ControladorAcciones();
$borrarAccion -> ctrBorrarAccion();
?>