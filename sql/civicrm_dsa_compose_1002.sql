ALTER TABLE `civicrm_dsa_compose`
	DROP `pid`,
	ADD `ref_date` DATE NOT NULL COMMENT 'reference date for dsa batch';
