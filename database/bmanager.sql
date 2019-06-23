-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2019 at 12:59 PM
-- Server version: 10.1.40-MariaDB
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bmanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `account_no` varchar(45) DEFAULT NULL,
  `total_due_payments` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `account_no`, `total_due_payments`) VALUES
(1, 2, '900001', 220000),
(2, 4, '900002', 1400);

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `bank_id` int(11) NOT NULL,
  `bank_name` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`bank_id`, `bank_name`) VALUES
(1, 'EQUITY BANK'),
(2, 'COOPERATIVE'),
(3, 'KCB'),
(4, 'I&M BANK'),
(5, 'NATIONAL BANK');

-- --------------------------------------------------------

--
-- Table structure for table `client_payment_modes`
--

CREATE TABLE `client_payment_modes` (
  `pay_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pay_mode_id` int(11) NOT NULL,
  `pay_bank_id` int(11) DEFAULT NULL,
  `pay_bank_acc` varchar(191) DEFAULT NULL,
  `pay_mpesa_no` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `client_payment_modes`
--

INSERT INTO `client_payment_modes` (`pay_id`, `user_id`, `pay_mode_id`, `pay_bank_id`, `pay_bank_acc`, `pay_mpesa_no`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 1, '0630177743190', NULL, NULL, NULL),
(2, 2, 1, NULL, NULL, '0753434233', '2019-06-22 12:08:51', '2019-06-22 12:08:51'),
(3, 4, 1, 0, NULL, '0773453634', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `investment_id` int(11) NOT NULL,
  `inv_status_id` int(11) NOT NULL DEFAULT '0',
  `inv_date` varchar(191) NOT NULL,
  `trans_id` varchar(11) NOT NULL,
  `account_no_id` int(11) DEFAULT NULL,
  `investment_amount` int(11) DEFAULT NULL,
  `monthly_inv` varchar(11) DEFAULT NULL,
  `compounded_inv` varchar(11) DEFAULT NULL,
  `investment_duration` int(11) NOT NULL,
  `monthly_duration` int(11) DEFAULT NULL,
  `comp_duration` int(11) DEFAULT NULL,
  `inv_type_id` int(11) NOT NULL,
  `inv_mode_id` int(11) NOT NULL,
  `mpesa_trans_code` varchar(191) DEFAULT NULL,
  `inv_bank_id` int(11) DEFAULT NULL,
  `bank_trans_code` varchar(191) DEFAULT NULL,
  `inv_bank_cheq_id` int(11) DEFAULT NULL,
  `cheque_no` varchar(191) DEFAULT NULL,
  `last_pay_date` varchar(191) NOT NULL,
  `initiated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`investment_id`, `inv_status_id`, `inv_date`, `trans_id`, `account_no_id`, `investment_amount`, `monthly_inv`, `compounded_inv`, `investment_duration`, `monthly_duration`, `comp_duration`, `inv_type_id`, `inv_mode_id`, `mpesa_trans_code`, `inv_bank_id`, `bank_trans_code`, `inv_bank_cheq_id`, `cheque_no`, `last_pay_date`, `initiated_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2019-05-30', 'ZWWIRGI8', 1, 110000, NULL, NULL, 12, NULL, NULL, 1, 1, 'FVERRRRRR', 0, NULL, NULL, NULL, '2020-05-30', 1, '2019-06-22 12:21:22', '2019-06-22 11:57:09'),
(2, 1, '2019-06-23', 'WTJO8UVJ', 2, 1000, NULL, NULL, 7, NULL, NULL, 1, 1, 'HFDGYFUHY764', 0, NULL, NULL, NULL, '2020-01-23', 1, '2019-06-23 10:57:31', '2019-06-23 10:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `inv_modes`
--

CREATE TABLE `inv_modes` (
  `id` int(11) NOT NULL,
  `inv_mode` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inv_modes`
--

INSERT INTO `inv_modes` (`id`, `inv_mode`) VALUES
(1, 'MPESA'),
(2, 'BANK ACCOUNT'),
(3, 'PERSONAL CHEQUE'),
(4, 'CASH');

-- --------------------------------------------------------

--
-- Table structure for table `inv_status`
--

CREATE TABLE `inv_status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(191) DEFAULT NULL,
  `status_color` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inv_status`
--

INSERT INTO `inv_status` (`status_id`, `status_name`, `status_color`) VALUES
(1, 'Pending', 'yellow'),
(2, 'Approved', 'green');

-- --------------------------------------------------------

--
-- Table structure for table `inv_types`
--

CREATE TABLE `inv_types` (
  `inv_id` int(11) NOT NULL,
  `inv_type` varchar(191) NOT NULL,
  `inv_desc` varchar(255) DEFAULT NULL,
  `color` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inv_types`
--

INSERT INTO `inv_types` (`inv_id`, `inv_type`, `inv_desc`, `color`) VALUES
(1, 'Monthly', '3', 'red'),
(2, 'Compounded', '6', 'blue'),
(3, 'Monthly + Compounded', '12', 'green');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_05_17_154035_create_permission_tables', 2),
(5, '2018_08_08_100000_create_telescope_entries_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(7, 'App\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'App\\User',
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\User', 1),
(2, 'App\\User', 3),
(3, 'App\\User', 2),
(3, 'App\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `trans_id` varchar(191) NOT NULL,
  `account_no_id` int(11) NOT NULL,
  `payment_amount` int(191) NOT NULL,
  `comp_amount_paid` int(191) DEFAULT NULL,
  `total_paid` int(191) DEFAULT NULL,
  `user_pay_date` varchar(191) NOT NULL,
  `conf_code` varchar(191) DEFAULT NULL,
  `comments` longtext,
  `payment_mode_info_id` int(11) DEFAULT NULL,
  `served_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `trans_id`, `account_no_id`, `payment_amount`, `comp_amount_paid`, `total_paid`, `user_pay_date`, `conf_code`, `comments`, `payment_mode_info_id`, `served_by`, `created_at`, `updated_at`) VALUES
(1, 'XOQWIAZA', 1, 20000, NULL, NULL, '2019-06-30', 'CFGDTDGYUTFTF', 'Client paid through MPESA number 0753325332', 2, 1, '2019-06-22 12:09:33', '2019-06-22 12:09:33'),
(2, 'SGHTDZIG', 1, 22533, NULL, NULL, '2019-07-30', 'uiefueyuuh', 'Paid', 1, 1, '2019-06-22 12:23:41', '2019-06-22 12:23:41');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `method_id` int(11) NOT NULL,
  `method_name` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`method_id`, `method_name`) VALUES
(1, 'MPESA'),
(2, 'BANK ACCOUNT');

-- --------------------------------------------------------

--
-- Table structure for table `payment_schedule`
--

CREATE TABLE `payment_schedule` (
  `id` int(11) NOT NULL,
  `payment_times` int(11) NOT NULL DEFAULT '0',
  `account_no_id` int(11) NOT NULL,
  `inv_type` int(11) NOT NULL,
  `topped_up` int(11) NOT NULL DEFAULT '0',
  `topup_amount` int(191) DEFAULT NULL,
  `tot_payable_amnt` int(11) NOT NULL,
  `monthly_amount` int(11) NOT NULL,
  `tot_comp_amount` int(191) DEFAULT NULL,
  `updated_next_pay` int(191) DEFAULT NULL,
  `updated_monthly_pay` int(191) DEFAULT NULL,
  `comp_monthly_pay` longtext,
  `updated_pay_plan` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_schedule`
--

INSERT INTO `payment_schedule` (`id`, `payment_times`, `account_no_id`, `inv_type`, `topped_up`, `topup_amount`, `tot_payable_amnt`, `monthly_amount`, `tot_comp_amount`, `updated_next_pay`, `updated_monthly_pay`, `comp_monthly_pay`, `updated_pay_plan`) VALUES
(1, 2, 1, 1, 1, 10000, 240000, 20000, NULL, 22533, 22000, NULL, NULL),
(2, 0, 2, 1, 0, NULL, 1400, 200, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'investments.view', 'web', NULL, NULL),
(2, 'investments.approve', 'web', NULL, NULL),
(3, 'topups.view', 'web', NULL, NULL),
(4, 'payments.manage', 'web', NULL, NULL),
(5, 'secretaries.manage', 'web', NULL, NULL),
(6, 'reports.manage', 'web', NULL, NULL),
(7, 'dashboard.data', 'web', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', NULL, NULL),
(2, 'Secretary', 'web', NULL, NULL),
(3, 'User', 'web', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `topups`
--

CREATE TABLE `topups` (
  `topup_id` bigint(20) NOT NULL,
  `account_id` int(11) NOT NULL,
  `topup_amount` int(11) NOT NULL,
  `inv_mode_id` int(11) DEFAULT NULL,
  `mpesa_trans_code` varchar(191) DEFAULT NULL,
  `inv_bank_id` int(11) DEFAULT NULL,
  `bank_trans_code` varchar(191) DEFAULT NULL,
  `inv_bank_cheq_id` int(11) DEFAULT NULL,
  `cheque_no` varchar(191) DEFAULT NULL,
  `topped_at` varchar(191) DEFAULT NULL,
  `served_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `topups`
--

INSERT INTO `topups` (`topup_id`, `account_id`, `topup_amount`, `inv_mode_id`, `mpesa_trans_code`, `inv_bank_id`, `bank_trans_code`, `inv_bank_cheq_id`, `cheque_no`, `topped_at`, `served_by`, `created_at`, `updated_at`) VALUES
(1, 1, 10000, 1, 'FGDFDJFDHF', 0, NULL, NULL, NULL, '2019-06-22', 1, '2019-06-22 12:21:22', '2019-06-22 12:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `refered_by` int(11) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `refered_by`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'FREDRICK OCHIENG', 'fredrick.ochieng@ke.wananchi.com', NULL, NULL, '$2y$10$hL33Y1hKKvIjo41eyK/fVu6fEyyM.EssqdXXq8QZzXbKzQItoE14K', 'lIXpXBFjxXypkiNk9VbSEIGZz2RbbB0P8Id91Bnru24ch4QZSwLeJgqG4ncw', '2019-06-10 17:39:25', '2019-06-21 08:11:41'),
(2, 'FREDRICK OCHIENG', 'fredrick.owuor2014@gmail.com', NULL, NULL, '$2y$10$0xS1QzrWoURBPIY9TigeMelPRo7DxkZEs51iNxVO9o6WcZ5mhNU3.', NULL, '2019-06-22 11:57:09', '2019-06-22 11:57:09'),
(3, 'STEPHANIE ACHIENG', 'stephen.omondi@ke.wananchi.com', NULL, NULL, '$2y$10$0UGNMMJaHXWvaEap1dx2s.EVEra4jk4zaQwTb3Z2KYLDjhw3JfS6u', NULL, '2019-06-22 11:59:43', '2019-06-22 11:59:43'),
(4, 'CHRISTINE ACHIENG', 'christineachieng@gmail.com', 3, NULL, '$2y$10$PU0S/IaM.oGJxeBLPh8uleH.M4VMiW5SNAa1lOhQFkuImg42KTq.u', NULL, '2019-06-23 10:56:53', '2019-06-23 10:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `users_details`
--

CREATE TABLE `users_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `telephone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_town` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kin_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kin_telephone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_details`
--

INSERT INTO `users_details` (`id`, `user_id`, `telephone`, `id_no`, `dob`, `home_address`, `home_town`, `kin_name`, `kin_telephone`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2019-06-21 18:59:30', '2019-06-21 18:59:30'),
(2, 2, '0706534353', '32456464', '2019-05-27', '537, NAIROBI', 'KISUMU', 'CATES CAFE', '0708865489', 1, '2019-06-22 11:57:09', '2019-06-22 11:57:09'),
(3, 3, '0708536349', '32456464', NULL, NULL, NULL, NULL, NULL, 0, '2019-06-22 11:59:43', '2019-06-22 11:59:43'),
(4, 4, '0708536388', '43434343', '2019-05-18', '537, NAIROBI', 'KISUMU', 'CATES CAFE', '0708865489', 1, '2019-06-23 10:56:53', '2019-06-23 10:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_pay_modes`
--

CREATE TABLE `user_pay_modes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pay_mode_id` int(11) NOT NULL,
  `pay_mpesa_no` varchar(191) DEFAULT NULL,
  `pay_bank_id` int(11) DEFAULT NULL,
  `pay_bank_acc` varchar(191) DEFAULT NULL,
  `pay_dates` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_pay_modes`
--

INSERT INTO `user_pay_modes` (`id`, `user_id`, `pay_mode_id`, `pay_mpesa_no`, `pay_bank_id`, `pay_bank_acc`, `pay_dates`) VALUES
(1, 2, 2, NULL, 1, '0630177743190', '[\"2019-06-30\",\"2019-07-30\",\"2019-08-30\",\"2019-09-30\",\"2019-10-30\",\"2019-11-30\",\"2019-12-30\",\"2020-01-30\",\"2020-03-01\",\"2020-04-01\",\"2020-05-01\",\"2020-06-01\"]'),
(2, 4, 1, '0773453634', 0, NULL, '[\"2019-07-23\",\"2019-08-23\",\"2019-09-23\",\"2019-10-23\",\"2019-11-23\",\"2019-12-23\",\"2020-01-23\"]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`bank_id`);

--
-- Indexes for table `client_payment_modes`
--
ALTER TABLE `client_payment_modes`
  ADD PRIMARY KEY (`pay_id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`investment_id`);

--
-- Indexes for table `inv_modes`
--
ALTER TABLE `inv_modes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inv_status`
--
ALTER TABLE `inv_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `inv_types`
--
ALTER TABLE `inv_types`
  ADD PRIMARY KEY (`inv_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`method_id`);

--
-- Indexes for table `payment_schedule`
--
ALTER TABLE `payment_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `topups`
--
ALTER TABLE `topups`
  ADD PRIMARY KEY (`topup_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_details`
--
ALTER TABLE `users_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_pay_modes`
--
ALTER TABLE `user_pay_modes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `client_payment_modes`
--
ALTER TABLE `client_payment_modes`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `investment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inv_status`
--
ALTER TABLE `inv_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_schedule`
--
ALTER TABLE `payment_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `topups`
--
ALTER TABLE `topups`
  MODIFY `topup_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_details`
--
ALTER TABLE `users_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_pay_modes`
--
ALTER TABLE `user_pay_modes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `model_has_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_has_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_details`
--
ALTER TABLE `users_details`
  ADD CONSTRAINT `users_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
