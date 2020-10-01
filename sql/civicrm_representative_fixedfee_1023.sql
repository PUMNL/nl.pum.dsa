CREATE TABLE civicrm_representative_fixedfee (
  `id` int(11) AUTO_INCREMENT NOT NULL,
  `contact_id` int(11) NOT NULL,
  `amount` decimal(7,2) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT = DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;