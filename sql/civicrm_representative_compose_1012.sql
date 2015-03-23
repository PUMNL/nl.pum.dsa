CREATE TABLE IF NOT EXISTS `civicrm_representative_compose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL COMMENT 'this table contains additional data for activities of type Representative payment',
  `contact_id` int(11) NOT NULL COMMENT 'contact id',
  `relationship_type_id` int(11) NOT NULL COMMENT 'defines contacts role in the case',
  `amount_rep` decimal(7,2) DEFAULT NULL,
  `approval_cid` int(11) DEFAULT NULL COMMENT 'approver contact id',
  `approval_datetime` datetime DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL COMMENT 'payment line id',
  `invoice_number` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_rep` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;
