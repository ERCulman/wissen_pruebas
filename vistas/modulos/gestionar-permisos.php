<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
  <div class="wrapper">
    <div class="content-wrapper">
      <section class="content-header">
        <h1>Gestionar Permisos por Rol</h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
          <li class="active">Gestionar Permisos</li>
        </ol>
      </section>

      <section class="content">
        <style>
        @media (max-width: 768px) {
          .box-title { font-size: 14px !important; }
          .btn-xs { padding: 2px 5px; font-size: 10px; }
          .checkbox { margin: 3px 0 !important; }
          .checkbox label { font-size: 12px; }
          .checkbox small { font-size: 10px; }
          .col-lg-6 { margin-bottom: 10px; }
        }
        .checkbox label {
          padding-left: 0 !important;
          min-height: auto;
        }
        .box-body .checkbox:last-child {
          margin-bottom: 0;
        }
        </style>
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Matriz de Permisos: Roles vs Acciones</h3>
          </div>

          <div class="box-body">
            <form id="formPermisos" method="post">
              <div class="form-group">
                <label>Seleccionar Rol:</label>
                <select class="form-control" id="selectRol" name="rolId" required>
                  <option value="">Seleccione un rol</option>
                  <?php
                  $roles = ControladorRoles::ctrMostrarRoles(null, null);
                  foreach($roles as $rol):
                  ?>
                  <option value="<?php echo $rol["id_rol"]; ?>"><?php echo $rol["nombre_rol"]; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div id="matrizPermisos" style="display: none;">
                <div class="row">
                  <?php
                  $acciones = ControladorAcciones::ctrMostrarAcciones(null, null);
                  
                  if($acciones):
                    // Agrupar acciones por módulo
                    $accionesPorModulo = array();
                    foreach($acciones as $accion){
                      $accionesPorModulo[$accion['modulo']][] = $accion;
                    }
                    
                    // Mostrar cada módulo en una card
                    foreach($accionesPorModulo as $modulo => $accionesModulo):
                  ?>
                  <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="box box-primary">
                      <div class="box-header with-border">
                        <h4 class="box-title">
                          <i class="fa fa-check-square-o"></i> <?php echo $modulo; ?>
                        </h4>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-xs btn-success btn-modulo-all" data-modulo="<?php echo $modulo; ?>">
                            <i class="fa fa-check"></i> Todas
                          </button>
                          <button type="button" class="btn btn-xs btn-warning btn-modulo-none" data-modulo="<?php echo $modulo; ?>">
                            <i class="fa fa-times"></i> Ninguna
                          </button>
                        </div>
                      </div>
                      <div class="box-body" style="padding: 10px;">
                        <?php foreach($accionesModulo as $accion): ?>
                        <div class="checkbox" style="margin: 5px 0;">
                          <label style="font-weight: normal; display: flex; align-items: flex-start;">
                            <input type="checkbox" name="acciones[]" value="<?php echo $accion["id"]; ?>" class="accion-checkbox" data-modulo="<?php echo $modulo; ?>" style="margin-right: 8px; margin-top: 2px;">
                            <div style="flex: 1;">
                              <strong style="display: block; font-size: 13px; color: #333;"><?php echo $accion["nombre_accion"]; ?></strong>
                              <small style="color: #666; line-height: 1.2;"><?php echo $accion["descripcion"]; ?></small>
                            </div>
                          </label>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                  <?php 
                    endforeach;
                  else:
                  ?>
                  <div class="col-12">
                    <div class="alert alert-info text-center">
                      <i class="fa fa-info-circle"></i> No hay acciones registradas
                    </div>
                  </div>
                  <?php endif; ?>
                </div>

                <div class="row" style="margin-top: 15px;">
                  <div class="col-12">
                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar Permisos
                      </button>
                      <button type="button" class="btn btn-success" id="btnSeleccionarTodas">
                        <i class="fa fa-check-square-o"></i> Todas
                      </button>
                      <button type="button" class="btn btn-warning" id="btnDeseleccionarTodas">
                        <i class="fa fa-square-o"></i> Ninguna
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <?php
              $actualizarPermisos = new ControladorRoles();
              $actualizarPermisos -> ctrActualizarPermisosRol();
              ?>
            </form>
          </div>
        </div>
      </section>
    </div>
  </div>
</body>

<script src="vistas/js/roles.js"></script>