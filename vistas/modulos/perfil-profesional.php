<?php
// Verificar permisos
$auth = ServicioAutorizacion::getInstance();
if (!$auth->puede('perfil_profesional_ver')) {
    echo '<script>window.location = "acceso-denegado";</script>';
    exit();
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Perfil Profesional
            <small>Gestión de perfiles profesionales</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Perfil Profesional</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Perfil Profesional</h3>
            </div>
            <div class="box-body">
                <p>Módulo de Perfil Profesional - En desarrollo</p>
            </div>
        </div>
    </section>
</div>