ALTER TABLE  `civicrm_representative_fixedfee`
  ADD COLUMN `date` date NOT NULL AFTER `payment_id`,
  ADD COLUMN `invoice_number` text AFTER `date`;