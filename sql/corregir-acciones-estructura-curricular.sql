-- CORRECCIÓN DE ACCIONES PARA ESTRUCTURA CURRICULAR
-- ===================================================

-- Agregar acciones específicas para Áreas como módulo independiente
INSERT IGNORE INTO acciones (nombre_accion, descripcion, modulo, modulo_asociado, estado) VALUES
-- Áreas
('areas_ver', 'Ver listado de áreas académicas', 'Áreas', 'estructura-curricular', 'Activo'),
('areas_crear', 'Crear nuevas áreas académicas', 'Áreas', 'estructura-curricular', 'Activo'),
('areas_editar', 'Editar áreas académicas existentes', 'Áreas', 'estructura-curricular', 'Activo'),
('areas_eliminar', 'Eliminar áreas académicas', 'Áreas', 'estructura-curricular', 'Activo'),

-- Asignaturas
('asignaturas_ver', 'Ver listado de asignaturas', 'Asignaturas', 'estructura-curricular', 'Activo'),
('asignaturas_crear', 'Crear nuevas asignaturas', 'Asignaturas', 'estructura-curricular', 'Activo'),
('asignaturas_editar', 'Editar asignaturas existentes', 'Asignaturas', 'estructura-curricular', 'Activo'),
('asignaturas_eliminar', 'Eliminar asignaturas', 'Asignaturas', 'estructura-curricular', 'Activo'),

-- Currículo (mantener las existentes pero con nombres más específicos)
('curriculo_ver', 'Ver currículos creados', 'Currículo', 'estructura-curricular', 'Activo'),
('curriculo_crear', 'Crear nuevos currículos', 'Currículo', 'estructura-curricular', 'Activo'),
('curriculo_editar', 'Editar currículos existentes', 'Currículo', 'estructura-curricular', 'Activo'),
('curriculo_eliminar', 'Eliminar currículos', 'Currículo', 'estructura-curricular', 'Activo');

-- Actualizar las acciones genéricas existentes para ser más específicas del currículo
UPDATE acciones SET 
    nombre_accion = 'curriculo_ver',
    descripcion = 'Ver currículos creados'
WHERE nombre_accion = 'estructura-curricular_ver';

UPDATE acciones SET 
    nombre_accion = 'curriculo_crear',
    descripcion = 'Crear nuevos currículos'
WHERE nombre_accion = 'estructura-curricular_crear';

UPDATE acciones SET 
    nombre_accion = 'curriculo_editar',
    descripcion = 'Editar currículos existentes'
WHERE nombre_accion = 'estructura-curricular_editar';

UPDATE acciones SET 
    nombre_accion = 'curriculo_eliminar',
    descripcion = 'Eliminar currículos'
WHERE nombre_accion = 'estructura-curricular_eliminar';