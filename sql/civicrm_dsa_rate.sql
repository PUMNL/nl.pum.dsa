CREATE TABLE IF NOT EXISTS `civicrm_dsa_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_id` int(11) NOT NULL COMMENT 'link to civicrm_dsa_batch',
  `country` varchar(3) NOT NULL COMMENT 'code according to ICSC',
  `code` varchar(4) NOT NULL,
  `location` varchar(70) NOT NULL,
  `rate` float NOT NULL DEFAULT '-1' COMMENT 'in EUR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE='utf8_unicode_ci' AUTO_INCREMENT=1  COMMENT='nl.pum.dsa';
