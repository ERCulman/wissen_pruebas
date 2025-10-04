-- Insertar acciones para el módulo de asistencia
INSERT INTO `acciones` VALUES 
(169,'asistencia_ver','Ver registros de asistencia','Asistencia','asistencia','Activo'),
(170,'asistencia_crear','Registrar asistencia de clase','Asistencia','asistencia','Activo'),
(171,'asistencia_editar','Editar registros de asistencia','Asistencia','asistencia','Activo'),
(172,'asistencia_justificar','Subir justificaciones de ausencias','Asistencia','asistencia','Activo'),
(173,'asistencia_revisar_justificaciones','Revisar y aprobar justificaciones','Asistencia','asistencia','Activo'),
(174,'asistencia_reportes','Generar reportes de asistencia','Asistencia','asistencia','Activo');

-- Asignar permisos a roles
-- Docentes (rol_id = 5)
INSERT INTO `roles_acciones` VALUES (5,169),(5,170),(5,171),(5,173);

-- Coordinadores (rol_id = 4)
INSERT INTO `roles_acciones` VALUES (4,169),(4,170),(4,171),(4,172),(4,173),(4,174);

-- Rectores (rol_id = 3)
INSERT INTO `roles_acciones` VALUES (3,169),(3,170),(3,171),(3,172),(3,173),(3,174);

-- Estudiantes (rol_id = 8) y Acudientes (rol_id = 7)
INSERT INTO `roles_acciones` VALUES (8,172),(7,172);

-- Superadministradores (rol_id = 1) y Administradores (rol_id = 2)
INSERT INTO `roles_acciones` VALUES (1,169),(1,170),(1,171),(1,172),(1,173),(1,174);
INSERT INTO `roles_acciones` VALUES (2,169),(2,170),(2,171),(2,172),(2,173),(2,174);