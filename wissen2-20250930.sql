-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: wissen2
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Habilidades`
--

DROP TABLE IF EXISTS `Habilidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Habilidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Habilidades`
--

LOCK TABLES `Habilidades` WRITE;
/*!40000 ALTER TABLE `Habilidades` DISABLE KEYS */;
/*!40000 ALTER TABLE `Habilidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acciones`
--

DROP TABLE IF EXISTS `acciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_accion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `modulo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modulo_asociado` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Módulo/ruta asociada para protección automática',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo' COMMENT 'Estado de la acción',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_nombre_accion` (`nombre_accion`),
  KEY `idx_acciones_modulo_asociado` (`modulo_asociado`),
  KEY `idx_acciones_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acciones`
--

LOCK TABLES `acciones` WRITE;
/*!40000 ALTER TABLE `acciones` DISABLE KEYS */;
INSERT INTO `acciones` VALUES (1,'usuarios_crear','Crear nuevos usuarios','Usuarios','usuarios','Activo'),(2,'usuarios_ver','Ver lista de usuarios','Usuarios','usuarios','Activo'),(3,'usuarios_editar','Editar datos de usuarios','Usuarios','usuarios','Activo'),(4,'usuarios_eliminar','Eliminar usuarios','Usuarios','usuarios','Activo'),(5,'institucion_crear','Crear nueva institución','Institución','institucion','Activo'),(6,'institucion_ver','Ver datos de institución','Institución','institucion','Activo'),(7,'institucion_editar','Editar datos de institución','Institución','institucion','Activo'),(8,'sedes_crear','Crear nuevas sedes','Sedes','sedes','Activo'),(9,'sedes_ver','Ver lista de sedes','Sedes','sedes','Activo'),(10,'sedes_editar','Editar datos de sedes','Sedes','sedes','Activo'),(11,'sedes_eliminar','Eliminar sedes','Sedes','sedes','Activo'),(12,'jornadas_crear','Crear nuevas jornadas','Jornadas','jornadas','Activo'),(13,'jornadas_ver','Ver lista de jornadas','Jornadas','jornadas','Activo'),(14,'jornadas_editar','Editar jornadas','Jornadas','jornadas','Activo'),(15,'jornadas_eliminar','Eliminar jornadas','Jornadas','jornadas','Activo'),(16,'niveles_crear','Crear niveles educativos','Niveles Educativos','niveleducativo','Activo'),(17,'niveles_ver','Ver niveles educativos','Niveles Educativos','niveleducativo','Activo'),(18,'niveles_editar','Editar niveles educativos','Niveles Educativos','niveleducativo','Activo'),(19,'niveles_eliminar','Eliminar niveles educativos','Niveles Educativos','niveleducativo','Activo'),(20,'grados_crear','Crear nuevos grados','Grados','grados','Activo'),(21,'grados_ver','Ver lista de grados','Grados','grados','Activo'),(22,'grados_editar','Editar grados','Grados','grados','Activo'),(23,'grados_eliminar','Eliminar grados','Grados','grados','Activo'),(24,'cursos_crear','Crear nuevos cursos','Cursos','cursos','Activo'),(25,'cursos_ver','Ver lista de cursos','Cursos','cursos','Activo'),(26,'cursos_editar','Editar cursos','Cursos','cursos','Activo'),(27,'cursos_eliminar','Eliminar cursos','Cursos','cursos','Activo'),(28,'oferta_crear','Crear oferta educativa','Oferta Educativa','oferta','Activo'),(29,'oferta_ver','Ver oferta educativa','Oferta Educativa','oferta','Activo'),(30,'oferta_editar','Editar oferta educativa','Oferta Educativa','oferta','Activo'),(31,'oferta_eliminar','Eliminar oferta educativa','Oferta Educativa','oferta','Activo'),(32,'matricula_crear','Registrar nueva matrícula','Matrícula','matricula','Activo'),(33,'matricula_ver','Ver matrículas','Matrícula','matricula','Activo'),(34,'matricula_editar','Editar matrícula','Matrícula','matricula','Activo'),(35,'matricula_eliminar','Eliminar matrícula','Matrícula','matricula','Activo'),(36,'estructura-curricular_crear_area','Crear estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(37,'estructura-curricular_ver_area','Ver estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(38,'estructura-curricular_editar_area','Editar estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(39,'estructura-curricular_eliminar_area','Eliminar estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(40,'periodos_crear','Crear períodos académicos','Períodos','periodos','Activo'),(41,'periodos_ver','Ver períodos académicos','Períodos','periodos','Activo'),(42,'periodos_editar','Editar períodos académicos','Períodos','periodos','Activo'),(43,'periodos_eliminar','Eliminar períodos académicos','Períodos','periodos','Activo'),(44,'roles_crear','Crear nuevos roles','Gestión de Roles','asignar-roles','Activo'),(45,'roles_ver','Ver roles del sistema','Gestión de Roles','asignar-roles','Activo'),(46,'roles_editar','Editar roles','Gestión de Roles','asignar-roles','Activo'),(47,'permisos_asignar','Asignar permisos a roles','Roles y Permisos','gestionar-permisos','Activo'),(48,'permisos_ver','Ver permisos del sistema','Roles y Permisos','gestionar-acciones','Activo'),(49,'reportes_generar','Generar reportes','Reportes',NULL,'Activo'),(50,'reportes_ver','Ver reportes generados','Reportes',NULL,'Activo'),(51,'reportes_exportar','Exportar reportes','Reportes',NULL,'Activo'),(56,'estudiantes_ver','Ver listado de estudiantes','Estudiantes','estudiantes','Activo'),(57,'estudiantes_crear','Crear nuevos estudiantes','Estudiantes','estudiantes','Activo'),(58,'estudiantes_editar','Editar estudiantes existentes','Estudiantes','estudiantes','Activo'),(59,'estudiantes_eliminar','Eliminar estudiantes','Estudiantes','estudiantes','Activo'),(80,'niveleducativo_ver','Ver listado de niveles educativos','Nivel Educativo','niveleducativo','Activo'),(81,'niveleducativo_crear','Crear nuevos niveles educativos','Nivel Educativo','niveleducativo','Activo'),(82,'niveleducativo_editar','Editar niveles educativos existentes','Nivel Educativo','niveleducativo','Activo'),(83,'niveleducativo_eliminar','Eliminar niveles educativos','Nivel Educativo','niveleducativo','Activo'),(92,'estructura-curricular_ver','Ver estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(93,'estructura-curricular_crear','Crear estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(94,'estructura-curricular_editar','Editar estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(95,'estructura-curricular_eliminar','Eliminar estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(140,'usuarios_cambiar_estado','Activar/Desactivar usuarios','Gestión de Usuarios','usuarios','Activo'),(141,'matricula_reportes','Generar reportes de matrícula','Gestión de Matrículas','matricula','Activo'),(142,'permisos_crear','Crear nuevos permisos','Gestión de Acciones','gestionar-acciones','Activo'),(143,'permisos_editar','Editar permisos','Gestión de Acciones','gestionar-acciones','Activo'),(144,'permisos_eliminar','Eliminar permisos','Gestión de Acciones','gestionar-acciones','Activo'),(145,'permisos_revocar','Revocar permisos de roles','Asignación de Permisos','gestionar-permisos','Activo'),(146,'roles_asignar','Asignar roles a usuarios','Gestión de Roles','asignar-roles','Activo'),(147,'roles_eliminar','Eliminar roles','Gestión de Roles','asignar-roles','Activo'),(148,'sistema_sincronizar','Sincronizar sistema de permisos','Sincronización del Sistema','sincronizar-permisos','Activo'),(149,'sistema_diagnosticar','Diagnosticar problemas del sistema','Sincronización del Sistema','sincronizar-permisos','Activo'),(150,'sistema_reparar','Reparar sistema automáticamente','Sincronización del Sistema','sincronizar-permisos','Activo'),(151,'curriculo_crear','Crear estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(152,'curriculo_ver','Ver estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(153,'curriculo_editar','Editar estructura curricular','Estructura Curricular','estructura-curricular','Activo'),(154,'curriculo_eliminar','Eliminar estructura curricular','Estructura Curricular','estructura-curricular','Activo');
/*!40000 ALTER TABLE `acciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `administradores_sistema`
--

DROP TABLE IF EXISTS `administradores_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administradores_sistema` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `rol_id` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  `autorizado_por` int NOT NULL,
  `fecha_autorizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_rol_unico` (`usuario_id`,`rol_id`),
  KEY `fk_admin_sistema_rol` (`rol_id`),
  KEY `fk_admin_sistema_autorizado` (`autorizado_por`),
  CONSTRAINT `fk_admin_sistema_autorizado` FOREIGN KEY (`autorizado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_admin_sistema_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_admin_sistema_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administradores_sistema`
--

LOCK TABLES `administradores_sistema` WRITE;
/*!40000 ALTER TABLE `administradores_sistema` DISABLE KEYS */;
INSERT INTO `administradores_sistema` VALUES (1,14,1,'2025-09-26',NULL,'Activo',14,'2025-09-26 21:42:32'),(2,5,2,'2025-09-27',NULL,'Activo',14,'2025-09-27 01:42:27');
/*!40000 ALTER TABLE `administradores_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `anio_lectivo`
--

DROP TABLE IF EXISTS `anio_lectivo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anio_lectivo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `anio` year NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anio_lectivo`
--

LOCK TABLES `anio_lectivo` WRITE;
/*!40000 ALTER TABLE `anio_lectivo` DISABLE KEYS */;
INSERT INTO `anio_lectivo` VALUES (1,2025);
/*!40000 ALTER TABLE `anio_lectivo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

LOCK TABLES `area` WRITE;
/*!40000 ALTER TABLE `area` DISABLE KEYS */;
INSERT INTO `area` VALUES (1,'Matemáticas'),(2,'Sociales');
/*!40000 ALTER TABLE `area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignacion_academica`
--

DROP TABLE IF EXISTS `asignacion_academica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_academica` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cuerpo_docente_id` int NOT NULL,
  `estructura_curricular_id` int NOT NULL,
  `grupo_id` int NOT NULL,
  `periodo_academico_id` int NOT NULL,
  `intensidad_horaria_semanal` int DEFAULT NULL,
  `estado` enum('Activa','Planeada','Finalizada','Cancelada') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Activa',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_asignacion_unica` (`cuerpo_docente_id`,`estructura_curricular_id`,`grupo_id`,`periodo_academico_id`),
  KEY `cuerpo_docente_id` (`cuerpo_docente_id`),
  KEY `estructura_curricular_id` (`estructura_curricular_id`),
  KEY `grupo_id` (`grupo_id`),
  KEY `periodo_academico_id` (`periodo_academico_id`),
  CONSTRAINT `asignacion_academica_ibfk_1` FOREIGN KEY (`cuerpo_docente_id`) REFERENCES `cuerpo_docente` (`id`),
  CONSTRAINT `asignacion_academica_ibfk_2` FOREIGN KEY (`estructura_curricular_id`) REFERENCES `estructura_curricular` (`id`),
  CONSTRAINT `asignacion_academica_ibfk_3` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`),
  CONSTRAINT `asignacion_academica_ibfk_4` FOREIGN KEY (`periodo_academico_id`) REFERENCES `periodo` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_academica`
--

LOCK TABLES `asignacion_academica` WRITE;
/*!40000 ALTER TABLE `asignacion_academica` DISABLE KEYS */;
/*!40000 ALTER TABLE `asignacion_academica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignacion_acudiente`
--

DROP TABLE IF EXISTS `asignacion_acudiente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_acudiente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricula_id` int NOT NULL,
  `roles_institucionales_id` int NOT NULL,
  `parentesco` enum('Padre','Madre','Tío','Tía','Abuelo','Abuela','Otro') COLLATE utf8mb4_general_ci NOT NULL,
  `es_firmante_principal` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'No',
  `autorizado_recoger` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'No',
  `campo_firma` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_matricula_acudiente` (`matricula_id`,`roles_institucionales_id`),
  KEY `fk_asignacion_rol_institucional` (`roles_institucionales_id`),
  CONSTRAINT `fk_asignacion_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matricula` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_asignacion_rol_institucional` FOREIGN KEY (`roles_institucionales_id`) REFERENCES `roles_institucionales` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_acudiente`
--

LOCK TABLES `asignacion_acudiente` WRITE;
/*!40000 ALTER TABLE `asignacion_acudiente` DISABLE KEYS */;
INSERT INTO `asignacion_acudiente` VALUES (6,2,2,'Padre','Si','Si',NULL,'');
/*!40000 ALTER TABLE `asignacion_acudiente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignatura`
--

DROP TABLE IF EXISTS `asignatura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignatura` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `area_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `area_id` (`area_id`),
  CONSTRAINT `asignatura_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignatura`
--

LOCK TABLES `asignatura` WRITE;
/*!40000 ALTER TABLE `asignatura` DISABLE KEYS */;
INSERT INTO `asignatura` VALUES (1,'Geometría',1),(5,'Calculo',1),(8,'Estadistica',1);
/*!40000 ALTER TABLE `asignatura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificaciones`
--

DROP TABLE IF EXISTS `certificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perfil_id` int NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_entidad_emitente` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `codigo_verificacion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url_credencial` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `perfil_id` (`perfil_id`),
  CONSTRAINT `certificaciones_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil_profesional` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificaciones`
--

LOCK TABLES `certificaciones` WRITE;
/*!40000 ALTER TABLE `certificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `certificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competencia`
--

DROP TABLE IF EXISTS `competencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `estructura_curricular_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `estructura_curricular_id` (`estructura_curricular_id`),
  CONSTRAINT `competencia_ibfk_1` FOREIGN KEY (`estructura_curricular_id`) REFERENCES `estructura_curricular` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competencia`
--

LOCK TABLES `competencia` WRITE;
/*!40000 ALTER TABLE `competencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `competencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuerpo_docente`
--

DROP TABLE IF EXISTS `cuerpo_docente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuerpo_docente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_institucional_id` int NOT NULL,
  `escalafon_docente` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Escalafón según normativa educativa',
  `max_horas_academicas_semanales` int NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rol_institucional_unico` (`rol_institucional_id`),
  CONSTRAINT `fk_cuerpo_docente_rol_institucional` FOREIGN KEY (`rol_institucional_id`) REFERENCES `roles_institucionales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuerpo_docente`
--

LOCK TABLES `cuerpo_docente` WRITE;
/*!40000 ALTER TABLE `cuerpo_docente` DISABLE KEYS */;
/*!40000 ALTER TABLE `cuerpo_docente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuerpo_docente_estados`
--

DROP TABLE IF EXISTS `cuerpo_docente_estados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuerpo_docente_estados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cuerpo_docente_id` int NOT NULL,
  `estado` enum('Activo','Licencia','Suspendido','Vacaciones','Incapacidad') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `documento_soporte` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_estados_cuerpo_docente` (`cuerpo_docente_id`),
  CONSTRAINT `fk_estados_cuerpo_docente` FOREIGN KEY (`cuerpo_docente_id`) REFERENCES `cuerpo_docente` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuerpo_docente_estados`
--

LOCK TABLES `cuerpo_docente_estados` WRITE;
/*!40000 ALTER TABLE `cuerpo_docente_estados` DISABLE KEYS */;
/*!40000 ALTER TABLE `cuerpo_docente_estados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `curso`
--

DROP TABLE IF EXISTS `curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `curso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` enum('Númerico','Alfabético') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `curso`
--

LOCK TABLES `curso` WRITE;
/*!40000 ALTER TABLE `curso` DISABLE KEYS */;
INSERT INTO `curso` VALUES (1,'Númerico','001'),(6,'Númerico','02');
/*!40000 ALTER TABLE `curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docente_asignaturas_habilitadas`
--

DROP TABLE IF EXISTS `docente_asignaturas_habilitadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docente_asignaturas_habilitadas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cuerpo_docente_id` int NOT NULL,
  `asignatura_estructura_curricular_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_docente_estructura` (`cuerpo_docente_id`,`asignatura_estructura_curricular_id`),
  KEY `fk_habilitadas_estructura` (`asignatura_estructura_curricular_id`),
  CONSTRAINT `fk_habilitadas_cuerpo_docente` FOREIGN KEY (`cuerpo_docente_id`) REFERENCES `cuerpo_docente` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_habilitadas_estructura` FOREIGN KEY (`asignatura_estructura_curricular_id`) REFERENCES `estructura_curricular` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docente_asignaturas_habilitadas`
--

LOCK TABLES `docente_asignaturas_habilitadas` WRITE;
/*!40000 ALTER TABLE `docente_asignaturas_habilitadas` DISABLE KEYS */;
/*!40000 ALTER TABLE `docente_asignaturas_habilitadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perfil_id` int NOT NULL,
  `tipo_soporte` enum('Diploma','Certificado','Certificación Laboral','Otro') COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_mime_verificado` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_almacenamiento` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL,
  `tamanio_en_bytes` bigint DEFAULT NULL,
  `entidad_relacionada` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_entidad_relacionada` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `perfil_id` (`perfil_id`),
  CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil_profesional` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos`
--

LOCK TABLES `documentos` WRITE;
/*!40000 ALTER TABLE `documentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado`
--

DROP TABLE IF EXISTS `estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `estado` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado`
--

LOCK TABLES `estado` WRITE;
/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` VALUES (1,'Activo'),(2,'Inactivo'),(3,'Matriculado'),(4,'Retirado'),(5,'Trasladado'),(6,'Promocionado'),(7,'Aprobado'),(8,'Reprobado'),(9,'Pendiente'),(10,'Presente'),(11,'Ausente');
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estructura_curricular`
--

DROP TABLE IF EXISTS `estructura_curricular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estructura_curricular` (
  `id` int NOT NULL AUTO_INCREMENT,
  `intensidad_horaria_semanal` int NOT NULL,
  `oferta_academica_id` int NOT NULL,
  `asignatura_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `oferta_academica_id` (`oferta_academica_id`),
  KEY `asignatura_id` (`asignatura_id`),
  CONSTRAINT `estructura_curricular_ibfk_1` FOREIGN KEY (`oferta_academica_id`) REFERENCES `oferta_academica` (`id`),
  CONSTRAINT `estructura_curricular_ibfk_2` FOREIGN KEY (`asignatura_id`) REFERENCES `asignatura` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estructura_curricular`
--

LOCK TABLES `estructura_curricular` WRITE;
/*!40000 ALTER TABLE `estructura_curricular` DISABLE KEYS */;
INSERT INTO `estructura_curricular` VALUES (4,3,18,5),(10,12,18,8);
/*!40000 ALTER TABLE `estructura_curricular` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `experiencia_laboral`
--

DROP TABLE IF EXISTS `experiencia_laboral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `experiencia_laboral` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perfil_id` int NOT NULL,
  `nombre_empresa` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `cargo_ocupado` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `trabajo_actual` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL,
  `responsabilidades` text COLLATE utf8mb4_general_ci,
  `nombre_jefe` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pais_empresa` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ciudad_empresa` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono_contacto` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email_contacto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website_empresa` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `perfil_id` (`perfil_id`),
  CONSTRAINT `experiencia_laboral_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil_profesional` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `experiencia_laboral`
--

LOCK TABLES `experiencia_laboral` WRITE;
/*!40000 ALTER TABLE `experiencia_laboral` DISABLE KEYS */;
/*!40000 ALTER TABLE `experiencia_laboral` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grado`
--

DROP TABLE IF EXISTS `grado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grado` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nivel_educativo_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_nivel_educativo_grado` (`nivel_educativo_id`),
  CONSTRAINT `fk_nivel_educativo_grado` FOREIGN KEY (`nivel_educativo_id`) REFERENCES `nivel_educativo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grado`
--

LOCK TABLES `grado` WRITE;
/*!40000 ALTER TABLE `grado` DISABLE KEYS */;
INSERT INTO `grado` VALUES (2,'2','Segundo',2);
/*!40000 ALTER TABLE `grado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupo`
--

DROP TABLE IF EXISTS `grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `oferta_educativa_id` int NOT NULL,
  `curso_id` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `cupos` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `oferta_educativa_id` (`oferta_educativa_id`),
  KEY `curso_id` (`curso_id`),
  CONSTRAINT `grupo_ibfk_1` FOREIGN KEY (`oferta_educativa_id`) REFERENCES `oferta_academica` (`id`),
  CONSTRAINT `grupo_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `curso` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupo`
--

LOCK TABLES `grupo` WRITE;
/*!40000 ALTER TABLE `grupo` DISABLE KEYS */;
INSERT INTO `grupo` VALUES (8,18,1,'Segundo 001 - 25 Cupos',25),(12,19,1,'Segundo 001 - 25 Cupos',25);
/*!40000 ALTER TABLE `grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habilidades_perfil_profesional`
--

DROP TABLE IF EXISTS `habilidades_perfil_profesional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habilidades_perfil_profesional` (
  `id` int NOT NULL AUTO_INCREMENT,
  `habilidad_id` int NOT NULL,
  `perfil_id` int NOT NULL,
  `nivel_dominio` enum('Básico','Intermedio','Avanzado') COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `perfil_habilidad_unica` (`perfil_id`,`habilidad_id`),
  KEY `habilidad_id` (`habilidad_id`),
  KEY `perfil_id` (`perfil_id`),
  CONSTRAINT `habilidades_perfil_profesional_ibfk_1` FOREIGN KEY (`habilidad_id`) REFERENCES `Habilidades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `habilidades_perfil_profesional_ibfk_2` FOREIGN KEY (`perfil_id`) REFERENCES `perfil_profesional` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habilidades_perfil_profesional`
--

LOCK TABLES `habilidades_perfil_profesional` WRITE;
/*!40000 ALTER TABLE `habilidades_perfil_profesional` DISABLE KEYS */;
/*!40000 ALTER TABLE `habilidades_perfil_profesional` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_educativo`
--

DROP TABLE IF EXISTS `historial_educativo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_educativo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perfil_id` int NOT NULL,
  `nivel_educativo` enum('Primaria','Bachillerato','Técnico','Tecnólogo','Pregrado','Especialización','maestría','Doctorado') COLLATE utf8mb4_general_ci NOT NULL,
  `titulo_obtenido` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_institucion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estudio_terminado` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL,
  `estudio_en_progreso` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL,
  `ciudad_institucion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pais_institucion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `perfil_id` (`perfil_id`),
  CONSTRAINT `historial_educativo_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil_profesional` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_educativo`
--

LOCK TABLES `historial_educativo` WRITE;
/*!40000 ALTER TABLE `historial_educativo` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_educativo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institucion`
--

DROP TABLE IF EXISTS `institucion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `institucion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `codigo_dane` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `nit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `resolucion_creacion` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cantidad_sedes` int NOT NULL,
  `id_usuario_representante` int NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_registro` timestamp NULL DEFAULT NULL,
  `fecha_actualización` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institucion`
--

LOCK TABLES `institucion` WRITE;
/*!40000 ALTER TABLE `institucion` DISABLE KEYS */;
INSERT INTO `institucion` VALUES (1,'Institución Educativa Akasha','29957200396','1005083859','Resolución 325 de 2018','DG 82G 73A 80','akasha@akasha.com','3223394085',3,14,1,NULL,NULL),(2,'Institución Educativa Jupiter','29957200368','1005083855','Resolución 325 de 2010','DG 82G 73A 80','jupiter@juiter.com','3223394065',5,10,1,NULL,NULL),(3,'Institucion Educativa Internado Güerima','299572000392','1005783850','015 del 25 de febrero de 1979','Güerima','guerima@guerima.com','3152365894',4,11,0,NULL,NULL),(4,'Colombofrancés','299572000264','1005783850','Resolución 325 de 2010','DG 82G 73A 80','colombofrances@colombo.com','3223394080',1,10,1,NULL,NULL);
/*!40000 ALTER TABLE `institucion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jornada`
--

DROP TABLE IF EXISTS `jornada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jornada` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jornada`
--

LOCK TABLES `jornada` WRITE;
/*!40000 ALTER TABLE `jornada` DISABLE KEYS */;
INSERT INTO `jornada` VALUES (2,'MA','Mañana');
/*!40000 ALTER TABLE `jornada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matricula`
--

DROP TABLE IF EXISTS `matricula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matricula` (
  `id` int NOT NULL AUTO_INCREMENT,
  `roles_institucionales_id` int NOT NULL,
  `grupo_id` int NOT NULL,
  `sede_jornada_id` int NOT NULL,
  `numero_matricula` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_matricula` date NOT NULL,
  `nuevo` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL,
  `repitente` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL,
  `estado_matricula` enum('Matriculado','Retirado','Aprobado','Reprobado','Promocionado') COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_numero_por_sede_jornada` (`sede_jornada_id`,`numero_matricula`),
  KEY `fk_matricula_rol_institucional` (`roles_institucionales_id`),
  KEY `fk_matricula_grupo` (`grupo_id`),
  CONSTRAINT `fk_matricula_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupo` (`id`),
  CONSTRAINT `fk_matricula_rol_institucional` FOREIGN KEY (`roles_institucionales_id`) REFERENCES `roles_institucionales` (`id`),
  CONSTRAINT `fk_matricula_sede_jornada` FOREIGN KEY (`sede_jornada_id`) REFERENCES `sede_jornada` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matricula`
--

LOCK TABLES `matricula` WRITE;
/*!40000 ALTER TABLE `matricula` DISABLE KEYS */;
INSERT INTO `matricula` VALUES (2,1,12,4,'2025-1-0002','2025-02-10','Si','Si','Matriculado');
/*!40000 ALTER TABLE `matricula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nivel_educativo`
--

DROP TABLE IF EXISTS `nivel_educativo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nivel_educativo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel_educativo`
--

LOCK TABLES `nivel_educativo` WRITE;
/*!40000 ALTER TABLE `nivel_educativo` DISABLE KEYS */;
INSERT INTO `nivel_educativo` VALUES (2,'PRI','Primaria');
/*!40000 ALTER TABLE `nivel_educativo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oferta_academica`
--

DROP TABLE IF EXISTS `oferta_academica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oferta_academica` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grado_id` int NOT NULL,
  `sede_jornada_id` int NOT NULL,
  `anio_lectivo_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `grado_id` (`grado_id`),
  KEY `sede_jornada_id` (`sede_jornada_id`),
  KEY `anio_lectivo_id` (`anio_lectivo_id`),
  CONSTRAINT `oferta_academica_ibfk_1` FOREIGN KEY (`grado_id`) REFERENCES `grado` (`id`),
  CONSTRAINT `oferta_academica_ibfk_2` FOREIGN KEY (`sede_jornada_id`) REFERENCES `sede_jornada` (`id`),
  CONSTRAINT `oferta_academica_ibfk_3` FOREIGN KEY (`anio_lectivo_id`) REFERENCES `anio_lectivo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oferta_academica`
--

LOCK TABLES `oferta_academica` WRITE;
/*!40000 ALTER TABLE `oferta_academica` DISABLE KEYS */;
INSERT INTO `oferta_academica` VALUES (18,2,1,1),(19,2,4,1);
/*!40000 ALTER TABLE `oferta_academica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfil_profesional`
--

DROP TABLE IF EXISTS `perfil_profesional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perfil_profesional` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `titulo_profesional` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `resumen_profesional` text COLLATE utf8mb4_general_ci,
  `url_perfil_linkedin` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url_portafolio_personal` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado_disponibilidad` enum('Busqueda activa','Abierto a nuevas oportunidades','Disponible','No disponible') COLLATE utf8mb4_general_ci NOT NULL,
  `dispuesto_a_reubicarse` enum('Si','No') COLLATE utf8mb4_general_ci NOT NULL,
  `expectativa_salarial` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfil_profesional`
--

LOCK TABLES `perfil_profesional` WRITE;
/*!40000 ALTER TABLE `perfil_profesional` DISABLE KEYS */;
/*!40000 ALTER TABLE `perfil_profesional` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodo`
--

DROP TABLE IF EXISTS `periodo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periodo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `anio_lectivo_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `anio_lectivo_id` (`anio_lectivo_id`),
  CONSTRAINT `periodo_ibfk_1` FOREIGN KEY (`anio_lectivo_id`) REFERENCES `anio_lectivo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodo`
--

LOCK TABLES `periodo` WRITE;
/*!40000 ALTER TABLE `periodo` DISABLE KEYS */;
INSERT INTO `periodo` VALUES (2,'Periodo I','2025-02-10','2025-04-15',1);
/*!40000 ALTER TABLE `periodo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos_especiales`
--

DROP TABLE IF EXISTS `permisos_especiales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos_especiales` (
  `usuario_id` int NOT NULL,
  `accion_id` int NOT NULL,
  `sede_jornada_id` int NOT NULL,
  PRIMARY KEY (`usuario_id`,`accion_id`,`sede_jornada_id`),
  KEY `accion_id` (`accion_id`),
  KEY `sede_jornada_id` (`sede_jornada_id`),
  CONSTRAINT `permisos_especiales_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `permisos_especiales_ibfk_2` FOREIGN KEY (`accion_id`) REFERENCES `acciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permisos_especiales_ibfk_3` FOREIGN KEY (`sede_jornada_id`) REFERENCES `sede_jornada` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos_especiales`
--

LOCK TABLES `permisos_especiales` WRITE;
/*!40000 ALTER TABLE `permisos_especiales` DISABLE KEYS */;
INSERT INTO `permisos_especiales` VALUES (16,32,4),(38,37,1),(38,92,1);
/*!40000 ALTER TABLE `permisos_especiales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Superadministrador'),(2,'Administrador'),(3,'Rector'),(4,'Coordinador'),(5,'Docente'),(6,'Administrativo'),(7,'Acudiente'),(8,'Estudiante'),(9,'Exalumno');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_acciones`
--

DROP TABLE IF EXISTS `roles_acciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_acciones` (
  `rol_id` int NOT NULL,
  `accion_id` int NOT NULL,
  PRIMARY KEY (`rol_id`,`accion_id`),
  KEY `accion_id` (`accion_id`),
  CONSTRAINT `roles_acciones_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE,
  CONSTRAINT `roles_acciones_ibfk_2` FOREIGN KEY (`accion_id`) REFERENCES `acciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_acciones`
--

LOCK TABLES `roles_acciones` WRITE;
/*!40000 ALTER TABLE `roles_acciones` DISABLE KEYS */;
INSERT INTO `roles_acciones` VALUES (1,1),(2,1),(3,1),(1,2),(2,2),(3,2),(4,2),(1,3),(2,3),(3,3),(1,4),(2,4),(1,5),(2,5),(1,6),(2,6),(3,6),(1,7),(2,7),(3,7),(1,8),(2,8),(3,8),(1,9),(2,9),(3,9),(1,10),(2,10),(3,10),(1,11),(2,11),(3,11),(1,12),(2,12),(1,13),(2,13),(1,14),(2,14),(1,15),(2,15),(1,16),(2,16),(1,17),(2,17),(1,18),(2,18),(1,19),(2,19),(1,20),(2,20),(1,21),(2,21),(4,21),(5,21),(1,22),(2,22),(1,23),(2,23),(1,24),(2,24),(1,25),(2,25),(4,25),(5,25),(1,26),(2,26),(1,27),(2,27),(1,28),(2,28),(3,28),(6,28),(1,29),(2,29),(3,29),(6,29),(1,30),(2,30),(3,30),(6,30),(1,31),(2,31),(3,31),(6,31),(1,32),(2,32),(3,32),(4,32),(6,32),(1,33),(2,33),(3,33),(4,33),(5,33),(6,33),(7,33),(1,34),(2,34),(3,34),(4,34),(6,34),(1,35),(2,35),(1,36),(2,36),(3,36),(1,37),(2,37),(3,37),(6,37),(1,38),(2,38),(3,38),(1,39),(2,39),(3,39),(1,40),(2,40),(3,40),(6,40),(1,41),(2,41),(3,41),(6,41),(1,42),(2,42),(3,42),(6,42),(1,43),(2,43),(3,43),(6,43),(1,44),(2,44),(1,45),(2,45),(3,45),(1,46),(2,46),(1,47),(2,47),(3,47),(1,48),(2,48),(3,48),(1,49),(2,49),(3,49),(1,50),(2,50),(3,50),(7,50),(1,51),(2,51),(3,51),(1,56),(2,56),(1,57),(2,57),(1,58),(2,58),(1,59),(2,59),(1,80),(2,80),(1,81),(2,81),(1,82),(2,82),(1,83),(2,83),(1,92),(2,92),(4,92),(6,92),(1,93),(2,93),(1,94),(2,94),(1,95),(2,95),(1,140),(2,140),(1,141),(2,141),(1,142),(2,142),(1,143),(2,143),(1,144),(2,144),(1,145),(2,145),(1,146),(2,146),(1,147),(2,147),(1,148),(2,148),(1,149),(2,149),(1,150),(2,150);
/*!40000 ALTER TABLE `roles_acciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_institucionales`
--

DROP TABLE IF EXISTS `roles_institucionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_institucionales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `rol_id` int NOT NULL,
  `sede_id` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_rol_id` (`rol_id`),
  KEY `idx_sede_id` (`sede_id`),
  CONSTRAINT `fk_vinculo_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_vinculo_sede` FOREIGN KEY (`sede_id`) REFERENCES `sede` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_vinculo_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_institucionales`
--

LOCK TABLES `roles_institucionales` WRITE;
/*!40000 ALTER TABLE `roles_institucionales` DISABLE KEYS */;
INSERT INTO `roles_institucionales` VALUES (1,10,8,2,'2025-02-10',NULL,'Activo'),(2,29,7,2,'2025-02-10',NULL,'Activo'),(3,14,3,1,'2025-09-27',NULL,'Activo'),(5,17,8,1,'2025-09-27',NULL,'Activo'),(6,19,8,1,'2025-09-27',NULL,'Activo'),(7,21,8,1,'2025-09-27',NULL,'Activo'),(8,31,8,2,'2025-09-27',NULL,'Activo'),(9,34,8,2,'2025-09-27',NULL,'Activo'),(10,29,6,1,'2025-09-27',NULL,'Activo'),(11,38,7,1,'2025-09-27',NULL,'Activo'),(12,16,5,2,'2025-09-27',NULL,'Activo'),(13,22,5,2,'2025-09-27',NULL,'Activo'),(14,16,7,1,'2025-09-27',NULL,'Activo');
/*!40000 ALTER TABLE `roles_institucionales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sede`
--

DROP TABLE IF EXISTS `sede`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sede` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_sede` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_sede` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_sede` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `codigo_dane` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `consecutivo_dane` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `resolucion_creacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_creacion_sede` date NOT NULL,
  `direccion` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono_sede` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `celular_sede` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `institucion_id` int DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '0',
  `fecha_registro` timestamp NULL DEFAULT NULL,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_institucion_id` (`institucion_id`),
  CONSTRAINT `fk_institucion_id` FOREIGN KEY (`institucion_id`) REFERENCES `institucion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sede`
--

LOCK TABLES `sede` WRITE;
/*!40000 ALTER TABLE `sede` DISABLE KEYS */;
INSERT INTO `sede` VALUES (1,'1','Principal','Institución Educativa Akasha Sede Principal','299572000392','29957200039201','018 de  l 25 de Julio de 2009','2008-02-04','DG 82G 73A 80','3102563589','3105268945',1,1,'2025-07-16 05:00:00','2025-08-19 05:00:00'),(2,'1','Principal','ColomboFrances Sede Principal','299572000393','29957200039201','018 de  l 25 de Julio de 2008','2021-02-01','DG 82G 73A 80','3102563589','3105268945',4,1,'2025-08-03 05:00:00','2025-08-03 05:00:00'),(3,'2','Adscrita','ColomboFrances Mi Casita','299572000395','2995720039202','018 de  l 25 de Julio de 2010','2010-07-18','DG 82G 73A 80','3102563589','3105268945',4,0,'2025-08-19 05:00:00','2025-09-28 05:00:00');
/*!40000 ALTER TABLE `sede` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sede_jornada`
--

DROP TABLE IF EXISTS `sede_jornada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sede_jornada` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sede_id` int NOT NULL,
  `jornada_id` int NOT NULL,
  `anio_lectivo_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sede_jornada_unique` (`sede_id`,`jornada_id`,`anio_lectivo_id`),
  KEY `jornada_id` (`jornada_id`),
  KEY `anio_lectivo_id` (`anio_lectivo_id`),
  CONSTRAINT `sede_jornada_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sede` (`id`),
  CONSTRAINT `sede_jornada_ibfk_2` FOREIGN KEY (`jornada_id`) REFERENCES `jornada` (`id`),
  CONSTRAINT `sede_jornada_ibfk_3` FOREIGN KEY (`anio_lectivo_id`) REFERENCES `anio_lectivo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sede_jornada`
--

LOCK TABLES `sede_jornada` WRITE;
/*!40000 ALTER TABLE `sede_jornada` DISABLE KEYS */;
INSERT INTO `sede_jornada` VALUES (1,1,2,1),(4,2,2,1);
/*!40000 ALTER TABLE `sede_jornada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `numero_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_documento` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombres_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sexo_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rh_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad_usuario` int NOT NULL,
  `telefono_usuario` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email_usuario` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `estado_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `etnia_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `foto_usuario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `numero_documento` (`numero_documento`),
  KEY `estado_usuario` (`estado_usuario`(768))
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (5,'102232205','CC','Catalina','Perez','Masculino','A+','2004-10-26',20,'3045857060','santiagoneusasena@gmail.com','catalinaperez','admin123','2025-09-29 00:43:02',NULL,'Activo',NULL,NULL,'',''),(10,'1022322043','CC','Tomas Santiago','Perez','Masculino','A+','2003-10-26',21,'3112966085','santiagoneusasena@gmail.com','TomytoLol','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-04 03:59:28',NULL,'Inactivo',NULL,NULL,'',''),(11,'0987654321','CC','Santiago','Reyes Rodriguez','Masculino','A+','2003-10-26',21,'3045857065','santiagoneusasena@gmail.com','santiagor','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-08-04 00:23:44',NULL,'Activo',NULL,NULL,'',''),(14,'2608202512','CC','Eduardo','Ramirez Culman','Masculino','O+','1984-12-10',37,'3045857065','edculman@gmail.com','eduCulman','$1$rasmusle$Mv9eBgE.mijKONHFMCIDq1','2025-09-30 22:36:28',NULL,'Activo',NULL,NULL,'',''),(16,'657849','CC','liliana','fonseca','Femenino','O-','2003-10-26',32,'3207654322','lilianafonseca@gmail.com','lilianafon','$1$rasmusle$0GYWlU.VOlg2Kw0dULQZb1','2025-08-04 00:23:44',NULL,'Activo',NULL,NULL,'',''),(17,'1005083210','TI','Eduardo','Perales','Masculino','O+','2015-05-12',10,'3101102320','eduardo@perales.com','eperales','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-08-02 20:31:44',NULL,'Activo',NULL,NULL,NULL,NULL),(19,'1136274510','TI','Camila','Perez Gaitan','Femenino','O+','2016-05-12',9,'3102563256','camila@perez.com','cperez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-08-02 20:33:52',NULL,'Activo',NULL,NULL,NULL,NULL),(20,'1136274516','CC','Mary','Gaitan','Femenino','A+','2000-02-15',25,'3102563220','mary@gaitan.com','maryg','$1$rasmusle$A2nppRUlg.oR8mc89rPJy0','2025-08-02 20:35:43',NULL,'Activo',NULL,NULL,NULL,NULL),(21,'123456789','TI','Juan Camilo','Restrepo','Masculino','A-','2014-02-12',11,'3121234567','juan@restrepo.com','jcamilo','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-28 23:49:03',NULL,'Activo',NULL,NULL,NULL,NULL),(22,'1136274500','CC','Jota P','Rodriguez','Masculino','AB+','1991-02-25',34,'3102345678','jotap@rodriguez.com','jotapr','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-27 07:11:56',NULL,'Activo',NULL,NULL,NULL,NULL),(23,'1136270200','CC','Juan Guillermo ','Perez','Masculino','A+','1980-07-12',45,'3101234567','juan@perez.com','jperez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-08-03 01:04:48',NULL,'Activo',NULL,NULL,NULL,NULL),(26,'1136274800','CC','Juan Jose','Perez','Masculino','A+','1990-02-16',35,'3101231232','jperez@perez.com','jjperez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-08-03 01:11:12',NULL,'Activo',NULL,NULL,NULL,NULL),(27,'1005088852','CC','Pedro','Gomez','Masculino','O+','1980-02-10',45,'3101234567','pedro@gomez.com','pgomez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-08-16 23:51:59',NULL,'Activo',NULL,NULL,NULL,NULL),(28,'123456712','CC','Eduardo','Gaitan P','Masculino','A+','1990-02-10',35,'3223233232','eduardo@gaitan.com','egaitan','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-08-17 18:15:45',NULL,'Activo',NULL,NULL,NULL,NULL),(29,'1005083858','CC','Juan Carlos','Gómez Pinzón','Masculino','A+','1990-02-25',35,'3223658925','edculman@gmail.com','jgomez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-10 01:21:05',NULL,'Activo',NULL,NULL,NULL,NULL),(30,'1005083001','CC','Juan Camilo','Torres','Masculino','AB+','2000-02-15',25,'3206523232','edculman1205@gmail.com','jtorres','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-09 23:10:37',NULL,'Activo',NULL,NULL,NULL,NULL),(31,'1122836201','RC','Liam','Ramirez','Masculino','A+','2018-11-29',6,'3223394060','edculman@gmail.com','lramirez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-09 23:16:22',NULL,'Activo',NULL,NULL,NULL,NULL),(32,'12356984','CC','Charly','Jimenez','Masculino','AB+','1996-10-02',31,'3102363568','charly12brs@gmail.com','charly','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-20 15:07:39',NULL,'Activo',NULL,NULL,NULL,NULL),(33,'123456123','CC','Eduardo','Rodríguez','Masculino','A+','1990-12-05',39,'3123123231','edculman@gmail.com','erodriguez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-26 02:03:22',NULL,'Activo',NULL,NULL,NULL,NULL),(34,'35263235','TI','Camila','Ramirez','Femenino','A+','2015-02-10',10,'3133334455','edculman@gmail.com','cramirez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-26 02:08:44',NULL,'Activo',NULL,NULL,NULL,NULL),(35,'12332156','CC','Pedro','Suarez','Masculino','AB+','1980-06-02',45,'3122223322','edculman@gmail.com','psuarez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-26 02:10:08',NULL,'Activo',NULL,NULL,NULL,NULL),(36,'1215624','CC','Paola','Perez','Masculino','O+','1990-05-05',35,'3233333322','edculman@gmail.com','pperez','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-26 02:12:39',NULL,'Activo',NULL,NULL,NULL,NULL),(37,'1136274111','CC','Eneyda','Gaitan','Masculino','O+','1994-02-10',31,'3222223344','edculman@gmail.com','egaitan','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-26 02:16:18',NULL,'Activo',NULL,NULL,NULL,NULL),(38,'123456002','CC','Jhon','Arteaga','Masculino','AB+','1990-08-02',35,'3228889988','edculman@gmail.com','jarteaga','$1$rasmusle$Mv9eBgE.mijKONHFMCIDq1','2025-09-30 23:12:26',NULL,'Activo',NULL,NULL,NULL,NULL),(39,'32365200','CC','Adela','Marin','Femenino','O+','1980-09-02',45,'3225556600','edculman@gmail.com','amarin','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-26 02:26:39',NULL,'Activo',NULL,NULL,NULL,NULL),(40,'1121847111','CC','Andrea Catalina','Villamil','Femenino','B+','1995-10-02',34,'3123633636','edculman@gmail.com','avillamil','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-28 00:20:15',NULL,'Activo',NULL,NULL,NULL,NULL),(49,'1121847122','RC','Juliana','Villamil','Femenino','B+','2019-02-10',5,'3223395684','edculman@gmail.com','jvillamil','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-28 00:30:32',NULL,'Activo',NULL,NULL,NULL,NULL),(50,'1005083369','CC','Juan Jose','Reina','Masculino','O+','1990-05-30',35,'3228889966','edculman@gmail.com','jreina','$1$rasmusle$YRXA9tfFHlJy7qhe7BvzN.','2025-09-28 00:58:37',NULL,'Inactivo',NULL,NULL,NULL,NULL),(51,'1005083888','CC','Enrique','Guzman','Masculino','A+','1980-02-10',45,'3118881131','edculman@gmail.com','eguzman','$2y$10$z5FTCkR3jzcr3Lp2e5K7FOBzTi7eJ.x0ccY4l9SMn93i/qjYtVxne','2025-09-29 00:17:41',NULL,'Pendiente',NULL,NULL,NULL,NULL),(52,'1102056800','CC','Enrique','Rodríguez','Masculino','A+','1975-02-10',50,'3223331110','edculman@gmail.com','erodriguez1','$2y$10$q6OU/OVR.Da135QlUGEBeun46OSnN09Kl4Q8VUkPHZjKabj5c/90y','2025-09-29 00:27:28',NULL,'Pendiente','d089d39cdb40a2c5067c916e02123d01fdfc09d5e0ecd439fa29bc07f866900e','2025-09-29 01:27:28',NULL,NULL),(53,'58650100','CC','Marisol ','Orjuela','Femenino','O+','1985-05-16',40,'3001111111','edculman@gmail.com','morjuela','$2y$10$67MUbAwLPrWZpZX1vAKZOeGnHkNenskWy2CDqtXBukpumi1tt9qzm','2025-09-29 00:30:56',NULL,'Pendiente',NULL,NULL,NULL,NULL),(54,'1005083822','CC','Eduardo','Perez','Masculino','O+','2000-05-20',25,'3102357795','edculman@gmail.com','eperez','$1$rasmusle$Mv9eBgE.mijKONHFMCIDq1','2025-09-30 21:00:59',NULL,'Pendiente',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'wissen2'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-30 18:33:36
