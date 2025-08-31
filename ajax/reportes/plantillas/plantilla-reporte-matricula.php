<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Matrícula</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header h2 { margin: 5px 0; color: #555; font-size: 14px; }
        .panel { border: 1px solid #ccc; border-radius: 5px; margin-bottom: 15px; page-break-inside: avoid; }
        .panel-heading { background-color: #f0f0f0; padding: 8px; border-bottom: 1px solid #ccc; font-weight: bold; }
        .panel-body { padding: 12px; }
        .row::after { content: ""; clear: both; display: table; }
        .col-2 { float: left; width: 48%; margin-right: 2%; }
        .col-3 { float: left; width: 32%; margin-right: 1%; }
        .col-full { width: 100%; }
        strong { display: block; color: #333; font-size: 10px; margin-bottom: 2px; }
        p { margin: 0 0 10px 0; }
        .well { background-color: #f9f9f9; border: 1px solid #eee; padding: 10px; margin-bottom: 10px; border-radius: 3px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?= htmlspecialchars(isset($data['institucion_nombre']) ? $data['institucion_nombre'] : 'Institución Educativa') ?></h1>
        <h2><?= htmlspecialchars(isset($data['nombre_sede']) ? $data['nombre_sede'] : 'Sede Principal') ?></h2>
        <hr>
        <h3>Ficha de Matrícula</h3>
    </div>

    <div class="panel">
        <div class="panel-heading">Información de Matrícula</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-2"><strong>Número de Matrícula:</strong><p><?= htmlspecialchars(isset($data['numero_matricula']) ? $data['numero_matricula'] : 'N/A') ?></p></div>
                <div class="col-2"><strong>Fecha de Matrícula:</strong><p><?= htmlspecialchars(isset($data['fecha_matricula']) ? $data['fecha_matricula'] : 'N/A') ?></p></div>
            </div>
            <div class="row">
                <div class="col-2"><strong>¿Es Estudiante Nuevo?:</strong><p><?= htmlspecialchars(isset($data['nuevo']) ? $data['nuevo'] : 'N/A') ?></p></div>
                <div class="col-2"><strong>Estado de Matrícula:</strong><p><?= htmlspecialchars(isset($data['estado_matricula']) ? $data['estado_matricula'] : 'N/A') ?></p></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">Información del Estudiante</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-2"><strong>Nombres Completos:</strong><p><?= htmlspecialchars($data['estudiante_nombres'] . ' ' . $data['estudiante_apellidos']) ?></p></div>
                <div class="col-2"><strong>Documento:</strong><p><?= htmlspecialchars($data['estudiante_tipo_documento'] . ' - ' . $data['estudiante_documento']) ?></p></div>
            </div>
            <div class="row">
                <div class="col-3"><strong>Sexo:</strong><p><?= htmlspecialchars(isset($data['estudiante_sexo']) ? $data['estudiante_sexo'] : 'N/A') ?></p></div>
                <div class="col-3"><strong>RH:</strong><p><?= htmlspecialchars(isset($data['estudiante_rh']) ? $data['estudiante_rh'] : 'N/A') ?></p></div>
                <div class="col-3"><strong>Fecha de Nacimiento:</strong><p><?= htmlspecialchars(isset($data['estudiante_fecha_nacimiento']) ? $data['estudiante_fecha_nacimiento'] : 'N/A') ?></p></div>
            </div>
            <div class="row">
                <div class="col-3"><strong>Edad:</strong><p><?= isset($data['estudiante_edad']) ? $data['estudiante_edad'] . ' años' : 'N/A' ?></p></div>
                <div class="col-3"><strong>Teléfono:</strong><p><?= htmlspecialchars(isset($data['estudiante_telefono']) ? $data['estudiante_telefono'] : 'N/A') ?></p></div>
                <div class="col-3"><strong>Email:</strong><p><?= htmlspecialchars(isset($data['estudiante_email']) ? $data['estudiante_email'] : 'N/A') ?></p></div>
            </div>
            <div class="row">
                <div class="col-3"><strong>Código Estudiante:</strong><p><?= htmlspecialchars(isset($data['codigo_estudiante']) ? $data['codigo_estudiante'] : 'N/A') ?></p></div>
                <div class="col-3"><strong>Fecha de Ingreso:</strong><p><?= htmlspecialchars(isset($data['fecha_ingreso']) ? $data['fecha_ingreso'] : 'N/A') ?></p></div>
                <div class="col-3"><strong>Estado Año Anterior:</strong><p><?= htmlspecialchars(isset($data['estado_anio_anterior']) ? $data['estado_anio_anterior'] : 'N/A') ?></p></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">Información Académica</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-3"><strong>Jornada:</strong><p><?= htmlspecialchars(isset($data['jornada']) ? $data['jornada'] : 'N/A') ?></p></div>
                <div class="col-3"><strong>Grado:</strong><p><?= htmlspecialchars(isset($data['grado_completo']) ? $data['grado_completo'] : 'N/A') ?></p></div>
                <div class="col-3"><strong>Grupo:</strong><p><?= htmlspecialchars(isset($data['grupo_nombre']) ? $data['grupo_nombre'] : 'N/A') ?></p></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">Acudientes Asignados</div>
        <div class="panel-body">
            <?php if (!empty($data['acudientes'])): ?>
                <?php foreach ($data['acudientes'] as $acudiente): ?>
                    <div class="well">
                        <div class="row">
                            <div class="col-2">
                                <strong><?= htmlspecialchars($acudiente['acudiente_nombres'] . ' ' . $acudiente['acudiente_apellidos']) ?></strong>
                                (<?= htmlspecialchars($acudiente['parentesco']) ?>)
                            </div>
                            <div class="col-2" style="text-align: right;">
                                <strong><?= $acudiente['autorizado_recoger'] === 'Si' ? 'Autorizado para recoger' : 'No Autorizado para recoger' ?></strong>
                            </div>
                        </div>
                        <?php if (!empty($acudiente['observacion'])): ?>
                            <hr style="margin: 5px 0;">
                            <div class="row">
                                <div class="col-full">
                                    <strong>Observación:</strong>
                                    <p><?= htmlspecialchars($acudiente['observacion']) ?></p>
                                </div>
                                </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay acudientes registrados.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>