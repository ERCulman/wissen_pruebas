-- Agregar campos de horario a la tabla asistencia_clase
-- Estos campos permitirán automatizar el cálculo de retrasos y cambio automático a ausente

ALTER TABLE `asistencia_clase` 
ADD COLUMN `hora_inicio_clase` TIME NOT NULL COMMENT 'Hora de inicio de la clase' AFTER `fecha`,
ADD COLUMN `hora_fin_clase` TIME NOT NULL COMMENT 'Hora de finalización de la clase' AFTER `hora_inicio_clase`;

-- Agregar índice para optimizar consultas por horario
ALTER TABLE `asistencia_clase` 
ADD INDEX `idx_horario_clase` (`fecha`, `hora_inicio_clase`, `hora_fin_clase`);