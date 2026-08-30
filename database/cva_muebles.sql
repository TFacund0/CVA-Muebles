-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cva_muebles
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  `activo` int(2) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Escritorio',0,NULL,NULL),(5,'Sillones',1,NULL,NULL),(6,'Roperos',1,NULL,NULL),(7,'Camas',1,NULL,NULL),(8,'Bajo Mesadas',1,NULL,NULL),(9,'Estantes',1,NULL,NULL),(14,'Alacenas',0,NULL,NULL),(16,'Mesas',1,NULL,NULL),(17,'Sillas',1,NULL,NULL),(20,'Cómodas',1,NULL,NULL),(21,'Escaleras',0,NULL,NULL);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultas`
--

DROP TABLE IF EXISTS `consultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consultas` (
  `id_consulta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `asunto` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `activo` varchar(2) NOT NULL DEFAULT 'SI',
  PRIMARY KEY (`id_consulta`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultas`
--

LOCK TABLES `consultas` WRITE;
/*!40000 ALTER TABLE `consultas` DISABLE KEYS */;
-- Consultas de ejemplo. Los datos reales de contacto se excluyen a proposito de este dump.
INSERT INTO `consultas` VALUES (2,'Nombre','Apellido','consulta1@example.com','3700000001','Presupuesto','Me gustaria saber el presupuesto para un mueble en especifico\r\n','2025-06-17 00:00:00','NO'),(4,'Nombre','Apellido','consulta2@example.com','3700000002','Garantía','Estoy interasado en saber como funciona la garantia\r\n','2025-06-18 00:00:00','SI'),(5,'Nombre','Apellido','consulta3@example.com','3700000003','Solicitud de presupuesto','Quiero saber de cuanto es el presupuesto acerca de un mueble que quiero diseñar\r\n','2025-06-19 00:00:00','SI'),(6,'Nombre','Apellido','consulta4@example.com','3700000004','Consulta general','Tengo una duda acerca de como se realiza el pago de algún mueble\r\n','2025-06-19 00:00:00','NO');
/*!40000 ALTER TABLE `consultas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `favoritos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) unsigned NOT NULL,
  `producto_id` int(11) unsigned NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favoritos`
--

LOCK TABLES `favoritos` WRITE;
/*!40000 ALTER TABLE `favoritos` DISABLE KEYS */;
INSERT INTO `favoritos` VALUES (3,14,10,'2026-05-19 21:31:29'),(17,14,12,'2026-05-21 07:32:18');
/*!40000 ALTER TABLE `favoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galeria_clientes`
--

DROP TABLE IF EXISTS `galeria_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galeria_clientes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) unsigned NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `activo` enum('SI','NO') DEFAULT 'NO',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galeria_clientes`
--

LOCK TABLES `galeria_clientes` WRITE;
/*!40000 ALTER TABLE `galeria_clientes` DISABLE KEYS */;
INSERT INTO `galeria_clientes` VALUES (1,21,'https://res.cloudinary.com/dztaqoc3d/image/upload/v1779595613/cva_muebles/galeria/bilnl9mqhwcqozpdiuap.jpg','Que locura el universo','2026-05-13 06:51:24','SI');
/*!40000 ALTER TABLE `galeria_clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-07-17-000001','App\\Database\\Migrations\\Baseline','default','App',1784334839,1),(2,'2026-07-17-000002','App\\Database\\Migrations\\AlterMoneyColumnsToDecimal','default','App',1784334840,1),(3,'2026-07-17-000003','App\\Database\\Migrations\\AddStandardTimestamps','default','App',1784334840,1),(4,'2026-07-17-000004','App\\Database\\Migrations\\StandardizeSoftDeletes','default','App',1784334840,1),(5,'2026-07-18-000001','App\\Database\\Migrations\\Baseline','default','App',1784379584,2),(6,'2026-07-18-000002','App\\Database\\Migrations\\AlterMoneyColumnsToDecimal','default','App',1784379584,2),(7,'2026-07-18-000003','App\\Database\\Migrations\\StandardizeSoftDeletes','default','App',1784380287,3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfiles`
--

DROP TABLE IF EXISTS `perfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfiles`
--

LOCK TABLES `perfiles` WRITE;
/*!40000 ALTER TABLE `perfiles` DISABLE KEYS */;
INSERT INTO `perfiles` VALUES (1,'admin'),(2,'cliente');
/*!40000 ALTER TABLE `perfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto_imagenes`
--

DROP TABLE IF EXISTS `producto_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto_imagenes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `fecha_alta` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `producto_imagenes_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_imagenes`
--

LOCK TABLES `producto_imagenes` WRITE;
/*!40000 ALTER TABLE `producto_imagenes` DISABLE KEYS */;
INSERT INTO `producto_imagenes` VALUES (1,7,'https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595610/cva_muebles/productos/sky4c8drmouu90k5haql.jpg',0,'2026-05-14 15:03:19');
/*!40000 ALTER TABLE `producto_imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_prod` varchar(100) NOT NULL,
  `imagen` varchar(255) NOT NULL DEFAULT '',
  `categoria_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `precio_vta` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `stock_min` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (7,'Estante de pino','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595568/cva_muebles/productos/r0n8q6d1vh9ltfmhfoqx.jpg',14,350000.00,500000.00,10,3,'',NULL,'2026-07-25 20:10:05','2026-07-17 21:34:00'),(8,'Estante sin fondo','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595571/cva_muebles/productos/cybeztpkll0cuf3xd0vm.jpg',9,250000.00,400000.00,10,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(10,'Mesa de eucalipto','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595576/cva_muebles/productos/kocz8vnzdgvvpuaimzzi.jpg',16,150000.00,250000.00,1,1,'',NULL,'2026-07-25 20:10:05',NULL),(11,'Mesa de pino','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595578/cva_muebles/productos/u5h6hwbp78xqijslojpn.jpg',16,150000.00,250000.00,15,5,NULL,NULL,'2026-08-30 13:24:31','2026-08-30 13:24:31'),(12,'Ropero con escritorio integrado','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595580/cva_muebles/productos/rlatupr8lycjxbvzcos1.jpg',6,700000.00,999999.00,5,3,NULL,NULL,'2026-07-25 20:10:05',NULL),(13,'Mesa con sillas','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595582/cva_muebles/productos/ubyrrr1o24ugtho1wg3r.jpg',16,400000.00,600000.00,10,10,NULL,NULL,'2026-07-25 20:10:05',NULL),(14,'Alacena de eucalipto','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595584/cva_muebles/productos/kd2m5yxvjhy3sfphrrh9.jpg',14,200000.00,300000.00,15,5,NULL,NULL,'2026-07-25 20:10:05','2026-07-17 21:34:00'),(15,'Ropero de eucalipto con pino','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595586/cva_muebles/productos/vlrliqtwhjqjetivba6d.jpg',6,400000.00,500000.00,5,1,NULL,NULL,'2026-07-25 20:10:05',NULL),(16,'Cama de eucalipto','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595588/cva_muebles/productos/f9rsz2agq1qs1rhayu9g.jpg',7,200000.00,300000.00,10,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(17,'Alacena','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595590/cva_muebles/productos/zrgtsgrbsagwirka0ecf.jpg',9,300000.00,400000.00,14,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(18,'Estante de pino','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595592/cva_muebles/productos/d8ubaj1sd51pqug4cias.jpg',9,100000.00,150000.00,19,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(19,'Bajo mesada de pino','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595595/cva_muebles/productos/ldcnmd272qmzxegub7xb.jpg',8,200000.00,300000.00,9,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(20,'Silla acolchonada','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595597/cva_muebles/productos/hd7x9ftnnlrqqp8moxwg.jpg',17,120000.00,150000.00,15,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(21,'Sillas de eucalipto','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595599/cva_muebles/productos/r2sip4la4uypgxqqwi7x.jpg',17,200000.00,300000.00,14,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(22,'Escritorio pequeño','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595601/cva_muebles/productos/ppszlxwbmikhhrj3iwm3.jpg',1,50000.00,100000.00,16,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(23,'Sillon para exterior','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595604/cva_muebles/productos/k7tcaexciexmkoilcamv.jpg',5,200000.00,300000.00,2,5,NULL,NULL,'2026-07-25 20:10:05',NULL),(24,'Cómoda de eucalipto','https://res.cloudinary.com/dztaqoc3d/image/upload/q_auto,f_auto/v1779595606/cva_muebles/productos/r5ptkbgdl6dddoboggbx.jpg',20,200000.00,300000.00,11,5,NULL,NULL,'2026-07-25 20:10:05',NULL);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `perfil_id` int(11) NOT NULL DEFAULT 2,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuario` (`usuario`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
-- Datos de ejemplo. Los IDs se conservan porque ventas_cabecera y favoritos los referencian.
-- La clave del usuario admin es CambiarEstaClave123 y debe cambiarse en la primera sesion.
INSERT INTO `usuarios` VALUES (14,'Admin','Sistema','admin','admin@example.com','$2y$10$6FMOBIi/nKa/yLZ.yr2.K.rN1U.AFOMs1kXTqnXtALgckvq1O6fbG','',1,NULL,'2026-08-30 13:52:15',NULL),(20,'Cliente','Externo','cliente_whatsapp','whatsapp@example.com','manual_order_only','',2,NULL,NULL,'2026-07-17 21:34:00'),(23,'Cliente','Demo','cliente_demo','cliente.demo@example.com','$2y$10$6FMOBIi/nKa/yLZ.yr2.K.rN1U.AFOMs1kXTqnXtALgckvq1O6fbG','',2,NULL,NULL,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas_cabecera`
--

DROP TABLE IF EXISTS `ventas_cabecera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas_cabecera` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(10) unsigned DEFAULT NULL,
  `total_venta` decimal(10,2) NOT NULL,
  `estado` enum('PENDIENTE','EN_PROCESO','TERMINADO','ENTREGADO') DEFAULT 'PENDIENTE',
  `observaciones` text DEFAULT NULL,
  `tipo_pedido` enum('CATALOGO','A_MEDIDA') DEFAULT 'CATALOGO',
  `estado_aprobacion` varchar(20) DEFAULT 'ACEPTADO',
  `prioridad` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas_cabecera`
--

LOCK TABLES `ventas_cabecera` WRITE;
/*!40000 ALTER TABLE `ventas_cabecera` DISABLE KEYS */;
INSERT INTO `ventas_cabecera` VALUES (49,'2026-05-19 21:25:18',14,250000.00,'PENDIENTE','','','SOLICITUD',0),(52,'2026-05-20 17:13:24',14,250000.00,'PENDIENTE','','','SOLICITUD',0),(53,'2026-05-21 07:34:49',14,1249999.00,'PENDIENTE','','','ACEPTADO',0),(54,'2026-05-24 09:48:16',14,250000.00,'PENDIENTE','','','SOLICITUD',0),(55,'2026-05-24 09:50:47',14,250000.00,'EN_PROCESO','','','ACEPTADO',1),(56,'2026-05-27 14:52:15',14,2000000.00,'PENDIENTE','','','ACEPTADO',0);
/*!40000 ALTER TABLE `ventas_cabecera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas_detalle`
--

DROP TABLE IF EXISTS `ventas_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `venta_id` (`venta_id`),
  CONSTRAINT `ventas_detalle_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id_producto`) ON DELETE SET NULL,
  CONSTRAINT `ventas_detalle_ibfk_2` FOREIGN KEY (`venta_id`) REFERENCES `ventas_cabecera` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas_detalle`
--

LOCK TABLES `ventas_detalle` WRITE;
/*!40000 ALTER TABLE `ventas_detalle` DISABLE KEYS */;
INSERT INTO `ventas_detalle` VALUES (54,49,11,1,250000.00),(57,52,10,1,250000.00),(58,53,11,1,250000.00),(59,53,12,1,999999.00),(60,54,10,1,250000.00),(61,55,11,1,250000.00),(62,56,10,8,250000.00);
/*!40000 ALTER TABLE `ventas_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas_pagos`
--

DROP TABLE IF EXISTS `ventas_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `nota` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  CONSTRAINT `ventas_pagos_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas_cabecera` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas_pagos`
--

LOCK TABLES `ventas_pagos` WRITE;
/*!40000 ALTER TABLE `ventas_pagos` DISABLE KEYS */;
/*!40000 ALTER TABLE `ventas_pagos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-30 14:06:11
