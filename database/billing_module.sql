CREATE TABLE IF NOT EXISTS `billing_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `billing_settings` (`setting_key`, `setting_value`) VALUES
('company_name', 'PIYAN NETWORK'),
('auto_isolate', '1'),
('isolate_profile', 'ISOLIR'),
('default_due_day', '10'),
('wa_reminder', '1');

CREATE TABLE IF NOT EXISTS `billing_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(100) NOT NULL,
  `mikrotik_profile` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `speed` varchar(50) DEFAULT NULL,
  `billing_cycle` int(11) DEFAULT 30,
  `description` text,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `billing_customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_number` varchar(50) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `address` text,
  `profile_id` int(11) DEFAULT NULL,
  `mikrotik_username` varchar(100) DEFAULT NULL,
  `mikrotik_password` varchar(100) DEFAULT NULL,
  `router_session` varchar(100) DEFAULT NULL,
  `billing_day` int(11) DEFAULT 10,
  `status` enum('active','isolated','inactive') DEFAULT 'active',
  `is_isolated` tinyint(1) DEFAULT 0,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `billing_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(60) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `period` varchar(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('unpaid','paid','overdue','cancelled') DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `billing_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL,
  `paid_by` varchar(100) DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;