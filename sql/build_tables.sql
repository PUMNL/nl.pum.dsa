--
-- Table structure for table `civicrm_country_pum`
--

CREATE TABLE IF NOT EXISTS `civicrm_country_pum` (
  `ISO2` varchar(2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Link to civicrm_country',
  `ISO3` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ICSC` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Link to UN DSA tables',
  UNIQUE KEY `ISO2` (`ISO2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='nl.pum.dsa';


--
-- Table structure for table `civicrm_dsa_batch`
--

CREATE TABLE IF NOT EXISTS `civicrm_dsa_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `importdate` datetime NOT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='nl.pum.dsa' ;


--
-- Table structure for table `civicrm_dsa_compose`
--

CREATE TABLE IF NOT EXISTS `civicrm_dsa_compose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL COMMENT 'this table contains additional data for activities of type DSA',
  `contact_id` int(11) NOT NULL COMMENT 'contact id',
  `relationship_type_id` int(11) NOT NULL COMMENT 'defines contacts role in the case',
  `loc_id` int(11) DEFAULT NULL COMMENT 'location id',
  `percentage` int(3) DEFAULT NULL,
  `days` int(3) DEFAULT NULL,
  `amount_dsa` decimal(7,2) DEFAULT NULL,
  `amount_briefing` decimal(7,2) DEFAULT NULL,
  `amount_airport` decimal(7,2) DEFAULT NULL,
  `amount_transfer` decimal(7,2) DEFAULT NULL,
  `amount_hotel` decimal(7,2) DEFAULT NULL,
  `amount_visa` decimal(7,2) DEFAULT NULL,
  `amount_medical` decimal(7,2) DEFAULT NULL,
  `amount_other` decimal(7,2) DEFAULT NULL,
  `description_other` text COLLATE utf8_unicode_ci,
  `amount_advance` decimal(7,2) DEFAULT NULL,
  `ref_date` date NOT NULL COMMENT 'reference date for dsa batch',
  `approval_cid` int(11) DEFAULT NULL COMMENT 'approver contact id',
  `approval_datetime` datetime DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL COMMENT 'payment line id',
  `invoice_number` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_dsa` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_briefing` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_airport` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_transfer` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_hotel` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_visa` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_medical` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_other` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_advance` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credited_activity_id` int(11) DEFAULT NULL COMMENT 'For DSA creditation only: refers to the activity_id of the original payment',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='nl.pum.dsa' ;


--
-- Set existing records (if any) in table `civicrm_dsa_compose` to type=1
--

UPDATE `civicrm_dsa_compose`
  SET `type` = 1
  WHERE `type` IS NULL;

  
--
-- Table structure for table `civicrm_dsa_convert`
--

CREATE TABLE IF NOT EXISTS `civicrm_dsa_convert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(3) COLLATE utf8_unicode_ci NOT NULL COMMENT 'code according to ICSC',
  `code` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `rate` float NOT NULL DEFAULT '-1' COMMENT 'in USD',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='nl.pum.dsa' ;


--
-- Table structure for table `civicrm_dsa_payment`
--

CREATE TABLE IF NOT EXISTS `civicrm_dsa_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `filename` varchar(30) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `filetype` varchar(30) DEFAULT NULL,
  `content` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='nl.pum.dsa' ;


--
-- Table structure for table `civicrm_dsa_rate`
--

CREATE TABLE IF NOT EXISTS `civicrm_dsa_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_id` int(11) NOT NULL COMMENT 'link to civicrm_dsa_batch',
  `country` varchar(3) COLLATE utf8_unicode_ci NOT NULL COMMENT 'code according to ICSC',
  `code` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `rate` float NOT NULL DEFAULT '-1' COMMENT 'in EUR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='nl.pum.dsa' ;


--
-- Table structure for table `civicrm_pum_scheduling_group`
--

CREATE TABLE IF NOT EXISTS `civicrm_pum_scheduling_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


--
-- Table structure for table `civicrm_pum_scheduling_value`
--

CREATE TABLE IF NOT EXISTS `civicrm_pum_scheduling_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `parameter` text COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


--
-- Table structure for table `civicrm_representative_compose`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
