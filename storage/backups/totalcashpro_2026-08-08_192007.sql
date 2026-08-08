-- MySQL dump 10.13  Distrib 5.7.24, for osx11.1 (x86_64)
--
-- Host: 127.0.0.1    Database: totalcashpro
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `access_requests`
--

DROP TABLE IF EXISTS `access_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_employees` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `selected_plan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `access_requests_organization_id_foreign` (`organization_id`),
  KEY `access_requests_status_index` (`status`),
  KEY `access_requests_created_at_index` (`created_at`),
  KEY `access_requests_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `access_requests_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `access_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_requests`
--

LOCK TABLES `access_requests` WRITE;
/*!40000 ALTER TABLE `access_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `access_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint unsigned DEFAULT NULL,
  `actor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_logs_actor_id_foreign` (`actor_id`),
  KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_logs_event_index` (`event`),
  KEY `activity_logs_created_at_index` (`created_at`),
  CONSTRAINT `activity_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `audience` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `target_plan_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization_id` bigint unsigned DEFAULT NULL,
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_app',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_status_index` (`status`),
  KEY `announcements_organization_id_foreign` (`organization_id`),
  CONSTRAINT `announcements_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_breaks`
--

DROP TABLE IF EXISTS `attendance_breaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_breaks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `break_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `kiosk_break_type_id` bigint unsigned DEFAULT NULL,
  `break_started_at` datetime NOT NULL,
  `break_ended_at` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `planned_minutes` smallint unsigned DEFAULT NULL,
  `source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `branch_kiosk_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_breaks_organization_id_foreign` (`organization_id`),
  KEY `attendance_breaks_branch_id_foreign` (`branch_id`),
  KEY `attendance_breaks_user_time_idx` (`user_id`,`break_started_at`),
  KEY `attendance_breaks_branch_kiosk_id_foreign` (`branch_kiosk_id`),
  KEY `attendance_breaks_kiosk_break_type_id_foreign` (`kiosk_break_type_id`),
  CONSTRAINT `attendance_breaks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_breaks_branch_kiosk_id_foreign` FOREIGN KEY (`branch_kiosk_id`) REFERENCES `branch_kiosks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_breaks_kiosk_break_type_id_foreign` FOREIGN KEY (`kiosk_break_type_id`) REFERENCES `kiosk_break_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_breaks_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_breaks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_breaks`
--

LOCK TABLES `attendance_breaks` WRITE;
/*!40000 ALTER TABLE `attendance_breaks` DISABLE KEYS */;
INSERT INTO `attendance_breaks` VALUES (1,1,1,4,'lunch',NULL,'2026-08-03 12:30:00','2026-08-03 13:00:00','completed',0,NULL,'manual',NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `attendance_breaks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_logs`
--

DROP TABLE IF EXISTS `attendance_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `branch_kiosk_id` bigint unsigned DEFAULT NULL,
  `rota_shift_id` bigint unsigned DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `logged_at` datetime NOT NULL,
  `idempotency_key` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_logs_idempotency_key_unique` (`idempotency_key`),
  KEY `attendance_logs_branch_id_foreign` (`branch_id`),
  KEY `attendance_logs_user_id_foreign` (`user_id`),
  KEY `attendance_logs_org_user_time_idx` (`organization_id`,`user_id`,`logged_at`),
  KEY `attendance_logs_branch_kiosk_id_foreign` (`branch_kiosk_id`),
  KEY `attendance_logs_rota_shift_id_foreign` (`rota_shift_id`),
  CONSTRAINT `attendance_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_logs_branch_kiosk_id_foreign` FOREIGN KEY (`branch_kiosk_id`) REFERENCES `branch_kiosks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_logs_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_logs_rota_shift_id_foreign` FOREIGN KEY (`rota_shift_id`) REFERENCES `rota_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_logs`
--

LOCK TABLES `attendance_logs` WRITE;
/*!40000 ALTER TABLE `attendance_logs` DISABLE KEYS */;
INSERT INTO `attendance_logs` VALUES (1,1,1,3,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(2,1,1,3,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(3,1,1,3,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(4,1,1,3,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(5,1,1,3,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(6,1,1,3,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(7,1,1,3,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(8,1,1,3,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(9,1,1,3,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(10,1,1,3,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(11,1,1,3,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(12,1,1,3,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(13,1,1,3,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(14,1,1,3,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(15,1,1,3,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(16,1,1,3,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(17,1,1,3,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(18,1,1,3,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(19,1,1,3,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(20,1,1,3,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(21,1,1,3,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(22,1,1,3,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(23,1,1,3,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(24,1,1,4,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(25,1,1,4,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(26,1,1,4,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(27,1,1,4,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(28,1,1,4,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(29,1,1,4,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(30,1,1,4,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(31,1,1,4,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(32,1,1,4,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(33,1,1,4,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(34,1,1,4,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(35,1,1,4,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(36,1,1,4,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(37,1,1,4,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(38,1,1,4,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(39,1,1,4,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(40,1,1,4,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(41,1,1,4,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(42,1,1,4,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(43,1,1,4,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(44,1,1,4,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(45,1,1,4,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(46,1,1,4,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(47,1,1,4,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(48,1,1,5,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(49,1,1,5,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(50,1,1,5,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(51,1,1,5,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(52,1,1,5,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(53,1,1,5,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(54,1,1,5,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(55,1,1,5,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(56,1,1,5,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(57,1,1,5,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(58,1,1,5,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(59,1,1,5,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(60,1,1,5,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(61,1,1,5,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(62,1,1,5,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(63,1,1,5,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(64,1,1,5,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(65,1,1,5,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(66,1,1,5,NULL,NULL,'clock-in','manual','2026-08-05 09:25:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(67,1,1,5,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(68,1,1,5,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(69,1,1,5,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(70,1,1,5,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(71,1,1,5,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(72,1,1,6,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(73,1,1,6,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(74,1,1,6,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(75,1,1,6,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(76,1,1,6,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(77,1,1,6,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(78,1,1,6,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(79,1,1,6,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(80,1,1,6,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(81,1,1,6,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(82,1,1,6,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(83,1,1,6,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(84,1,1,6,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(85,1,1,6,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(86,1,1,6,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(87,1,1,6,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(88,1,1,6,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(89,1,1,6,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(90,1,1,6,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(91,1,1,6,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(92,1,1,6,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(93,1,1,6,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(94,1,1,6,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(95,1,1,6,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(96,1,1,7,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(97,1,1,7,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(98,1,1,7,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(99,1,1,7,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(100,1,1,7,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(101,1,1,7,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(102,1,1,7,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(103,1,1,7,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(104,1,1,7,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(105,1,1,7,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(106,1,1,7,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(107,1,1,7,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(108,1,1,7,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(109,1,1,7,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(110,1,1,7,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(111,1,1,7,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(112,1,1,7,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(113,1,1,7,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(114,1,1,7,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(115,1,1,7,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(116,1,1,7,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(117,1,1,7,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(118,1,1,7,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(119,1,1,7,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(120,1,2,8,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(121,1,2,8,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(122,1,2,8,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(123,1,2,8,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(124,1,2,8,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(125,1,2,8,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(126,1,2,8,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(127,1,2,8,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(128,1,2,8,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(129,1,2,8,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(130,1,2,8,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(131,1,2,8,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(132,1,2,8,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(133,1,2,8,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(134,1,2,8,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(135,1,2,8,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(136,1,2,8,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(137,1,2,8,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(138,1,2,8,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(139,1,2,8,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(140,1,2,8,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(141,1,2,8,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(142,1,2,8,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(143,1,2,8,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(144,1,2,9,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(145,1,2,9,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(146,1,2,9,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(147,1,2,9,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(148,1,2,9,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(149,1,2,9,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(150,1,2,9,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(151,1,2,9,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(152,1,2,9,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(153,1,2,9,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(154,1,2,9,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(155,1,2,9,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(156,1,2,9,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(157,1,2,9,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(158,1,2,9,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(159,1,2,9,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(160,1,2,9,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(161,1,2,9,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(162,1,2,9,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(163,1,2,9,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(164,1,2,9,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(165,1,2,9,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(166,1,2,9,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(167,1,2,9,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(168,1,2,10,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(169,1,2,10,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(170,1,2,10,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(171,1,2,10,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(172,1,2,10,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(173,1,2,10,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(174,1,2,10,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(175,1,2,10,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(176,1,2,10,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(177,1,2,10,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(178,1,2,10,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(179,1,2,10,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(180,1,2,10,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(181,1,2,10,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(182,1,2,10,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(183,1,2,10,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(184,1,2,10,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(185,1,2,10,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(186,1,2,10,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(187,1,2,10,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(188,1,2,10,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(189,1,2,10,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(190,1,2,10,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(191,1,2,10,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(192,1,2,11,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(193,1,2,11,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(194,1,2,11,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(195,1,2,11,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(196,1,2,11,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(197,1,2,11,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(198,1,2,11,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(199,1,2,11,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(200,1,2,11,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(201,1,2,11,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(202,1,2,11,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(203,1,2,11,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(204,1,2,11,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(205,1,2,11,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(206,1,2,11,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(207,1,2,11,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(208,1,2,11,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(209,1,2,11,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(210,1,2,11,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(211,1,2,11,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(212,1,2,11,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(213,1,2,11,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(214,1,2,11,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(215,1,2,11,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(216,1,2,12,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(217,1,2,12,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(218,1,2,12,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(219,1,2,12,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(220,1,2,12,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(221,1,2,12,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(222,1,2,12,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(223,1,2,12,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(224,1,2,12,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(225,1,2,12,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(226,1,2,12,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(227,1,2,12,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(228,1,2,12,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(229,1,2,12,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(230,1,2,12,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(231,1,2,12,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(232,1,2,12,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(233,1,2,12,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(234,1,2,12,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(235,1,2,12,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(236,1,2,12,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(237,1,2,12,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(238,1,2,12,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(239,1,2,12,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(240,1,3,13,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(241,1,3,13,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(242,1,3,13,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(243,1,3,13,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(244,1,3,13,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(245,1,3,13,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(246,1,3,13,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(247,1,3,13,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(248,1,3,13,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(249,1,3,13,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(250,1,3,13,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(251,1,3,13,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(252,1,3,13,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(253,1,3,13,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(254,1,3,13,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(255,1,3,13,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(256,1,3,13,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(257,1,3,13,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(258,1,3,13,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(259,1,3,13,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(260,1,3,13,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(261,1,3,13,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(262,1,3,13,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(263,1,3,13,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(264,1,3,14,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(265,1,3,14,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(266,1,3,14,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(267,1,3,14,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(268,1,3,14,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(269,1,3,14,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(270,1,3,14,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(271,1,3,14,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(272,1,3,14,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(273,1,3,14,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(274,1,3,14,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(275,1,3,14,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(276,1,3,14,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(277,1,3,14,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(278,1,3,14,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(279,1,3,14,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(280,1,3,14,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(281,1,3,14,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(282,1,3,14,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(283,1,3,14,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(284,1,3,14,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(285,1,3,14,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(286,1,3,14,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(287,1,3,14,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(288,1,3,15,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(289,1,3,15,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(290,1,3,15,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(291,1,3,15,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(292,1,3,15,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(293,1,3,15,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(294,1,3,15,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(295,1,3,15,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(296,1,3,15,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(297,1,3,15,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(298,1,3,15,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(299,1,3,15,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(300,1,3,15,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(301,1,3,15,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(302,1,3,15,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(303,1,3,15,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(304,1,3,15,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(305,1,3,15,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(306,1,3,15,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(307,1,3,15,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(308,1,3,15,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(309,1,3,15,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(310,1,3,15,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(311,1,3,15,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(312,1,3,16,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(313,1,3,16,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(314,1,3,16,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(315,1,3,16,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(316,1,3,16,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(317,1,3,16,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(318,1,3,16,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(319,1,3,16,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(320,1,3,16,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(321,1,3,16,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(322,1,3,16,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(323,1,3,16,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(324,1,3,16,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(325,1,3,16,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(326,1,3,16,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(327,1,3,16,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(328,1,3,16,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(329,1,3,16,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(330,1,3,16,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(331,1,3,16,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(332,1,3,16,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(333,1,3,16,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(334,1,3,16,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(335,1,3,16,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(336,1,3,17,NULL,NULL,'clock-in','manual','2026-07-26 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(337,1,3,17,NULL,NULL,'clock-out','manual','2026-07-26 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(338,1,3,17,NULL,NULL,'clock-in','manual','2026-07-27 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(339,1,3,17,NULL,NULL,'clock-out','manual','2026-07-27 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(340,1,3,17,NULL,NULL,'clock-in','manual','2026-07-28 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(341,1,3,17,NULL,NULL,'clock-out','manual','2026-07-28 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(342,1,3,17,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(343,1,3,17,NULL,NULL,'clock-out','manual','2026-07-29 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(344,1,3,17,NULL,NULL,'clock-in','manual','2026-07-30 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(345,1,3,17,NULL,NULL,'clock-out','manual','2026-07-30 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(346,1,3,17,NULL,NULL,'clock-in','manual','2026-07-31 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(347,1,3,17,NULL,NULL,'clock-out','manual','2026-07-31 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(348,1,3,17,NULL,NULL,'clock-in','manual','2026-08-02 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(349,1,3,17,NULL,NULL,'clock-out','manual','2026-08-02 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(350,1,3,17,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(351,1,3,17,NULL,NULL,'clock-out','manual','2026-08-03 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(352,1,3,17,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(353,1,3,17,NULL,NULL,'clock-out','manual','2026-08-04 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(354,1,3,17,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(355,1,3,17,NULL,NULL,'clock-out','manual','2026-08-05 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(356,1,3,17,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(357,1,3,17,NULL,NULL,'clock-out','manual','2026-08-06 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(358,1,3,17,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(359,1,3,17,NULL,NULL,'clock-out','manual','2026-08-07 17:05:00',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `attendance_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_target_type_target_id_index` (`target_type`,`target_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_webhook_events`
--

DROP TABLE IF EXISTS `billing_webhook_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_webhook_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `external_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `billing_webhook_events_external_id_unique` (`external_id`),
  KEY `billing_webhook_events_provider_type_index` (`provider`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_webhook_events`
--

LOCK TABLES `billing_webhook_events` WRITE;
/*!40000 ALTER TABLE `billing_webhook_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_webhook_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bills`
--

DROP TABLE IF EXISTS `bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `supplier_invoice_id` bigint unsigned DEFAULT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `due_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `bank_account_id` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bills_branch_id_foreign` (`branch_id`),
  KEY `bills_created_by_foreign` (`created_by`),
  KEY `bills_organization_id_status_index` (`organization_id`,`status`),
  KEY `bills_organization_id_due_date_index` (`organization_id`,`due_date`),
  KEY `bills_bank_account_id_foreign` (`bank_account_id`),
  KEY `bills_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  KEY `bills_purchase_order_id_foreign` (`purchase_order_id`),
  CONSTRAINT `bills_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bills_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bills_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bills_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bills_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bills_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bills`
--

LOCK TABLES `bills` WRITE;
/*!40000 ALTER TABLE `bills` DISABLE KEYS */;
INSERT INTO `bills` VALUES (1,1,1,NULL,NULL,'Monthly rent - Dockside','Harbour Properties','rent',3200.00,0.00,0.00,3200.00,'2026-08-18','approved',NULL,NULL,NULL,NULL,NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_kiosks`
--

DROP TABLE IF EXISTS `branch_kiosks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_kiosks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `welcome_message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Welcome — enter your PIN to clock in or out.',
  `show_photos` tinyint(1) NOT NULL DEFAULT '1',
  `settings` json DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `last_started_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_kiosks_token_unique` (`token`),
  KEY `branch_kiosks_organization_id_is_enabled_index` (`organization_id`,`is_enabled`),
  KEY `branch_kiosks_branch_id_is_enabled_index` (`branch_id`,`is_enabled`),
  CONSTRAINT `branch_kiosks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `branch_kiosks_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_kiosks`
--

LOCK TABLES `branch_kiosks` WRITE;
/*!40000 ALTER TABLE `branch_kiosks` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_kiosks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `manager_user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_hours` json DEFAULT NULL,
  `receipt_footer` text COLLATE utf8mb4_unicode_ci,
  `finance_bank_account_id` bigint unsigned DEFAULT NULL,
  `cash_drawer_id` bigint unsigned DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `staff_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branches_organization_id_slug_unique` (`organization_id`,`slug`),
  KEY `branches_organization_id_status_index` (`organization_id`,`status`),
  KEY `branches_status_index` (`status`),
  KEY `branches_manager_user_id_foreign` (`manager_user_id`),
  KEY `branches_finance_bank_account_id_foreign` (`finance_bank_account_id`),
  KEY `branches_cash_drawer_id_foreign` (`cash_drawer_id`),
  CONSTRAINT `branches_cash_drawer_id_foreign` FOREIGN KEY (`cash_drawer_id`) REFERENCES `cash_drawers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `branches_finance_bank_account_id_foreign` FOREIGN KEY (`finance_bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `branches_manager_user_id_foreign` FOREIGN KEY (`manager_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `branches_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,3,'Dockside Kitchen','dockside-kitchen','Brighton','12 Marina Parade, Brighton','+44 7700 90010','dockside-kitchen@harbourkitchen.test','BN1 1AA','{\"mon\": \"08:00-23:00\", \"sun\": \"09:00-22:00\"}','Thank you for dining with Harbour Kitchen Group',NULL,NULL,'{\"till_count\": 3}','open',0,'2026-08-08 14:19:49','2026-08-08 14:20:01',NULL),(2,1,NULL,'Harbour Central','harbour-central','London','48 Harbour Way, Canary Wharf, London','+44 7700 90011','harbour-central@harbourkitchen.test','E14 5AB','{\"mon\": \"08:00-23:00\", \"sun\": \"09:00-22:00\"}','Thank you for dining with Harbour Kitchen Group',NULL,NULL,'{\"till_count\": 2}','open',0,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(3,1,NULL,'Riverside','riverside','Kingston','3 Thames Street, Kingston upon Thames','+44 7700 90012','riverside@harbourkitchen.test','KT1 1HL','{\"mon\": \"08:00-23:00\", \"sun\": \"09:00-22:00\"}','Thank you for dining with Harbour Kitchen Group',NULL,NULL,'{\"till_count\": 2}','open',0,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budget_lines`
--

DROP TABLE IF EXISTS `budget_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budget_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `budget_id` bigint unsigned NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `budget_lines_budget_id_category_unique` (`budget_id`,`category`),
  CONSTRAINT `budget_lines_budget_id_foreign` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budget_lines`
--

LOCK TABLES `budget_lines` WRITE;
/*!40000 ALTER TABLE `budget_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `year` smallint unsigned NOT NULL,
  `month` tinyint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `budgets_branch_id_foreign` (`branch_id`),
  KEY `budgets_created_by_foreign` (`created_by`),
  KEY `budgets_organization_id_year_month_index` (`organization_id`,`year`,`month`),
  CONSTRAINT `budgets_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `budgets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `budgets_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budgets`
--

LOCK TABLES `budgets` WRITE;
/*!40000 ALTER TABLE `budgets` DISABLE KEYS */;
/*!40000 ALTER TABLE `budgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_alert_rules`
--

DROP TABLE IF EXISTS `business_alert_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_alert_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `rule_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `threshold_value` decimal(12,2) DEFAULT NULL,
  `threshold_percent` decimal(5,2) DEFAULT NULL,
  `threshold_days` smallint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alert_rule_unique` (`organization_id`,`branch_id`,`rule_type`),
  KEY `business_alert_rules_branch_id_foreign` (`branch_id`),
  CONSTRAINT `business_alert_rules_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `business_alert_rules_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_alert_rules`
--

LOCK TABLES `business_alert_rules` WRITE;
/*!40000 ALTER TABLE `business_alert_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_alert_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_alerts`
--

DROP TABLE IF EXISTS `business_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `alert_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `acknowledged_by` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `business_alerts_branch_id_foreign` (`branch_id`),
  KEY `business_alerts_acknowledged_by_foreign` (`acknowledged_by`),
  KEY `business_alerts_resolved_by_foreign` (`resolved_by`),
  KEY `business_alerts_organization_id_status_priority_index` (`organization_id`,`status`,`priority`),
  KEY `alert_ref_idx` (`alert_type`,`reference_type`,`reference_id`),
  CONSTRAINT `business_alerts_acknowledged_by_foreign` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `business_alerts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `business_alerts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `business_alerts_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_alerts`
--

LOCK TABLES `business_alerts` WRITE;
/*!40000 ALTER TABLE `business_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('totalcashpro-cache-report_center_version:1','i:503;',2101576801);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
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
-- Table structure for table `cash_drawer_sessions`
--

DROP TABLE IF EXISTS `cash_drawer_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_drawer_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `cash_drawer_id` bigint unsigned NOT NULL,
  `opened_by_user_id` bigint unsigned NOT NULL,
  `closed_by_user_id` bigint unsigned DEFAULT NULL,
  `opened_at` timestamp NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `opening_float` decimal(12,2) NOT NULL DEFAULT '0.00',
  `opening_count` json DEFAULT NULL,
  `closing_count` json DEFAULT NULL,
  `expected_cash` decimal(12,2) DEFAULT NULL,
  `actual_cash` decimal(12,2) DEFAULT NULL,
  `variance` decimal(12,2) DEFAULT NULL,
  `variance_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_drawer_sessions_branch_id_foreign` (`branch_id`),
  KEY `cash_drawer_sessions_opened_by_user_id_foreign` (`opened_by_user_id`),
  KEY `cash_drawer_sessions_closed_by_user_id_foreign` (`closed_by_user_id`),
  KEY `cash_drawer_sessions_cash_drawer_id_status_index` (`cash_drawer_id`,`status`),
  KEY `cash_drawer_sessions_organization_id_branch_id_opened_at_index` (`organization_id`,`branch_id`,`opened_at`),
  CONSTRAINT `cash_drawer_sessions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawer_sessions_cash_drawer_id_foreign` FOREIGN KEY (`cash_drawer_id`) REFERENCES `cash_drawers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawer_sessions_closed_by_user_id_foreign` FOREIGN KEY (`closed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawer_sessions_opened_by_user_id_foreign` FOREIGN KEY (`opened_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawer_sessions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_drawer_sessions`
--

LOCK TABLES `cash_drawer_sessions` WRITE;
/*!40000 ALTER TABLE `cash_drawer_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_drawer_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_drawer_transactions`
--

DROP TABLE IF EXISTS `cash_drawer_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_drawer_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cash_drawer_id` bigint unsigned NOT NULL,
  `cash_drawer_session_id` bigint unsigned DEFAULT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paired_transaction_id` bigint unsigned DEFAULT NULL,
  `transfer_drawer_id` bigint unsigned DEFAULT NULL,
  `approval_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `cash_up_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_drawer_transactions_organization_id_foreign` (`organization_id`),
  KEY `cash_drawer_transactions_branch_id_foreign` (`branch_id`),
  KEY `cash_drawer_transactions_cash_up_id_foreign` (`cash_up_id`),
  KEY `cash_drawer_transactions_created_by_foreign` (`created_by`),
  KEY `cash_drawer_transactions_cash_drawer_id_created_at_index` (`cash_drawer_id`,`created_at`),
  KEY `cash_drawer_transactions_cash_drawer_session_id_foreign` (`cash_drawer_session_id`),
  KEY `cash_drawer_transactions_paired_transaction_id_foreign` (`paired_transaction_id`),
  KEY `cash_drawer_transactions_transfer_drawer_id_foreign` (`transfer_drawer_id`),
  KEY `cash_drawer_transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `cash_drawer_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawer_transactions_cash_drawer_id_foreign` FOREIGN KEY (`cash_drawer_id`) REFERENCES `cash_drawers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawer_transactions_cash_drawer_session_id_foreign` FOREIGN KEY (`cash_drawer_session_id`) REFERENCES `cash_drawer_sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawer_transactions_cash_up_id_foreign` FOREIGN KEY (`cash_up_id`) REFERENCES `cash_ups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawer_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawer_transactions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawer_transactions_paired_transaction_id_foreign` FOREIGN KEY (`paired_transaction_id`) REFERENCES `cash_drawer_transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawer_transactions_transfer_drawer_id_foreign` FOREIGN KEY (`transfer_drawer_id`) REFERENCES `cash_drawers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_drawer_transactions`
--

LOCK TABLES `cash_drawer_transactions` WRITE;
/*!40000 ALTER TABLE `cash_drawer_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_drawer_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_drawers`
--

DROP TABLE IF EXISTS `cash_drawers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_drawers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `assigned_user_id` bigint unsigned DEFAULT NULL,
  `finance_bank_account_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_opened_at` timestamp NULL DEFAULT NULL,
  `last_closed_at` timestamp NULL DEFAULT NULL,
  `last_cash_up_at` timestamp NULL DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_drawers_branch_code_unique` (`organization_id`,`branch_id`,`code`),
  KEY `cash_drawers_branch_id_foreign` (`branch_id`),
  KEY `cash_drawers_finance_bank_account_id_foreign` (`finance_bank_account_id`),
  KEY `cash_drawers_organization_id_branch_id_index` (`organization_id`,`branch_id`),
  KEY `cash_drawers_assigned_user_id_foreign` (`assigned_user_id`),
  KEY `cash_drawers_created_by_foreign` (`created_by`),
  KEY `cash_drawers_updated_by_foreign` (`updated_by`),
  CONSTRAINT `cash_drawers_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawers_finance_bank_account_id_foreign` FOREIGN KEY (`finance_bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_drawers_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_drawers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_drawers`
--

LOCK TABLES `cash_drawers` WRITE;
/*!40000 ALTER TABLE `cash_drawers` DISABLE KEYS */;
INSERT INTO `cash_drawers` VALUES (1,1,1,'Till 1','TILL-01',100.00,462.40,'GBP',NULL,NULL,1,'active',NULL,NULL,'2026-08-08 14:20:02','{\"default_opening_float\": 100}',2,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:02'),(2,1,1,'Till 2','TILL-02',100.00,362.40,'GBP',NULL,NULL,1,'active',NULL,NULL,'2026-08-08 14:20:02','{\"default_opening_float\": 100}',2,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:02'),(3,1,1,'Till 3','TILL-03',100.00,100.00,'GBP',NULL,NULL,1,'active',NULL,NULL,NULL,'{\"default_opening_float\": 100}',2,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(4,1,2,'Till 1','TILL-01',100.00,512.40,'GBP',NULL,NULL,1,'active',NULL,NULL,'2026-08-08 14:20:02','{\"default_opening_float\": 100}',2,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:02'),(5,1,2,'Till 2','TILL-02',100.00,412.40,'GBP',NULL,NULL,1,'active',NULL,NULL,'2026-08-08 14:20:02','{\"default_opening_float\": 100}',2,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:02'),(6,1,3,'Till 1','TILL-01',100.00,312.40,'GBP',NULL,NULL,1,'active',NULL,NULL,'2026-08-08 14:20:02','{\"default_opening_float\": 100}',2,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:02'),(7,1,3,'Till 2','TILL-02',100.00,100.00,'GBP',NULL,NULL,1,'active',NULL,NULL,NULL,'{\"default_opening_float\": 100}',2,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `cash_drawers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_ups`
--

DROP TABLE IF EXISTS `cash_ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_ups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `cash_drawer_id` bigint unsigned DEFAULT NULL,
  `cash_drawer_session_id` bigint unsigned DEFAULT NULL,
  `cashup_date` date NOT NULL,
  `shift` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opening_float` decimal(12,2) NOT NULL DEFAULT '0.00',
  `opening_float_count` json DEFAULT NULL,
  `cash_sales_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `coins_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `coins_detail` json DEFAULT NULL,
  `notes_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes_detail` json DEFAULT NULL,
  `cards_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cards_detail` json DEFAULT NULL,
  `expenses_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `expenses_detail` json DEFAULT NULL,
  `online_orders_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `online_orders_detail` json DEFAULT NULL,
  `platform_deductions_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `platform_deductions_detail` json DEFAULT NULL,
  `expected_cash` decimal(12,2) DEFAULT NULL,
  `actual_cash` decimal(12,2) DEFAULT NULL,
  `variance` decimal(12,2) DEFAULT NULL,
  `variance_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `approved_by_user_id` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_ups_unique_shift_drawer` (`organization_id`,`branch_id`,`cash_drawer_id`,`cashup_date`,`shift`),
  KEY `cash_ups_branch_id_foreign` (`branch_id`),
  KEY `cash_ups_created_by_foreign` (`created_by`),
  KEY `cash_ups_organization_id_cashup_date_index` (`organization_id`,`cashup_date`),
  KEY `cash_ups_cash_drawer_session_id_foreign` (`cash_drawer_session_id`),
  KEY `cash_ups_approved_by_user_id_foreign` (`approved_by_user_id`),
  KEY `cash_ups_cash_drawer_id_cashup_date_index` (`cash_drawer_id`,`cashup_date`),
  KEY `cash_ups_organization_id_status_index` (`organization_id`,`status`),
  CONSTRAINT `cash_ups_approved_by_user_id_foreign` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_ups_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_ups_cash_drawer_id_foreign` FOREIGN KEY (`cash_drawer_id`) REFERENCES `cash_drawers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_ups_cash_drawer_session_id_foreign` FOREIGN KEY (`cash_drawer_session_id`) REFERENCES `cash_drawer_sessions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_ups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_ups_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_ups`
--

LOCK TABLES `cash_ups` WRITE;
/*!40000 ALTER TABLE `cash_ups` DISABLE KEYS */;
INSERT INTO `cash_ups` VALUES (1,1,1,1,NULL,'2026-08-08','Morning',100.00,NULL,512.40,12.40,'[{\"qty\": 8, \"coin\": \"£1\"}, {\"qty\": 12, \"coin\": \"20p\"}]',550.00,'[{\"qty\": 11, \"note\": \"£50\", \"is_qty\": true}]',0.00,'[]',50.00,'[{\"amount\": 50, \"description\": \"Supplies\"}]',45.00,'[{\"amount\": 45, \"platform\": \"Uber Eats\"}]',0.00,NULL,562.40,562.40,0.00,NULL,'approved',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02',2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(2,1,1,1,NULL,'2026-08-08','Evening',100.00,NULL,397.40,12.40,'[{\"qty\": 8, \"coin\": \"£1\"}, {\"qty\": 12, \"coin\": \"20p\"}]',450.00,'[{\"qty\": 9, \"note\": \"£50\", \"is_qty\": true}]',180.00,'[{\"type\": \"machine\", \"amount\": 180, \"payment_type\": \"Card Machine\"}]',35.00,'[{\"amount\": 35, \"description\": \"Supplies\"}]',45.00,'[{\"amount\": 45, \"platform\": \"Uber Eats\"}]',0.00,NULL,462.40,462.40,0.00,NULL,'approved',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02',2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(3,1,1,2,NULL,'2026-08-08','Evening',100.00,NULL,282.40,12.40,'[{\"qty\": 8, \"coin\": \"£1\"}, {\"qty\": 12, \"coin\": \"20p\"}]',350.00,'[{\"qty\": 7, \"note\": \"£50\", \"is_qty\": true}]',120.00,'[{\"type\": \"machine\", \"amount\": 120, \"payment_type\": \"Card Machine\"}]',20.00,'[{\"amount\": 20, \"description\": \"Supplies\"}]',45.00,'[{\"amount\": 45, \"platform\": \"Uber Eats\"}]',0.00,NULL,362.40,362.40,0.00,NULL,'approved',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02',2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(4,1,2,4,NULL,'2026-08-07','Morning',100.00,NULL,452.40,12.40,'[{\"qty\": 8, \"coin\": \"£1\"}, {\"qty\": 12, \"coin\": \"20p\"}]',500.00,'[{\"qty\": 10, \"note\": \"£50\", \"is_qty\": true}]',0.00,'[]',40.00,'[{\"amount\": 40, \"description\": \"Supplies\"}]',45.00,'[{\"amount\": 45, \"platform\": \"Uber Eats\"}]',0.00,NULL,512.40,512.40,0.00,NULL,'approved',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02',2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(5,1,2,5,NULL,'2026-08-07','Evening',100.00,NULL,337.40,12.40,'[{\"qty\": 8, \"coin\": \"£1\"}, {\"qty\": 12, \"coin\": \"20p\"}]',400.00,'[{\"qty\": 8, \"note\": \"£50\", \"is_qty\": true}]',200.00,'[{\"type\": \"machine\", \"amount\": 200, \"payment_type\": \"Card Machine\"}]',25.00,'[{\"amount\": 25, \"description\": \"Supplies\"}]',45.00,'[{\"amount\": 45, \"platform\": \"Uber Eats\"}]',0.00,NULL,412.40,412.40,0.00,NULL,'approved',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02',2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(6,1,3,6,NULL,'2026-08-06','Evening',100.00,NULL,227.40,12.40,'[{\"qty\": 8, \"coin\": \"£1\"}, {\"qty\": 12, \"coin\": \"20p\"}]',300.00,'[{\"qty\": 6, \"note\": \"£50\", \"is_qty\": true}]',95.00,'[{\"type\": \"machine\", \"amount\": 95, \"payment_type\": \"Card Machine\"}]',15.00,'[{\"amount\": 15, \"description\": \"Supplies\"}]',45.00,'[{\"amount\": 45, \"platform\": \"Uber Eats\"}]',0.00,NULL,312.40,312.40,0.00,NULL,'approved',NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02',2,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `cash_ups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_faqs`
--

DROP TABLE IF EXISTS `cms_faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_faqs_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_faqs`
--

LOCK TABLES `cms_faqs` WRITE;
/*!40000 ALTER TABLE `cms_faqs` DISABLE KEYS */;
INSERT INTO `cms_faqs` VALUES (1,'How much does TotalCashPro cost?','Basic is £19.99/month. Professional is £29.99/month.',1,'published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(2,'Can I sign up instantly?','No. Submit a request and our team reviews it before creating your account.',2,'published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `cms_faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_features`
--

DROP TABLE IF EXISTS `cms_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_features` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `plan_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_features_plan_slug_index` (`plan_slug`),
  KEY `cms_features_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_features`
--

LOCK TABLES `cms_features` WRITE;
/*!40000 ALTER TABLE `cms_features` DISABLE KEYS */;
INSERT INTO `cms_features` VALUES (1,'Daily Cash Up','Daily Cash Up for TotalCashPro customers.','basic',NULL,1,'published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(2,'Staff Clock In & Out','Staff Clock In & Out for TotalCashPro customers.','basic',NULL,2,'published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(3,'Inventory Management','Inventory Management for TotalCashPro customers.','professional',NULL,9,'published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(4,'Payroll & Wages','Payroll & Wages for TotalCashPro customers.','professional',NULL,10,'published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `cms_features` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_hero_sections`
--

DROP TABLE IF EXISTS `cms_hero_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_hero_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `page_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home',
  `eyebrow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subheadline` text COLLATE utf8mb4_unicode_ci,
  `primary_cta_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_cta_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_cta_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_cta_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_hero_sections_page_key_index` (`page_key`),
  KEY `cms_hero_sections_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_hero_sections`
--

LOCK TABLES `cms_hero_sections` WRITE;
/*!40000 ALTER TABLE `cms_hero_sections` DISABLE KEYS */;
INSERT INTO `cms_hero_sections` VALUES (1,'home','Cloud software for restaurants & retail','Manage cash, staff and reports from one secure dashboard','Built for restaurants, cafés, takeaways and retail businesses.','Start Free Trial','/register','Choose Your Plan','/#pricing',NULL,'published',1,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `cms_hero_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_pages`
--

DROP TABLE IF EXISTS `cms_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `meta` json DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_pages_slug_unique` (`slug`),
  KEY `cms_pages_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_pages`
--

LOCK TABLES `cms_pages` WRITE;
/*!40000 ALTER TABLE `cms_pages` DISABLE KEYS */;
INSERT INTO `cms_pages` VALUES (1,'Home','home',NULL,'published',NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(2,'About','about',NULL,'published',NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(3,'Contact','contact',NULL,'published',NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(4,'Privacy','privacy',NULL,'published',NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(5,'Terms','terms',NULL,'published',NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `cms_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_testimonials`
--

DROP TABLE IF EXISTS `cms_testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_testimonials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_testimonials_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_testimonials`
--

LOCK TABLES `cms_testimonials` WRITE;
/*!40000 ALTER TABLE `cms_testimonials` DISABLE KEYS */;
INSERT INTO `cms_testimonials` VALUES (1,'Amelia Hart','Operations Manager','Northbridge Kitchen','Cash ups and staff attendance finally live in one place.',1,1,'published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `cms_testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `max_uses` int unsigned DEFAULT NULL,
  `used_count` int unsigned NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `plan_id` bigint unsigned DEFAULT NULL,
  `organization_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`),
  KEY `coupons_expires_at_index` (`expires_at`),
  KEY `coupons_status_index` (`status`),
  KEY `coupons_plan_id_foreign` (`plan_id`),
  KEY `coupons_organization_id_foreign` (`organization_id`),
  CONSTRAINT `coupons_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `coupons_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_customer_notes`
--

DROP TABLE IF EXISTS `crm_customer_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_customer_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crm_customer_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_customer_notes_crm_customer_id_foreign` (`crm_customer_id`),
  KEY `crm_customer_notes_organization_id_foreign` (`organization_id`),
  KEY `crm_customer_notes_created_by_foreign` (`created_by`),
  CONSTRAINT `crm_customer_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_customer_notes_crm_customer_id_foreign` FOREIGN KEY (`crm_customer_id`) REFERENCES `crm_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_customer_notes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_customer_notes`
--

LOCK TABLES `crm_customer_notes` WRITE;
/*!40000 ALTER TABLE `crm_customer_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_customer_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_customer_visits`
--

DROP TABLE IF EXISTS `crm_customer_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_customer_visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crm_customer_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `visited_at` timestamp NOT NULL,
  `spend_amount` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_customer_visits_crm_customer_id_foreign` (`crm_customer_id`),
  KEY `crm_customer_visits_organization_id_foreign` (`organization_id`),
  KEY `crm_customer_visits_branch_id_foreign` (`branch_id`),
  CONSTRAINT `crm_customer_visits_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_customer_visits_crm_customer_id_foreign` FOREIGN KEY (`crm_customer_id`) REFERENCES `crm_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_customer_visits_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_customer_visits`
--

LOCK TABLES `crm_customer_visits` WRITE;
/*!40000 ALTER TABLE `crm_customer_visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_customer_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_customers`
--

DROP TABLE IF EXISTS `crm_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marketing_preferences` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_customers_branch_id_foreign` (`branch_id`),
  KEY `crm_customers_organization_id_email_index` (`organization_id`,`email`),
  CONSTRAINT `crm_customers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_customers_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_customers`
--

LOCK TABLES `crm_customers` WRITE;
/*!40000 ALTER TABLE `crm_customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deliveries`
--

DROP TABLE IF EXISTS `deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `purchase_order_id` bigint unsigned NOT NULL,
  `rider_id` bigint unsigned DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `priority` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `expected_pickup_at` timestamp NULL DEFAULT NULL,
  `expected_delivery_at` timestamp NULL DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `at_supplier_at` timestamp NULL DEFAULT NULL,
  `collected_at` timestamp NULL DEFAULT NULL,
  `out_for_delivery_at` timestamp NULL DEFAULT NULL,
  `arrived_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `awaiting_receiving` tinyint(1) NOT NULL DEFAULT '0',
  `failed_at` timestamp NULL DEFAULT NULL,
  `failure_reason` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `pickup_notes` text COLLATE utf8mb4_unicode_ci,
  `pickup_discrepancy_qty` decimal(12,3) DEFAULT NULL,
  `pickup_discrepancy_reason` text COLLATE utf8mb4_unicode_ci,
  `delivery_notes` text COLLATE utf8mb4_unicode_ci,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliveries_organization_id_foreign` (`organization_id`),
  KEY `deliveries_branch_id_foreign` (`branch_id`),
  KEY `deliveries_assigned_by_foreign` (`assigned_by`),
  KEY `deliveries_rider_id_status_index` (`rider_id`,`status`),
  KEY `deliveries_purchase_order_id_index` (`purchase_order_id`),
  CONSTRAINT `deliveries_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `deliveries_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deliveries_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deliveries_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `deliveries_rider_id_foreign` FOREIGN KEY (`rider_id`) REFERENCES `riders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deliveries`
--

LOCK TABLES `deliveries` WRITE;
/*!40000 ALTER TABLE `deliveries` DISABLE KEYS */;
INSERT INTO `deliveries` VALUES (1,1,1,1,1,'delivered','normal',NULL,'2026-08-06 14:20:02',NULL,NULL,NULL,NULL,NULL,'2026-08-06 14:20:02',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_events`
--

DROP TABLE IF EXISTS `delivery_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` bigint unsigned NOT NULL,
  `event` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_events_created_by_foreign` (`created_by`),
  KEY `delivery_events_delivery_id_created_at_index` (`delivery_id`,`created_at`),
  CONSTRAINT `delivery_events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_events_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_events`
--

LOCK TABLES `delivery_events` WRITE;
/*!40000 ALTER TABLE `delivery_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_proofs`
--

DROP TABLE IF EXISTS `delivery_proofs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_proofs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` bigint unsigned NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_proofs_delivery_id_foreign` (`delivery_id`),
  CONSTRAINT `delivery_proofs_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_proofs`
--

LOCK TABLES `delivery_proofs` WRITE;
/*!40000 ALTER TABLE `delivery_proofs` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_proofs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grant_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) DEFAULT NULL,
  `custom_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discounts_organization_id_status_index` (`organization_id`,`status`),
  KEY `discounts_status_index` (`status`),
  CONSTRAINT `discounts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discounts`
--

LOCK TABLES `discounts` WRITE;
/*!40000 ALTER TABLE `discounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci,
  `trigger` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_slug_unique` (`slug`),
  KEY `email_templates_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES (1,'Access credentials','access-credentials','Your TotalCashPro login details','Your account has been created. Email: {{email}} Password: {{password}}','Account created','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(2,'Welcome Email','welcome','Welcome to TotalCashPro','Welcome aboard, {{name}}.','Welcome','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(3,'Reset Password','reset-password','Reset your TotalCashPro password','Use this link to reset your password: {{url}}','Password reset','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(4,'Trial Started','trial-started','Your trial has started','Your {{plan}} trial is active until {{ends_at}}.','Trial started','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(5,'Trial Ending','trial-ending','Your trial ends soon','Your trial ends on {{ends_at}}. Upgrade to keep access.','Trial ending','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(6,'Subscription Active','subscription-active','Subscription activated','Your {{plan}} subscription is now active.','Subscription active','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(7,'Subscription Expired','subscription-expired','Subscription expired','Your subscription has expired. Renew to restore access.','Subscription expired','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(8,'Invoice','invoice','Your invoice {{invoice}}','Invoice {{invoice}} for {{amount}} is attached.','Invoice','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(9,'Payment Success','payment-success','Payment received','We received your payment of {{amount}}.','Payment success','en','published','2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `finance_attachments`
--

DROP TABLE IF EXISTS `finance_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `attachable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachable_id` bigint unsigned NOT NULL,
  `disk` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL DEFAULT '0',
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_attachments_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`),
  KEY `finance_attachments_uploaded_by_foreign` (`uploaded_by`),
  KEY `finance_attachments_org_morph_idx` (`organization_id`,`attachable_type`,`attachable_id`),
  CONSTRAINT `finance_attachments_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_attachments`
--

LOCK TABLES `finance_attachments` WRITE;
/*!40000 ALTER TABLE `finance_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_bank_accounts`
--

DROP TABLE IF EXISTS `finance_bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number_last4` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `opening_balance` decimal(14,2) NOT NULL DEFAULT '0.00',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_bank_accounts_branch_id_foreign` (`branch_id`),
  KEY `finance_bank_accounts_organization_id_branch_id_index` (`organization_id`,`branch_id`),
  CONSTRAINT `finance_bank_accounts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finance_bank_accounts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_bank_accounts`
--

LOCK TABLES `finance_bank_accounts` WRITE;
/*!40000 ALTER TABLE `finance_bank_accounts` DISABLE KEYS */;
INSERT INTO `finance_bank_accounts` VALUES (1,1,1,'Main Operating Account','Barclays','20-00-00','4821','GBP',25000.00,1,1,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `finance_bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_income_entries`
--

DROP TABLE IF EXISTS `finance_income_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_income_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `bank_account_id` bigint unsigned DEFAULT NULL,
  `source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `net_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `income_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_income_entries_branch_id_foreign` (`branch_id`),
  KEY `finance_income_entries_bank_account_id_foreign` (`bank_account_id`),
  KEY `finance_income_entries_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `finance_income_entries_created_by_foreign` (`created_by`),
  KEY `finance_income_entries_organization_id_income_date_index` (`organization_id`,`income_date`),
  KEY `finance_income_entries_organization_id_status_index` (`organization_id`,`status`),
  CONSTRAINT `finance_income_entries_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finance_income_entries_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_income_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finance_income_entries_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_income_entries`
--

LOCK TABLES `finance_income_entries` WRITE;
/*!40000 ALTER TABLE `finance_income_entries` DISABLE KEYS */;
INSERT INTO `finance_income_entries` VALUES (1,1,1,NULL,'cash_up','App\\Models\\CashUp',1,'Cash up 08 Aug Morning',557.40,0.00,557.40,'2026-08-08','approved',NULL,'2026-08-08 14:20:02',NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(2,1,1,NULL,'cash_up','App\\Models\\CashUp',2,'Cash up 08 Aug Evening',622.40,0.00,622.40,'2026-08-08','approved',NULL,'2026-08-08 14:20:02',NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(3,1,1,NULL,'cash_up','App\\Models\\CashUp',3,'Cash up 08 Aug Evening',447.40,0.00,447.40,'2026-08-08','approved',NULL,'2026-08-08 14:20:02',NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(4,1,2,NULL,'cash_up','App\\Models\\CashUp',4,'Cash up 07 Aug Morning',497.40,0.00,497.40,'2026-08-07','approved',NULL,'2026-08-08 14:20:02',NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(5,1,2,NULL,'cash_up','App\\Models\\CashUp',5,'Cash up 07 Aug Evening',582.40,0.00,582.40,'2026-08-07','approved',NULL,'2026-08-08 14:20:02',NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(6,1,3,NULL,'cash_up','App\\Models\\CashUp',6,'Cash up 06 Aug Evening',367.40,0.00,367.40,'2026-08-06','approved',NULL,'2026-08-08 14:20:02',NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `finance_income_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_integration_connections`
--

DROP TABLE IF EXISTS `finance_integration_connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_integration_connections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `provider` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disconnected',
  `external_account_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `connected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `finance_integrations_org_provider_unique` (`organization_id`,`provider`),
  CONSTRAINT `finance_integration_connections_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_integration_connections`
--

LOCK TABLES `finance_integration_connections` WRITE;
/*!40000 ALTER TABLE `finance_integration_connections` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_integration_connections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_payroll_runs`
--

DROP TABLE IF EXISTS `finance_payroll_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_payroll_runs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `week_start` date NOT NULL,
  `week_end` date NOT NULL,
  `payment_due_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `finance_payroll_runs_week_unique` (`organization_id`,`branch_id`,`week_start`),
  KEY `finance_payroll_runs_branch_id_foreign` (`branch_id`),
  KEY `finance_payroll_runs_created_by_foreign` (`created_by`),
  KEY `finance_payroll_runs_organization_id_status_index` (`organization_id`,`status`),
  CONSTRAINT `finance_payroll_runs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_payroll_runs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finance_payroll_runs_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_payroll_runs`
--

LOCK TABLES `finance_payroll_runs` WRITE;
/*!40000 ALTER TABLE `finance_payroll_runs` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_payroll_runs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_supplier_payments`
--

DROP TABLE IF EXISTS `finance_supplier_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_supplier_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `supplier_invoice_id` bigint unsigned NOT NULL,
  `bank_account_id` bigint unsigned DEFAULT NULL,
  `net_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `payment_date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_supplier_payments_branch_id_foreign` (`branch_id`),
  KEY `finance_supplier_payments_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  KEY `finance_supplier_payments_bank_account_id_foreign` (`bank_account_id`),
  KEY `finance_supplier_payments_created_by_foreign` (`created_by`),
  KEY `finance_supplier_payments_organization_id_payment_date_index` (`organization_id`,`payment_date`),
  CONSTRAINT `finance_supplier_payments_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finance_supplier_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_supplier_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `finance_supplier_payments_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_supplier_payments_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_supplier_payments`
--

LOCK TABLES `finance_supplier_payments` WRITE;
/*!40000 ALTER TABLE `finance_supplier_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_supplier_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_received_lines`
--

DROP TABLE IF EXISTS `goods_received_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_received_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `goods_received_note_id` bigint unsigned NOT NULL,
  `purchase_order_line_id` bigint unsigned NOT NULL,
  `quantity_received` decimal(12,3) NOT NULL DEFAULT '0.000',
  `quantity_damaged` decimal(12,3) NOT NULL DEFAULT '0.000',
  `quantity_missing` decimal(12,3) NOT NULL DEFAULT '0.000',
  `quantity_accepted` decimal(12,3) NOT NULL DEFAULT '0.000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_received_lines_goods_received_note_id_foreign` (`goods_received_note_id`),
  KEY `goods_received_lines_purchase_order_line_id_foreign` (`purchase_order_line_id`),
  CONSTRAINT `goods_received_lines_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_received_lines_purchase_order_line_id_foreign` FOREIGN KEY (`purchase_order_line_id`) REFERENCES `purchase_order_lines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_lines`
--

LOCK TABLES `goods_received_lines` WRITE;
/*!40000 ALTER TABLE `goods_received_lines` DISABLE KEYS */;
INSERT INTO `goods_received_lines` VALUES (1,1,1,1.000,0.000,0.000,1.000,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `goods_received_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_received_notes`
--

DROP TABLE IF EXISTS `goods_received_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_received_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_order_id` bigint unsigned NOT NULL,
  `delivery_id` bigint unsigned DEFAULT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `received_at` date NOT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_received_notes_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `goods_received_notes_organization_id_foreign` (`organization_id`),
  KEY `goods_received_notes_branch_id_foreign` (`branch_id`),
  KEY `goods_received_notes_received_by_foreign` (`received_by`),
  KEY `goods_received_notes_delivery_id_foreign` (`delivery_id`),
  CONSTRAINT `goods_received_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_received_notes_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_received_notes_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_received_notes_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_notes`
--

LOCK TABLES `goods_received_notes` WRITE;
/*!40000 ALTER TABLE `goods_received_notes` DISABLE KEYS */;
INSERT INTO `goods_received_notes` VALUES (1,'GRN-HK-0001',1,1,1,1,'2026-08-06',2,NULL,'completed','2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `goods_received_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_returns`
--

DROP TABLE IF EXISTS `goods_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `goods_received_note_id` bigint unsigned DEFAULT NULL,
  `inventory_item_id` bigint unsigned DEFAULT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `reason` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_returns_organization_id_foreign` (`organization_id`),
  KEY `goods_returns_branch_id_foreign` (`branch_id`),
  KEY `goods_returns_supplier_id_foreign` (`supplier_id`),
  KEY `goods_returns_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `goods_returns_goods_received_note_id_foreign` (`goods_received_note_id`),
  KEY `goods_returns_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `goods_returns_created_by_foreign` (`created_by`),
  CONSTRAINT `goods_returns_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_returns_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_returns_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_returns_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_returns_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_returns`
--

LOCK TABLES `goods_returns` WRITE;
/*!40000 ALTER TABLE `goods_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `goods_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hr_contracts`
--

DROP TABLE IF EXISTS `hr_contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `contract_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `hourly_rate` decimal(8,2) DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_contracts_user_id_foreign` (`user_id`),
  KEY `hr_contracts_organization_id_foreign` (`organization_id`),
  CONSTRAINT `hr_contracts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hr_contracts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_contracts`
--

LOCK TABLES `hr_contracts` WRITE;
/*!40000 ALTER TABLE `hr_contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hr_emergency_contacts`
--

DROP TABLE IF EXISTS `hr_emergency_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_emergency_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_emergency_contacts_user_id_foreign` (`user_id`),
  KEY `hr_emergency_contacts_organization_id_foreign` (`organization_id`),
  CONSTRAINT `hr_emergency_contacts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hr_emergency_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_emergency_contacts`
--

LOCK TABLES `hr_emergency_contacts` WRITE;
/*!40000 ALTER TABLE `hr_emergency_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_emergency_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hr_employee_documents`
--

DROP TABLE IF EXISTS `hr_employee_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_employee_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` date DEFAULT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_employee_documents_user_id_foreign` (`user_id`),
  KEY `hr_employee_documents_organization_id_foreign` (`organization_id`),
  KEY `hr_employee_documents_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `hr_employee_documents_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hr_employee_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hr_employee_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_employee_documents`
--

LOCK TABLES `hr_employee_documents` WRITE;
/*!40000 ALTER TABLE `hr_employee_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_employee_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hr_training_records`
--

DROP TABLE IF EXISTS `hr_training_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_training_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_training_records_user_id_foreign` (`user_id`),
  KEY `hr_training_records_organization_id_foreign` (`organization_id`),
  CONSTRAINT `hr_training_records_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hr_training_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_training_records`
--

LOCK TABLES `hr_training_records` WRITE;
/*!40000 ALTER TABLE `hr_training_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_training_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hr_warnings`
--

DROP TABLE IF EXISTS `hr_warnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hr_warnings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `level` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_at` date NOT NULL,
  `issued_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_warnings_user_id_foreign` (`user_id`),
  KEY `hr_warnings_organization_id_foreign` (`organization_id`),
  KEY `hr_warnings_issued_by_foreign` (`issued_by`),
  CONSTRAINT `hr_warnings_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hr_warnings_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hr_warnings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hr_warnings`
--

LOCK TABLES `hr_warnings` WRITE;
/*!40000 ALTER TABLE `hr_warnings` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_warnings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_categories`
--

DROP TABLE IF EXISTS `inventory_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_categories_organization_id_foreign` (`organization_id`),
  KEY `inventory_categories_branch_id_foreign` (`branch_id`),
  CONSTRAINT `inventory_categories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_categories_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_categories`
--

LOCK TABLES `inventory_categories` WRITE;
/*!40000 ALTER TABLE `inventory_categories` DISABLE KEYS */;
INSERT INTO `inventory_categories` VALUES (1,1,1,'Meat','Meat supplies','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(2,1,1,'Vegetables','Vegetables supplies','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(3,1,1,'Frozen','Frozen supplies','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(4,1,1,'Dry Goods','Dry Goods supplies','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(5,1,1,'Drinks','Drinks supplies','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(6,1,1,'Packaging','Packaging supplies','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(7,1,1,'Cleaning','Cleaning supplies','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL);
/*!40000 ALTER TABLE `inventory_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_counts`
--

DROP TABLE IF EXISTS `inventory_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_counts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `diff_pcs` int NOT NULL DEFAULT '0',
  `new_pcs` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_counts_organization_id_foreign` (`organization_id`),
  KEY `inventory_counts_branch_id_foreign` (`branch_id`),
  KEY `inventory_counts_item_id_foreign` (`item_id`),
  KEY `inventory_counts_created_by_foreign` (`created_by`),
  CONSTRAINT `inventory_counts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_counts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_counts_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_counts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_counts`
--

LOCK TABLES `inventory_counts` WRITE;
/*!40000 ALTER TABLE `inventory_counts` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_counts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_items`
--

DROP TABLE IF EXISTS `inventory_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT NULL,
  `selling_price` decimal(12,2) DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `batch_number` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `packaging` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `pcs_per_box` int unsigned NOT NULL DEFAULT '1',
  `stock_total_pcs` int NOT NULL DEFAULT '0',
  `stock_limit` int NOT NULL DEFAULT '0',
  `par_level` int unsigned NOT NULL DEFAULT '0',
  `min_level` int unsigned NOT NULL DEFAULT '0',
  `max_level` int unsigned NOT NULL DEFAULT '0',
  `order_multiple` int unsigned NOT NULL DEFAULT '1',
  `pack_size` int unsigned NOT NULL DEFAULT '1',
  `lead_time_days` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_items_branch_id_foreign` (`branch_id`),
  KEY `inventory_items_category_id_foreign` (`category_id`),
  KEY `inventory_items_organization_id_branch_id_index` (`organization_id`,`branch_id`),
  KEY `inventory_items_supplier_id_foreign` (`supplier_id`),
  KEY `inventory_items_organization_id_sku_index` (`organization_id`,`sku`),
  KEY `inventory_items_organization_id_barcode_index` (`organization_id`,`barcode`),
  CONSTRAINT `inventory_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `inventory_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_items_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_items`
--

LOCK TABLES `inventory_items` WRITE;
/*!40000 ALTER TABLE `inventory_items` DISABLE KEYS */;
INSERT INTO `inventory_items` VALUES (1,1,1,1,'Chicken Breast','CHICKEN-1',NULL,NULL,8.50,21.25,NULL,NULL,NULL,'pcs','pcs',1,45,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(2,1,1,1,'Beef Mince','BEEF-MIN-1',NULL,NULL,9.20,23.00,NULL,NULL,NULL,'pcs','pcs',1,30,15,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(3,1,1,3,'Salmon Fillet','SALMON-F-1',NULL,NULL,12.00,30.00,NULL,NULL,NULL,'pcs','pcs',1,18,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(4,1,1,2,'Potatoes','POTATOES-1',NULL,NULL,1.20,3.00,NULL,NULL,NULL,'pcs','pcs',1,80,25,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(5,1,1,2,'Onions','ONIONS-1',NULL,NULL,0.80,2.00,NULL,NULL,NULL,'pcs','pcs',1,60,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(6,1,1,2,'Tomatoes','TOMATOES-1',NULL,NULL,1.50,3.75,NULL,NULL,NULL,'pcs','pcs',1,40,15,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(7,1,1,3,'Frozen Chips','FROZEN-C-1',NULL,NULL,3.50,8.75,NULL,NULL,NULL,'pcs','pcs',1,50,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(8,1,1,3,'Pizza Dough','PIZZA-DO-1',NULL,NULL,2.80,7.00,NULL,NULL,NULL,'pcs','pcs',1,25,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(9,1,1,4,'Flour 16kg','FLOUR-16-1',NULL,NULL,14.00,35.00,NULL,NULL,NULL,'pcs','pcs',1,12,4,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(10,1,1,4,'Olive Oil 5L','OLIVE-OI-1',NULL,NULL,22.00,55.00,NULL,NULL,NULL,'pcs','pcs',1,8,3,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(11,1,1,5,'Coca-Cola 330ml','COCA-COL-1',NULL,NULL,0.65,1.63,NULL,NULL,NULL,'pcs','pcs',1,120,48,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(12,1,1,5,'Sparkling Water','SPARKLIN-1',NULL,NULL,0.45,1.13,NULL,NULL,NULL,'pcs','pcs',1,96,36,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(13,1,1,6,'Paper Cups 12oz','PAPER-CU-1',NULL,NULL,0.12,0.30,NULL,NULL,NULL,'pcs','pcs',1,180,40,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(14,1,1,6,'Takeaway Boxes','TAKEAWAY-1',NULL,NULL,0.18,0.45,NULL,NULL,NULL,'pcs','pcs',1,200,50,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(15,1,1,7,'Floor Cleaner','FLOOR-CL-1',NULL,NULL,8.50,21.25,NULL,NULL,NULL,'pcs','pcs',1,6,2,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(16,1,1,1,'Rice 20kg','RICE-20K-1',NULL,NULL,6.64,16.60,NULL,NULL,NULL,'pcs','pcs',1,67,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(17,1,1,2,'Pasta Penne','PASTA-PE-1',NULL,NULL,3.33,8.33,NULL,NULL,NULL,'pcs','pcs',1,10,13,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(18,1,1,3,'Mozzarella','MOZZAREL-1',NULL,NULL,5.87,14.68,NULL,NULL,NULL,'pcs','pcs',1,77,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(19,1,1,4,'Cheddar Block','CHEDDAR-1',NULL,NULL,12.71,31.78,NULL,NULL,NULL,'pcs','pcs',1,63,6,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(20,1,1,5,'Butter','BUTTER-1',NULL,NULL,3.60,9.00,NULL,NULL,NULL,'pcs','pcs',1,78,13,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(21,1,1,6,'Eggs Tray','EGGS-TRA-1',NULL,NULL,10.82,27.05,NULL,NULL,NULL,'pcs','pcs',1,56,11,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(22,1,1,7,'Lettuce','LETTUCE-1',NULL,NULL,1.67,4.18,NULL,NULL,NULL,'pcs','pcs',1,69,19,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(23,1,1,1,'Cucumber','CUCUMBER-1',NULL,NULL,4.54,11.35,NULL,NULL,NULL,'pcs','pcs',1,15,14,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(24,1,1,2,'Peppers','PEPPERS-1',NULL,NULL,2.06,5.15,NULL,NULL,NULL,'pcs','pcs',1,44,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(25,1,1,3,'Mushrooms','MUSHROOM-1',NULL,NULL,3.65,9.13,NULL,NULL,NULL,'pcs','pcs',1,15,18,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(26,1,1,4,'Garlic','GARLIC-1',NULL,NULL,2.72,6.80,NULL,NULL,NULL,'pcs','pcs',1,20,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(27,1,1,5,'Ginger','GINGER-1',NULL,NULL,6.73,16.83,NULL,NULL,NULL,'pcs','pcs',1,52,6,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(28,1,1,6,'Coconut Milk','COCONUT-1',NULL,NULL,4.31,10.77,NULL,NULL,NULL,'pcs','pcs',1,15,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(29,1,1,7,'Soy Sauce','SOY-SAUC-1',NULL,NULL,12.47,31.18,NULL,NULL,NULL,'pcs','pcs',1,12,19,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(30,1,1,1,'Ketchup','KETCHUP-1',NULL,NULL,3.58,8.95,NULL,NULL,NULL,'pcs','pcs',1,17,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(31,1,1,2,'Mayonnaise','MAYONNAI-1',NULL,NULL,6.90,17.25,NULL,NULL,NULL,'pcs','pcs',1,54,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(32,1,1,3,'Napkins','NAPKINS-1',NULL,NULL,12.43,31.08,NULL,NULL,NULL,'pcs','pcs',1,19,5,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(33,1,1,4,'Gloves','GLOVES-1',NULL,NULL,11.30,28.25,NULL,NULL,NULL,'pcs','pcs',1,35,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(34,1,1,5,'Sanitiser','SANITISE-1',NULL,NULL,4.55,11.38,NULL,NULL,NULL,'pcs','pcs',1,67,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(35,1,1,6,'Bin Bags','BIN-BAGS-1',NULL,NULL,7.34,18.35,NULL,NULL,NULL,'pcs','pcs',1,44,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(36,1,1,7,'Sprite','SPRITE-1',NULL,NULL,6.88,17.20,NULL,NULL,NULL,'pcs','pcs',1,77,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(37,1,1,1,'Orange Juice','ORANGE-J-1',NULL,NULL,11.97,29.93,NULL,NULL,NULL,'pcs','pcs',1,14,8,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(38,1,1,2,'Lamb Chops','LAMB-CHO-1',NULL,NULL,10.65,26.63,NULL,NULL,NULL,'pcs','pcs',1,31,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(39,1,1,3,'Prawns','PRAWNS-1',NULL,NULL,2.48,6.20,NULL,NULL,NULL,'pcs','pcs',1,57,8,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(40,1,1,4,'Tofu','TOFU-1',NULL,NULL,4.23,10.58,NULL,NULL,NULL,'pcs','pcs',1,62,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(41,1,2,1,'Chicken Breast','CHICKEN-2',NULL,NULL,8.50,21.25,NULL,NULL,NULL,'pcs','pcs',1,45,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(42,1,2,1,'Beef Mince','BEEF-MIN-2',NULL,NULL,9.20,23.00,NULL,NULL,NULL,'pcs','pcs',1,30,15,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(43,1,2,3,'Salmon Fillet','SALMON-F-2',NULL,NULL,12.00,30.00,NULL,NULL,NULL,'pcs','pcs',1,18,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(44,1,2,2,'Potatoes','POTATOES-2',NULL,NULL,1.20,3.00,NULL,NULL,NULL,'pcs','pcs',1,80,25,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(45,1,2,2,'Onions','ONIONS-2',NULL,NULL,0.80,2.00,NULL,NULL,NULL,'pcs','pcs',1,60,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(46,1,2,2,'Tomatoes','TOMATOES-2',NULL,NULL,1.50,3.75,NULL,NULL,NULL,'pcs','pcs',1,40,15,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(47,1,2,3,'Frozen Chips','FROZEN-C-2',NULL,NULL,3.50,8.75,NULL,NULL,NULL,'pcs','pcs',1,50,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(48,1,2,3,'Pizza Dough','PIZZA-DO-2',NULL,NULL,2.80,7.00,NULL,NULL,NULL,'pcs','pcs',1,25,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(49,1,2,4,'Flour 16kg','FLOUR-16-2',NULL,NULL,14.00,35.00,NULL,NULL,NULL,'pcs','pcs',1,12,4,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(50,1,2,4,'Olive Oil 5L','OLIVE-OI-2',NULL,NULL,22.00,55.00,NULL,NULL,NULL,'pcs','pcs',1,8,3,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(51,1,2,5,'Coca-Cola 330ml','COCA-COL-2',NULL,NULL,0.65,1.63,NULL,NULL,NULL,'pcs','pcs',1,120,48,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(52,1,2,5,'Sparkling Water','SPARKLIN-2',NULL,NULL,0.45,1.13,NULL,NULL,NULL,'pcs','pcs',1,96,36,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(53,1,2,6,'Paper Cups 12oz','PAPER-CU-2',NULL,NULL,0.12,0.30,NULL,NULL,NULL,'pcs','pcs',1,180,40,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(54,1,2,6,'Takeaway Boxes','TAKEAWAY-2',NULL,NULL,0.18,0.45,NULL,NULL,NULL,'pcs','pcs',1,200,50,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(55,1,2,7,'Floor Cleaner','FLOOR-CL-2',NULL,NULL,8.50,21.25,NULL,NULL,NULL,'pcs','pcs',1,6,2,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(56,1,2,1,'Rice 20kg','RICE-20K-2',NULL,NULL,6.64,16.60,NULL,NULL,NULL,'pcs','pcs',1,67,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(57,1,2,2,'Pasta Penne','PASTA-PE-2',NULL,NULL,3.33,8.33,NULL,NULL,NULL,'pcs','pcs',1,10,13,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(58,1,2,3,'Mozzarella','MOZZAREL-2',NULL,NULL,5.87,14.68,NULL,NULL,NULL,'pcs','pcs',1,77,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(59,1,2,4,'Cheddar Block','CHEDDAR-2',NULL,NULL,12.71,31.78,NULL,NULL,NULL,'pcs','pcs',1,63,6,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(60,1,2,5,'Butter','BUTTER-2',NULL,NULL,3.60,9.00,NULL,NULL,NULL,'pcs','pcs',1,78,13,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(61,1,2,6,'Eggs Tray','EGGS-TRA-2',NULL,NULL,10.82,27.05,NULL,NULL,NULL,'pcs','pcs',1,56,11,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(62,1,2,7,'Lettuce','LETTUCE-2',NULL,NULL,1.67,4.18,NULL,NULL,NULL,'pcs','pcs',1,69,19,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(63,1,2,1,'Cucumber','CUCUMBER-2',NULL,NULL,4.54,11.35,NULL,NULL,NULL,'pcs','pcs',1,15,14,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(64,1,2,2,'Peppers','PEPPERS-2',NULL,NULL,2.06,5.15,NULL,NULL,NULL,'pcs','pcs',1,44,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(65,1,2,3,'Mushrooms','MUSHROOM-2',NULL,NULL,3.65,9.13,NULL,NULL,NULL,'pcs','pcs',1,15,18,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(66,1,2,4,'Garlic','GARLIC-2',NULL,NULL,2.72,6.80,NULL,NULL,NULL,'pcs','pcs',1,20,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(67,1,2,5,'Ginger','GINGER-2',NULL,NULL,6.73,16.83,NULL,NULL,NULL,'pcs','pcs',1,52,6,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(68,1,2,6,'Coconut Milk','COCONUT-2',NULL,NULL,4.31,10.77,NULL,NULL,NULL,'pcs','pcs',1,15,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(69,1,2,7,'Soy Sauce','SOY-SAUC-2',NULL,NULL,12.47,31.18,NULL,NULL,NULL,'pcs','pcs',1,12,19,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(70,1,2,1,'Ketchup','KETCHUP-2',NULL,NULL,3.58,8.95,NULL,NULL,NULL,'pcs','pcs',1,17,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(71,1,2,2,'Mayonnaise','MAYONNAI-2',NULL,NULL,6.90,17.25,NULL,NULL,NULL,'pcs','pcs',1,54,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(72,1,2,3,'Napkins','NAPKINS-2',NULL,NULL,12.43,31.08,NULL,NULL,NULL,'pcs','pcs',1,19,5,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(73,1,2,4,'Gloves','GLOVES-2',NULL,NULL,11.30,28.25,NULL,NULL,NULL,'pcs','pcs',1,35,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(74,1,2,5,'Sanitiser','SANITISE-2',NULL,NULL,4.55,11.38,NULL,NULL,NULL,'pcs','pcs',1,67,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(75,1,2,6,'Bin Bags','BIN-BAGS-2',NULL,NULL,7.34,18.35,NULL,NULL,NULL,'pcs','pcs',1,44,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(76,1,2,7,'Sprite','SPRITE-2',NULL,NULL,6.88,17.20,NULL,NULL,NULL,'pcs','pcs',1,77,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(77,1,2,1,'Orange Juice','ORANGE-J-2',NULL,NULL,11.97,29.93,NULL,NULL,NULL,'pcs','pcs',1,14,8,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(78,1,2,2,'Lamb Chops','LAMB-CHO-2',NULL,NULL,10.65,26.63,NULL,NULL,NULL,'pcs','pcs',1,31,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(79,1,2,3,'Prawns','PRAWNS-2',NULL,NULL,2.48,6.20,NULL,NULL,NULL,'pcs','pcs',1,57,8,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(80,1,2,4,'Tofu','TOFU-2',NULL,NULL,4.23,10.58,NULL,NULL,NULL,'pcs','pcs',1,62,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(81,1,3,1,'Chicken Breast','CHICKEN-3',NULL,NULL,8.50,21.25,NULL,NULL,NULL,'pcs','pcs',1,45,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(82,1,3,1,'Beef Mince','BEEF-MIN-3',NULL,NULL,9.20,23.00,NULL,NULL,NULL,'pcs','pcs',1,30,15,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(83,1,3,3,'Salmon Fillet','SALMON-F-3',NULL,NULL,12.00,30.00,NULL,NULL,NULL,'pcs','pcs',1,18,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(84,1,3,2,'Potatoes','POTATOES-3',NULL,NULL,1.20,3.00,NULL,NULL,NULL,'pcs','pcs',1,80,25,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(85,1,3,2,'Onions','ONIONS-3',NULL,NULL,0.80,2.00,NULL,NULL,NULL,'pcs','pcs',1,60,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(86,1,3,2,'Tomatoes','TOMATOES-3',NULL,NULL,1.50,3.75,NULL,NULL,NULL,'pcs','pcs',1,40,15,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(87,1,3,3,'Frozen Chips','FROZEN-C-3',NULL,NULL,3.50,8.75,NULL,NULL,NULL,'pcs','pcs',1,50,20,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(88,1,3,3,'Pizza Dough','PIZZA-DO-3',NULL,NULL,2.80,7.00,NULL,NULL,NULL,'pcs','pcs',1,25,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(89,1,3,4,'Flour 16kg','FLOUR-16-3',NULL,NULL,14.00,35.00,NULL,NULL,NULL,'pcs','pcs',1,12,4,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(90,1,3,4,'Olive Oil 5L','OLIVE-OI-3',NULL,NULL,22.00,55.00,NULL,NULL,NULL,'pcs','pcs',1,8,3,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(91,1,3,5,'Coca-Cola 330ml','COCA-COL-3',NULL,NULL,0.65,1.63,NULL,NULL,NULL,'pcs','pcs',1,120,48,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(92,1,3,5,'Sparkling Water','SPARKLIN-3',NULL,NULL,0.45,1.13,NULL,NULL,NULL,'pcs','pcs',1,96,36,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(93,1,3,6,'Paper Cups 12oz','PAPER-CU-3',NULL,NULL,0.12,0.30,NULL,NULL,NULL,'pcs','pcs',1,180,40,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(94,1,3,6,'Takeaway Boxes','TAKEAWAY-3',NULL,NULL,0.18,0.45,NULL,NULL,NULL,'pcs','pcs',1,200,50,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(95,1,3,7,'Floor Cleaner','FLOOR-CL-3',NULL,NULL,8.50,21.25,NULL,NULL,NULL,'pcs','pcs',1,6,2,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(96,1,3,1,'Rice 20kg','RICE-20K-3',NULL,NULL,6.64,16.60,NULL,NULL,NULL,'pcs','pcs',1,67,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(97,1,3,2,'Pasta Penne','PASTA-PE-3',NULL,NULL,3.33,8.33,NULL,NULL,NULL,'pcs','pcs',1,10,13,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(98,1,3,3,'Mozzarella','MOZZAREL-3',NULL,NULL,5.87,14.68,NULL,NULL,NULL,'pcs','pcs',1,77,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(99,1,3,4,'Cheddar Block','CHEDDAR-3',NULL,NULL,12.71,31.78,NULL,NULL,NULL,'pcs','pcs',1,63,6,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(100,1,3,5,'Butter','BUTTER-3',NULL,NULL,3.60,9.00,NULL,NULL,NULL,'pcs','pcs',1,78,13,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(101,1,3,6,'Eggs Tray','EGGS-TRA-3',NULL,NULL,10.82,27.05,NULL,NULL,NULL,'pcs','pcs',1,56,11,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(102,1,3,7,'Lettuce','LETTUCE-3',NULL,NULL,1.67,4.18,NULL,NULL,NULL,'pcs','pcs',1,69,19,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(103,1,3,1,'Cucumber','CUCUMBER-3',NULL,NULL,4.54,11.35,NULL,NULL,NULL,'pcs','pcs',1,15,14,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(104,1,3,2,'Peppers','PEPPERS-3',NULL,NULL,2.06,5.15,NULL,NULL,NULL,'pcs','pcs',1,44,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(105,1,3,3,'Mushrooms','MUSHROOM-3',NULL,NULL,3.65,9.13,NULL,NULL,NULL,'pcs','pcs',1,15,18,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(106,1,3,4,'Garlic','GARLIC-3',NULL,NULL,2.72,6.80,NULL,NULL,NULL,'pcs','pcs',1,20,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(107,1,3,5,'Ginger','GINGER-3',NULL,NULL,6.73,16.83,NULL,NULL,NULL,'pcs','pcs',1,52,6,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(108,1,3,6,'Coconut Milk','COCONUT-3',NULL,NULL,4.31,10.77,NULL,NULL,NULL,'pcs','pcs',1,15,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(109,1,3,7,'Soy Sauce','SOY-SAUC-3',NULL,NULL,12.47,31.18,NULL,NULL,NULL,'pcs','pcs',1,12,19,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(110,1,3,1,'Ketchup','KETCHUP-3',NULL,NULL,3.58,8.95,NULL,NULL,NULL,'pcs','pcs',1,17,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(111,1,3,2,'Mayonnaise','MAYONNAI-3',NULL,NULL,6.90,17.25,NULL,NULL,NULL,'pcs','pcs',1,54,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(112,1,3,3,'Napkins','NAPKINS-3',NULL,NULL,12.43,31.08,NULL,NULL,NULL,'pcs','pcs',1,19,5,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(113,1,3,4,'Gloves','GLOVES-3',NULL,NULL,11.30,28.25,NULL,NULL,NULL,'pcs','pcs',1,35,10,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(114,1,3,5,'Sanitiser','SANITISE-3',NULL,NULL,4.55,11.38,NULL,NULL,NULL,'pcs','pcs',1,67,16,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(115,1,3,6,'Bin Bags','BIN-BAGS-3',NULL,NULL,7.34,18.35,NULL,NULL,NULL,'pcs','pcs',1,44,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(116,1,3,7,'Sprite','SPRITE-3',NULL,NULL,6.88,17.20,NULL,NULL,NULL,'pcs','pcs',1,77,17,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(117,1,3,1,'Orange Juice','ORANGE-J-3',NULL,NULL,11.97,29.93,NULL,NULL,NULL,'pcs','pcs',1,14,8,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(118,1,3,2,'Lamb Chops','LAMB-CHO-3',NULL,NULL,10.65,26.63,NULL,NULL,NULL,'pcs','pcs',1,31,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(119,1,3,3,'Prawns','PRAWNS-3',NULL,NULL,2.48,6.20,NULL,NULL,NULL,'pcs','pcs',1,57,8,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(120,1,3,4,'Tofu','TOFU-3',NULL,NULL,4.23,10.58,NULL,NULL,NULL,'pcs','pcs',1,62,7,0,0,0,1,1,0,'2026-08-08 14:20:02','2026-08-08 14:20:02',NULL);
/*!40000 ALTER TABLE `inventory_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_settings`
--

DROP TABLE IF EXISTS `inventory_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `stocktake_weekday` tinyint unsigned NOT NULL DEFAULT '0',
  `stocktake_time` time NOT NULL DEFAULT '18:00:00',
  `stocktake_reminders` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_settings_organization_id_unique` (`organization_id`),
  CONSTRAINT `inventory_settings_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_settings`
--

LOCK TABLES `inventory_settings` WRITE;
/*!40000 ALTER TABLE `inventory_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stocktake_items`
--

DROP TABLE IF EXISTS `inventory_stocktake_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_stocktake_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventory_stocktake_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `system_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `counted_qty` decimal(12,3) DEFAULT NULL,
  `difference_qty` decimal(12,3) DEFAULT NULL,
  `par_level` decimal(12,3) NOT NULL DEFAULT '0.000',
  `on_order_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `suggested_order_qty` decimal(12,3) NOT NULL DEFAULT '0.000',
  `ordered_qty` decimal(12,3) DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `excluded_from_order` tinyint(1) NOT NULL DEFAULT '0',
  `override_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stocktake_item_unique` (`inventory_stocktake_id`,`inventory_item_id`),
  KEY `inventory_stocktake_items_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `inventory_stocktake_items_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `inventory_stocktake_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stocktake_items_inventory_stocktake_id_foreign` FOREIGN KEY (`inventory_stocktake_id`) REFERENCES `inventory_stocktakes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stocktake_items_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stocktake_items`
--

LOCK TABLES `inventory_stocktake_items` WRITE;
/*!40000 ALTER TABLE `inventory_stocktake_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stocktake_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_stocktakes`
--

DROP TABLE IF EXISTS `inventory_stocktakes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_stocktakes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `week_start` date NOT NULL,
  `week_end` date NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned NOT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `client_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idempotency_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_stocktakes_branch_id_foreign` (`branch_id`),
  KEY `inventory_stocktakes_created_by_foreign` (`created_by`),
  KEY `inventory_stocktakes_reviewed_by_foreign` (`reviewed_by`),
  KEY `inventory_stocktakes_approved_by_foreign` (`approved_by`),
  KEY `inventory_stocktakes_organization_id_branch_id_week_start_index` (`organization_id`,`branch_id`,`week_start`),
  KEY `inventory_stocktakes_status_branch_id_index` (`status`,`branch_id`),
  CONSTRAINT `inventory_stocktakes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_stocktakes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stocktakes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stocktakes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_stocktakes_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_stocktakes`
--

LOCK TABLES `inventory_stocktakes` WRITE;
/*!40000 ALTER TABLE `inventory_stocktakes` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_stocktakes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_transactions`
--

DROP TABLE IF EXISTS `inventory_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `quantity_before` decimal(12,3) NOT NULL,
  `quantity_after` decimal(12,3) NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_transactions_organization_id_foreign` (`organization_id`),
  KEY `inventory_transactions_branch_id_foreign` (`branch_id`),
  KEY `inventory_transactions_created_by_foreign` (`created_by`),
  KEY `inventory_transactions_inventory_item_id_created_at_index` (`inventory_item_id`,`created_at`),
  KEY `inventory_transactions_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `inventory_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_transactions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_transactions`
--

LOCK TABLES `inventory_transactions` WRITE;
/*!40000 ALTER TABLE `inventory_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_waste`
--

DROP TABLE IF EXISTS `inventory_waste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_waste` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `quantity_pcs` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_impact` decimal(12,2) DEFAULT NULL,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_waste_organization_id_foreign` (`organization_id`),
  KEY `inventory_waste_branch_id_foreign` (`branch_id`),
  KEY `inventory_waste_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `inventory_waste_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `inventory_waste_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_waste_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_waste_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_waste_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_waste`
--

LOCK TABLES `inventory_waste` WRITE;
/*!40000 ALTER TABLE `inventory_waste` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_waste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_matches`
--

DROP TABLE IF EXISTS `invoice_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_matches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `purchase_order_id` bigint unsigned NOT NULL,
  `goods_received_note_id` bigint unsigned DEFAULT NULL,
  `supplier_invoice_id` bigint unsigned NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `po_quantity` decimal(12,3) NOT NULL DEFAULT '0.000',
  `grn_quantity` decimal(12,3) NOT NULL DEFAULT '0.000',
  `invoice_quantity` decimal(12,3) NOT NULL DEFAULT '0.000',
  `po_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grn_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `invoice_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_variance` decimal(12,3) NOT NULL DEFAULT '0.000',
  `price_variance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_matches_branch_id_foreign` (`branch_id`),
  KEY `invoice_matches_supplier_id_foreign` (`supplier_id`),
  KEY `invoice_matches_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `invoice_matches_goods_received_note_id_foreign` (`goods_received_note_id`),
  KEY `invoice_matches_reviewed_by_foreign` (`reviewed_by`),
  KEY `invoice_matches_organization_id_status_index` (`organization_id`,`status`),
  KEY `invoice_matches_supplier_invoice_id_index` (`supplier_invoice_id`),
  CONSTRAINT `invoice_matches_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_matches_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_matches_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_matches_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_matches_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_matches_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_matches_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_matches`
--

LOCK TABLES `invoice_matches` WRITE;
/*!40000 ALTER TABLE `invoice_matches` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `subscription_id` bigint unsigned DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `due_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_number_unique` (`number`),
  KEY `invoices_organization_id_foreign` (`organization_id`),
  KEY `invoices_subscription_id_foreign` (`subscription_id`),
  KEY `invoices_status_index` (`status`),
  CONSTRAINT `invoices_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `kiosk_activity_logs`
--

DROP TABLE IF EXISTS `kiosk_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kiosk_activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_kiosk_id` bigint unsigned DEFAULT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `event` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_user_id` bigint unsigned DEFAULT NULL,
  `actor_user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `device_summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kiosk_activity_logs_branch_id_foreign` (`branch_id`),
  KEY `kiosk_activity_logs_staff_user_id_foreign` (`staff_user_id`),
  KEY `kiosk_activity_logs_actor_user_id_foreign` (`actor_user_id`),
  KEY `kiosk_activity_logs_branch_kiosk_id_created_at_index` (`branch_kiosk_id`,`created_at`),
  KEY `kiosk_activity_logs_organization_id_event_index` (`organization_id`,`event`),
  CONSTRAINT `kiosk_activity_logs_actor_user_id_foreign` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kiosk_activity_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_activity_logs_branch_kiosk_id_foreign` FOREIGN KEY (`branch_kiosk_id`) REFERENCES `branch_kiosks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_activity_logs_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_activity_logs_staff_user_id_foreign` FOREIGN KEY (`staff_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_activity_logs`
--

LOCK TABLES `kiosk_activity_logs` WRITE;
/*!40000 ALTER TABLE `kiosk_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `kiosk_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kiosk_break_types`
--

DROP TABLE IF EXISTS `kiosk_break_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kiosk_break_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `max_duration_minutes` smallint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kiosk_break_types_organization_id_slug_unique` (`organization_id`,`slug`),
  CONSTRAINT `kiosk_break_types_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_break_types`
--

LOCK TABLES `kiosk_break_types` WRITE;
/*!40000 ALTER TABLE `kiosk_break_types` DISABLE KEYS */;
INSERT INTO `kiosk_break_types` VALUES (1,1,'Lunch','lunch',NULL,0,60,1,1,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(2,1,'Jumma','jumma',NULL,1,30,1,2,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(3,1,'Namaz','namaz',NULL,1,30,1,3,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(4,1,'Tea Break','tea-break',NULL,1,30,1,4,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `kiosk_break_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kiosk_sessions`
--

DROP TABLE IF EXISTS `kiosk_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kiosk_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `branch_kiosk_id` bigint unsigned DEFAULT NULL,
  `session_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `started_by_user_id` bigint unsigned NOT NULL,
  `ended_by_user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `device_summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NOT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `revoked_by_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kiosk_sessions_session_token_unique` (`session_token`),
  KEY `kiosk_sessions_started_by_user_id_foreign` (`started_by_user_id`),
  KEY `kiosk_sessions_ended_by_user_id_foreign` (`ended_by_user_id`),
  KEY `kiosk_sessions_branch_kiosk_id_ended_at_index` (`branch_kiosk_id`,`ended_at`),
  KEY `kiosk_sessions_organization_id_foreign` (`organization_id`),
  KEY `kiosk_sessions_branch_id_foreign` (`branch_id`),
  KEY `kiosk_sessions_revoked_by_user_id_foreign` (`revoked_by_user_id`),
  CONSTRAINT `kiosk_sessions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kiosk_sessions_branch_kiosk_id_foreign` FOREIGN KEY (`branch_kiosk_id`) REFERENCES `branch_kiosks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_sessions_ended_by_user_id_foreign` FOREIGN KEY (`ended_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kiosk_sessions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_sessions_revoked_by_user_id_foreign` FOREIGN KEY (`revoked_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kiosk_sessions_started_by_user_id_foreign` FOREIGN KEY (`started_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_sessions`
--

LOCK TABLES `kiosk_sessions` WRITE;
/*!40000 ALTER TABLE `kiosk_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `kiosk_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kiosk_sync_events`
--

DROP TABLE IF EXISTS `kiosk_sync_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kiosk_sync_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `branch_kiosk_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `event_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idempotency_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_sequence` bigint unsigned DEFAULT NULL,
  `event_time` datetime NOT NULL,
  `sync_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'synced',
  `payload` json DEFAULT NULL,
  `result` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kiosk_sync_events_idempotent` (`branch_kiosk_id`,`idempotency_key`),
  KEY `kiosk_sync_events_branch_id_foreign` (`branch_id`),
  KEY `kiosk_sync_events_user_id_foreign` (`user_id`),
  KEY `kiosk_sync_events_organization_id_event_time_index` (`organization_id`,`event_time`),
  CONSTRAINT `kiosk_sync_events_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_sync_events_branch_kiosk_id_foreign` FOREIGN KEY (`branch_kiosk_id`) REFERENCES `branch_kiosks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_sync_events_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kiosk_sync_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_sync_events`
--

LOCK TABLES `kiosk_sync_events` WRITE;
/*!40000 ALTER TABLE `kiosk_sync_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `kiosk_sync_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_user_id_foreign` (`user_id`),
  KEY `leave_requests_branch_id_foreign` (`branch_id`),
  KEY `leave_requests_reviewed_by_foreign` (`reviewed_by`),
  KEY `leave_requests_organization_id_status_index` (`organization_id`,`status`),
  CONSTRAINT `leave_requests_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leave_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_histories`
--

DROP TABLE IF EXISTS `login_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operating_system` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `event_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'login',
  `failure_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logged_in_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_histories_user_id_logged_in_at_index` (`user_id`,`logged_in_at`),
  CONSTRAINT `login_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_histories`
--

LOCK TABLES `login_histories` WRITE;
/*!40000 ALTER TABLE `login_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL DEFAULT '0',
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_uploaded_by_foreign` (`uploaded_by`),
  KEY `media_collection_index` (`collection`),
  KEY `media_folder_index` (`folder`),
  CONSTRAINT `media_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_01_000100_create_access_requests_table',1),(5,'2026_08_01_000200_create_contact_messages_table',1),(6,'2026_08_02_000300_create_roles_and_permissions_tables',1),(7,'2026_08_02_010000_create_organizations_and_branches_tables',1),(8,'2026_08_02_010100_create_billing_tables',1),(9,'2026_08_02_010200_create_support_and_log_tables',1),(10,'2026_08_02_010300_create_cms_media_settings_tables',1),(11,'2026_08_02_020000_enhance_super_admin_module_tables',1),(12,'2026_08_02_030000_create_business_admin_domain_tables',1),(13,'2026_08_02_150000_create_rota_group_user_table',1),(14,'2026_08_03_030000_add_signup_onboarding_fields',1),(15,'2026_08_04_170000_create_accounting_tables',1),(16,'2026_08_04_180000_create_finance_module_tables',1),(17,'2026_08_07_000000_enterprise_completion_tables',1),(18,'2026_08_07_050845_create_personal_access_tokens_table',1),(19,'2026_08_07_100000_milestone3_workflow_tables',1),(20,'2026_08_07_120000_create_smart_kiosk_tables',1),(21,'2026_08_07_140000_allow_multiple_kiosks_per_branch',1),(22,'2026_08_07_200000_milestone4_security_tables',1),(23,'2026_08_07_210000_milestone4_1_notification_preferences',1),(24,'2026_08_07_220000_hash_staff_pins_and_tenancy_hardening',1),(25,'2026_08_08_100000_phase1_kiosk_attendance_enhancements',1),(26,'2026_08_09_100000_phase2_rota_versioning',1),(27,'2026_08_10_100000_phase3_cash_drawer_reconciliation',1),(28,'2026_08_11_100000_phase4_inventory_procurement_rider',1),(29,'2026_08_12_100000_phase5_supplier_procurement_matching',1),(30,'2026_08_14_100000_phase7_executive_intelligence',1),(31,'2026_08_15_100000_kiosk_v2_architecture',1),(32,'2026_08_16_100000_till_management',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_preferences`
--

DROP TABLE IF EXISTS `notification_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `database_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_preferences_user_id_category_unique` (`user_id`,`category`),
  CONSTRAINT `notification_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_preferences`
--

LOCK TABLES `notification_preferences` WRITE;
/*!40000 ALTER TABLE `notification_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `category` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `read_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_read_at_index` (`user_id`,`read_at`),
  KEY `notifications_read_at_index` (`read_at`),
  KEY `notifications_archived_at_index` (`archived_at`),
  KEY `notifications_user_id_category_index` (`user_id`,`category`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_kiosk_settings`
--

DROP TABLE IF EXISTS `organization_kiosk_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_kiosk_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `default_branch_id` bigint unsigned DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_attendance_list` tinyint(1) NOT NULL DEFAULT '1',
  `show_staff_names` tinyint(1) NOT NULL DEFAULT '1',
  `success_delay_seconds` smallint unsigned NOT NULL DEFAULT '3',
  `session_lifetime_minutes` smallint unsigned NOT NULL DEFAULT '480',
  `settings` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organization_kiosk_settings_organization_id_unique` (`organization_id`),
  KEY `organization_kiosk_settings_default_branch_id_foreign` (`default_branch_id`),
  CONSTRAINT `organization_kiosk_settings_default_branch_id_foreign` FOREIGN KEY (`default_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `organization_kiosk_settings_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_kiosk_settings`
--

LOCK TABLES `organization_kiosk_settings` WRITE;
/*!40000 ALTER TABLE `organization_kiosk_settings` DISABLE KEYS */;
INSERT INTO `organization_kiosk_settings` VALUES (1,1,1,'Staff Clock',1,1,3,480,NULL,1,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `organization_kiosk_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GB',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Europe/London',
  `business_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opens_at` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `closes_at` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `owner_user_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `trial_starts_at` timestamp NULL DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organizations_slug_unique` (`slug`),
  KEY `organizations_owner_user_id_foreign` (`owner_user_id`),
  KEY `organizations_email_index` (`email`),
  KEY `organizations_status_index` (`status`),
  KEY `organizations_trial_ends_at_index` (`trial_ends_at`),
  CONSTRAINT `organizations_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES (1,'Harbour Kitchen Group','harbour-kitchen-group','ops@harbourkitchen.test','+44 20 7946 0958','GB','GBP','Europe/London',NULL,NULL,NULL,'08:00','23:00','{\"cash\": {\"default_opening_float\": 100}, \"industry\": \"restaurant_hospitality\"}',2,'active',NULL,NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otp_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `purpose` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_user_id_purpose_index` (`user_id`,`purpose`),
  KEY `otp_codes_expires_at_index` (`expires_at`),
  CONSTRAINT `otp_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_codes`
--

LOCK TABLES `otp_codes` WRITE;
/*!40000 ALTER TABLE `otp_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `provider_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_organization_id_status_index` (`organization_id`,`status`),
  KEY `payments_paid_at_status_index` (`paid_at`,`status`),
  KEY `payments_provider_index` (`provider`),
  KEY `payments_provider_reference_index` (`provider_reference`),
  KEY `payments_status_index` (`status`),
  KEY `payments_paid_at_index` (`paid_at`),
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_role_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `permission_role_permission_id_foreign` (`permission_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,1,10);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'View Dashboard','dashboard.view','Overview','2026-08-08 14:19:49','2026-08-08 14:19:49'),(2,'Manage Businesses','businesses.manage','Customers','2026-08-08 14:19:49','2026-08-08 14:19:49'),(3,'Manage Users','users.manage','Customers','2026-08-08 14:19:49','2026-08-08 14:19:49'),(4,'Manage Plans','plans.manage','Billing','2026-08-08 14:19:49','2026-08-08 14:19:49'),(5,'Manage Subscriptions','subscriptions.manage','Billing','2026-08-08 14:19:49','2026-08-08 14:19:49'),(6,'Manage Coupons','coupons.manage','Billing','2026-08-08 14:19:49','2026-08-08 14:19:49'),(7,'Manage CMS','cms.manage','CMS','2026-08-08 14:19:49','2026-08-08 14:19:49'),(8,'Manage Settings','settings.manage','System','2026-08-08 14:19:49','2026-08-08 14:19:49'),(9,'Manage Roles','roles.manage','System','2026-08-08 14:19:49','2026-08-08 14:19:49'),(10,'View Audit Logs','audit.view','System','2026-08-08 14:19:49','2026-08-08 14:19:49');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `petty_cash_accounts`
--

DROP TABLE IF EXISTS `petty_cash_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `petty_cash_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `float_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `opening_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `custodian_user_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `petty_cash_accounts_organization_id_foreign` (`organization_id`),
  KEY `petty_cash_accounts_branch_id_foreign` (`branch_id`),
  KEY `petty_cash_accounts_custodian_user_id_foreign` (`custodian_user_id`),
  CONSTRAINT `petty_cash_accounts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `petty_cash_accounts_custodian_user_id_foreign` FOREIGN KEY (`custodian_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `petty_cash_accounts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `petty_cash_accounts`
--

LOCK TABLES `petty_cash_accounts` WRITE;
/*!40000 ALTER TABLE `petty_cash_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `petty_cash_transactions`
--

DROP TABLE IF EXISTS `petty_cash_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `petty_cash_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `petty_cash_account_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_date` date NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `petty_cash_transactions_petty_cash_account_id_foreign` (`petty_cash_account_id`),
  KEY `petty_cash_transactions_branch_id_foreign` (`branch_id`),
  KEY `petty_cash_transactions_created_by_foreign` (`created_by`),
  KEY `petty_cash_transactions_organization_id_transaction_date_index` (`organization_id`,`transaction_date`),
  CONSTRAINT `petty_cash_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `petty_cash_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `petty_cash_transactions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `petty_cash_transactions_petty_cash_account_id_foreign` FOREIGN KEY (`petty_cash_account_id`) REFERENCES `petty_cash_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `petty_cash_transactions`
--

LOCK TABLES `petty_cash_transactions` WRITE;
/*!40000 ALTER TABLE `petty_cash_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_monthly` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `billing_interval` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `features` json DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`),
  KEY `plans_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES (1,'Basic','basic','Starter','Cash up, attendance and daily reports for single-location teams.',19.99,'GBP','monthly','{\"bullets\": [\"1 branch\", \"Up to 5 staff\", \"Cash up & attendance\", \"Standard reports\"], \"entitlements\": {\"rota\": false, \"cash_up\": true, \"payroll\": false, \"reports\": true, \"inventory\": false, \"max_staff\": 5, \"suppliers\": false, \"accounting\": false, \"attendance\": true, \"staff_panel\": true, \"max_branches\": 1, \"advanced_reports\": false, \"multiple_branches\": false}}',0,1,1,1,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(2,'Professional','professional','Growth','Everything in Basic plus inventory, payroll, rota and multi-branch.',29.99,'GBP','monthly','{\"bullets\": [\"Unlimited branches\", \"Unlimited staff\", \"Inventory & payroll\", \"Staff rota & analytics\"], \"entitlements\": {\"rota\": true, \"cash_up\": true, \"payroll\": true, \"reports\": true, \"inventory\": true, \"max_staff\": null, \"suppliers\": true, \"accounting\": true, \"attendance\": true, \"staff_panel\": true, \"max_branches\": null, \"advanced_reports\": true, \"multiple_branches\": true}}',1,1,1,2,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL),(3,'Enterprise','enterprise','Scale','Full platform access with dedicated commercial terms.',0.00,'GBP','monthly','{\"bullets\": [\"Everything in Professional\", \"Dedicated success\", \"Custom commercial terms\"], \"entitlements\": {\"rota\": true, \"cash_up\": true, \"payroll\": true, \"reports\": true, \"inventory\": true, \"max_staff\": null, \"suppliers\": true, \"accounting\": true, \"attendance\": true, \"staff_panel\": true, \"max_branches\": null, \"advanced_reports\": true, \"multiple_branches\": true}}',0,1,0,3,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `procurement_settings`
--

DROP TABLE IF EXISTS `procurement_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `procurement_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `quantity_tolerance_percent` decimal(5,2) NOT NULL DEFAULT '2.00',
  `price_tolerance_percent` decimal(5,2) NOT NULL DEFAULT '1.00',
  `auto_create_bill_on_match` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `procurement_settings_organization_id_unique` (`organization_id`),
  CONSTRAINT `procurement_settings_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `procurement_settings`
--

LOCK TABLES `procurement_settings` WRITE;
/*!40000 ALTER TABLE `procurement_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `procurement_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_amendments`
--

DROP TABLE IF EXISTS `purchase_order_amendments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_order_amendments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `amended_by` bigint unsigned NOT NULL,
  `field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_amendments_amended_by_foreign` (`amended_by`),
  KEY `purchase_order_amendments_purchase_order_id_created_at_index` (`purchase_order_id`,`created_at`),
  CONSTRAINT `purchase_order_amendments_amended_by_foreign` FOREIGN KEY (`amended_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_amendments_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_amendments`
--

LOCK TABLES `purchase_order_amendments` WRITE;
/*!40000 ALTER TABLE `purchase_order_amendments` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_amendments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_lines`
--

DROP TABLE IF EXISTS `purchase_order_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_order_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `quantity_received` decimal(12,3) NOT NULL DEFAULT '0.000',
  `unit_cost` decimal(12,2) NOT NULL,
  `snapshot_unit_cost` decimal(12,2) DEFAULT NULL,
  `snapshot_pack_size` int unsigned DEFAULT NULL,
  `snapshot_supplier_sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_rate` decimal(5,2) NOT NULL DEFAULT '20.00',
  `line_total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_lines_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_lines_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `purchase_order_lines_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `purchase_order_lines_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_order_lines_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_lines_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_lines`
--

LOCK TABLES `purchase_order_lines` WRITE;
/*!40000 ALTER TABLE `purchase_order_lines` DISABLE KEYS */;
INSERT INTO `purchase_order_lines` VALUES (1,1,NULL,NULL,'Weekly vegetables',1.000,1.000,248.50,NULL,NULL,NULL,20.00,248.50,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `purchase_order_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `po_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `ordered_at` date DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `sent_by` bigint unsigned DEFAULT NULL,
  `expected_at` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `vat_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `supplier_invoice_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_organization_id_po_number_unique` (`organization_id`,`po_number`),
  KEY `purchase_orders_branch_id_foreign` (`branch_id`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  KEY `purchase_orders_approved_by_foreign` (`approved_by`),
  KEY `purchase_orders_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  KEY `purchase_orders_sent_by_foreign` (`sent_by`),
  CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,1,1,1,'PO-HK-0001','received','2026-08-04',NULL,NULL,'2026-08-06',248.50,49.70,298.20,NULL,'2026-08-03 14:20:02',2,NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurring_bills`
--

DROP TABLE IF EXISTS `recurring_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurring_bills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `frequency` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `next_due_date` date NOT NULL,
  `last_generated_date` date DEFAULT NULL,
  `finance_bank_account_id` bigint unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_bills_branch_id_foreign` (`branch_id`),
  KEY `recurring_bills_finance_bank_account_id_foreign` (`finance_bank_account_id`),
  KEY `recurring_bills_created_by_foreign` (`created_by`),
  KEY `recurring_bills_organization_id_next_due_date_index` (`organization_id`,`next_due_date`),
  CONSTRAINT `recurring_bills_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `recurring_bills_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `recurring_bills_finance_bank_account_id_foreign` FOREIGN KEY (`finance_bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `recurring_bills_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurring_bills`
--

LOCK TABLES `recurring_bills` WRITE;
/*!40000 ALTER TABLE `recurring_bills` DISABLE KEYS */;
/*!40000 ALTER TABLE `recurring_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riders`
--

DROP TABLE IF EXISTS `riders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `riders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `branch_ids` json DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_reference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_registration` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `riders_organization_id_user_id_unique` (`organization_id`,`user_id`),
  KEY `riders_user_id_foreign` (`user_id`),
  CONSTRAINT `riders_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riders`
--

LOCK TABLES `riders` WRITE;
/*!40000 ALTER TABLE `riders` DISABLE KEYS */;
INSERT INTO `riders` VALUES (1,1,18,'active','[1, 2, 3]','+44 7700 888001',NULL,'Van',NULL,NULL,NULL,1,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `riders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_protected` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','super_admin','Full platform access for TotalCashPro operators.',1,'2026-08-08 14:19:49','2026-08-08 14:19:49'),(2,'Admin','admin','Business admin role (Phase 2+).',1,'2026-08-08 14:19:49','2026-08-08 14:19:49'),(3,'Staff','staff','Staff role (Phase 2+).',1,'2026-08-08 14:19:49','2026-08-08 14:19:49'),(4,'Rider','rider','Delivery rider role (Phase 4+).',1,'2026-08-08 14:19:49','2026-08-08 14:19:49');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rota_amendments`
--

DROP TABLE IF EXISTS `rota_amendments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rota_amendments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rota_version_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `rota_shift_id` bigint unsigned DEFAULT NULL,
  `field` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` json DEFAULT NULL,
  `new_value` json DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amended_by_user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rota_amendments_organization_id_foreign` (`organization_id`),
  KEY `rota_amendments_branch_id_foreign` (`branch_id`),
  KEY `rota_amendments_user_id_foreign` (`user_id`),
  KEY `rota_amendments_rota_shift_id_foreign` (`rota_shift_id`),
  KEY `rota_amendments_amended_by_user_id_foreign` (`amended_by_user_id`),
  KEY `rota_amendments_rota_version_id_created_at_index` (`rota_version_id`,`created_at`),
  CONSTRAINT `rota_amendments_amended_by_user_id_foreign` FOREIGN KEY (`amended_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_amendments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_amendments_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_amendments_rota_shift_id_foreign` FOREIGN KEY (`rota_shift_id`) REFERENCES `rota_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rota_amendments_rota_version_id_foreign` FOREIGN KEY (`rota_version_id`) REFERENCES `rota_versions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_amendments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_amendments`
--

LOCK TABLES `rota_amendments` WRITE;
/*!40000 ALTER TABLE `rota_amendments` DISABLE KEYS */;
/*!40000 ALTER TABLE `rota_amendments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rota_group_user`
--

DROP TABLE IF EXISTS `rota_group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rota_group_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rota_group_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rota_group_user_user_id_unique` (`user_id`),
  UNIQUE KEY `rota_group_user_rota_group_id_user_id_unique` (`rota_group_id`,`user_id`),
  CONSTRAINT `rota_group_user_rota_group_id_foreign` FOREIGN KEY (`rota_group_id`) REFERENCES `rota_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_group_user`
--

LOCK TABLES `rota_group_user` WRITE;
/*!40000 ALTER TABLE `rota_group_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `rota_group_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rota_groups`
--

DROP TABLE IF EXISTS `rota_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rota_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#16A34A',
  `display_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rota_groups_organization_id_foreign` (`organization_id`),
  KEY `rota_groups_branch_id_foreign` (`branch_id`),
  CONSTRAINT `rota_groups_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_groups_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_groups`
--

LOCK TABLES `rota_groups` WRITE;
/*!40000 ALTER TABLE `rota_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `rota_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rota_sections`
--

DROP TABLE IF EXISTS `rota_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rota_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0F766E',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rota_sections_organization_id_foreign` (`organization_id`),
  KEY `rota_sections_branch_id_foreign` (`branch_id`),
  CONSTRAINT `rota_sections_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_sections_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_sections`
--

LOCK TABLES `rota_sections` WRITE;
/*!40000 ALTER TABLE `rota_sections` DISABLE KEYS */;
INSERT INTO `rota_sections` VALUES (1,1,1,'Kitchen','#0F766E','2026-08-08 14:20:01','2026-08-08 14:20:01'),(2,1,1,'Front of House','#16A34A','2026-08-08 14:20:01','2026-08-08 14:20:01'),(3,1,1,'Grill','#B45309','2026-08-08 14:20:01','2026-08-08 14:20:01'),(4,1,2,'Kitchen','#0F766E','2026-08-08 14:20:01','2026-08-08 14:20:01'),(5,1,2,'Front of House','#16A34A','2026-08-08 14:20:01','2026-08-08 14:20:01'),(6,1,2,'Grill','#B45309','2026-08-08 14:20:01','2026-08-08 14:20:01'),(7,1,3,'Kitchen','#0F766E','2026-08-08 14:20:01','2026-08-08 14:20:01'),(8,1,3,'Front of House','#16A34A','2026-08-08 14:20:01','2026-08-08 14:20:01'),(9,1,3,'Grill','#B45309','2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `rota_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rota_shifts`
--

DROP TABLE IF EXISTS `rota_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rota_shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rota_version_id` bigint unsigned DEFAULT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `rota_section_id` bigint unsigned NOT NULL,
  `rota_group_id` bigint unsigned DEFAULT NULL,
  `shift_date` date NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `shift_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `break_minutes` smallint unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rota_shifts_branch_id_foreign` (`branch_id`),
  KEY `rota_shifts_rota_section_id_foreign` (`rota_section_id`),
  KEY `rota_shifts_rota_group_id_foreign` (`rota_group_id`),
  KEY `rota_shifts_org_date_idx` (`organization_id`,`shift_date`),
  KEY `rota_shifts_user_date_status_idx` (`user_id`,`shift_date`,`status`),
  KEY `rota_shifts_version_date_idx` (`rota_version_id`,`shift_date`),
  CONSTRAINT `rota_shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_shifts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_shifts_rota_group_id_foreign` FOREIGN KEY (`rota_group_id`) REFERENCES `rota_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rota_shifts_rota_section_id_foreign` FOREIGN KEY (`rota_section_id`) REFERENCES `rota_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_shifts_rota_version_id_foreign` FOREIGN KEY (`rota_version_id`) REFERENCES `rota_versions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_shifts`
--

LOCK TABLES `rota_shifts` WRITE;
/*!40000 ALTER TABLE `rota_shifts` DISABLE KEYS */;
INSERT INTO `rota_shifts` VALUES (1,1,1,1,3,1,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(2,1,1,1,3,1,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(3,1,1,1,3,1,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(4,1,1,1,3,1,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(5,1,1,1,3,1,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(6,1,1,1,3,1,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(7,1,1,1,3,1,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(8,1,1,1,3,1,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(9,1,1,1,4,2,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(10,1,1,1,4,2,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(11,1,1,1,4,2,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(12,1,1,1,4,2,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(13,1,1,1,4,2,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(14,1,1,1,4,2,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(15,1,1,1,4,2,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(16,1,1,1,4,2,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(17,1,1,1,5,3,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(18,1,1,1,5,3,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(19,1,1,1,5,3,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(20,1,1,1,5,3,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(21,1,1,1,5,3,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(22,1,1,1,5,3,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(23,1,1,1,5,3,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(24,1,1,1,5,3,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(25,1,1,1,6,1,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(26,1,1,1,6,1,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(27,1,1,1,6,1,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(28,1,1,1,6,1,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(29,1,1,1,6,1,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(30,1,1,1,6,1,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(31,1,1,1,6,1,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(32,1,1,1,6,1,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(33,1,1,1,7,2,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(34,1,1,1,7,2,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(35,1,1,1,7,2,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(36,1,1,1,7,2,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(37,1,1,1,7,2,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(38,1,1,1,7,2,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(39,1,1,1,7,2,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(40,1,1,1,7,2,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(41,2,1,2,8,4,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(42,2,1,2,8,4,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(43,2,1,2,8,4,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(44,2,1,2,8,4,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(45,2,1,2,8,4,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(46,2,1,2,8,4,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(47,2,1,2,8,4,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(48,2,1,2,8,4,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(49,2,1,2,9,5,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(50,2,1,2,9,5,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(51,2,1,2,9,5,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(52,2,1,2,9,5,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(53,2,1,2,9,5,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(54,2,1,2,9,5,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(55,2,1,2,9,5,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(56,2,1,2,9,5,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(57,2,1,2,10,6,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(58,2,1,2,10,6,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(59,2,1,2,10,6,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(60,2,1,2,10,6,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(61,2,1,2,10,6,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(62,2,1,2,10,6,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(63,2,1,2,10,6,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(64,2,1,2,10,6,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(65,2,1,2,11,4,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(66,2,1,2,11,4,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(67,2,1,2,11,4,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(68,2,1,2,11,4,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(69,2,1,2,11,4,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(70,2,1,2,11,4,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(71,2,1,2,11,4,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(72,2,1,2,11,4,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(73,2,1,2,12,5,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(74,2,1,2,12,5,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(75,2,1,2,12,5,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(76,2,1,2,12,5,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(77,2,1,2,12,5,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(78,2,1,2,12,5,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(79,2,1,2,12,5,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(80,2,1,2,12,5,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(81,3,1,3,13,7,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(82,3,1,3,13,7,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(83,3,1,3,13,7,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(84,3,1,3,13,7,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(85,3,1,3,13,7,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(86,3,1,3,13,7,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(87,3,1,3,13,7,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(88,3,1,3,13,7,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(89,3,1,3,14,8,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(90,3,1,3,14,8,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(91,3,1,3,14,8,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(92,3,1,3,14,8,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(93,3,1,3,14,8,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(94,3,1,3,14,8,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(95,3,1,3,14,8,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(96,3,1,3,14,8,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(97,3,1,3,15,9,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(98,3,1,3,15,9,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(99,3,1,3,15,9,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(100,3,1,3,15,9,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(101,3,1,3,15,9,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(102,3,1,3,15,9,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(103,3,1,3,15,9,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(104,3,1,3,15,9,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(105,3,1,3,16,7,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(106,3,1,3,16,7,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(107,3,1,3,16,7,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(108,3,1,3,16,7,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(109,3,1,3,16,7,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(110,3,1,3,16,7,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(111,3,1,3,16,7,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(112,3,1,3,16,7,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(113,3,1,3,17,8,NULL,'2026-07-27','2026-07-27 09:00:00','2026-07-27 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(114,3,1,3,17,8,NULL,'2026-07-27','2026-07-27 17:00:00','2026-07-27 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(115,3,1,3,17,8,NULL,'2026-07-28','2026-07-28 09:00:00','2026-07-28 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(116,3,1,3,17,8,NULL,'2026-07-29','2026-07-29 09:00:00','2026-07-29 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(117,3,1,3,17,8,NULL,'2026-07-29','2026-07-29 17:00:00','2026-07-29 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(118,3,1,3,17,8,NULL,'2026-07-30','2026-07-30 09:00:00','2026-07-30 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(119,3,1,3,17,8,NULL,'2026-07-31','2026-07-31 09:00:00','2026-07-31 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(120,3,1,3,17,8,NULL,'2026-07-31','2026-07-31 17:00:00','2026-07-31 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(121,4,1,1,3,1,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(122,4,1,1,3,1,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(123,4,1,1,3,1,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(124,4,1,1,3,1,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(125,4,1,1,3,1,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(126,4,1,1,3,1,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(127,4,1,1,3,1,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(128,4,1,1,3,1,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(129,4,1,1,4,2,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(130,4,1,1,4,2,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(131,4,1,1,4,2,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(132,4,1,1,4,2,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(133,4,1,1,4,2,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(134,4,1,1,4,2,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(135,4,1,1,4,2,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(136,4,1,1,4,2,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(137,4,1,1,5,3,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(138,4,1,1,5,3,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(139,4,1,1,5,3,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(140,4,1,1,5,3,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(141,4,1,1,5,3,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(142,4,1,1,5,3,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(143,4,1,1,5,3,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(144,4,1,1,5,3,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(145,4,1,1,6,1,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(146,4,1,1,6,1,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(147,4,1,1,6,1,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(148,4,1,1,6,1,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(149,4,1,1,6,1,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(150,4,1,1,6,1,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(151,4,1,1,6,1,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(152,4,1,1,6,1,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(153,4,1,1,7,2,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(154,4,1,1,7,2,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(155,4,1,1,7,2,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(156,4,1,1,7,2,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(157,4,1,1,7,2,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(158,4,1,1,7,2,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(159,4,1,1,7,2,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(160,4,1,1,7,2,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(161,5,1,2,8,4,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(162,5,1,2,8,4,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(163,5,1,2,8,4,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(164,5,1,2,8,4,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(165,5,1,2,8,4,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(166,5,1,2,8,4,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(167,5,1,2,8,4,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(168,5,1,2,8,4,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(169,5,1,2,9,5,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(170,5,1,2,9,5,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(171,5,1,2,9,5,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(172,5,1,2,9,5,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(173,5,1,2,9,5,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(174,5,1,2,9,5,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(175,5,1,2,9,5,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(176,5,1,2,9,5,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(177,5,1,2,10,6,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(178,5,1,2,10,6,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(179,5,1,2,10,6,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(180,5,1,2,10,6,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(181,5,1,2,10,6,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(182,5,1,2,10,6,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(183,5,1,2,10,6,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(184,5,1,2,10,6,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(185,5,1,2,11,4,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(186,5,1,2,11,4,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(187,5,1,2,11,4,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(188,5,1,2,11,4,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(189,5,1,2,11,4,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(190,5,1,2,11,4,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(191,5,1,2,11,4,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(192,5,1,2,11,4,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(193,5,1,2,12,5,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(194,5,1,2,12,5,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(195,5,1,2,12,5,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(196,5,1,2,12,5,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(197,5,1,2,12,5,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(198,5,1,2,12,5,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(199,5,1,2,12,5,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(200,5,1,2,12,5,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(201,6,1,3,13,7,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(202,6,1,3,13,7,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(203,6,1,3,13,7,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(204,6,1,3,13,7,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(205,6,1,3,13,7,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(206,6,1,3,13,7,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(207,6,1,3,13,7,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(208,6,1,3,13,7,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(209,6,1,3,14,8,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(210,6,1,3,14,8,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(211,6,1,3,14,8,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(212,6,1,3,14,8,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(213,6,1,3,14,8,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(214,6,1,3,14,8,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(215,6,1,3,14,8,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(216,6,1,3,14,8,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(217,6,1,3,15,9,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(218,6,1,3,15,9,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(219,6,1,3,15,9,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(220,6,1,3,15,9,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(221,6,1,3,15,9,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(222,6,1,3,15,9,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(223,6,1,3,15,9,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(224,6,1,3,15,9,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(225,6,1,3,16,7,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(226,6,1,3,16,7,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(227,6,1,3,16,7,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(228,6,1,3,16,7,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(229,6,1,3,16,7,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(230,6,1,3,16,7,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(231,6,1,3,16,7,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(232,6,1,3,16,7,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(233,6,1,3,17,8,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(234,6,1,3,17,8,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(235,6,1,3,17,8,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(236,6,1,3,17,8,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(237,6,1,3,17,8,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(238,6,1,3,17,8,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(239,6,1,3,17,8,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01'),(240,6,1,3,17,8,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `rota_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rota_versions`
--

DROP TABLE IF EXISTS `rota_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rota_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `week_start` date NOT NULL,
  `version_number` smallint unsigned NOT NULL DEFAULT '1',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by_user_id` bigint unsigned DEFAULT NULL,
  `finalized_by_user_id` bigint unsigned DEFAULT NULL,
  `published_by_user_id` bigint unsigned DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rota_versions_unique` (`organization_id`,`branch_id`,`week_start`,`version_number`),
  KEY `rota_versions_branch_id_foreign` (`branch_id`),
  KEY `rota_versions_created_by_user_id_foreign` (`created_by_user_id`),
  KEY `rota_versions_finalized_by_user_id_foreign` (`finalized_by_user_id`),
  KEY `rota_versions_published_by_user_id_foreign` (`published_by_user_id`),
  KEY `rota_versions_week_status_idx` (`organization_id`,`branch_id`,`week_start`,`status`),
  CONSTRAINT `rota_versions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_versions_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rota_versions_finalized_by_user_id_foreign` FOREIGN KEY (`finalized_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rota_versions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rota_versions_published_by_user_id_foreign` FOREIGN KEY (`published_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_versions`
--

LOCK TABLES `rota_versions` WRITE;
/*!40000 ALTER TABLE `rota_versions` DISABLE KEYS */;
INSERT INTO `rota_versions` VALUES (1,1,1,'2026-07-27',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-07-25 19:00:00',NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(2,1,2,'2026-07-27',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-07-25 19:00:00',NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(3,1,3,'2026-07-27',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-07-25 19:00:00',NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(4,1,1,'2026-08-03',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-08-01 19:00:00',NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(5,1,2,'2026-08-03',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-08-01 19:00:00',NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(6,1,3,'2026-08-03',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-08-01 19:00:00',NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `rota_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_reports`
--

DROP TABLE IF EXISTS `saved_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saved_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filters` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `saved_reports_organization_id_foreign` (`organization_id`),
  KEY `saved_reports_user_id_foreign` (`user_id`),
  CONSTRAINT `saved_reports_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saved_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_reports`
--

LOCK TABLES `saved_reports` WRITE;
/*!40000 ALTER TABLE `saved_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `saved_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduled_payments`
--

DROP TABLE IF EXISTS `scheduled_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheduled_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `payable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payable_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `scheduled_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `finance_bank_account_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scheduled_payments_branch_id_foreign` (`branch_id`),
  KEY `scheduled_payments_finance_bank_account_id_foreign` (`finance_bank_account_id`),
  KEY `scheduled_payments_organization_id_scheduled_date_status_index` (`organization_id`,`scheduled_date`,`status`),
  CONSTRAINT `scheduled_payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scheduled_payments_finance_bank_account_id_foreign` FOREIGN KEY (`finance_bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scheduled_payments_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduled_payments`
--

LOCK TABLES `scheduled_payments` WRITE;
/*!40000 ALTER TABLE `scheduled_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduled_reports`
--

DROP TABLE IF EXISTS `scheduled_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheduled_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saved_report_id` bigint unsigned DEFAULT NULL,
  `report_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `filters` json DEFAULT NULL,
  `format` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email',
  `created_by` bigint unsigned DEFAULT NULL,
  `frequency` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `run_at` time DEFAULT NULL,
  `recipients` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_run_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scheduled_reports_organization_id_foreign` (`organization_id`),
  KEY `scheduled_reports_saved_report_id_foreign` (`saved_report_id`),
  KEY `scheduled_reports_branch_id_foreign` (`branch_id`),
  KEY `scheduled_reports_created_by_foreign` (`created_by`),
  CONSTRAINT `scheduled_reports_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scheduled_reports_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scheduled_reports_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scheduled_reports_saved_report_id_foreign` FOREIGN KEY (`saved_report_id`) REFERENCES `saved_reports` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduled_reports`
--

LOCK TABLES `scheduled_reports` WRITE;
/*!40000 ALTER TABLE `scheduled_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_logs`
--

DROP TABLE IF EXISTS `security_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `event` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `security_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `security_logs_event_index` (`event`),
  CONSTRAINT `security_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_logs`
--

LOCK TABLES `security_logs` WRITE;
/*!40000 ALTER TABLE `security_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_group_key_unique` (`group`,`key`),
  KEY `settings_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'general','platform_name','TotalCashPro','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(2,'general','support_email','hello@totalcashpro.com','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(3,'general','default_currency','GBP','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(4,'general','timezone','Europe/London','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(5,'brand','primary_color','#16A34A','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(6,'brand','logo_path','/logo.png','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(7,'brand','favicon_path','/favicon.png','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(8,'seo','default_title','TotalCashPro','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(9,'seo','meta_description','Cash, staff and reporting for multi-branch businesses.','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(10,'email','from_name','TotalCashPro','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(11,'email','from_address','noreply@totalcashpro.com','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(12,'payments','provider','manual','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(13,'payments','currency','GBP','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(14,'system','app_environment','production','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(15,'system','queue_driver','database','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(16,'appearance','default_theme','Light','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(17,'appearance','density','Comfortable','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(18,'maintenance','maintenance_mode','Off','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(19,'maintenance','banner_message','Scheduled maintenance window','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(20,'localization','locale','en_GB','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(21,'localization','date_format','d M Y','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(22,'localization','currency','GBP','string','2026-08-08 14:19:49','2026-08-08 14:19:49'),(23,'localization','timezone','Europe/London','string','2026-08-08 14:19:49','2026-08-08 14:19:49');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shift_swap_requests`
--

DROP TABLE IF EXISTS `shift_swap_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shift_swap_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `requester_id` bigint unsigned NOT NULL,
  `target_user_id` bigint unsigned DEFAULT NULL,
  `rota_shift_id` bigint unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shift_swap_requests_organization_id_foreign` (`organization_id`),
  KEY `shift_swap_requests_branch_id_foreign` (`branch_id`),
  KEY `shift_swap_requests_requester_id_foreign` (`requester_id`),
  KEY `shift_swap_requests_target_user_id_foreign` (`target_user_id`),
  KEY `shift_swap_requests_rota_shift_id_foreign` (`rota_shift_id`),
  KEY `shift_swap_requests_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `shift_swap_requests_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shift_swap_requests_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shift_swap_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shift_swap_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shift_swap_requests_rota_shift_id_foreign` FOREIGN KEY (`rota_shift_id`) REFERENCES `rota_shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shift_swap_requests_target_user_id_foreign` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shift_swap_requests`
--

LOCK TABLES `shift_swap_requests` WRITE;
/*!40000 ALTER TABLE `shift_swap_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `shift_swap_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spendings`
--

DROP TABLE IF EXISTS `spendings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spendings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `bank_account_id` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `spent_date` date NOT NULL,
  `payment_method` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spendings_branch_id_foreign` (`branch_id`),
  KEY `spendings_created_by_foreign` (`created_by`),
  KEY `spendings_organization_id_spent_date_index` (`organization_id`,`spent_date`),
  KEY `spendings_bank_account_id_foreign` (`bank_account_id`),
  CONSTRAINT `spendings_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `finance_bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `spendings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `spendings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `spendings_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spendings`
--

LOCK TABLES `spendings` WRITE;
/*!40000 ALTER TABLE `spendings` DISABLE KEYS */;
INSERT INTO `spendings` VALUES (1,1,2,'Equipment repair','maintenance',185.00,0.00,0.00,185.00,'paid',NULL,NULL,NULL,'2026-08-05',NULL,NULL,2,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `spendings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_availability`
--

DROP TABLE IF EXISTS `staff_availability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff_availability` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `day_of_week` tinyint unsigned NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_availability_user_id_day_of_week_unique` (`user_id`,`day_of_week`),
  KEY `staff_availability_organization_id_foreign` (`organization_id`),
  KEY `staff_availability_branch_id_foreign` (`branch_id`),
  CONSTRAINT `staff_availability_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_availability_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_availability_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_availability`
--

LOCK TABLES `staff_availability` WRITE;
/*!40000 ALTER TABLE `staff_availability` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_availability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_adjustments`
--

DROP TABLE IF EXISTS `stock_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `adjustment_pcs` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_adjustments_organization_id_foreign` (`organization_id`),
  KEY `stock_adjustments_branch_id_foreign` (`branch_id`),
  KEY `stock_adjustments_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `stock_adjustments_created_by_foreign` (`created_by`),
  CONSTRAINT `stock_adjustments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_adjustments_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustments_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_adjustments`
--

LOCK TABLES `stock_adjustments` WRITE;
/*!40000 ALTER TABLE `stock_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_transfers`
--

DROP TABLE IF EXISTS `stock_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `from_branch_id` bigint unsigned NOT NULL,
  `to_branch_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `quantity_pcs` int NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfers_organization_id_foreign` (`organization_id`),
  KEY `stock_transfers_from_branch_id_foreign` (`from_branch_id`),
  KEY `stock_transfers_to_branch_id_foreign` (`to_branch_id`),
  KEY `stock_transfers_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `stock_transfers_requested_by_foreign` (`requested_by`),
  CONSTRAINT `stock_transfers_from_branch_id_foreign` FOREIGN KEY (`from_branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_to_branch_id_foreign` FOREIGN KEY (`to_branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_transfers`
--

LOCK TABLES `stock_transfers` WRITE;
/*!40000 ALTER TABLE `stock_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_histories`
--

DROP TABLE IF EXISTS `subscription_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `organization_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `subscription_histories_subscription_id_foreign` (`subscription_id`),
  KEY `subscription_histories_plan_id_foreign` (`plan_id`),
  KEY `subscription_histories_organization_id_created_at_index` (`organization_id`,`created_at`),
  CONSTRAINT `subscription_histories_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscription_histories_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subscription_histories_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_histories`
--

LOCK TABLES `subscription_histories` WRITE;
/*!40000 ALTER TABLE `subscription_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscription_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `pending_plan_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trialing',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `trial_starts_at` timestamp NULL DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `current_period_start` timestamp NULL DEFAULT NULL,
  `current_period_end` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `stripe_customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `subscriptions_organization_id_status_index` (`organization_id`,`status`),
  KEY `subscriptions_status_index` (`status`),
  KEY `subscriptions_ends_at_index` (`ends_at`),
  KEY `subscriptions_trial_ends_at_index` (`trial_ends_at`),
  KEY `subscriptions_current_period_end_index` (`current_period_end`),
  KEY `subscriptions_pending_plan_id_foreign` (`pending_plan_id`),
  CONSTRAINT `subscriptions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscriptions_pending_plan_id_foreign` FOREIGN KEY (`pending_plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (1,1,2,NULL,'active','2026-02-08 14:19:49',NULL,NULL,NULL,'2026-07-31 19:00:00','2026-08-31 18:59:59',NULL,NULL,NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49',NULL);
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_contacts`
--

DROP TABLE IF EXISTS `supplier_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_contacts_organization_id_foreign` (`organization_id`),
  KEY `supplier_contacts_supplier_id_is_primary_index` (`supplier_id`,`is_primary`),
  CONSTRAINT `supplier_contacts_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_contacts_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_contacts`
--

LOCK TABLES `supplier_contacts` WRITE;
/*!40000 ALTER TABLE `supplier_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_disputes`
--

DROP TABLE IF EXISTS `supplier_disputes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_disputes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `supplier_invoice_id` bigint unsigned DEFAULT NULL,
  `invoice_match_id` bigint unsigned DEFAULT NULL,
  `disputed_amount` decimal(12,2) NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `resolved_by` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_disputes_organization_id_foreign` (`organization_id`),
  KEY `supplier_disputes_supplier_id_foreign` (`supplier_id`),
  KEY `supplier_disputes_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  KEY `supplier_disputes_invoice_match_id_foreign` (`invoice_match_id`),
  KEY `supplier_disputes_created_by_foreign` (`created_by`),
  KEY `supplier_disputes_resolved_by_foreign` (`resolved_by`),
  CONSTRAINT `supplier_disputes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_disputes_invoice_match_id_foreign` FOREIGN KEY (`invoice_match_id`) REFERENCES `invoice_matches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_disputes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_disputes_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_disputes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_disputes_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_disputes`
--

LOCK TABLES `supplier_disputes` WRITE;
/*!40000 ALTER TABLE `supplier_disputes` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_disputes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_invoice_lines`
--

DROP TABLE IF EXISTS `supplier_invoice_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_invoice_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_invoice_id` bigint unsigned NOT NULL,
  `purchase_order_line_id` bigint unsigned DEFAULT NULL,
  `inventory_item_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `vat_rate` decimal(5,2) NOT NULL DEFAULT '20.00',
  `line_total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_invoice_lines_supplier_invoice_id_foreign` (`supplier_invoice_id`),
  KEY `supplier_invoice_lines_purchase_order_line_id_foreign` (`purchase_order_line_id`),
  KEY `supplier_invoice_lines_inventory_item_id_foreign` (`inventory_item_id`),
  CONSTRAINT `supplier_invoice_lines_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_invoice_lines_purchase_order_line_id_foreign` FOREIGN KEY (`purchase_order_line_id`) REFERENCES `purchase_order_lines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_invoice_lines_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_invoice_lines`
--

LOCK TABLES `supplier_invoice_lines` WRITE;
/*!40000 ALTER TABLE `supplier_invoice_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_invoice_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_invoices`
--

DROP TABLE IF EXISTS `supplier_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `goods_received_note_id` bigint unsigned DEFAULT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_invoice_unique` (`organization_id`,`supplier_id`,`invoice_no`),
  KEY `supplier_invoices_branch_id_foreign` (`branch_id`),
  KEY `supplier_invoices_supplier_id_foreign` (`supplier_id`),
  KEY `supplier_invoices_organization_id_status_index` (`organization_id`,`status`),
  KEY `supplier_invoices_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `supplier_invoices_goods_received_note_id_foreign` (`goods_received_note_id`),
  CONSTRAINT `supplier_invoices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_invoices_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_invoices_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_invoices_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_invoices`
--

LOCK TABLES `supplier_invoices` WRITE;
/*!40000 ALTER TABLE `supplier_invoices` DISABLE KEYS */;
INSERT INTO `supplier_invoices` VALUES (1,1,1,1,NULL,NULL,'FP-2026-001','2026-08-06','2026-08-22',298.20,0.00,0.00,0.00,'GBP',NULL,0.00,'Weekly produce delivery','pending',NULL,NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02'),(2,1,2,5,NULL,NULL,'PM-2026-042','2026-07-19','2026-08-03',1250.00,0.00,0.00,0.00,'GBP',NULL,0.00,NULL,'overdue',NULL,NULL,'2026-08-08 14:20:02','2026-08-08 14:20:02');
/*!40000 ALTER TABLE `supplier_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_price_history`
--

DROP TABLE IF EXISTS `supplier_price_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_price_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `effective_from` date NOT NULL,
  `effective_until` date DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_price_history_organization_id_foreign` (`organization_id`),
  KEY `supplier_price_history_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `supplier_price_history_created_by_foreign` (`created_by`),
  KEY `supplier_price_hist_idx` (`supplier_id`,`inventory_item_id`,`effective_from`),
  CONSTRAINT `supplier_price_history_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_price_history_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_price_history_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_price_history_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_price_history`
--

LOCK TABLES `supplier_price_history` WRITE;
/*!40000 ALTER TABLE `supplier_price_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_price_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_products`
--

DROP TABLE IF EXISTS `supplier_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `inventory_item_id` bigint unsigned NOT NULL,
  `supplier_sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_size` int unsigned NOT NULL DEFAULT '1',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `vat_rate` decimal(5,2) NOT NULL DEFAULT '20.00',
  `moq` decimal(12,2) NOT NULL DEFAULT '0.00',
  `order_multiple` int unsigned NOT NULL DEFAULT '1',
  `lead_time_days` smallint unsigned NOT NULL DEFAULT '0',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `effective_from` date DEFAULT NULL,
  `effective_until` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_products_supplier_id_inventory_item_id_unique` (`supplier_id`,`inventory_item_id`),
  KEY `supplier_products_organization_id_foreign` (`organization_id`),
  KEY `supplier_products_inventory_item_id_foreign` (`inventory_item_id`),
  CONSTRAINT `supplier_products_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_products_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_products`
--

LOCK TABLES `supplier_products` WRITE;
/*!40000 ALTER TABLE `supplier_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trading_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `lead_time_days` smallint unsigned NOT NULL DEFAULT '0',
  `min_order_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_organization_id_foreign` (`organization_id`),
  KEY `suppliers_branch_id_foreign` (`branch_id`),
  CONSTRAINT `suppliers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,1,1,'Fresh Produce Co',NULL,'orders@freshproduce.test','+44 20 7000 1000','Accounts Team',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(2,1,2,'Harbour Beverages',NULL,'sales@harbourbev.test','+44 20 7000 1001','Accounts Team',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(3,1,3,'PackRight Ltd',NULL,'hello@packright.test','+44 20 7000 1002','Accounts Team',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(4,1,1,'CleanPro Supplies',NULL,'orders@cleanpro.test','+44 20 7000 1003','Accounts Team',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(5,1,2,'Premium Meats UK',NULL,'trade@premiummeats.test','+44 20 7000 1004','Accounts Team',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL),(6,1,3,'Thames Valley Foods',NULL,'accounts@thamesvalley.test','+44 20 7000 1005','Accounts Team',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-08 14:20:02','2026-08-08 14:20:02',NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_ticket_replies`
--

DROP TABLE IF EXISTS `support_ticket_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_ticket_replies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_replies_support_ticket_id_foreign` (`support_ticket_id`),
  KEY `support_ticket_replies_user_id_foreign` (`user_id`),
  CONSTRAINT `support_ticket_replies_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_replies`
--

LOCK TABLES `support_ticket_replies` WRITE;
/*!40000 ALTER TABLE `support_ticket_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_ticket_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_organization_id_foreign` (`organization_id`),
  KEY `support_tickets_user_id_foreign` (`user_id`),
  KEY `support_tickets_priority_index` (`priority`),
  KEY `support_tickets_status_index` (`status`),
  CONSTRAINT `support_tickets_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `two_factor_recovery_codes`
--

DROP TABLE IF EXISTS `two_factor_recovery_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `two_factor_recovery_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `two_factor_recovery_codes_user_id_used_at_index` (`user_id`,`used_at`),
  CONSTRAINT `two_factor_recovery_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `two_factor_recovery_codes`
--

LOCK TABLES `two_factor_recovery_codes` WRITE;
/*!40000 ALTER TABLE `two_factor_recovery_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `two_factor_recovery_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_devices`
--

DROP TABLE IF EXISTS `user_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operating_system` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_trusted` tinyint(1) NOT NULL DEFAULT '0',
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `last_active_at` timestamp NULL DEFAULT NULL,
  `logged_out_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_devices_user_id_logged_out_at_index` (`user_id`,`logged_out_at`),
  KEY `user_devices_session_id_index` (`session_id`),
  CONSTRAINT `user_devices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_devices`
--

LOCK TABLES `user_devices` WRITE;
/*!40000 ALTER TABLE `user_devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_code` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `onboarding_completed_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `organization_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_method` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_org_pin_unique` (`organization_id`,`pin_code`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@totalcashpro.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-08 14:19:49',NULL,'$2y$12$blsxojVPNuauywf.q.wwK.V03uZE9LGkPBQ9v1gHeztavbVlKws5m',1,NULL,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49'),(2,'Ava Morgan','ava@harbourkitchen.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-09 14:19:49','2026-06-14 14:19:49','$2y$12$zoD8doKeA40/Pc2SDCmDFuzZPf2iKoaYxvE53ZtZCJgRo0y7rzBYG',2,1,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:49','2026-08-08 14:19:49'),(3,'Jamie Cole','jamie.cole@harbourkitchen.test','+44 7700 001001','$2y$12$Q/Yek.EpzWK83pSn3Bq2BOO88FYtHHFYskCjYFZCPYar9p2sEfQSu',NULL,11.50,NULL,NULL,NULL,'2026-08-08 14:19:50',NULL,'$2y$12$dXg5ZGdfJIxjhLHKRXeKY.odDyRPDnY6dTsvWIRG0gHiDjq5Ye/Pi',3,1,1,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:50','2026-08-08 14:19:50'),(4,'Priya Shah','priya.shah@harbourkitchen.test','+44 7700 001001','$2y$12$vP1Ns2WgbAeeCpJffD3kQ.y5LZNsh/GnKnq.7nVBV4IJqyWG.zOFG',NULL,12.25,NULL,NULL,NULL,'2026-08-08 14:19:51',NULL,'$2y$12$6JdC9iNVMkcEmcspg9.5juj7BR255Fq4dryIdVwjInNIwrQgyFwCO',3,1,1,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:51','2026-08-08 14:19:51'),(5,'Marcus Lee','marcus.lee@harbourkitchen.test','+44 7700 001001','$2y$12$n54Y6sDtql9rXI9LjbtS0uH4NoUfrp0Iss2ksKyn30ng2kjg1uB4K',NULL,13.00,NULL,NULL,NULL,'2026-08-08 14:19:52',NULL,'$2y$12$O67OSCSSftuQ3YYm631LbuAt/TuTbudcZg/zaIumWCd1kOK2x.KNu',3,1,1,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:52','2026-08-08 14:19:52'),(6,'Elena Voss','elena.voss@harbourkitchen.test','+44 7700 001001','$2y$12$zAflyKQ0Wbr8zU6rjT/HmuIWr0VfemYLotyQ1WBNCMCkiJd1t3pze',NULL,13.75,NULL,NULL,NULL,'2026-08-08 14:19:52',NULL,'$2y$12$9HCUR5H2HCa2aCo8kRytdetQrh0BLHwctimg9mugzRDzKc10mmHdm',3,1,1,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:52','2026-08-08 14:19:52'),(7,'Tom Wright','tom.wright@harbourkitchen.test','+44 7700 001001','$2y$12$R01ZjzLpF7yN0pVQGp23Pe1GvAEAW2t1nzlk6woJPakc5tIchKz0a',NULL,14.50,NULL,NULL,NULL,'2026-08-08 14:19:53',NULL,'$2y$12$grsnYUqYB5S2xyOGp43kceNuZgro0Rvk1A4idobsKnaHd3BcnNbpa',3,1,1,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:53','2026-08-08 14:19:53'),(8,'Sofia Reed','sofia.reed@harbourkitchen.test','+44 7700 001001','$2y$12$OEXRnuSBl4xzswTLqV0yJ.TRJMdn/Bha/irid5jo8l0Nb3uEaHL.i',NULL,11.50,NULL,NULL,NULL,'2026-08-08 14:19:54',NULL,'$2y$12$6Mrt0F92uy/tB6FTeZRqhu9ljQiIloPFv1x39YZlTtrIjqwjDFIOC',3,1,2,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:54','2026-08-08 14:19:54'),(9,'Noah Blake','noah.blake@harbourkitchen.test','+44 7700 001001','$2y$12$HAJkMGsHm5qEgZqqItLrQebHksPZ4jfkgLlo0KS0YYesTEPw3jIiC',NULL,12.25,NULL,NULL,NULL,'2026-08-08 14:19:55',NULL,'$2y$12$GSKeWcXGcbeQI1nqADDHWO45f1sJIEdEr/vClhzR5ZGNCnHTf8OtC',3,1,2,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:55','2026-08-08 14:19:55'),(10,'Amara Singh','amara.singh@harbourkitchen.test','+44 7700 001001','$2y$12$71Xupwyq2lswiGzjbMYs2.yfdCb4/8Gf9aWs.lzCJu2gH2Lms3T32',NULL,13.00,NULL,NULL,NULL,'2026-08-08 14:19:55',NULL,'$2y$12$/.LIwoTeQQs4fz/YcPSobeDUpjzk2jupvXQv5FvdMCbGGDAAC8vS2',3,1,2,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:55','2026-08-08 14:19:55'),(11,'Jake Foster','jake.foster@harbourkitchen.test','+44 7700 001001','$2y$12$J8O13vaQDGVqZA4KLMWxF.0Ko9W3gkmr17eKPzPB.DcdWWtDUt.ZC',NULL,13.75,NULL,NULL,NULL,'2026-08-08 14:19:56',NULL,'$2y$12$QzqdBOAQ7/GPteqygUfy8u5zAXZ63ZmprPEoqvKNmWesOsgovsLIu',3,1,2,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:56','2026-08-08 14:19:56'),(12,'Lily Chen','lily.chen@harbourkitchen.test','+44 7700 001001','$2y$12$XSWvhkO4jyXK0hfGbXXzIO.QVUibyTrkA7bhFC6IkN592CUECm/MS',NULL,14.50,NULL,NULL,NULL,'2026-08-08 14:19:57',NULL,'$2y$12$QQ7j4knZPCLAmnFaPBycQOFv3G4u6GNDI/FMyAlchzwcKGNZxqo72',3,1,2,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:57','2026-08-08 14:19:57'),(13,'Owen Harris','owen.harris@harbourkitchen.test','+44 7700 001001','$2y$12$gvGD6UPNysxr/4NgmFR3zuM76QSdmmJ6MTnDXN.i/71kIlsSwKC1C',NULL,11.50,NULL,NULL,NULL,'2026-08-08 14:19:57',NULL,'$2y$12$GgKlWX.7WYXOnagvl/nZr..7th4MxVKTz0..c0DtpSQGGH6Ouo7iW',3,1,3,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:57','2026-08-08 14:19:57'),(14,'Mia Patel','mia.patel@harbourkitchen.test','+44 7700 001001','$2y$12$D2gZZ89DUyLtaGCVIatpy.7rsbZaGPm4kPTJ5lDx5..0DCvG2sS66',NULL,12.25,NULL,NULL,NULL,'2026-08-08 14:19:58',NULL,'$2y$12$bKzc72pgKFS6N9wObiyLHO2PfhxDPw7.nSBCJp1OwvWwN3DDIPem6',3,1,3,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:58','2026-08-08 14:19:58'),(15,'Ryan O\'Brien','ryan.obrien@harbourkitchen.test','+44 7700 001001','$2y$12$aL8cLc0e3nrlPlNJUV7slOo/PeyeqpM9V4U5gKWrn7S2XPZVm7P.G',NULL,13.00,NULL,NULL,NULL,'2026-08-08 14:19:59',NULL,'$2y$12$kJcgYDrfbVfEpkQ0sXI9xeQIFZFuD2gdmlVGaJAQbbzEoFXvQE.Mq',3,1,3,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:19:59','2026-08-08 14:19:59'),(16,'Zara Ahmed','zara.ahmed@harbourkitchen.test','+44 7700 001001','$2y$12$ihDTNuqOd5816hSZq95YUO1nVDISW4/UAED5ZrAv9FgouV/xU5zCO',NULL,13.75,NULL,NULL,NULL,'2026-08-08 14:20:00',NULL,'$2y$12$UXNET0bJ2qoDPI6XVbOhc.7xutN3QyT/yeliFdPTTm/pjL0om3.1.',3,1,3,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:20:00','2026-08-08 14:20:00'),(17,'Ben Cooper','ben.cooper@harbourkitchen.test','+44 7700 001001','$2y$12$fCgCfPfdcAzq4CLt7Yzt9.YAGxgdRF5FFfn8uAbkMwQKyFO1JjFm6',NULL,14.50,NULL,NULL,NULL,'2026-08-08 14:20:01',NULL,'$2y$12$EiiJYHU335zjhyyZ4M0Gs.1xLzgiIGXtves8wWCP/QMA.V0P.56Vy',3,1,3,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01'),(18,'Alex Rider','rider@harbourkitchen.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-08 14:20:01',NULL,'$2y$12$LdqRd6AyHmeTOWQ.DP9z5.6S3xGxlP0Dux56mAhQfYi09qvDrCzzy',4,1,1,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-08 14:20:01','2026-08-08 14:20:01');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wages`
--

DROP TABLE IF EXISTS `wages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `payroll_run_id` bigint unsigned DEFAULT NULL,
  `hours_worked` decimal(8,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `payment_due_date` date DEFAULT NULL,
  `from_attendance` tinyint(1) NOT NULL DEFAULT '0',
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `paid_date` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wages_organization_id_foreign` (`organization_id`),
  KEY `wages_branch_id_foreign` (`branch_id`),
  KEY `wages_user_id_foreign` (`user_id`),
  KEY `wages_created_by_foreign` (`created_by`),
  KEY `wages_payroll_run_id_foreign` (`payroll_run_id`),
  CONSTRAINT `wages_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wages_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wages_payroll_run_id_foreign` FOREIGN KEY (`payroll_run_id`) REFERENCES `finance_payroll_runs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wages`
--

LOCK TABLES `wages` WRITE;
/*!40000 ALTER TABLE `wages` DISABLE KEYS */;
/*!40000 ALTER TABLE `wages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-09  0:20:08
