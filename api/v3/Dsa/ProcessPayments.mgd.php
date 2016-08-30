<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Cron:Process DSA Payments',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Process DSA Payments',
      'description' => 'Collect DSA activities in status "Payable", export to FIN and mark "Paid"',
      'run_frequency' => 'Hourly',
      'api_entity' => 'Dsa',
      'api_action' => 'ProcessPayments',
      'parameters' => '',
    ),
  ),
);