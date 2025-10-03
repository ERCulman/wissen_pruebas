<?php

/**
 * MAPA DE RUTAS PARA LAS VISTAS
 * * Define qué páginas (módulos) existen en la aplicación y cuál es
 * el permiso mínimo requerido para acceder a cada una.
 * 'publico' significa que solo requiere una sesión iniciada.
 */

return [
    // --- Rutas Públicas (no requieren un permiso específico) ---
    'inicio' => ['archivo' => 'inicio.php', 'permiso' => 'publico'],
    'salir' => ['archivo' => 'salir.php', 'permiso' => 'publico'],
    'acceso-denegado' => ['archivo' => 'acceso-denegado.php', 'permiso' => 'publico'],

    // --- Rutas Protegidas por Módulo ---
    // La convención es que el permiso para ver un módulo es "nombre-ruta_ver"
    'usuarios' => ['archivo' => 'usuarios.php', 'permiso' => 'usuarios_ver'],
    'institucion' => ['archivo' => 'institucion.php', 'permiso' => 'institucion_ver'],
    'sedes' => ['archivo' => 'sedes.php', 'permiso' => 'sedes_ver'],
    'niveleducativo' => ['archivo' => 'niveleducativo.php', 'permiso' => 'niveles_ver'],
    'jornadas' => ['archivo' => 'jornadas.php', 'permiso' => 'jornadas_ver'],
    'grados' => ['archivo' => 'grados.php', 'permiso' => 'grados_ver'],
    'cursos' => ['archivo' => 'cursos.php', 'permiso' => 'cursos_ver'],
    'oferta' => ['archivo' => 'oferta.php', 'permiso' => 'oferta_ver'],
    'periodos' => ['archivo' => 'periodos.php', 'permiso' => 'periodos_ver'],
    'estructura-curricular' => ['archivo' => 'estructura-curricular.php', 'permiso' => 'estructura-curricular_ver'],
    'matricula' => ['archivo' => 'matricula.php', 'permiso' => 'matricula_ver'],
    'gestionar-acciones' => ['archivo' => 'gestionar-acciones.php', 'permiso' => 'permisos_ver'],
    'gestionar-permisos' => ['archivo' => 'gestionar-permisos.php', 'permiso' => 'permisos_asignar'],
    'asignar-roles' => ['archivo' => 'asignar-roles.php', 'permiso' => 'roles_ver'],
    'asignacion-docente-asignaturas' => ['archivo' => 'asignacion-docente-asignaturas.php', 'permiso' => 'asignacion_docente_ver'],
    'asignacion-academica' => ['archivo' => 'asignacion-academica.php', 'permiso' => 'asignacion_academica_ver'],
    'perfil-profesional' => ['archivo' => 'perfil-profesional.php', 'permiso' => 'perfil_profesional_ver'],



    // NOTA: Tus otras rutas como "perfil-laboral", "estudiantes", etc.,
    // deberían ser agregadas aquí siguiendo el mismo formato. Por ejemplo:
    // 'estudiantes' => ['archivo' => 'estudiantes.php', 'permiso' => 'estudiantes_ver'],
];