<?php

/**
 * DsaRate.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
//function _civicrm_api3_dsa_rate_get_spec(&$spec) {
//  $spec['country']['api.required'] = 1;
//}

/**
 * DsaRate.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_dsa_rate_get($params) {
	if ($params['date']) {
		$dt = strtotime($params['date']);
		$dt = date('Y-m-d', $dt);
	} else {
		$dt = null;
	}
	if (!array_key_exists('country', $params)) {
		throw new API_Exception('Parameter "country" is missing (need a valid ISO-2 code)'); // already preceeded by validation in API
	} elseif ($params['country'] == '') {
		throw new API_Exception('Parameter "country" is empty (needs a valid ISO-2 code)'); // same validation error
	} elseif (strlen($params['country']) != 2 && !is_numeric($params['country'])) {
		throw new API_Exception('Parameter "country" should contain a valid ISO-2 code or the cicicrm country id');
	} else {
		$returnValues = CRM_Dsa_Page_DSAImport::getActiveCountryRates($params['country'], $dt);
		return civicrm_api3_create_success($returnValues, $params, 'DsaRate', 'Get');
	}
	return;
}

/**
 * Adjust Metadata for Get action
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_dsa_rate_get_spec(&$params) {
	$params['country'] = array(
		'title'			=> 'Country code (id or ISO 3166 "alpha 2" format)',
		'type'			=> 'string',
		'api.required'	=> 1
	);
	$params['date'] = array(
		'title'			=> 'Survey date (defaults to today)',
		'type'			=> 'date time',
	);
}