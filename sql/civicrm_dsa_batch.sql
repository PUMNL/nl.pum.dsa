CREATE TABLE IF NOT EXISTS `civicrm_dsa_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `importdate` datetime NOT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARACTER SET=utf8 COLLATE='utf8_unicode_ci' COMMENT='nl.pum.dsa';
