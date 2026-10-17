-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: odontotrack
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `anamnesis`
--

DROP TABLE IF EXISTS `anamnesis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anamnesis` (
  `id_anamnesis` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_expediente` bigint unsigned NOT NULL,
  `alergias` text COLLATE utf8mb4_unicode_ci,
  `antecedentes` text COLLATE utf8mb4_unicode_ci,
  `medicamentos` text COLLATE utf8mb4_unicode_ci,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_anamnesis`),
  UNIQUE KEY `anamnesis_id_expediente_unique` (`id_expediente`),
  CONSTRAINT `anamnesis_id_expediente_foreign` FOREIGN KEY (`id_expediente`) REFERENCES `expediente` (`id_expediente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anamnesis`
--

LOCK TABLES `anamnesis` WRITE;
/*!40000 ALTER TABLE `anamnesis` DISABLE KEYS */;
/*!40000 ALTER TABLE `anamnesis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cita`
--

DROP TABLE IF EXISTS `cita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cita` (
  `id_cita` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` bigint unsigned NOT NULL,
  `id_odontologo` bigint unsigned NOT NULL,
  `id_consultorio` bigint unsigned NOT NULL,
  `inicio` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PROGRAMADA',
  `motivo` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_cita`),
  KEY `cita_id_paciente_foreign` (`id_paciente`),
  KEY `cita_id_odontologo_inicio_fin_index` (`id_odontologo`,`inicio`,`fin`),
  KEY `cita_id_consultorio_inicio_fin_index` (`id_consultorio`,`inicio`,`fin`),
  CONSTRAINT `cita_id_consultorio_foreign` FOREIGN KEY (`id_consultorio`) REFERENCES `consultorio` (`id_consultorio`),
  CONSTRAINT `cita_id_odontologo_foreign` FOREIGN KEY (`id_odontologo`) REFERENCES `odontologo` (`id_odontologo`),
  CONSTRAINT `cita_id_paciente_foreign` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cita`
--

LOCK TABLES `cita` WRITE;
/*!40000 ALTER TABLE `cita` DISABLE KEYS */;
/*!40000 ALTER TABLE `cita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultorio`
--

DROP TABLE IF EXISTS `consultorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultorio` (
  `id_consultorio` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_consultorio`),
  UNIQUE KEY `consultorio_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultorio`
--

LOCK TABLES `consultorio` WRITE;
/*!40000 ALTER TABLE `consultorio` DISABLE KEYS */;
INSERT INTO `consultorio` VALUES (1,'Consultorio 1',1),(2,'Consultorio 2',1),(3,'Consultorio 3',1);
/*!40000 ALTER TABLE `consultorio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evolucion_clinica`
--

DROP TABLE IF EXISTS `evolucion_clinica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evolucion_clinica` (
  `id_evolucion` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_tratamiento` bigint unsigned NOT NULL,
  `id_odontologo` bigint unsigned NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_evolucion`),
  KEY `evolucion_clinica_id_tratamiento_foreign` (`id_tratamiento`),
  KEY `evolucion_clinica_id_odontologo_foreign` (`id_odontologo`),
  CONSTRAINT `evolucion_clinica_id_odontologo_foreign` FOREIGN KEY (`id_odontologo`) REFERENCES `odontologo` (`id_odontologo`),
  CONSTRAINT `evolucion_clinica_id_tratamiento_foreign` FOREIGN KEY (`id_tratamiento`) REFERENCES `tratamiento` (`id_tratamiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evolucion_clinica`
--

LOCK TABLES `evolucion_clinica` WRITE;
/*!40000 ALTER TABLE `evolucion_clinica` DISABLE KEYS */;
/*!40000 ALTER TABLE `evolucion_clinica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expediente`
--

DROP TABLE IF EXISTS `expediente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expediente` (
  `id_expediente` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_paciente` bigint unsigned NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_expediente`),
  UNIQUE KEY `expediente_id_paciente_unique` (`id_paciente`),
  CONSTRAINT `expediente_id_paciente_foreign` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`id_paciente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expediente`
--

LOCK TABLES `expediente` WRITE;
/*!40000 ALTER TABLE `expediente` DISABLE KEYS */;
/*!40000 ALTER TABLE `expediente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_10_07_000001_create_usuario_table',1),(2,'2026_10_07_000002_create_odontologo_table',1),(3,'2026_10_07_000003_create_consultorio_table',1),(4,'2026_10_07_000004_create_paciente_table',1),(5,'2026_10_07_000005_create_cita_table',1),(6,'2026_10_07_000006_create_expediente_table',1),(7,'2026_10_07_000007_create_anamnesis_table',1),(8,'2026_10_07_000008_create_tratamiento_table',1),(9,'2026_10_07_000009_create_evolucion_clinica_table',1),(10,'2026_10_07_000010_create_registro_odontograma_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `odontologo`
--

DROP TABLE IF EXISTS `odontologo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `odontologo` (
  `id_odontologo` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint unsigned NOT NULL,
  `numero_colegiado` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_odontologo`),
  UNIQUE KEY `odontologo_id_usuario_unique` (`id_usuario`),
  UNIQUE KEY `odontologo_numero_colegiado_unique` (`numero_colegiado`),
  CONSTRAINT `odontologo_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `odontologo`
--

LOCK TABLES `odontologo` WRITE;
/*!40000 ALTER TABLE `odontologo` DISABLE KEYS */;
INSERT INTO `odontologo` VALUES (1,2,'JVPO-1001'),(2,3,'JVPO-1002');
/*!40000 ALTER TABLE `odontologo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paciente`
--

DROP TABLE IF EXISTS `paciente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paciente` (
  `id_paciente` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_paciente`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paciente`
--

LOCK TABLES `paciente` WRITE;
/*!40000 ALTER TABLE `paciente` DISABLE KEYS */;
INSERT INTO `paciente` VALUES (1,'Carlos Alberto','Rivera Mejía','1990-03-14','7012-3456','carlos.rivera@example.com',1),(2,'María José','Flores Portillo','1985-07-22','7023-4567','maria.flores@example.com',1),(3,'José Roberto','Campos Aguilar','1978-11-05','7034-5678',NULL,1),(4,'Ana Lucía','Menjívar Ortiz','2001-01-30','7045-6789','ana.menjivar@example.com',1),(5,'Pedro Antonio','Quintanilla Cruz','1969-09-18','7056-7890',NULL,1),(6,'Gabriela Esther','Serrano Lara','1995-05-09','7067-8901','gabriela.serrano@example.com',1),(7,'Miguel Ángel','Henríquez Díaz','1988-12-02','7078-9012',NULL,1),(8,'Sofía Alejandra','Pineda Rosales','2005-04-27','7089-0123','sofia.pineda@example.com',1),(9,'Ricardo Ernesto','Alvarado Peña','1974-06-11','7090-1234',NULL,1),(10,'Daniela Beatriz','Guardado Romero','1999-08-20','7101-2345','daniela.guardado@example.com',1);
/*!40000 ALTER TABLE `paciente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_odontograma`
--

DROP TABLE IF EXISTS `registro_odontograma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registro_odontograma` (
  `id_registro` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_expediente` bigint unsigned NOT NULL,
  `id_odontologo` bigint unsigned NOT NULL,
  `pieza` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cara` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condicion` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registrado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_registro`),
  KEY `registro_odontograma_id_odontologo_foreign` (`id_odontologo`),
  KEY `registro_odontograma_id_expediente_pieza_index` (`id_expediente`,`pieza`),
  CONSTRAINT `registro_odontograma_id_expediente_foreign` FOREIGN KEY (`id_expediente`) REFERENCES `expediente` (`id_expediente`),
  CONSTRAINT `registro_odontograma_id_odontologo_foreign` FOREIGN KEY (`id_odontologo`) REFERENCES `odontologo` (`id_odontologo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_odontograma`
--

LOCK TABLES `registro_odontograma` WRITE;
/*!40000 ALTER TABLE `registro_odontograma` DISABLE KEYS */;
/*!40000 ALTER TABLE `registro_odontograma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tratamiento`
--

DROP TABLE IF EXISTS `tratamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tratamiento` (
  `id_tratamiento` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_expediente` bigint unsigned NOT NULL,
  `diagnostico` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PLANIFICADO',
  `fecha_inicio` date NOT NULL,
  PRIMARY KEY (`id_tratamiento`),
  KEY `tratamiento_id_expediente_foreign` (`id_expediente`),
  CONSTRAINT `tratamiento_id_expediente_foreign` FOREIGN KEY (`id_expediente`) REFERENCES `expediente` (`id_expediente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tratamiento`
--

LOCK TABLES `tratamiento` WRITE;
/*!40000 ALTER TABLE `tratamiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `tratamiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clave_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario_correo_unique` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Recepción Sonrisas','recepcion@odontotrack.test','$2y$12$e57966vTDJH8GaGVo/dz1.Pi71arI5vejz46kalUCwup8jWlvLwCC','RECEPCIONISTA',1),(2,'Dra. Ana Martínez','doctor1@odontotrack.test','$2y$12$L.PNb2gRUVYKN6D0tTR6f.uHCoDNbrnA7wMbEVyQkmwwu5syH9W1S','ODONTOLOGO',1),(3,'Dr. Luis Hernández','doctor2@odontotrack.test','$2y$12$KlwNYAe3haSs63o7zG1dTOfl9XKOYAZsSn.DMkRh3WZD6xKhjrM1q','ODONTOLOGO',1);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-10 21:27:16
