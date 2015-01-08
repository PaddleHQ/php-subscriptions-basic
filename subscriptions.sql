CREATE TABLE `subscriptions` (
  `subscription_id` int(11) unsigned NOT NULL,
  `plan_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `next_bill_date` date DEFAULT NULL,
  `update_url` text,
  `cancel_url` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;