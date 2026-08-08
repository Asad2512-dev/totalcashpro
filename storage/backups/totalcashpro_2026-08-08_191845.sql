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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_requests`
--

LOCK TABLES `access_requests` WRITE;
/*!40000 ALTER TABLE `access_requests` DISABLE KEYS */;
INSERT INTO `access_requests` VALUES (1,NULL,NULL,'Coastal Cafe Co','Priya Shah','newbiz@coastalcafe.test','+44 7700 90100',NULL,'GB','Cafe','6-20','professional','Looking to start next month across two locations.',NULL,'pending',NULL,'2026-08-04 17:08:07','2026-08-04 17:08:07'),(2,NULL,1,'Old Mill Foods','Dan Foster','rejected@oldmill.test','+44 7700 90101',NULL,'GB','Retail','1-5','basic',NULL,'Incomplete business details.','rejected','2026-08-05 02:08:23','2026-08-04 17:08:07','2026-08-07 02:08:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'Super Admin','demo.seeded','Demo Super Admin dataset seeded',NULL,NULL,'{\"source\": \"DemoDataSeeder\"}','2026-08-04 17:08:07'),(2,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-04 17:08:13'),(3,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-04 17:08:19'),(4,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-04 17:08:23'),(5,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-04 17:08:31'),(6,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-04 17:08:40'),(7,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-04 17:09:08'),(8,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-05 08:03:41'),(9,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-05 13:18:46'),(10,1,'Super Admin','user.login','Super Admin signed in','App\\Models\\User',1,NULL,'2026-08-06 17:49:07'),(11,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-06 17:49:39'),(12,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-07 00:31:39'),(13,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-07 00:37:47'),(14,3,'Jamie Cole','user.login','Staff signed in','App\\Models\\User',3,NULL,'2026-08-07 00:49:26'),(15,3,'Jamie Cole','attendance.clock-in','Jamie Cole performed clock-in','App\\Models\\User',3,NULL,'2026-08-07 00:49:38'),(16,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-07 00:51:34'),(17,2,'Ava Morgan','kiosk.opened','Attendance kiosk opened',NULL,NULL,'{\"branch_id\": 2, \"branch_name\": \"Dockside\"}','2026-08-07 01:15:31'),(18,2,'Ava Morgan','kiosk.pin_failed','Failed PIN attempt on attendance kiosk',NULL,NULL,'{\"branch_id\": 2}','2026-08-07 01:15:35'),(19,2,'Ava Morgan','kiosk.pin_failed','Failed PIN attempt on attendance kiosk',NULL,NULL,'{\"branch_id\": 2}','2026-08-07 01:15:36'),(20,1,'Super Admin','demo.seeded','Demo Super Admin dataset seeded',NULL,NULL,'{\"source\": \"DemoDataSeeder\"}','2026-08-07 02:08:12'),(21,1,'Super Admin','demo.seeded','Demo Super Admin dataset seeded',NULL,NULL,'{\"source\": \"DemoDataSeeder\"}','2026-08-07 02:08:23'),(22,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-07 04:01:28'),(23,1,'Super Admin','user.login','Super Admin signed in','App\\Models\\User',1,NULL,'2026-08-07 05:26:40'),(24,2,'Ava Morgan','user.login','Business Admin signed in','App\\Models\\User',2,NULL,'2026-08-07 05:29:11');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'April platform maintenance','Scheduled maintenance this Sunday 02:00–04:00 BST.','everyone',NULL,NULL,'both','published','2026-08-10 02:08:23','2026-08-06 02:08:23','2026-08-04 17:08:07','2026-08-07 02:08:23',NULL),(2,'Professional plan feature drop','New branch reports are rolling out for Professional accounts.','professional','professional',NULL,'in_app','draft','2026-08-14 02:08:23',NULL,'2026-08-04 17:08:07','2026-08-07 02:08:23',NULL);
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
INSERT INTO `attendance_breaks` VALUES (1,1,2,3,'other',NULL,'2026-08-05 12:00:00','2026-08-05 12:30:00','active',0,NULL,'manual',NULL,NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_logs`
--

LOCK TABLES `attendance_logs` WRITE;
/*!40000 ALTER TABLE `attendance_logs` DISABLE KEYS */;
INSERT INTO `attendance_logs` VALUES (1,1,2,3,NULL,NULL,'clock-in','manual','2026-08-03 09:00:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,2,3,NULL,NULL,'clock-out','manual','2026-08-03 15:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(3,1,2,3,NULL,NULL,'clock-in','manual','2026-08-04 09:00:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(4,1,2,3,NULL,NULL,'clock-out','manual','2026-08-04 15:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(5,1,2,3,NULL,NULL,'clock-in','manual','2026-08-05 09:00:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(6,1,2,3,NULL,NULL,'clock-out','manual','2026-08-05 15:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(7,1,2,3,NULL,NULL,'clock-in','manual','2026-08-06 09:00:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(8,1,2,3,NULL,NULL,'clock-out','manual','2026-08-06 15:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(9,1,2,3,NULL,NULL,'clock-in','manual','2026-08-07 09:00:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(10,1,2,3,NULL,NULL,'clock-out','manual','2026-08-07 15:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(11,1,2,18,NULL,NULL,'clock-in','manual','2026-08-03 09:05:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(12,1,2,18,NULL,NULL,'clock-out','manual','2026-08-03 15:11:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(13,1,2,18,NULL,NULL,'clock-in','manual','2026-08-04 09:05:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(14,1,2,18,NULL,NULL,'clock-out','manual','2026-08-04 15:11:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(15,1,2,18,NULL,NULL,'clock-in','manual','2026-08-05 09:05:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(16,1,2,18,NULL,NULL,'clock-out','manual','2026-08-05 15:11:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(17,1,2,18,NULL,NULL,'clock-in','manual','2026-08-06 09:05:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(18,1,2,18,NULL,NULL,'clock-out','manual','2026-08-06 15:11:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(19,1,2,18,NULL,NULL,'clock-in','manual','2026-08-07 09:05:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(20,1,2,18,NULL,NULL,'clock-out','manual','2026-08-07 15:11:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(21,1,2,19,NULL,NULL,'clock-in','manual','2026-08-03 09:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(22,1,2,19,NULL,NULL,'clock-out','manual','2026-08-03 15:12:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(23,1,2,19,NULL,NULL,'clock-in','manual','2026-08-04 09:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(24,1,2,19,NULL,NULL,'clock-out','manual','2026-08-04 15:12:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(25,1,2,19,NULL,NULL,'clock-in','manual','2026-08-05 09:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(26,1,2,19,NULL,NULL,'clock-out','manual','2026-08-05 15:12:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(27,1,2,19,NULL,NULL,'clock-in','manual','2026-08-06 09:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(28,1,2,19,NULL,NULL,'clock-out','manual','2026-08-06 15:12:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(29,1,2,19,NULL,NULL,'clock-in','manual','2026-08-07 09:10:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(30,1,2,19,NULL,NULL,'clock-out','manual','2026-08-07 15:12:00',NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(31,1,2,3,NULL,NULL,'clock-in','manual','2026-08-04 22:08:13',NULL,'2026-08-04 17:08:13','2026-08-04 17:08:13'),(32,1,2,3,NULL,NULL,'clock-in','manual','2026-07-29 09:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(33,1,2,3,NULL,NULL,'clock-out','manual','2026-07-29 16:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(34,1,2,3,NULL,NULL,'clock-in','manual','2026-07-22 09:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(35,1,2,3,NULL,NULL,'clock-out','manual','2026-07-22 16:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(36,1,2,3,NULL,NULL,'clock-in','manual','2026-07-15 09:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(37,1,2,3,NULL,NULL,'clock-out','manual','2026-07-15 16:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(38,1,2,3,NULL,NULL,'clock-in','manual','2026-07-08 09:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(39,1,2,3,NULL,NULL,'clock-out','manual','2026-07-08 16:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(40,1,2,3,NULL,NULL,'clock-in','manual','2026-07-01 09:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(41,1,2,3,NULL,NULL,'clock-out','manual','2026-07-01 16:00:00',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(42,1,2,3,NULL,NULL,'clock-in','manual','2026-08-07 05:49:38',NULL,'2026-08-07 00:49:38','2026-08-07 00:49:38'),(43,1,2,3,NULL,NULL,'clock-in','manual','2026-08-07 06:15:38',NULL,'2026-08-07 01:15:38','2026-08-07 01:15:38'),(44,1,2,3,NULL,NULL,'clock-in','manual','2026-08-07 06:15:53',NULL,'2026-08-07 01:15:53','2026-08-07 01:15:53'),(45,1,1,3,NULL,NULL,'clock-in','manual','2026-08-08 10:51:23',NULL,'2026-08-08 05:51:23','2026-08-08 05:51:23'),(46,1,1,3,NULL,NULL,'clock-out','manual','2026-08-08 10:51:39',NULL,'2026-08-08 05:51:39','2026-08-08 05:51:39'),(47,1,1,3,NULL,NULL,'clock-in','manual','2026-08-08 11:00:54',NULL,'2026-08-08 06:00:54','2026-08-08 06:00:54'),(48,1,1,3,NULL,NULL,'clock-out','manual','2026-08-08 11:01:13',NULL,'2026-08-08 06:01:13','2026-08-08 06:01:13'),(49,1,2,18,1,NULL,'clock-in','kiosk','2026-08-08 16:30:35',NULL,'2026-08-08 11:30:35','2026-08-08 11:30:35');
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'demo.seeded',NULL,NULL,'127.0.0.1','DemoDataSeeder',NULL,'{\"organizations\": 8}','2026-08-04 17:08:07'),(2,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','curl/8.7.1',NULL,NULL,'2026-08-04 17:08:13'),(3,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','curl/8.7.1',NULL,NULL,'2026-08-04 17:08:19'),(4,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','curl/8.7.1',NULL,NULL,'2026-08-04 17:08:23'),(5,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','curl/8.7.1',NULL,NULL,'2026-08-04 17:08:31'),(6,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-04 17:08:40'),(7,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','curl/8.7.1',NULL,NULL,'2026-08-04 17:09:08'),(8,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-05 08:03:41'),(9,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-05 13:18:46'),(10,1,'auth.login','App\\Models\\User',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-06 17:49:07'),(11,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-06 17:49:39'),(12,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-07 00:31:39'),(13,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-07 00:37:47'),(14,3,'auth.login.staff','App\\Models\\User',3,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-07 00:49:26'),(15,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-07 00:51:34'),(16,1,'demo.seeded',NULL,NULL,'127.0.0.1','DemoDataSeeder',NULL,'{\"organizations\": 8}','2026-08-07 02:08:12'),(17,1,'demo.seeded',NULL,NULL,'127.0.0.1','DemoDataSeeder',NULL,'{\"organizations\": 8}','2026-08-07 02:08:23'),(18,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-07 04:01:28'),(19,1,'auth.login','App\\Models\\User',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-07 05:26:40'),(20,2,'auth.login.business','App\\Models\\User',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,NULL,'2026-08-07 05:29:11');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bills`
--

LOCK TABLES `bills` WRITE;
/*!40000 ALTER TABLE `bills` DISABLE KEYS */;
INSERT INTO `bills` VALUES (1,1,2,NULL,NULL,'Monthly rent','Harbour Properties','rent',2400.00,2400.00,0.00,2400.00,'2026-08-14','approved',NULL,NULL,NULL,NULL,NULL,2,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,1,NULL,NULL,'Business insurance','CoverSure','insurance',185.00,154.17,30.83,185.00,'2026-08-09','approved',NULL,NULL,NULL,NULL,NULL,2,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(3,1,2,NULL,NULL,'Gas & electricity','Brighton Energy','utilities',420.00,350.00,70.00,420.00,'2026-07-24','paid',1,'2026-08-02 09:33:57','2026-08-03 09:33:57',NULL,NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(4,1,2,NULL,NULL,'POS terminal rental','SumUp','utilities',49.00,40.83,8.17,49.00,'2026-08-13','approved',1,'2026-08-02 09:33:57',NULL,NULL,NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(5,1,1,NULL,NULL,'Water rates Q2','Thames Water','utilities',310.00,258.33,51.67,310.00,'2026-08-19','approved',1,'2026-08-02 09:33:57',NULL,NULL,NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(6,1,1,NULL,NULL,'Equipment lease','KitchenLease Ltd','utilities',890.00,741.67,148.33,890.00,'2026-08-26','draft',1,NULL,NULL,NULL,NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(7,1,2,NULL,NULL,'Overdue supplier statement','Coastal Wholesale','utilities',156.80,130.67,26.13,156.80,'2026-07-31','overdue',1,'2026-08-02 09:33:57',NULL,NULL,NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_kiosks`
--

LOCK TABLES `branch_kiosks` WRITE;
/*!40000 ALTER TABLE `branch_kiosks` DISABLE KEYS */;
INSERT INTO `branch_kiosks` VALUES (1,1,2,'Front Enterance',NULL,'5T7pNVyxBpG3xZ3WqyiUsmMu1I3nNlONlE6HdOqjVfn0HBjXgEfPTANjEm48qdcl','Welcome — enter your PIN to clock in or out.',1,NULL,1,'2026-08-08 11:30:07','2026-08-07 01:26:25','2026-08-08 11:30:07'),(2,1,1,'Attendence',NULL,'O4hxUjz6KdS8or78TsB3D4xu2L5Hdm6sV1aR7S1ZEvc97U3cOY4rhGwClqlGXYrs','Welcome — enter your PIN to clock in or out.',1,NULL,1,'2026-08-08 05:59:48','2026-08-08 05:50:39','2026-08-08 06:15:36');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,NULL,'Harbour Central','harbour-central','London','London High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',3,'2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(2,1,NULL,'Dockside','dockside','Brighton','Brighton High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',4,'2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(3,2,NULL,'Main Bakery','main-bakery','Manchester','Manchester High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',3,'2026-08-04 17:08:05','2026-08-04 17:08:05',NULL),(4,3,NULL,'Northbridge HQ','northbridge-hq','Leeds','Leeds High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',3,'2026-08-04 17:08:05','2026-08-04 17:08:05',NULL),(5,3,NULL,'City Market','city-market','York','York High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',4,'2026-08-04 17:08:05','2026-08-04 17:08:05',NULL),(6,4,NULL,'Riverbend','riverbend','Bristol','Bristol High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'closed',3,'2026-08-04 17:08:05','2026-08-04 17:08:05',NULL),(7,5,NULL,'Cedar House','cedar-house','Edinburgh','Edinburgh High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',3,'2026-08-04 17:08:06','2026-08-04 17:08:06',NULL),(8,6,NULL,'Summit Store','summit-store','Cardiff','Cardiff High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',3,'2026-08-04 17:08:06','2026-08-04 17:08:06',NULL),(9,7,NULL,'Greenfield Main','greenfield-main','Birmingham','Birmingham High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',3,'2026-08-04 17:08:07','2026-08-04 17:08:07',NULL),(10,7,NULL,'Westside','westside','Coventry','Coventry High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',4,'2026-08-04 17:08:07','2026-08-04 17:08:07',NULL),(11,8,NULL,'Lakeside Counter','lakeside-counter','Oxford','Oxford High Street',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'open',3,'2026-08-04 17:08:07','2026-08-04 17:08:07',NULL);
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
INSERT INTO `cache` VALUES ('totalcashpro-cache-5c785c036466adea360111aa28563bfd556b5fba','i:3;',1786206667),('totalcashpro-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1786206667;',1786206667),('totalcashpro-cache-77de68daecd823babbb58edb1c8e14d7106e83bb','i:1;',1786081838),('totalcashpro-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer','i:1786081838;',1786081838),('totalcashpro-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0','i:1;',1786187178),('totalcashpro-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0:timer','i:1786187178;',1786187178),('totalcashpro-cache-feature_access.org.1','a:5:{s:12:\"max_branches\";N;s:9:\"max_staff\";N;s:8:\"features\";a:11:{s:7:\"cash_up\";b:1;s:10:\"attendance\";b:1;s:7:\"reports\";b:1;s:9:\"inventory\";b:1;s:7:\"payroll\";b:1;s:4:\"rota\";b:1;s:9:\"suppliers\";b:1;s:10:\"accounting\";b:1;s:16:\"advanced_reports\";b:1;s:17:\"multiple_branches\";b:1;s:11:\"staff_panel\";b:1;}s:9:\"plan_slug\";s:12:\"professional\";s:7:\"bullets\";a:4:{i:0;s:18:\"Unlimited branches\";i:1;s:15:\"Unlimited staff\";i:2;s:19:\"Inventory & payroll\";i:3;s:22:\"Staff rota & analytics\";}}',1786216375),('totalcashpro-cache-report_center_version:1','i:259;',2101300437),('totalcashpro-cache-report_center:v2:1:62ef35c11592d74f6c42148a93d891cc','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-07\";s:2:\"to\";s:10:\"2026-08-05\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:61937.47999999999;s:8:\"expenses\";d:451.49;s:6:\"profit\";d:59368.23999999999;s:4:\"cash\";d:49956.37999999999;s:5:\"cards\";d:23022.4;s:13:\"online_orders\";d:9429.76;s:5:\"bills\";d:156.8;s:10:\"bills_paid\";d:420;s:7:\"payroll\";d:2978.25;s:12:\"payroll_paid\";d:992.75;s:15:\"payroll_pending\";d:1985.5;s:21:\"inventory_adjustments\";i:6;s:9:\"low_stock\";i:4;s:17:\"supplier_payments\";d:705;s:20:\"supplier_outstanding\";d:1080;s:16:\"attendance_hours\";d:46;s:10:\"vat_output\";d:1996.82;s:9:\"vat_input\";d:139.42000000000002;s:7:\"vat_due\";d:1857.3999999999999;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:1736.42;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1752.16;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2122.42;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2978.22;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:2760.48;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:3147.36;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:1003.36;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:118;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:120;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:88.5;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:89.99;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:35;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:1736.42;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1634.16;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2002.42;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2889.72;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:2760.48;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:3057.37;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:968.36;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:7;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:7;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:7;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:7;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:6;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:6;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:6;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:1736.42;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1634.16;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2002.42;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2889.72;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:2760.48;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:3057.37;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:968.36;}}s:14:\"supplier_spend\";a:3:{i:0;a:2:{s:8:\"supplier\";s:16:\"EcoPack Supplies\";s:5:\"total\";d:260;}i:1;a:2:{s:8:\"supplier\";s:19:\"London Dairy Direct\";s:5:\"total\";d:235;}i:2;a:2:{s:8:\"supplier\";s:13:\"Coastal Meats\";s:5:\"total\";d:210;}}s:18:\"expense_categories\";a:2:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:331.49;}i:1;a:2:{s:8:\"category\";s:9:\"marketing\";s:5:\"total\";d:120;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:25078.3;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:24878.08;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"22 Jul · £3,710.48\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:20:\"05 Aug · £1,003.36\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £331.49\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:28:\"EcoPack Supplies · £260.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £25,078.30\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:19:\"Jamie Cole · 46.5h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£13.02 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:120:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:2;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:3;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:4;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:5;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:6;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:7;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:8;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:9;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:10;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:11;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:12;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:13;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:14;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:15;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:16;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:17;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:18;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:19;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:20;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:21;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:22;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:23;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:24;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:25;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:26;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:27;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:28;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:29;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:30;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:31;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:32;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:33;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:34;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:35;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:36;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:37;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:38;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:39;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:40;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:41;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:42;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:43;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:44;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:45;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:46;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:47;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:48;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:49;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:50;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:51;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:52;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:53;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:54;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:55;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:56;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:57;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:58;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:59;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:60;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:61;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:62;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:63;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:64;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:65;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:66;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:67;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:68;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:69;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:70;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:71;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:72;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:73;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:74;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:75;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:76;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:77;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:78;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:79;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:80;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:81;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:82;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:83;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:84;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:85;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:86;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:87;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:88;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:89;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:90;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:91;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:92;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:93;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:94;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:95;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:96;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:97;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:98;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:99;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:100;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:101;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:102;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:103;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:104;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:105;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:106;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:107;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:108;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}i:109;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}i:110;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£387.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:111;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£387.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:112;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£336.90\";i:4;s:8:\"£120.00\";i:5;s:7:\"£76.00\";}i:113;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£336.90\";i:4;s:8:\"£120.00\";i:5;s:7:\"£76.00\";}i:114;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£364.57\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:115;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£364.57\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:116;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£418.14\";i:4;s:8:\"£156.60\";i:5;s:7:\"£95.04\";}i:117;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£418.14\";i:4;s:8:\"£156.60\";i:5;s:7:\"£95.04\";}i:118;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£450.07\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:119;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£450.07\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£61,937.48\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:2064.58;s:14:\"weekly_revenue\";d:14452.08;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1785940361),('totalcashpro-cache-report_center:v3:1:249:62ef35c11592d74f6c42148a93d891cc','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-07\";s:2:\"to\";s:10:\"2026-08-05\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:58974.97999999999;s:8:\"expenses\";d:451.49;s:6:\"profit\";d:54420.23999999999;s:4:\"cash\";d:49956.37999999999;s:5:\"cards\";d:23022.4;s:13:\"online_orders\";d:9429.76;s:5:\"bills\";d:156.8;s:10:\"bills_paid\";d:420;s:7:\"payroll\";d:2978.25;s:12:\"payroll_paid\";d:992.75;s:15:\"payroll_pending\";d:1985.5;s:21:\"inventory_adjustments\";i:6;s:9:\"low_stock\";i:4;s:17:\"supplier_payments\";d:705;s:20:\"supplier_outstanding\";d:1080;s:16:\"attendance_hours\";d:46;s:10:\"vat_output\";d:2999.3;s:9:\"vat_input\";d:139.42000000000002;s:7:\"vat_due\";d:2859.88;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:1736.42;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1752.16;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2122.42;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2978.22;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1640.48;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1304.86;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:1003.36;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:118;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:120;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:88.5;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:89.99;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:35;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:1736.42;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1634.16;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2002.42;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2889.72;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1640.48;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1214.87;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:968.36;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:7;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:7;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:7;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:7;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:6;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:6;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:6;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-07\";s:5:\"label\";s:6:\"07 Jul\";s:5:\"value\";d:1736.42;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1634.16;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2002.42;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:24;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2889.72;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1640.48;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1214.87;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:968.36;}}s:14:\"supplier_spend\";a:3:{i:0;a:2:{s:8:\"supplier\";s:16:\"EcoPack Supplies\";s:5:\"total\";d:260;}i:1;a:2:{s:8:\"supplier\";s:19:\"London Dairy Direct\";s:5:\"total\";d:235;}i:2;a:2:{s:8:\"supplier\";s:13:\"Coastal Meats\";s:5:\"total\";d:210;}}s:18:\"expense_categories\";a:2:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:331.49;}i:1;a:2:{s:8:\"category\";s:9:\"marketing\";s:5:\"total\";d:120;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:25078.3;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:24878.08;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"22 Jul · £3,710.48\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:20:\"05 Aug · £1,003.36\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £331.49\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:28:\"EcoPack Supplies · £260.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £25,078.30\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:19:\"Jamie Cole · 46.5h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£13.02 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:120:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:2;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:3;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:4;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:5;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:6;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:7;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:8;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:9;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:10;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:11;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:12;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:13;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:14;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:15;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:16;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:17;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:18;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:19;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:20;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:21;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:22;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:23;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:24;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:25;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:26;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:27;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:28;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:29;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:30;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:31;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:32;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:33;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:34;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:35;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:36;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:37;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:38;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:39;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:40;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:41;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:42;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:43;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:44;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:45;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:46;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:47;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:48;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:49;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:50;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:51;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:52;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:53;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:54;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:55;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:56;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:57;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:58;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:59;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:60;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:61;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:62;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:63;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:64;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:65;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:66;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:67;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:68;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:69;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:70;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:71;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:72;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:73;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:74;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:75;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:76;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:77;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:78;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:79;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:80;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:81;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:82;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:83;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:84;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:85;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:86;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:87;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:88;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:89;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:90;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:91;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:92;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:93;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:94;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:95;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:96;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:97;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:98;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:99;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:100;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:101;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:102;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:103;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:104;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:105;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:106;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:107;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:108;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}i:109;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}i:110;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£387.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:111;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£387.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:112;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£336.90\";i:4;s:8:\"£120.00\";i:5;s:7:\"£76.00\";}i:113;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£336.90\";i:4;s:8:\"£120.00\";i:5;s:7:\"£76.00\";}i:114;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£364.57\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:115;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£364.57\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:116;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£418.14\";i:4;s:8:\"£156.60\";i:5;s:7:\"£95.04\";}i:117;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£418.14\";i:4;s:8:\"£156.60\";i:5;s:7:\"£95.04\";}i:118;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£450.07\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:119;a:6:{i:0;s:11:\"07 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£450.07\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£58,974.98\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:1965.83;s:14:\"weekly_revenue\";d:13760.83;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1785953972),('totalcashpro-cache-report_center:v3:1:249:8687babb7d37dfa64136fad942e6f16b','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-08\";s:2:\"to\";s:10:\"2026-08-06\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:57238.56;s:8:\"expenses\";d:451.49;s:6:\"profit\";d:52683.82;s:4:\"cash\";d:48219.96;s:5:\"cards\";d:22372.8;s:13:\"online_orders\";d:9035.52;s:5:\"bills\";d:156.8;s:10:\"bills_paid\";d:420;s:7:\"payroll\";d:2978.25;s:12:\"payroll_paid\";d:992.75;s:15:\"payroll_pending\";d:1985.5;s:21:\"inventory_adjustments\";i:6;s:9:\"low_stock\";i:4;s:17:\"supplier_payments\";d:705;s:20:\"supplier_outstanding\";d:1080;s:16:\"attendance_hours\";d:52;s:10:\"vat_output\";d:2999.3;s:9:\"vat_input\";d:139.42000000000002;s:7:\"vat_due\";d:2859.88;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1752.16;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2122.42;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2978.22;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1640.48;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1304.86;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:1003.36;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:118;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:120;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:88.5;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:89.99;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:35;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1634.16;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2002.42;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2889.72;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1640.48;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1214.87;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:968.36;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:7;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:7;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:7;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:7;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:6;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:6;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:6;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:6;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-08\";s:5:\"label\";s:6:\"08 Jul\";s:5:\"value\";d:3227.94;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1927.3;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:1902.36;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1519.84;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1511.9;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2040.32;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1678.06;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3354.88;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1316.96;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2205.8;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1807.3;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1855.7;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1496.64;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1619.68;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3710.48;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1580.46;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2300.18;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2174.6;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1786.72;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1634.16;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1363.22;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2002.42;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1514.12;}i:23;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:1940.96;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2601.64;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2889.72;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1640.48;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1214.87;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:968.36;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}}s:14:\"supplier_spend\";a:3:{i:0;a:2:{s:8:\"supplier\";s:16:\"EcoPack Supplies\";s:5:\"total\";d:260;}i:1;a:2:{s:8:\"supplier\";s:19:\"London Dairy Direct\";s:5:\"total\";d:235;}i:2;a:2:{s:8:\"supplier\";s:13:\"Coastal Meats\";s:5:\"total\";d:210;}}s:18:\"expense_categories\";a:2:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:331.49;}i:1;a:2:{s:8:\"category\";s:9:\"marketing\";s:5:\"total\";d:120;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:24178.16;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:24041.8;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"22 Jul · £3,710.48\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:20:\"05 Aug · £1,003.36\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £331.49\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:28:\"EcoPack Supplies · £260.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £24,178.16\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:20:\"Jamie Cole · 52.67h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£13.02 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:116:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:2;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:3;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:4;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:5;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:6;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:7;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:8;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:9;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:10;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:11;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:12;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:13;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:14;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:15;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:16;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:17;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:18;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:19;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:20;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:21;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:22;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:23;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:24;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:25;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:26;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:27;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:28;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:29;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:30;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:31;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:32;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:33;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:34;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:35;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:36;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:37;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:38;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:39;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:40;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:41;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:42;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:43;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:44;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:45;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:46;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:47;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:48;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:49;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:50;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:51;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:52;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:53;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:54;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:55;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:56;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:57;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:58;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:59;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:60;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:61;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:62;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:63;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:64;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:65;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:66;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:67;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:68;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:69;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:70;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:71;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:72;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:73;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:74;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:75;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:76;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:77;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:78;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:79;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:80;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:81;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:82;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:83;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:84;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:85;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:86;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:87;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:88;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:89;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:90;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:91;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:92;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:93;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:94;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:95;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:96;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:97;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:98;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:99;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:100;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:101;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:102;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:103;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:104;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:105;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:106;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:107;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:108;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}i:109;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}i:110;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£387.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:111;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£387.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:112;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£336.90\";i:4;s:8:\"£120.00\";i:5;s:7:\"£76.00\";}i:113;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£336.90\";i:4;s:8:\"£120.00\";i:5;s:7:\"£76.00\";}i:114;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£364.57\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:115;a:6:{i:0;s:11:\"08 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£364.57\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£57,238.56\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:1907.95;s:14:\"weekly_revenue\";d:13355.66;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1786017926),('totalcashpro-cache-report_center:v3:1:252:790ac035ef753c112c2e2b3582c0c163','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-09\";s:2:\"to\";s:10:\"2026-08-07\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:27761.6;s:8:\"expenses\";d:291.49;s:6:\"profit\";d:23601.859999999997;s:4:\"cash\";d:23368;s:5:\"cards\";d:10936.8;s:13:\"online_orders\";d:4349.12;s:5:\"bills\";d:156.8;s:10:\"bills_paid\";d:420;s:7:\"payroll\";d:2978.25;s:12:\"payroll_paid\";d:992.75;s:15:\"payroll_pending\";d:1985.5;s:21:\"inventory_adjustments\";i:4;s:9:\"low_stock\";i:2;s:17:\"supplier_payments\";d:470;s:20:\"supplier_outstanding\";d:720;s:16:\"attendance_hours\";d:51;s:10:\"vat_output\";d:1395.99;s:9:\"vat_input\";d:112.75;s:7:\"vat_due\";d:1283.24;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1152.1;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1357.2;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1916.64;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:628.48;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:482;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:120;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:46.5;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:89.99;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:35;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1152.1;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1237.2;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1870.14;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:538.49;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:447;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:7;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:7;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:7;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:6;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:6;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:6;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:6;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:6;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-09\";s:5:\"label\";s:6:\"09 Jul\";s:5:\"value\";d:1152.1;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1237.2;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:22;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1870.14;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:538.49;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:447;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}}s:14:\"supplier_spend\";a:2:{i:0;a:2:{s:8:\"supplier\";s:16:\"EcoPack Supplies\";s:5:\"total\";d:260;}i:1;a:2:{s:8:\"supplier\";s:13:\"Coastal Meats\";s:5:\"total\";d:210;}}s:18:\"expense_categories\";a:2:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:171.49;}i:1;a:2:{s:8:\"category\";s:9:\"marketing\";s:5:\"total\";d:120;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:23449.02;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:23368;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"02 Aug · £1,916.64\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:18:\"05 Aug · £482.00\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £171.49\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:28:\"EcoPack Supplies · £260.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £23,449.02\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:40:\"Staff · Harbour Kitchen Group · 51.83h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£12.32 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:56:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:2;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:3;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:4;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:5;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:6;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:7;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:8;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:9;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:10;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:11;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:12;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:13;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:14;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:15;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:16;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:17;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:18;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:19;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:20;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:21;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:22;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:23;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:24;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:25;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:26;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:27;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:28;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:29;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:30;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:31;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:32;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:33;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:34;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:35;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:36;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:37;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:38;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:39;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:40;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:41;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:42;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:43;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:44;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:45;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:46;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:47;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:48;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:49;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:50;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:51;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:52;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:53;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:54;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}i:55;a:6:{i:0;s:11:\"09 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£576.05\";i:4;s:8:\"£288.60\";i:5;s:7:\"£94.72\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£27,761.60\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:925.39;s:14:\"weekly_revenue\";d:6477.71;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1786095508),('totalcashpro-cache-report_center:v3:1:253:7bdea693f89288fc5fe5747504005684','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-10\";s:2:\"to\";s:10:\"2026-08-08\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:25473.82000000001;s:8:\"expenses\";d:160;s:6:\"profit\";d:25078.82000000001;s:4:\"cash\";d:22673.82000000001;s:5:\"cards\";d:10546.8;s:13:\"online_orders\";d:4242.24;s:5:\"bills\";d:0;s:10:\"bills_paid\";d:0;s:7:\"payroll\";d:0;s:12:\"payroll_paid\";d:0;s:15:\"payroll_pending\";d:0;s:21:\"inventory_adjustments\";i:2;s:9:\"low_stock\";i:2;s:17:\"supplier_payments\";d:235;s:20:\"supplier_outstanding\";d:360;s:16:\"attendance_hours\";d:0;s:10:\"vat_output\";d:994.99;s:9:\"vat_input\";d:26.67;s:7:\"vat_due\";d:968.32;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:978.32;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:782.8;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:779.88;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:1055.04;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:869.64;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1681.28;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:526;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1134.04;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:930.52;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:957.66;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:773.86;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:839.12;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1863.08;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:633.8;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:1546.9;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1120.04;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:921.84;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:905.62;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:706.88;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:765.22;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:608.6;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:998.16;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:987.76;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1061.58;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:848.44;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:676.38;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:521.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:118;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:42;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:978.32;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:782.8;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:779.88;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:1055.04;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:869.64;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1681.28;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:526;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1134.04;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:930.52;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:957.66;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:773.86;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:839.12;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1863.08;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:633.8;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:1546.9;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1120.04;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:921.84;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:787.62;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:706.88;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:765.22;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:608.6;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:998.16;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:987.76;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1019.58;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:848.44;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:676.38;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:521.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:978.32;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:782.8;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:779.88;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:1055.04;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:869.64;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1681.28;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:526;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1134.04;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:930.52;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:957.66;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:773.86;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:839.12;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1863.08;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:633.8;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:1546.9;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1120.04;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:921.84;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:787.62;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:706.88;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:765.22;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:608.6;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:998.16;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:987.76;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1019.58;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:848.44;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:676.38;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:521.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"supplier_spend\";a:1:{i:0;a:2:{s:8:\"supplier\";s:19:\"London Dairy Direct\";s:5:\"total\";d:235;}}s:18:\"expense_categories\";a:1:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:160;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:22673.82;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:22215.9;}}}s:8:\"insights\";a:6:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"22 Jul · £1,863.08\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:18:\"05 Aug · £521.36\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £160.00\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:31:\"London Dairy Direct · £235.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £22,673.82\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£13.02 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:54:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:2;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:3;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:4;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:5;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:6;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:7;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:8;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:9;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:10;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:11;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:12;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:13;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:14;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:15;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:16;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:17;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:18;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:19;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:20;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:21;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:22;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:23;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:24;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:25;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:26;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:27;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:28;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:29;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:30;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:31;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:32;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:33;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:34;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:35;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:36;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:37;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:38;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:39;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:40;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:41;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:42;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:43;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:44;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:45;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:46;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:47;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:48;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:49;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:50;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:51;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:52;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:53;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£25,473.82\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:849.13;s:14:\"weekly_revenue\";d:5943.89;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1786185884),('totalcashpro-cache-report_center:v3:1:258:5dd36c4f65c6d3c4f631e74f147fba0c','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-10\";s:2:\"to\";s:10:\"2026-08-08\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:26609.500000000007;s:8:\"expenses\";d:291.49;s:6:\"profit\";d:22449.760000000006;s:4:\"cash\";d:22215.90000000001;s:5:\"cards\";d:10359.6;s:13:\"online_orders\";d:4159.68;s:5:\"bills\";d:156.8;s:10:\"bills_paid\";d:420;s:7:\"payroll\";d:2978.25;s:12:\"payroll_paid\";d:992.75;s:15:\"payroll_pending\";d:1985.5;s:21:\"inventory_adjustments\";i:4;s:9:\"low_stock\";i:2;s:17:\"supplier_payments\";d:470;s:20:\"supplier_outstanding\";d:720;s:16:\"attendance_hours\";d:51;s:10:\"vat_output\";d:1395.99;s:9:\"vat_input\";d:112.75;s:7:\"vat_due\";d:1283.24;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1357.2;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1916.64;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:628.48;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:482;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:120;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:46.5;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:89.99;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:35;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1237.2;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1870.14;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:538.49;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:447;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:7;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:7;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:6;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:6;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:6;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:6;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:6;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1237.2;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1870.14;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:538.49;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:447;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"supplier_spend\";a:2:{i:0;a:2:{s:8:\"supplier\";s:16:\"EcoPack Supplies\";s:5:\"total\";d:260;}i:1;a:2:{s:8:\"supplier\";s:13:\"Coastal Meats\";s:5:\"total\";d:210;}}s:18:\"expense_categories\";a:2:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:171.49;}i:1;a:2:{s:8:\"category\";s:9:\"marketing\";s:5:\"total\";d:120;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:22673.82;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:22215.9;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"02 Aug · £1,916.64\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:18:\"05 Aug · £482.00\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £171.49\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:28:\"EcoPack Supplies · £260.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £22,673.82\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:40:\"Staff · Harbour Kitchen Group · 51.83h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£12.32 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:54:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:2;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:3;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:4;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:5;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:6;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:7;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:8;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:9;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:10;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:11;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:12;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:13;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:14;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:15;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:16;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:17;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:18;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:19;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:20;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:21;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:22;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:23;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:24;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:25;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:26;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:27;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:28;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:29;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:30;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:31;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:32;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:33;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:34;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:35;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:36;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:37;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:38;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:39;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:40;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:41;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:42;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:43;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:44;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:45;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:46;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:47;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:48;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:49;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:50;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:51;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:52;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:53;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£26,609.50\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:886.98;s:14:\"weekly_revenue\";d:6208.88;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1786187787),('totalcashpro-cache-report_center:v3:1:258:7bdea693f89288fc5fe5747504005684','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-10\";s:2:\"to\";s:10:\"2026-08-08\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:26609.500000000007;s:8:\"expenses\";d:291.49;s:6:\"profit\";d:22449.760000000006;s:4:\"cash\";d:22215.90000000001;s:5:\"cards\";d:10359.6;s:13:\"online_orders\";d:4159.68;s:5:\"bills\";d:156.8;s:10:\"bills_paid\";d:420;s:7:\"payroll\";d:2978.25;s:12:\"payroll_paid\";d:992.75;s:15:\"payroll_pending\";d:1985.5;s:21:\"inventory_adjustments\";i:4;s:9:\"low_stock\";i:2;s:17:\"supplier_payments\";d:470;s:20:\"supplier_outstanding\";d:720;s:16:\"attendance_hours\";d:51;s:10:\"vat_output\";d:1395.99;s:9:\"vat_input\";d:112.75;s:7:\"vat_due\";d:1283.24;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1357.2;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1916.64;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:628.48;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:482;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:120;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:46.5;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:89.99;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:35;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1237.2;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1870.14;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:538.49;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:447;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:7;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:7;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:6;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:6;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:6;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:6;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:6;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:924.04;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:737.04;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:732.02;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:985.28;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:808.42;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1673.6;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:790.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1071.76;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:876.78;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:898.04;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:722.78;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:780.56;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1847.4;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:946.66;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:753.28;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1054.56;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:864.88;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:846.54;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:656.34;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:1237.2;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:905.52;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:942.8;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:1613.88;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1870.14;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:792.04;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:538.49;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:447;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"supplier_spend\";a:2:{i:0;a:2:{s:8:\"supplier\";s:16:\"EcoPack Supplies\";s:5:\"total\";d:260;}i:1;a:2:{s:8:\"supplier\";s:13:\"Coastal Meats\";s:5:\"total\";d:210;}}s:18:\"expense_categories\";a:2:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:171.49;}i:1;a:2:{s:8:\"category\";s:9:\"marketing\";s:5:\"total\";d:120;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:22673.82;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:22215.9;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"02 Aug · £1,916.64\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:18:\"05 Aug · £482.00\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £171.49\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:28:\"EcoPack Supplies · £260.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £22,673.82\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:40:\"Staff · Harbour Kitchen Group · 51.83h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£12.32 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:54:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£241.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:2;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:3;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£314.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:4;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:5;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:6;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:7;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£498.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:8;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:9;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:10;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:11;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£471.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:12;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:13;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:14;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:15;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:16;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:17;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£328.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:18;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:19;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£423.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:20;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:21;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£432.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:22;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:23;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:24;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:25;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£376.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:26;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:27;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£473.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:28;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:29;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:30;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:31;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£390.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:32;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:33;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£361.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:34;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:35;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£449.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:36;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:37;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£438.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:38;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:39;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£535.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:40;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:41;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£395.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:42;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:43;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£324.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:44;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:45;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£404.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:46;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:47;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£492.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:48;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:49;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£366.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:50;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:51;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£368.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:52;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:53;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£26,609.50\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:886.98;s:14:\"weekly_revenue\";d:6208.88;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1786205449),('totalcashpro-cache-report_center:v3:1:258:a744d8a83a2bdeef488d985729c03712','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-10\";s:2:\"to\";s:10:\"2026-08-08\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:25473.82000000001;s:8:\"expenses\";d:160;s:6:\"profit\";d:25078.82000000001;s:4:\"cash\";d:22673.82000000001;s:5:\"cards\";d:10546.8;s:13:\"online_orders\";d:4242.24;s:5:\"bills\";d:0;s:10:\"bills_paid\";d:0;s:7:\"payroll\";d:0;s:12:\"payroll_paid\";d:0;s:15:\"payroll_pending\";d:0;s:21:\"inventory_adjustments\";i:2;s:9:\"low_stock\";i:2;s:17:\"supplier_payments\";d:235;s:20:\"supplier_outstanding\";d:360;s:16:\"attendance_hours\";d:0.01;s:10:\"vat_output\";d:994.99;s:9:\"vat_input\";d:26.67;s:7:\"vat_due\";d:968.32;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:978.32;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:782.8;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:779.88;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:1055.04;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:869.64;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1681.28;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:526;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1134.04;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:930.52;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:957.66;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:773.86;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:839.12;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1863.08;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:633.8;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:1546.9;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1120.04;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:921.84;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:905.62;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:706.88;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:765.22;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:608.6;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:998.16;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:987.76;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1061.58;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:848.44;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:676.38;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:521.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:118;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:42;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:978.32;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:782.8;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:779.88;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:1055.04;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:869.64;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1681.28;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:526;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1134.04;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:930.52;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:957.66;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:773.86;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:839.12;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1863.08;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:633.8;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:1546.9;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1120.04;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:921.84;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:787.62;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:706.88;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:765.22;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:608.6;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:998.16;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:987.76;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1019.58;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:848.44;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:676.38;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:521.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0.01;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:978.32;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:782.8;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:779.88;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:1055.04;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:869.64;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:1681.28;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:526;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:1134.04;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:930.52;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:957.66;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:773.86;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:839.12;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:1863.08;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:633.8;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:1546.9;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:1120.04;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:921.84;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:787.62;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:706.88;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:765.22;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:608.6;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:998.16;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:987.76;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:1019.58;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:848.44;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:676.38;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:521.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"supplier_spend\";a:1:{i:0;a:2:{s:8:\"supplier\";s:19:\"London Dairy Direct\";s:5:\"total\";d:235;}}s:18:\"expense_categories\";a:1:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:160;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:22673.82;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:22215.9;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"22 Jul · £1,863.08\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:18:\"05 Aug · £521.36\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £160.00\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:31:\"London Dairy Direct · £235.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £22,673.82\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:39:\"Staff · Harbour Kitchen Group · 0.01h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£13.02 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:54:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£260.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:2;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:3;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£338.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:4;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:5;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£424.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:6;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:7;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£530.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:8;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:9;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£493.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:10;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:11;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£499.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:12;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:13;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£304.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:14;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:15;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£382.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:16;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:17;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£353.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:18;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:19;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:20;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:21;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£460.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:22;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:23;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£560.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:24;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:25;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£398.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:26;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:27;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£316.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:28;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:29;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£396.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:30;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:31;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£419.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:32;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:33;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£386.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:34;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:35;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£478.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:36;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:37;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£465.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:38;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:39;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£567.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:40;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:41;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£263.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:42;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:43;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£350.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:44;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:45;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£434.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:46;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:47;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£527.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:48;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:49;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£389.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:50;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:51;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£391.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:52;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:53;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£489.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£25,473.82\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:849.13;s:14:\"weekly_revenue\";d:5943.89;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1786187792),('totalcashpro-cache-report_center:v3:1:259:7bdea693f89288fc5fe5747504005684','a:11:{s:5:\"range\";a:3:{s:4:\"from\";s:10:\"2026-07-10\";s:2:\"to\";s:10:\"2026-08-08\";s:5:\"label\";s:12:\"Last 30 days\";}s:4:\"kpis\";a:19:{s:7:\"revenue\";d:54027.32;s:8:\"expenses\";d:451.49;s:6:\"profit\";d:49472.58;s:4:\"cash\";d:46833.72;s:5:\"cards\";d:20906.4;s:13:\"online_orders\";d:8401.92;s:5:\"bills\";d:156.8;s:10:\"bills_paid\";d:420;s:7:\"payroll\";d:2978.25;s:12:\"payroll_paid\";d:992.75;s:15:\"payroll_pending\";d:1985.5;s:21:\"inventory_adjustments\";i:6;s:9:\"low_stock\";i:4;s:17:\"supplier_payments\";d:705;s:20:\"supplier_outstanding\";d:1080;s:16:\"attendance_hours\";d:51.01;s:10:\"vat_output\";d:2390.98;s:9:\"vat_input\";d:139.42000000000002;s:7:\"vat_due\";d:2251.56;}s:6:\"charts\";a:9:{s:13:\"revenue_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:2006.36;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1591.84;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1551.9;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2144.32;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1750.06;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3394.88;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1420.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2277.8;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1847.3;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1959.7;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1568.64;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1659.68;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3814.48;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1652.46;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2340.18;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2278.6;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1858.72;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1792.16;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1467.22;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2194.42;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1554.12;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:2044.96;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2673.64;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:3018.22;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1744.48;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1376.86;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:1043.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"expenses_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:118;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:120;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:88.5;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:89.99;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:35;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:12:\"profit_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:2006.36;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1591.84;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1551.9;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2144.32;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1750.06;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3394.88;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1420.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2277.8;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1847.3;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1959.7;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1568.64;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1659.68;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3814.48;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1652.46;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2340.18;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2278.6;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1858.72;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1674.16;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1467.22;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2074.42;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1554.12;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:2044.96;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2673.64;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2929.72;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1744.48;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1286.87;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:1008.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:16:\"attendance_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:7;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:7;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:7;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:6;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:6;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:6;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:6;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:6;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0.01;}}s:13:\"payroll_trend\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:0;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:0;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:0;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:0;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:0;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:0;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:0;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:0;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:0;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:0;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:0;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:0;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:0;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:0;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:0;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:0;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:0;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:0;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:0;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:0;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:0;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:0;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:0;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:0;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:0;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:0;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:0;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:9:\"cash_flow\";a:30:{i:0;a:3:{s:4:\"date\";s:10:\"2026-07-10\";s:5:\"label\";s:6:\"10 Jul\";s:5:\"value\";d:2006.36;}i:1;a:3:{s:4:\"date\";s:10:\"2026-07-11\";s:5:\"label\";s:6:\"11 Jul\";s:5:\"value\";d:1591.84;}i:2;a:3:{s:4:\"date\";s:10:\"2026-07-12\";s:5:\"label\";s:6:\"12 Jul\";s:5:\"value\";d:1551.9;}i:3;a:3:{s:4:\"date\";s:10:\"2026-07-13\";s:5:\"label\";s:6:\"13 Jul\";s:5:\"value\";d:2144.32;}i:4;a:3:{s:4:\"date\";s:10:\"2026-07-14\";s:5:\"label\";s:6:\"14 Jul\";s:5:\"value\";d:1750.06;}i:5;a:3:{s:4:\"date\";s:10:\"2026-07-15\";s:5:\"label\";s:6:\"15 Jul\";s:5:\"value\";d:3394.88;}i:6;a:3:{s:4:\"date\";s:10:\"2026-07-16\";s:5:\"label\";s:6:\"16 Jul\";s:5:\"value\";d:1420.96;}i:7;a:3:{s:4:\"date\";s:10:\"2026-07-17\";s:5:\"label\";s:6:\"17 Jul\";s:5:\"value\";d:2277.8;}i:8;a:3:{s:4:\"date\";s:10:\"2026-07-18\";s:5:\"label\";s:6:\"18 Jul\";s:5:\"value\";d:1847.3;}i:9;a:3:{s:4:\"date\";s:10:\"2026-07-19\";s:5:\"label\";s:6:\"19 Jul\";s:5:\"value\";d:1959.7;}i:10;a:3:{s:4:\"date\";s:10:\"2026-07-20\";s:5:\"label\";s:6:\"20 Jul\";s:5:\"value\";d:1568.64;}i:11;a:3:{s:4:\"date\";s:10:\"2026-07-21\";s:5:\"label\";s:6:\"21 Jul\";s:5:\"value\";d:1659.68;}i:12;a:3:{s:4:\"date\";s:10:\"2026-07-22\";s:5:\"label\";s:6:\"22 Jul\";s:5:\"value\";d:3814.48;}i:13;a:3:{s:4:\"date\";s:10:\"2026-07-23\";s:5:\"label\";s:6:\"23 Jul\";s:5:\"value\";d:1652.46;}i:14;a:3:{s:4:\"date\";s:10:\"2026-07-24\";s:5:\"label\";s:6:\"24 Jul\";s:5:\"value\";d:2340.18;}i:15;a:3:{s:4:\"date\";s:10:\"2026-07-25\";s:5:\"label\";s:6:\"25 Jul\";s:5:\"value\";d:2278.6;}i:16;a:3:{s:4:\"date\";s:10:\"2026-07-26\";s:5:\"label\";s:6:\"26 Jul\";s:5:\"value\";d:1858.72;}i:17;a:3:{s:4:\"date\";s:10:\"2026-07-27\";s:5:\"label\";s:6:\"27 Jul\";s:5:\"value\";d:1674.16;}i:18;a:3:{s:4:\"date\";s:10:\"2026-07-28\";s:5:\"label\";s:6:\"28 Jul\";s:5:\"value\";d:1467.22;}i:19;a:3:{s:4:\"date\";s:10:\"2026-07-29\";s:5:\"label\";s:6:\"29 Jul\";s:5:\"value\";d:2074.42;}i:20;a:3:{s:4:\"date\";s:10:\"2026-07-30\";s:5:\"label\";s:6:\"30 Jul\";s:5:\"value\";d:1554.12;}i:21;a:3:{s:4:\"date\";s:10:\"2026-07-31\";s:5:\"label\";s:6:\"31 Jul\";s:5:\"value\";d:2044.96;}i:22;a:3:{s:4:\"date\";s:10:\"2026-08-01\";s:5:\"label\";s:6:\"01 Aug\";s:5:\"value\";d:2673.64;}i:23;a:3:{s:4:\"date\";s:10:\"2026-08-02\";s:5:\"label\";s:6:\"02 Aug\";s:5:\"value\";d:2929.72;}i:24;a:3:{s:4:\"date\";s:10:\"2026-08-03\";s:5:\"label\";s:6:\"03 Aug\";s:5:\"value\";d:1744.48;}i:25;a:3:{s:4:\"date\";s:10:\"2026-08-04\";s:5:\"label\";s:6:\"04 Aug\";s:5:\"value\";d:1286.87;}i:26;a:3:{s:4:\"date\";s:10:\"2026-08-05\";s:5:\"label\";s:6:\"05 Aug\";s:5:\"value\";d:1008.36;}i:27;a:3:{s:4:\"date\";s:10:\"2026-08-06\";s:5:\"label\";s:6:\"06 Aug\";s:5:\"value\";d:0;}i:28;a:3:{s:4:\"date\";s:10:\"2026-08-07\";s:5:\"label\";s:6:\"07 Aug\";s:5:\"value\";d:0;}i:29;a:3:{s:4:\"date\";s:10:\"2026-08-08\";s:5:\"label\";s:6:\"08 Aug\";s:5:\"value\";d:0;}}s:14:\"supplier_spend\";a:3:{i:0;a:2:{s:8:\"supplier\";s:16:\"EcoPack Supplies\";s:5:\"total\";d:260;}i:1;a:2:{s:8:\"supplier\";s:19:\"London Dairy Direct\";s:5:\"total\";d:235;}i:2;a:2:{s:8:\"supplier\";s:13:\"Coastal Meats\";s:5:\"total\";d:210;}}s:18:\"expense_categories\";a:2:{i:0;a:2:{s:8:\"category\";s:8:\"supplies\";s:5:\"total\";d:331.49;}i:1;a:2:{s:8:\"category\";s:9:\"marketing\";s:5:\"total\";d:120;}}s:17:\"branch_comparison\";a:2:{i:0;a:2:{s:6:\"branch\";s:15:\"Harbour Central\";s:7:\"revenue\";d:23645.82;}i:1;a:2:{s:6:\"branch\";s:8:\"Dockside\";s:7:\"revenue\";d:23187.9;}}}s:8:\"insights\";a:7:{i:0;a:3:{s:5:\"title\";s:19:\"Highest revenue day\";s:5:\"value\";s:20:\"22 Jul · £3,814.48\";s:4:\"tone\";s:7:\"success\";}i:1;a:3:{s:5:\"title\";s:18:\"Lowest revenue day\";s:5:\"value\";s:20:\"05 Aug · £1,043.36\";s:4:\"tone\";s:7:\"warning\";}i:2;a:3:{s:5:\"title\";s:20:\"Top expense category\";s:5:\"value\";s:20:\"Supplies · £331.49\";s:4:\"tone\";s:4:\"info\";}i:3;a:3:{s:5:\"title\";s:19:\"Most spend supplier\";s:5:\"value\";s:28:\"EcoPack Supplies · £260.00\";s:4:\"tone\";s:4:\"info\";}i:4;a:3:{s:5:\"title\";s:22:\"Best performing branch\";s:5:\"value\";s:30:\"Harbour Central · £23,645.82\";s:4:\"tone\";s:7:\"success\";}i:5;a:3:{s:5:\"title\";s:20:\"Most active employee\";s:5:\"value\";s:40:\"Staff · Harbour Kitchen Group · 51.84h\";s:4:\"tone\";s:7:\"neutral\";}i:6;a:3:{s:5:\"title\";s:23:\"Largest cash difference\";s:5:\"value\";s:22:\"£13.02 · 17 Jul 2026\";s:4:\"tone\";s:6:\"danger\";}}s:10:\"comparison\";N;s:5:\"table\";a:2:{s:7:\"columns\";a:6:{i:0;s:4:\"Date\";i:1;s:6:\"Branch\";i:2;s:5:\"Shift\";i:3;s:11:\"Net revenue\";i:4;s:5:\"Cards\";i:5;s:6:\"Online\";}s:4:\"rows\";a:108:{i:0;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£251.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:1;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£251.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:2;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£270.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:3;a:6:{i:0;s:11:\"05 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£270.68\";i:4;s:8:\"£129.60\";i:5;s:7:\"£43.20\";}i:4;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£332.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:5;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£332.24\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:6;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£356.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:7;a:6:{i:0;s:11:\"04 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£356.19\";i:4;s:8:\"£168.20\";i:5;s:7:\"£60.32\";}i:8;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£422.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:9;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£422.02\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:10;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£450.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:11;a:6:{i:0;s:11:\"03 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£450.22\";i:4;s:8:\"£210.80\";i:5;s:7:\"£79.36\";}i:12;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£508.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:13;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£508.32\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:14;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£540.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:15;a:6:{i:0;s:11:\"02 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£540.79\";i:4;s:8:\"£257.40\";i:5;s:8:\"£100.32\";}i:16;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£483.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:17;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£483.14\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:18;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£511.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:19;a:6:{i:0;s:11:\"01 Aug 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£511.88\";i:4;s:8:\"£168.00\";i:5;s:8:\"£123.20\";}i:20;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£497.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:21;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£497.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:22;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£525.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:23;a:6:{i:0;s:11:\"31 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£525.08\";i:4;s:8:\"£214.60\";i:5;s:7:\"£59.20\";}i:24;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:25;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.76\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:26;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£314.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:27;a:6:{i:0;s:11:\"30 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£314.30\";i:4;s:8:\"£170.00\";i:5;s:7:\"£52.00\";}i:28;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£371.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:29;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£371.60\";i:4;s:8:\"£195.00\";i:5;s:7:\"£64.00\";}i:30;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£400.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:31;a:6:{i:0;s:11:\"29 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£400.61\";i:4;s:8:\"£210.60\";i:5;s:7:\"£69.12\";}i:32;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£354.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:33;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£354.17\";i:4;s:8:\"£129.60\";i:5;s:7:\"£82.08\";}i:34;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£379.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:35;a:6:{i:0;s:11:\"28 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£379.44\";i:4;s:8:\"£139.20\";i:5;s:7:\"£88.16\";}i:36;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£433.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:37;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£433.27\";i:4;s:8:\"£168.20\";i:5;s:8:\"£102.08\";}i:38;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£462.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:39;a:6:{i:0;s:11:\"27 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£462.81\";i:4;s:8:\"£179.80\";i:5;s:8:\"£109.12\";}i:40;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£450.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:41;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£450.44\";i:4;s:8:\"£210.80\";i:5;s:7:\"£49.60\";}i:42;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£478.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:43;a:6:{i:0;s:11:\"26 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£478.92\";i:4;s:8:\"£224.40\";i:5;s:7:\"£52.80\";}i:44;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£553.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:45;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£553.28\";i:4;s:8:\"£257.40\";i:5;s:7:\"£68.64\";}i:46;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£586.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:47;a:6:{i:0;s:11:\"25 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£586.02\";i:4;s:8:\"£273.00\";i:5;s:7:\"£72.80\";}i:48;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£386.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:49;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£386.64\";i:4;s:8:\"£168.00\";i:5;s:7:\"£89.60\";}i:50;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£408.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:51;a:6:{i:0;s:11:\"24 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£408.45\";i:4;s:8:\"£177.60\";i:5;s:7:\"£94.72\";}i:52;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£491.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:53;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£491.33\";i:4;s:8:\"£214.60\";i:5;s:8:\"£112.48\";}i:54;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£334.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:55;a:6:{i:0;s:11:\"23 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£334.90\";i:4;s:8:\"£145.00\";i:5;s:7:\"£76.00\";}i:56;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£392.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:57;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£392.20\";i:4;s:8:\"£170.00\";i:5;s:7:\"£88.00\";}i:58;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£422.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:59;a:6:{i:0;s:11:\"22 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£422.54\";i:4;s:8:\"£183.60\";i:5;s:7:\"£95.04\";}i:60;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£400.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:61;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£400.28\";i:4;s:8:\"£210.60\";i:5;s:7:\"£43.20\";}i:62;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£429.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:63;a:6:{i:0;s:11:\"21 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£429.56\";i:4;s:8:\"£226.20\";i:5;s:7:\"£46.40\";}i:64;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£379.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:65;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£379.39\";i:4;s:8:\"£139.20\";i:5;s:7:\"£60.32\";}i:66;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£404.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:67;a:6:{i:0;s:11:\"20 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£404.93\";i:4;s:8:\"£148.80\";i:5;s:7:\"£64.48\";}i:68;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£475.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:69;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£475.02\";i:4;s:8:\"£179.80\";i:5;s:7:\"£79.36\";}i:70;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£504.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:71;a:6:{i:0;s:11:\"19 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£504.83\";i:4;s:8:\"£191.40\";i:5;s:7:\"£84.48\";}i:72;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£448.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:73;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£448.39\";i:4;s:8:\"£224.40\";i:5;s:8:\"£100.32\";}i:74;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£475.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:75;a:6:{i:0;s:11:\"18 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£475.26\";i:4;s:8:\"£238.00\";i:5;s:8:\"£106.40\";}i:76;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£553.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:77;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£553.88\";i:4;s:8:\"£273.00\";i:5;s:8:\"£123.20\";}i:78;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£585.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:79;a:6:{i:0;s:11:\"17 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£585.02\";i:4;s:8:\"£288.60\";i:5;s:8:\"£130.24\";}i:80;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£421.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:81;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£421.48\";i:4;s:8:\"£177.60\";i:5;s:7:\"£59.20\";}i:82;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£289.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:83;a:6:{i:0;s:11:\"16 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£289.00\";i:4;s:8:\"£120.00\";i:5;s:7:\"£40.00\";}i:84;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£334.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:85;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£334.30\";i:4;s:8:\"£145.00\";i:5;s:7:\"£52.00\";}i:86;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£360.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:87;a:6:{i:0;s:11:\"15 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£360.64\";i:4;s:8:\"£156.60\";i:5;s:7:\"£56.16\";}i:88;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£422.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:89;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£422.21\";i:4;s:8:\"£183.60\";i:5;s:7:\"£69.12\";}i:90;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£452.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:91;a:6:{i:0;s:11:\"14 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£452.82\";i:4;s:8:\"£197.20\";i:5;s:7:\"£74.24\";}i:92;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£518.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:93;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£518.64\";i:4;s:8:\"£226.20\";i:5;s:7:\"£88.16\";}i:94;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£553.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:95;a:6:{i:0;s:11:\"13 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£553.52\";i:4;s:8:\"£241.80\";i:5;s:7:\"£94.24\";}i:96;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£376.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:97;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£376.01\";i:4;s:8:\"£148.80\";i:5;s:8:\"£109.12\";}i:98;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£399.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:99;a:6:{i:0;s:11:\"12 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£399.94\";i:4;s:8:\"£158.40\";i:5;s:8:\"£116.16\";}i:100;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£386.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:101;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£386.52\";i:4;s:8:\"£191.40\";i:5;s:7:\"£52.80\";}i:102;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£409.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:103;a:6:{i:0;s:11:\"11 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£409.40\";i:4;s:8:\"£203.00\";i:5;s:7:\"£56.00\";}i:104;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Morning\";i:3;s:8:\"£488.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:105;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:8:\"Dockside\";i:2;s:7:\"Evening\";i:3;s:8:\"£488.02\";i:4;s:8:\"£238.00\";i:5;s:7:\"£72.80\";}i:106;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Morning\";i:3;s:8:\"£515.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}i:107;a:6:{i:0;s:11:\"10 Jul 2026\";i:1;s:15:\"Harbour Central\";i:2;s:7:\"Evening\";i:3;s:8:\"£515.16\";i:4;s:8:\"£251.60\";i:5;s:7:\"£76.96\";}}}s:7:\"summary\";a:2:{s:8:\"headline\";s:13:\"Total revenue\";s:5:\"value\";s:11:\"£54,027.32\";}s:6:\"growth\";N;s:8:\"averages\";a:2:{s:13:\"daily_revenue\";d:1800.91;s:14:\"weekly_revenue\";d:12606.37;}s:8:\"is_empty\";b:0;s:13:\"empty_message\";s:82:\"No data for the selected filters. Try widening the date range or switching branch.\";}',1786209794);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_drawers`
--

LOCK TABLES `cash_drawers` WRITE;
/*!40000 ALTER TABLE `cash_drawers` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=361 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_ups`
--

LOCK TABLES `cash_ups` WRITE;
/*!40000 ALTER TABLE `cash_ups` DISABLE KEYS */;
INSERT INTO `cash_ups` VALUES (181,1,2,NULL,NULL,'2026-08-05','Morning',0.00,NULL,0.00,10.00,'[{\"qty\": 10, \"coin\": \"£1\"}]',80.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',120.00,'[{\"type\": \"machine\", \"amount\": 120, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',40.00,'[{\"amount\": 22, \"platform\": \"Uber Eats\"}, {\"amount\": 18, \"platform\": \"Deliveroo\"}]',4.00,'[{\"amount\": 2.2, \"platform\": \"Uber Eats\"}, {\"amount\": 1.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(182,1,2,NULL,NULL,'2026-08-05','Evening',0.00,NULL,0.00,10.00,'[{\"qty\": 10, \"coin\": \"£1\"}]',80.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',120.00,'[{\"type\": \"machine\", \"amount\": 120, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',40.00,'[{\"amount\": 22, \"platform\": \"Uber Eats\"}, {\"amount\": 18, \"platform\": \"Deliveroo\"}]',4.00,'[{\"amount\": 2.2, \"platform\": \"Uber Eats\"}, {\"amount\": 1.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(183,1,2,NULL,NULL,'2026-08-04','Morning',0.00,NULL,0.00,13.50,'[{\"qty\": 13, \"coin\": \"£1\"}]',102.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',156.60,'[{\"type\": \"machine\", \"amount\": 156.60000000000002, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',56.16,'[{\"amount\": 30.89, \"platform\": \"Uber Eats\"}, {\"amount\": 25.27, \"platform\": \"Deliveroo\"}]',5.62,'[{\"amount\": 3.09, \"platform\": \"Uber Eats\"}, {\"amount\": 2.53, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(184,1,2,NULL,NULL,'2026-08-04','Evening',0.00,NULL,0.00,13.50,'[{\"qty\": 13, \"coin\": \"£1\"}]',102.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',156.60,'[{\"type\": \"machine\", \"amount\": 156.60000000000002, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',56.16,'[{\"amount\": 30.89, \"platform\": \"Uber Eats\"}, {\"amount\": 25.27, \"platform\": \"Deliveroo\"}]',5.62,'[{\"amount\": 3.09, \"platform\": \"Uber Eats\"}, {\"amount\": 2.53, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(185,1,2,NULL,NULL,'2026-08-03','Morning',0.00,NULL,0.00,17.40,'[{\"qty\": 17, \"coin\": \"£1\"}]',127.60,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',197.20,'[{\"type\": \"machine\", \"amount\": 197.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',74.24,'[{\"amount\": 40.83, \"platform\": \"Uber Eats\"}, {\"amount\": 33.41, \"platform\": \"Deliveroo\"}]',7.42,'[{\"amount\": 4.08, \"platform\": \"Uber Eats\"}, {\"amount\": 3.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(186,1,2,NULL,NULL,'2026-08-03','Evening',0.00,NULL,0.00,17.40,'[{\"qty\": 17, \"coin\": \"£1\"}]',127.60,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',197.20,'[{\"type\": \"machine\", \"amount\": 197.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',74.24,'[{\"amount\": 40.83, \"platform\": \"Uber Eats\"}, {\"amount\": 33.41, \"platform\": \"Deliveroo\"}]',7.42,'[{\"amount\": 4.08, \"platform\": \"Uber Eats\"}, {\"amount\": 3.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(187,1,2,NULL,NULL,'2026-08-02','Morning',0.00,NULL,0.00,21.70,'[{\"qty\": 21, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',241.80,'[{\"type\": \"machine\", \"amount\": 241.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',94.24,'[{\"amount\": 51.83, \"platform\": \"Uber Eats\"}, {\"amount\": 42.41, \"platform\": \"Deliveroo\"}]',9.42,'[{\"amount\": 5.18, \"platform\": \"Uber Eats\"}, {\"amount\": 4.24, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(188,1,2,NULL,NULL,'2026-08-02','Evening',0.00,NULL,0.00,21.70,'[{\"qty\": 21, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',241.80,'[{\"type\": \"machine\", \"amount\": 241.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',94.24,'[{\"amount\": 51.83, \"platform\": \"Uber Eats\"}, {\"amount\": 42.41, \"platform\": \"Deliveroo\"}]',9.42,'[{\"amount\": 5.18, \"platform\": \"Uber Eats\"}, {\"amount\": 4.24, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(189,1,2,NULL,NULL,'2026-08-01','Morning',0.00,NULL,0.00,26.40,'[{\"qty\": 26, \"coin\": \"£1\"}]',184.80,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',158.40,'[{\"type\": \"machine\", \"amount\": 158.4, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',116.16,'[{\"amount\": 63.89, \"platform\": \"Uber Eats\"}, {\"amount\": 52.27, \"platform\": \"Deliveroo\"}]',11.62,'[{\"amount\": 6.39, \"platform\": \"Uber Eats\"}, {\"amount\": 5.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(190,1,2,NULL,NULL,'2026-08-01','Evening',0.00,NULL,0.00,26.40,'[{\"qty\": 26, \"coin\": \"£1\"}]',184.80,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',158.40,'[{\"type\": \"machine\", \"amount\": 158.4, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',116.16,'[{\"amount\": 63.89, \"platform\": \"Uber Eats\"}, {\"amount\": 52.27, \"platform\": \"Deliveroo\"}]',11.62,'[{\"amount\": 6.39, \"platform\": \"Uber Eats\"}, {\"amount\": 5.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(191,1,2,NULL,NULL,'2026-07-31','Morning',0.00,NULL,0.00,14.00,'[{\"qty\": 14, \"coin\": \"£1\"}]',217.00,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',203.00,'[{\"type\": \"machine\", \"amount\": 203, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',56.00,'[{\"amount\": 30.8, \"platform\": \"Uber Eats\"}, {\"amount\": 25.2, \"platform\": \"Deliveroo\"}]',5.60,'[{\"amount\": 3.08, \"platform\": \"Uber Eats\"}, {\"amount\": 2.52, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(192,1,2,NULL,NULL,'2026-07-31','Evening',0.00,NULL,0.00,14.00,'[{\"qty\": 14, \"coin\": \"£1\"}]',217.00,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',203.00,'[{\"type\": \"machine\", \"amount\": 203, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',56.00,'[{\"amount\": 30.8, \"platform\": \"Uber Eats\"}, {\"amount\": 25.2, \"platform\": \"Deliveroo\"}]',5.60,'[{\"amount\": 3.08, \"platform\": \"Uber Eats\"}, {\"amount\": 2.52, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(193,1,2,NULL,NULL,'2026-07-30','Morning',0.00,NULL,0.00,18.50,'[{\"qty\": 18, \"coin\": \"£1\"}]',118.40,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',251.60,'[{\"type\": \"machine\", \"amount\": 251.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',76.96,'[{\"amount\": 42.33, \"platform\": \"Uber Eats\"}, {\"amount\": 34.63, \"platform\": \"Deliveroo\"}]',7.70,'[{\"amount\": 4.24, \"platform\": \"Uber Eats\"}, {\"amount\": 3.47, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(194,1,2,NULL,NULL,'2026-07-30','Evening',0.00,NULL,0.00,18.50,'[{\"qty\": 18, \"coin\": \"£1\"}]',118.40,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',251.60,'[{\"type\": \"machine\", \"amount\": 251.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',76.96,'[{\"amount\": 42.33, \"platform\": \"Uber Eats\"}, {\"amount\": 34.63, \"platform\": \"Deliveroo\"}]',7.70,'[{\"amount\": 4.24, \"platform\": \"Uber Eats\"}, {\"amount\": 3.47, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(195,1,2,NULL,NULL,'2026-07-29','Morning',0.00,NULL,0.00,15.00,'[{\"qty\": 15, \"coin\": \"£1\"}]',95.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',195.00,'[{\"type\": \"machine\", \"amount\": 195, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',64.00,'[{\"amount\": 35.2, \"platform\": \"Uber Eats\"}, {\"amount\": 28.8, \"platform\": \"Deliveroo\"}]',6.40,'[{\"amount\": 3.52, \"platform\": \"Uber Eats\"}, {\"amount\": 2.88, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(196,1,2,NULL,NULL,'2026-07-29','Evening',0.00,NULL,0.00,15.00,'[{\"qty\": 15, \"coin\": \"£1\"}]',95.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',195.00,'[{\"type\": \"machine\", \"amount\": 195, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',64.00,'[{\"amount\": 35.2, \"platform\": \"Uber Eats\"}, {\"amount\": 28.8, \"platform\": \"Deliveroo\"}]',6.40,'[{\"amount\": 3.52, \"platform\": \"Uber Eats\"}, {\"amount\": 2.88, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(197,1,2,NULL,NULL,'2026-07-28','Morning',0.00,NULL,0.00,18.90,'[{\"qty\": 18, \"coin\": \"£1\"}]',118.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',82.08,'[{\"amount\": 45.14, \"platform\": \"Uber Eats\"}, {\"amount\": 36.94, \"platform\": \"Deliveroo\"}]',8.21,'[{\"amount\": 4.52, \"platform\": \"Uber Eats\"}, {\"amount\": 3.69, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(198,1,2,NULL,NULL,'2026-07-28','Evening',0.00,NULL,0.00,18.90,'[{\"qty\": 18, \"coin\": \"£1\"}]',118.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',82.08,'[{\"amount\": 45.14, \"platform\": \"Uber Eats\"}, {\"amount\": 36.94, \"platform\": \"Deliveroo\"}]',8.21,'[{\"amount\": 4.52, \"platform\": \"Uber Eats\"}, {\"amount\": 3.69, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(199,1,2,NULL,NULL,'2026-07-27','Morning',0.00,NULL,0.00,23.20,'[{\"qty\": 23, \"coin\": \"£1\"}]',145.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',102.08,'[{\"amount\": 56.14, \"platform\": \"Uber Eats\"}, {\"amount\": 45.94, \"platform\": \"Deliveroo\"}]',10.21,'[{\"amount\": 5.62, \"platform\": \"Uber Eats\"}, {\"amount\": 4.59, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(200,1,2,NULL,NULL,'2026-07-27','Evening',0.00,NULL,0.00,23.20,'[{\"qty\": 23, \"coin\": \"£1\"}]',145.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',102.08,'[{\"amount\": 56.14, \"platform\": \"Uber Eats\"}, {\"amount\": 45.94, \"platform\": \"Deliveroo\"}]',10.21,'[{\"amount\": 5.62, \"platform\": \"Uber Eats\"}, {\"amount\": 4.59, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(201,1,2,NULL,NULL,'2026-07-26','Morning',0.00,NULL,0.00,12.40,'[{\"qty\": 12, \"coin\": \"£1\"}]',173.60,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',49.60,'[{\"amount\": 27.28, \"platform\": \"Uber Eats\"}, {\"amount\": 22.32, \"platform\": \"Deliveroo\"}]',4.96,'[{\"amount\": 2.73, \"platform\": \"Uber Eats\"}, {\"amount\": 2.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(202,1,2,NULL,NULL,'2026-07-26','Evening',0.00,NULL,0.00,12.40,'[{\"qty\": 12, \"coin\": \"£1\"}]',173.60,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',49.60,'[{\"amount\": 27.28, \"platform\": \"Uber Eats\"}, {\"amount\": 22.32, \"platform\": \"Deliveroo\"}]',4.96,'[{\"amount\": 2.73, \"platform\": \"Uber Eats\"}, {\"amount\": 2.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(203,1,2,NULL,NULL,'2026-07-25','Morning',0.00,NULL,0.00,16.50,'[{\"qty\": 16, \"coin\": \"£1\"}]',204.60,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',68.64,'[{\"amount\": 37.75, \"platform\": \"Uber Eats\"}, {\"amount\": 30.89, \"platform\": \"Deliveroo\"}]',6.86,'[{\"amount\": 3.77, \"platform\": \"Uber Eats\"}, {\"amount\": 3.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(204,1,2,NULL,NULL,'2026-07-25','Evening',0.00,NULL,0.00,16.50,'[{\"qty\": 16, \"coin\": \"£1\"}]',204.60,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',68.64,'[{\"amount\": 37.75, \"platform\": \"Uber Eats\"}, {\"amount\": 30.89, \"platform\": \"Deliveroo\"}]',6.86,'[{\"amount\": 3.77, \"platform\": \"Uber Eats\"}, {\"amount\": 3.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(205,1,2,NULL,NULL,'2026-07-24','Morning',0.00,NULL,0.00,21.00,'[{\"qty\": 21, \"coin\": \"£1\"}]',112.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',89.60,'[{\"amount\": 49.28, \"platform\": \"Uber Eats\"}, {\"amount\": 40.32, \"platform\": \"Deliveroo\"}]',8.96,'[{\"amount\": 4.93, \"platform\": \"Uber Eats\"}, {\"amount\": 4.03, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(206,1,2,NULL,NULL,'2026-07-24','Evening',0.00,NULL,0.00,21.00,'[{\"qty\": 21, \"coin\": \"£1\"}]',112.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',89.60,'[{\"amount\": 49.28, \"platform\": \"Uber Eats\"}, {\"amount\": 40.32, \"platform\": \"Deliveroo\"}]',8.96,'[{\"amount\": 4.93, \"platform\": \"Uber Eats\"}, {\"amount\": 4.03, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(207,1,2,NULL,NULL,'2026-07-23','Morning',0.00,NULL,0.00,25.90,'[{\"qty\": 25, \"coin\": \"£1\"}]',140.60,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',112.48,'[{\"amount\": 61.86, \"platform\": \"Uber Eats\"}, {\"amount\": 50.62, \"platform\": \"Deliveroo\"}]',11.25,'[{\"amount\": 6.19, \"platform\": \"Uber Eats\"}, {\"amount\": 5.06, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(208,1,2,NULL,NULL,'2026-07-23','Evening',0.00,NULL,0.00,25.90,'[{\"qty\": 25, \"coin\": \"£1\"}]',140.60,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',112.48,'[{\"amount\": 61.86, \"platform\": \"Uber Eats\"}, {\"amount\": 50.62, \"platform\": \"Deliveroo\"}]',11.25,'[{\"amount\": 6.19, \"platform\": \"Uber Eats\"}, {\"amount\": 5.06, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(209,1,2,NULL,NULL,'2026-07-22','Morning',0.00,NULL,0.00,20.00,'[{\"qty\": 20, \"coin\": \"£1\"}]',110.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',88.00,'[{\"amount\": 48.4, \"platform\": \"Uber Eats\"}, {\"amount\": 39.6, \"platform\": \"Deliveroo\"}]',8.80,'[{\"amount\": 4.84, \"platform\": \"Uber Eats\"}, {\"amount\": 3.96, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(210,1,2,NULL,NULL,'2026-07-22','Evening',0.00,NULL,0.00,20.00,'[{\"qty\": 20, \"coin\": \"£1\"}]',110.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',88.00,'[{\"amount\": 48.4, \"platform\": \"Uber Eats\"}, {\"amount\": 39.6, \"platform\": \"Deliveroo\"}]',8.80,'[{\"amount\": 4.84, \"platform\": \"Uber Eats\"}, {\"amount\": 3.96, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(211,1,2,NULL,NULL,'2026-07-21','Morning',0.00,NULL,0.00,10.80,'[{\"qty\": 10, \"coin\": \"£1\"}]',135.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',43.20,'[{\"amount\": 23.76, \"platform\": \"Uber Eats\"}, {\"amount\": 19.44, \"platform\": \"Deliveroo\"}]',4.32,'[{\"amount\": 2.38, \"platform\": \"Uber Eats\"}, {\"amount\": 1.94, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(212,1,2,NULL,NULL,'2026-07-21','Evening',0.00,NULL,0.00,10.80,'[{\"qty\": 10, \"coin\": \"£1\"}]',135.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',43.20,'[{\"amount\": 23.76, \"platform\": \"Uber Eats\"}, {\"amount\": 19.44, \"platform\": \"Deliveroo\"}]',4.32,'[{\"amount\": 2.38, \"platform\": \"Uber Eats\"}, {\"amount\": 1.94, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(213,1,2,NULL,NULL,'2026-07-20','Morning',0.00,NULL,0.00,14.50,'[{\"qty\": 14, \"coin\": \"£1\"}]',162.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',60.32,'[{\"amount\": 33.18, \"platform\": \"Uber Eats\"}, {\"amount\": 27.14, \"platform\": \"Deliveroo\"}]',6.03,'[{\"amount\": 3.32, \"platform\": \"Uber Eats\"}, {\"amount\": 2.71, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(214,1,2,NULL,NULL,'2026-07-20','Evening',0.00,NULL,0.00,14.50,'[{\"qty\": 14, \"coin\": \"£1\"}]',162.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',60.32,'[{\"amount\": 33.18, \"platform\": \"Uber Eats\"}, {\"amount\": 27.14, \"platform\": \"Deliveroo\"}]',6.03,'[{\"amount\": 3.32, \"platform\": \"Uber Eats\"}, {\"amount\": 2.71, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(215,1,2,NULL,NULL,'2026-07-19','Morning',0.00,NULL,0.00,18.60,'[{\"qty\": 18, \"coin\": \"£1\"}]',192.20,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',179.80,'[{\"type\": \"machine\", \"amount\": 179.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',79.36,'[{\"amount\": 43.65, \"platform\": \"Uber Eats\"}, {\"amount\": 35.71, \"platform\": \"Deliveroo\"}]',7.94,'[{\"amount\": 4.37, \"platform\": \"Uber Eats\"}, {\"amount\": 3.57, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(216,1,2,NULL,NULL,'2026-07-19','Evening',0.00,NULL,0.00,18.60,'[{\"qty\": 18, \"coin\": \"£1\"}]',192.20,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',179.80,'[{\"type\": \"machine\", \"amount\": 179.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',79.36,'[{\"amount\": 43.65, \"platform\": \"Uber Eats\"}, {\"amount\": 35.71, \"platform\": \"Deliveroo\"}]',7.94,'[{\"amount\": 4.37, \"platform\": \"Uber Eats\"}, {\"amount\": 3.57, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(217,1,2,NULL,NULL,'2026-07-18','Morning',0.00,NULL,0.00,23.10,'[{\"qty\": 23, \"coin\": \"£1\"}]',105.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',224.40,'[{\"type\": \"machine\", \"amount\": 224.4, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',100.32,'[{\"amount\": 55.18, \"platform\": \"Uber Eats\"}, {\"amount\": 45.14, \"platform\": \"Deliveroo\"}]',10.03,'[{\"amount\": 5.52, \"platform\": \"Uber Eats\"}, {\"amount\": 4.51, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(218,1,2,NULL,NULL,'2026-07-18','Evening',0.00,NULL,0.00,23.10,'[{\"qty\": 23, \"coin\": \"£1\"}]',105.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',224.40,'[{\"type\": \"machine\", \"amount\": 224.4, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',100.32,'[{\"amount\": 55.18, \"platform\": \"Uber Eats\"}, {\"amount\": 45.14, \"platform\": \"Deliveroo\"}]',10.03,'[{\"amount\": 5.52, \"platform\": \"Uber Eats\"}, {\"amount\": 4.51, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(219,1,2,NULL,NULL,'2026-07-17','Morning',0.00,NULL,0.00,28.00,'[{\"qty\": 28, \"coin\": \"£1\"}]',133.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',273.00,'[{\"type\": \"machine\", \"amount\": 273, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',123.20,'[{\"amount\": 67.76, \"platform\": \"Uber Eats\"}, {\"amount\": 55.44, \"platform\": \"Deliveroo\"}]',12.32,'[{\"amount\": 6.78, \"platform\": \"Uber Eats\"}, {\"amount\": 5.54, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(220,1,2,NULL,NULL,'2026-07-17','Evening',0.00,NULL,0.00,28.00,'[{\"qty\": 28, \"coin\": \"£1\"}]',133.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',273.00,'[{\"type\": \"machine\", \"amount\": 273, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',123.20,'[{\"amount\": 67.76, \"platform\": \"Uber Eats\"}, {\"amount\": 55.44, \"platform\": \"Deliveroo\"}]',12.32,'[{\"amount\": 6.78, \"platform\": \"Uber Eats\"}, {\"amount\": 5.54, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(221,1,2,NULL,NULL,'2026-07-16','Morning',0.00,NULL,0.00,14.80,'[{\"qty\": 14, \"coin\": \"£1\"}]',162.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',177.60,'[{\"type\": \"machine\", \"amount\": 177.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',59.20,'[{\"amount\": 32.56, \"platform\": \"Uber Eats\"}, {\"amount\": 26.64, \"platform\": \"Deliveroo\"}]',5.92,'[{\"amount\": 3.26, \"platform\": \"Uber Eats\"}, {\"amount\": 2.66, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(222,1,2,NULL,NULL,'2026-07-16','Evening',0.00,NULL,0.00,14.80,'[{\"qty\": 14, \"coin\": \"£1\"}]',162.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',177.60,'[{\"type\": \"machine\", \"amount\": 177.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',59.20,'[{\"amount\": 32.56, \"platform\": \"Uber Eats\"}, {\"amount\": 26.64, \"platform\": \"Deliveroo\"}]',5.92,'[{\"amount\": 3.26, \"platform\": \"Uber Eats\"}, {\"amount\": 2.66, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(223,1,2,NULL,NULL,'2026-07-15','Morning',0.00,NULL,0.00,12.50,'[{\"qty\": 12, \"coin\": \"£1\"}]',125.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',145.00,'[{\"type\": \"machine\", \"amount\": 145, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',52.00,'[{\"amount\": 28.6, \"platform\": \"Uber Eats\"}, {\"amount\": 23.4, \"platform\": \"Deliveroo\"}]',5.20,'[{\"amount\": 2.86, \"platform\": \"Uber Eats\"}, {\"amount\": 2.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(224,1,2,NULL,NULL,'2026-07-15','Evening',0.00,NULL,0.00,12.50,'[{\"qty\": 12, \"coin\": \"£1\"}]',125.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',145.00,'[{\"type\": \"machine\", \"amount\": 145, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',52.00,'[{\"amount\": 28.6, \"platform\": \"Uber Eats\"}, {\"amount\": 23.4, \"platform\": \"Deliveroo\"}]',5.20,'[{\"amount\": 2.86, \"platform\": \"Uber Eats\"}, {\"amount\": 2.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(225,1,2,NULL,NULL,'2026-07-14','Morning',0.00,NULL,0.00,16.20,'[{\"qty\": 16, \"coin\": \"£1\"}]',151.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',183.60,'[{\"type\": \"machine\", \"amount\": 183.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',69.12,'[{\"amount\": 38.02, \"platform\": \"Uber Eats\"}, {\"amount\": 31.1, \"platform\": \"Deliveroo\"}]',6.91,'[{\"amount\": 3.8, \"platform\": \"Uber Eats\"}, {\"amount\": 3.11, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(226,1,2,NULL,NULL,'2026-07-14','Evening',0.00,NULL,0.00,16.20,'[{\"qty\": 16, \"coin\": \"£1\"}]',151.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',183.60,'[{\"type\": \"machine\", \"amount\": 183.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',69.12,'[{\"amount\": 38.02, \"platform\": \"Uber Eats\"}, {\"amount\": 31.1, \"platform\": \"Deliveroo\"}]',6.91,'[{\"amount\": 3.8, \"platform\": \"Uber Eats\"}, {\"amount\": 3.11, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(227,1,2,NULL,NULL,'2026-07-13','Morning',0.00,NULL,0.00,20.30,'[{\"qty\": 20, \"coin\": \"£1\"}]',179.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',226.20,'[{\"type\": \"machine\", \"amount\": 226.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',88.16,'[{\"amount\": 48.49, \"platform\": \"Uber Eats\"}, {\"amount\": 39.67, \"platform\": \"Deliveroo\"}]',8.82,'[{\"amount\": 4.85, \"platform\": \"Uber Eats\"}, {\"amount\": 3.97, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(228,1,2,NULL,NULL,'2026-07-13','Evening',0.00,NULL,0.00,20.30,'[{\"qty\": 20, \"coin\": \"£1\"}]',179.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',226.20,'[{\"type\": \"machine\", \"amount\": 226.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',88.16,'[{\"amount\": 48.49, \"platform\": \"Uber Eats\"}, {\"amount\": 39.67, \"platform\": \"Deliveroo\"}]',8.82,'[{\"amount\": 4.85, \"platform\": \"Uber Eats\"}, {\"amount\": 3.97, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(229,1,2,NULL,NULL,'2026-07-12','Morning',0.00,NULL,0.00,24.80,'[{\"qty\": 24, \"coin\": \"£1\"}]',99.20,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',148.80,'[{\"type\": \"machine\", \"amount\": 148.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',109.12,'[{\"amount\": 60.02, \"platform\": \"Uber Eats\"}, {\"amount\": 49.1, \"platform\": \"Deliveroo\"}]',10.91,'[{\"amount\": 6, \"platform\": \"Uber Eats\"}, {\"amount\": 4.91, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(230,1,2,NULL,NULL,'2026-07-12','Evening',0.00,NULL,0.00,24.80,'[{\"qty\": 24, \"coin\": \"£1\"}]',99.20,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',148.80,'[{\"type\": \"machine\", \"amount\": 148.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',109.12,'[{\"amount\": 60.02, \"platform\": \"Uber Eats\"}, {\"amount\": 49.1, \"platform\": \"Deliveroo\"}]',10.91,'[{\"amount\": 6, \"platform\": \"Uber Eats\"}, {\"amount\": 4.91, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(231,1,2,NULL,NULL,'2026-07-11','Morning',0.00,NULL,0.00,13.20,'[{\"qty\": 13, \"coin\": \"£1\"}]',125.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',191.40,'[{\"type\": \"machine\", \"amount\": 191.4, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',52.80,'[{\"amount\": 29.04, \"platform\": \"Uber Eats\"}, {\"amount\": 23.76, \"platform\": \"Deliveroo\"}]',5.28,'[{\"amount\": 2.9, \"platform\": \"Uber Eats\"}, {\"amount\": 2.38, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(232,1,2,NULL,NULL,'2026-07-11','Evening',0.00,NULL,0.00,13.20,'[{\"qty\": 13, \"coin\": \"£1\"}]',125.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',191.40,'[{\"type\": \"machine\", \"amount\": 191.4, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',52.80,'[{\"amount\": 29.04, \"platform\": \"Uber Eats\"}, {\"amount\": 23.76, \"platform\": \"Deliveroo\"}]',5.28,'[{\"amount\": 2.9, \"platform\": \"Uber Eats\"}, {\"amount\": 2.38, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(233,1,2,NULL,NULL,'2026-07-10','Morning',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',154.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',238.00,'[{\"type\": \"machine\", \"amount\": 237.99999999999997, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',72.80,'[{\"amount\": 40.04, \"platform\": \"Uber Eats\"}, {\"amount\": 32.76, \"platform\": \"Deliveroo\"}]',7.28,'[{\"amount\": 4, \"platform\": \"Uber Eats\"}, {\"amount\": 3.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(234,1,2,NULL,NULL,'2026-07-10','Evening',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',154.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',238.00,'[{\"type\": \"machine\", \"amount\": 237.99999999999997, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',72.80,'[{\"amount\": 40.04, \"platform\": \"Uber Eats\"}, {\"amount\": 32.76, \"platform\": \"Deliveroo\"}]',7.28,'[{\"amount\": 4, \"platform\": \"Uber Eats\"}, {\"amount\": 3.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(235,1,2,NULL,NULL,'2026-07-09','Morning',0.00,NULL,0.00,22.20,'[{\"qty\": 22, \"coin\": \"£1\"}]',185.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',288.60,'[{\"type\": \"machine\", \"amount\": 288.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',94.72,'[{\"amount\": 52.1, \"platform\": \"Uber Eats\"}, {\"amount\": 42.62, \"platform\": \"Deliveroo\"}]',9.47,'[{\"amount\": 5.21, \"platform\": \"Uber Eats\"}, {\"amount\": 4.26, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(236,1,2,NULL,NULL,'2026-07-09','Evening',0.00,NULL,0.00,22.20,'[{\"qty\": 22, \"coin\": \"£1\"}]',185.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',288.60,'[{\"type\": \"machine\", \"amount\": 288.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',94.72,'[{\"amount\": 52.1, \"platform\": \"Uber Eats\"}, {\"amount\": 42.62, \"platform\": \"Deliveroo\"}]',9.47,'[{\"amount\": 5.21, \"platform\": \"Uber Eats\"}, {\"amount\": 4.26, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(237,1,2,NULL,NULL,'2026-07-08','Morning',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',140.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',120.00,'[{\"type\": \"machine\", \"amount\": 120, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',76.00,'[{\"amount\": 41.8, \"platform\": \"Uber Eats\"}, {\"amount\": 34.2, \"platform\": \"Deliveroo\"}]',7.60,'[{\"amount\": 4.18, \"platform\": \"Uber Eats\"}, {\"amount\": 3.42, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(238,1,2,NULL,NULL,'2026-07-08','Evening',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',140.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',120.00,'[{\"type\": \"machine\", \"amount\": 120, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',76.00,'[{\"amount\": 41.8, \"platform\": \"Uber Eats\"}, {\"amount\": 34.2, \"platform\": \"Deliveroo\"}]',7.60,'[{\"amount\": 4.18, \"platform\": \"Uber Eats\"}, {\"amount\": 3.42, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(239,1,2,NULL,NULL,'2026-07-07','Morning',0.00,NULL,0.00,21.60,'[{\"qty\": 21, \"coin\": \"£1\"}]',167.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',156.60,'[{\"type\": \"machine\", \"amount\": 156.60000000000002, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',95.04,'[{\"amount\": 52.27, \"platform\": \"Uber Eats\"}, {\"amount\": 42.77, \"platform\": \"Deliveroo\"}]',9.50,'[{\"amount\": 5.23, \"platform\": \"Uber Eats\"}, {\"amount\": 4.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(240,1,2,NULL,NULL,'2026-07-07','Evening',0.00,NULL,0.00,21.60,'[{\"qty\": 21, \"coin\": \"£1\"}]',167.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',156.60,'[{\"type\": \"machine\", \"amount\": 156.60000000000002, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',95.04,'[{\"amount\": 52.27, \"platform\": \"Uber Eats\"}, {\"amount\": 42.77, \"platform\": \"Deliveroo\"}]',9.50,'[{\"amount\": 5.23, \"platform\": \"Uber Eats\"}, {\"amount\": 4.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(241,1,2,NULL,NULL,'2026-07-06','Morning',0.00,NULL,0.00,11.60,'[{\"qty\": 11, \"coin\": \"£1\"}]',92.80,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',197.20,'[{\"type\": \"machine\", \"amount\": 197.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',46.40,'[{\"amount\": 25.52, \"platform\": \"Uber Eats\"}, {\"amount\": 20.88, \"platform\": \"Deliveroo\"}]',4.64,'[{\"amount\": 2.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(242,1,2,NULL,NULL,'2026-07-06','Evening',0.00,NULL,0.00,11.60,'[{\"qty\": 11, \"coin\": \"£1\"}]',92.80,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',197.20,'[{\"type\": \"machine\", \"amount\": 197.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',46.40,'[{\"amount\": 25.52, \"platform\": \"Uber Eats\"}, {\"amount\": 20.88, \"platform\": \"Deliveroo\"}]',4.64,'[{\"amount\": 2.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(243,1,2,NULL,NULL,'2026-07-05','Morning',0.00,NULL,0.00,15.50,'[{\"qty\": 15, \"coin\": \"£1\"}]',117.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',241.80,'[{\"type\": \"machine\", \"amount\": 241.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',64.48,'[{\"amount\": 35.46, \"platform\": \"Uber Eats\"}, {\"amount\": 29.02, \"platform\": \"Deliveroo\"}]',6.45,'[{\"amount\": 3.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.9, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(244,1,2,NULL,NULL,'2026-07-05','Evening',0.00,NULL,0.00,15.50,'[{\"qty\": 15, \"coin\": \"£1\"}]',117.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',241.80,'[{\"type\": \"machine\", \"amount\": 241.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',64.48,'[{\"amount\": 35.46, \"platform\": \"Uber Eats\"}, {\"amount\": 29.02, \"platform\": \"Deliveroo\"}]',6.45,'[{\"amount\": 3.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.9, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(245,1,2,NULL,NULL,'2026-07-04','Morning',0.00,NULL,0.00,19.80,'[{\"qty\": 19, \"coin\": \"£1\"}]',145.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',158.40,'[{\"type\": \"machine\", \"amount\": 158.4, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',84.48,'[{\"amount\": 46.46, \"platform\": \"Uber Eats\"}, {\"amount\": 38.02, \"platform\": \"Deliveroo\"}]',8.45,'[{\"amount\": 4.65, \"platform\": \"Uber Eats\"}, {\"amount\": 3.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(246,1,2,NULL,NULL,'2026-07-04','Evening',0.00,NULL,0.00,19.80,'[{\"qty\": 19, \"coin\": \"£1\"}]',145.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',158.40,'[{\"type\": \"machine\", \"amount\": 158.4, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',84.48,'[{\"amount\": 46.46, \"platform\": \"Uber Eats\"}, {\"amount\": 38.02, \"platform\": \"Deliveroo\"}]',8.45,'[{\"amount\": 4.65, \"platform\": \"Uber Eats\"}, {\"amount\": 3.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(247,1,2,NULL,NULL,'2026-07-03','Morning',0.00,NULL,0.00,24.50,'[{\"qty\": 24, \"coin\": \"£1\"}]',175.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',203.00,'[{\"type\": \"machine\", \"amount\": 203, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',106.40,'[{\"amount\": 58.52, \"platform\": \"Uber Eats\"}, {\"amount\": 47.88, \"platform\": \"Deliveroo\"}]',10.64,'[{\"amount\": 5.85, \"platform\": \"Uber Eats\"}, {\"amount\": 4.79, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(248,1,2,NULL,NULL,'2026-07-03','Evening',0.00,NULL,0.00,24.50,'[{\"qty\": 24, \"coin\": \"£1\"}]',175.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',203.00,'[{\"type\": \"machine\", \"amount\": 203, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',106.40,'[{\"amount\": 58.52, \"platform\": \"Uber Eats\"}, {\"amount\": 47.88, \"platform\": \"Deliveroo\"}]',10.64,'[{\"amount\": 5.85, \"platform\": \"Uber Eats\"}, {\"amount\": 4.79, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(249,1,2,NULL,NULL,'2026-07-02','Morning',0.00,NULL,0.00,29.60,'[{\"qty\": 29, \"coin\": \"£1\"}]',207.20,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',251.60,'[{\"type\": \"machine\", \"amount\": 251.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',130.24,'[{\"amount\": 71.63, \"platform\": \"Uber Eats\"}, {\"amount\": 58.61, \"platform\": \"Deliveroo\"}]',13.02,'[{\"amount\": 7.16, \"platform\": \"Uber Eats\"}, {\"amount\": 5.86, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(250,1,2,NULL,NULL,'2026-07-02','Evening',0.00,NULL,0.00,29.60,'[{\"qty\": 29, \"coin\": \"£1\"}]',207.20,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',251.60,'[{\"type\": \"machine\", \"amount\": 251.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',130.24,'[{\"amount\": 71.63, \"platform\": \"Uber Eats\"}, {\"amount\": 58.61, \"platform\": \"Deliveroo\"}]',13.02,'[{\"amount\": 7.16, \"platform\": \"Uber Eats\"}, {\"amount\": 5.86, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(251,1,2,NULL,NULL,'2026-07-01','Morning',0.00,NULL,0.00,10.00,'[{\"qty\": 10, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',195.00,'[{\"type\": \"machine\", \"amount\": 195, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',40.00,'[{\"amount\": 22, \"platform\": \"Uber Eats\"}, {\"amount\": 18, \"platform\": \"Deliveroo\"}]',4.00,'[{\"amount\": 2.2, \"platform\": \"Uber Eats\"}, {\"amount\": 1.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(252,1,2,NULL,NULL,'2026-07-01','Evening',0.00,NULL,0.00,10.00,'[{\"qty\": 10, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',195.00,'[{\"type\": \"machine\", \"amount\": 195, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',40.00,'[{\"amount\": 22, \"platform\": \"Uber Eats\"}, {\"amount\": 18, \"platform\": \"Deliveroo\"}]',4.00,'[{\"amount\": 2.2, \"platform\": \"Uber Eats\"}, {\"amount\": 1.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(253,1,2,NULL,NULL,'2026-06-30','Morning',0.00,NULL,0.00,13.50,'[{\"qty\": 13, \"coin\": \"£1\"}]',86.40,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',56.16,'[{\"amount\": 30.89, \"platform\": \"Uber Eats\"}, {\"amount\": 25.27, \"platform\": \"Deliveroo\"}]',5.62,'[{\"amount\": 3.09, \"platform\": \"Uber Eats\"}, {\"amount\": 2.53, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(254,1,2,NULL,NULL,'2026-06-30','Evening',0.00,NULL,0.00,13.50,'[{\"qty\": 13, \"coin\": \"£1\"}]',86.40,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',56.16,'[{\"amount\": 30.89, \"platform\": \"Uber Eats\"}, {\"amount\": 25.27, \"platform\": \"Deliveroo\"}]',5.62,'[{\"amount\": 3.09, \"platform\": \"Uber Eats\"}, {\"amount\": 2.53, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(255,1,2,NULL,NULL,'2026-06-29','Morning',0.00,NULL,0.00,17.40,'[{\"qty\": 17, \"coin\": \"£1\"}]',110.20,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',74.24,'[{\"amount\": 40.83, \"platform\": \"Uber Eats\"}, {\"amount\": 33.41, \"platform\": \"Deliveroo\"}]',7.42,'[{\"amount\": 4.08, \"platform\": \"Uber Eats\"}, {\"amount\": 3.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(256,1,2,NULL,NULL,'2026-06-29','Evening',0.00,NULL,0.00,17.40,'[{\"qty\": 17, \"coin\": \"£1\"}]',110.20,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',74.24,'[{\"amount\": 40.83, \"platform\": \"Uber Eats\"}, {\"amount\": 33.41, \"platform\": \"Deliveroo\"}]',7.42,'[{\"amount\": 4.08, \"platform\": \"Uber Eats\"}, {\"amount\": 3.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(257,1,2,NULL,NULL,'2026-06-28','Morning',0.00,NULL,0.00,21.70,'[{\"qty\": 21, \"coin\": \"£1\"}]',136.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',94.24,'[{\"amount\": 51.83, \"platform\": \"Uber Eats\"}, {\"amount\": 42.41, \"platform\": \"Deliveroo\"}]',9.42,'[{\"amount\": 5.18, \"platform\": \"Uber Eats\"}, {\"amount\": 4.24, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(258,1,2,NULL,NULL,'2026-06-28','Evening',0.00,NULL,0.00,21.70,'[{\"qty\": 21, \"coin\": \"£1\"}]',136.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',94.24,'[{\"amount\": 51.83, \"platform\": \"Uber Eats\"}, {\"amount\": 42.41, \"platform\": \"Deliveroo\"}]',9.42,'[{\"amount\": 5.18, \"platform\": \"Uber Eats\"}, {\"amount\": 4.24, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(259,1,2,NULL,NULL,'2026-06-27','Morning',0.00,NULL,0.00,26.40,'[{\"qty\": 26, \"coin\": \"£1\"}]',165.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',116.16,'[{\"amount\": 63.89, \"platform\": \"Uber Eats\"}, {\"amount\": 52.27, \"platform\": \"Deliveroo\"}]',11.62,'[{\"amount\": 6.39, \"platform\": \"Uber Eats\"}, {\"amount\": 5.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(260,1,2,NULL,NULL,'2026-06-27','Evening',0.00,NULL,0.00,26.40,'[{\"qty\": 26, \"coin\": \"£1\"}]',165.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',116.16,'[{\"amount\": 63.89, \"platform\": \"Uber Eats\"}, {\"amount\": 52.27, \"platform\": \"Deliveroo\"}]',11.62,'[{\"amount\": 6.39, \"platform\": \"Uber Eats\"}, {\"amount\": 5.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(261,1,2,NULL,NULL,'2026-06-26','Morning',0.00,NULL,0.00,14.00,'[{\"qty\": 14, \"coin\": \"£1\"}]',196.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',56.00,'[{\"amount\": 30.8, \"platform\": \"Uber Eats\"}, {\"amount\": 25.2, \"platform\": \"Deliveroo\"}]',5.60,'[{\"amount\": 3.08, \"platform\": \"Uber Eats\"}, {\"amount\": 2.52, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(262,1,2,NULL,NULL,'2026-06-26','Evening',0.00,NULL,0.00,14.00,'[{\"qty\": 14, \"coin\": \"£1\"}]',196.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',56.00,'[{\"amount\": 30.8, \"platform\": \"Uber Eats\"}, {\"amount\": 25.2, \"platform\": \"Deliveroo\"}]',5.60,'[{\"amount\": 3.08, \"platform\": \"Uber Eats\"}, {\"amount\": 2.52, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(263,1,2,NULL,NULL,'2026-06-25','Morning',0.00,NULL,0.00,18.50,'[{\"qty\": 18, \"coin\": \"£1\"}]',229.40,'[{\"qty\": 11, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',76.96,'[{\"amount\": 42.33, \"platform\": \"Uber Eats\"}, {\"amount\": 34.63, \"platform\": \"Deliveroo\"}]',7.70,'[{\"amount\": 4.24, \"platform\": \"Uber Eats\"}, {\"amount\": 3.47, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(264,1,2,NULL,NULL,'2026-06-25','Evening',0.00,NULL,0.00,18.50,'[{\"qty\": 18, \"coin\": \"£1\"}]',229.40,'[{\"qty\": 11, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',76.96,'[{\"amount\": 42.33, \"platform\": \"Uber Eats\"}, {\"amount\": 34.63, \"platform\": \"Deliveroo\"}]',7.70,'[{\"amount\": 4.24, \"platform\": \"Uber Eats\"}, {\"amount\": 3.47, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(265,1,2,NULL,NULL,'2026-06-24','Morning',0.00,NULL,0.00,15.00,'[{\"qty\": 15, \"coin\": \"£1\"}]',80.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',64.00,'[{\"amount\": 35.2, \"platform\": \"Uber Eats\"}, {\"amount\": 28.8, \"platform\": \"Deliveroo\"}]',6.40,'[{\"amount\": 3.52, \"platform\": \"Uber Eats\"}, {\"amount\": 2.88, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(266,1,2,NULL,NULL,'2026-06-24','Evening',0.00,NULL,0.00,15.00,'[{\"qty\": 15, \"coin\": \"£1\"}]',80.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',64.00,'[{\"amount\": 35.2, \"platform\": \"Uber Eats\"}, {\"amount\": 28.8, \"platform\": \"Deliveroo\"}]',6.40,'[{\"amount\": 3.52, \"platform\": \"Uber Eats\"}, {\"amount\": 2.88, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(267,1,2,NULL,NULL,'2026-06-23','Morning',0.00,NULL,0.00,18.90,'[{\"qty\": 18, \"coin\": \"£1\"}]',102.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',82.08,'[{\"amount\": 45.14, \"platform\": \"Uber Eats\"}, {\"amount\": 36.94, \"platform\": \"Deliveroo\"}]',8.21,'[{\"amount\": 4.52, \"platform\": \"Uber Eats\"}, {\"amount\": 3.69, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(268,1,2,NULL,NULL,'2026-06-23','Evening',0.00,NULL,0.00,18.90,'[{\"qty\": 18, \"coin\": \"£1\"}]',102.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',82.08,'[{\"amount\": 45.14, \"platform\": \"Uber Eats\"}, {\"amount\": 36.94, \"platform\": \"Deliveroo\"}]',8.21,'[{\"amount\": 4.52, \"platform\": \"Uber Eats\"}, {\"amount\": 3.69, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(269,1,2,NULL,NULL,'2026-06-22','Morning',0.00,NULL,0.00,23.20,'[{\"qty\": 23, \"coin\": \"£1\"}]',127.60,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',102.08,'[{\"amount\": 56.14, \"platform\": \"Uber Eats\"}, {\"amount\": 45.94, \"platform\": \"Deliveroo\"}]',10.21,'[{\"amount\": 5.62, \"platform\": \"Uber Eats\"}, {\"amount\": 4.59, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(270,1,2,NULL,NULL,'2026-06-22','Evening',0.00,NULL,0.00,23.20,'[{\"qty\": 23, \"coin\": \"£1\"}]',127.60,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',102.08,'[{\"amount\": 56.14, \"platform\": \"Uber Eats\"}, {\"amount\": 45.94, \"platform\": \"Deliveroo\"}]',10.21,'[{\"amount\": 5.62, \"platform\": \"Uber Eats\"}, {\"amount\": 4.59, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(271,1,1,NULL,NULL,'2026-08-05','Morning',0.00,NULL,0.00,10.80,'[{\"qty\": 10, \"coin\": \"£1\"}]',86.40,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',43.20,'[{\"amount\": 23.76, \"platform\": \"Uber Eats\"}, {\"amount\": 19.44, \"platform\": \"Deliveroo\"}]',4.32,'[{\"amount\": 2.38, \"platform\": \"Uber Eats\"}, {\"amount\": 1.94, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(272,1,1,NULL,NULL,'2026-08-05','Evening',0.00,NULL,0.00,10.80,'[{\"qty\": 10, \"coin\": \"£1\"}]',86.40,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',43.20,'[{\"amount\": 23.76, \"platform\": \"Uber Eats\"}, {\"amount\": 19.44, \"platform\": \"Deliveroo\"}]',4.32,'[{\"amount\": 2.38, \"platform\": \"Uber Eats\"}, {\"amount\": 1.94, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(273,1,1,NULL,NULL,'2026-08-04','Morning',0.00,NULL,0.00,14.50,'[{\"qty\": 14, \"coin\": \"£1\"}]',110.20,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',60.32,'[{\"amount\": 33.18, \"platform\": \"Uber Eats\"}, {\"amount\": 27.14, \"platform\": \"Deliveroo\"}]',6.03,'[{\"amount\": 3.32, \"platform\": \"Uber Eats\"}, {\"amount\": 2.71, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(274,1,1,NULL,NULL,'2026-08-04','Evening',0.00,NULL,0.00,14.50,'[{\"qty\": 14, \"coin\": \"£1\"}]',110.20,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',60.32,'[{\"amount\": 33.18, \"platform\": \"Uber Eats\"}, {\"amount\": 27.14, \"platform\": \"Deliveroo\"}]',6.03,'[{\"amount\": 3.32, \"platform\": \"Uber Eats\"}, {\"amount\": 2.71, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(275,1,1,NULL,NULL,'2026-08-03','Morning',0.00,NULL,0.00,18.60,'[{\"qty\": 18, \"coin\": \"£1\"}]',136.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',79.36,'[{\"amount\": 43.65, \"platform\": \"Uber Eats\"}, {\"amount\": 35.71, \"platform\": \"Deliveroo\"}]',7.94,'[{\"amount\": 4.37, \"platform\": \"Uber Eats\"}, {\"amount\": 3.57, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(276,1,1,NULL,NULL,'2026-08-03','Evening',0.00,NULL,0.00,18.60,'[{\"qty\": 18, \"coin\": \"£1\"}]',136.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',79.36,'[{\"amount\": 43.65, \"platform\": \"Uber Eats\"}, {\"amount\": 35.71, \"platform\": \"Deliveroo\"}]',7.94,'[{\"amount\": 4.37, \"platform\": \"Uber Eats\"}, {\"amount\": 3.57, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(277,1,1,NULL,NULL,'2026-08-02','Morning',0.00,NULL,0.00,23.10,'[{\"qty\": 23, \"coin\": \"£1\"}]',165.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',100.32,'[{\"amount\": 55.18, \"platform\": \"Uber Eats\"}, {\"amount\": 45.14, \"platform\": \"Deliveroo\"}]',10.03,'[{\"amount\": 5.52, \"platform\": \"Uber Eats\"}, {\"amount\": 4.51, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(278,1,1,NULL,NULL,'2026-08-02','Evening',0.00,NULL,0.00,23.10,'[{\"qty\": 23, \"coin\": \"£1\"}]',165.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',100.32,'[{\"amount\": 55.18, \"platform\": \"Uber Eats\"}, {\"amount\": 45.14, \"platform\": \"Deliveroo\"}]',10.03,'[{\"amount\": 5.52, \"platform\": \"Uber Eats\"}, {\"amount\": 4.51, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(279,1,1,NULL,NULL,'2026-08-01','Morning',0.00,NULL,0.00,28.00,'[{\"qty\": 28, \"coin\": \"£1\"}]',196.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',123.20,'[{\"amount\": 67.76, \"platform\": \"Uber Eats\"}, {\"amount\": 55.44, \"platform\": \"Deliveroo\"}]',12.32,'[{\"amount\": 6.78, \"platform\": \"Uber Eats\"}, {\"amount\": 5.54, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(280,1,1,NULL,NULL,'2026-08-01','Evening',0.00,NULL,0.00,28.00,'[{\"qty\": 28, \"coin\": \"£1\"}]',196.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',123.20,'[{\"amount\": 67.76, \"platform\": \"Uber Eats\"}, {\"amount\": 55.44, \"platform\": \"Deliveroo\"}]',12.32,'[{\"amount\": 6.78, \"platform\": \"Uber Eats\"}, {\"amount\": 5.54, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(281,1,1,NULL,NULL,'2026-07-31','Morning',0.00,NULL,0.00,14.80,'[{\"qty\": 14, \"coin\": \"£1\"}]',229.40,'[{\"qty\": 11, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',59.20,'[{\"amount\": 32.56, \"platform\": \"Uber Eats\"}, {\"amount\": 26.64, \"platform\": \"Deliveroo\"}]',5.92,'[{\"amount\": 3.26, \"platform\": \"Uber Eats\"}, {\"amount\": 2.66, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(282,1,1,NULL,NULL,'2026-07-31','Evening',0.00,NULL,0.00,14.80,'[{\"qty\": 14, \"coin\": \"£1\"}]',229.40,'[{\"qty\": 11, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',59.20,'[{\"amount\": 32.56, \"platform\": \"Uber Eats\"}, {\"amount\": 26.64, \"platform\": \"Deliveroo\"}]',5.92,'[{\"amount\": 3.26, \"platform\": \"Uber Eats\"}, {\"amount\": 2.66, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(283,1,1,NULL,NULL,'2026-07-30','Morning',0.00,NULL,0.00,12.50,'[{\"qty\": 12, \"coin\": \"£1\"}]',80.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',52.00,'[{\"amount\": 28.6, \"platform\": \"Uber Eats\"}, {\"amount\": 23.4, \"platform\": \"Deliveroo\"}]',5.20,'[{\"amount\": 2.86, \"platform\": \"Uber Eats\"}, {\"amount\": 2.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(284,1,1,NULL,NULL,'2026-07-30','Evening',0.00,NULL,0.00,12.50,'[{\"qty\": 12, \"coin\": \"£1\"}]',80.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',52.00,'[{\"amount\": 28.6, \"platform\": \"Uber Eats\"}, {\"amount\": 23.4, \"platform\": \"Deliveroo\"}]',5.20,'[{\"amount\": 2.86, \"platform\": \"Uber Eats\"}, {\"amount\": 2.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(285,1,1,NULL,NULL,'2026-07-29','Morning',0.00,NULL,0.00,16.20,'[{\"qty\": 16, \"coin\": \"£1\"}]',102.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',69.12,'[{\"amount\": 38.02, \"platform\": \"Uber Eats\"}, {\"amount\": 31.1, \"platform\": \"Deliveroo\"}]',6.91,'[{\"amount\": 3.8, \"platform\": \"Uber Eats\"}, {\"amount\": 3.11, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(286,1,1,NULL,NULL,'2026-07-29','Evening',0.00,NULL,0.00,16.20,'[{\"qty\": 16, \"coin\": \"£1\"}]',102.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',69.12,'[{\"amount\": 38.02, \"platform\": \"Uber Eats\"}, {\"amount\": 31.1, \"platform\": \"Deliveroo\"}]',6.91,'[{\"amount\": 3.8, \"platform\": \"Uber Eats\"}, {\"amount\": 3.11, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(287,1,1,NULL,NULL,'2026-07-28','Morning',0.00,NULL,0.00,20.30,'[{\"qty\": 20, \"coin\": \"£1\"}]',127.60,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',88.16,'[{\"amount\": 48.49, \"platform\": \"Uber Eats\"}, {\"amount\": 39.67, \"platform\": \"Deliveroo\"}]',8.82,'[{\"amount\": 4.85, \"platform\": \"Uber Eats\"}, {\"amount\": 3.97, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(288,1,1,NULL,NULL,'2026-07-28','Evening',0.00,NULL,0.00,20.30,'[{\"qty\": 20, \"coin\": \"£1\"}]',127.60,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',88.16,'[{\"amount\": 48.49, \"platform\": \"Uber Eats\"}, {\"amount\": 39.67, \"platform\": \"Deliveroo\"}]',8.82,'[{\"amount\": 4.85, \"platform\": \"Uber Eats\"}, {\"amount\": 3.97, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(289,1,1,NULL,NULL,'2026-07-27','Morning',0.00,NULL,0.00,24.80,'[{\"qty\": 24, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',179.80,'[{\"type\": \"machine\", \"amount\": 179.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',109.12,'[{\"amount\": 60.02, \"platform\": \"Uber Eats\"}, {\"amount\": 49.1, \"platform\": \"Deliveroo\"}]',10.91,'[{\"amount\": 6, \"platform\": \"Uber Eats\"}, {\"amount\": 4.91, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(290,1,1,NULL,NULL,'2026-07-27','Evening',0.00,NULL,0.00,24.80,'[{\"qty\": 24, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',179.80,'[{\"type\": \"machine\", \"amount\": 179.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',109.12,'[{\"amount\": 60.02, \"platform\": \"Uber Eats\"}, {\"amount\": 49.1, \"platform\": \"Deliveroo\"}]',10.91,'[{\"amount\": 6, \"platform\": \"Uber Eats\"}, {\"amount\": 4.91, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(291,1,1,NULL,NULL,'2026-07-26','Morning',0.00,NULL,0.00,13.20,'[{\"qty\": 13, \"coin\": \"£1\"}]',184.80,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',224.40,'[{\"type\": \"machine\", \"amount\": 224.4, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',52.80,'[{\"amount\": 29.04, \"platform\": \"Uber Eats\"}, {\"amount\": 23.76, \"platform\": \"Deliveroo\"}]',5.28,'[{\"amount\": 2.9, \"platform\": \"Uber Eats\"}, {\"amount\": 2.38, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(292,1,1,NULL,NULL,'2026-07-26','Evening',0.00,NULL,0.00,13.20,'[{\"qty\": 13, \"coin\": \"£1\"}]',184.80,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',224.40,'[{\"type\": \"machine\", \"amount\": 224.4, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',52.80,'[{\"amount\": 29.04, \"platform\": \"Uber Eats\"}, {\"amount\": 23.76, \"platform\": \"Deliveroo\"}]',5.28,'[{\"amount\": 2.9, \"platform\": \"Uber Eats\"}, {\"amount\": 2.38, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(293,1,1,NULL,NULL,'2026-07-25','Morning',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',217.00,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',273.00,'[{\"type\": \"machine\", \"amount\": 273, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',72.80,'[{\"amount\": 40.04, \"platform\": \"Uber Eats\"}, {\"amount\": 32.76, \"platform\": \"Deliveroo\"}]',7.28,'[{\"amount\": 4, \"platform\": \"Uber Eats\"}, {\"amount\": 3.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(294,1,1,NULL,NULL,'2026-07-25','Evening',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',217.00,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',273.00,'[{\"type\": \"machine\", \"amount\": 273, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',72.80,'[{\"amount\": 40.04, \"platform\": \"Uber Eats\"}, {\"amount\": 32.76, \"platform\": \"Deliveroo\"}]',7.28,'[{\"amount\": 4, \"platform\": \"Uber Eats\"}, {\"amount\": 3.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(295,1,1,NULL,NULL,'2026-07-24','Morning',0.00,NULL,0.00,22.20,'[{\"qty\": 22, \"coin\": \"£1\"}]',118.40,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',177.60,'[{\"type\": \"machine\", \"amount\": 177.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',94.72,'[{\"amount\": 52.1, \"platform\": \"Uber Eats\"}, {\"amount\": 42.62, \"platform\": \"Deliveroo\"}]',9.47,'[{\"amount\": 5.21, \"platform\": \"Uber Eats\"}, {\"amount\": 4.26, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(296,1,1,NULL,NULL,'2026-07-24','Evening',0.00,NULL,0.00,22.20,'[{\"qty\": 22, \"coin\": \"£1\"}]',118.40,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',177.60,'[{\"type\": \"machine\", \"amount\": 177.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',94.72,'[{\"amount\": 52.1, \"platform\": \"Uber Eats\"}, {\"amount\": 42.62, \"platform\": \"Deliveroo\"}]',9.47,'[{\"amount\": 5.21, \"platform\": \"Uber Eats\"}, {\"amount\": 4.26, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(297,1,1,NULL,NULL,'2026-07-23','Morning',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',95.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',145.00,'[{\"type\": \"machine\", \"amount\": 145, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',76.00,'[{\"amount\": 41.8, \"platform\": \"Uber Eats\"}, {\"amount\": 34.2, \"platform\": \"Deliveroo\"}]',7.60,'[{\"amount\": 4.18, \"platform\": \"Uber Eats\"}, {\"amount\": 3.42, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(298,1,1,NULL,NULL,'2026-07-23','Evening',0.00,NULL,0.00,17.50,'[{\"qty\": 17, \"coin\": \"£1\"}]',95.00,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',145.00,'[{\"type\": \"machine\", \"amount\": 145, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',76.00,'[{\"amount\": 41.8, \"platform\": \"Uber Eats\"}, {\"amount\": 34.2, \"platform\": \"Deliveroo\"}]',7.60,'[{\"amount\": 4.18, \"platform\": \"Uber Eats\"}, {\"amount\": 3.42, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(299,1,1,NULL,NULL,'2026-07-22','Morning',0.00,NULL,0.00,21.60,'[{\"qty\": 21, \"coin\": \"£1\"}]',118.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',183.60,'[{\"type\": \"machine\", \"amount\": 183.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',95.04,'[{\"amount\": 52.27, \"platform\": \"Uber Eats\"}, {\"amount\": 42.77, \"platform\": \"Deliveroo\"}]',9.50,'[{\"amount\": 5.23, \"platform\": \"Uber Eats\"}, {\"amount\": 4.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(300,1,1,NULL,NULL,'2026-07-22','Evening',0.00,NULL,0.00,21.60,'[{\"qty\": 21, \"coin\": \"£1\"}]',118.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',183.60,'[{\"type\": \"machine\", \"amount\": 183.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',95.04,'[{\"amount\": 52.27, \"platform\": \"Uber Eats\"}, {\"amount\": 42.77, \"platform\": \"Deliveroo\"}]',9.50,'[{\"amount\": 5.23, \"platform\": \"Uber Eats\"}, {\"amount\": 4.28, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(301,1,1,NULL,NULL,'2026-07-21','Morning',0.00,NULL,0.00,11.60,'[{\"qty\": 11, \"coin\": \"£1\"}]',145.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',226.20,'[{\"type\": \"machine\", \"amount\": 226.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',46.40,'[{\"amount\": 25.52, \"platform\": \"Uber Eats\"}, {\"amount\": 20.88, \"platform\": \"Deliveroo\"}]',4.64,'[{\"amount\": 2.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(302,1,1,NULL,NULL,'2026-07-21','Evening',0.00,NULL,0.00,11.60,'[{\"qty\": 11, \"coin\": \"£1\"}]',145.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',226.20,'[{\"type\": \"machine\", \"amount\": 226.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',46.40,'[{\"amount\": 25.52, \"platform\": \"Uber Eats\"}, {\"amount\": 20.88, \"platform\": \"Deliveroo\"}]',4.64,'[{\"amount\": 2.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(303,1,1,NULL,NULL,'2026-07-20','Morning',0.00,NULL,0.00,15.50,'[{\"qty\": 15, \"coin\": \"£1\"}]',173.60,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',148.80,'[{\"type\": \"machine\", \"amount\": 148.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',64.48,'[{\"amount\": 35.46, \"platform\": \"Uber Eats\"}, {\"amount\": 29.02, \"platform\": \"Deliveroo\"}]',6.45,'[{\"amount\": 3.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.9, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(304,1,1,NULL,NULL,'2026-07-20','Evening',0.00,NULL,0.00,15.50,'[{\"qty\": 15, \"coin\": \"£1\"}]',173.60,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',148.80,'[{\"type\": \"machine\", \"amount\": 148.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',64.48,'[{\"amount\": 35.46, \"platform\": \"Uber Eats\"}, {\"amount\": 29.02, \"platform\": \"Deliveroo\"}]',6.45,'[{\"amount\": 3.55, \"platform\": \"Uber Eats\"}, {\"amount\": 2.9, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(305,1,1,NULL,NULL,'2026-07-19','Morning',0.00,NULL,0.00,19.80,'[{\"qty\": 19, \"coin\": \"£1\"}]',204.60,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',191.40,'[{\"type\": \"machine\", \"amount\": 191.4, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',84.48,'[{\"amount\": 46.46, \"platform\": \"Uber Eats\"}, {\"amount\": 38.02, \"platform\": \"Deliveroo\"}]',8.45,'[{\"amount\": 4.65, \"platform\": \"Uber Eats\"}, {\"amount\": 3.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(306,1,1,NULL,NULL,'2026-07-19','Evening',0.00,NULL,0.00,19.80,'[{\"qty\": 19, \"coin\": \"£1\"}]',204.60,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',191.40,'[{\"type\": \"machine\", \"amount\": 191.4, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',84.48,'[{\"amount\": 46.46, \"platform\": \"Uber Eats\"}, {\"amount\": 38.02, \"platform\": \"Deliveroo\"}]',8.45,'[{\"amount\": 4.65, \"platform\": \"Uber Eats\"}, {\"amount\": 3.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(307,1,1,NULL,NULL,'2026-07-18','Morning',0.00,NULL,0.00,24.50,'[{\"qty\": 24, \"coin\": \"£1\"}]',112.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',238.00,'[{\"type\": \"machine\", \"amount\": 237.99999999999997, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',106.40,'[{\"amount\": 58.52, \"platform\": \"Uber Eats\"}, {\"amount\": 47.88, \"platform\": \"Deliveroo\"}]',10.64,'[{\"amount\": 5.85, \"platform\": \"Uber Eats\"}, {\"amount\": 4.79, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(308,1,1,NULL,NULL,'2026-07-18','Evening',0.00,NULL,0.00,24.50,'[{\"qty\": 24, \"coin\": \"£1\"}]',112.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',238.00,'[{\"type\": \"machine\", \"amount\": 237.99999999999997, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',106.40,'[{\"amount\": 58.52, \"platform\": \"Uber Eats\"}, {\"amount\": 47.88, \"platform\": \"Deliveroo\"}]',10.64,'[{\"amount\": 5.85, \"platform\": \"Uber Eats\"}, {\"amount\": 4.79, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(309,1,1,NULL,NULL,'2026-07-17','Morning',0.00,NULL,0.00,29.60,'[{\"qty\": 29, \"coin\": \"£1\"}]',140.60,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',288.60,'[{\"type\": \"machine\", \"amount\": 288.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',130.24,'[{\"amount\": 71.63, \"platform\": \"Uber Eats\"}, {\"amount\": 58.61, \"platform\": \"Deliveroo\"}]',13.02,'[{\"amount\": 7.16, \"platform\": \"Uber Eats\"}, {\"amount\": 5.86, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(310,1,1,NULL,NULL,'2026-07-17','Evening',0.00,NULL,0.00,29.60,'[{\"qty\": 29, \"coin\": \"£1\"}]',140.60,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',288.60,'[{\"type\": \"machine\", \"amount\": 288.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',130.24,'[{\"amount\": 71.63, \"platform\": \"Uber Eats\"}, {\"amount\": 58.61, \"platform\": \"Deliveroo\"}]',13.02,'[{\"amount\": 7.16, \"platform\": \"Uber Eats\"}, {\"amount\": 5.86, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(311,1,1,NULL,NULL,'2026-07-16','Morning',0.00,NULL,0.00,10.00,'[{\"qty\": 10, \"coin\": \"£1\"}]',110.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',120.00,'[{\"type\": \"machine\", \"amount\": 120, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',40.00,'[{\"amount\": 22, \"platform\": \"Uber Eats\"}, {\"amount\": 18, \"platform\": \"Deliveroo\"}]',4.00,'[{\"amount\": 2.2, \"platform\": \"Uber Eats\"}, {\"amount\": 1.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(312,1,1,NULL,NULL,'2026-07-16','Evening',0.00,NULL,0.00,10.00,'[{\"qty\": 10, \"coin\": \"£1\"}]',110.00,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',120.00,'[{\"type\": \"machine\", \"amount\": 120, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',40.00,'[{\"amount\": 22, \"platform\": \"Uber Eats\"}, {\"amount\": 18, \"platform\": \"Deliveroo\"}]',4.00,'[{\"amount\": 2.2, \"platform\": \"Uber Eats\"}, {\"amount\": 1.8, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(313,1,1,NULL,NULL,'2026-07-15','Morning',0.00,NULL,0.00,13.50,'[{\"qty\": 13, \"coin\": \"£1\"}]',135.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',156.60,'[{\"type\": \"machine\", \"amount\": 156.60000000000002, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',56.16,'[{\"amount\": 30.89, \"platform\": \"Uber Eats\"}, {\"amount\": 25.27, \"platform\": \"Deliveroo\"}]',5.62,'[{\"amount\": 3.09, \"platform\": \"Uber Eats\"}, {\"amount\": 2.53, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(314,1,1,NULL,NULL,'2026-07-15','Evening',0.00,NULL,0.00,13.50,'[{\"qty\": 13, \"coin\": \"£1\"}]',135.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',156.60,'[{\"type\": \"machine\", \"amount\": 156.60000000000002, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',56.16,'[{\"amount\": 30.89, \"platform\": \"Uber Eats\"}, {\"amount\": 25.27, \"platform\": \"Deliveroo\"}]',5.62,'[{\"amount\": 3.09, \"platform\": \"Uber Eats\"}, {\"amount\": 2.53, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(315,1,1,NULL,NULL,'2026-07-14','Morning',0.00,NULL,0.00,17.40,'[{\"qty\": 17, \"coin\": \"£1\"}]',162.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',197.20,'[{\"type\": \"machine\", \"amount\": 197.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',74.24,'[{\"amount\": 40.83, \"platform\": \"Uber Eats\"}, {\"amount\": 33.41, \"platform\": \"Deliveroo\"}]',7.42,'[{\"amount\": 4.08, \"platform\": \"Uber Eats\"}, {\"amount\": 3.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(316,1,1,NULL,NULL,'2026-07-14','Evening',0.00,NULL,0.00,17.40,'[{\"qty\": 17, \"coin\": \"£1\"}]',162.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',197.20,'[{\"type\": \"machine\", \"amount\": 197.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',74.24,'[{\"amount\": 40.83, \"platform\": \"Uber Eats\"}, {\"amount\": 33.41, \"platform\": \"Deliveroo\"}]',7.42,'[{\"amount\": 4.08, \"platform\": \"Uber Eats\"}, {\"amount\": 3.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(317,1,1,NULL,NULL,'2026-07-13','Morning',0.00,NULL,0.00,21.70,'[{\"qty\": 21, \"coin\": \"£1\"}]',192.20,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',241.80,'[{\"type\": \"machine\", \"amount\": 241.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',94.24,'[{\"amount\": 51.83, \"platform\": \"Uber Eats\"}, {\"amount\": 42.41, \"platform\": \"Deliveroo\"}]',9.42,'[{\"amount\": 5.18, \"platform\": \"Uber Eats\"}, {\"amount\": 4.24, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(318,1,1,NULL,NULL,'2026-07-13','Evening',0.00,NULL,0.00,21.70,'[{\"qty\": 21, \"coin\": \"£1\"}]',192.20,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',241.80,'[{\"type\": \"machine\", \"amount\": 241.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',94.24,'[{\"amount\": 51.83, \"platform\": \"Uber Eats\"}, {\"amount\": 42.41, \"platform\": \"Deliveroo\"}]',9.42,'[{\"amount\": 5.18, \"platform\": \"Uber Eats\"}, {\"amount\": 4.24, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(319,1,1,NULL,NULL,'2026-07-12','Morning',0.00,NULL,0.00,26.40,'[{\"qty\": 26, \"coin\": \"£1\"}]',105.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',158.40,'[{\"type\": \"machine\", \"amount\": 158.4, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',116.16,'[{\"amount\": 63.89, \"platform\": \"Uber Eats\"}, {\"amount\": 52.27, \"platform\": \"Deliveroo\"}]',11.62,'[{\"amount\": 6.39, \"platform\": \"Uber Eats\"}, {\"amount\": 5.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(320,1,1,NULL,NULL,'2026-07-12','Evening',0.00,NULL,0.00,26.40,'[{\"qty\": 26, \"coin\": \"£1\"}]',105.60,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',158.40,'[{\"type\": \"machine\", \"amount\": 158.4, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',116.16,'[{\"amount\": 63.89, \"platform\": \"Uber Eats\"}, {\"amount\": 52.27, \"platform\": \"Deliveroo\"}]',11.62,'[{\"amount\": 6.39, \"platform\": \"Uber Eats\"}, {\"amount\": 5.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(321,1,1,NULL,NULL,'2026-07-11','Morning',0.00,NULL,0.00,14.00,'[{\"qty\": 14, \"coin\": \"£1\"}]',133.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',203.00,'[{\"type\": \"machine\", \"amount\": 203, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',56.00,'[{\"amount\": 30.8, \"platform\": \"Uber Eats\"}, {\"amount\": 25.2, \"platform\": \"Deliveroo\"}]',5.60,'[{\"amount\": 3.08, \"platform\": \"Uber Eats\"}, {\"amount\": 2.52, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(322,1,1,NULL,NULL,'2026-07-11','Evening',0.00,NULL,0.00,14.00,'[{\"qty\": 14, \"coin\": \"£1\"}]',133.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',203.00,'[{\"type\": \"machine\", \"amount\": 203, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',56.00,'[{\"amount\": 30.8, \"platform\": \"Uber Eats\"}, {\"amount\": 25.2, \"platform\": \"Deliveroo\"}]',5.60,'[{\"amount\": 3.08, \"platform\": \"Uber Eats\"}, {\"amount\": 2.52, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(323,1,1,NULL,NULL,'2026-07-10','Morning',0.00,NULL,0.00,18.50,'[{\"qty\": 18, \"coin\": \"£1\"}]',162.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',251.60,'[{\"type\": \"machine\", \"amount\": 251.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',76.96,'[{\"amount\": 42.33, \"platform\": \"Uber Eats\"}, {\"amount\": 34.63, \"platform\": \"Deliveroo\"}]',7.70,'[{\"amount\": 4.24, \"platform\": \"Uber Eats\"}, {\"amount\": 3.47, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(324,1,1,NULL,NULL,'2026-07-10','Evening',0.00,NULL,0.00,18.50,'[{\"qty\": 18, \"coin\": \"£1\"}]',162.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',251.60,'[{\"type\": \"machine\", \"amount\": 251.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',76.96,'[{\"amount\": 42.33, \"platform\": \"Uber Eats\"}, {\"amount\": 34.63, \"platform\": \"Deliveroo\"}]',7.70,'[{\"amount\": 4.24, \"platform\": \"Uber Eats\"}, {\"amount\": 3.47, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(325,1,1,NULL,NULL,'2026-07-09','Morning',0.00,NULL,0.00,15.00,'[{\"qty\": 15, \"coin\": \"£1\"}]',125.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',195.00,'[{\"type\": \"machine\", \"amount\": 195, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',64.00,'[{\"amount\": 35.2, \"platform\": \"Uber Eats\"}, {\"amount\": 28.8, \"platform\": \"Deliveroo\"}]',6.40,'[{\"amount\": 3.52, \"platform\": \"Uber Eats\"}, {\"amount\": 2.88, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(326,1,1,NULL,NULL,'2026-07-09','Evening',0.00,NULL,0.00,15.00,'[{\"qty\": 15, \"coin\": \"£1\"}]',125.00,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',195.00,'[{\"type\": \"machine\", \"amount\": 195, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',64.00,'[{\"amount\": 35.2, \"platform\": \"Uber Eats\"}, {\"amount\": 28.8, \"platform\": \"Deliveroo\"}]',6.40,'[{\"amount\": 3.52, \"platform\": \"Uber Eats\"}, {\"amount\": 2.88, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(327,1,1,NULL,NULL,'2026-07-08','Morning',0.00,NULL,0.00,18.90,'[{\"qty\": 18, \"coin\": \"£1\"}]',151.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',82.08,'[{\"amount\": 45.14, \"platform\": \"Uber Eats\"}, {\"amount\": 36.94, \"platform\": \"Deliveroo\"}]',8.21,'[{\"amount\": 4.52, \"platform\": \"Uber Eats\"}, {\"amount\": 3.69, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(328,1,1,NULL,NULL,'2026-07-08','Evening',0.00,NULL,0.00,18.90,'[{\"qty\": 18, \"coin\": \"£1\"}]',151.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',129.60,'[{\"type\": \"machine\", \"amount\": 129.60000000000002, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',82.08,'[{\"amount\": 45.14, \"platform\": \"Uber Eats\"}, {\"amount\": 36.94, \"platform\": \"Deliveroo\"}]',8.21,'[{\"amount\": 4.52, \"platform\": \"Uber Eats\"}, {\"amount\": 3.69, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(329,1,1,NULL,NULL,'2026-07-07','Morning',0.00,NULL,0.00,23.20,'[{\"qty\": 23, \"coin\": \"£1\"}]',179.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',102.08,'[{\"amount\": 56.14, \"platform\": \"Uber Eats\"}, {\"amount\": 45.94, \"platform\": \"Deliveroo\"}]',10.21,'[{\"amount\": 5.62, \"platform\": \"Uber Eats\"}, {\"amount\": 4.59, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(330,1,1,NULL,NULL,'2026-07-07','Evening',0.00,NULL,0.00,23.20,'[{\"qty\": 23, \"coin\": \"£1\"}]',179.80,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',168.20,'[{\"type\": \"machine\", \"amount\": 168.2, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',102.08,'[{\"amount\": 56.14, \"platform\": \"Uber Eats\"}, {\"amount\": 45.94, \"platform\": \"Deliveroo\"}]',10.21,'[{\"amount\": 5.62, \"platform\": \"Uber Eats\"}, {\"amount\": 4.59, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(331,1,1,NULL,NULL,'2026-07-06','Morning',0.00,NULL,0.00,12.40,'[{\"qty\": 12, \"coin\": \"£1\"}]',99.20,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',49.60,'[{\"amount\": 27.28, \"platform\": \"Uber Eats\"}, {\"amount\": 22.32, \"platform\": \"Deliveroo\"}]',4.96,'[{\"amount\": 2.73, \"platform\": \"Uber Eats\"}, {\"amount\": 2.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(332,1,1,NULL,NULL,'2026-07-06','Evening',0.00,NULL,0.00,12.40,'[{\"qty\": 12, \"coin\": \"£1\"}]',99.20,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',210.80,'[{\"type\": \"machine\", \"amount\": 210.8, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',49.60,'[{\"amount\": 27.28, \"platform\": \"Uber Eats\"}, {\"amount\": 22.32, \"platform\": \"Deliveroo\"}]',4.96,'[{\"amount\": 2.73, \"platform\": \"Uber Eats\"}, {\"amount\": 2.23, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(333,1,1,NULL,NULL,'2026-07-05','Morning',0.00,NULL,0.00,16.50,'[{\"qty\": 16, \"coin\": \"£1\"}]',125.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',68.64,'[{\"amount\": 37.75, \"platform\": \"Uber Eats\"}, {\"amount\": 30.89, \"platform\": \"Deliveroo\"}]',6.86,'[{\"amount\": 3.77, \"platform\": \"Uber Eats\"}, {\"amount\": 3.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(334,1,1,NULL,NULL,'2026-07-05','Evening',0.00,NULL,0.00,16.50,'[{\"qty\": 16, \"coin\": \"£1\"}]',125.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',257.40,'[{\"type\": \"machine\", \"amount\": 257.40000000000003, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',68.64,'[{\"amount\": 37.75, \"platform\": \"Uber Eats\"}, {\"amount\": 30.89, \"platform\": \"Deliveroo\"}]',6.86,'[{\"amount\": 3.77, \"platform\": \"Uber Eats\"}, {\"amount\": 3.09, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(335,1,1,NULL,NULL,'2026-07-04','Morning',0.00,NULL,0.00,21.00,'[{\"qty\": 21, \"coin\": \"£1\"}]',154.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',89.60,'[{\"amount\": 49.28, \"platform\": \"Uber Eats\"}, {\"amount\": 40.32, \"platform\": \"Deliveroo\"}]',8.96,'[{\"amount\": 4.93, \"platform\": \"Uber Eats\"}, {\"amount\": 4.03, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(336,1,1,NULL,NULL,'2026-07-04','Evening',0.00,NULL,0.00,21.00,'[{\"qty\": 21, \"coin\": \"£1\"}]',154.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',168.00,'[{\"type\": \"machine\", \"amount\": 168, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',89.60,'[{\"amount\": 49.28, \"platform\": \"Uber Eats\"}, {\"amount\": 40.32, \"platform\": \"Deliveroo\"}]',8.96,'[{\"amount\": 4.93, \"platform\": \"Uber Eats\"}, {\"amount\": 4.03, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(337,1,1,NULL,NULL,'2026-07-03','Morning',0.00,NULL,0.00,25.90,'[{\"qty\": 25, \"coin\": \"£1\"}]',185.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',112.48,'[{\"amount\": 61.86, \"platform\": \"Uber Eats\"}, {\"amount\": 50.62, \"platform\": \"Deliveroo\"}]',11.25,'[{\"amount\": 6.19, \"platform\": \"Uber Eats\"}, {\"amount\": 5.06, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(338,1,1,NULL,NULL,'2026-07-03','Evening',0.00,NULL,0.00,25.90,'[{\"qty\": 25, \"coin\": \"£1\"}]',185.00,'[{\"qty\": 9, \"note\": \"£20\", \"is_qty\": true}]',214.60,'[{\"type\": \"machine\", \"amount\": 214.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',112.48,'[{\"amount\": 61.86, \"platform\": \"Uber Eats\"}, {\"amount\": 50.62, \"platform\": \"Deliveroo\"}]',11.25,'[{\"amount\": 6.19, \"platform\": \"Uber Eats\"}, {\"amount\": 5.06, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(339,1,1,NULL,NULL,'2026-07-02','Morning',0.00,NULL,0.00,20.00,'[{\"qty\": 20, \"coin\": \"£1\"}]',140.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',88.00,'[{\"amount\": 48.4, \"platform\": \"Uber Eats\"}, {\"amount\": 39.6, \"platform\": \"Deliveroo\"}]',8.80,'[{\"amount\": 4.84, \"platform\": \"Uber Eats\"}, {\"amount\": 3.96, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(340,1,1,NULL,NULL,'2026-07-02','Evening',0.00,NULL,0.00,20.00,'[{\"qty\": 20, \"coin\": \"£1\"}]',140.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',170.00,'[{\"type\": \"machine\", \"amount\": 170, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',88.00,'[{\"amount\": 48.4, \"platform\": \"Uber Eats\"}, {\"amount\": 39.6, \"platform\": \"Deliveroo\"}]',8.80,'[{\"amount\": 4.84, \"platform\": \"Uber Eats\"}, {\"amount\": 3.96, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(341,1,1,NULL,NULL,'2026-07-01','Morning',0.00,NULL,0.00,10.80,'[{\"qty\": 10, \"coin\": \"£1\"}]',167.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',43.20,'[{\"amount\": 23.76, \"platform\": \"Uber Eats\"}, {\"amount\": 19.44, \"platform\": \"Deliveroo\"}]',4.32,'[{\"amount\": 2.38, \"platform\": \"Uber Eats\"}, {\"amount\": 1.94, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(342,1,1,NULL,NULL,'2026-07-01','Evening',0.00,NULL,0.00,10.80,'[{\"qty\": 10, \"coin\": \"£1\"}]',167.40,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',210.60,'[{\"type\": \"machine\", \"amount\": 210.6, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',43.20,'[{\"amount\": 23.76, \"platform\": \"Uber Eats\"}, {\"amount\": 19.44, \"platform\": \"Deliveroo\"}]',4.32,'[{\"amount\": 2.38, \"platform\": \"Uber Eats\"}, {\"amount\": 1.94, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(343,1,1,NULL,NULL,'2026-06-30','Morning',0.00,NULL,0.00,14.50,'[{\"qty\": 14, \"coin\": \"£1\"}]',92.80,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',60.32,'[{\"amount\": 33.18, \"platform\": \"Uber Eats\"}, {\"amount\": 27.14, \"platform\": \"Deliveroo\"}]',6.03,'[{\"amount\": 3.32, \"platform\": \"Uber Eats\"}, {\"amount\": 2.71, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(344,1,1,NULL,NULL,'2026-06-30','Evening',0.00,NULL,0.00,14.50,'[{\"qty\": 14, \"coin\": \"£1\"}]',92.80,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',139.20,'[{\"type\": \"machine\", \"amount\": 139.2, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',60.32,'[{\"amount\": 33.18, \"platform\": \"Uber Eats\"}, {\"amount\": 27.14, \"platform\": \"Deliveroo\"}]',6.03,'[{\"amount\": 3.32, \"platform\": \"Uber Eats\"}, {\"amount\": 2.71, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(345,1,1,NULL,NULL,'2026-06-29','Morning',0.00,NULL,0.00,18.60,'[{\"qty\": 18, \"coin\": \"£1\"}]',117.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',179.80,'[{\"type\": \"machine\", \"amount\": 179.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',79.36,'[{\"amount\": 43.65, \"platform\": \"Uber Eats\"}, {\"amount\": 35.71, \"platform\": \"Deliveroo\"}]',7.94,'[{\"amount\": 4.37, \"platform\": \"Uber Eats\"}, {\"amount\": 3.57, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(346,1,1,NULL,NULL,'2026-06-29','Evening',0.00,NULL,0.00,18.60,'[{\"qty\": 18, \"coin\": \"£1\"}]',117.80,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',179.80,'[{\"type\": \"machine\", \"amount\": 179.8, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',79.36,'[{\"amount\": 43.65, \"platform\": \"Uber Eats\"}, {\"amount\": 35.71, \"platform\": \"Deliveroo\"}]',7.94,'[{\"amount\": 4.37, \"platform\": \"Uber Eats\"}, {\"amount\": 3.57, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(347,1,1,NULL,NULL,'2026-06-28','Morning',0.00,NULL,0.00,23.10,'[{\"qty\": 23, \"coin\": \"£1\"}]',145.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',224.40,'[{\"type\": \"machine\", \"amount\": 224.4, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',100.32,'[{\"amount\": 55.18, \"platform\": \"Uber Eats\"}, {\"amount\": 45.14, \"platform\": \"Deliveroo\"}]',10.03,'[{\"amount\": 5.52, \"platform\": \"Uber Eats\"}, {\"amount\": 4.51, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(348,1,1,NULL,NULL,'2026-06-28','Evening',0.00,NULL,0.00,23.10,'[{\"qty\": 23, \"coin\": \"£1\"}]',145.20,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',224.40,'[{\"type\": \"machine\", \"amount\": 224.4, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',100.32,'[{\"amount\": 55.18, \"platform\": \"Uber Eats\"}, {\"amount\": 45.14, \"platform\": \"Deliveroo\"}]',10.03,'[{\"amount\": 5.52, \"platform\": \"Uber Eats\"}, {\"amount\": 4.51, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(349,1,1,NULL,NULL,'2026-06-27','Morning',0.00,NULL,0.00,28.00,'[{\"qty\": 28, \"coin\": \"£1\"}]',175.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',273.00,'[{\"type\": \"machine\", \"amount\": 273, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',123.20,'[{\"amount\": 67.76, \"platform\": \"Uber Eats\"}, {\"amount\": 55.44, \"platform\": \"Deliveroo\"}]',12.32,'[{\"amount\": 6.78, \"platform\": \"Uber Eats\"}, {\"amount\": 5.54, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(350,1,1,NULL,NULL,'2026-06-27','Evening',0.00,NULL,0.00,28.00,'[{\"qty\": 28, \"coin\": \"£1\"}]',175.00,'[{\"qty\": 8, \"note\": \"£20\", \"is_qty\": true}]',273.00,'[{\"type\": \"machine\", \"amount\": 273, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',123.20,'[{\"amount\": 67.76, \"platform\": \"Uber Eats\"}, {\"amount\": 55.44, \"platform\": \"Deliveroo\"}]',12.32,'[{\"amount\": 6.78, \"platform\": \"Uber Eats\"}, {\"amount\": 5.54, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(351,1,1,NULL,NULL,'2026-06-26','Morning',0.00,NULL,0.00,14.80,'[{\"qty\": 14, \"coin\": \"£1\"}]',207.20,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',177.60,'[{\"type\": \"machine\", \"amount\": 177.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',59.20,'[{\"amount\": 32.56, \"platform\": \"Uber Eats\"}, {\"amount\": 26.64, \"platform\": \"Deliveroo\"}]',5.92,'[{\"amount\": 3.26, \"platform\": \"Uber Eats\"}, {\"amount\": 2.66, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(352,1,1,NULL,NULL,'2026-06-26','Evening',0.00,NULL,0.00,14.80,'[{\"qty\": 14, \"coin\": \"£1\"}]',207.20,'[{\"qty\": 10, \"note\": \"£20\", \"is_qty\": true}]',177.60,'[{\"type\": \"machine\", \"amount\": 177.6, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',59.20,'[{\"amount\": 32.56, \"platform\": \"Uber Eats\"}, {\"amount\": 26.64, \"platform\": \"Deliveroo\"}]',5.92,'[{\"amount\": 3.26, \"platform\": \"Uber Eats\"}, {\"amount\": 2.66, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(353,1,1,NULL,NULL,'2026-06-25','Morning',0.00,NULL,0.00,12.50,'[{\"qty\": 12, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',145.00,'[{\"type\": \"machine\", \"amount\": 145, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',52.00,'[{\"amount\": 28.6, \"platform\": \"Uber Eats\"}, {\"amount\": 23.4, \"platform\": \"Deliveroo\"}]',5.20,'[{\"amount\": 2.86, \"platform\": \"Uber Eats\"}, {\"amount\": 2.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(354,1,1,NULL,NULL,'2026-06-25','Evening',0.00,NULL,0.00,12.50,'[{\"qty\": 12, \"coin\": \"£1\"}]',155.00,'[{\"qty\": 7, \"note\": \"£20\", \"is_qty\": true}]',145.00,'[{\"type\": \"machine\", \"amount\": 145, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',52.00,'[{\"amount\": 28.6, \"platform\": \"Uber Eats\"}, {\"amount\": 23.4, \"platform\": \"Deliveroo\"}]',5.20,'[{\"amount\": 2.86, \"platform\": \"Uber Eats\"}, {\"amount\": 2.34, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(355,1,1,NULL,NULL,'2026-06-24','Morning',0.00,NULL,0.00,16.20,'[{\"qty\": 16, \"coin\": \"£1\"}]',86.40,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',183.60,'[{\"type\": \"machine\", \"amount\": 183.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',69.12,'[{\"amount\": 38.02, \"platform\": \"Uber Eats\"}, {\"amount\": 31.1, \"platform\": \"Deliveroo\"}]',6.91,'[{\"amount\": 3.8, \"platform\": \"Uber Eats\"}, {\"amount\": 3.11, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(356,1,1,NULL,NULL,'2026-06-24','Evening',0.00,NULL,0.00,16.20,'[{\"qty\": 16, \"coin\": \"£1\"}]',86.40,'[{\"qty\": 4, \"note\": \"£20\", \"is_qty\": true}]',183.60,'[{\"type\": \"machine\", \"amount\": 183.6, \"payment_type\": \"Card Machine 1\"}]',5.00,'[{\"amount\": 5, \"description\": \"Supplies\"}]',69.12,'[{\"amount\": 38.02, \"platform\": \"Uber Eats\"}, {\"amount\": 31.1, \"platform\": \"Deliveroo\"}]',6.91,'[{\"amount\": 3.8, \"platform\": \"Uber Eats\"}, {\"amount\": 3.11, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(357,1,1,NULL,NULL,'2026-06-23','Morning',0.00,NULL,0.00,20.30,'[{\"qty\": 20, \"coin\": \"£1\"}]',110.20,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',226.20,'[{\"type\": \"machine\", \"amount\": 226.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',88.16,'[{\"amount\": 48.49, \"platform\": \"Uber Eats\"}, {\"amount\": 39.67, \"platform\": \"Deliveroo\"}]',8.82,'[{\"amount\": 4.85, \"platform\": \"Uber Eats\"}, {\"amount\": 3.97, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(358,1,1,NULL,NULL,'2026-06-23','Evening',0.00,NULL,0.00,20.30,'[{\"qty\": 20, \"coin\": \"£1\"}]',110.20,'[{\"qty\": 5, \"note\": \"£20\", \"is_qty\": true}]',226.20,'[{\"type\": \"machine\", \"amount\": 226.2, \"payment_type\": \"Card Machine 1\"}]',9.00,'[{\"amount\": 9, \"description\": \"Supplies\"}]',88.16,'[{\"amount\": 48.49, \"platform\": \"Uber Eats\"}, {\"amount\": 39.67, \"platform\": \"Deliveroo\"}]',8.82,'[{\"amount\": 4.85, \"platform\": \"Uber Eats\"}, {\"amount\": 3.97, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(359,1,1,NULL,NULL,'2026-06-22','Morning',0.00,NULL,0.00,24.80,'[{\"qty\": 24, \"coin\": \"£1\"}]',136.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',148.80,'[{\"type\": \"machine\", \"amount\": 148.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',109.12,'[{\"amount\": 60.02, \"platform\": \"Uber Eats\"}, {\"amount\": 49.1, \"platform\": \"Deliveroo\"}]',10.91,'[{\"amount\": 6, \"platform\": \"Uber Eats\"}, {\"amount\": 4.91, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58'),(360,1,1,NULL,NULL,'2026-06-22','Evening',0.00,NULL,0.00,24.80,'[{\"qty\": 24, \"coin\": \"£1\"}]',136.40,'[{\"qty\": 6, \"note\": \"£20\", \"is_qty\": true}]',148.80,'[{\"type\": \"machine\", \"amount\": 148.8, \"payment_type\": \"Card Machine 1\"}]',13.00,'[{\"amount\": 13, \"description\": \"Supplies\"}]',109.12,'[{\"amount\": 60.02, \"platform\": \"Uber Eats\"}, {\"amount\": 49.1, \"platform\": \"Deliveroo\"}]',10.91,'[{\"amount\": 6, \"platform\": \"Uber Eats\"}, {\"amount\": 4.91, \"platform\": \"Deliveroo\"}]',NULL,NULL,NULL,NULL,'draft',NULL,NULL,NULL,2,'2026-08-05 09:33:58','2026-08-05 09:33:58');
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
INSERT INTO `cms_faqs` VALUES (1,'How much does TotalCashPro cost?','Basic is £19.99/month. Professional is £29.99/month.',1,'published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(2,'Can I sign up instantly?','No. Submit a request and our team reviews it before creating your account.',2,'published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL);
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
INSERT INTO `cms_features` VALUES (1,'Daily Cash Up','Daily Cash Up for TotalCashPro customers.','basic',NULL,1,'published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(2,'Staff Clock In & Out','Staff Clock In & Out for TotalCashPro customers.','basic',NULL,2,'published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(3,'Inventory Management','Inventory Management for TotalCashPro customers.','professional',NULL,9,'published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(4,'Payroll & Wages','Payroll & Wages for TotalCashPro customers.','professional',NULL,10,'published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL);
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
INSERT INTO `cms_hero_sections` VALUES (1,'home','Cloud software for restaurants & retail','Manage cash, staff and reports from one secure dashboard','Built for restaurants, cafés, takeaways and retail businesses.','Start Free Trial','/register','Choose Your Plan','/#pricing',NULL,'published',1,'2026-08-04 17:08:04','2026-08-04 17:08:04',NULL);
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
INSERT INTO `cms_pages` VALUES (1,'Home','home',NULL,'published',NULL,'2026-08-04 17:08:04','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(2,'About','about',NULL,'published',NULL,'2026-08-04 17:08:04','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(3,'Contact','contact',NULL,'published',NULL,'2026-08-04 17:08:04','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(4,'Privacy','privacy',NULL,'published',NULL,'2026-08-04 17:08:04','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(5,'Terms','terms',NULL,'published',NULL,'2026-08-04 17:08:04','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL);
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
INSERT INTO `cms_testimonials` VALUES (1,'Amelia Hart','Operations Manager','Northbridge Kitchen','Cash ups and staff attendance finally live in one place.',1,1,'published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (1,'Alex Rivera','hello@prospect.test',NULL,'Enterprise pricing question','Do you support 40+ branches and custom SSO?','2026-08-04 17:08:07','2026-08-04 17:08:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'LAUNCH20','percentage',20.00,100,12,'2026-07-07 02:08:23','2026-11-07 02:08:23','active',1,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:23',NULL),(2,'FLAT10','fixed',10.00,50,4,'2026-07-28 02:08:23','2026-09-07 02:08:23','active',NULL,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:23',NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deliveries`
--

LOCK TABLES `deliveries` WRITE;
/*!40000 ALTER TABLE `deliveries` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discounts`
--

LOCK TABLES `discounts` WRITE;
/*!40000 ALTER TABLE `discounts` DISABLE KEYS */;
INSERT INTO `discounts` VALUES (1,1,'custom_price','custom_price',NULL,39.00,'active','2026-06-07 02:08:23','2027-08-07 02:08:23','Founding partner rate','2026-08-04 17:08:07','2026-08-07 02:08:23',NULL),(2,8,'percentage','lifetime',100.00,0.00,'active','2026-07-07 02:08:23',NULL,'Lifetime access','2026-08-04 17:08:07','2026-08-07 02:08:23',NULL);
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
INSERT INTO `email_templates` VALUES (1,'Access credentials','access-credentials','Your TotalCashPro login details','Your account has been created. Email: {{email}} Password: {{password}}','Account created','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(2,'Welcome Email','welcome','Welcome to TotalCashPro','Welcome aboard, {{name}}.','Welcome','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(3,'Reset Password','reset-password','Reset your TotalCashPro password','Use this link to reset your password: {{url}}','Password reset','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(4,'Trial Started','trial-started','Your trial has started','Your {{plan}} trial is active until {{ends_at}}.','Trial started','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(5,'Trial Ending','trial-ending','Your trial ends soon','Your trial ends on {{ends_at}}. Upgrade to keep access.','Trial ending','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(6,'Subscription Active','subscription-active','Subscription activated','Your {{plan}} subscription is now active.','Subscription active','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(7,'Subscription Expired','subscription-expired','Subscription expired','Your subscription has expired. Renew to restore access.','Subscription expired','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(8,'Invoice','invoice','Your invoice {{invoice}}','Invoice {{invoice}} for {{amount}} is attached.','Invoice','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(9,'Payment Success','payment-success','Payment received','We received your payment of {{amount}}.','Payment success','en','published','2026-08-04 17:08:04','2026-08-04 17:08:04',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_bank_accounts`
--

LOCK TABLES `finance_bank_accounts` WRITE;
/*!40000 ALTER TABLE `finance_bank_accounts` DISABLE KEYS */;
INSERT INTO `finance_bank_accounts` VALUES (1,1,2,'Main Operating Account','Barclays','20-00-00','4821','GBP',12500.00,1,1,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(2,1,1,'Central Float Account','HSBC','40-00-00','9012','GBP',3200.00,0,1,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_income_entries`
--

LOCK TABLES `finance_income_entries` WRITE;
/*!40000 ALTER TABLE `finance_income_entries` DISABLE KEYS */;
INSERT INTO `finance_income_entries` VALUES (1,1,2,1,'cash_up',NULL,NULL,'Friday night takings',1535.42,307.08,1842.50,'2026-08-04','paid',NULL,'2026-08-05 09:33:57','2026-08-05 09:33:57',2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(2,1,2,1,'manual',NULL,NULL,'Weekend card settlement',766.67,153.33,920.00,'2026-08-02','paid',NULL,'2026-08-03 09:33:57','2026-08-03 09:33:57',2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(3,1,2,1,'manual',NULL,NULL,'Catering invoice #104',541.67,108.33,650.00,'2026-07-29','approved',NULL,'2026-07-30 09:33:57',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(4,1,1,1,'cash_up',NULL,NULL,'Harbour Central lunch service',933.33,186.67,1120.00,'2026-08-03','paid',NULL,'2026-08-04 09:33:57','2026-08-04 09:33:57',2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(5,1,1,1,'other',NULL,NULL,'Corporate lunch booking',400.00,80.00,480.00,'2026-07-31','draft',NULL,NULL,NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(6,1,2,1,'other',NULL,NULL,'Deliveroo weekly payout',323.67,64.73,388.40,'2026-08-01','paid',NULL,'2026-08-02 09:33:57','2026-08-02 09:33:57',2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(7,1,2,1,'other',NULL,NULL,'Uber Eats settlement',246.00,49.20,295.20,'2026-08-01','paid',NULL,'2026-08-02 09:33:57','2026-08-07 04:39:15',2,'2026-08-05 09:30:17','2026-08-07 04:39:15'),(8,1,1,1,'manual',NULL,NULL,'Private event deposit',625.00,125.00,750.00,'2026-07-24','approved',NULL,'2026-07-25 09:33:57',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(9,1,2,1,'cash_up',NULL,NULL,'Weekly trading income',929.17,185.83,1115.00,'2026-07-22','paid',NULL,'2026-07-23 09:30:17','2026-07-23 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(10,1,1,1,'cash_up',NULL,NULL,'Weekly trading income',891.67,178.33,1070.00,'2026-07-22','paid',NULL,'2026-07-23 09:30:17','2026-07-23 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(11,1,2,1,'cash_up',NULL,NULL,'Weekly trading income',854.17,170.83,1025.00,'2026-07-15','paid',NULL,'2026-07-16 09:30:17','2026-07-16 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(12,1,1,1,'cash_up',NULL,NULL,'Weekly trading income',816.67,163.33,980.00,'2026-07-15','paid',NULL,'2026-07-16 09:30:17','2026-07-16 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(13,1,2,1,'cash_up',NULL,NULL,'Weekly trading income',779.17,155.83,935.00,'2026-07-08','paid',NULL,'2026-07-09 09:30:17','2026-07-09 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(14,1,1,1,'cash_up',NULL,NULL,'Weekly trading income',741.67,148.33,890.00,'2026-07-08','paid',NULL,'2026-07-09 09:30:17','2026-07-09 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(15,1,2,1,'cash_up',NULL,NULL,'Weekly trading income',704.17,140.83,845.00,'2026-07-01','paid',NULL,'2026-07-02 09:30:17','2026-07-02 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(16,1,1,1,'cash_up',NULL,NULL,'Weekly trading income',666.67,133.33,800.00,'2026-07-01','paid',NULL,'2026-07-02 09:30:17','2026-07-02 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(17,1,2,1,'cash_up',NULL,NULL,'Weekly trading income',966.67,193.33,1160.00,'2026-06-24','paid',NULL,'2026-06-25 09:30:17','2026-06-25 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(18,1,1,1,'cash_up',NULL,NULL,'Weekly trading income',929.17,185.83,1115.00,'2026-06-24','paid',NULL,'2026-06-25 09:30:17','2026-06-25 09:30:17',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(19,1,2,1,'manual',NULL,NULL,'Weekly manual income',929.17,185.83,1115.00,'2026-07-22','paid',NULL,'2026-07-23 09:33:57','2026-07-23 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(20,1,1,1,'manual',NULL,NULL,'Weekly manual income',891.67,178.33,1070.00,'2026-07-22','paid',NULL,'2026-07-23 09:33:57','2026-07-23 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(21,1,2,1,'manual',NULL,NULL,'Weekly manual income',854.17,170.83,1025.00,'2026-07-15','paid',NULL,'2026-07-16 09:33:57','2026-07-16 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(22,1,1,1,'manual',NULL,NULL,'Weekly manual income',816.67,163.33,980.00,'2026-07-15','paid',NULL,'2026-07-16 09:33:57','2026-07-16 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(23,1,2,1,'manual',NULL,NULL,'Weekly manual income',779.17,155.83,935.00,'2026-07-08','paid',NULL,'2026-07-09 09:33:57','2026-07-09 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(24,1,1,1,'manual',NULL,NULL,'Weekly manual income',741.67,148.33,890.00,'2026-07-08','paid',NULL,'2026-07-09 09:33:57','2026-07-09 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(25,1,2,1,'manual',NULL,NULL,'Weekly manual income',704.17,140.83,845.00,'2026-07-01','paid',NULL,'2026-07-02 09:33:57','2026-07-02 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(26,1,1,1,'manual',NULL,NULL,'Weekly manual income',666.67,133.33,800.00,'2026-07-01','paid',NULL,'2026-07-02 09:33:57','2026-07-02 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(27,1,2,1,'manual',NULL,NULL,'Weekly manual income',966.67,193.33,1160.00,'2026-06-24','paid',NULL,'2026-06-25 09:33:57','2026-06-25 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57'),(28,1,1,1,'manual',NULL,NULL,'Weekly manual income',929.17,185.83,1115.00,'2026-06-24','paid',NULL,'2026-06-25 09:33:57','2026-06-25 09:33:57',2,'2026-08-05 09:33:57','2026-08-05 09:33:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_integration_connections`
--

LOCK TABLES `finance_integration_connections` WRITE;
/*!40000 ALTER TABLE `finance_integration_connections` DISABLE KEYS */;
INSERT INTO `finance_integration_connections` VALUES (1,1,'stripe','disconnected',NULL,'{\"demo\": true}',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(2,1,'gocardless','disconnected',NULL,'{\"demo\": true}',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(3,1,'xero','disconnected',NULL,'{\"demo\": true}',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(4,1,'quickbooks','disconnected',NULL,'{\"demo\": true}',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(5,1,'sage','disconnected',NULL,'{\"demo\": true}',NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_payroll_runs`
--

LOCK TABLES `finance_payroll_runs` WRITE;
/*!40000 ALTER TABLE `finance_payroll_runs` DISABLE KEYS */;
INSERT INTO `finance_payroll_runs` VALUES (1,1,2,'2026-08-03','2026-08-09','2026-08-12','draft','Weekly payroll run (seeded)',NULL,NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(2,1,2,'2026-07-27','2026-08-02','2026-08-05','approved','Weekly payroll run (seeded)','2026-08-03 18:59:59',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(3,1,2,'2026-07-20','2026-07-26','2026-07-29','paid','Weekly payroll run (seeded)','2026-07-27 18:59:59','2026-07-29 18:59:59',2,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_supplier_payments`
--

LOCK TABLES `finance_supplier_payments` WRITE;
/*!40000 ALTER TABLE `finance_supplier_payments` DISABLE KEYS */;
INSERT INTO `finance_supplier_payments` VALUES (1,1,2,3,1,175.00,35.00,210.00,'2026-07-26','BACS-0100','bank_transfer','paid',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(2,1,1,5,1,195.00,40.00,235.00,'2026-07-26','BACS-0101','bank_transfer','paid',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(3,1,2,7,1,215.00,45.00,260.00,'2026-07-26','BACS-0102','bank_transfer','paid',2,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_lines`
--

LOCK TABLES `goods_received_lines` WRITE;
/*!40000 ALTER TABLE `goods_received_lines` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_notes`
--

LOCK TABLES `goods_received_notes` WRITE;
/*!40000 ALTER TABLE `goods_received_notes` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_categories`
--

LOCK TABLES `inventory_categories` WRITE;
/*!40000 ALTER TABLE `inventory_categories` DISABLE KEYS */;
INSERT INTO `inventory_categories` VALUES (1,1,2,'Packaging','Cups, lids and boxes','2026-08-04 17:08:08','2026-08-04 17:08:08',NULL),(2,1,2,'Dry Goods','Dry Goods stock','2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(3,1,2,'Beverages','Beverages stock','2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(4,1,1,'Front of House','Front of House stock','2026-08-05 09:30:17','2026-08-05 09:30:17',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_counts`
--

LOCK TABLES `inventory_counts` WRITE;
/*!40000 ALTER TABLE `inventory_counts` DISABLE KEYS */;
INSERT INTO `inventory_counts` VALUES (1,1,2,2,-3,237,'Weekly stock check',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(2,1,2,3,-3,15,'Weekly stock check',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(3,1,2,4,-3,93,'Weekly stock check',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(4,1,2,5,-3,9,'Weekly stock check',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(5,1,1,6,-3,5,'Weekly stock check',2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(6,1,1,7,-3,22,'Weekly stock check',2,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_items`
--

LOCK TABLES `inventory_items` WRITE;
/*!40000 ALTER TABLE `inventory_items` DISABLE KEYS */;
INSERT INTO `inventory_items` VALUES (1,1,2,1,'Paper Cups 12oz',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'box','pcs',50,180,40,0,0,0,1,1,0,'2026-08-04 17:08:08','2026-08-04 17:08:08',NULL),(2,1,2,2,'Burger Buns',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'unit','pcs',1,240,60,0,0,0,1,1,0,'2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(3,1,2,2,'Frozen Fries 2kg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'unit','pcs',1,18,24,0,0,0,1,1,0,'2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(4,1,2,3,'Soft Drink Cans',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'unit','pcs',1,96,48,0,0,0,1,1,0,'2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(5,1,2,3,'Oat Milk 1L',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'unit','pcs',1,12,20,0,0,0,1,1,0,'2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(6,1,1,4,'Napkins pack',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'unit','pcs',1,8,15,0,0,0,1,1,0,'2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(7,1,1,4,'Takeaway bags',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'unit','pcs',1,25,30,0,0,0,1,1,0,'2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(8,1,1,4,'buns',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'box','pcs',4,100,10,0,0,0,1,1,0,'2026-08-08 05:46:48','2026-08-08 05:46:48',NULL);
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
  KEY `inventory_stocktake_items_inventory_stocktake_id_foreign` (`inventory_stocktake_id`),
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,1,1,'INV-HARB-1',29.99,'GBP','paid','2026-08-01 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(2,1,1,'INV-HARB-2',29.99,'GBP','paid','2026-07-01 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(3,1,1,'INV-HARB-3',29.99,'GBP','paid','2026-06-01 19:00:00','2026-06-02 19:00:00','2026-06-02 19:00:00','2026-06-02 19:00:00',NULL),(4,1,1,'INV-HARB-4',29.99,'GBP','paid','2026-05-01 19:00:00','2026-05-02 19:00:00','2026-05-02 19:00:00','2026-05-02 19:00:00',NULL),(5,1,1,'INV-HARB-5',29.99,'GBP','paid','2026-04-01 19:00:00','2026-04-02 19:00:00','2026-04-02 19:00:00','2026-04-02 19:00:00',NULL),(6,3,3,'INV-NORT-1',19.99,'GBP','paid','2026-08-01 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(7,3,3,'INV-NORT-2',19.99,'GBP','paid','2026-07-01 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(8,3,3,'INV-NORT-3',19.99,'GBP','paid','2026-06-01 19:00:00','2026-06-02 19:00:00','2026-06-02 19:00:00','2026-06-02 19:00:00',NULL),(9,4,4,'INV-RIVE-1',19.99,'GBP','paid','2026-08-01 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(10,5,5,'INV-CEDA-1',29.99,'GBP','paid','2026-08-01 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(11,5,5,'INV-CEDA-2',29.99,'GBP','paid','2026-07-01 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(12,7,7,'INV-GREE-1',29.99,'GBP','paid','2026-08-01 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(13,7,7,'INV-GREE-2',29.99,'GBP','paid','2026-07-01 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(14,7,7,'INV-GREE-3',29.99,'GBP','paid','2026-06-01 19:00:00','2026-06-02 19:00:00','2026-06-02 19:00:00','2026-06-02 19:00:00',NULL),(15,7,7,'INV-GREE-4',29.99,'GBP','paid','2026-05-01 19:00:00','2026-05-02 19:00:00','2026-05-02 19:00:00','2026-05-02 19:00:00',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_activity_logs`
--

LOCK TABLES `kiosk_activity_logs` WRITE;
/*!40000 ALTER TABLE `kiosk_activity_logs` DISABLE KEYS */;
INSERT INTO `kiosk_activity_logs` VALUES (1,1,1,2,'kiosk_started',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','{\"session_id\": 1}','2026-08-07 01:26:59'),(2,1,1,2,'kiosk_closed',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-07 01:29:22'),(3,2,1,1,'kiosk_started',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','{\"session_id\": 2}','2026-08-08 05:50:55'),(4,2,1,1,'clock_in',3,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','{\"hours\": null, \"action\": \"clock-in\"}','2026-08-08 05:51:23'),(5,2,1,1,'clock_out',3,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','{\"hours\": 0, \"action\": \"clock-out\"}','2026-08-08 05:51:39'),(6,2,1,1,'kiosk_closed',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-08 05:53:52'),(7,2,1,1,'kiosk_started',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','{\"session_id\": 3}','2026-08-08 05:59:48'),(8,2,1,1,'clock_in',3,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','{\"hours\": null, \"action\": \"clock-in\"}','2026-08-08 06:00:54'),(9,2,1,1,'clock_out',3,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','{\"hours\": 0.01, \"action\": \"clock-out\"}','2026-08-08 06:01:13'),(10,2,1,1,'kiosk_closed',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-08 06:05:18'),(11,2,1,1,'force_logout',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-08 06:15:33'),(12,2,1,1,'force_logout',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-08 06:15:36'),(13,2,1,1,'kiosk_reset',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-08 06:15:36'),(14,1,1,2,'force_logout',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-08 06:15:42'),(15,1,1,2,'kiosk_reset',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac',NULL,'2026-08-08 06:15:42'),(16,1,1,2,'kiosk_started',NULL,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.15.6 Chrome/144.0.7559.236 Electron/40.10.3 Safari/537.36','Mac','{\"session_id\": 4}','2026-08-08 11:30:07'),(17,1,1,2,'pin_failed',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.15.6 Chrome/144.0.7559.236 Electron/40.10.3 Safari/537.36','Mac','{\"pin_length\": 4}','2026-08-08 11:30:16'),(18,1,1,2,'clock_in',18,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.15.6 Chrome/144.0.7559.236 Electron/40.10.3 Safari/537.36','Mac','{\"hours\": null, \"action\": \"clock-in\"}','2026-08-08 11:30:35');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_break_types`
--

LOCK TABLES `kiosk_break_types` WRITE;
/*!40000 ALTER TABLE `kiosk_break_types` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_sessions`
--

LOCK TABLES `kiosk_sessions` WRITE;
/*!40000 ALTER TABLE `kiosk_sessions` DISABLE KEYS */;
INSERT INTO `kiosk_sessions` VALUES (1,NULL,NULL,1,'3r2snv0FG2PQ1ZGx573ueEPiQrHc1YKOKRQsc4AZdRlkGqgNEGCojpZn5nlE6Eo9','active',2,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','2026-08-07 01:26:59',NULL,'2026-08-07 01:29:22',NULL,NULL,'2026-08-07 01:26:59','2026-08-07 01:29:22'),(2,NULL,NULL,2,'BXN2Q0iud1ht5i1y6bOXKVONTT634viGH46Mg5330YkQxX8GQ9sTrwxcttEYULnb','active',2,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','2026-08-08 05:50:55',NULL,'2026-08-08 05:53:52',NULL,NULL,'2026-08-08 05:50:55','2026-08-08 05:53:52'),(3,NULL,NULL,2,'aHONXvG9quOSerd1b5GdJjVER8N88CGhJ1oj4AJbeT95RjWOUGQAZPWpVIzj5985','active',2,2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','Mac','2026-08-08 05:59:48',NULL,'2026-08-08 06:05:18',NULL,NULL,'2026-08-08 05:59:48','2026-08-08 06:05:18'),(4,NULL,NULL,1,'ZDfpPxYxGQ0eoIIkVafLmWwkaj7QCY4A06ZsHJxuOFK6YbtIE6tAmsEHq1hBODS7','active',2,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.15.6 Chrome/144.0.7559.236 Electron/40.10.3 Safari/537.36','Mac','2026-08-08 11:30:07',NULL,NULL,NULL,NULL,'2026-08-08 11:30:07','2026-08-08 11:30:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_histories`
--

LOCK TABLES `login_histories` WRITE;
/*!40000 ALTER TABLE `login_histories` DISABLE KEYS */;
INSERT INTO `login_histories` VALUES (1,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'login',NULL,'2026-08-07 00:31:39','2026-08-07 00:31:39','2026-08-07 00:31:39'),(2,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'logout',NULL,'2026-08-07 00:32:10','2026-08-07 00:32:10','2026-08-07 00:32:10'),(3,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'login',NULL,'2026-08-07 00:37:47','2026-08-07 00:37:47','2026-08-07 00:37:47'),(4,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'logout',NULL,'2026-08-07 00:49:20','2026-08-07 00:49:20','2026-08-07 00:49:20'),(5,3,'staff.harbour-kitchen-group@totalcashpro.test','staff','127.0.0.1','Chrome','Mac','macOS',NULL,1,'login',NULL,'2026-08-07 00:49:26','2026-08-07 00:49:26','2026-08-07 00:49:26'),(6,3,'staff.harbour-kitchen-group@totalcashpro.test','staff','127.0.0.1','Chrome','Mac','macOS',NULL,1,'logout',NULL,'2026-08-07 00:51:23','2026-08-07 00:51:23','2026-08-07 00:51:23'),(7,NULL,'admin@lifereplay.dev',NULL,'127.0.0.1','Chrome','Mac','macOS',NULL,0,'login','Invalid credentials','2026-08-07 00:51:31','2026-08-07 00:51:31','2026-08-07 00:51:31'),(8,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'login',NULL,'2026-08-07 00:51:34','2026-08-07 00:51:34','2026-08-07 00:51:34'),(9,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'logout',NULL,'2026-08-07 01:58:34','2026-08-07 01:58:34','2026-08-07 01:58:34'),(10,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'login',NULL,'2026-08-07 04:01:28','2026-08-07 04:01:28','2026-08-07 04:01:28'),(11,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'logout',NULL,'2026-08-07 05:26:36','2026-08-07 05:26:36','2026-08-07 05:26:36'),(12,1,'admin@totalcashpro.com','super_admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'login',NULL,'2026-08-07 05:26:40','2026-08-07 05:26:40','2026-08-07 05:26:40'),(13,1,'admin@totalcashpro.com','super_admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'logout',NULL,'2026-08-07 05:29:00','2026-08-07 05:29:00','2026-08-07 05:29:00'),(14,2,'ava@harbourkitchen.test','admin','127.0.0.1','Chrome','Mac','macOS',NULL,1,'login',NULL,'2026-08-07 05:29:11','2026-08-07 05:29:11','2026-08-07 05:29:11');
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
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_01_000100_create_access_requests_table',1),(5,'2026_08_01_000200_create_contact_messages_table',1),(6,'2026_08_02_000300_create_roles_and_permissions_tables',1),(7,'2026_08_02_010000_create_organizations_and_branches_tables',1),(8,'2026_08_02_010100_create_billing_tables',1),(9,'2026_08_02_010200_create_support_and_log_tables',1),(10,'2026_08_02_010300_create_cms_media_settings_tables',1),(11,'2026_08_02_020000_enhance_super_admin_module_tables',1),(12,'2026_08_02_030000_create_business_admin_domain_tables',1),(13,'2026_08_02_150000_create_rota_group_user_table',1),(14,'2026_08_03_030000_add_signup_onboarding_fields',1),(15,'2026_08_04_170000_create_accounting_tables',1),(16,'2026_08_04_180000_create_finance_module_tables',1),(17,'2026_08_07_000000_enterprise_completion_tables',2),(18,'2026_08_07_100000_milestone3_workflow_tables',3),(19,'2026_08_07_050845_create_personal_access_tokens_table',4),(20,'2026_08_07_200000_milestone4_security_tables',4),(21,'2026_08_07_210000_milestone4_1_notification_preferences',5),(22,'2026_08_07_120000_create_smart_kiosk_tables',6),(23,'2026_08_07_140000_allow_multiple_kiosks_per_branch',7),(24,'2026_08_07_220000_hash_staff_pins_and_tenancy_hardening',8),(25,'2026_08_08_100000_phase1_kiosk_attendance_enhancements',9),(26,'2026_08_09_100000_phase2_rota_versioning',10),(27,'2026_08_10_100000_phase3_cash_drawer_reconciliation',11),(28,'2026_08_11_100000_phase4_inventory_procurement_rider',12),(29,'2026_08_12_100000_phase5_supplier_procurement_matching',13),(30,'2026_08_14_100000_phase7_executive_intelligence',14),(31,'2026_08_15_100000_kiosk_v2_architecture',15),(32,'2026_08_16_100000_till_management',16);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,'New business request waiting','Coastal Cafe Co submitted an access request.','alert','system','high',NULL,NULL,NULL,'2026-08-04 17:08:07','2026-08-04 17:08:07'),(2,1,'Payment failed for Northbridge','A card payment failed and needs follow-up.','info','system','normal','2026-08-07 01:08:23',NULL,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:23'),(3,2,'Low stock alert — Paper Cups','Dockside packaging is below the reorder limit.','alert','system','high','2026-08-07 00:49:15',NULL,NULL,'2026-08-05 09:30:17','2026-08-07 00:49:15'),(4,2,'Payroll run ready for approval','This week\'s Dockside payroll is waiting for review.','info','system','normal','2026-08-07 00:49:16',NULL,NULL,'2026-08-05 09:30:17','2026-08-07 00:49:16'),(5,2,'Supplier invoice due soon','Fresh Produce Co invoice FP-1001 is due in 10 days.','info','system','normal','2026-08-05 07:33:58',NULL,NULL,'2026-08-05 09:30:17','2026-08-05 09:33:58'),(6,2,'Cash up completed','Morning shift cash up saved for Dockside.','success','system','normal','2026-08-05 07:33:58',NULL,NULL,'2026-08-05 09:30:17','2026-08-05 09:33:58'),(7,3,'Your rota is published','Next week\'s shifts are now available in Staff Rota.','info','system','normal',NULL,NULL,NULL,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_kiosk_settings`
--

LOCK TABLES `organization_kiosk_settings` WRITE;
/*!40000 ALTER TABLE `organization_kiosk_settings` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES (1,'Harbour Kitchen Group','harbour-kitchen-group','ops@harbourkitchen.test','+44 7700 90010','GB','GBP','Europe/London',NULL,NULL,NULL,'08:00','23:00','{\"strict_rota_clockin\": \"0\", \"strict_business_hours\": \"0\"}',2,'active',NULL,NULL,'2026-06-28 02:08:17','2026-08-07 02:08:17',NULL),(2,'Oak Street Bakery','oak-street-bakery','hello@oakstreet.test','+44 7700 90011','GB','GBP','Europe/London',NULL,NULL,NULL,NULL,NULL,NULL,4,'trial','2026-08-04 02:08:18','2026-08-18 02:08:18','2026-07-02 02:08:18','2026-08-06 02:08:18',NULL),(3,'Northbridge Retail','northbridge-retail','finance@northbridge.test','+44 7700 90012','GB','GBP','Europe/London',NULL,NULL,NULL,NULL,NULL,NULL,6,'active',NULL,NULL,'2026-07-06 02:08:19','2026-08-05 02:08:19',NULL),(4,'Riverbend Cafe','riverbend-cafe','team@riverbend.test','+44 7700 90013','GB','GBP','Europe/London',NULL,NULL,NULL,NULL,NULL,NULL,8,'suspended',NULL,NULL,'2026-07-10 02:08:19','2026-08-04 02:08:19',NULL),(5,'Cedar Hospitality','cedar-hospitality','billing@cedar.test','+44 7700 90014','GB','GBP','Europe/London',NULL,NULL,NULL,NULL,NULL,NULL,10,'cancelled',NULL,NULL,'2026-07-14 02:08:20','2026-08-03 02:08:20',NULL),(6,'Summit Pantry','summit-pantry','admin@summitpantry.test','+44 7700 90015','GB','GBP','Europe/London',NULL,NULL,NULL,NULL,NULL,NULL,12,'pending',NULL,NULL,'2026-07-18 02:08:21','2026-08-02 02:08:21',NULL),(7,'Greenfield Markets','greenfield-markets','owners@greenfield.test','+44 7700 90016','GB','GBP','Europe/London',NULL,NULL,NULL,NULL,NULL,NULL,14,'active',NULL,NULL,'2026-07-22 02:08:22','2026-08-01 02:08:22',NULL),(8,'Lakeside Deli','lakeside-deli','info@lakeside.test','+44 7700 90017','GB','GBP','Europe/London',NULL,NULL,NULL,NULL,NULL,NULL,16,'active',NULL,NULL,'2026-07-26 02:08:22','2026-07-31 02:08:22',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,1,29.99,'GBP','manual','PAY-HARB-1','paid','card','2026-08-02 19:00:00',NULL,'2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(2,1,2,29.99,'GBP','manual','PAY-HARB-2','paid','card','2026-07-02 19:00:00',NULL,'2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(3,1,3,29.99,'GBP','manual','PAY-HARB-3','paid','card','2026-06-02 19:00:00',NULL,'2026-06-02 19:00:00','2026-06-02 19:00:00',NULL),(4,1,4,29.99,'GBP','manual','PAY-HARB-4','paid','card','2026-05-02 19:00:00',NULL,'2026-05-02 19:00:00','2026-05-02 19:00:00',NULL),(5,1,5,29.99,'GBP','manual','PAY-HARB-5','paid','card','2026-04-02 19:00:00',NULL,'2026-04-02 19:00:00','2026-04-02 19:00:00',NULL),(6,3,6,19.99,'GBP','manual','PAY-NORT-1','paid','card','2026-08-02 19:00:00',NULL,'2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(7,3,7,19.99,'GBP','manual','PAY-NORT-2','paid','card','2026-07-02 19:00:00',NULL,'2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(8,3,8,19.99,'GBP','manual','PAY-NORT-3','paid','card','2026-06-02 19:00:00',NULL,'2026-06-02 19:00:00','2026-06-02 19:00:00',NULL),(9,3,NULL,19.99,'GBP','manual','PAY-FAIL-NB-1','failed','card',NULL,NULL,'2026-08-04 17:08:05','2026-08-04 17:08:05',NULL),(10,4,9,19.99,'GBP','manual','PAY-RIVE-1','paid','card','2026-08-02 19:00:00',NULL,'2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(11,5,10,29.99,'GBP','manual','PAY-CEDA-1','paid','card','2026-08-02 19:00:00',NULL,'2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(12,5,11,29.99,'GBP','manual','PAY-CEDA-2','paid','card','2026-07-02 19:00:00',NULL,'2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(13,7,12,29.99,'GBP','manual','PAY-GREE-1','paid','card','2026-08-02 19:00:00',NULL,'2026-08-02 19:00:00','2026-08-02 19:00:00',NULL),(14,7,13,29.99,'GBP','manual','PAY-GREE-2','paid','card','2026-07-02 19:00:00',NULL,'2026-07-02 19:00:00','2026-07-02 19:00:00',NULL),(15,7,14,29.99,'GBP','manual','PAY-GREE-3','paid','card','2026-06-02 19:00:00',NULL,'2026-06-02 19:00:00','2026-06-02 19:00:00',NULL),(16,7,15,29.99,'GBP','manual','PAY-GREE-4','paid','card','2026-05-02 19:00:00',NULL,'2026-05-02 19:00:00','2026-05-02 19:00:00',NULL);
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
INSERT INTO `permissions` VALUES (1,'View Dashboard','dashboard.view','Overview','2026-08-04 17:08:04','2026-08-04 17:08:04'),(2,'Manage Businesses','businesses.manage','Customers','2026-08-04 17:08:04','2026-08-04 17:08:04'),(3,'Manage Users','users.manage','Customers','2026-08-04 17:08:04','2026-08-04 17:08:04'),(4,'Manage Plans','plans.manage','Billing','2026-08-04 17:08:04','2026-08-04 17:08:04'),(5,'Manage Subscriptions','subscriptions.manage','Billing','2026-08-04 17:08:04','2026-08-04 17:08:04'),(6,'Manage Coupons','coupons.manage','Billing','2026-08-04 17:08:04','2026-08-04 17:08:04'),(7,'Manage CMS','cms.manage','CMS','2026-08-04 17:08:04','2026-08-04 17:08:04'),(8,'Manage Settings','settings.manage','System','2026-08-04 17:08:04','2026-08-04 17:08:04'),(9,'Manage Roles','roles.manage','System','2026-08-04 17:08:04','2026-08-04 17:08:04'),(10,'View Audit Logs','audit.view','System','2026-08-04 17:08:04','2026-08-04 17:08:04');
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
INSERT INTO `plans` VALUES (1,'Basic','basic','Starter','Cash up, attendance and daily reports for single-location teams.',19.99,'GBP','monthly','{\"bullets\": [\"1 branch\", \"Up to 5 staff\", \"Cash up & attendance\", \"Standard reports\"], \"entitlements\": {\"rota\": false, \"cash_up\": true, \"payroll\": false, \"reports\": true, \"inventory\": false, \"max_staff\": 5, \"suppliers\": false, \"accounting\": false, \"attendance\": true, \"staff_panel\": true, \"max_branches\": 1, \"advanced_reports\": false, \"multiple_branches\": false}}',0,1,1,1,'2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(2,'Professional','professional','Growth','Everything in Basic plus inventory, payroll, rota and multi-branch.',29.99,'GBP','monthly','{\"bullets\": [\"Unlimited branches\", \"Unlimited staff\", \"Inventory & payroll\", \"Staff rota & analytics\"], \"entitlements\": {\"rota\": true, \"cash_up\": true, \"payroll\": true, \"reports\": true, \"inventory\": true, \"max_staff\": null, \"suppliers\": true, \"accounting\": true, \"attendance\": true, \"staff_panel\": true, \"max_branches\": null, \"advanced_reports\": true, \"multiple_branches\": true}}',1,1,1,2,'2026-08-04 17:08:04','2026-08-04 17:08:04',NULL),(3,'Enterprise','enterprise','Scale','Full platform access with dedicated commercial terms.',0.00,'GBP','monthly','{\"bullets\": [\"Everything in Professional\", \"Dedicated success\", \"Custom commercial terms\"], \"entitlements\": {\"rota\": true, \"cash_up\": true, \"payroll\": true, \"reports\": true, \"inventory\": true, \"max_staff\": null, \"suppliers\": true, \"accounting\": true, \"attendance\": true, \"staff_panel\": true, \"max_branches\": null, \"advanced_reports\": true, \"multiple_branches\": true}}',0,1,0,3,'2026-08-04 17:08:04','2026-08-04 17:08:04',NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_lines`
--

LOCK TABLES `purchase_order_lines` WRITE;
/*!40000 ALTER TABLE `purchase_order_lines` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riders`
--

LOCK TABLES `riders` WRITE;
/*!40000 ALTER TABLE `riders` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','super_admin','Full platform access for TotalCashPro operators.',1,'2026-08-04 17:08:04','2026-08-04 17:08:04'),(2,'Admin','admin','Business admin role (Phase 2+).',1,'2026-08-04 17:08:04','2026-08-04 17:08:04'),(3,'Staff','staff','Staff role (Phase 2+).',1,'2026-08-04 17:08:04','2026-08-04 17:08:04');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_group_user`
--

LOCK TABLES `rota_group_user` WRITE;
/*!40000 ALTER TABLE `rota_group_user` DISABLE KEYS */;
INSERT INTO `rota_group_user` VALUES (1,1,3,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,18,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(3,1,19,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(4,2,20,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(5,2,21,'2026-08-04 17:08:08','2026-08-04 17:08:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_groups`
--

LOCK TABLES `rota_groups` WRITE;
/*!40000 ALTER TABLE `rota_groups` DISABLE KEYS */;
INSERT INTO `rota_groups` VALUES (1,1,2,'Kitchen','#007bff',1,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,2,'Front of House','#16A34A',2,'2026-08-04 17:08:08','2026-08-04 17:08:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_sections`
--

LOCK TABLES `rota_sections` WRITE;
/*!40000 ALTER TABLE `rota_sections` DISABLE KEYS */;
INSERT INTO `rota_sections` VALUES (1,1,2,'Burgers','#563d7c','2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,2,'Fries','#0F766E','2026-08-04 17:08:08','2026-08-04 17:08:08'),(3,1,2,'Front','#16A34A','2026-08-04 17:08:08','2026-08-04 17:08:08'),(4,1,2,'Grill','#B45309','2026-08-04 17:08:08','2026-08-04 17:08:08'),(5,1,1,'burger station','#563d7c','2026-08-08 05:56:29','2026-08-08 05:56:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_shifts`
--

LOCK TABLES `rota_shifts` WRITE;
/*!40000 ALTER TABLE `rota_shifts` DISABLE KEYS */;
INSERT INTO `rota_shifts` VALUES (1,1,1,2,3,1,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,1,2,3,1,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(3,1,1,2,3,1,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(4,1,1,2,3,1,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(5,1,1,2,3,1,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(6,1,1,2,3,1,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(7,1,1,2,3,1,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(8,1,1,2,3,1,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(9,1,1,2,18,2,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(10,1,1,2,18,2,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(11,1,1,2,18,2,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(12,1,1,2,18,2,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(13,1,1,2,18,2,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(14,1,1,2,18,2,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(15,1,1,2,18,2,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(16,1,1,2,18,2,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(17,1,1,2,19,3,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(18,1,1,2,19,3,NULL,'2026-08-03','2026-08-03 17:00:00','2026-08-03 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(19,1,1,2,19,3,NULL,'2026-08-04','2026-08-04 09:00:00','2026-08-04 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(20,1,1,2,19,3,NULL,'2026-08-05','2026-08-05 09:00:00','2026-08-05 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(21,1,1,2,19,3,NULL,'2026-08-05','2026-08-05 17:00:00','2026-08-05 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(22,1,1,2,19,3,NULL,'2026-08-06','2026-08-06 09:00:00','2026-08-06 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(23,1,1,2,19,3,NULL,'2026-08-07','2026-08-07 09:00:00','2026-08-07 15:00:00','Morning',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(24,1,1,2,19,3,NULL,'2026-08-07','2026-08-07 17:00:00','2026-08-07 22:00:00','Evening',0,'published','2026-08-04 17:08:08','2026-08-04 17:08:08'),(25,2,1,1,20,5,NULL,'2026-08-03','2026-08-03 09:00:00','2026-08-03 17:00:00','Morning',0,'published','2026-08-08 05:56:38','2026-08-08 05:56:38');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rota_versions`
--

LOCK TABLES `rota_versions` WRITE;
/*!40000 ALTER TABLE `rota_versions` DISABLE KEYS */;
INSERT INTO `rota_versions` VALUES (1,1,2,'2026-08-03',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-08-08 11:36:23',NULL,NULL,'2026-08-08 11:36:23','2026-08-08 11:36:23'),(2,1,1,'2026-08-03',1,'published',NULL,NULL,NULL,NULL,NULL,'2026-08-08 11:36:23',NULL,NULL,'2026-08-08 11:36:23','2026-08-08 11:36:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_logs`
--

LOCK TABLES `security_logs` WRITE;
/*!40000 ALTER TABLE `security_logs` DISABLE KEYS */;
INSERT INTO `security_logs` VALUES (1,2,'login_success','Business Admin signed in','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:31:39','2026-08-07 00:31:39'),(2,2,'logout','User signed out','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:32:10','2026-08-07 00:32:10'),(3,2,'login_success','Business Admin signed in','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:37:47','2026-08-07 00:37:47'),(4,2,'logout','User signed out','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:49:20','2026-08-07 00:49:20'),(5,3,'login_success','Staff signed in','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:49:26','2026-08-07 00:49:26'),(6,3,'logout','User signed out','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:51:23','2026-08-07 00:51:23'),(7,NULL,'login_failure','Failed login attempt for admin@lifereplay.dev','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:51:31','2026-08-07 00:51:31'),(8,2,'login_success','Business Admin signed in','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 00:51:34','2026-08-07 00:51:34'),(9,2,'logout','User signed out','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 01:58:34','2026-08-07 01:58:34'),(10,2,'login_success','Business Admin signed in','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 04:01:28','2026-08-07 04:01:28'),(11,2,'logout','User signed out','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 05:26:36','2026-08-07 05:26:36'),(12,1,'login_success','Super Admin signed in','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 05:26:40','2026-08-07 05:26:40'),(13,1,'logout','User signed out','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 05:29:00','2026-08-07 05:29:00'),(14,2,'login_success','Business Admin signed in','127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-08-07 05:29:11','2026-08-07 05:29:11');
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
INSERT INTO `sessions` VALUES ('4WBQ4Gsj3iqidy2YKO8MDWfsRipppwxTl5c89scM',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.15.6 Chrome/144.0.7559.236 Electron/40.10.3 Safari/537.36','eyJfdG9rZW4iOiJubElZODJtRHFoSFhUbVVNSVYwdTBxQlJzMGhZUlBhOFhzeGZVTVJpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9raW9za1wvNVQ3cE5WeXhCcEczeFozV3F5aVVzbU11MUkzbk5sT05sRTZIZE9xalZmbjBIQmpYZ0VmUFRBTmpFbTQ4cWRjbCIsInJvdXRlIjoia2lvc2suc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1786206635),('6tkItPOwRGNulfEVHo8LjCqCI7u5kGB5aEwEkAZg',2,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJhMWJoWG9RTHFNMFl5WkM2S3l4MDhaMmR3VFU1cDIxZElKS1c5akFDIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYnVzaW5lc3MtYWRtaW4iLCJyb3V0ZSI6ImJ1c2luZXNzLWFkbWluLmRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImJ1c2luZXNzX2FkbWluIjp7ImJyYW5jaF9pZCI6Mn19',1786216250),('Ac3icU4Nxx6A17xtseIi17o66yHFmdJu3ahZPGG5',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.15.6 Chrome/144.0.7559.236 Electron/40.10.3 Safari/537.36','eyJfdG9rZW4iOiJXMElaaTlYa21MMDIxbVpkSUp5a1dIdW9ORjU0RjAyQVkyRXRvS2xFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1786203990);
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
INSERT INTO `settings` VALUES (1,'general','platform_name','TotalCashPro','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(2,'general','support_email','hello@totalcashpro.com','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(3,'general','default_currency','GBP','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(4,'general','timezone','Europe/London','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(5,'brand','primary_color','#16A34A','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(6,'brand','logo_path','/logo.png','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(7,'brand','favicon_path','/favicon.png','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(8,'seo','default_title','TotalCashPro','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(9,'seo','meta_description','Cash, staff and reporting for multi-branch businesses.','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(10,'email','from_name','TotalCashPro','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(11,'email','from_address','noreply@totalcashpro.com','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(12,'payments','provider','manual','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(13,'payments','currency','GBP','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(14,'system','app_environment','production','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(15,'system','queue_driver','database','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(16,'appearance','default_theme','Light','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(17,'appearance','density','Comfortable','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(18,'maintenance','maintenance_mode','Off','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(19,'maintenance','banner_message','Scheduled maintenance window','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(20,'localization','locale','en_GB','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(21,'localization','date_format','d M Y','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(22,'localization','currency','GBP','string','2026-08-04 17:08:04','2026-08-04 17:08:04'),(23,'localization','timezone','Europe/London','string','2026-08-04 17:08:04','2026-08-04 17:08:04');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spendings`
--

LOCK TABLES `spendings` WRITE;
/*!40000 ALTER TABLE `spendings` DISABLE KEYS */;
INSERT INTO `spendings` VALUES (1,1,2,'Cleaning supplies','supplies',46.50,38.75,7.75,46.50,'paid',NULL,NULL,NULL,'2026-08-02','card','Floor cleaner and cloths',2,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,2,'Social media ads','marketing',120.00,100.00,20.00,120.00,'paid',NULL,NULL,NULL,'2026-07-29','bank',NULL,2,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(3,1,2,'Staff uniforms','supplies',89.99,74.99,15.00,89.99,'approved',1,'2026-08-04 09:33:57',NULL,'2026-08-04','card',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(4,1,2,'Window cleaner','supplies',35.00,29.17,5.83,35.00,'draft',1,NULL,NULL,'2026-08-05','card',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(5,1,1,'Fresh flowers','supplies',42.00,35.00,7.00,42.00,'paid',1,'2026-08-02 09:33:57','2026-08-02 09:33:57','2026-08-02','card',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(6,1,1,'Menu printing','supplies',118.00,98.33,19.67,118.00,'paid',1,'2026-07-27 09:33:57','2026-07-27 09:33:57','2026-07-27','card',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:33:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (1,1,2,NULL,'active','2026-03-07 02:08:18',NULL,NULL,NULL,'2026-07-31 19:00:00','2026-08-31 18:59:59',NULL,NULL,NULL,'2026-08-04 17:08:04','2026-08-07 02:08:18',NULL),(2,2,1,NULL,'trialing','2026-07-07 02:08:18',NULL,'2026-08-04 02:08:18','2026-08-18 02:08:18','2026-07-31 19:00:00','2026-08-18 02:08:18',NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:18',NULL),(3,3,1,NULL,'active','2026-05-07 02:08:19',NULL,NULL,NULL,'2026-07-31 19:00:00','2026-08-31 18:59:59',NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:19',NULL),(4,4,1,NULL,'suspended','2026-07-07 02:08:20',NULL,NULL,NULL,'2026-07-31 19:00:00','2026-08-31 18:59:59',NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:20',NULL),(5,5,2,NULL,'cancelled','2026-06-07 02:08:21','2026-08-02 02:08:21',NULL,NULL,'2026-07-31 19:00:00','2026-08-02 02:08:21','2026-08-02 02:08:21',NULL,NULL,'2026-08-04 17:08:06','2026-08-07 02:08:21',NULL),(6,6,1,NULL,'expired','2026-07-07 02:08:21','2026-08-02 02:08:21',NULL,NULL,'2026-07-31 19:00:00','2026-08-02 02:08:21',NULL,NULL,NULL,'2026-08-04 17:08:06','2026-08-07 02:08:21',NULL),(7,7,2,NULL,'lifetime','2026-04-07 02:08:22',NULL,NULL,NULL,'2026-07-31 19:00:00',NULL,NULL,NULL,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:22',NULL),(8,8,1,NULL,'free','2026-07-07 02:08:23',NULL,NULL,NULL,'2026-07-31 19:00:00','2026-08-31 18:59:59',NULL,NULL,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:23',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_invoices`
--

LOCK TABLES `supplier_invoices` WRITE;
/*!40000 ALTER TABLE `supplier_invoices` DISABLE KEYS */;
INSERT INTO `supplier_invoices` VALUES (1,1,2,1,NULL,NULL,'FP-1001','2026-07-31','2026-08-14',248.50,0.00,0.00,0.00,'GBP',NULL,0.00,'Weekly produce delivery','pending',NULL,NULL,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,2,2,NULL,NULL,'COA-P1','2026-08-03','2026-08-19',320.00,266.67,53.33,320.00,'GBP',NULL,0.00,'Weekly delivery','pending','2026-08-04 09:33:57',NULL,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(3,1,2,2,NULL,NULL,'COA-PAID1','2026-07-18','2026-08-01',210.00,175.00,35.00,210.00,'GBP',NULL,0.00,'Previous delivery — settled','paid','2026-07-20 09:33:57',NULL,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(4,1,1,3,NULL,NULL,'LON-P2','2026-08-03','2026-08-19',360.00,299.67,60.33,360.00,'GBP',NULL,0.00,'Weekly delivery','pending','2026-08-04 09:33:57',NULL,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(5,1,1,3,NULL,NULL,'LON-PAID2','2026-07-18','2026-08-01',235.00,195.00,40.00,235.00,'GBP',NULL,0.00,'Previous delivery — settled','paid','2026-07-20 09:33:57',NULL,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(6,1,2,4,NULL,NULL,'ECO-P3','2026-08-03','2026-08-19',400.00,332.67,67.33,400.00,'GBP',NULL,0.00,'Weekly delivery','pending','2026-08-04 09:33:57',NULL,'2026-08-05 09:30:17','2026-08-05 09:33:57'),(7,1,2,4,NULL,NULL,'ECO-PAID3','2026-07-18','2026-08-01',260.00,215.00,45.00,260.00,'GBP',NULL,0.00,'Previous delivery — settled','paid','2026-07-20 09:33:57',NULL,'2026-08-05 09:30:17','2026-08-05 09:33:57');
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
  KEY `supplier_price_history_supplier_id_foreign` (`supplier_id`),
  KEY `supplier_price_history_inventory_item_id_foreign` (`inventory_item_id`),
  KEY `supplier_price_history_created_by_foreign` (`created_by`),
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,1,2,'Fresh Produce Co',NULL,'orders@freshproduce.test','+44 7700 111222','Helen Park','12 Market Road, Brighton',NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-04 17:08:08','2026-08-04 17:08:08',NULL),(2,1,2,'Coastal Meats',NULL,'coastal.meats@supplier.test','+44 7700 220001','Mike Turner',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(3,1,1,'London Dairy Direct',NULL,'london.dairy.direct@supplier.test','+44 7700 220002','Sarah Mills',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-05 09:30:17','2026-08-05 09:30:17',NULL),(4,1,2,'EcoPack Supplies',NULL,'ecopack.supplies@supplier.test','+44 7700 220003','Jen Wu',NULL,NULL,NULL,NULL,NULL,'GBP',0,0.00,NULL,'active','2026-08-05 09:30:17','2026-08-05 09:30:17',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_ticket_replies`
--

LOCK TABLES `support_ticket_replies` WRITE;
/*!40000 ALTER TABLE `support_ticket_replies` DISABLE KEYS */;
INSERT INTO `support_ticket_replies` VALUES (1,1,1,'Thanks for reaching out — we are looking into this now.',0,'2026-08-04 17:08:07','2026-08-04 17:08:07'),(2,2,1,'Thanks for reaching out — we are looking into this now.',0,'2026-08-04 17:08:07','2026-08-04 17:08:07'),(3,3,1,'Thanks for reaching out — we are looking into this now.',0,'2026-08-04 17:08:07','2026-08-04 17:08:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (1,'TCP-1001',1,1,'Need help connecting a second till','Customer asked about: Need help connecting a second till','high','open','2026-08-04 17:08:07','2026-08-04 17:08:07',NULL),(2,'TCP-1002',2,1,'Trial access question','Customer asked about: Trial access question','normal','pending','2026-08-04 17:08:07','2026-08-04 17:08:07',NULL),(3,'TCP-1003',3,1,'Invoice copy for March','Customer asked about: Invoice copy for March','low','closed','2026-08-04 17:08:07','2026-08-04 17:08:07',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_devices`
--

LOCK TABLES `user_devices` WRITE;
/*!40000 ALTER TABLE `user_devices` DISABLE KEYS */;
INSERT INTO `user_devices` VALUES (1,2,'GCvcgPGWBme8brSZFWBj4WR3unaI92JFgYqvG2zj','Mac · Chrome','Chrome','macOS','127.0.0.1',0,0,'2026-08-07 00:31:39',NULL,'2026-08-07 00:31:39','2026-08-07 00:37:47'),(2,2,'1LJCiRWS4J4Ffr7jYimKTpW3TSpS4mGNZgMw6VdO','Mac · Chrome','Chrome','macOS','127.0.0.1',0,0,'2026-08-07 00:37:47',NULL,'2026-08-07 00:37:47','2026-08-07 00:51:34'),(3,3,'d61XuiRxoqqPI0dYHwrzxnJOMY1Eki2ha5HujEvH','Mac · Chrome','Chrome','macOS','127.0.0.1',0,1,'2026-08-07 00:49:26',NULL,'2026-08-07 00:49:26','2026-08-07 00:49:26'),(4,2,'WPqI0gd6bxkhbMFPzLkkfCINQAezoKLi4LaELIwN','Mac · Chrome','Chrome','macOS','127.0.0.1',0,0,'2026-08-07 00:51:34',NULL,'2026-08-07 00:51:34','2026-08-07 04:01:28'),(5,2,'yP75PWP16ka3ExTFMAvERix1dXiXmw3qnsz9O3FU','Mac · Chrome','Chrome','macOS','127.0.0.1',0,0,'2026-08-07 04:01:28',NULL,'2026-08-07 04:01:28','2026-08-07 05:29:11'),(6,1,'r5VL3xZiiRBTJ2FEG7HRAt0Rxz34lpUxFMCvk6j2','Mac · Chrome','Chrome','macOS','127.0.0.1',0,1,'2026-08-07 05:26:40',NULL,'2026-08-07 05:26:40','2026-08-07 05:26:40'),(7,2,'nCyTfvpfcTaiqKimHZaz9ICEkUKORgWIUlediNUv','Mac · Chrome','Chrome','macOS','127.0.0.1',0,1,'2026-08-07 05:29:11',NULL,'2026-08-07 05:29:11','2026-08-07 05:29:11');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','admin@totalcashpro.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-04 17:08:04',NULL,'$2y$12$5LXb8KcAYsxrT9eBC1dJAeHcovFKk4ymHR3PCROZh/Q20/7pVBgDK',1,NULL,NULL,'active','2026-08-07 05:26:40',NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:04','2026-08-07 05:26:40'),(2,'Ava Morgan','ava@harbourkitchen.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-18 02:08:17','2026-07-18 02:08:17','$2y$12$ecRQDGYRh7.QEI/3L0CEpO34FSuGBAGqZ83l/OiQiEag0AJKXEroG',2,1,NULL,'active','2026-08-07 05:29:11','n5gPYW2nIcAGHZjAIX5Eld5Mfx4FEl0hYQZ8AYrJDyGyv2kz7ILhHq31mZkj',0,NULL,NULL,NULL,'2026-08-04 17:08:04','2026-08-07 05:29:11'),(3,'Staff · Harbour Kitchen Group','staff.harbour-kitchen-group@totalcashpro.test',NULL,'$2y$12$/0/q0Q8kKfqQsyHvKK8FIezmZaxASWyRk8/G.q//wz2YAOYE2.Q02',NULL,12.50,NULL,NULL,NULL,'2026-08-07 02:08:18',NULL,'$2y$12$xajkZIllA33TOoYDpUFfFuOV59vAJYekaoGgeJX0lWe0r16QyIdhW',3,1,1,'active','2026-08-07 00:49:26','IHggXA39uSC4rXTieVOrmH84rgENGk2QaKVpEwubMiyKtHfZBX7QTnzGFMO9',0,NULL,NULL,NULL,'2026-08-04 17:08:04','2026-08-07 03:37:08'),(4,'Tom Reed','tom@oakstreet.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-19 02:08:18','2026-07-19 02:08:18','$2y$12$/4CNHCIsd4W/3DArrecVSuneflVbP4iQOyWnhdDehr4BYBE/5Hz5y',2,2,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:04','2026-08-07 02:08:18'),(5,'Staff · Oak Street Bakery','staff.oak-street-bakery@totalcashpro.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-07 02:08:18',NULL,'$2y$12$BJ.hna8WjlY4omcNUzHhROWbtCXyJjRD6jvwQlpvVLDOSCzAMzjxm',3,2,3,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:18'),(6,'Sara Khan','sara@northbridge.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-20 02:08:19','2026-07-20 02:08:19','$2y$12$QmR5otZulHc7820OYjOnxOubDIjXfTpDm56rp8wMbCi72x1/kIQ/.',2,3,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:19'),(7,'Staff · Northbridge Retail','staff.northbridge-retail@totalcashpro.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-07 02:08:19',NULL,'$2y$12$9E7kQ5ZA4OZq7VVUWomWaOSpH8PVCG7P8rE8j4C6DvFSxERlfbPp2',3,3,4,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:19'),(8,'James Cole','james@riverbend.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-21 02:08:19','2026-07-21 02:08:19','$2y$12$a//BCRKzq..vjiBVrVOmvulI8AVbnegvB0XLh8nKgN2YtP20stcTq',2,4,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:19'),(9,'Staff · Riverbend Cafe','staff.riverbend-cafe@totalcashpro.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-07 02:08:20',NULL,'$2y$12$oMPyKanDsDVDzhhSCf.zE.IMdYsbcsq7aCygz.2Wpblte44jclAxe',3,4,6,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:05','2026-08-07 02:08:20'),(10,'Mia Chen','mia@cedar.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-22 02:08:20','2026-07-22 02:08:20','$2y$12$wIzDanIJZ1cM1GdcBPYIv.uGE2xZ21KGVA4019KhKpDLysSHIi9vm',2,5,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:06','2026-08-07 02:08:20'),(11,'Staff · Cedar Hospitality','staff.cedar-hospitality@totalcashpro.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-07 02:08:21',NULL,'$2y$12$z4lwVw0K2AmnaVE5Smg4SOQuHeUdZOQKl5KJ4Lcd93oLX9.SNQl2e',3,5,7,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:06','2026-08-07 02:08:21'),(12,'Noah Price','noah@summitpantry.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-23 02:08:21','2026-07-23 02:08:21','$2y$12$xF7IwV4zNYJSN0z55wkoKuhCLCNFhpEndGkIYfkyUdGWcgMmVZhTK',2,6,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:06','2026-08-07 02:08:21'),(13,'Staff · Summit Pantry','staff.summit-pantry@totalcashpro.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-07 02:08:21',NULL,'$2y$12$3kn9mTXRPcQ3H4QyWOY90uDdLW1DJ/mADaj6R.Px5OESirjmVmbfa',3,6,8,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:06','2026-08-07 02:08:21'),(14,'Ellie Brooks','ellie@greenfield.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-24 02:08:22','2026-07-24 02:08:22','$2y$12$qHK3NZO558hW2HKYyaExaey/HyGd3qg8ICJ68ZwZSR3NPsI6kM76W',2,7,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:06','2026-08-07 02:08:22'),(15,'Staff · Greenfield Markets','staff.greenfield-markets@totalcashpro.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-07 02:08:22',NULL,'$2y$12$Yf05tniD/CdNbElm4ujhguuyRWWnl4QDLyNrI6EZBKBVfT.9eJpOe',3,7,9,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:22'),(16,'Chris Patel','chris@lakeside.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-25 02:08:22','2026-07-25 02:08:22','$2y$12$eTaNpNRXElJqtFcpLfUJwOuBl.NW6rngSkbOo5LT/V84CXR7RU/v6',2,8,NULL,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:22'),(17,'Staff · Lakeside Deli','staff.lakeside-deli@totalcashpro.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-07 02:08:23',NULL,'$2y$12$wzpRXGTS6ye/Q2Cj1P.U0O.VH/qcTQxzGpTw0EPcBTV7FHU3ervnu',3,8,11,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:07','2026-08-07 02:08:23'),(18,'Priya Shah','priya@harbourkitchen.test',NULL,'$2y$12$.BcrKxWBp1ra/IOZHXOepeYPpWcET3di8wlwN12QhU/JyVdnUHxrO',NULL,13.00,NULL,NULL,NULL,'2026-08-04 17:08:07',NULL,'$2y$12$efv.BY/w.6wrxYSy44CUiu2giYfS3PLUh8SuXgwwL9hZEq/h89CU2',3,1,2,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:07','2026-08-07 03:37:08'),(19,'Marcus Lee','marcus@harbourkitchen.test',NULL,'$2y$12$BbMjmDR7f6BPmwAmaxua.Ohlzph2rSYbCT6eSZ3LfRQQUUpLITSyW',NULL,11.75,NULL,NULL,NULL,'2026-08-04 17:08:08',NULL,'$2y$12$bMV2cvu8/oVl73CbKGk/MuYtcl3SjxjtCXSN/ORR2.xtsDStGRCQO',3,1,2,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:08','2026-08-07 03:37:09'),(20,'Sofia Reed','sofia@harbourkitchen.test',NULL,'$2y$12$3JkDgJx/vPkyhOGQchmH9upTBDuTSh9fXhGonMom/SPeM8W620tb.',NULL,14.00,NULL,NULL,NULL,'2026-08-04 17:08:08',NULL,'$2y$12$nBtT5hXByruVLwklceRQ2OCk7S6gvz/kSKkV7elu1O4Zmpxixd3x6',3,1,1,'active',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:08','2026-08-07 03:37:09'),(21,'Noah Blake','noah@harbourkitchen.test',NULL,'$2y$12$v9dqYfNpE9TrnaHAuCxO7.u/xqYaaByUqR2VpKdTIJQzxiVGPaecG',NULL,12.00,NULL,NULL,NULL,'2026-08-04 17:08:08',NULL,'$2y$12$QCJegjfYNhMtWT42rzGzoOvfUe/G1GEOYm0oBb3ohAAaRK.SmiGsq',3,1,1,'suspended',NULL,NULL,0,NULL,NULL,NULL,'2026-08-04 17:08:08','2026-08-08 05:51:07');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wages`
--

LOCK TABLES `wages` WRITE;
/*!40000 ALTER TABLE `wages` DISABLE KEYS */;
INSERT INTO `wages` VALUES (1,1,2,3,NULL,28.00,350.00,0.00,0.00,0.00,NULL,NULL,NULL,0,NULL,'Week seed wage','Pending',NULL,2,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(2,1,2,18,NULL,28.00,364.00,0.00,0.00,0.00,NULL,NULL,NULL,0,NULL,'Week seed wage','Pending',NULL,2,'2026-08-04 17:08:08','2026-08-04 17:08:08'),(3,1,2,3,1,27.00,337.50,337.50,0.00,337.50,'2026-08-03','2026-08-09','2026-08-12',1,NULL,'Payroll run 03 Aug','Pending',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(4,1,2,18,1,26.00,338.00,338.00,0.00,338.00,'2026-08-03','2026-08-09','2026-08-12',1,NULL,'Payroll run 03 Aug','Pending',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(5,1,2,19,1,27.00,317.25,317.25,0.00,317.25,'2026-08-03','2026-08-09','2026-08-12',1,NULL,'Payroll run 03 Aug','Pending',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(6,1,2,3,2,27.00,337.50,337.50,0.00,337.50,'2026-07-27','2026-08-02','2026-08-05',1,'2026-08-03 18:59:59','Payroll run 27 Jul','Approved',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(7,1,2,18,2,26.00,338.00,338.00,0.00,338.00,'2026-07-27','2026-08-02','2026-08-05',1,'2026-08-03 18:59:59','Payroll run 27 Jul','Approved',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(8,1,2,19,2,27.00,317.25,317.25,0.00,317.25,'2026-07-27','2026-08-02','2026-08-05',1,'2026-08-03 18:59:59','Payroll run 27 Jul','Approved',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(9,1,2,3,3,27.00,337.50,337.50,0.00,337.50,'2026-07-20','2026-07-26','2026-07-29',1,'2026-07-27 18:59:59','Payroll run 20 Jul','Paid',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(10,1,2,18,3,26.00,338.00,338.00,0.00,338.00,'2026-07-20','2026-07-26','2026-07-29',1,'2026-07-27 18:59:59','Payroll run 20 Jul','Paid',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17'),(11,1,2,19,3,27.00,317.25,317.25,0.00,317.25,'2026-07-20','2026-07-26','2026-07-29',1,'2026-07-27 18:59:59','Payroll run 20 Jul','Paid',NULL,2,'2026-08-05 09:30:17','2026-08-05 09:30:17');
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

-- Dump completed on 2026-08-09  0:18:51
