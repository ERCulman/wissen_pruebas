<body class="hold-transition skin-blue sidebar-collapse sidebar-mini login-page">
  <div class="wrapper">
    <div class="content-wrapper">
      <section class="content-header">
        <h1>Asignar Roles a Usuarios</h1>
        <ol class="breadcrumb">
          <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
          <li class="active">Asignar Roles</li>
        </ol>
      </section>

      <section class="content">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Gestión de Roles de Usuarios</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-success" id="btnAsignarSeleccionados" disabled>
                <i class="fa fa-check"></i> Asignar Roles Seleccionados
              </button>
            </div>
          </div>

          <div class="box-body">
            <!-- Filtros -->
            <div class="row" style="margin-bottom: 15px;">
              <div class="col-md-3">
                <label>Filtrar por estado:</label>
                <select class="form-control" id="filtroEstado">
                  <option value="">Todos los usuarios</option>
                  <option value="sin_rol">Solo usuarios sin rol</option>
                  <option value="con_rol">Solo usuarios con rol</option>
                </select>
              </div>
              <div class="col-md-3">
                <label>Filtrar por rol específico:</label>
                <select class="form-control" id="filtroRolEspecifico">
                  <option value="">Todos los roles</option>
                  <?php
                  $roles = ControladorRoles::ctrMostrarRoles(null, null);
                  $sedes = ControladorSede::ctrMostrarSede(null, null);
                  
                  if($roles):
                    foreach($roles as $rol):
                  ?>
                  <option value="<?php echo $rol["nombre_rol"]; ?>"><?php echo $rol["nombre_rol"]; ?></option>
                  <?php 
                    endforeach;
                  endif;
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label>Buscar usuario:</label>
                <input type="text" class="form-control" id="buscarUsuario" placeholder="Buscar por nombre o documento...">
              </div>
            </div>

            <div class="table-responsive">
              <!-- Controles de asignación masiva -->
              <div class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="dataTables_length" id="tablaUsuarios_length">
                      <label>Mostrar 
                        <select name="tablaUsuarios_length" class="form-control input-sm">
                          <option value="10">10</option>
                          <option value="25" selected>25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select> registros
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="dataTables_filter" style="text-align: right;">
                      <label style="margin-right: 15px;">Sede para asignar:
                        <select class="form-control input-sm" id="sedeAsignacionMasiva" style="width: auto; display: inline-block; margin-left: 5px;">
                          <option value="">Seleccionar sede</option>
                          <?php 
                          if($sedes):
                            foreach($sedes as $sede):
                          ?>
                          <option value="<?php echo $sede["id"]; ?>"><?php echo $sede["nombre_sede"]; ?></option>
                          <?php 
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </label>
                      <label>Rol para asignar:
                        <select class="form-control input-sm" id="rolAsignacionMasiva" style="width: auto; display: inline-block; margin-left: 5px;">
                          <option value="">Seleccionar rol</option>
                          <?php 
                          if($roles):
                            foreach($roles as $rol):
                          ?>
                          <option value="<?php echo $rol["id_rol"]; ?>"><?php echo $rol["nombre_rol"]; ?></option>
                          <?php 
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              
              <table class="table table-bordered table-striped" id="tablaUsuarios">
                <thead>
                  <tr>
                    <th style="width: 5%">
                      <input type="checkbox" id="selectAll">
                    </th>
                    <th>Tipo Doc.</th>
                    <th>Documento</th>
                    <th>Nombre Completo</th>
                    <th>Roles Activos</th>
                    <th>Roles Inactivos</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Obtener todos los usuarios
                  $usuarios = ControladorUsuarios::ctrMostrarUsuario(null, null);
                  
                  // Obtener usuarios con roles institucionales
                  $usuariosConRoles = ControladorRoles::ctrMostrarUsuariosConRoles();
                  $rolesUsuarios = array();
                  if($usuariosConRoles):
                    foreach($usuariosConRoles as $usuarioRol):
                      if(!isset($rolesUsuarios[$usuarioRol["id_usuario"]])){
                        $rolesUsuarios[$usuarioRol["id_usuario"]] = array();
                      }
                      $rolesUsuarios[$usuarioRol["id_usuario"]][] = array(
                        "rol" => $usuarioRol["nombre_rol"],
                        "sede" => $usuarioRol["nombre_sede"],
                        "tipo" => $usuarioRol["tipo_rol"],
                        "id" => $usuarioRol["rol_institucional_id"],
                        "estado" => $usuarioRol["estado"]
                      );
                    endforeach;
                  endif;

                  // TODO: Agregar consulta para administradores_sistema
                  // $adminsSistema = ControladorRoles::ctrMostrarAdministradoresSistema();
                  
                  if($usuarios):
                    foreach($usuarios as $usuario):
                      $rolesDelUsuario = isset($rolesUsuarios[$usuario["id_usuario"]]) ? $rolesUsuarios[$usuario["id_usuario"]] : array();
                  ?>
                  <tr data-usuario-id="<?php echo $usuario["id_usuario"]; ?>">
                    <td>
                      <input type="checkbox" class="usuario-checkbox" value="<?php echo $usuario["id_usuario"]; ?>">
                    </td>
                    <td><?php echo $usuario["tipo_documento"]; ?></td>
                    <td><?php echo $usuario["numero_documento"]; ?></td>
                    <td><?php echo $usuario["nombres_usuario"] . " " . $usuario["apellidos_usuario"]; ?></td>
                    <td>
                      <?php 
                      $rolesActivos = array_filter($rolesDelUsuario, function($rol) {
                        return isset($rol['estado']) && $rol['estado'] == 'Activo';
                      });
                      
                      if(empty($rolesActivos)): ?>
                        <span class="label label-default">Sin roles activos</span>
                      <?php else: ?>
                        <?php foreach($rolesActivos as $rol): ?>
                          <span class="label label-success" style="margin-right: 3px;"><?php echo $rol["rol"]; ?></span>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php 
                      $rolesInactivos = array_filter($rolesDelUsuario, function($rol) {
                        return isset($rol['estado']) && $rol['estado'] == 'Inactivo';
                      });
                      
                      if(empty($rolesInactivos)): ?>
                        <span class="text-muted">-</span>
                      <?php else: ?>
                        <?php foreach($rolesInactivos as $rol): ?>
                          <button class="btn btn-warning btn-xs" style="margin-right: 3px; margin-bottom: 2px;" title="Rol inactivo">
                            <?php echo $rol["rol"]; ?>
                          </button>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </td>
                    <td>
                      <button class="btn btn-primary btn-sm btnGestionarRoles" data-id="<?php echo $usuario["id_usuario"]; ?>" data-nombre="<?php echo $usuario["nombres_usuario"] . ' ' . $usuario["apellidos_usuario"]; ?>" style="margin-right: 5px;">
                        <i class="fa fa-cogs"></i> Gestionar
                      </button>
                      <button class="btn btn-primary btn-sm btnGestionarPermisos" data-id="<?php echo $usuario["id_usuario"]; ?>" data-nombre="<?php echo $usuario["nombres_usuario"] . ' ' . $usuario["apellidos_usuario"]; ?>">
                        <i class="fa fa-key"></i> Permisos Especiales
                      </button>
                    </td>
                  </tr>
                  <?php 
                    endforeach;
                  else:
                  ?>
                  <tr>
                    <td colspan="7" class="text-center">No hay usuarios registrados</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- MODAL GESTIONAR ROLES -->
    <div id="modalGestionarRoles" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="background: #3c8ebdff; color: white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Gestionar Roles de <span id="nombreUsuarioModal"></span></h4>
          </div>
          <div class="modal-body">
            <input type="hidden" id="usuarioIdModal">
            
            <!-- Roles Actuales -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <h4 class="box-title">Roles Actuales</h4>
              </div>
              <div class="box-body" id="rolesActualesContainer">
                <!-- Se cargan dinámicamente -->
              </div>
            </div>
            
            <!-- Editar Rol Existente -->
            <div class="box box-warning" id="seccionEditarRol" style="display: none;">
              <div class="box-header with-border">
                <h4 class="box-title">Editar Rol</h4>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-xs btn-default" id="btnCancelarEdicion">
                    <i class="fa fa-times"></i> Cancelar
                  </button>
                </div>
              </div>
              <div class="box-body">
                <form id="formEditarRol">
                  <input type="hidden" id="editarRolId">
                  <input type="hidden" id="editarTipoRol">
                  <div class="row">
                    <div class="col-md-3">
                      <label>Rol:</label>
                      <input type="text" class="form-control" id="editarRolTexto" readonly style="background-color: #f5f5f5;">
                    </div>
                    <div class="col-md-3">
                      <label>Sede:</label>
                      <input type="text" class="form-control" id="editarSedeTexto" readonly style="background-color: #f5f5f5;">
                    </div>
                    <div class="col-md-3">
                      <label>Fecha Inicio:</label>
                      <input type="date" class="form-control" id="editarFechaInicio" readonly style="background-color: #f5f5f5;">
                    </div>
                    <div class="col-md-3">
                      <label>Estado:</label>
                      <select class="form-control" id="editarEstadoRol" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                      </select>
                    </div>
                  </div>
                  <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save"></i> Guardar Cambios
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            
            <!-- Agregar Nuevo Rol -->
            <div class="box box-success" id="seccionAgregarRol">
              <div class="box-header with-border">
                <h4 class="box-title">Agregar Nuevo Rol</h4>
              </div>
              <div class="box-body">
                <form id="formAgregarRol">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Rol:</label>
                      <select class="form-control" id="nuevoRol" required>
                        <option value="">Seleccionar rol</option>
                        <?php foreach($roles as $rol): ?>
                        <option value="<?php echo $rol["id_rol"]; ?>" data-nombre="<?php echo $rol["nombre_rol"]; ?>"><?php echo $rol["nombre_rol"]; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4" id="campoSede">
                      <label>Sede:</label>
                      <select class="form-control" id="nuevaSede">
                        <option value="">Seleccionar sede</option>
                        <?php foreach($sedes as $sede): ?>
                        <option value="<?php echo $sede["id"]; ?>"><?php echo $sede["nombre_sede"]; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4" id="campoAutorizadoPor" style="display: none;">
                      <label>Autorizado por:</label>
                      <input type="text" class="form-control" id="autorizadoPor" value="<?php echo $_SESSION['nombres_usuario'] . ' ' . $_SESSION['apellidos_usuario']; ?>" readonly style="background-color: #f5f5f5;">
                      <input type="hidden" id="autorizadoPorId" value="<?php echo $_SESSION['id_usuario']; ?>">
                    </div>
                    <div class="col-md-4">
                      <label>Fecha Inicio:</label>
                      <input type="date" class="form-control" id="nuevaFecha" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                  </div>
                  <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Agregar Rol
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-success" id="btnGuardarCambios">
              <i class="fa fa-save"></i> Guardar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL GESTIONAR PERMISOS -->
    <div id="modalGestionarPermisos" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="background: #17a2b8; color: white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Permisos de <span id="nombreUsuarioPermisos"></span></h4>
          </div>
          <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            <input type="hidden" id="usuarioIdPermisos">
            
            <!-- Seleccionar Rol/Sede si tiene múltiples -->
            <div class="box box-info" id="selectorRolSede" style="display: none;">
              <div class="box-header with-border">
                <h4 class="box-title">Seleccionar Rol/Sede para Permisos Especiales</h4>
              </div>
              <div class="box-body">
                <select class="form-control" id="rolSedeSelector">
                  <option value="">Seleccione rol y sede...</option>
                </select>
              </div>
            </div>
            
            <!-- Permisos Heredados de Roles -->
            <div class="box box-success">
              <div class="box-header with-border">
                <h4 class="box-title">Permisos Heredados de Roles</h4>
              </div>
              <div class="box-body" id="permisosHeredados">
                <!-- Se cargan dinámicamente -->
              </div>
            </div>
            
            <!-- Permisos Especiales -->
            <div class="box box-success">
              <div class="box-header with-border">
                <h4 class="box-title">Permisos Especiales</h4>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-xs btn-success" id="btnAgregarPermisoEspecial" disabled>
                    <i class="fa fa-plus"></i> Agregar Permiso
                  </button>
                </div>
              </div>
              <div class="box-body" id="permisosEspeciales">
                <!-- Se cargan dinámicamente -->
              </div>
            </div>
            
            <!-- Resumen Consolidado -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <h4 class="box-title">Resumen - Todos los Permisos Activos</h4>
              </div>
              <div class="box-body" id="resumenPermisos">
                <!-- Se cargan dinámicamente -->
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL AGREGAR PERMISO ESPECIAL -->
    <div id="modalAgregarPermisoEspecial" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background: #f39c12; color: white;">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Agregar Permiso Especial</h4>
          </div>
          <div class="modal-body">
            <form id="formPermisoEspecial">
              <input type="hidden" id="usuarioPermisoEspecial">
              <input type="hidden" id="sedePermisoEspecial">
              
              <div class="form-group">
                <label>Acción/Permiso:</label>
                <select class="form-control" id="accionPermisoEspecial" required>
                  <option value="">Seleccionar acción...</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-warning" id="btnGuardarPermisoEspecial">
              <i class="fa fa-save"></i> Agregar Permiso
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<script>
$(document).ready(function(){
    
    // Inicializar DataTable sin controles duplicados
    var tabla = $("#tablaUsuarios").DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        "pageLength": 25,
        "dom": 'rt<"bottom"ip><"clear">',
        "searching": true,
        "lengthChange": false
    });
    
    // Conectar el select personalizado con DataTable
    $("select[name='tablaUsuarios_length']").on('change', function(){
        tabla.page.len($(this).val()).draw();
    });
    
    // Búsqueda personalizada
    $("#buscarUsuario").on('keyup', function(){
        tabla.search(this.value).draw();
    });
    
    // Filtro por estado
    $("#filtroEstado").on('change', function(){
        var filtro = $(this).val();
        if(filtro === "sin_rol"){
            tabla.column(4).search("Sin roles activos", false, false).draw();
        } else if(filtro === "con_rol"){
            tabla.column(4).search("^(?!.*Sin roles activos).*$", true, false).draw();
        } else {
            tabla.column(4).search("").draw();
        }
    });
    
    // Filtro por rol específico (busca en ambas columnas)
    $("#filtroRolEspecifico").on('change', function(){
        var rol = $(this).val();
        
        // Limpiar todos los filtros personalizados primero
        $.fn.dataTable.ext.search = [];
        
        if(rol){
            // Función personalizada para buscar en múltiples columnas
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex){
                    var rolesActivos = data[4] || '';
                    var rolesInactivos = data[5] || '';
                    return rolesActivos.indexOf(rol) !== -1 || rolesInactivos.indexOf(rol) !== -1;
                }
            );
        }
        tabla.draw();
    });
    
    // Seleccionar todos
    $("#selectAll").change(function(){
        $(".usuario-checkbox:visible").prop('checked', $(this).prop("checked"));
        actualizarBotonAsignar();
    });
    
    // Cambio en checkboxes individuales
    $(document).on('change', '.usuario-checkbox', function(){
        actualizarBotonAsignar();
    });
    
    // Cambio en selects de asignación masiva
    $("#sedeAsignacionMasiva, #rolAsignacionMasiva").on('change', function(){
        actualizarBotonAsignar();
    });
    
    function actualizarBotonAsignar(){
        var seleccionados = $(".usuario-checkbox:checked").length;
        var sede = $("#sedeAsignacionMasiva").val();
        var rol = $("#rolAsignacionMasiva").val();
        
        if(seleccionados > 0 && sede && rol){
            $("#btnAsignarSeleccionados").prop('disabled', false);
        } else {
            $("#btnAsignarSeleccionados").prop('disabled', true);
        }
    }
    
    // Asignar roles seleccionados
    $("#btnAsignarSeleccionados").click(function(){
        var asignaciones = [];
        var sede = $("#sedeAsignacionMasiva").val();
        var rol = $("#rolAsignacionMasiva").val();
        var fecha = new Date().toISOString().split('T')[0]; // Fecha actual
        
        $(".usuario-checkbox:checked").each(function(){
            var usuarioId = $(this).val();
            
            asignaciones.push({
                usuario_id: usuarioId,
                rol_id: rol,
                sede_id: sede,
                fecha_inicio: fecha
            });
        });
        
        if(asignaciones.length === 0){
            Swal.fire("Advertencia", "Debe seleccionar al menos un usuario", "warning");
            return;
        }
        
        var nombreRol = $("#rolAsignacionMasiva option:selected").text();
        var nombreSede = $("#sedeAsignacionMasiva option:selected").text();
        
        Swal.fire({
            title: '¿Asignar rol "' + nombreRol + '" a ' + asignaciones.length + ' usuarios?',
            text: "Sede: " + nombreSede + " - Fecha: " + fecha,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, asignar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "ajax/roles.ajax.php",
                    method: "POST",
                    data: {
                        accion: "asignarRolesMasivo",
                        asignaciones: JSON.stringify(asignaciones)
                    },
                    success: function(respuesta){
                        if(respuesta === "ok"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Roles asignados correctamente',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else if(respuesta === "error_estudiantes"){
                            Swal.fire("Advertencia", "Algunos usuarios son estudiantes y no pueden tener otros roles. Se omitieron esas asignaciones.", "warning");
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            Swal.fire("Error", "Error al asignar los roles", "error");
                        }
                    },
                    error: function(){
                        Swal.fire("Error", "Error de conexión", "error");
                    }
                });
            }
        });
    });
    
    // Gestionar roles individuales
    $(document).on("click", ".btnGestionarRoles", function(){
        var usuarioId = $(this).attr("data-id");
        var nombreUsuario = $(this).attr("data-nombre");
        
        $("#usuarioIdModal").val(usuarioId);
        $("#nombreUsuarioModal").text(nombreUsuario);
        
        // Cargar roles actuales del usuario
        cargarRolesUsuario(usuarioId);
        
        $("#modalGestionarRoles").modal('show');
    });
    
    // Función para cargar roles del usuario
    function cargarRolesUsuario(usuarioId){
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerRolesUsuario",
                usuarioId: usuarioId
            },
            dataType: "json",
            success: function(roles){
                var html = "";
                if(roles.length > 0){
                    roles.forEach(function(rol){
                        html += '<div class="alert alert-info" style="margin-bottom: 10px;">';
                        html += '<strong>' + rol.nombre_rol + '</strong> - ' + rol.nombre_sede;
                        html += '<span class="pull-right">';
                        html += '<button class="btn btn-xs btn-warning btnEditarRolIndividual" data-id="' + rol.id + '" data-tipo="' + (rol.tipo_rol || 'institucional') + '" data-rol-nombre="' + rol.nombre_rol + '" data-sede-nombre="' + rol.nombre_sede + '" data-fecha="' + rol.fecha_inicio + '" data-estado="' + rol.estado + '" style="margin-right: 5px;"><i class="fa fa-edit"></i></button>';
                        html += '<button class="btn btn-xs btn-danger btnEliminarRolIndividual" data-id="' + rol.id + '" data-tipo="' + (rol.tipo_rol || 'institucional') + '"><i class="fa fa-trash"></i></button>';
                        html += '</span>';
                        html += '</div>';
                    });
                } else {
                    html = '<div class="alert alert-warning">Este usuario no tiene roles asignados</div>';
                }
                $("#rolesActualesContainer").html(html);
            },
            error: function(){
                $("#rolesActualesContainer").html('<div class="alert alert-danger">Error al cargar los roles</div>');
            }
        });
    }
    
    // Detectar cambio de rol para mostrar campos apropiados
    $("#nuevoRol").on('change', function(){
        var rolNombre = $(this).find('option:selected').data('nombre');
        var esRolSistema = (rolNombre === 'Superadministrador' || rolNombre === 'Administrador');
        
        if(esRolSistema){
            $("#campoSede").hide();
            $("#campoAutorizadoPor").show();
            $("#nuevaSede").prop('required', false);
        } else {
            $("#campoSede").show();
            $("#campoAutorizadoPor").hide();
            $("#nuevaSede").prop('required', true);
        }
    });
    
    // Agregar nuevo rol
    $("#formAgregarRol").on('submit', function(e){
        e.preventDefault();
        
        var usuarioId = $("#usuarioIdModal").val();
        var rolId = $("#nuevoRol").val();
        var rolNombre = $("#nuevoRol").find('option:selected').data('nombre');
        var sedeId = $("#nuevaSede").val();
        var fecha = $("#nuevaFecha").val();
        var autorizadoPorId = $("#autorizadoPorId").val();
        
        var esRolSistema = (rolNombre === 'Superadministrador' || rolNombre === 'Administrador');
        
        // Validar campos según tipo de rol
        if(!rolId || !fecha){
            Swal.fire("Error", "Rol y fecha son obligatorios", "error");
            return;
        }
        
        if(!esRolSistema && !sedeId){
            Swal.fire("Error", "La sede es obligatoria para roles institucionales", "error");
            return;
        }
        
        // Debug - remover después
        console.log('Datos a enviar:', {
            esRolSistema: esRolSistema,
            usuarioId: usuarioId,
            rolId: rolId,
            rolNombre: rolNombre,
            sedeId: sedeId,
            autorizadoPorId: autorizadoPorId
        });
        
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: {
                accion: "agregarRolIndividual",
                usuarioId: usuarioId,
                rolId: rolId,
                sedeId: sedeId,
                fecha: fecha,
                esRolSistema: esRolSistema ? 'true' : 'false',
                autorizadoPorId: autorizadoPorId
            },
            success: function(respuesta){
                if(respuesta === "ok"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Rol agregado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Limpiar formulario
                    $("#formAgregarRol")[0].reset();
                    $("#nuevaFecha").val('<?php echo date('Y-m-d'); ?>');
                    
                    // Recargar roles del usuario
                    cargarRolesUsuario(usuarioId);
                } else if(respuesta === "error_estudiante"){
                    Swal.fire("Error", "Los estudiantes no pueden tener otros roles", "error");
                } else if(respuesta === "error_ya_estudiante"){
                    Swal.fire("Error", "Este usuario ya es estudiante y no puede tener otros roles", "error");
                } else {
                    Swal.fire("Error", "Error al agregar el rol", "error");
                }
            },
            error: function(){
                Swal.fire("Error", "Error de conexión", "error");
            }
        });
    });
    
    // Eliminar rol individual
    $(document).on('click', '.btnEliminarRolIndividual', function(){
        var rolId = $(this).data('id');
        var tipoRol = $(this).data('tipo');
        var usuarioId = $("#usuarioIdModal").val();
        
        Swal.fire({
            title: '¿Eliminar este rol?',
            text: "Si tiene relaciones activas solo se inactivará",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, proceder'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "ajax/roles.ajax.php",
                    method: "POST",
                    data: {
                        accion: "eliminarRolIndividual",
                        rolId: rolId,
                        tipoRol: tipoRol
                    },
                    success: function(respuesta){
                        if(respuesta === "ok"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Rol eliminado correctamente',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            cargarRolesUsuario(usuarioId);
                        } else if(respuesta === "inactivado"){
                            Swal.fire({
                                icon: 'info',
                                title: 'Rol inactivado',
                                text: 'El rol tenía relaciones activas y fue inactivado',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            cargarRolesUsuario(usuarioId);
                        } else {
                            Swal.fire("Error", "Error al eliminar el rol", "error");
                        }
                    },
                    error: function(){
                        Swal.fire("Error", "Error de conexión", "error");
                    }
                });
            }
        });
    });
    
    // Eliminar rol individual
    $(document).on('click', '.btnEliminarRolIndividual', function(){
        var rolId = $(this).data('id');
        var tipoRol = $(this).data('tipo');
        var usuarioId = $("#usuarioIdModal").val();
        
        Swal.fire({
            title: '¿Eliminar este rol?',
            text: "Si tiene relaciones activas solo se inactivará",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, proceder'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "ajax/roles.ajax.php",
                    method: "POST",
                    data: {
                        accion: "eliminarRolIndividual",
                        rolId: rolId,
                        tipoRol: tipoRol
                    },
                    success: function(respuesta){
                        if(respuesta === "ok"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Rol eliminado correctamente',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            cargarRolesUsuario(usuarioId);
                        } else if(respuesta === "inactivado"){
                            Swal.fire({
                                icon: 'info',
                                title: 'Rol inactivado',
                                text: 'El rol tenía relaciones activas y fue inactivado',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            cargarRolesUsuario(usuarioId);
                        } else {
                            Swal.fire("Error", "Error al eliminar el rol", "error");
                        }
                    },
                    error: function(){
                        Swal.fire("Error", "Error de conexión", "error");
                    }
                });
            }
        });
    });
    
    // Editar rol individual
    $(document).on('click', '.btnEditarRolIndividual', function(){
        var rolId = $(this).data('id');
        var tipoRol = $(this).data('tipo');
        var rolNombre = $(this).data('rol-nombre');
        var sedeNombre = $(this).data('sede-nombre');
        var fecha = $(this).data('fecha');
        var estado = $(this).data('estado');
        
        // Llenar formulario de edición con los datos actuales
        $('#editarRolId').val(rolId);
        $('#editarTipoRol').val(tipoRol);
        $('#editarRolTexto').val(rolNombre);
        $('#editarSedeTexto').val(sedeNombre);
        $('#editarFechaInicio').val(fecha);
        $('#editarEstadoRol').val(estado);
        
        // Mostrar sección de editar y ocultar agregar
        $('#seccionEditarRol').show();
        $('#seccionAgregarRol').hide();
    });
    
    // Cancelar edición
    $('#btnCancelarEdicion').click(function(){
        $('#seccionEditarRol').hide();
        $('#seccionAgregarRol').show();
        $('#formEditarRol')[0].reset();
    });
    
    // Guardar edición de rol
    $('#formEditarRol').on('submit', function(e){
        e.preventDefault();
        
        var rolId = $('#editarRolId').val();
        var tipoRol = $('#editarTipoRol').val();
        var estado = $('#editarEstadoRol').val();
        var usuarioId = $('#usuarioIdModal').val();
        
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: {
                accion: "editarEstadoRol",
                rolId: rolId,
                tipoRol: tipoRol,
                estado: estado
            },
            success: function(respuesta){
                if(respuesta === "ok"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Rol actualizado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Cancelar edición
                    $('#btnCancelarEdicion').click();
                    
                    // Recargar roles del usuario
                    cargarRolesUsuario(usuarioId);
                } else {
                    Swal.fire("Error", "Error al actualizar el rol", "error");
                }
            },
            error: function(){
                Swal.fire("Error", "Error de conexión", "error");
            }
        });
    });
    
    // Botón Guardar y Cerrar
    $('#btnGuardarCambios').click(function(){
        $('#modalGestionarRoles').modal('hide');
        window.location.reload();
    });
    
    // Gestionar permisos individuales
    $(document).on("click", ".btnGestionarPermisos", function(){
        var usuarioId = $(this).attr("data-id");
        var nombreUsuario = $(this).attr("data-nombre");
        
        // Verificar si el usuario tiene roles asignados
        var $fila = $(this).closest('tr');
        var rolesActivos = $fila.find('td:eq(4)').text().trim();
        
        if(rolesActivos === 'Sin roles activos'){
            Swal.fire({
                icon: 'warning',
                title: 'Usuario sin rol asignado',
                text: 'Para poder agregar permisos especiales debe asignar un rol al usuario primero.',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        $("#usuarioIdPermisos").val(usuarioId);
        $("#nombreUsuarioPermisos").text(nombreUsuario);
        
        // Cargar permisos del usuario
        cargarPermisosUsuario(usuarioId);
        
        $("#modalGestionarPermisos").modal('show');
    });
    
    // Función para cargar permisos del usuario
    function cargarPermisosUsuario(usuarioId){
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerPermisosUsuario",
                usuarioId: usuarioId
            },
            dataType: "json",
            success: function(data){
                if(data.error){
                    $("#permisosHeredados").html('<div class="alert alert-danger">' + data.error + '</div>');
                    return;
                }
                
                // Lógica simplificada: siempre mostrar selector si tiene roles
                if(data.roles && data.roles.length > 0){
                    var rolesActivos = data.roles.filter(function(rol){ return rol.estado === 'Activo'; });
                    
                    if(rolesActivos.length > 0){
                        // SIEMPRE mostrar selector si tiene roles activos
                        var selectorHtml = '<option value="">Seleccione rol y sede...</option>';
                        rolesActivos.forEach(function(rol){
                            var sedeId = rol.sede_id || 'sistema';
                            selectorHtml += '<option value="' + rol.id + '_' + sedeId + '">' + rol.nombre_rol + ' - ' + rol.nombre_sede + '</option>';
                        });
                        $("#rolSedeSelector").html(selectorHtml);
                        $("#selectorRolSede").show();
                    } else {
                        $("#selectorRolSede").hide();
                        $("#btnAgregarPermisoEspecial").prop('disabled', true);
                    }
                } else {
                    $("#selectorRolSede").hide();
                    $("#btnAgregarPermisoEspecial").prop('disabled', true);
                }
                
                // Mostrar permisos heredados
                mostrarPermisosHeredados(data.permisosHeredados || []);
                
                // Mostrar permisos especiales
                mostrarPermisosEspeciales(data.permisosEspeciales || []);
                
                // Mostrar resumen
                mostrarResumenPermisos(data.resumen || []);
            },
            error: function(){
                $("#permisosHeredados").html('<div class="alert alert-danger">Error al cargar los permisos</div>');
            }
        });
    }
    
    // Selector de rol/sede para permisos especiales
    $("#rolSedeSelector").on('change', function(){
        var seleccionado = $(this).val();
        $("#btnAgregarPermisoEspecial").prop('disabled', !seleccionado);
    });
    
    function mostrarPermisosHeredados(permisos){
        var html = "";
        if(permisos.length > 0){
            permisos.forEach(function(grupo){
                html += '<div class="panel panel-default">';
                html += '<div class="panel-heading"><strong>' + grupo.rol + ' (' + grupo.sede + ')</strong></div>';
                html += '<div class="panel-body" style="display: flex; flex-wrap: wrap; gap: 5px;">';
                grupo.acciones.forEach(function(accion){
                    html += '<span class="label label-success" style="margin-bottom: 3px; white-space: nowrap;">' + accion + '</span>';
                });
                html += '</div></div>';
            });
        } else {
            html = '<div class="alert alert-info">Este usuario no tiene permisos heredados de roles</div>';
        }
        $("#permisosHeredados").html(html);
    }
    
    function mostrarPermisosEspeciales(permisos){
        var html = "";
        if(permisos.length > 0){
            // Agrupar por sede
            var permisosPorSede = {};
            permisos.forEach(function(permiso){
                if(!permisosPorSede[permiso.sede]){
                    permisosPorSede[permiso.sede] = [];
                }
                permisosPorSede[permiso.sede].push(permiso);
            });
            
            // Mostrar agrupados por sede
            Object.keys(permisosPorSede).forEach(function(sede){
                html += '<div class="panel panel-default">';
                html += '<div class="panel-heading"><strong>Permisos Especiales - ' + sede + '</strong></div>';
                html += '<div class="panel-body"><div class="row">';
                permisosPorSede[sede].forEach(function(permiso, index){
                    html += '<div class="col-md-6" style="margin-bottom: 8px;">';
                    html += '<div style="padding: 5px; border-left: 3px solid #d73925;">';
                    html += '<span class="label label-success" style="margin-right: 5px;">' + permiso.accion + '</span>';
                    if(permiso.descripcion){
                        html += '<small style="color: #333;"> - ' + permiso.descripcion + '</small>';
                    }
                    html += '<button class="btn btn-xs pull-right btnEliminarPermisoEspecial" ';
                    html += 'data-usuario-id="' + permiso.usuario_id + '" ';
                    html += 'data-accion-id="' + permiso.accion_id + '" ';
                    html += 'data-sede-jornada-id="' + permiso.sede_jornada_id + '" ';
                    html += 'data-accion-nombre="' + permiso.accion + '" ';
                    html += 'style="color: #d73925; background: none; border: none; padding: 2px 5px;" title="Eliminar permiso">';
                    html += '<i class="fa fa-times"></i></button>';
                    html += '<div class="clearfix"></div></div></div>';
                });
                html += '</div></div></div>';
            });
        } else {
            html = '<div class="alert alert-info">No tiene permisos especiales asignados</div>';
        }
        $("#permisosEspeciales").html(html);
    }
    
    function mostrarResumenPermisos(permisos){
        var html = "";
        if(permisos.length > 0){
            html += '<div style="display: flex; flex-wrap: wrap; gap: 5px;">';
            permisos.forEach(function(permiso){
                var origen = permiso.origen === 'rol' ? '(Heredado)' : '(Especial)';
                html += '<span class="label label-primary" style="margin-bottom: 3px; white-space: nowrap;">' + permiso.accion + ' ' + origen + '</span>';
            });
            html += '</div>';
        } else {
            html = '<div class="alert alert-warning">Este usuario no tiene permisos activos</div>';
        }
        $("#resumenPermisos").html(html);
    }
    
    // Agregar permiso especial
    $("#btnAgregarPermisoEspecial").click(function(){
        var usuarioId = $("#usuarioIdPermisos").val();
        var rolSedeSeleccionado = $("#rolSedeSelector").val();
        
        if(!rolSedeSeleccionado){
            Swal.fire("Error", "Debe seleccionar un rol y sede primero", "error");
            return;
        }
        
        var partes = rolSedeSeleccionado.split('_');
        var rolId = partes[0];
        var sedeId = partes[1];
        
        $("#usuarioPermisoEspecial").val(usuarioId);
        $("#sedePermisoEspecial").val(sedeId);
        
        // Cargar acciones disponibles
        cargarAccionesDisponibles(usuarioId, sedeId);
        
        $("#modalAgregarPermisoEspecial").modal('show');
    });
    
    // Función para cargar acciones que el usuario NO tiene
    function cargarAccionesDisponibles(usuarioId, sedeId){
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: {
                accion: "obtenerAccionesDisponibles",
                usuarioId: usuarioId,
                sedeId: sedeId
            },
            dataType: "json",
            success: function(acciones){
                var html = '<option value="">Seleccionar acción...</option>';
                if(acciones && acciones.length > 0){
                    acciones.forEach(function(accion){
                        html += '<option value="' + accion.id + '">' + accion.nombre_accion + ' - ' + accion.descripcion + '</option>';
                    });
                } else {
                    html += '<option value="">No hay acciones disponibles</option>';
                }
                $("#accionPermisoEspecial").html(html);
            },
            error: function(xhr, status, error){
                $("#accionPermisoEspecial").html('<option value="">Error al cargar acciones</option>');
            }
        });
    }
    
    // Guardar permiso especial
    $("#btnGuardarPermisoEspecial").click(function(){
        var usuarioId = $("#usuarioPermisoEspecial").val();
        var sedeId = $("#sedePermisoEspecial").val();
        var accionId = $("#accionPermisoEspecial").val();
        
        if(!accionId){
            Swal.fire("Error", "Debe seleccionar una acción", "error");
            return;
        }
        
        console.log('Datos a enviar:', {
            usuarioId: usuarioId,
            sedeId: sedeId,
            accionId: accionId
        });
        
        $.ajax({
            url: "ajax/roles.ajax.php",
            method: "POST",
            data: {
                accion: "agregarPermisoEspecial",
                usuarioId: usuarioId,
                sedeId: sedeId,
                accionId: accionId
            },
            success: function(respuesta){
                if(respuesta.trim() === "ok"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Permiso especial agregado',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    $("#modalAgregarPermisoEspecial").modal('hide');
                    $("#formPermisoEspecial")[0].reset();
                    
                    // Recargar permisos
                    cargarPermisosUsuario(usuarioId);
                } else {
                    Swal.fire("Error", "Error al agregar el permiso especial. Respuesta: " + respuesta, "error");
                }
            },
            error: function(xhr, status, error){
                Swal.fire("Error", "Error de conexión: " + xhr.responseText, "error");
            }
        });
    });
    
    // Eliminar permiso especial
    $(document).on('click', '.btnEliminarPermisoEspecial', function(){
        var usuarioId = $(this).data('usuario-id');
        var accionId = $(this).data('accion-id');
        var sedeJornadaId = $(this).data('sede-jornada-id');
        var accionNombre = $(this).data('accion-nombre');
        
        Swal.fire({
            title: '¿Eliminar permiso especial?',
            text: 'Se eliminará el permiso "' + accionNombre + '"',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "ajax/roles.ajax.php",
                    method: "POST",
                    data: {
                        accion: "eliminarPermisoEspecial",
                        usuarioId: usuarioId,
                        accionId: accionId,
                        sedeJornadaId: sedeJornadaId
                    },
                    success: function(respuesta){
                        if(respuesta.trim() === "ok"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Permiso eliminado',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            // Recargar permisos
                            cargarPermisosUsuario(usuarioId);
                        } else {
                            Swal.fire("Error", "Error al eliminar el permiso: " + respuesta, "error");
                        }
                    },
                    error: function(){
                        Swal.fire("Error", "Error de conexión", "error");
                    }
                });
            }
        });
    });
    
});
</script>

<?php
// Procesar asignaciones si vienen por POST
if(isset($_POST["asignacionesRoles"])){
    $asignaciones = json_decode($_POST["asignacionesRoles"], true);
    
    foreach($asignaciones as $asignacion){
        $datos = array(
            "usuario_id" => $asignacion["usuario_id"],
            "rol_id" => $asignacion["rol_id"],
            "sede_id" => $asignacion["sede_id"],
            "fecha_inicio" => $asignacion["fecha_inicio"],
            "fecha_fin" => null,
            "estado" => "Activo"
        );
        
        ModeloRoles::mdlAsignarRolInstitucional($datos);
    }
    
    echo '<script>
        Swal.fire({
            type: "success",
            title: "Roles asignados correctamente",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        }).then(function(result){
            if (result.value) {
                window.location = "asignar-roles";
            }
        })
    </script>';
}
?>