-- Verificar e insertar acciones para el módulo de Asignación Académica si no existen
INSERT IGNORE INTO `acciones` (`nombre_accion`, `descripcion`, `modulo`, `modulo_asociado`, `estado`) VALUES
('asignacion_academica_ver', 'Ver asignaciones académicas', 'Asignación Académica', 'asignacion-academica', 'Activo'),
('asignacion_academica_crear', 'Crear asignaciones académicas', 'Asignación Académica', 'asignacion-academica', 'Activo'),
('asignacion_academica_editar', 'Editar asignaciones académicas', 'Asignación Académica', 'asignacion-academica', 'Activo'),
('asignacion_academica_eliminar', 'Eliminar asignaciones académicas', 'Asignación Académica', 'asignacion-academica', 'Activo');

-- Asignar permisos a roles (Superadministrador, Administrador, Rector) si no existen
INSERT IGNORE INTO `roles_acciones` (`rol_id`, `accion_id`) 
SELECT r.id_rol, a.id 
FROM roles r, acciones a 
WHERE r.nombre_rol IN ('Superadministrador', 'Administrador', 'Rector') 
AND a.nombre_accion IN ('asignacion_academica_ver', 'asignacion_academica_crear', 'asignacion_academica_editar', 'asignacion_academica_eliminar')
AND NOT EXISTS (
    SELECT 1 FROM roles_acciones ra 
    WHERE ra.rol_id = r.id_rol AND ra.accion_id = a.id
);