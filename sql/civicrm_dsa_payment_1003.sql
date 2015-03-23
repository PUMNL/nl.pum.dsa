CREATE TABLE IF NOT EXISTS `civicrm_dsa_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `filename` varchar(30) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `filetype` varchar(30) DEFAULT NULL,
  `content` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
