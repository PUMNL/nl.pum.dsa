CREATE TABLE IF NOT EXISTS `civicrm_pum_scheduling_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `parameter` text NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE='utf8_unicode_ci' AUTO_INCREMENT=1;
