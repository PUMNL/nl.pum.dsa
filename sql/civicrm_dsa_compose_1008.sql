ALTER TABLE `civicrm_dsa_compose`
   DROP `amount_debriefing`,
 CHANGE `amount_outfit` `amount_medical` DECIMAL( 7, 2 ) NULL DEFAULT NULL;