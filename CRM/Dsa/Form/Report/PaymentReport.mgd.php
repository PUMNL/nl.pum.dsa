<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Dsa_Form_Report_PaymentReport',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'PaymentReport',
      'description' => 'PaymentReport (nl.pum.dsa)',
      'class_name' => 'CRM_Dsa_Form_Report_PaymentReport',
      'report_url' => 'nl.pum.dsa/paymentreport',
      'component' => '',
    ),
  ),
);