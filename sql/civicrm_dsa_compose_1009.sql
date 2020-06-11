ALTER TABLE `civicrm_dsa_compose`
  ADD `invoice_number` VARCHAR( 7 ) NULL,
  ADD `invoice_dsa` VARCHAR( 1 ) NULL,
  ADD `invoice_briefing` VARCHAR( 1 ) NULL,
  ADD `invoice_airport` VARCHAR( 1 ) NULL,
  ADD `invoice_transfer` VARCHAR( 1 ) NULL,
  ADD `invoice_hotel` VARCHAR( 1 ) NULL,
  ADD `invoice_visa` VARCHAR( 1 ) NULL,
  ADD `invoice_medical` VARCHAR( 1 ) NULL,
  ADD `invoice_other` VARCHAR( 1 ) NULL,
  ADD `invoice_advance` VARCHAR( 1 ) NULL;
