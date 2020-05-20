ALTER TABLE `civicrm_dsa_compose`
	 ADD `secondary_approval_cid` INT NULL;
ALTER TABLE `civicrm_dsa_compose`
	 ADD `secondary_approval_datetime` datetime NULL;
ALTER TABLE `civicrm_dsa_compose`
	 ADD `secondary_approval_approved` BOOLEAN DEFAULT NULL;
UPDATE `civicrm_dsa_compose` SET `secondary_approval_approved` = 1, `secondary_approval_cid` = `approval_cid`, `secondary_approval_datetime` = `approval_datetime` WHERE payment_id IS NOT NULL;