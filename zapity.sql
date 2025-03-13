SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `calls` (
  `id` int(11) NOT NULL,
  `lead_status_id` int(11) NOT NULL DEFAULT 0,
  `call_origin` varchar(24) NOT NULL COMMENT '1=incoming,2=outgoing,3=incoming text, 4=outgoing text',
  `phone_number` bigint(20) NOT NULL,
  `caller_id_name` varchar(255) DEFAULT NULL,
  `call_datetime` datetime DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `message` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `calls_flags_link` (
  `id` int(11) NOT NULL,
  `call_id` int(11) DEFAULT NULL,
  `call_flag_id` int(11) DEFAULT NULL,
  `specify` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `call_flags` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `flag_name` varchar(255) NOT NULL,
  `display_order` int(10) UNSIGNED DEFAULT NULL,
  `specify` int(11) NOT NULL DEFAULT 0,
  `followup` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `call_history_import` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` varchar(100) NOT NULL,
  `call_type` varchar(10) NOT NULL,
  `source_number` varchar(20) NOT NULL,
  `destination_number` varchar(20) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `duration` time NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `call_datetime` datetime NOT NULL,
  `call_status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `calls_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `customer_addresses` (
  `address_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `address_line1` varchar(100) NOT NULL,
  `address_line2` varchar(100) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(50) NOT NULL,
  `address_type` enum('billing','shipping') NOT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `customer_notes` (
  `note_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `customer_phones` (
  `phone_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `phone_type` enum('home','work','mobile') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `customer_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `goal_delivery` date DEFAULT NULL,
  `latest_delivery` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `job_items` (
  `job_item_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `ro` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `job_status_link` (
  `job_status_link_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `job_status_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `status_datetime` datetime NOT NULL,
  `estimated_delivery` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `job_status_lookup` (
  `job_status_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `lead_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `gps_lat` decimal(9,6) DEFAULT NULL,
  `gps_long` decimal(9,6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `location_reps` (
  `id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `rep_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `root` varchar(255) NOT NULL,
  `directory` varchar(255) NOT NULL,
  `date_time_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `photo_links` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `photo_link_type_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `date_time_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `photo_types` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL,
  `is_taxable` tinyint(1) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `product_category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `qb_customers` (
  `Id` int(11) NOT NULL,
  `SyncToken` varchar(10) DEFAULT NULL,
  `domain` varchar(10) DEFAULT NULL,
  `GivenName` varchar(50) DEFAULT NULL,
  `DisplayName` varchar(100) DEFAULT NULL,
  `BillWithParent` tinyint(1) DEFAULT NULL,
  `FullyQualifiedName` varchar(100) DEFAULT NULL,
  `CompanyName` varchar(100) DEFAULT NULL,
  `FamilyName` varchar(50) DEFAULT NULL,
  `sparse` tinyint(1) DEFAULT NULL,
  `PrimaryEmailAddr` varchar(100) DEFAULT NULL,
  `PrimaryPhone` varchar(20) DEFAULT NULL,
  `Active` tinyint(1) DEFAULT NULL,
  `Job` tinyint(1) DEFAULT NULL,
  `BalanceWithJobs` decimal(10,2) DEFAULT NULL,
  `BillAddr_City` varchar(50) DEFAULT NULL,
  `BillAddr_Line1` varchar(100) DEFAULT NULL,
  `BillAddr_PostalCode` varchar(10) DEFAULT NULL,
  `BillAddr_Lat` decimal(10,7) DEFAULT NULL,
  `BillAddr_Long` decimal(10,7) DEFAULT NULL,
  `BillAddr_CountrySubDivisionCode` varchar(10) DEFAULT NULL,
  `BillAddr_Id` int(11) DEFAULT NULL,
  `PreferredDeliveryMethod` varchar(10) DEFAULT NULL,
  `Taxable` tinyint(1) DEFAULT NULL,
  `PrintOnCheckName` varchar(100) DEFAULT NULL,
  `Balance` decimal(10,2) DEFAULT NULL,
  `MetaData_CreateTime` datetime DEFAULT NULL,
  `MetaData_LastUpdatedTime` datetime DEFAULT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `role_permissions` (
  `role_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `todos` (
  `id` int(11) NOT NULL,
  `todo_type_id` int(11) DEFAULT NULL,
  `link_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `updated` datetime DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `assigned_user_id` int(11) DEFAULT NULL,
  `due_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `todo_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(64) NOT NULL,
  `phonenumber` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `user_roles` (
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


ALTER TABLE `calls`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `calls_flags_link`
  ADD PRIMARY KEY (`id`),
  ADD KEY `call_id` (`call_id`),
  ADD KEY `call_flag_id` (`call_flag_id`);

ALTER TABLE `call_flags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`);

ALTER TABLE `call_history_import`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_call_datetime` (`call_datetime`);

ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `type_id` (`type_id`);

ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `customer_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `customer_phones`
  ADD PRIMARY KEY (`phone_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE `customer_types`
  ADD PRIMARY KEY (`type_id`);

ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`);

ALTER TABLE `job_items`
  ADD PRIMARY KEY (`job_item_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `fk_product_id` (`product_id`);

ALTER TABLE `job_status_link`
  ADD PRIMARY KEY (`job_status_link_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `job_status_id` (`job_status_id`);

ALTER TABLE `job_status_lookup`
  ADD PRIMARY KEY (`job_status_id`);

ALTER TABLE `lead_statuses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `location_reps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `photo_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `photo_id` (`photo_id`),
  ADD KEY `photo_link_type_id` (`photo_link_type_id`);

ALTER TABLE `photo_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `product_category`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `qb_customers`
  ADD PRIMARY KEY (`Id`);

ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

ALTER TABLE `role_permissions`
  ADD KEY `role_id` (`role_id`),
  ADD KEY `permission_id` (`permission_id`);

ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `todo_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `user_roles`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);


ALTER TABLE `calls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `calls_flags_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `call_flags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `call_history_import`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_phones`
  MODIFY `phone_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `job_items`
  MODIFY `job_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `job_status_link`
  MODIFY `job_status_link_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `job_status_lookup`
  MODIFY `job_status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lead_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `location_reps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `photo_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `photo_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `todos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `todo_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `calls_flags_link`
  ADD CONSTRAINT `calls_flags_link_ibfk_1` FOREIGN KEY (`call_id`) REFERENCES `calls` (`id`),
  ADD CONSTRAINT `calls_flags_link_ibfk_2` FOREIGN KEY (`call_flag_id`) REFERENCES `call_flags` (`id`);

ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `customer_types` (`type_id`);

ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

ALTER TABLE `customer_notes`
  ADD CONSTRAINT `customer_notes_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

ALTER TABLE `customer_phones`
  ADD CONSTRAINT `customer_phones_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

ALTER TABLE `job_items`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `job_items_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`);

ALTER TABLE `job_status_link`
  ADD CONSTRAINT `job_status_link_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`),
  ADD CONSTRAINT `job_status_link_ibfk_2` FOREIGN KEY (`job_status_id`) REFERENCES `job_status_lookup` (`job_status_id`);

ALTER TABLE `location_reps`
  ADD CONSTRAINT `location_reps_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

ALTER TABLE `photo_links`
  ADD CONSTRAINT `photo_links_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`),
  ADD CONSTRAINT `photo_links_ibfk_2` FOREIGN KEY (`photo_link_type_id`) REFERENCES `photo_types` (`id`);

ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_category` (`id`);

ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`);

ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
