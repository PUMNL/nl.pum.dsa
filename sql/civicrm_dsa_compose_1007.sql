ALTER TABLE `civicrm_dsa_compose`
  ADD `type` INT NOT NULL AFTER `id`;

UPDATE civicrm_dsa_compose
  SET type=1;