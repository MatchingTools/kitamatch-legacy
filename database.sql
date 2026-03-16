-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 12, 2021 at 03:21 PM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

DROP TABLE IF EXISTS `applicants`;
CREATE TABLE IF NOT EXISTS `applicants` (
  `aid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` datetime DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` smallint(5) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `siblings` int(10) UNSIGNED DEFAULT NULL,
  `additionalCriteria_1` int(5) DEFAULT NULL,
  `additionalCriteria_2` int(5) DEFAULT NULL,
  `additionalCriteria_3` int(5) DEFAULT NULL,
  `additionalCriteria_4` int(5) DEFAULT NULL,
  `additionalCriteria_5` int(5) DEFAULT NULL,
  `additionalCriteria_6` int(5) DEFAULT NULL,
  `additionalCriteria_7` int(5) DEFAULT NULL,
  `additionalCriteria_8` int(5) DEFAULT NULL,
  `additionalCriteria_9` int(5) DEFAULT NULL,
  `additionalCriteria_10` int(5) DEFAULT NULL,
  `additionalCriteria_11` int(5) DEFAULT NULL,
  `additionalCriteria_12` int(5) DEFAULT NULL,
  `volume_of_employment` int(10) UNSIGNED DEFAULT NULL,
  `religion` int(10) UNSIGNED DEFAULT NULL,
  `parental_status` int(10) UNSIGNED DEFAULT NULL,
  `care_start` smallint(6) DEFAULT NULL,
  `care_scope` smallint(6) DEFAULT NULL,
  `alternative_scope` tinyint(4) DEFAULT NULL,
  `alternative_start` tinyint(4) DEFAULT NULL,
  `age_cohort` smallint(6) DEFAULT NULL,
  `change_request` mediumint(8) UNSIGNED DEFAULT NULL,
  `points_manual` int(10) UNSIGNED DEFAULT NULL,
  `first_preference_program` int(11) NOT NULL,
  `sibling_applicant_id1` int(5) DEFAULT NULL,
  `sibling_applicant_id2` int(5) DEFAULT NULL,
  `sibling_applicant_id3` int(5) DEFAULT NULL,
  `sibling_applicant_id4` int(5) DEFAULT NULL,
  PRIMARY KEY (`aid`),
  KEY `status` (`status`),
  KEY `gid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `capacities`
--

DROP TABLE IF EXISTS `capacities`;
CREATE TABLE IF NOT EXISTS `capacities` (
  `id` int(11) NOT NULL,
  `pid` int(10) UNSIGNED DEFAULT NULL,
  `care_start` int(10) UNSIGNED DEFAULT NULL,
  `care_scope` int(10) UNSIGNED DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

DROP TABLE IF EXISTS `codes`;
CREATE TABLE IF NOT EXISTS `codes` (
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `code` smallint(5) UNSIGNED NOT NULL,
  `value` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

DROP TABLE IF EXISTS `criteria`;
CREATE TABLE IF NOT EXISTS `criteria` (
  `cid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `criterium_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `criterium_value` smallint(5) UNSIGNED NOT NULL,
  `criterium_value_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criterium_question` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multiplier` tinyint(1) DEFAULT NULL,
  `p_id` int(11) DEFAULT NULL,
  `rank` smallint(6) NOT NULL,
  `program` tinyint(1) DEFAULT NULL,
  `q_order` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`cid`),
  KEY `criterium_value` (`criterium_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

DROP TABLE IF EXISTS `guardians`;
CREATE TABLE IF NOT EXISTS `guardians` (
  `gid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `last_name` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plz` int(10) UNSIGNED DEFAULT NULL,
  `siblings` smallint(5) UNSIGNED DEFAULT NULL,
  `parental_status` smallint(5) UNSIGNED DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volume_of_employment` smallint(5) UNSIGNED DEFAULT NULL,
  `status` smallint(5) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`gid`),
  UNIQUE KEY `uid` (`uid`),
  KEY `siblings` (`siblings`),
  KEY `parental_status` (`parental_status`),
  KEY `volume_of_employment` (`volume_of_employment`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
CREATE TABLE IF NOT EXISTS `matches` (
  `mid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`mid`),
  KEY `aid` (`aid`(191),`pid`(191)),
  KEY `pid` (`pid`(191)),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2056 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  KEY `password_resets_email_index` (`email`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

DROP TABLE IF EXISTS `preferences`;
CREATE TABLE IF NOT EXISTS `preferences` (
  `prid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_from` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `pr_kind` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rank` smallint(6) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `isValid` tinyint(1) NOT NULL,
  `invalidReason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`prid`),
  KEY `id_from` (`id_from`(191)),
  KEY `id_to` (`id_to`(191))
) ENGINE=InnoDB AUTO_INCREMENT=58844 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
CREATE TABLE IF NOT EXISTS `programs` (
  `pid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` smallint(5) UNSIGNED DEFAULT NULL,
  `p_kind` tinyint(1) DEFAULT NULL,
  `status` smallint(5) UNSIGNED NOT NULL,
  `coordination` tinyint(1) DEFAULT NULL,
  `uid` int(10) UNSIGNED DEFAULT NULL,
  `proid` int(10) UNSIGNED DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plz` int(10) UNSIGNED DEFAULT NULL,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `age_cohort` smallint(5) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `status` (`status`),
  KEY `uid` (`uid`),
  KEY `proid` (`proid`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

DROP TABLE IF EXISTS `providers`;
CREATE TABLE IF NOT EXISTS `providers` (
  `proid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uid` int(10) UNSIGNED DEFAULT NULL,
  `address` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plz` int(11) DEFAULT NULL,
  `city` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` smallint(5) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`proid`),
  KEY `uid` (`uid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `account_type` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1332 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `providers`
--
ALTER TABLE `providers`
  ADD CONSTRAINT `providers_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `providers_ibfk_2` FOREIGN KEY (`status`) REFERENCES `codes` (`code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
