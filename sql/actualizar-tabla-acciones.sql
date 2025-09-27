-- ACTUALIZACIÓN DE LA TABLA ACCIONES PARA SOPORTE ESCALABLE
-- =========================================================

-- Agregar columnas necesarias para el nuevo sistema
ALTER TABLE acciones 
ADD COLUMN IF NOT EXISTS modulo_asociado VARCHAR(50) NULL COMMENT 'Módulo/ruta asociada para protección automática',
ADD COLUMN IF NOT EXISTS estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo' COMMENT 'Estado de la acción';

-- Crear índices para mejor performance
CREATE INDEX IF NOT EXISTS idx_acciones_modulo_asociado ON acciones(modulo_asociado);
CREATE INDEX IF NOT EXISTS idx_acciones_estado ON acciones(estado);

-- Actualizar acciones existentes con módulos asociados
UPDATE acciones SET modulo_asociado = 'usuarios' WHERE nombre_accion LIKE 'usuarios_%';
UPDATE acciones SET modulo_asociado = 'roles' WHERE nombre_accion LIKE 'roles_%';
UPDATE acciones SET modulo_asociado = 'sedes' WHERE nombre_accion LIKE 'sedes_%';
UPDATE acciones SET modulo_asociado = 'institucion' WHERE nombre_accion LIKE 'institucion_%';
UPDATE acciones SET modulo_asociado = 'grados' WHERE nombre_accion LIKE 'grados_%';
UPDATE acciones SET modulo_asociado = 'cursos' WHERE nombre_accion LIKE 'cursos_%';
UPDATE acciones SET modulo_asociado = 'matricula' WHERE nombre_accion LIKE 'matricula_%';
UPDATE acciones SET modulo_asociado = 'gestionar-acciones' WHERE nombre_accion LIKE 'permisos_%';
UPDATE acciones SET modulo_asociado = 'gestionar-permisos' WHERE nombre_accion = 'permisos_asignar';
UPDATE acciones SET modulo_asociado = 'asignar-roles' WHERE nombre_accion = 'roles_asignar';

-- Insertar acciones estándar para módulos comunes si no existen
INSERT IGNORE INTO acciones (nombre_accion, descripcion, modulo, modulo_asociado, estado) VALUES
-- Usuarios
('usuarios_ver', 'Ver listado de usuarios', 'Usuarios', 'usuarios', 'Activo'),
('usuarios_crear', 'Crear nuevos usuarios', 'Usuarios', 'usuarios', 'Activo'),
('usuarios_editar', 'Editar usuarios existentes', 'Usuarios', 'usuarios', 'Activo'),
('usuarios_eliminar', 'Eliminar usuarios', 'Usuarios', 'usuarios', 'Activo'),

-- Estudiantes
('estudiantes_ver', 'Ver listado de estudiantes', 'Estudiantes', 'estudiantes', 'Activo'),
('estudiantes_crear', 'Crear nuevos estudiantes', 'Estudiantes', 'estudiantes', 'Activo'),
('estudiantes_editar', 'Editar estudiantes existentes', 'Estudiantes', 'estudiantes', 'Activo'),
('estudiantes_eliminar', 'Eliminar estudiantes', 'Estudiantes', 'estudiantes', 'Activo'),

-- Sedes
('sedes_ver', 'Ver listado de sedes', 'Sedes', 'sedes', 'Activo'),
('sedes_crear', 'Crear nuevas sedes', 'Sedes', 'sedes', 'Activo'),
('sedes_editar', 'Editar sedes existentes', 'Sedes', 'sedes', 'Activo'),
('sedes_eliminar', 'Eliminar sedes', 'Sedes', 'sedes', 'Activo'),

-- Grados
('grados_ver', 'Ver listado de grados', 'Grados', 'grados', 'Activo'),
('grados_crear', 'Crear nuevos grados', 'Grados', 'grados', 'Activo'),
('grados_editar', 'Editar grados existentes', 'Grados', 'grados', 'Activo'),
('grados_eliminar', 'Eliminar grados', 'Grados', 'grados', 'Activo'),

-- Cursos
('cursos_ver', 'Ver listado de cursos', 'Cursos', 'cursos', 'Activo'),
('cursos_crear', 'Crear nuevos cursos', 'Cursos', 'cursos', 'Activo'),
('cursos_editar', 'Editar cursos existentes', 'Cursos', 'cursos', 'Activo'),
('cursos_eliminar', 'Eliminar cursos', 'Cursos', 'cursos', 'Activo'),

-- Matrículas
('matricula_ver', 'Ver listado de matrículas', 'Matricula', 'matricula', 'Activo'),
('matricula_crear', 'Crear nuevas matrículas', 'Matricula', 'matricula', 'Activo'),
('matricula_editar', 'Editar matrículas existentes', 'Matricula', 'matricula', 'Activo'),
('matricula_eliminar', 'Eliminar matrículas', 'Matricula', 'matricula', 'Activo'),

-- Jornadas
('jornadas_ver', 'Ver listado de jornadas', 'Jornadas', 'jornadas', 'Activo'),
('jornadas_crear', 'Crear nuevas jornadas', 'Jornadas', 'jornadas', 'Activo'),
('jornadas_editar', 'Editar jornadas existentes', 'Jornadas', 'jornadas', 'Activo'),
('jornadas_eliminar', 'Eliminar jornadas', 'Jornadas', 'jornadas', 'Activo'),

-- Nivel Educativo
('niveleducativo_ver', 'Ver listado de niveles educativos', 'Nivel Educativo', 'niveleducativo', 'Activo'),
('niveleducativo_crear', 'Crear nuevos niveles educativos', 'Nivel Educativo', 'niveleducativo', 'Activo'),
('niveleducativo_editar', 'Editar niveles educativos existentes', 'Nivel Educativo', 'niveleducativo', 'Activo'),
('niveleducativo_eliminar', 'Eliminar niveles educativos', 'Nivel Educativo', 'niveleducativo', 'Activo'),

-- Períodos
('periodos_ver', 'Ver listado de períodos', 'Períodos', 'periodos', 'Activo'),
('periodos_crear', 'Crear nuevos períodos', 'Períodos', 'periodos', 'Activo'),
('periodos_editar', 'Editar períodos existentes', 'Períodos', 'periodos', 'Activo'),
('periodos_eliminar', 'Eliminar períodos', 'Períodos', 'periodos', 'Activo'),

-- Oferta Educativa
('oferta_ver', 'Ver listado de ofertas educativas', 'Oferta', 'oferta', 'Activo'),
('oferta_crear', 'Crear nuevas ofertas educativas', 'Oferta', 'oferta', 'Activo'),
('oferta_editar', 'Editar ofertas educativas existentes', 'Oferta', 'oferta', 'Activo'),
('oferta_eliminar', 'Eliminar ofertas educativas', 'Oferta', 'oferta', 'Activo'),

-- Estructura Curricular
('estructura-curricular_ver', 'Ver estructura curricular', 'Estructura Curricular', 'estructura-curricular', 'Activo'),
('estructura-curricular_crear', 'Crear estructura curricular', 'Estructura Curricular', 'estructura-curricular', 'Activo'),
('estructura-curricular_editar', 'Editar estructura curricular', 'Estructura Curricular', 'estructura-curricular', 'Activo'),
('estructura-curricular_eliminar', 'Eliminar estructura curricular', 'Estructura Curricular', 'estructura-curricular', 'Activo');

-- Establecer estado activo para todas las acciones existentes
UPDATE acciones SET estado = 'Activo' WHERE estado IS NULL;