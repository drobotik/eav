-- MySQL dump 10.13  Distrib 8.0.28, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: eav
-- ------------------------------------------------------
-- Server version	5.7.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `eav_attribute_groups`
--

DROP TABLE IF EXISTS `eav_attribute_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_attribute_groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `set_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `IDX_C2831A0510FB0D18` (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_attribute_sets`
--

DROP TABLE IF EXISTS `eav_attribute_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_attribute_sets` (
  `set_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `name` int(11) NOT NULL,
  PRIMARY KEY (`set_id`),
  KEY `IDX_D8519F7B115F0EE5` (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_attributes`
--

DROP TABLE IF EXISTS `eav_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_attributes` (
  `attribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `strategy` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `IDX_F88060A8115F0EE5` (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_domains`
--

DROP TABLE IF EXISTS `eav_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_domains` (
  `domain_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_entities`
--

DROP TABLE IF EXISTS `eav_entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_entities` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `set_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`),
  KEY `IDX_390C547B115F0EE5` (`domain_id`),
  KEY `IDX_390C547B10FB0D18` (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_pivot`
--

DROP TABLE IF EXISTS `eav_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_pivot` (
  `pivot_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `set_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pivot_id`),
  UNIQUE KEY `UNIQ_C451A1BA115F0EE510FB0D18FE54D947B6E62EFA` (`domain_id`,`set_id`,`group_id`,`attribute_id`),
  KEY `IDX_C451A1BA115F0EE5` (`domain_id`),
  KEY `IDX_C451A1BA10FB0D18` (`set_id`),
  KEY `IDX_C451A1BAFE54D947` (`group_id`),
  KEY `IDX_C451A1BAB6E62EFA` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_value_datetime`
--

DROP TABLE IF EXISTS `eav_value_datetime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_value_datetime` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `value` datetime NOT NULL,
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNIQ_C99E7BC5115F0EE581257D5DB6E62EFA` (`domain_id`,`entity_id`,`attribute_id`),
  KEY `IDX_C99E7BC5115F0EE5` (`domain_id`),
  KEY `IDX_C99E7BC581257D5D` (`entity_id`),
  KEY `IDX_C99E7BC5B6E62EFA` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_value_decimal`
--

DROP TABLE IF EXISTS `eav_value_decimal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_value_decimal` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `value` decimal(10,0) NOT NULL,
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNIQ_329FA76D115F0EE581257D5DB6E62EFA` (`domain_id`,`entity_id`,`attribute_id`),
  KEY `IDX_329FA76D115F0EE5` (`domain_id`),
  KEY `IDX_329FA76D81257D5D` (`entity_id`),
  KEY `IDX_329FA76DB6E62EFA` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_value_int`
--

DROP TABLE IF EXISTS `eav_value_int`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_value_int` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNIQ_507A0E2E115F0EE581257D5DB6E62EFA` (`domain_id`,`entity_id`,`attribute_id`),
  KEY `IDX_507A0E2E115F0EE5` (`domain_id`),
  KEY `IDX_507A0E2E81257D5D` (`entity_id`),
  KEY `IDX_507A0E2EB6E62EFA` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_value_text`
--

DROP TABLE IF EXISTS `eav_value_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_value_text` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNIQ_5B7F02C6115F0EE581257D5DB6E62EFA` (`domain_id`,`entity_id`,`attribute_id`),
  KEY `IDX_5B7F02C6115F0EE5` (`domain_id`),
  KEY `IDX_5B7F02C681257D5D` (`entity_id`),
  KEY `IDX_5B7F02C6B6E62EFA` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eav_value_varchar`
--

DROP TABLE IF EXISTS `eav_value_varchar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eav_value_varchar` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `attribute_id` int(10) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`value_id`),
  UNIQUE KEY `UNIQ_97B6C942115F0EE581257D5DB6E62EFA` (`domain_id`,`entity_id`,`attribute_id`),
  KEY `IDX_97B6C942115F0EE5` (`domain_id`),
  KEY `IDX_97B6C94281257D5D` (`entity_id`),
  KEY `IDX_97B6C942B6E62EFA` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-13 12:19:07
