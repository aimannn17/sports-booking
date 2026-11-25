-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sports_booking
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
-- Current Database: `sports_booking`
--

/*!40000 DROP DATABASE IF EXISTS `sports_booking`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `sports_booking` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `sports_booking`;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(12) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `time_slot_id` bigint(20) unsigned NOT NULL,
  `amount_due` decimal(6,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL,
  `payment_deadline` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancel_reason` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_booking_code_unique` (`booking_code`),
  KEY `bookings_time_slot_id_foreign` (`time_slot_id`),
  KEY `bookings_user_id_status_index` (`user_id`,`status`),
  KEY `bookings_status_index` (`status`),
  CONSTRAINT `bookings_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`),
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,'BK-5ZZGT00O',2,27,0.00,'confirmed',NULL,NULL,NULL,'2026-11-20 03:39:47','2026-11-20 03:39:47'),(2,'BK-GP6DMGCE',2,266,0.00,'confirmed',NULL,NULL,NULL,'2026-11-20 03:39:47','2026-11-20 03:39:47'),(3,'BK-FQLJK638',3,143,0.00,'confirmed',NULL,NULL,NULL,'2026-11-20 03:39:47','2026-11-20 03:39:47'),(4,'BK-M0YSJ8LE',4,39,0.00,'cancelled',NULL,'2026-11-20 11:39:47','Exam clash','2026-11-20 03:39:47','2026-11-20 03:39:47'),(5,'BK-SUSG7QGF',5,348,40.00,'pending','2026-11-21 11:39:47',NULL,NULL,'2026-11-20 03:39:47','2026-11-20 03:39:47'),(6,'BK-SQE4S8PK',5,56,20.00,'confirmed','2026-11-21 11:39:47',NULL,NULL,'2026-11-20 03:39:47','2026-11-20 03:39:47'),(7,'BK-MNWOYGHQ',6,277,60.00,'confirmed','2026-11-21 11:39:47',NULL,NULL,'2026-11-20 03:39:47','2026-11-20 03:39:47'),(8,'BK-RLPQDOEW',6,198,20.00,'pending','2026-11-21 11:39:47',NULL,NULL,'2026-11-20 03:39:47','2026-11-20 03:39:47'),(9,'BK-GNDRFBBH',2,113,0.00,'completed',NULL,NULL,NULL,'2026-11-11 10:00:00','2026-11-20 03:39:47'),(10,'BK-R8HBGLBM',3,232,0.00,'completed',NULL,NULL,NULL,'2026-11-15 11:00:00','2026-11-20 03:39:47'),(11,'BK-TMTIAHZO',4,230,0.00,'cancelled',NULL,'2026-11-12 19:00:00','Feeling unwell','2026-11-11 11:00:00','2026-11-20 03:39:47'),(12,'BK-AZLWTNML',5,423,40.00,'completed',NULL,NULL,NULL,'2026-11-15 10:00:00','2026-11-20 03:39:47');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `facility_type_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `external_fee` decimal(6,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facilities_facility_type_id_foreign` (`facility_type_id`),
  KEY `facilities_is_active_index` (`is_active`),
  CONSTRAINT `facilities_facility_type_id_foreign` FOREIGN KEY (`facility_type_id`) REFERENCES `facility_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facilities`
--

LOCK TABLES `facilities` WRITE;
/*!40000 ALTER TABLE `facilities` DISABLE KEYS */;
INSERT INTO `facilities` VALUES (1,1,'Badminton Court A','Sports Hall, Level 1',20.00,1,'Wooden floor, 2 nets. Bring your own racquet.','2026-11-20 03:39:46','2026-11-20 03:39:46'),(2,1,'Badminton Court B','Sports Hall, Level 1',20.00,1,'Next to Court A.','2026-11-20 03:39:46','2026-11-20 03:39:46'),(3,1,'Badminton Court C','Sports Hall, Level 2',20.00,0,'Closed for floor repairs.','2026-11-20 03:39:46','2026-11-20 03:39:46'),(4,2,'Futsal Court','Outdoor Complex',60.00,1,'Covered court with floodlights.','2026-11-20 03:39:46','2026-11-20 03:39:46'),(5,3,'Tennis Court 1','Outdoor Complex',40.00,1,'Hard court. Floodlights until 10 pm.','2026-11-20 03:39:46','2026-11-20 03:39:46'),(6,4,'Volleyball Court','Sports Hall, Level 2',30.00,1,NULL,'2026-11-20 03:39:46','2026-11-20 03:39:46');
/*!40000 ALTER TABLE `facilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_schedules`
--

DROP TABLE IF EXISTS `facility_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facility_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `facility_id` bigint(20) unsigned NOT NULL,
  `day_of_week` tinyint(4) NOT NULL,
  `open_time` time NOT NULL,
  `close_time` time NOT NULL,
  `slot_minutes` smallint(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facility_schedules_facility_id_day_of_week_unique` (`facility_id`,`day_of_week`),
  CONSTRAINT `facility_schedules_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_schedules`
--

LOCK TABLES `facility_schedules` WRITE;
/*!40000 ALTER TABLE `facility_schedules` DISABLE KEYS */;
INSERT INTO `facility_schedules` VALUES (1,1,0,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(2,1,1,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(3,1,2,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(4,1,3,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(5,1,4,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(6,1,5,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(7,1,6,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(8,2,0,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(9,2,1,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(10,2,2,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(11,2,3,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(12,2,4,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(13,2,5,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(14,2,6,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(15,3,0,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(16,3,1,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(17,3,2,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(18,3,3,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(19,3,4,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(20,3,5,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(21,3,6,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(22,4,0,'08:00:00','21:30:00',90,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(23,4,1,'08:00:00','21:30:00',90,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(24,4,2,'08:00:00','21:30:00',90,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(25,4,3,'08:00:00','21:30:00',90,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(26,4,4,'08:00:00','21:30:00',90,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(27,4,5,'08:00:00','21:30:00',90,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(28,4,6,'08:00:00','21:30:00',90,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(29,5,0,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(30,5,1,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(31,5,2,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(32,5,3,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(33,5,4,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(34,5,5,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(35,5,6,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(36,6,0,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(37,6,1,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(38,6,2,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(39,6,3,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(40,6,4,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(41,6,5,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46'),(42,6,6,'08:00:00','22:00:00',60,'2026-11-20 03:39:46','2026-11-20 03:39:46');
/*!40000 ALTER TABLE `facility_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_types`
--

DROP TABLE IF EXISTS `facility_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facility_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facility_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_types`
--

LOCK TABLES `facility_types` WRITE;
/*!40000 ALTER TABLE `facility_types` DISABLE KEYS */;
INSERT INTO `facility_types` VALUES (1,'Badminton','2026-11-20 03:39:46','2026-11-20 03:39:46'),(2,'Futsal','2026-11-20 03:39:46','2026-11-20 03:39:46'),(3,'Tennis','2026-11-20 03:39:46','2026-11-20 03:39:46'),(4,'Volleyball','2026-11-20 03:39:46','2026-11-20 03:39:46');
/*!40000 ALTER TABLE `facility_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000000_create_passkeys_table',1),(5,'2025_08_14_170933_add_two_factor_columns_to_users_table',1),(6,'2026_10_01_000001_add_role_fields_to_users_table',1),(7,'2026_10_01_000002_create_facility_types_table',1),(8,'2026_10_01_000003_create_facilities_table',1),(9,'2026_10_01_000004_create_facility_schedules_table',1),(10,'2026_10_01_000005_create_time_slots_table',1),(11,'2026_10_01_000006_create_bookings_table',1),(12,'2026_10_01_000007_create_payments_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `passkeys`
--

DROP TABLE IF EXISTS `passkeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `passkeys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `credential` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`credential`)),
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  KEY `passkeys_user_id_index` (`user_id`),
  CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `passkeys`
--

LOCK TABLES `passkeys` WRITE;
/*!40000 ALTER TABLE `passkeys` DISABLE KEYS */;
/*!40000 ALTER TABLE `passkeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(6,2) NOT NULL,
  `method` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `reference_no` varchar(50) DEFAULT NULL,
  `receipt_no` varchar(20) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_booking_id_unique` (`booking_id`),
  UNIQUE KEY `payments_receipt_no_unique` (`receipt_no`),
  KEY `payments_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,6,1,20.00,'cash','paid',NULL,'RCP-20261120-0001','2026-11-20 11:39:47','2026-11-20 03:39:47','2026-11-20 03:39:47'),(2,7,1,60.00,'bank_transfer','paid','MBB88231907','RCP-20261120-0002','2026-11-20 11:39:47','2026-11-20 03:39:47','2026-11-20 03:39:47'),(3,12,1,40.00,'cash','paid',NULL,'RCP-20261120-0003','2026-11-20 11:39:47','2026-11-20 03:39:47','2026-11-20 03:39:47');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('B9Zyj1c51z5axCYUa3EkmwxgiBJfiGbgddQIHj5R',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.146.0 Chrome/150.0.7871.250 Electron/43.8.6 Safari/537.36','eyJfdG9rZW4iOiJYZHRxd2toNHJ1UEtHVXRkZlFtNUszMVlWb1hMdnJpekhsYnQ2WVg2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1795175506);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_slots`
--

DROP TABLE IF EXISTS `time_slots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_slots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `facility_id` bigint(20) unsigned NOT NULL,
  `slot_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `time_slots_facility_id_slot_date_start_time_unique` (`facility_id`,`slot_date`,`start_time`),
  KEY `time_slots_slot_date_index` (`slot_date`),
  CONSTRAINT `time_slots_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=541 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_slots`
--

LOCK TABLES `time_slots` WRITE;
/*!40000 ALTER TABLE `time_slots` DISABLE KEYS */;
INSERT INTO `time_slots` VALUES (1,1,'2026-11-20','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(2,1,'2026-11-20','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(3,1,'2026-11-20','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(4,1,'2026-11-20','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(5,1,'2026-11-20','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(6,1,'2026-11-20','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(7,1,'2026-11-20','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(8,1,'2026-11-20','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(9,1,'2026-11-20','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(10,1,'2026-11-20','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(11,1,'2026-11-20','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(12,1,'2026-11-20','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(13,1,'2026-11-20','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(14,1,'2026-11-20','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(15,1,'2026-11-21','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(16,1,'2026-11-21','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(17,1,'2026-11-21','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(18,1,'2026-11-21','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(19,1,'2026-11-21','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(20,1,'2026-11-21','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(21,1,'2026-11-21','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(22,1,'2026-11-21','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(23,1,'2026-11-21','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(24,1,'2026-11-21','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(25,1,'2026-11-21','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(26,1,'2026-11-21','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(27,1,'2026-11-21','20:00:00','21:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(28,1,'2026-11-21','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(29,1,'2026-11-22','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(30,1,'2026-11-22','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(31,1,'2026-11-22','10:00:00','11:00:00','blocked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(32,1,'2026-11-22','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(33,1,'2026-11-22','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(34,1,'2026-11-22','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(35,1,'2026-11-22','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(36,1,'2026-11-22','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(37,1,'2026-11-22','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(38,1,'2026-11-22','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(39,1,'2026-11-22','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:47'),(40,1,'2026-11-22','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(41,1,'2026-11-22','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(42,1,'2026-11-22','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(43,1,'2026-11-23','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(44,1,'2026-11-23','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(45,1,'2026-11-23','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(46,1,'2026-11-23','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(47,1,'2026-11-23','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(48,1,'2026-11-23','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(49,1,'2026-11-23','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(50,1,'2026-11-23','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(51,1,'2026-11-23','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(52,1,'2026-11-23','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(53,1,'2026-11-23','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(54,1,'2026-11-23','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(55,1,'2026-11-23','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(56,1,'2026-11-23','21:00:00','22:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(57,1,'2026-11-24','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(58,1,'2026-11-24','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(59,1,'2026-11-24','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(60,1,'2026-11-24','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(61,1,'2026-11-24','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(62,1,'2026-11-24','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(63,1,'2026-11-24','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(64,1,'2026-11-24','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(65,1,'2026-11-24','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(66,1,'2026-11-24','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(67,1,'2026-11-24','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(68,1,'2026-11-24','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(69,1,'2026-11-24','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(70,1,'2026-11-24','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(71,1,'2026-11-25','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(72,1,'2026-11-25','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(73,1,'2026-11-25','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(74,1,'2026-11-25','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(75,1,'2026-11-25','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(76,1,'2026-11-25','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(77,1,'2026-11-25','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(78,1,'2026-11-25','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(79,1,'2026-11-25','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(80,1,'2026-11-25','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(81,1,'2026-11-25','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(82,1,'2026-11-25','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(83,1,'2026-11-25','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(84,1,'2026-11-25','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(85,1,'2026-11-26','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(86,1,'2026-11-26','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(87,1,'2026-11-26','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(88,1,'2026-11-26','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(89,1,'2026-11-26','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(90,1,'2026-11-26','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(91,1,'2026-11-26','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(92,1,'2026-11-26','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(93,1,'2026-11-26','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(94,1,'2026-11-26','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(95,1,'2026-11-26','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(96,1,'2026-11-26','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(97,1,'2026-11-26','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(98,1,'2026-11-26','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(99,1,'2026-11-27','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(100,1,'2026-11-27','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(101,1,'2026-11-27','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(102,1,'2026-11-27','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(103,1,'2026-11-27','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(104,1,'2026-11-27','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(105,1,'2026-11-27','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(106,1,'2026-11-27','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(107,1,'2026-11-27','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(108,1,'2026-11-27','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(109,1,'2026-11-27','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(110,1,'2026-11-27','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(111,1,'2026-11-27','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(112,1,'2026-11-27','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(113,1,'2026-11-13','18:00:00','19:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(114,1,'2026-11-13','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(115,1,'2026-11-17','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(116,1,'2026-11-17','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(117,2,'2026-11-20','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(118,2,'2026-11-20','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(119,2,'2026-11-20','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(120,2,'2026-11-20','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(121,2,'2026-11-20','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(122,2,'2026-11-20','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(123,2,'2026-11-20','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(124,2,'2026-11-20','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(125,2,'2026-11-20','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(126,2,'2026-11-20','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(127,2,'2026-11-20','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(128,2,'2026-11-20','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(129,2,'2026-11-20','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(130,2,'2026-11-20','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(131,2,'2026-11-21','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(132,2,'2026-11-21','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(133,2,'2026-11-21','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(134,2,'2026-11-21','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(135,2,'2026-11-21','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(136,2,'2026-11-21','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(137,2,'2026-11-21','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(138,2,'2026-11-21','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(139,2,'2026-11-21','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(140,2,'2026-11-21','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(141,2,'2026-11-21','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(142,2,'2026-11-21','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(143,2,'2026-11-21','20:00:00','21:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(144,2,'2026-11-21','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(145,2,'2026-11-22','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(146,2,'2026-11-22','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(147,2,'2026-11-22','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(148,2,'2026-11-22','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(149,2,'2026-11-22','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(150,2,'2026-11-22','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(151,2,'2026-11-22','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(152,2,'2026-11-22','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(153,2,'2026-11-22','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(154,2,'2026-11-22','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(155,2,'2026-11-22','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(156,2,'2026-11-22','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(157,2,'2026-11-22','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(158,2,'2026-11-22','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(159,2,'2026-11-23','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(160,2,'2026-11-23','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(161,2,'2026-11-23','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(162,2,'2026-11-23','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(163,2,'2026-11-23','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(164,2,'2026-11-23','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(165,2,'2026-11-23','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(166,2,'2026-11-23','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(167,2,'2026-11-23','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(168,2,'2026-11-23','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(169,2,'2026-11-23','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(170,2,'2026-11-23','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(171,2,'2026-11-23','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(172,2,'2026-11-23','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(173,2,'2026-11-24','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(174,2,'2026-11-24','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(175,2,'2026-11-24','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(176,2,'2026-11-24','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(177,2,'2026-11-24','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(178,2,'2026-11-24','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(179,2,'2026-11-24','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(180,2,'2026-11-24','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(181,2,'2026-11-24','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(182,2,'2026-11-24','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(183,2,'2026-11-24','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(184,2,'2026-11-24','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(185,2,'2026-11-24','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(186,2,'2026-11-24','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(187,2,'2026-11-25','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(188,2,'2026-11-25','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(189,2,'2026-11-25','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(190,2,'2026-11-25','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(191,2,'2026-11-25','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(192,2,'2026-11-25','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(193,2,'2026-11-25','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(194,2,'2026-11-25','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(195,2,'2026-11-25','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(196,2,'2026-11-25','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(197,2,'2026-11-25','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(198,2,'2026-11-25','19:00:00','20:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(199,2,'2026-11-25','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(200,2,'2026-11-25','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(201,2,'2026-11-26','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(202,2,'2026-11-26','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(203,2,'2026-11-26','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(204,2,'2026-11-26','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(205,2,'2026-11-26','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(206,2,'2026-11-26','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(207,2,'2026-11-26','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(208,2,'2026-11-26','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(209,2,'2026-11-26','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(210,2,'2026-11-26','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(211,2,'2026-11-26','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(212,2,'2026-11-26','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(213,2,'2026-11-26','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(214,2,'2026-11-26','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(215,2,'2026-11-27','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(216,2,'2026-11-27','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(217,2,'2026-11-27','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(218,2,'2026-11-27','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(219,2,'2026-11-27','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(220,2,'2026-11-27','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(221,2,'2026-11-27','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(222,2,'2026-11-27','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(223,2,'2026-11-27','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(224,2,'2026-11-27','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(225,2,'2026-11-27','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(226,2,'2026-11-27','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(227,2,'2026-11-27','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(228,2,'2026-11-27','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(229,2,'2026-11-13','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(230,2,'2026-11-13','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(231,2,'2026-11-17','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(232,2,'2026-11-17','19:00:00','20:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(233,4,'2026-11-20','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(234,4,'2026-11-20','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(235,4,'2026-11-20','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(236,4,'2026-11-20','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(237,4,'2026-11-20','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(238,4,'2026-11-20','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(239,4,'2026-11-20','17:00:00','18:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(240,4,'2026-11-20','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(241,4,'2026-11-20','20:00:00','21:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(242,4,'2026-11-21','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(243,4,'2026-11-21','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(244,4,'2026-11-21','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(245,4,'2026-11-21','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(246,4,'2026-11-21','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(247,4,'2026-11-21','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(248,4,'2026-11-21','17:00:00','18:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(249,4,'2026-11-21','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(250,4,'2026-11-21','20:00:00','21:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(251,4,'2026-11-22','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(252,4,'2026-11-22','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(253,4,'2026-11-22','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(254,4,'2026-11-22','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(255,4,'2026-11-22','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(256,4,'2026-11-22','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(257,4,'2026-11-22','17:00:00','18:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(258,4,'2026-11-22','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(259,4,'2026-11-22','20:00:00','21:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(260,4,'2026-11-23','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(261,4,'2026-11-23','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(262,4,'2026-11-23','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(263,4,'2026-11-23','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(264,4,'2026-11-23','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(265,4,'2026-11-23','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(266,4,'2026-11-23','17:00:00','18:30:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(267,4,'2026-11-23','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(268,4,'2026-11-23','20:00:00','21:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(269,4,'2026-11-24','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(270,4,'2026-11-24','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(271,4,'2026-11-24','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(272,4,'2026-11-24','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(273,4,'2026-11-24','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(274,4,'2026-11-24','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(275,4,'2026-11-24','17:00:00','18:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(276,4,'2026-11-24','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(277,4,'2026-11-24','20:00:00','21:30:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(278,4,'2026-11-25','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(279,4,'2026-11-25','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(280,4,'2026-11-25','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(281,4,'2026-11-25','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(282,4,'2026-11-25','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(283,4,'2026-11-25','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(284,4,'2026-11-25','17:00:00','18:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(285,4,'2026-11-25','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(286,4,'2026-11-25','20:00:00','21:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(287,4,'2026-11-26','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(288,4,'2026-11-26','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(289,4,'2026-11-26','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(290,4,'2026-11-26','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(291,4,'2026-11-26','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(292,4,'2026-11-26','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(293,4,'2026-11-26','17:00:00','18:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(294,4,'2026-11-26','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(295,4,'2026-11-26','20:00:00','21:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(296,4,'2026-11-27','08:00:00','09:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(297,4,'2026-11-27','09:30:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(298,4,'2026-11-27','11:00:00','12:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(299,4,'2026-11-27','12:30:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(300,4,'2026-11-27','14:00:00','15:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(301,4,'2026-11-27','15:30:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(302,4,'2026-11-27','17:00:00','18:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(303,4,'2026-11-27','18:30:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(304,4,'2026-11-27','20:00:00','21:30:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(305,4,'2026-11-13','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(306,4,'2026-11-13','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(307,4,'2026-11-17','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(308,4,'2026-11-17','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(309,5,'2026-11-20','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(310,5,'2026-11-20','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(311,5,'2026-11-20','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(312,5,'2026-11-20','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(313,5,'2026-11-20','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(314,5,'2026-11-20','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(315,5,'2026-11-20','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(316,5,'2026-11-20','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(317,5,'2026-11-20','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(318,5,'2026-11-20','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(319,5,'2026-11-20','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(320,5,'2026-11-20','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(321,5,'2026-11-20','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(322,5,'2026-11-20','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(323,5,'2026-11-21','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(324,5,'2026-11-21','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(325,5,'2026-11-21','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(326,5,'2026-11-21','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(327,5,'2026-11-21','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(328,5,'2026-11-21','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(329,5,'2026-11-21','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(330,5,'2026-11-21','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(331,5,'2026-11-21','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(332,5,'2026-11-21','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(333,5,'2026-11-21','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(334,5,'2026-11-21','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(335,5,'2026-11-21','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(336,5,'2026-11-21','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(337,5,'2026-11-22','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(338,5,'2026-11-22','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(339,5,'2026-11-22','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(340,5,'2026-11-22','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(341,5,'2026-11-22','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(342,5,'2026-11-22','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(343,5,'2026-11-22','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(344,5,'2026-11-22','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(345,5,'2026-11-22','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(346,5,'2026-11-22','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(347,5,'2026-11-22','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(348,5,'2026-11-22','19:00:00','20:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(349,5,'2026-11-22','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(350,5,'2026-11-22','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(351,5,'2026-11-23','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(352,5,'2026-11-23','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(353,5,'2026-11-23','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(354,5,'2026-11-23','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(355,5,'2026-11-23','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(356,5,'2026-11-23','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(357,5,'2026-11-23','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(358,5,'2026-11-23','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(359,5,'2026-11-23','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(360,5,'2026-11-23','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(361,5,'2026-11-23','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(362,5,'2026-11-23','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(363,5,'2026-11-23','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(364,5,'2026-11-23','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(365,5,'2026-11-24','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(366,5,'2026-11-24','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(367,5,'2026-11-24','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(368,5,'2026-11-24','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(369,5,'2026-11-24','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(370,5,'2026-11-24','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(371,5,'2026-11-24','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(372,5,'2026-11-24','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(373,5,'2026-11-24','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(374,5,'2026-11-24','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(375,5,'2026-11-24','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(376,5,'2026-11-24','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(377,5,'2026-11-24','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(378,5,'2026-11-24','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(379,5,'2026-11-25','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(380,5,'2026-11-25','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(381,5,'2026-11-25','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(382,5,'2026-11-25','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(383,5,'2026-11-25','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(384,5,'2026-11-25','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(385,5,'2026-11-25','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(386,5,'2026-11-25','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(387,5,'2026-11-25','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(388,5,'2026-11-25','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(389,5,'2026-11-25','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(390,5,'2026-11-25','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(391,5,'2026-11-25','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(392,5,'2026-11-25','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(393,5,'2026-11-26','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(394,5,'2026-11-26','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(395,5,'2026-11-26','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(396,5,'2026-11-26','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(397,5,'2026-11-26','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(398,5,'2026-11-26','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(399,5,'2026-11-26','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(400,5,'2026-11-26','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(401,5,'2026-11-26','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(402,5,'2026-11-26','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(403,5,'2026-11-26','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(404,5,'2026-11-26','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(405,5,'2026-11-26','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(406,5,'2026-11-26','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(407,5,'2026-11-27','08:00:00','09:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(408,5,'2026-11-27','09:00:00','10:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(409,5,'2026-11-27','10:00:00','11:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(410,5,'2026-11-27','11:00:00','12:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(411,5,'2026-11-27','12:00:00','13:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(412,5,'2026-11-27','13:00:00','14:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(413,5,'2026-11-27','14:00:00','15:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(414,5,'2026-11-27','15:00:00','16:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(415,5,'2026-11-27','16:00:00','17:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(416,5,'2026-11-27','17:00:00','18:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(417,5,'2026-11-27','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(418,5,'2026-11-27','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(419,5,'2026-11-27','20:00:00','21:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(420,5,'2026-11-27','21:00:00','22:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(421,5,'2026-11-13','18:00:00','19:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(422,5,'2026-11-13','19:00:00','20:00:00','available','2026-11-20 03:39:46','2026-11-20 03:39:46'),(423,5,'2026-11-17','18:00:00','19:00:00','booked','2026-11-20 03:39:46','2026-11-20 03:39:47'),(424,5,'2026-11-17','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(425,6,'2026-11-20','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(426,6,'2026-11-20','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(427,6,'2026-11-20','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(428,6,'2026-11-20','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(429,6,'2026-11-20','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(430,6,'2026-11-20','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(431,6,'2026-11-20','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(432,6,'2026-11-20','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(433,6,'2026-11-20','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(434,6,'2026-11-20','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(435,6,'2026-11-20','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(436,6,'2026-11-20','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(437,6,'2026-11-20','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(438,6,'2026-11-20','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(439,6,'2026-11-21','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(440,6,'2026-11-21','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(441,6,'2026-11-21','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(442,6,'2026-11-21','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(443,6,'2026-11-21','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(444,6,'2026-11-21','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(445,6,'2026-11-21','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(446,6,'2026-11-21','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(447,6,'2026-11-21','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(448,6,'2026-11-21','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(449,6,'2026-11-21','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(450,6,'2026-11-21','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(451,6,'2026-11-21','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(452,6,'2026-11-21','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(453,6,'2026-11-22','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(454,6,'2026-11-22','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(455,6,'2026-11-22','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(456,6,'2026-11-22','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(457,6,'2026-11-22','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(458,6,'2026-11-22','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(459,6,'2026-11-22','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(460,6,'2026-11-22','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(461,6,'2026-11-22','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(462,6,'2026-11-22','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(463,6,'2026-11-22','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(464,6,'2026-11-22','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(465,6,'2026-11-22','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(466,6,'2026-11-22','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(467,6,'2026-11-23','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(468,6,'2026-11-23','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(469,6,'2026-11-23','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(470,6,'2026-11-23','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(471,6,'2026-11-23','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(472,6,'2026-11-23','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(473,6,'2026-11-23','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(474,6,'2026-11-23','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(475,6,'2026-11-23','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(476,6,'2026-11-23','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(477,6,'2026-11-23','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(478,6,'2026-11-23','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(479,6,'2026-11-23','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(480,6,'2026-11-23','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(481,6,'2026-11-24','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(482,6,'2026-11-24','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(483,6,'2026-11-24','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(484,6,'2026-11-24','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(485,6,'2026-11-24','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(486,6,'2026-11-24','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(487,6,'2026-11-24','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(488,6,'2026-11-24','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(489,6,'2026-11-24','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(490,6,'2026-11-24','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(491,6,'2026-11-24','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(492,6,'2026-11-24','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(493,6,'2026-11-24','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(494,6,'2026-11-24','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(495,6,'2026-11-25','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(496,6,'2026-11-25','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(497,6,'2026-11-25','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(498,6,'2026-11-25','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(499,6,'2026-11-25','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(500,6,'2026-11-25','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(501,6,'2026-11-25','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(502,6,'2026-11-25','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(503,6,'2026-11-25','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(504,6,'2026-11-25','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(505,6,'2026-11-25','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(506,6,'2026-11-25','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(507,6,'2026-11-25','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(508,6,'2026-11-25','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(509,6,'2026-11-26','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(510,6,'2026-11-26','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(511,6,'2026-11-26','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(512,6,'2026-11-26','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(513,6,'2026-11-26','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(514,6,'2026-11-26','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(515,6,'2026-11-26','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(516,6,'2026-11-26','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(517,6,'2026-11-26','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(518,6,'2026-11-26','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(519,6,'2026-11-26','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(520,6,'2026-11-26','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(521,6,'2026-11-26','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(522,6,'2026-11-26','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(523,6,'2026-11-27','08:00:00','09:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(524,6,'2026-11-27','09:00:00','10:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(525,6,'2026-11-27','10:00:00','11:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(526,6,'2026-11-27','11:00:00','12:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(527,6,'2026-11-27','12:00:00','13:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(528,6,'2026-11-27','13:00:00','14:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(529,6,'2026-11-27','14:00:00','15:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(530,6,'2026-11-27','15:00:00','16:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(531,6,'2026-11-27','16:00:00','17:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(532,6,'2026-11-27','17:00:00','18:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(533,6,'2026-11-27','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(534,6,'2026-11-27','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(535,6,'2026-11-27','20:00:00','21:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(536,6,'2026-11-27','21:00:00','22:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(537,6,'2026-11-13','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(538,6,'2026-11-13','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(539,6,'2026-11-17','18:00:00','19:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47'),(540,6,'2026-11-17','19:00:00','20:00:00','available','2026-11-20 03:39:47','2026-11-20 03:39:47');
/*!40000 ALTER TABLE `time_slots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_student_id_unique` (`student_id`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Sports Centre Admin','admin@gmi.test','2026-11-20 03:39:45','$2y$12$WJVGr4EVmbxadArJ46MUmeuDZH4PJ77YJqvCs5/34KKkLw2snH2Cq','03-89141000',NULL,'admin',NULL,NULL,NULL,'L9jUq2x2Yj','2026-11-20 03:39:46','2026-11-20 03:39:46'),(2,'Ahmad Faris','student@gmi.test','2026-11-20 03:39:46','$2y$12$WJVGr4EVmbxadArJ46MUmeuDZH4PJ77YJqvCs5/34KKkLw2snH2Cq','011-7517039','GMI2023001','student',NULL,NULL,NULL,'slbMRjsomT','2026-11-20 03:39:46','2026-11-20 03:39:46'),(3,'Nurul Aina','aina@gmi.test','2026-11-20 03:39:46','$2y$12$WJVGr4EVmbxadArJ46MUmeuDZH4PJ77YJqvCs5/34KKkLw2snH2Cq','018-6362006','GMI2023002','student',NULL,NULL,NULL,'kWLQRRGZce','2026-11-20 03:39:46','2026-11-20 03:39:46'),(4,'Lim Wei Jie','weijie@gmi.test','2026-11-20 03:39:46','$2y$12$WJVGr4EVmbxadArJ46MUmeuDZH4PJ77YJqvCs5/34KKkLw2snH2Cq','011-4912193','GMI2023003','student',NULL,NULL,NULL,'3q7I2AMCFf','2026-11-20 03:39:46','2026-11-20 03:39:46'),(5,'Siti Rahmah (alumni)','external@gmi.test','2026-11-20 03:39:46','$2y$12$WJVGr4EVmbxadArJ46MUmeuDZH4PJ77YJqvCs5/34KKkLw2snH2Cq','010-6369576',NULL,'external',NULL,NULL,NULL,'2j9kg25Ck0','2026-11-20 03:39:46','2026-11-20 03:39:46'),(6,'Rajesh Kumar','rajesh@gmi.test','2026-11-20 03:39:46','$2y$12$WJVGr4EVmbxadArJ46MUmeuDZH4PJ77YJqvCs5/34KKkLw2snH2Cq','018-2314938',NULL,'external',NULL,NULL,NULL,'2YZ2IHvNxI','2026-11-20 03:39:46','2026-11-20 03:39:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'sports_booking'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-20 20:08:57
