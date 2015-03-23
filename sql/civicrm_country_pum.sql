CREATE TABLE IF NOT EXISTS `civicrm_country_pum` (
  `ISO2` varchar(2) NOT NULL COMMENT 'Link to civicrm_country',
  `ISO3` varchar(3),
  `ICSC` varchar(3) COMMENT 'Link to UN DSA tables',
  UNIQUE KEY `ISO2` (`ISO2`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE='utf8_unicode_ci' COMMENT='nl.pum.dsa'
