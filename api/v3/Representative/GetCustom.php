<?php

/**
 * Representative.GetCustom API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_representative_getcustom_spec(&$spec) {
  $spec['activity_id'] = array(
    'title'     => 'Activity id',
    'type'      => 'integer',
    'api.required'  => 1,
  );
}

/**
 * Representative.GetCustom API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
  */
function civicrm_api3_representative_getcustom($params) {
  $sql = '
SELECT
  rep.*
FROM
  civicrm_activity act,
  civicrm_representative_compose rep
WHERE
  act.id = ' . $params['activity_id'] . ' AND
  rep.activity_id = ifnull(act.original_id, act.id)
  ';
  $dao = CRM_Core_DAO::executeQuery($sql);
  $result = array();
  $cols = civicrm_api3_representative_columns();
  while ($dao->fetch()) {
    $rec = array();
    foreach($cols as $col) {
      if (property_exists($dao, $col)) {
        $rec[$col] = $dao->$col;
      }
    }
    $result[] = $rec;
  }
  return civicrm_api3_create_success($result, $params, 'Dsa', 'GetCustom');
}

function civicrm_api3_representative_columns() {
  return array(
    'id',
    'type',
    'case_id',
    'activity_id',
    'contact_id',
    'relationship_type_id',
    'amount_rep',
    'approval_cid',
    'approval_datetime',
    'payment_id',
    'invoice_number',
    'invoice_rep',
    'comments',
    'donor_id',
  );
}
