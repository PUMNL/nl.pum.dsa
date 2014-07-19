<?php

global $charMod;

/**
 * Dsa.ProcessPayments API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_dsa_processpayments_spec(&$spec) {
/*
  $spec['magicword']['api.required'] = 1;
*/
}

/**
 * Dsa.ProcessPayments API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_dsa_processpayments($params) {
  
  global $charMod;

  // verify schedule
  // ....
  
  // create new payment record and retrieve its id
  $runTime = time();
  $fileName = 'dsa_' . date("Ymd_Hms", $runTime) . '.txt'; //===== need to add a path; is not root folder of CMS ========================================
  $sqlTime = '\'' . date('Y-m-d H:m:s', $runTime) . '\'';
  $sql = "INSERT INTO civicrm_dsa_payment (timestamp) VALUES (" . $sqlTime . ")";
  $dao = CRM_Core_DAO::executeQuery($sql);
  
  $sql = "SELECT id FROM civicrm_dsa_payment WHERE timestamp=" . $sqlTime;
  $dao = CRM_Core_DAO::executeQuery($sql);
  if (!$dao->N == 1) {
	throw new API_Exception('Could not single out a payment record for timestamp ' . $sqlTime . ' - Export terminated.');
  }
  $result = $dao->fetch();
  $paymentId = $dao->id;
  
  $params = array(
	'version' => 3,
	'q' => 'civicrm/ajax/rest',
	'options' => array(
		'limit' => 0,
	),
	'return' => 'iso_code,name',
  );
  $result = civicrm_api('Country', 'get', $params);
  $country = $result['values'];
  //dpm($country, '$country');
  
  // fetch DSA statusses
  $statusLst = _getDsaStatusList();
  if (!array_key_exists('dsa_payable', $statusLst) || !array_key_exists('dsa_paid', $statusLst)) {
    // cannot look for payable activities or cannot mark them paid
	throw new API_Exception('Mandatory status for DSA is not defined - export terminated.');
  }
  
  // character replacement definitions
  $charMod = _charReplacementBuild();
  
  // new file for export to Financial system (fin)
  $exportFin = fopen($fileName, 'x'); // write mode, error if file exists
  if (!$exportFin) {
	throw new API_Exception('File "' . $fileName . '" already exists - export terminated.');
  }
  
  // empty string to build an overal payment report
  $reportDsa = '';
  
  // empty string to build a payment report for the expert
  $reportExpert = '';
  
  // general ledger ("grootboek') codes
  $gl = _dsa_generalLedgerCodes();
  
  // standard record set (keys in dutch) containing run-specific details ==========================
  // fields not yet in the right order!
  $finrec_std = array(
	'Boekjaar'				=> date('y', $runTime),											// 14 for 2014
	'Dagboek'				=> 'I1',														// I1 for DSA
	'Periode'				=> date('m', $runTime),											// 06 for June
	'Datum'					=> date('d-m-Y', $runTime),										// today
	'Bedrag'				=> '0',															// not in use (10 * "0")
	'Filler1'				=> ' ',															// not in use (9 * " ")
	'Filler2'				=> '',															// not in use (13 * " ")
	'FactuurNrRunType'		=> 'D',															// D for DSA
	
	
	
	'Soort'					=> _dsaSize('', 1, ' ', TRUE),
	'Shortname'				=> _dsaSize('', 1, ' ', TRUE),
	'Rekeninghouder'		=> _dsaSize('', 1, ' ', TRUE),
	'RekeninghouderLand'	=> _dsaSize('', 1, ' ', TRUE),
	'RekeninghouderAdres1'	=> _dsaSize('', 1, ' ', TRUE),
	'RekeninghouderAdres2'	=> _dsaSize('', 1, ' ', TRUE),
	'IBAN'					=> _dsaSize('', 1, ' ', TRUE),
	'Banknaam'				=> _dsaSize('', 1, ' ', TRUE),
	'BankPlaats'			=> _dsaSize('', 1, ' ', TRUE),
	'BankLand'				=> _dsaSize('', 1, ' ', TRUE),
	'BIC'					=> _dsaSize('', 1, ' ', TRUE),
  );
  
  // fetch payable dsa
  $daoDsa = _dao_retrievePayableDsa($statusLst);
  
  // loop: dsa payments
  $warnings = array();
  while ($daoDsa->fetch()) {
	dpm($daoDsa, 'dsa record');
	// check if address (country id), approver id and approval date are available
	if (is_null($daoDsa->country_id)) {
		$warnings[] = 'No primary address found for contact ' . $daoDsa->display_name . ' (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ')';
	} elseif (is_null($daoDsa->approval_datetime) || is_null($daoDsa->approver_name)){
		$warnings[] = 'DSA approval details missing (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ')';
	} else {
//		decide on outfit allowance & fill out field (per project, mission, 6 months or each main activity?)
		
		// add activity specific details ==============================================================
		// fields not yet in the right order!
		// based on (extension of / copy of) the run specific details $finrec_std
		$finrec_act = $finrec_std;
		$finrec_act['Kostendrager']			= $daoDsa->case_country;						// country code main activity
		$finrec_act['Kostenplaats']			= trim(implode(
												array($daoDsa->case_country, $daoDsa->case_sequence, $daoDsa->case_type),
												''));										// project number CCNNNNNT (country, number, type)
		$finrec_act['Sponsorcode']			= 's';											// sponsor code ("10   " for DGIS, where "600  " would be preferred)
		$finrec_act['OmschrijvingA']		= $daoDsa->last_name;							// description fragment: surname
		$finrec_act['OmschrijvingB']		= '';											// description fragment: additional space
		$finrec_act['OmschrijvingC']		= trim(implode(
												array($daoDsa->case_sequence, $daoDsa->case_type, $daoDsa->case_country),
												' '));										// description fragment: main activity number
		$finrec_act['FactuurNrYear']		= '';											// 14 for 2014; date of "preparation", not dsa payment!
		$finrec_act['FactuurNr']			= '';											// sequence based: "123456" would return "2345", "12" would return "0001"
		$finrec_act['FactuurDatum']			= '';											// creation date (dd-mm-yyyy) of DSA activity (in Notes 1st save when in status "preparation")
		$finrec_act['Kenmerk']				= trim(implode(
												array($daoDsa->case_sequence, $daoDsa->case_country),
												''));										// project number NNNNNCC (number, country)
		$finrec_act['CrediteurNr']			= 'sh2';										// experts shortname (8 char)
		$finrec_act['Shortname']			= 'sh1';										// experts shortname (8 char)
		$finrec_act['NaamOrganisatie']		= trim(implode(
												array( $daoDsa->middle_name, $daoDsa->last_name, $daoDsa->Initials),
												' '));										// experts name (e.g. van Oranje-Nassau W.A.)
		$finrec_act['Taal']					= 'N';											// always "N"
		$finrec_act['Land']					= $country[$daoDsa->country_id]['iso_code'];
																							// experts country of residence
		$finrec_act['Adres1']				= $daoDsa->street_address;						// extperts street + number
		$finrec_act['Adres2']				= trim(implode(
												array( $daoDsa->postal_code, $daoDsa->postal_code_suffix, $daoDsa->city), 
												' '));				;						// extperts zip + city
		$finrec_act['BankRekNr']			= ltrim($daoDsa->Account_number, '0');			// experts bank account: number (not IBAN)
		$finrec_act['Soort']				= $daoDsa->case_type;							// main activity (case) type (1 character)
		$finrec_act['Rekeninghouder']		= 'rh';											// bank account holder: name
		$finrec_act['RekeninghouderLand']	= 'xx';											// bank account holder: country
		$finrec_act['RekeninghouderAdres1']	= $finrec_act['Adres1'];						// bank account holder: street + number
		$finrec_act['RekeninghouderAdres2']	= $finrec_act['Adres2'];						// bank account holder: zip + city
		$finrec_act['IBAN']					= $daoDsa->IBAN_number;							// bank account: IBAN
		$finrec_act['Banknaam']				= 'nm';											// bank name
		$finrec_act['BankPlaats']			= 'pl';											// bank city
		$finrec_act['BankLand']				= 'yy';											// bank country
		$finrec_act['BIC']					= $daoDsa->BIC_Swiftcode;						// experts bank account: BIC/Swift code
		
		// loop through the individual payment amounts for this activity
		$amt_type = '';
		for ($n=1; $n<36; $n++) {
			// skip if amount = 0
			// add amount specific details ============================================================
			// fields not yet in the right order!
			// based on (extension of / copy of) the activity specific details $finrec_act
			
			$finrec_amt = $finrec_act;
			$finrec_amt['Boekstuk']			= 'b';											// Sequence; per amount field
			$finrec_amt['DC']				= 'd';											// D for payment, C for full creditation. What about partial creditation (Debriefing DSA)?
			$finrec_amt['PlusMin']			= '+';											// + for payment, - for creditation
			$finrec_amt['FactuurPlusMin']	= '+';											// + for payment, - for creditation
			
			
			$amt_type = ($n<10?$n:chr($n+55)); // 1='1', 2='2, ... 9='9', 10='A', 11='B', ... 35='Z'
			$case_type = $daoDsa->case_name;
			$gl_key = '';
			switch ($amt_type) {
				case '1': // DSA amount
					$amt = _ifnull($daoDsa->amount_dsa, 0);
					switch ($case_type) {
						case 'BLP':
							$gl_key = 'gl_blp';
							break;
						default:
							$gl_key = 'gl_dsa';
					}
					break;

				case '3': // Outfit Allowance
					// additional query required! =============================================================
					$amt = 0; //_ifnull($daoDsa->amount_outfit, 0)
					$gl_key = 'gl_outfit';
					break;

				case '4': // Advance amounts (also: Acquisition Advance Amount)
					$amt = _ifnull($daoDsa->amount_advance, 0);
					switch ($case_type) {
						case 'BLP':
							$gl_key = 'gl_blp_adv';
							break;
						default:
							$gl_key = 'gl_def_adv';
					}
					break;
					
				case '5': // Km PUM briefing (also documented as "reserved for LR remunaration")
					$amt = _ifnull($daoDsa->amount_briefing, 0);
					$gl_key = 'gl_pum_km_brf';
					break;
					
				case '6': // Km PUM debriefing
					$amt = _ifnull($daoDsa->amount_debriefing, 0);
					$gl_key = 'gl_pum_km_debr';
					break;
					
				case '7': // Km Airport (Schiphol)
					$amt = _ifnull($daoDsa->amount_airport, 0);
					$gl_key = 'gl_pum_km_airp';
					break;
					
				case '8': // Transfer amount
					$amt = _ifnull($daoDsa->amount_transfer, 0);
					$gl_key = 'gl_transfer';
					break;
					
				case '9': // Hotel
					$amt = _ifnull($daoDsa->amount_hotel, 0);
					$gl_key = 'gl_hotel';
					break;
					
				case 'A': // Visa
					$amt = _ifnull($daoDsa->amount_visa, 0);
					$gl_key = 'gl_visa';
					break;
					
				case 'B': // Other
					$amt = _ifnull($daoDsa->amount_other, 0);
					$gl_key = 'gl_other';
					break;
					
				case 'C': // Meal / Parking
					$amt = 0;
					$gl_key = '';
					break;
					
				case 'D': // Debriefing settlement
					$amt = 0; // ===============================================================================
					$gl_key = '';
					break;
					
				case 'X': // Training/BLP payment guest
					$amt = 0;
					$gl_key = '';
					break;
					
				case 'Y': // Training/BLP payment expert/organisation costs
					$amt = 0;
					$gl_key = '';
					break;
					
				case 'Z': // Reserved for secondpayment LR remueration
					$amt = 0;
					$gl_key = '';
					break;
					
				default:
					// no action
					$amt = 0;
					$gl_key = '';
			}
			
			if (($amt==0) || ($gl_key=='')) {
				// no action
			} elseif (!array_key_exists($gl_key, $gl)) {
				// need a more controlled way out. E.g. skip amount or skip entire payment record
				// ================================================================================================
				throw exception ('Unknown key for General Ledger: ' . $gl_key);
			} else {
				// continue construction of payment line
				$finrec_amt['GrootboekNr']		= $gl[$gl_key];								// code from _dsa_generalLedgerCodes() - amount specific
				$finrec_amt['FactuurNrAmtType']	= $amt_type;								// represents type of amount in a single character: 1-9, A-Z
				$finrec_amt['FactuurBedrag']	= round($amt, 2, PHP_ROUND_HALF_UP) * 100;	// payment amount in EUR cents (123,456 -> 12346)
				$finrec_amt['ValutaCode']		= 'EUR';									// always EUR
				
				// dpm($finrec_amt, '$finrec_amt');
//dpm(_dsa_concatValues($finrec_amt), '_dsa_concatValues($finrec_amt)'); // ============================================
				//dpm($amt_type);
			}
		
			
	
			// append to temp string
			// append unique number to sql
		}
		
		// write temp string to file
		// add to sql: mark paid
		// execute sql

	} // fetch next payable DSA record
  }
	// close file
	fclose($exportFin);
	
dpm($warnings, 'Warnings'); // ================================================================================

  
  // files:
  // - export for FA
  // - overall payment details
  // - message per expert
  //
  // store file in node
  // ....  
  // mail FA
  // ....
  // delete file

}

/*
 * Function to mimic implode($ar), but implicitly modify the values before concatenation:
 * - replace / remove odd characters
 * - extend the length of values using certain characters (likely ' '  or '0') at either the left or the right of the value (string)
 * - trim each value down to a certain size
 */
function _dsa_concatValues($ar) {
	// field order and size is fixed!
	return 
		_dsaSize($ar['Boekjaar'],				 2, ' ', TRUE) .	// 14 for 2014
		_dsaSize($ar['Dagboek'],				 2, ' ', TRUE) .	// I1 for DSA
		_dsaSize($ar['Periode'],				 2, ' ', TRUE) .	// 06 for June
		_dsaSize($ar['Boekstuk'],				 4, ' ', TRUE) .	// #### sequence (unique per month)
		_dsaSize($ar['GrootboekNr'],			 4, ' ', TRUE) .	// general ledger code
		_dsaSize($ar['Sponsorcode'],			 5, ' ', TRUE) .	// "10   " for DGIS where "610  " would be preferred
		_dsaSize($ar['Kostenplaats'],			 8, ' ', TRUE) .	// project number CCNNNNNT (country, number, type)
		_dsaSize($ar['Kostendrager'],			 8, ' ', TRUE) .	// country code main activity (8 chars!)
		_dsaSize($ar['Datum'],					10, ' ', TRUE) .	// today
		_dsaSize($ar['DC'],						 1, '?', TRUE) .	// D for payment, C for full creditation, but what about partial creditation (DSA debriefing)?
		_dsaSize($ar['PlusMin'],				 1, '?', TRUE) .	// + for payment, - for creditation
		_dsaSize($ar['Bedrag'],					10, '0', FALSE) .	// not in use: 10 digit numeric 0
		_dsaSize($ar['Filler1'],				 9, ' ', TRUE) .	// not in use: 9 spaces
		_dsaSize($ar['OmschrijvingA'],			10, ' ', TRUE) .	// description fragment: surname
		_dsaSize(' ',							 1, ' ', TRUE) .	// description fragment: additional space
		_dsaSize($ar['OmschrijvingB'],			 6, ' ', TRUE) .	// description fragment: main activity number
		_dsaSize($ar['OmschrijvingC'],			 3, ' ', TRUE) .	// description fragment: country
		_dsaSize($ar['Filler2'],				13, ' ', TRUE) .	// not in use: 13 spaces
		_dsaSize($ar['FactuurNrRunType'],		 1, ' ', TRUE) .	// D for DSA
		_dsaSize($ar['FactuurNrYear'],			 2, ' ', TRUE) .	// 14 for 2014; date of "preparation", not dsa payment!
		_dsaSize($ar['FactuurNr'],				 4, ' ', TRUE) .	// sequence based: "123456" would return "2345", "12" would return "0001"
		_dsaSize($ar['FactuurNrAmtType'],		 1, ' ', TRUE) .	// represents type of amount in a single character: 1-9, A-Z
		_dsaSize($ar['FactuurDatum'],			10, ' ', TRUE) .	// currently: creation date (dd-mm-yyyy) of DSA activity (in Notes 1st save when in status "preparation")
		_dsaSize($ar['FactuurBedrag'],			11, '0', FALSE) .	// payment amount in EUR cents (123,456 -> 12346)
		_dsaSize($ar['FactuurPlusMin'],			 1, '?', TRUE) .	// + for payment, - for creditation
		_dsaSize($ar['Kenmerk'],				12, ' ', TRUE) .	// project number NNNNNCC (number, country)
		_dsaSize($ar['ValutaCode'],				 3, ' ', TRUE) .	// always EUR
		_dsaSize($ar['CrediteurNr'],			 8, ' ', TRUE) .	// experts shortname (8 char)
		_dsaSize($ar['NaamOrganisatie'],		35, ' ', TRUE) .	// experts name (e.g. "van Oranje-Nassau W.A.")
		_dsaSize($ar['Taal'],					 1, ' ', TRUE) .	// always "N"
		_dsaSize($ar['Land'],					 3, ' ', TRUE) .	// ISO2
		_dsaSize($ar['Adres1'],					35, ' ', TRUE) .	// experts street + number
		_dsaSize($ar['Adres2'],					35, ' ', TRUE) .	// experts zip + city
		_dsaSize($ar['BankRekNr'],				25, ' ', TRUE) .	// experts bank account: number (not IBAN)
		_dsaSize($ar['Soort'],					 1, ' ', TRUE) .	// main activity (case) type (1 character)
		_dsaSize($ar['Shortname'],				 8, ' ', TRUE) .	// experts shortname (8 char)
		_dsaSize($ar['Rekeninghouder'],			35, ' ', TRUE) .	// bank account holder: name
		_dsaSize($ar['RekeninghouderLand'],		20, ' ', TRUE) .	// bank account holder: country
		_dsaSize($ar['RekeninghouderAdres1'],	35, ' ', TRUE) .	// bank account holder: street + number
		_dsaSize($ar['RekeninghouderAdres2'],	35, ' ', TRUE) .	// bank account holder: zip + city
		_dsaSize($ar['IBAN'],					34, ' ', TRUE) .	// bank account: IBAN
		_dsaSize($ar['Banknaam'],				35, ' ', TRUE) .	// bank name
		_dsaSize($ar['BankPlaats'],				35, ' ', TRUE) .	// bank city
		_dsaSize($ar['BankLand'],				 3, ' ', TRUE) .	// bank country
		_dsaSize($ar['BIC'],					11, 'X', TRUE);		// experts bank account: BIC/Swift code
}

function _getCustomDefinitions() {
	// prepare result array
	$result = array();
	// retrieve table name for custom group "Additional Data"
	$sql = "SELECT id, name, table_name FROM civicrm_custom_group WHERE name = 'Additional_Data'";
	$dao = CRM_Core_DAO::executeQuery($sql);
	if (!$dao->N == 1) {
		return NULL;
	}
	$dao->fetch();
	$group_name = $dao->name;
	$tbl = $dao->table_name;
	$group_id = $dao->id;
	$result[$group_name] = array(
		'table' => $tbl,
		'group_id' => $group_id,
		'columns' => '',
		'fields' => array(),
	);
	// retrieve fieldnames for custom group "Additional Data"
	$sql = "SELECT name, label, column_name FROM civicrm_custom_field WHERE custom_group_id = " . $group_id;
	$dao = CRM_Core_DAO::executeQuery($sql);
	if (!$dao >= 1) {
		return NULL;
	}
	while ($dao->fetch()) {
		$result[$group_name]['columns'] .= 
			($result[$group_name]['columns'] == '' ? '' : (', ' . PHP_EOL)) .
			($tbl . '.' . $dao->column_name . ' as ' . $dao->name);
		$result[$group_name]['fields'][$dao->name] = array(
			'label' => $dao->label,
			'column_name' => $dao->column_name,
		);
	}
	// return all results in an array
	return $result;
}

function _getDsaStatusList() {
	$sql = '
		SELECT	ogv.name, ogv.value
		FROM	civicrm_option_value ogv, civicrm_option_group ogp
		WHERE	ogv.option_group_id = ogp.id
		AND		ogp.name = \'activity_status\'
		AND		ogv.name IN (\'dsa_payable\', \'dsa_paid\')
		';
	$dao = CRM_Core_DAO::executeQuery($sql);
	$result = array();
	while ($dao->fetch()) {
		$result[$dao->name] = $dao->value;
	}
	return $result;
}

/*
 * Function that builds (and returns) an associative array containing
 * - an array of special characters (to be replaced)
 * - an array (same size) of their replacement strings
 */
function _charReplacementBuild() {
	$arCharReplacement = array(
		'originals' => array(),
		'substitutes' => array(),
	);
	_charReplacementAdd($arCharReplacement, 'áàäãâ',	'a');
	_charReplacementAdd($arCharReplacement, 'ÁÀÄÃÂ',	'A');
	_charReplacementAdd($arCharReplacement, 'éèëê',		'e');
	_charReplacementAdd($arCharReplacement, 'ÉÈËÊ',		'E');
	_charReplacementAdd($arCharReplacement, 'íìïî',		'i');
	_charReplacementAdd($arCharReplacement, 'ÍÌÏÎ',		'I');
	_charReplacementAdd($arCharReplacement, 'óòöõôø',	'o');
	_charReplacementAdd($arCharReplacement, 'ÓÒÖÕÔØ',	'O');
	_charReplacementAdd($arCharReplacement, 'úùüû',		'u');
	_charReplacementAdd($arCharReplacement, 'ÚÙÜÛ',		'U');
	_charReplacementAdd($arCharReplacement, 'ýÿ',		'y');
	_charReplacementAdd($arCharReplacement, 'ÝŸ',		'Y');
	_charReplacementAdd($arCharReplacement, 'æ',		'ae');
	_charReplacementAdd($arCharReplacement, 'Æ',		'AE');
	_charReplacementAdd($arCharReplacement, 'ñ',		'n');
	_charReplacementAdd($arCharReplacement, 'Ñ',		'N');
	_charReplacementAdd($arCharReplacement, 'ç',		'c');
	_charReplacementAdd($arCharReplacement, 'Ç',		'C');
	_charReplacementAdd($arCharReplacement, 'ĳ',		'ij');
	_charReplacementAdd($arCharReplacement, 'Ĳ',		'IJ');
	return $arCharReplacement;
}

/*
 * Helper function for _charReplaceBuild()
 * Maps each character in $orgChars to the string in $replacementStr
 */
function _charReplacementAdd(&$arCharReplacement, $orgChars, $replacementStr) {
	for($i=0; $i<mb_strlen($orgChars); $i++) {
		$arCharReplacement['originals'][] = mb_substr($orgChars, $i, 1);
		$arCharReplacement['substitutes'][] = $replacementStr;
	}
}

/*
 * Function to replace all occurances of the elements in array $removeCharAr
 * within string $data by the (1:1) mapped elements in array $replacementCharAr
 */
function charReplacement($data, $removeCharAr, $replacementCharAr) {
	// Warning: $data may either grow or shrink in size
	// step 1: substitition of known characters
	$data = str_replace($removeCharAr, $replacementCharAr, $data); // replace all 'known' special characters by their 'known' replacement strings
	// step 2: removal of all other illegal characters
	$data = PREG_REPLACE("/[^0-9a-zA-Z \/\-]/i", '', $data); // remove all remaining special characters
	return $data;
}

/*
 * Function to resize a value to a certain length for export to a fixed row-size file
 * contains implicit replacement of odd characters
 */
function _dsaSize($value='', $size, $fillChar=' ', $alignLeft=TRUE) {	
	global $charMod;
	// replace odd characters
	$value = charReplacement($value, $charMod['originals'], $charMod['substitutes']);
	// verify or set the desired length of the value
	if(is_numeric($size)) {
		$size = intval($size, 10);
	} else {
		$size = strlen($value);
	}
	// prepare filler string to append to value
	if ($size > strlen($value)) {
		$len = $size - strlen($value);
		$filler = substr(str_repeat($fillChar, $len), 0, $len);
	} else {
		$filler = '';
	}
	// set value to the intended size
	if($alignLeft) {
		$value .= $filler;
	} else {
		$value = $filler . $value;
	}
	$value = substr($value, 0, $size);
	return $value;
}

/*
 * Function to query for payable dsa records along with the experts' details
 * Returns dao-object from which records can be fetched
 */
function _dao_retrievePayableDsa($statusLst) {
	// tables/columns defititions for custom fields for contact type Expert
	// custom data table and columns are added to the basic query to fetch dsa details along with the experts' details
	$custom = _getCustomDefinitions();
//	dpm($custom, 'Custom field definion');

	// query for all active DSA activities in status Payable, scheduled 10 days from now or before
	$sql = '
SELECT
	\'--META-->\' AS \'_META\',
	cac.case_id AS case_id,
	act.id AS act_id,
	con.id AS participant_id,
	dsa.approval_cid AS approver_id,
	\'--CASE-->\' as \'_CASE\',
/*	cas.* */
	ovl3.name as case_name,
	num.case_sequence,
	num.case_type,
	num.case_country ,
	\'--ACTIVITY-->\' AS \'_ACTIVITY\',
	act.*,
	\'--DSA-->\' AS \'_DSA\',
	dsa.loc_id,
	dsa.percentage,
	dsa.days,
	dsa.amount_dsa,
	dsa.amount_briefing,
	dsa.amount_debriefing,
	dsa.amount_airport,
	dsa.amount_transfer,
	dsa.amount_visa,
	dsa.amount_hotel,
	dsa.amount_outfit,
	dsa.amount_other,
	dsa.description_other,
	dsa.amount_advance,
	dsa.approval_datetime,
	dsa.ref_date,
	\'--APPROVER-->\' AS \'_APPROVER\',
	apr.display_name AS approver_name,
	\'--PARTICIPANT->\' AS \'_PARTICIPANT\',
	con.first_name,
	con.middle_name,
	con.last_name,
	con.prefix_id,
	con.suffix_id,
	con.email_greeting_id,
	con.email_greeting_custom,
	con.sort_name,
	con.display_name,
	con.legal_name,
	\'--ADDRESS-->\' AS \'_ADDRESS\',
	adr.street_address,
	adr.supplemental_address_1,
	adr.supplemental_address_2,
	adr.supplemental_address_3,
	adr.city,
	adr.country_id,
	adr.state_province_id,
	adr.postal_code_suffix,
	adr.postal_code,
	adr.usps_adc,
	adr.country_id,
	\'--CUSTOM-->\' AS \'_CUSTOM\',
	' . $custom['Additional_Data']['columns'] . '
FROM
	civicrm_option_group ogp1,
	civicrm_option_value ovl1,
	civicrm_activity act,
	civicrm_dsa_compose dsa
		LEFT JOIN civicrm_contact apr
		ON apr.id = dsa.approval_cid,
	civicrm_case_activity cac,
	civicrm_case cas,
	civicrm_case_pum num,
	civicrm_option_group ogp3,
	civicrm_option_value ovl3,
	civicrm_contact con
		LEFT JOIN civicrm_address adr
		ON adr.contact_id = con.id AND
       adr.is_primary = 1,
	' . $custom['Additional_Data']['table'] . '
WHERE
	ogp1.name = \'activity_type\' AND
	ovl1.option_group_id = ogp1.id AND
	ovl1.name = \'DSA\' AND
	act.activity_type_id = ovl1.value AND
	act.is_current_revision = 1 AND /* all `current` DSA activities */
	date( act.activity_date_time) <= DATE_ADD(curdate(), INTERVAL 10 DAY) AND /* scheduled in 10 day or before */
	act.status_id IN (
		SELECT
			ovl2.value
		FROM
			civicrm_option_group ogp2,
			civicrm_option_value ovl2
		WHERE
			ogp2.name = \'activity_status\' AND
			ovl2.option_group_id = ogp2.id AND
			ovl2.name = \'dsa_payable\') AND /* ready for payment */
	dsa.activity_id = ifnull(act.original_id, act.id) AND /* join data in civicrm_dsa_compose table */
	cac.activity_id = act.id AND
	cas.id = cac.case_id AND /* join case data through case activities */
	num.entity_id = cas.id AND /* join main activity (case) numbering */
	ogp3.name = \'case_type\' AND
	ovl3.option_group_id = ogp3.id AND
	ovl3.value = cas.case_type_id AND /* join case type name */
	con.id = dsa.contact_id AND /* join contact data - will drop DSA if not found */
	' . $custom['Additional_Data']['table'] . '.entity_id = con.id /* additional custom data for contact */
ORDER BY
	con.id
	';
	dpm(array($sql), 'DSA Payment $sql');
  
	$dao = CRM_Core_DAO::executeQuery($sql);
	
	return $dao;
}

/* Function to provide general ledger codes
 * should be replaced by a pre-filled table + query
 */
function _dsa_generalLedgerCodes() {
	$params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'option_group_name' => 'general_ledger',
		'return' => 'name,value',
		'options' => array(
			'limit' => 0,
		),
	);
	$result = civicrm_api('OptionValue', 'get', $params);
	//dpm($result, 'API result');
	$code_tbl = array();
	//dpm($result['values'], 'API values');
	foreach($result['values'] as $value) {
		$code_tbl[$value['name']] = $value['value'];
	}
	dpm($code_tbl, 'general ledger codes');
	return $code_tbl;
	/*
	'LR'						=> '493',
	'DSA_VOORSCHOTBLP'			=> '1411',
	'DSA_BLP' 					=> '5600',
	'DSA_BLP_KM'				=> '5640',
	'DSA_VOORSCHOTTRAINING'		=> '1410',
	'DSA_TRAINING'				=> '5500',	
	'DSA_TRAINING_KM'			=> '5540',
	'DSA' 						=> '5100',
	'DSA_OUTFIT'				=> '5110',
	'DSA_VOORSCHOTDEFAULT'		=> '1412',
	'DSA_KMPUM'					=> '5140',	// Briefing
	'DSA_KMPUM_DEBRIEFING'		=> '5141',	// Debriefing
	'DSA_KMSCHIPHOL'			=> '5020',
	'DSA_TRANSFER'				=> '5000',
	'DSA_HOTEL'					=> '5120',
	'DSA_VISA'					=> '5030',
	'DSA_OTHER'					=> '5130',
	'DSA_MEALPARKING'			=> '5133',
	'DSA_DEBRIEFINGVERREKENING'	=> '5105',
	'DSA_DUMMY'					=> '9999',
	*/
}

/*
 * Equivalent function for mySQL 'ifnull(a,b)'
 * if returns a if not NULL, otherwise returns b
 */
function _ifnull($a, $b=null) {
    return is_null($a)?$b:$a;
}