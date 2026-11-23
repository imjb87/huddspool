/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `email_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bcc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` text COLLATE utf8mb4_unicode_ci,
  `attachments` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expulsions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expulsions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `season_id` bigint unsigned NOT NULL,
  `expellable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expellable_id` bigint unsigned NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expulsions_season_id_foreign` (`season_id`),
  KEY `expulsions_expellable_type_expellable_id_index` (`expellable_type`,`expellable_id`),
  CONSTRAINT `expulsions_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fixtures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fixtures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `week` int NOT NULL,
  `fixture_date` date NOT NULL,
  `home_team_id` bigint unsigned NOT NULL,
  `away_team_id` bigint unsigned NOT NULL,
  `season_id` bigint unsigned DEFAULT NULL,
  `section_id` bigint unsigned NOT NULL,
  `venue_id` bigint unsigned DEFAULT NULL,
  `ruleset_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fixtures_section_week_date_index` (`section_id`,`week`,`fixture_date`),
  KEY `fixtures_home_team_id_foreign` (`home_team_id`),
  KEY `fixtures_away_team_id_foreign` (`away_team_id`),
  KEY `fixtures_season_id_foreign` (`season_id`),
  KEY `fixtures_venue_id_foreign` (`venue_id`),
  KEY `fixtures_ruleset_id_foreign` (`ruleset_id`),
  CONSTRAINT `fixtures_ruleset_id_foreign` FOREIGN KEY (`ruleset_id`) REFERENCES `rulesets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fixtures_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fixtures_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fixtures_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `frames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `frames` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `result_id` bigint unsigned NOT NULL,
  `home_player_id` bigint unsigned DEFAULT NULL,
  `home_score` int NOT NULL,
  `away_player_id` bigint unsigned DEFAULT NULL,
  `away_score` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `frames_result_id_id_index` (`result_id`,`id`),
  KEY `frames_home_player_result_index` (`home_player_id`,`result_id`),
  KEY `frames_away_player_result_index` (`away_player_id`,`result_id`),
  CONSTRAINT `frames_away_player_id_foreign` FOREIGN KEY (`away_player_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `frames_home_player_id_foreign` FOREIGN KEY (`home_player_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `frames_result_id_foreign` FOREIGN KEY (`result_id`) REFERENCES `results` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `knockout_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `knockout_matches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `knockout_id` bigint unsigned NOT NULL,
  `knockout_round_id` bigint unsigned NOT NULL,
  `position` int unsigned NOT NULL DEFAULT '1',
  `home_participant_id` bigint unsigned DEFAULT NULL,
  `away_participant_id` bigint unsigned DEFAULT NULL,
  `winner_participant_id` bigint unsigned DEFAULT NULL,
  `forfeit_participant_id` bigint unsigned DEFAULT NULL,
  `forfeit_reason` text COLLATE utf8mb4_unicode_ci,
  `venue_id` bigint unsigned DEFAULT NULL,
  `override_home_venue` tinyint(1) NOT NULL DEFAULT '0',
  `referee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `home_score` tinyint unsigned DEFAULT NULL,
  `away_score` tinyint unsigned DEFAULT NULL,
  `best_of` tinyint unsigned DEFAULT NULL,
  `next_match_id` bigint unsigned DEFAULT NULL,
  `next_slot` enum('home','away') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `reported_by_id` bigint unsigned DEFAULT NULL,
  `reported_at` timestamp NULL DEFAULT NULL,
  `report_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `knockout_matches_knockout_round_id_foreign` (`knockout_round_id`),
  KEY `knockout_matches_home_participant_id_foreign` (`home_participant_id`),
  KEY `knockout_matches_away_participant_id_foreign` (`away_participant_id`),
  KEY `knockout_matches_winner_participant_id_foreign` (`winner_participant_id`),
  KEY `knockout_matches_venue_id_foreign` (`venue_id`),
  KEY `knockout_matches_reported_by_id_foreign` (`reported_by_id`),
  KEY `knockout_matches_forfeit_participant_id_foreign` (`forfeit_participant_id`),
  KEY `knockout_matches_knockout_round_index` (`knockout_id`,`knockout_round_id`),
  KEY `knockout_matches_next_match_slot_index` (`next_match_id`,`next_slot`),
  CONSTRAINT `knockout_matches_away_participant_id_foreign` FOREIGN KEY (`away_participant_id`) REFERENCES `knockout_participants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_matches_forfeit_participant_id_foreign` FOREIGN KEY (`forfeit_participant_id`) REFERENCES `knockout_participants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_matches_home_participant_id_foreign` FOREIGN KEY (`home_participant_id`) REFERENCES `knockout_participants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_matches_knockout_id_foreign` FOREIGN KEY (`knockout_id`) REFERENCES `knockouts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knockout_matches_knockout_round_id_foreign` FOREIGN KEY (`knockout_round_id`) REFERENCES `knockout_rounds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knockout_matches_next_match_id_foreign` FOREIGN KEY (`next_match_id`) REFERENCES `knockout_matches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_matches_reported_by_id_foreign` FOREIGN KEY (`reported_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_matches_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_matches_winner_participant_id_foreign` FOREIGN KEY (`winner_participant_id`) REFERENCES `knockout_participants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `knockout_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `knockout_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `knockout_id` bigint unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seed` int unsigned DEFAULT NULL,
  `team_id` bigint unsigned DEFAULT NULL,
  `player_one_id` bigint unsigned DEFAULT NULL,
  `player_two_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `knockout_participants_knockout_id_foreign` (`knockout_id`),
  KEY `knockout_participants_team_id_foreign` (`team_id`),
  KEY `knockout_participants_player_one_id_foreign` (`player_one_id`),
  KEY `knockout_participants_player_two_id_foreign` (`player_two_id`),
  CONSTRAINT `knockout_participants_knockout_id_foreign` FOREIGN KEY (`knockout_id`) REFERENCES `knockouts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knockout_participants_player_one_id_foreign` FOREIGN KEY (`player_one_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_participants_player_two_id_foreign` FOREIGN KEY (`player_two_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `knockout_participants_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `knockout_rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `knockout_rounds` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `knockout_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int unsigned NOT NULL DEFAULT '1',
  `scheduled_for` datetime DEFAULT NULL,
  `best_of` tinyint unsigned DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `knockout_rounds_knockout_id_foreign` (`knockout_id`),
  CONSTRAINT `knockout_rounds_knockout_id_foreign` FOREIGN KEY (`knockout_id`) REFERENCES `knockouts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `knockouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `knockouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `season_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `best_of` tinyint unsigned DEFAULT NULL,
  `entry_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knockouts_season_id_slug_unique` (`season_id`,`slug`),
  CONSTRAINT `knockouts_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `author_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_settings_notification_type_unique` (`notification_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pulse_aggregates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_aggregates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bucket` int unsigned NOT NULL,
  `period` mediumint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `aggregate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(20,2) NOT NULL,
  `count` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pulse_aggregates_bucket_period_type_aggregate_key_hash_unique` (`bucket`,`period`,`type`,`aggregate`,`key_hash`),
  KEY `pulse_aggregates_period_bucket_index` (`period`,`bucket`),
  KEY `pulse_aggregates_type_index` (`type`),
  KEY `pulse_aggregates_period_type_aggregate_bucket_index` (`period`,`type`,`aggregate`,`bucket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pulse_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pulse_entries_timestamp_index` (`timestamp`),
  KEY `pulse_entries_type_index` (`type`),
  KEY `pulse_entries_key_hash_index` (`key_hash`),
  KEY `pulse_entries_timestamp_type_key_hash_value_index` (`timestamp`,`type`,`key_hash`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pulse_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pulse_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` binary(16) GENERATED ALWAYS AS (unhex(md5(`key`))) VIRTUAL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pulse_values_type_key_hash_unique` (`type`,`key_hash`),
  KEY `pulse_values_timestamp_index` (`timestamp`),
  KEY `pulse_values_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `endpoint` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `endpoint_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `public_key` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_encoding` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_hash_unique` (`endpoint_hash`),
  KEY `push_subscriptions_user_id_foreign` (`user_id`),
  CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fixture_id` bigint unsigned NOT NULL,
  `home_team_id` int NOT NULL,
  `home_team_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_score` int NOT NULL,
  `submitted_by` bigint unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `draft_version` int unsigned NOT NULL DEFAULT '0',
  `draft_updated_by` bigint unsigned DEFAULT NULL,
  `draft_state` json DEFAULT NULL,
  `away_team_id` int NOT NULL,
  `away_team_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `away_score` int NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL,
  `is_overridden` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `section_id` int NOT NULL,
  `ruleset_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `results_ruleset_id_foreign` (`ruleset_id`),
  KEY `results_draft_updated_by_foreign` (`draft_updated_by`),
  KEY `results_fixture_confirmed_index` (`fixture_id`,`is_confirmed`),
  KEY `results_section_confirmed_index` (`section_id`,`is_confirmed`),
  KEY `results_submitted_by_foreign` (`submitted_by`),
  CONSTRAINT `results_draft_updated_by_foreign` FOREIGN KEY (`draft_updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `results_fixture_id_foreign` FOREIGN KEY (`fixture_id`) REFERENCES `fixtures` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `results_ruleset_id_foreign` FOREIGN KEY (`ruleset_id`) REFERENCES `rulesets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `results_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rulesets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rulesets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `season_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `season_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `season_id` bigint unsigned NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `existing_venue_id` bigint unsigned DEFAULT NULL,
  `venue_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `venue_address` text COLLATE utf8mb4_unicode_ci,
  `venue_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `season_entries_reference_unique` (`reference`),
  KEY `season_entries_season_id_foreign` (`season_id`),
  KEY `season_entries_existing_venue_id_foreign` (`existing_venue_id`),
  CONSTRAINT `season_entries_existing_venue_id_foreign` FOREIGN KEY (`existing_venue_id`) REFERENCES `venues` (`id`) ON DELETE SET NULL,
  CONSTRAINT `season_entries_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `season_knockout_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `season_knockout_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `season_entry_id` bigint unsigned NOT NULL,
  `knockout_id` bigint unsigned NOT NULL,
  `season_team_entry_id` bigint unsigned DEFAULT NULL,
  `existing_team_id` bigint unsigned DEFAULT NULL,
  `entrant_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `player_one_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `player_two_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `season_knockout_entries_season_entry_id_foreign` (`season_entry_id`),
  KEY `season_knockout_entries_knockout_id_foreign` (`knockout_id`),
  KEY `season_knockout_entries_season_team_entry_id_foreign` (`season_team_entry_id`),
  KEY `season_knockout_entries_existing_team_id_foreign` (`existing_team_id`),
  CONSTRAINT `season_knockout_entries_existing_team_id_foreign` FOREIGN KEY (`existing_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  CONSTRAINT `season_knockout_entries_knockout_id_foreign` FOREIGN KEY (`knockout_id`) REFERENCES `knockouts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `season_knockout_entries_season_entry_id_foreign` FOREIGN KEY (`season_entry_id`) REFERENCES `season_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `season_knockout_entries_season_team_entry_id_foreign` FOREIGN KEY (`season_team_entry_id`) REFERENCES `season_team_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `season_team_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `season_team_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `season_entry_id` bigint unsigned NOT NULL,
  `existing_team_id` bigint unsigned DEFAULT NULL,
  `ruleset_id` bigint unsigned DEFAULT NULL,
  `second_ruleset_id` bigint unsigned DEFAULT NULL,
  `existing_venue_id` bigint unsigned DEFAULT NULL,
  `team_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `venue_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `venue_address` text COLLATE utf8mb4_unicode_ci,
  `venue_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `season_team_entries_season_entry_id_foreign` (`season_entry_id`),
  KEY `season_team_entries_existing_team_id_foreign` (`existing_team_id`),
  KEY `season_team_entries_existing_venue_id_foreign` (`existing_venue_id`),
  KEY `season_team_entries_ruleset_id_foreign` (`ruleset_id`),
  KEY `season_team_entries_second_ruleset_id_foreign` (`second_ruleset_id`),
  CONSTRAINT `season_team_entries_existing_team_id_foreign` FOREIGN KEY (`existing_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  CONSTRAINT `season_team_entries_existing_venue_id_foreign` FOREIGN KEY (`existing_venue_id`) REFERENCES `venues` (`id`) ON DELETE SET NULL,
  CONSTRAINT `season_team_entries_ruleset_id_foreign` FOREIGN KEY (`ruleset_id`) REFERENCES `rulesets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `season_team_entries_season_entry_id_foreign` FOREIGN KEY (`season_entry_id`) REFERENCES `season_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `season_team_entries_second_ruleset_id_foreign` FOREIGN KEY (`second_ruleset_id`) REFERENCES `rulesets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dates` json NOT NULL,
  `signup_opens_at` timestamp NULL DEFAULT NULL,
  `signup_closes_at` timestamp NULL DEFAULT NULL,
  `team_entry_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_open` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `section_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `section_team` (
  `section_id` bigint unsigned NOT NULL,
  `team_id` bigint unsigned NOT NULL,
  `deducted` int NOT NULL DEFAULT '0',
  `withdrawn_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sort` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_team_section_id_team_id_unique` (`section_id`,`team_id`),
  KEY `section_team_team_id_foreign` (`team_id`),
  CONSTRAINT `section_team_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `section_team_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season_id` bigint unsigned NOT NULL,
  `ruleset_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sections_season_id_ruleset_id_slug_unique` (`season_id`,`ruleset_id`,`slug`),
  KEY `sections_ruleset_id_foreign` (`ruleset_id`),
  CONSTRAINT `sections_ruleset_id_foreign` FOREIGN KEY (`ruleset_id`) REFERENCES `rulesets` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sections_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `support_ticket_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_ticket_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_activities_user_id_foreign` (`user_id`),
  KEY `support_ticket_activities_support_ticket_id_created_at_index` (`support_ticket_id`,`created_at`),
  CONSTRAINT `support_ticket_activities_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `support_ticket_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_ticket_replies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal_note` tinyint(1) NOT NULL DEFAULT '0',
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `seen_by` bigint unsigned DEFAULT NULL,
  `seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_replies_user_id_foreign` (`user_id`),
  KEY `support_ticket_replies_seen_by_foreign` (`seen_by`),
  KEY `support_ticket_replies_support_ticket_id_created_at_index` (`support_ticket_id`,`created_at`),
  CONSTRAINT `support_ticket_replies_seen_by_foreign` FOREIGN KEY (`seen_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_ticket_replies_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_ticket_replies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `requester_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requester_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `is_seen` tinyint(1) NOT NULL DEFAULT '0',
  `seen_by` bigint unsigned DEFAULT NULL,
  `seen_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_user_id_foreign` (`user_id`),
  KEY `support_tickets_seen_by_foreign` (`seen_by`),
  KEY `support_tickets_status_index` (`status`),
  KEY `support_tickets_last_activity_at_index` (`last_activity_at`),
  CONSTRAINT `support_tickets_seen_by_foreign` FOREIGN KEY (`seen_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venue_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tables_venue_id_foreign` (`venue_id`),
  CONSTRAINT `tables_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shortname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `venue_id` bigint unsigned DEFAULT NULL,
  `captain_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `folded_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_captain_id_foreign` (`captain_id`),
  KEY `teams_venue_id_foreign` (`venue_id`),
  CONSTRAINT `teams_captain_id_foreign` FOREIGN KEY (`captain_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teams_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receives_support_ticket_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `push_prompted_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_team_id_foreign` (`team_id`),
  CONSTRAINT `users_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `venues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2010_02_01_231350_create_venues_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2012_02_01_231400_create_teams_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2015_07_31_1_email_log',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2016_06_01_000001_create_oauth_auth_codes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2016_06_01_000002_create_oauth_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2016_06_01_000003_create_oauth_refresh_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2016_06_01_000004_create_oauth_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2016_09_21_001638_add_bcc_column_email_log',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2017_11_10_001638_add_more_mail_columns_email_log',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2018_05_11_115355_use_longtext_for_attachments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2023_02_01_231450_create_seasons_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2023_02_01_231456_create_sections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2023_02_02_181558_create_rulesets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2023_02_02_181632_create_fixtures_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2023_02_02_181639_create_frames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2023_02_02_182214_create_results_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2023_02_02_204023_create_section_team_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2023_06_07_000001_create_pulse_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2023_07_06_134038_add_content_to_rulesets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2023_07_07_064949_set_venue_id_on_teams_to_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2023_07_07_095711_remove_role_from_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2023_07_07_100035_add_captain_to_teams',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2023_07_07_182122_make_venue_id_nullable_on_fixtures',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2023_07_08_134124_add_season_id_to_fixtures',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2023_07_08_134528_add_ruleset_id_to_fixtures',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2023_07_12_131254_create_news_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2023_07_16_032815_add_shortname_to_teams',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2023_07_22_175342_add_role_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2023_07_22_180128_give_all_captains_team_admin_role_on_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2023_07_22_181333_add_submitted_by_to_results',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2023_07_23_190141_add_withdrawn_to_section_team',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2023_08_28_234324_create_pages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2023_10_24_161531_add_deducted_to_section_teams',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2023_10_25_115958_add_slug_to_rulesets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2024_06_01_000001_create_oauth_device_codes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2024_07_23_135931_add_folded_at_to_teams',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2024_07_24_170104_add_slug_to_seasons',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2024_07_30_150441_create_tables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2024_08_15_000001_create_knockouts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2024_08_15_000002_create_knockout_participants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2024_08_15_000003_create_knockout_rounds_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2024_08_15_000004_create_knockout_matches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2024_08_19_155708_add_primary_id_to_section_team',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2024_12_19_165406_create_expulsions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_03_08_104347_add_section_id_to_results',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_03_15_102754_add_sort_to_section_teams',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_03_20_000000_add_ruleset_id_to_results',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_03_21_000000_backfill_confirmed_results',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_03_21_010000_create_fixture_result_locks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_04_07_000001_add_avatar_path_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_12_16_143139_add_referee_to_knockout_matches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_12_16_143738_update_scheduled_for_column_on_knockout_rounds_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_12_16_145119_add_forfeit_fields_to_knockout_matches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_12_17_000001_add_is_visible_to_knockout_rounds_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_12_17_000010_add_lat_lng_to_venues_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_01_19_143150_add_override_home_venue_to_knockout_matches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_03_12_120000_add_reported_at_to_knockout_matches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_03_12_130000_add_report_reason_to_knockout_matches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_03_13_084909_add_submitted_at_to_results_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_03_13_104030_add_deleted_at_to_frames_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_03_15_144632_add_slug_to_sections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_03_21_121338_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_03_21_121410_seed_roles_and_permissions_for_existing_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_03_21_125328_sync_admin_submission_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_03_21_131848_create_season_signups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_03_21_131849_create_season_signup_teams_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_03_21_131850_create_season_signup_knockouts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_03_21_131924_add_signup_fields_to_seasons_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_03_21_131925_add_entry_fee_to_knockouts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_03_21_160000_add_ruleset_id_to_season_team_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_03_21_182936_add_registration_fields_to_season_entries_and_second_ruleset_to_season_team_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2026_03_21_184727_add_team_contact_fields_to_season_team_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2026_03_25_101212_relax_section_slug_uniqueness',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2026_03_25_124500_relax_knockout_slug_uniqueness',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_03_25_150500_repair_section_slugs_blocked_by_soft_deletes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_03_25_184033_add_result_draft_columns_to_results_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_04_02_111747_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_04_02_115734_create_push_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_04_02_141853_add_deleted_at_to_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_04_03_082838_add_push_prompted_at_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_04_03_093349_add_slug_to_news_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_04_03_113236_add_published_at_to_news_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_04_05_081148_create_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_04_05_081313_import_existing_user_avatars_to_media_library',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_04_05_093944_add_device_metadata_to_push_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_07_27_000000_create_notification_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_11_16_000001_add_competition_query_indexes_and_remove_legacy_locks',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_11_18_132140_create_support_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_11_18_132144_create_support_ticket_replies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_11_18_132149_create_support_ticket_activities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2026_11_18_132408_migrate_creators_ticketing_data_to_support_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2026_11_18_132933_add_support_ticket_notification_preference_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2026_11_18_132936_add_support_ticket_notification_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2026_11_18_134129_remove_retired_gpt_auth_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2026_11_18_135055_create_invitations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2026_11_18_140101_retire_creators_ticketing_tables',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2026_11_18_141500_remove_invitation_flow',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2026_11_18_143000_strengthen_competition_relationships',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2026_11_18_143100_backfill_spatie_roles',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2026_11_18_143200_remove_unused_personal_access_tokens_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2026_11_18_143300_relax_fixture_team_foreign_keys',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2026_11_18_143400_make_expulsion_details_nullable',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2026_11_18_160605_remove_assignee_id_from_support_tickets_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2026_11_18_162235_finalize_user_authorization_and_competition_relationships',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2026_11_18_163033_remove_priority_from_support_tickets_table',9);
