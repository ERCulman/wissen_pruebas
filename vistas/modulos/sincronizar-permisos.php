<?php
require_once "controladores/sincronizacion.controlador.php";
require_once "modelos/sincronizacion.modelo.php";

// Verificar permisos de administrador
if(!ControladorAuth::ctrEsAdministradorSistema()){
    echo '<script>window.location = "acceso-denegado";</script>';
    exit;
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Sincronización de Permisos</h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
            <li class="active">Sincronización de Permisos</li>
        </ol>
    </section>

    <section class="content">
        
        <!-- PANEL DE ESTADÍSTICAS -->
        <div class="row">
            <?php
            $stats = ModeloSincronizacion::mdlObtenerEstadisticas();
            ?>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $stats['total_acciones'] ?? 0; ?></h3>
                        <p>Acciones Activas</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-key"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo $stats['total_roles'] ?? 0; ?></h3>
                        <p>Roles del Sistema</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo $stats['total_usuarios'] ?? 0; ?></h3>
                        <p>Usuarios Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo $stats['total_modulos'] ?? 0; ?></h3>
                        <p>Módulos Registrados</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL DE ACCIONES -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-cogs"></i> Herramientas de Sincronización</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-refresh"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sincronizar Sistema</span>
                                <span class="info-box-number">Actualizar Permisos</span>
                                <button class="btn btn-primary btn-sm" id="btnSincronizar">
                                    <i class="fa fa-refresh"></i> Sincronizar Ahora
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Verificar Integridad</span>
                                <span class="info-box-number">Diagnosticar Problemas</span>
                                <button class="btn btn-success btn-sm" id="btnVerificar">
                                    <i class="fa fa-check"></i> Verificar Sistema
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-orange"><i class="fa fa-wrench"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Reparar Automático</span>
                                <span class="info-box-number">Solucionar Problemas</span>
                                <button class="btn btn-warning btn-sm" id="btnReparar">
                                    <i class="fa fa-wrench"></i> Reparar Sistema
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL DE RESULTADOS -->
        <div class="box" id="panelResultados" style="display: none;">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-info-circle"></i> Resultados</h3>
            </div>
            <div class="box-body" id="contenidoResultados">
                <!-- Los resultados se mostrarán aquí -->
            </div>
        </div>

        <!-- PANEL DE DIAGNÓSTICO -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-stethoscope"></i> Diagnóstico del Sistema</h3>
            </div>
            <div class="box-body">
                <?php
                $integridad = ControladorSincronizacion::ctrVerificarIntegridad();
                
                if($integridad['estado'] == 'ok') {
                    echo '<div class="alert alert-success">
                        <h4><i class="fa fa-check"></i> Sistema Íntegro</h4>
                        <p>No se encontraron problemas en el sistema de permisos.</p>
                    </div>';
                } else if($integridad['estado'] == 'problemas_encontrados') {
                    echo '<div class="alert alert-warning">
                        <h4><i class="fa fa-warning"></i> Problemas Encontrados (' . $integridad['total_problemas'] . ')</h4>
                        <ul>';
                    foreach($integridad['problemas'] as $problema) {
                        echo '<li>' . $problema . '</li>';
                    }
                    echo '</ul>
                        <p><strong>Recomendación:</strong> Ejecutar la sincronización automática para resolver estos problemas.</p>
                    </div>';
                } else {
                    echo '<div class="alert alert-danger">
                        <h4><i class="fa fa-times"></i> Error en Diagnóstico</h4>
                        <p>No se pudo completar el diagnóstico del sistema.</p>
                    </div>';
                }
                ?>
            </div>
        </div>

    </section>
</div>

<script>
$(document).ready(function() {
    
    // Sincronizar sistema
    $('#btnSincronizar').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sincronizando...');
        
        $.ajax({
            url: 'ajax/sincronizacion.ajax.php',
            method: 'POST',
            data: { accion: 'sincronizar' },
            dataType: 'json',
            success: function(response) {
                mostrarResultado(response, 'Sincronización');
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sincronizar Ahora');
            },
            error: function() {
                swal('Error', 'No se pudo completar la sincronización', 'error');
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sincronizar Ahora');
            }
        });
    });
    
    // Verificar integridad
    $('#btnVerificar').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verificando...');
        
        $.ajax({
            url: 'ajax/sincronizacion.ajax.php',
            method: 'POST',
            data: { accion: 'verificar' },
            dataType: 'json',
            success: function(response) {
                mostrarResultado(response, 'Verificación');
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Verificar Sistema');
            },
            error: function() {
                swal('Error', 'No se pudo completar la verificación', 'error');
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Verificar Sistema');
            }
        });
    });
    
    // Reparar sistema
    $('#btnReparar').click(function() {
        swal({
            title: '¿Reparar Sistema?',
            text: 'Esta acción sincronizará y reparará automáticamente los permisos del sistema.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, reparar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.value) {
                var btn = $('#btnReparar');
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Reparando...');
                
                $.ajax({
                    url: 'ajax/sincronizacion.ajax.php',
                    method: 'POST',
                    data: { accion: 'reparar' },
                    dataType: 'json',
                    success: function(response) {
                        mostrarResultado(response, 'Reparación');
                        btn.prop('disabled', false).html('<i class="fa fa-wrench"></i> Reparar Sistema');
                        
                        // Recargar página después de 3 segundos
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    },
                    error: function() {
                        swal('Error', 'No se pudo completar la reparación', 'error');
                        btn.prop('disabled', false).html('<i class="fa fa-wrench"></i> Reparar Sistema');
                    }
                });
            }
        });
    });
    
    function mostrarResultado(response, operacion) {
        var html = '<h4>' + operacion + ' Completada</h4>';
        
        if (response.estado == 'ok') {
            html += '<div class="alert alert-success"><i class="fa fa-check"></i> Operación exitosa</div>';
        } else if (response.estado == 'problemas_encontrados') {
            html += '<div class="alert alert-warning">';
            html += '<h5><i class="fa fa-warning"></i> Problemas encontrados (' + response.total_problemas + '):</h5>';
            html += '<ul>';
            response.problemas.forEach(function(problema) {
                html += '<li>' + problema + '</li>';
            });
            html += '</ul></div>';
        } else {
            html += '<div class="alert alert-danger"><i class="fa fa-times"></i> Error en la operación</div>';
        }
        
        $('#contenidoResultados').html(html);
        $('#panelResultados').show();
    }
});
</script>

<?php
// Procesar acciones POST
if(isset($_POST['accion_sincronizacion'])) {
    switch($_POST['accion_sincronizacion']) {
        case 'sincronizar':
            $resultado = ControladorSincronizacion::ctrSincronizarAccionesModulos();
            if($resultado == "ok") {
                echo '<script>
                    swal({
                        type: "success",
                        title: "Sincronización Completada",
                        text: "El sistema de permisos ha sido sincronizado correctamente",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "sincronizar-permisos";
                        }
                    })
                </script>';
            }
            break;
    }
}
?>