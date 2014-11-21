<?php

/**
 * Dsa.GetFinancials API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_dsa_getfinancials_spec(&$spec) {
  $spec['contact_id'] = array(
		'title'			=> 'Contact id',
		'type'			=> 'integer',
		'api.required'	=> 1,
	);
}

/**
 * Dsa.GetFinancials API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_dsa_getfinancials($params) {
	$filtered_params = _civicrm_api3_dsa_getfinancials_filter_params($params);
	if (count($filtered_params) == 0) {
		throw new API_Exception('No accepted parameters found', 1001);
	}

	$dao = _civicrm_api3_dsa_getfinancials_dao($filtered_params);
	$result = array();
	while ($dao->fetch()) {
		// collect data
		$result[] = array(
			'type' => $dao->type,
			'reference' => $dao->case_sequence . ' ' . $dao->case_type . ' ' . $dao->case_country,
			'description' => $dao->subject,
			'payment_type' =>$dao->payment_type,
			'amount' => $dao->total_amount,
			'contact_id' => $dao->contact_id,
			'date' => $dao->payment_datetime,
		);
	}
    return civicrm_api3_create_success($result, $params);
}

function _civicrm_api3_dsa_getfinancials_filter_params($params) {
	$required = array(
		'contact_id',
	);
	$result = array();
	foreach($params as $key => $value) {
		if (in_array($key, $required)) {
			$result[$key] = $value;
		}
	}
	return $result;
}

function _civicrm_api3_dsa_getfinancials_dao($params) {
	$tbl = array(
		'Donor_details_FA'  => _getCustomTableInfo('Donor_details_FA'),	// contains donor (sponsor) code
	);

	$sql = '
SELECT
  \'DSA payment\' type,
  cas.id case_id,
  num.case_sequence,
  num.case_type,
  num.case_country,
  cas.case_type_id,
  act.id act_id,
  act.activity_type_id,
  act.subject,
  act.activity_date_time,
  IF(dsa.type = 1, \'D\', \'C\') payment_type,
  (
    dsa.amount_airport +
    dsa.amount_dsa +
    dsa.amount_briefing +
    dsa.amount_transfer +
    dsa.amount_hotel +
    dsa.amount_visa +
    dsa.amount_medical +
    dsa.amount_other +
    dsa.amount_advance
  ) AS total_amount,
  dsa.invoice_number,
  dsa.credited_activity_id,
  dsa.payment_id,
  pay.`timestamp` payment_datetime,
  ifnull(dsa.donor_id, \'\') donor_id,
  ifnull(dnr.display_name, \'\') donor_name,
  ifnull(' . $tbl['Donor_details_FA']['columns']['Donor_code']['column_name'] . ', \'\') donor_code,
  dsa.contact_id,
  con.display_name
FROM
  civicrm_activity act,
  civicrm_dsa_compose dsa
  LEFT JOIN civicrm_contact dnr ON dnr.id = dsa.donor_id
  LEFT JOIN ' . $tbl['Donor_details_FA']['group_table'] . '
    ON ' . $tbl['Donor_details_FA']['group_table'] . '.entity_id = dnr.id
  LEFT JOIN civicrm_dsa_payment pay ON pay.id = dsa.payment_id
  LEFT JOIN civicrm_contact con ON con.id = dsa.contact_id,
  civicrm_case_activity cac,
  civicrm_case cas
  LEFT JOIN civicrm_case_pum num ON num.entity_id = cas.id
WHERE
  dsa.activity_id = ifnull(
                      act.original_id,
                      act.id) AND
  act.activity_type_id IN (SELECT
                             ovl1.value
                           FROM
                             civicrm_option_group ogp1,
                             civicrm_option_value ovl1
                           WHERE
                             ogp1.name = \'activity_type\' AND
                             ovl1.option_group_id = ogp1.id AND
                             ovl1.name = \'DSA\') AND
  act.status_id IN (SELECT
                      ovl2.value
                    FROM
                      civicrm_option_group ogp2,
                      civicrm_option_value ovl2
                    WHERE
                      ogp2.name = \'activity_status\' AND
                      ovl2.option_group_id = ogp2.id AND
                      ovl2.name = \'dsa_paid\') AND
  act.is_current_revision = 1 AND
  dsa.contact_id = ' . $params['contact_id'] . ' AND
  cac.activity_id = act.id AND
  cas.id = cac.case_id
ORDER BY
  case_sequence,
  payment_datetime
	';
	
	$dao = CRM_Core_DAO::executeQuery($sql);
	
	return $dao;
}