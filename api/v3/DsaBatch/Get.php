<?php

/**
 * DsaBatch.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_dsa_batch_get($params) {
  $returnValues = CRM_Dsa_Page_DSAImport::getAllDSABatches();
  return civicrm_api3_create_success($returnValues, $params, 'DsaBatch', 'Get');
}

/**
 * Adjust Metadata for Get action
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_dsa_batch_get_spec(&$params) {
  $params['id'] = array(
    'title' => 'ID of the imported DSA batch',
    'type'  => CRM_Utils_Type::T_INT,
  );
  $params['importdate'] = array(
    'title' => 'Date/time of DSA batch import',
    'type'  => 'date time',
  );
  $params['startdate'] = array(
    'title' => 'Date/time when DSA batch becomes effective',
    'type'  => 'date',
  );
  $params['enddate'] = array(
    'title' => 'Date/time when DSA batch is no longer effective',
    'type'  => 'date',
  );
}