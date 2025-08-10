-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 01, 2025 at 08:28 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Neo', '2025-07-18 23:35:15', '2025-07-18 23:35:15'),
(2, 'Project', '2025-07-18 23:35:15', '2025-07-18 23:35:15'),
(3, 'Internal', '2025-07-18 23:35:15', '2025-07-18 23:35:15'),
(4, 'External', '2025-07-18 23:35:15', '2025-07-18 23:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `comment_training`
--

CREATE TABLE `comment_training` (
  `id` bigint UNSIGNED NOT NULL,
  `training_record_id` bigint UNSIGNED NOT NULL,
  `approval` enum('Approved','Pending','Reject') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comment_training`
--

INSERT INTO `comment_training` (`id`, `training_record_id`, `approval`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 'Pending', NULL, '2025-07-18 23:43:14', '2025-07-18 23:43:14'),
(2, 1, 'Approved', 'ok', '2025-08-01 01:10:18', '2025-08-01 01:10:18'),
(3, 2, 'Pending', NULL, '2025-08-01 01:13:28', '2025-08-01 01:13:28'),
(4, 3, 'Pending', NULL, '2025-08-01 01:16:32', '2025-08-01 01:16:32'),
(5, 3, 'Approved', 'OK', '2025-08-01 01:17:43', '2025-08-01 01:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_peserta`
--

CREATE TABLE `hasil_peserta` (
  `id` bigint UNSIGNED NOT NULL,
  `peserta_id` bigint UNSIGNED NOT NULL,
  `training_record_id` bigint UNSIGNED NOT NULL,
  `theory_result` enum('Pass','Fail','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `practical_result` enum('Pass','Fail','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('1','2','3','4','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_judgement` enum('Competence','Attend','NA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hasil_peserta`
--

INSERT INTO `hasil_peserta` (`id`, `peserta_id`, `training_record_id`, `theory_result`, `practical_result`, `level`, `final_judgement`, `license`, `certificate`, `expired_date`, `category`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Pass', 'Pass', '1', 'Attend', '1', NULL, NULL, NULL, NULL, NULL),
(2, 2, 2, 'Pass', 'Pass', '1', 'Attend', '0', NULL, NULL, NULL, NULL, NULL),
(3, 3, 3, 'Pass', 'Pass', '1', 'Attend', '0', NULL, NULL, NULL, NULL, NULL),
(4, 2, 3, 'Pass', 'Pass', '3', 'Attend', '1', NULL, NULL, NULL, NULL, NULL),
(5, 1, 3, 'Pass', 'Pass', '1', 'Attend', '1', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '2024_08_03_014119_training_skill_table', 1),
(4, '2024_08_04_113629_create_categories_table', 1),
(5, '2024_08_04_113723_create_training_records_table', 1),
(6, '2024_08_04_113731_create_pesertas_table', 1),
(7, '2024_08_05_130546_training_record_peserta', 1),
(8, '2024_08_22_104558_create_personal_access_tokens_table', 1),
(9, '2025_04_14_023050_training_record_training_skill', 1),
(10, '2025_04_15_041539_commant_training', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesertas`
--

CREATE TABLE `pesertas` (
  `id` bigint UNSIGNED NOT NULL,
  `badge_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dept` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `join_date` date NOT NULL,
  `status` enum('Active','Non Active') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `user_id_login` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesertas`
--

INSERT INTO `pesertas` (`id`, `badge_no`, `employee_name`, `dept`, `position`, `join_date`, `status`, `gender`, `category_level`, `user_id`, `user_id_login`, `created_at`, `updated_at`) VALUES
(1, 'SA001', 'Reina Nikolaus', 'IT', 'Head of IT', '2020-07-19', 'Active', 'Male', 'Level 4', 1, 1, '2025-07-18 23:35:52', '2025-07-18 23:35:52'),
(2, 'AD001', 'Admin HR', 'HR', 'HR Supervisor', '2021-07-19', 'Active', 'Female', 'Level 3', 1, 2, '2025-07-18 23:35:52', '2025-07-18 23:35:52'),
(3, 'B001', 'Peserta 1', 'Production', 'Operator', '2024-07-19', 'Active', 'Male', 'Level 1', 2, 3, '2025-07-18 23:35:53', '2025-07-18 23:35:53'),
(4, 'B002', 'Peserta 2', 'Production', 'Operator', '2024-07-19', 'Active', 'Female', 'Level 1', 2, 4, '2025-07-18 23:35:53', '2025-07-18 23:35:53'),
(5, 'B003', 'Peserta Tanpa Akun', 'Quality', 'Inspector', '2023-07-19', 'Active', 'Female', 'Level 2', 2, NULL, '2025-07-18 23:35:53', '2025-07-18 23:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('30woncjxjqpVgpUyHEIwbQ2xHoZ6Aolk8jDCJXYj', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaGgwNGxVZkFVeWZBNmE2RVFURU1JNTJDZzFsdEYwRXFzZVY0dFZ5eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo5OiJ1c2VyX25hbWUiO047fQ==', 1754036264);

-- --------------------------------------------------------

--
-- Table structure for table `training_records`
--

CREATE TABLE `training_records` (
  `id` bigint UNSIGNED NOT NULL,
  `doc_ref` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `training_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trainer_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rev` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `station` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Completed','Pending','Waiting Approval') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Waiting Approval',
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `training_duration` time DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_records`
--

INSERT INTO `training_records` (`id`, `doc_ref`, `training_name`, `trainer_name`, `rev`, `station`, `status`, `date_start`, `date_end`, `training_duration`, `attachment`, `category_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Doc12', 'Test', 'Budi', '32', 'NA', 'Completed', '2025-07-19', '2025-07-19', '02:00:00', 'attachment/62-Article+Text-92-1-10-20200224.pdf', 1, 1, '2025-07-18 23:43:14', '2025-08-01 01:10:18'),
(2, 'PAD02yi', 'Training 1', 'Budi', '32', 'Test', 'Pending', '2025-08-12', '2025-08-13', '02:00:00', 'attachment/test.pdf', 1, 1, '2025-08-01 01:13:27', '2025-08-01 01:13:27'),
(3, 'PAD02yi2', 'Training 2', 'Budi', '32', 'Test', 'Completed', '2025-08-12', '2025-08-13', '02:00:00', 'attachment/test.pdf', 2, 1, '2025-08-01 01:16:32', '2025-08-01 01:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `training_record_training_skill`
--

CREATE TABLE `training_record_training_skill` (
  `id` bigint UNSIGNED NOT NULL,
  `training_skill_id` bigint UNSIGNED NOT NULL,
  `training_record_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_record_training_skill`
--

INSERT INTO `training_record_training_skill` (`id`, `training_skill_id`, `training_record_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 2, NULL, NULL),
(3, 2, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training_skill`
--

CREATE TABLE `training_skill` (
  `id` bigint UNSIGNED NOT NULL,
  `job_skill` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skill_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_skill`
--

INSERT INTO `training_skill` (`id`, `job_skill`, `skill_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Not Found', 'NA', '2025-07-18 23:41:48', '2025-07-18 23:41:48', NULL),
(2, 'Test', 'V1', '2025-08-01 01:14:31', '2025-08-01 01:14:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Super Admin','Admin','User') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'User',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'Super Admin', '$2y$12$e8Bxc2NIvvdScokgZvWnVeX8gZLWYhyr7q5Zr0gtZdVcuQPkkRW1W', '2025-07-18 23:35:52', '2025-07-18 23:35:52'),
(2, 'admin', 'Admin', '$2y$12$hMByU/drAm8xbRSzlpXnC.g7Ap8XwX0a9Vp1fryO987hSxKNCk5V2', '2025-07-18 23:35:52', '2025-07-18 23:35:52'),
(3, 'peserta1', 'User', '$2y$12$WiaoxBgYoehSrM9XHgEv/.GUU17i.SX0cWV3mdkJVOh4swwMcPLxS', '2025-07-18 23:35:53', '2025-07-18 23:35:53'),
(4, 'peserta2', 'User', '$2y$12$3reNtZGtgxkREI9G81TlpO8jjfES3UokI7fw7irhOW..BeUhqmAOO', '2025-07-18 23:35:53', '2025-07-18 23:35:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_training`
--
ALTER TABLE `comment_training`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_training_training_record_id_foreign` (`training_record_id`);

--
-- Indexes for table `hasil_peserta`
--
ALTER TABLE `hasil_peserta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hasil_peserta_peserta_id_foreign` (`peserta_id`),
  ADD KEY `hasil_peserta_training_record_id_foreign` (`training_record_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`user`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pesertas`
--
ALTER TABLE `pesertas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesertas_badge_no_unique` (`badge_no`),
  ADD KEY `pesertas_user_id_foreign` (`user_id`),
  ADD KEY `pesertas_user_id_login_foreign` (`user_id_login`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `training_records`
--
ALTER TABLE `training_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_records_category_id_foreign` (`category_id`),
  ADD KEY `training_records_user_id_foreign` (`user_id`);

--
-- Indexes for table `training_record_training_skill`
--
ALTER TABLE `training_record_training_skill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_record_training_skill_training_record_id_foreign` (`training_record_id`),
  ADD KEY `training_record_training_skill_training_skill_id_foreign` (`training_skill_id`);

--
-- Indexes for table `training_skill`
--
ALTER TABLE `training_skill`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comment_training`
--
ALTER TABLE `comment_training`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hasil_peserta`
--
ALTER TABLE `hasil_peserta`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesertas`
--
ALTER TABLE `pesertas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `training_records`
--
ALTER TABLE `training_records`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `training_record_training_skill`
--
ALTER TABLE `training_record_training_skill`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `training_skill`
--
ALTER TABLE `training_skill`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment_training`
--
ALTER TABLE `comment_training`
  ADD CONSTRAINT `comment_training_training_record_id_foreign` FOREIGN KEY (`training_record_id`) REFERENCES `training_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hasil_peserta`
--
ALTER TABLE `hasil_peserta`
  ADD CONSTRAINT `hasil_peserta_peserta_id_foreign` FOREIGN KEY (`peserta_id`) REFERENCES `pesertas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_peserta_training_record_id_foreign` FOREIGN KEY (`training_record_id`) REFERENCES `training_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pesertas`
--
ALTER TABLE `pesertas`
  ADD CONSTRAINT `pesertas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pesertas_user_id_login_foreign` FOREIGN KEY (`user_id_login`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `training_records`
--
ALTER TABLE `training_records`
  ADD CONSTRAINT `training_records_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `training_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_record_training_skill`
--
ALTER TABLE `training_record_training_skill`
  ADD CONSTRAINT `training_record_training_skill_training_record_id_foreign` FOREIGN KEY (`training_record_id`) REFERENCES `training_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_record_training_skill_training_skill_id_foreign` FOREIGN KEY (`training_skill_id`) REFERENCES `training_skill` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
