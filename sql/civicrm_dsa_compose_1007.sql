ALTER TABLE `civicrm_dsa_compose`
  ADD `type` INT NOT NULL;

UPDATE civicrm_dsa_compose
  SET type=1
  WHERE type IS NULL;