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
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Escritorio',0),(5,'Sillones',1),(6,'Roperos',1),(7,'Camas',1),(8,'Bajo Mesadas',1),(9,'Estantes',1),(14,'Alacenas',0),(16,'Mesas',1),(17,'Sillas',1),(20,'Cómodas',1),(21,'Escaleras',1);
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
INSERT INTO `consultas` VALUES (2,'Facu','Ace','facu@gmail.com','3777 45 05214','Presupuesto','Me gustaria saber el presupuesto para un mueble en especifico\r\n','2025-06-17 00:00:00','NO'),(4,'Fer','Arce','fer@gmail.com','315125','Garantía','Estoy interasado en saber como funciona la garantia\r\n','2025-06-18 00:00:00','SI'),(5,'Fer','Arce','fer@gmail.com','3251512','Solicitud de presupuesto','Quiero saber de cuanto es el presupuesto acerca de un mueble que quiero diseñar\r\n','2025-06-19 00:00:00','SI'),(6,'Luciano','Erck','LucianoErck@gmail.com','315325235','Consulta general','Tengo una duda acerca de como se realiza el pago de algún mueble\r\n','2025-06-19 00:00:00','NO');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favoritos`
--

LOCK TABLES `favoritos` WRITE;
/*!40000 ALTER TABLE `favoritos` DISABLE KEYS */;
INSERT INTO `favoritos` VALUES (1,21,24,'2026-05-13 06:30:44'),(3,14,10,'2026-05-19 21:31:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galeria_clientes`
--

LOCK TABLES `galeria_clientes` WRITE;
/*!40000 ALTER TABLE `galeria_clientes` DISABLE KEYS */;
INSERT INTO `galeria_clientes` VALUES (1,21,'1778655084_d310d8afad220cec70f3.jpg','Que locura el universo','2026-05-13 06:51:24','SI'),(2,14,'1778801623_4ed57550dee1a1370379.jpg','faa','2026-05-14 20:33:43','NO');
/*!40000 ALTER TABLE `galeria_clientes` ENABLE KEYS */;
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
INSERT INTO `producto_imagenes` VALUES (1,7,'1778781799_f1d064ddd178fa7718a4.jpg',0,'2026-05-14 15:03:19');
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
  `precio` float(10,2) NOT NULL,
  `precio_vta` float(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `stock_min` int(11) NOT NULL,
  `eliminado` varchar(10) NOT NULL DEFAULT 'NO',
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (7,'Estante de pino','1750367182_a9bfb3d20bd7ad94120e.jpeg',14,350000.00,500000.00,10,3,'SI',''),(8,'Estante sin fondo','1750367276_108871df0457e1f274d4.jpeg',9,250000.00,400000.00,10,5,'SI',NULL),(9,'Estante de algarrobo','1750367312_6c05567373aae8ebb766.jpg',9,200000.00,300000.00,10,5,'SI',NULL),(10,'Mesa de eucalipto','1750367354_02d7591be201fb9b77e9.jpeg',16,150000.00,250000.00,1,1,'NO',''),(11,'Mesa de pino','1750367397_96846ed6948c07783b77.jpg',16,150000.00,250000.00,15,5,'NO',NULL),(12,'Ropero con escritorio integrado','1750367468_0fccc0b93644d616541d.jpeg',6,700000.00,999999.00,5,3,'NO',NULL),(13,'Mesa con sillas','1750367547_0b112486af66993228de.jpeg',16,400000.00,600000.00,10,10,'NO',NULL),(14,'Alacena de eucalipto','1750367674_109299dabf88251dac02.jpeg',14,200000.00,300000.00,15,5,'SI',NULL),(15,'Ropero de eucalipto con pino','1750367749_398664b6930e43ed0df9.jpeg',6,400000.00,500000.00,5,1,'NO',NULL),(16,'Cama de eucalipto','1750367783_c96227329266c39680ec.jpeg',7,200000.00,300000.00,10,5,'NO',NULL),(17,'Alacena','1750367820_8ea403364418e14b3ad6.jpeg',9,300000.00,400000.00,14,5,'NO',NULL),(18,'Estante de pino','1750367852_283de3c6f2ab233b345b.jpeg',9,100000.00,150000.00,19,5,'NO',NULL),(19,'Bajo mesada de pino','1750367889_d03703f413f65044d0b6.jpeg',8,200000.00,300000.00,9,5,'NO',NULL),(20,'Silla acolchonada','1750367932_cfefde8c8ab04e0ba99c.jpeg',17,120000.00,150000.00,15,5,'NO',NULL),(21,'Sillas de eucalipto','1750367975_965e96f6a45306f852e8.jpeg',17,200000.00,300000.00,14,5,'NO',NULL),(22,'Escritorio pequeño','1750368275_de84da360c6f474dd2af.jpeg',1,50000.00,100000.00,16,5,'NO',NULL),(23,'Sillon para exterior','1750368328_18bab67f42e4432ba330.jpeg',5,200000.00,300000.00,2,5,'NO',NULL),(24,'Cómoda de eucalipto','1750368431_5b739415e4bfe73ca164.jpeg',20,200000.00,300000.00,11,5,'NO',NULL);
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
  `baja` varchar(2) NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuario` (`usuario`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (4,'Tobias','Acevedo','TFazcux','tobias@gmail.com','$2y$10$zKcL4S.MQgLPP6cEKXndreX8biSI1fgpyIx8SnZSp4gcU5o30ZFam','',2,'SI'),(5,'Juan Roman','Zacarias','Juanro','zacariasjuanro@gmail.com','$2y$10$ISmznfwWZzHS9Hd.idGEYem.kNF.ABoup4gfPnmfOgFEpRDmTITly','',2,'NO'),(6,'Kevin','Garcia','TFacundo','kevingarcia@gmail.com','$2y$10$g0d0TYdHhZOdTThLlgspfe7Ju1T0/sXr56FIMvi8yJINKuTLcv5mW','1750353058_d9303ac1a7fde02639b8.jpg',1,'NO'),(7,'Nahuel','Canario','Nahu','nahu@gmail.com','$2y$10$iRbrbUCpyaWuBqi3x91yNefMPY1/gQMa4cRmqFMo6l14Mc595.mGy','',2,'NO'),(8,'Leonel','Aguilar','Leo','leonel@gmail.com','$2y$10$6snBXq5/wwksgeIoMbLUIuflToLwh3lq5P6E3Wt.xC4SDV1RjArIy','',2,'NO'),(11,'Fernando','Arce','Fer','fer@gmail.com','$2y$10$c3wdW6RH1s98YkkKMPS0Iu5ju9dVRmYrETI0BGaxf1hdMXU.I238e','',2,'NO'),(12,'Cesar','Acevedo','Cesar','cesar@gmail.com','$2y$10$6F/r1Svj1/tRiHE7erETNuybMxu3TWjwqj0i0hFAsaNhNMR9ZiOYa','',2,'NO'),(13,'Sabri','Sabri','Sabri','Sabri@gmail.com','$2y$10$qUHNDjPmOzDBlaPnKq.6Se.VVWTDbJX8Yl17wqRp5IiVhA/Gono5e','',2,'NO'),(14,'admin','nimda','admin','admin@gmail.com','$2y$10$KI9IwincoUIdeKsatZGHPuwtCnhGzUznR6achKfKmFi3QWwCtmmqm','1778629474_c43538cc24be3d80ef7c.jpg',1,'NO'),(15,'user','resu','usuario','user@gmail.com','$2y$10$beQYzA9fLgGMAWptR/9QR.DNoizImkMk0GAWkpYCcjQeSsVGGia4W','',2,'SI'),(18,'wololo','wololo','wololo','wololo@gmail.wololo','$2y$10$a/7OFED5yvWUBeEbiDpAtuJoY3vTWXq9N1qHxuyuTb96T1o1JpQqO','',2,'SI'),(19,'lari','lari','lari','lari@gmail.com','$2y$10$axfr.ByTLoQ9b6T8xQhBJ.6JVqp3Xt4XbMMg.kmWlX6Xa6p4CeSVa','',2,'SI'),(20,'Cliente','Externo','cliente_whatsapp','whatsapp@cva.com','manual_order_only','',2,'SI'),(21,'Tobias','Acevedo','Tob','tobi@gmail.com','$2y$10$71vcQbEu8Hf/vucCwHoGFOt9J3X5GNWreDE7AxjGH2rWxHLKyrZ/C','1778616916_e46172f562bf0e7a309c.jpg',2,'NO');
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
  `total_venta` float(10,2) NOT NULL,
  `estado` enum('PENDIENTE','EN_PROCESO','TERMINADO','ENTREGADO') DEFAULT 'PENDIENTE',
  `observaciones` text DEFAULT NULL,
  `tipo_pedido` enum('CATALOGO','A_MEDIDA') DEFAULT 'CATALOGO',
  `estado_aprobacion` varchar(20) DEFAULT 'ACEPTADO',
  `prioridad` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas_cabecera`
--

LOCK TABLES `ventas_cabecera` WRITE;
/*!40000 ALTER TABLE `ventas_cabecera` DISABLE KEYS */;
INSERT INTO `ventas_cabecera` VALUES (29,'2025-06-19 21:31:17',15,700000.00,'EN_PROCESO',NULL,'CATALOGO','ACEPTADO',0),(30,'2025-06-19 21:32:00',13,1150000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',0),(31,'2025-06-19 22:17:20',6,700000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',0),(32,'2025-06-19 22:26:01',15,600000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',0),(33,'2025-06-20 00:13:54',15,700000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',0),(34,'2025-06-20 00:19:00',15,300000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',0),(35,'2025-06-20 00:19:43',15,300000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',0),(36,'2025-06-20 01:06:08',18,600000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',0),(37,'2025-11-16 15:36:38',14,700000.00,'PENDIENTE',NULL,'CATALOGO','ACEPTADO',1),(38,'2026-05-14 20:25:21',21,100000.00,'PENDIENTE','','CATALOGO','ACEPTADO',2),(39,'2026-05-14 22:14:23',21,400000.00,'PENDIENTE','','CATALOGO','ACEPTADO',-1),(40,'2026-05-14 22:24:25',21,600000.00,'ENTREGADO','','CATALOGO','ACEPTADO',1),(41,'2026-05-14 22:31:13',14,400000.00,'PENDIENTE','','','ACEPTADO',-1),(42,'2026-05-14 22:38:17',14,400000.00,'PENDIENTE','','','RECHAZADO',0),(43,'2026-05-14 22:45:03',21,300000.00,'PENDIENTE','','','RECHAZADO',0),(44,'2026-05-14 22:48:19',21,300000.00,'PENDIENTE','','','RECHAZADO',0),(45,'2026-05-14 22:56:24',21,400000.00,'PENDIENTE','','','RECHAZADO',0),(46,'2026-05-14 20:13:41',21,500000.00,'PENDIENTE','Quiero tener ese mismo mueble pero en blanco, sera que es posible de esa manera? o que puede pasar? no se, lo que me digas o respondas unicamente.','','RECHAZADO',0),(47,'2026-05-19 14:51:50',14,500000.00,'PENDIENTE','','','RECHAZADO',0),(48,'2026-05-19 18:37:37',14,400000.00,'PENDIENTE','el estante lo quiero de color rojo','','RECHAZADO',0),(49,'2026-05-19 21:25:18',14,250000.00,'PENDIENTE','','','SOLICITUD',0),(50,'2026-05-19 21:28:05',14,250000.00,'PENDIENTE','','','SOLICITUD',0);
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
  `precio` float(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `venta_id` (`venta_id`),
  CONSTRAINT `ventas_detalle_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id_producto`) ON DELETE SET NULL,
  CONSTRAINT `ventas_detalle_ibfk_2` FOREIGN KEY (`venta_id`) REFERENCES `ventas_cabecera` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas_detalle`
--

LOCK TABLES `ventas_detalle` WRITE;
/*!40000 ALTER TABLE `ventas_detalle` DISABLE KEYS */;
INSERT INTO `ventas_detalle` VALUES (24,29,24,1,300000.00),(25,29,22,1,100000.00),(26,29,19,1,300000.00),(27,30,18,1,150000.00),(28,30,17,1,400000.00),(29,30,13,1,600000.00),(30,31,24,1,300000.00),(31,31,23,1,300000.00),(32,31,22,1,100000.00),(33,32,24,1,300000.00),(34,32,23,1,300000.00),(35,33,23,2,300000.00),(36,33,22,1,100000.00),(37,34,23,1,300000.00),(38,35,24,1,300000.00),(39,36,23,1,300000.00),(40,36,21,1,300000.00),(41,37,23,2,300000.00),(42,37,22,1,100000.00),(43,38,22,1,100000.00),(44,39,8,1,400000.00),(45,40,13,1,600000.00),(46,41,8,1,400000.00),(47,42,8,1,400000.00),(48,43,14,1,300000.00),(49,44,9,1,300000.00),(50,45,8,1,400000.00),(51,46,7,1,500000.00),(52,47,7,1,500000.00),(53,48,8,1,400000.00),(54,49,11,1,250000.00),(55,50,11,1,250000.00);
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
  `monto` float(10,2) NOT NULL,
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
INSERT INTO `ventas_pagos` VALUES (1,29,20000.00,'2026-05-14 02:12:15','seña'),(2,40,600000.00,'2026-05-19 21:01:35','Entrega total');
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

-- Dump completed on 2026-05-20 16:55:54
