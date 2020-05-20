<?php

/**
 * Class with general static util functions for dsa and rep payments
 *
 * @author Ralph Kersten
 * @license AGPL-V3.0
 */

class CRM_Dsa_Utils {
  /**
   * Function to check if user is allowed to edit dsa data (not status)
   *
   * @param int $activityId
   * @return boolean
   * @access public
   * @static
   */
  static function canEditDsaData($activityId) {
	$result = self::canEditDsa($activityId);
	return $result['editDsaData'];
  }

  /**
   * Function to check if user is allowed to edit dsa status (not data)
   *
   * @param int $activityId
   * @return boolean
   * @access public
   * @static
   */
  static function canEditDsaStatus($activityId) {
    $result = self::canEditDsa($activityId);
	return $result['editDsaStatus'];
  }

  /**
   * Function to check if user is allowed to set dsa status to dsa_payable
   *
   * @param int $activityId
   * @return boolean
   * @access public
   * @static
   */
  static function canApproveDsa($activityId) {
    $result = self::canEditDsa($activityId);
	return $result['editDsaApprove'];
  }

  /**
   * Function to check if user is allowed to edit representative payment data (not status)
   *
   * @param int $activityId
   * @return boolean
   * @access public
   * @static
   */
  static function canEditRepData($activityId) {
	$result = self::canEditDsa($activityId);
	return $result['editRepData'];
  }

  /**
   * Function to check if user is allowed to edit representative payment status (not data)
   *
   * @param int $activityId
   * @return boolean
   * @access public
   * @static
   */
  static function canEditRepStatus($activityId) {
    $result = self::canEditDsa($activityId);
	return $result['editRepStatus'];
  }

  /**
   * Function to check if user is allowed to set representative payment status to dsa_payable
   *
   * @param int $activityId
   * @return boolean
   * @access public
   * @static
   */
  static function canApproveRep($activityId) {
    $result = self::canEditDsa($activityId);
	return $result['editRepApprove'];
  }

  /**
   * Function to check if user is allowed to edit dsa (or representative payment) data, status and approve payment
   *
   * @param int $activityId
   * @return array of boolean
   * @access public
   * @static
   */
  static function canEditDsa($activityId) {
	// by default allow nothing
	$canEdit = array(
		'editDsaData'    => FALSE, // modify amounts etc.
		'editDsaStatus'  => FALSE, // modify status
		'editDsaApprove' => FALSE, // allow change to status Payable
		'editRepData'    => FALSE, // modify amounts etc.
		'editRepStatus'  => FALSE, // modify status
		'editRepApprove' => FALSE, // allow change to status Payable
	);

	// when no activityId is provided, assume the creation of new a activity (not sure which type)
	if (empty($activityId)) {
		$canEdit['editDsaData'] = CRM_Core_Permission::check('create DSA activity');
		$canEdit['editDsaStatus'] = $canEdit['editDsaData'];
		$canEdit['editDsaApprove'] = CRM_Core_Permission::check('approve DSA activity');
		$canEdit['editRepData'] = CRM_Core_Permission::check('create Representative payment activity');
		$canEdit['editRepStatus'] = $canEdit['editRepData'];
		$canEdit['editRepApprove'] = CRM_Core_Permission::check('approve Representative payment activity');
		/************************************************************
		* code contains a risk:
		* we cannot determine if the activity is a creditation
		* in creditations editing is not allowed!
		************************************************************/
		return $canEdit;
	}

	// beyond this point: activityId provided;

	$actDetails = self::getActivityDetails($activityId);
	try {
		if (is_null($actDetails)) {
			// invalid $activityId
			return $canEdit;
		} else {
			// activity found
			// add status name and based on the status id
			$actStatus = $actDetails['activity_status_name'];
		}

		// custom data (if available) should tell wether it is a payment (type=1) or creditation (type=3)
		// however, custom data may be unavailable!
		$actDebCred = 0;
		if (array_key_exists('custom', $actDetails)) {
			if (array_key_exists('type', $actDetails['custom'])) {
				$actDebCred = $actDetails['custom']['type'];
			}
		}
		/*************************************************************************************************
		 * code contains a risk:
		 * if an activity is created though civi workflow (xml), the custom data may still be unavailable
		 * in that case $actDebCred cannot determine if the activity is a creditation
		 * in creditations editing is not allowed!
		 *************************************************************************************************/

		// if status = paid then no changes are allowed
		if ($actStatus == 'dsa_paid') {
			return $canEdit;
		}

		// if status is payable or the activity is a creditation, only allow the status to be changed; not the data
		if ( ($actStatus == 'dsa_payable') || ($actDebCred == 3) ) {
			$canEdit['editDsaStatus'] = CRM_Core_Permission::check('edit DSA activity');
			$canEdit['editRepStatus'] = CRM_Core_Permission::check('edit Representative payment activity');
			// if allowed to edit, also allow save under (unchanged) status dsa_payable
			$canEdit['editDsaApprove'] = $canEdit['editDsaStatus'];
			$canEdit['editRepApprove'] = $canEdit['editRepStatus'];
			return $canEdit;
		}

		// in other (normal) statusses, only the privileges define what can be done
		$canEdit['editDsaData'] = CRM_Core_Permission::check('create DSA activity');
		$canEdit['editDsaStatus'] = $canEdit['editDsaData'];
		$canEdit['editDsaApprove'] = CRM_Core_Permission::check('approve DSA activity');
		$canEdit['editRepData'] = CRM_Core_Permission::check('create Representative payment activity');
		$canEdit['editRepStatus'] = $canEdit['editRepData'];
		$canEdit['editRepApprove'] = CRM_Core_Permission::check('approve Representative payment activity');
		return $canEdit;

	} catch (CiviCRM_API3_Exception $ex) {
		return $canEdit;
	}
  }

  /**
   * Function to retrieve an array of available activity statuses (value->name)
   *
   * @param  n.a.
   * @return array[int status_value -> string status_name]
   * @access public
   * @static
   */
  static function getActivityStatusOptions() {
	return(self::getOptionValueList('activity_status'));
  }

  /**
   * CRM_Dsa_Utils::getActivityStatusScheduled()
   *
   * Function to get id of activity status scheduled
   *
   * @return $result['id']
   */
  static function getActivityStatusScheduled() {
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'option_group_name' => 'activity_status',
        'name' => 'Scheduled',
      );
      $result = civicrm_api('OptionValue', 'getsingle', $params);
    } catch (CiviCRM_API3_Exception $ex) {

    }
    if(!empty($result['value'])) {
      return $result['value'];
    } else {
      return FALSE;
    }
  }

  /**
   * CRM_Dsa_Utils::getActivityStatusPayable()
   *
   * Function to get id of activity status payable
   *
   * @return $result['id']
   */
  static function getActivityStatusPayable() {
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'option_group_name' => 'activity_status',
        'name' => 'dsa_payable',
      );
      $result = civicrm_api('OptionValue', 'getsingle', $params);
    } catch (CiviCRM_API3_Exception $ex) {

    }
    if(!empty($result['value'])) {
      return $result['value'];
    } else {
      return FALSE;
    }
  }

  /**
   * Function to retrieve an array of available activity types (value->name)
   *
   * @param  n.a.
   * @return array[int activity_type_value -> string activity_type_name]
   * @access public
   * @static
   */
  static function getActivityTypeOptions() {
	return(self::getOptionValueList('activity_type'));
  }

  /**
   * Function to retrieve an array of available activity types (value->name)
   *
   * @param  string $optionGroupName
   * @return array[int value -> string name]
   * @access public
   * @static
   */
  static function getOptionValueList($optionGroupName) {
    $valueList = array();
	$params = array(
      'q' => 'civicrm/ajax/rest',
      'sequential' => 1,
      'option_group_name' => $optionGroupName,
      'return' => 'value,name',
	  'options' => array(
        'limit' => 9999,
      ),
    );
    $result = civicrm_api3('OptionValue', 'get', $params);
	foreach($result['values'] as $optionValue) {
		$valueList[$optionValue['value']] = $optionValue['name'];
	}
	return $valueList;
  }

  /**
   * Function to retrieve all column values for a specified activity
   *
   * @param  int $activityId
   * @return array[column_name -> column_value]
   * @access public
   * @static
   */
  static function getActivityDetails($activityId) {
    // get standard activity details
    $params = array(
      'version' => 3,
      'q' => 'civicrm/ajax/rest',
      'sequential' => 1,
      'id' => $activityId,
    );
    $result = civicrm_api('Activity', 'get', $params);
	if ($result['count']==0) {
      return NULL;
	} else {
	  $values = $result['values'][0];
	  self::_getActivitySupplement($values);
	  return($values);
	}
  }

  /**
   * Function to retrieve supplementary custom data for a specified activity
   *
   * @param  multi $activityData
   * @return array[column_name -> column_value]
   * @access private
   * @static
   */
  private static function _getActivitySupplement(&$activityData) {
    $actTypeList = self::getActivityTypeOptions();
    $actStatusList = self::getActivityStatusOptions();
	// add status name
	$activityData['activity_status_name'] = NULL;
	if (!is_null($activityData['status_id'])) {
	  if (array_key_exists($activityData['status_id'], $actStatusList)) {
	    $activityData['activity_status_name'] = $actStatusList[$activityData['status_id']];
	  }
	}
	// add activity type name
	$activityData['activity_type_name'] = NULL;
	if (!is_null($activityData['activity_type_id'])) {
	  if (array_key_exists($activityData['activity_type_id'], $actTypeList)) {
	    $activityData['activity_type_name'] = $actTypeList[$activityData['activity_type_id']];
	  }
	}
	// retrieve additional custom data
	switch ($activityData['activity_type_name']) {
	  // additional data for standard DSA
      case 'DSA':
	    $params = array(
          'version' => 3,
          'q' => 'civicrm/ajax/rest',
          'sequential' => 1,
          'activity_id' => $activityData['id'],
        );
        $result = civicrm_api('Dsa', 'GetCustom', $params);
        if ($result['count'] == 1) {
		  $activityData['custom'] = $result['values'][0];
		}
	    break;

      // additional data for Representative payments
      case 'Representative payment':
	    $params = array(
          'version' => 3,
          'q' => 'civicrm/ajax/rest',
          'sequential' => 1,
          'activity_id' => $activityData['id'],
        );
        $result = civicrm_api('Representative', 'GetCustom', $params);
        if ($result['count'] == 1) {
		  $activityData['custom'] = $result['values'][0];
		}
	    break;

      // additional data for Business DSA
	  // t.b.d.

	  // do not return additional data for other activities
	  default:
	}
  }



  /*
   * Function to mimic implode($ar), but implicitly modify the values before concatenation:
   * - replace / remove odd characters
   * - extend the length of values using certain characters (likely ' '  or '0') at either the left or the right of the value (string)
   * - trim each value down to a certain size
  */
  static function dsa_concatValues($ar) {
	// field order and size is fixed!
	// modifications may render all output useless for the financial system!
	$filter = CRM_Dsa_CharFilter::singleton();

	// prefilter chars in accountnumber and IBAN
	$ar['BankRekNr'] = $filter->charFilterNumbers($ar['BankRekNr']); // experts bank account number (not IBAN) can only contain [0-9]
	$ar['IBAN'] = $filter->charFilterUpperCaseNum($ar['IBAN'], TRUE); // bank account IBAN can only contain [0-9A-Z]
	$ar['BIC'] = $filter->charFilterUpperCaseNum($ar['BIC'], TRUE); // BIC/Swift code can only contain [0-9A-Z]

	return
		$filter->filteredResize($ar['Boekjaar'],              2, '', TRUE,  FALSE, TRUE) .	// 14 for 2014
		$filter->filteredResize($ar['Dagboek'],               2, '', TRUE,  FALSE, TRUE) .	// I1 for DSA, I3 for Representative payment
		$filter->filteredResize($ar['Periode'],               2, '', TRUE,  FALSE, TRUE) .	// 06 for June
		$filter->filteredResize($ar['Boekstuk'],              4, '0', FALSE, FALSE, TRUE) .	// #### sequence (must be unique per month)
		$filter->filteredResize($ar['GrootboekNr'],           5, '0', TRUE,  FALSE, TRUE) .	// general ledger code
		$filter->filteredResize($ar['Sponsorcode'],           5, '', TRUE,  FALSE, TRUE) .	// "10   " for DGIS where "610  " would be preferred
		$filter->filteredResize($ar['Kostenplaats'],          8, '', TRUE,  FALSE, TRUE) .	// project number CCNNNNNT (country, number, type)
		$filter->filteredResize($ar['Kostendrager'],          8, '', TRUE,  FALSE, TRUE) .	// country code main activity (8 chars!)
		$filter->filteredResize($ar['Datum'],                10, '', TRUE,  FALSE, TRUE) .	// today
		$filter->filteredResize($ar['DC'],                    1, '', TRUE,  FALSE, TRUE) .	// D for payment, C for full creditation. There will be NO partial creditation.
		$filter->filteredResize($ar['PlusMin'],               1, '', TRUE,  TRUE, TRUE)  .	// + for payment, - for creditation
		//$filter->filteredResize($ar['Bedrag'],             10, '0', FALSE, FALSE, TRUE) .	// not in use: 10 digit numeric 0
		//$filter->filteredResize($ar['Filler1'],             9, '', TRUE,  FALSE, TRUE) .	// not in use: 9 spaces
		$filter->filteredResize($ar['OmschrijvingA'],        10, '', TRUE,  FALSE, TRUE) .	// description fragment: surname
		//$filter->filteredResize(' ',                        1, '', TRUE,  FALSE, TRUE) .	// description fragment: additional space
		$filter->filteredResize($ar['OmschrijvingB'].' '.$ar['OmschrijvingC'], 8, '', TRUE,  FALSE, TRUE) .	// description fragment: main activity number ("NNNNN ")
		//$filter->filteredResize($ar['OmschrijvingC'],       3, '', TRUE,  FALSE, TRUE) .	// description fragment: country ("CC ")
		//$filter->filteredResize($ar['Filler2'],            13, '', TRUE,  FALSE, TRUE) .	// not in use: 13 spaces
		$filter->filteredResize($ar['FactuurNrRunType'].$ar['FactuurNrYear'].$filter->filteredResize($ar['FactuurNr'],5, '0', FALSE, FALSE, FALSE),		 8, '', TRUE,  FALSE, TRUE) .	// D for DSA, L for Representative payment
		//$filter->filteredResize($ar['FactuurNrYear'],       2, '', TRUE,  FALSE, TRUE) .	// 14 for 2014; date of "preparation", not dsa payment! :=> civi: date of original activity
		//$filter->filteredResize($ar['FactuurNr'],           4, '0', FALSE, FALSE, TRUE) .	// sequence based: "123456" would return "2345", "12" would return "0001"
		//$filter->filteredResize($ar['FactuurNrAmtType'],    1, '', TRUE,  FALSE, TRUE) .	// represents type of amount in a single character: 1-9, A-Z
		$filter->filteredResize($ar['FactuurDatum'],         10, '', TRUE,  FALSE, TRUE) .	// creation date (dd-mm-yyyy) of DSA activity (in Notes 1st save when in status "preparation") :=> civi: date of original activity
		$filter->filteredResize($ar['FactuurBedrag'],        11, '', FALSE, FALSE, TRUE) .	// payment amount in EUR cents (123,456 -> 12346)
		$filter->filteredResize($ar['FactuurPlusMin'],        1, '', TRUE,  TRUE, TRUE)  .	// + for payment, - for creditation
		$filter->filteredResize($ar['OmschrijvingB'],         8,'',TRUE,TRUE, TRUE). //Project number
		$filter->filteredResize($ar['OmschrijvingC'],         2,'',TRUE,TRUE, TRUE). //Country code
		//$filter->filteredResize($ar['Kenmerk'],            12, '', TRUE,  FALSE, TRUE) .	// project number NNNNNCC (number, country)
		$filter->filteredResize($ar['ValutaCode'],            3, '', TRUE,  FALSE, TRUE) .	// always EUR
		$filter->filteredResize($ar['CrediteurNr'],           8, '', TRUE,  FALSE, TRUE) .	// experts shortname (8 char)
		$filter->filteredResize($ar['NaamOrganisatie'],      35, ' ', TRUE,  FALSE, TRUE) .	// experts name (e.g. "van Oranje-Nassau W.A.")
		$filter->filteredResize($ar['Taal'],                  1, '', TRUE,  FALSE, TRUE) .	// always "N"
		$filter->filteredResize($ar['Land'],                  3, ' ', TRUE,  FALSE, TRUE) .	// ISO2
		$filter->filteredResize($ar['Adres1'],               35, ' ', TRUE,  FALSE, TRUE) .	// experts street + number
		$filter->filteredResize($ar['Adres2'],               35, ' ', TRUE,  FALSE, TRUE) .	// experts zip + city
		$filter->filteredResize($ar['BankRekNr'],            25, '', TRUE,  FALSE, TRUE) .	// experts bank account: number (not IBAN)
		$filter->filteredResize($ar['Soort'],                 1, '', TRUE,  FALSE, TRUE) .	// main activity (case) type (1 character)
		$filter->filteredResize($ar['Shortname'],             8, '', TRUE,  FALSE, TRUE) .	// experts shortname (8 char)
		$filter->filteredResize($ar['Rekeninghouder'],       35, ' ', TRUE,  FALSE, TRUE) .	// bank account holder: name
		$filter->filteredResize($ar['RekeninghouderLand'],    3, '', TRUE,  FALSE, TRUE) .	// bank account holder: country (ISO2)
		$filter->filteredResize($ar['RekeninghouderAdres1'], 35, ' ', TRUE,  FALSE, TRUE) .	// bank account holder: street + number
		$filter->filteredResize($ar['RekeninghouderAdres2'], 35, ' ', TRUE,  FALSE, TRUE) .	// bank account holder: zip + city
		$filter->filteredResize($ar['IBAN'],                 34, ' ', TRUE,  FALSE, TRUE) .	// bank account: IBAN
		$filter->filteredResize($ar['Banknaam'],             35, ' ', TRUE,  FALSE, TRUE) .	// bank name
		//$filter->filteredResize($ar['BankPlaats'],         35, '', TRUE,  FALSE, TRUE) .	// bank city
		$filter->filteredResize($ar['BankLand'],              3, ' ', TRUE,  FALSE, TRUE) .	// bank country (ISO2)
		$filter->filteredResize($ar['BIC'],                  11, 'X', TRUE,  FALSE, FALSE);		// experts bank account: BIC/Swift code
  }



  /*
   * Function to return DSA-specific status values in an array
   * format: array[status_name] = status_value
   */
  static function getDsaStatusList() {
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
   * Function to provide general ledger codes in an array
   * format: array[name]=value
   * should be replaced by a pre-filled table + query
   * limit is set as civi 4.4.4 does not handle limit 0 as unlimited
   */
  static function getGeneralLedgerCodeList() {
	$params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'option_group_name' => 'general_ledger',
		'return' => 'name,value',
		'options' => array(
			'limit' => 1000,
		),
	);
	$result = civicrm_api('OptionValue', 'get', $params);
	$code_tbl = array();
	foreach($result['values'] as $value) {
		$code_tbl[$value['name']] = $value['value'];
	}
	return $code_tbl;
}

  /*
   * Function to provide a translation table: country id to country ISO code and -name
   * limit is set as civi 4.4.4 does not handle limit 0 as unlimited
   */
  static function getCountryISOCodeList() {
	$params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'options' => array(
			'limit' => 5000,
		),
		'return' => 'iso_code,name',
	);
	$result = civicrm_api('Country', 'get', $params);
	return $result['values'];
  }

  /*
   * Equivalent function for mySQL 'ifnull(a,b)'
   * if returns a if not NULL, otherwise returns b
   */
  static function _ifnull($a, $b=null) {
    return is_null($a)?$b:$a;
  }

  /**
   * Method to check if activity type is a DSA activity type
   *
   * @param int $activityTypeId
   * @param int $activityStatusId
   * @return bool
   * @throws Exception when error in API
   * @access public
   * @static
   */
  public static function isDsaActivityType($activityTypeId, $activityStatusId) {
    if (empty($activityTypeId)) {
      return FALSE;
    }
    $optionGroupParams = array(
      'name' => 'activity_type',
      'return' => 'id');
    try {
      $optionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $optionGroupParams);
      /*
       * check for DSA and Representative payment activity types
       */
      $actTypesToBeChecked = array('DSA', 'Representative Payment');
      $dsaStatusList = self::getDsaStatusList();

      foreach ($actTypesToBeChecked as $actTypeCheck) {
        $optionValueParams = array(
          'option_group_id' => $optionGroupId,
          'name' => $actTypeCheck,
          'return' => 'value');
        try {
          $protectedActivityTypeId = civicrm_api3('OptionValue', 'Getvalue', $optionValueParams);
          if ($protectedActivityTypeId == $activityTypeId && $activityStatusId == $dsaStatusList['dsa_paid']) {
            return TRUE;
          }
        } catch (CiviCRM_API3_Exception $ex) {
          throw new Exception('Could not find a single option value with name '.$actTypeCheck.
            ' , error from API OptionValue Getvalue: ' . $ex->getMessage());
        }
      }
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find an option group with name activity_type,
        error from API OptionGroup Getvalue: '.$ex->getMessage());
    }
    return FALSE;
  }
	/**
	 * Method to create option value for default business amount (see issue 3057 http://redmine.pum.nl/issues/3057)
	 */
	public static function createBusinessDefaultAmount() {
		$optionGroupParams = array(
			'name' => "rep_payment_configuration",
			'return' => "id"
		);
		$optionGroupId = civicrm_api3("OptionGroup", "Getvalue", $optionGroupParams);
		$countParams = array(
			'option_group_id' => $optionGroupId,
			'name' => 'default_business_amount',
		);
		try {
			$countOptionValues = civicrm_api3("OptionValue", "Getcount", $countParams);
		} catch (CiviCRM_API3_Exception $ex) {
			$countOptionValues = 0;
		}
		if ($countOptionValues == 0) {
			$createParams = array(
				'option_group_id' => $optionGroupId,
				'label' => "Business default payment amount",
				'name' => "default_business_amount",
				'value' => "200.00",
				'weight' => 15,
				'description' => "Default amount for Representative Payment in Business (e.g. 200.00)",
				'default' => FALSE
			);
			civicrm_api3("OptionValue", "Create", $createParams);
		}
	}

  /**
   * CRM_Dsa_Utils::getProjectOfficerFromCase()
   *
   * Method to get the project officer from a case
   * The contact id is added to an array (in case there are multiple project officers) and will be returned.
   *
   * @param mixed $caseId
   * @return array $prof_contact_id
   */
  public static function getProjectOfficerFromCase($caseId){
    $prof_contact_id = array();

    $params_rel_prof = array(
      'version' => 3,
      'sequential' => 1,
      'name_b_a' => 'Case Coordinator',
    );
    $rel_prof = civicrm_api('RelationshipType', 'get', $params_rel_prof);
    if($rel_prof['count'] == 1) {
      foreach($rel_prof['values'] as $key => $rel){
        $params = array(
          'version' => 3,
          'sequential' => 1,
          'case_id' => $caseId,
          'relationship_type_id' => $rel['id'],
          'is_active' => 1,
        );
        $result = civicrm_api('Relationship', 'get', $params);

        foreach($result['values'] as $key => $value){
          $prof_contact_id[] = $value['contact_id_b'];
        }
      }
    }
    if(!empty($prof_contact_id)){
      return $prof_contact_id;
    }
  }

  /**
   * CRM_Dsa_Utils::getPaidStaff()
   *
   * Method to get the list of paid staff
   * Used for selection in configuration screen
   *
   * @return array $teamleaders
   */
  public static function getPaidStaff(){
    $paid_staff = array();
    try {
      $params_group_pumstaff = array(
        'version' => 3,
        'sequential' => 1,
        'title' => 'PUM Staff',
      );
      $result_group_pumstaff = civicrm_api('Group', 'getsingle', $params_group_pumstaff);

      $params_contacts_pumstaff = array(
        'version' => 3,
        'sequential' => 1,
        'group_id' => $result_group_pumstaff['id'],
        'status' => 'Added',
        'rowCount' => 0,
      );
      $result_contacts_pumstaff = civicrm_api('GroupContact', 'get', $params_contacts_pumstaff);

      foreach($result_contacts_pumstaff['values'] as $key => $value) {
        $paid_staff[$value['contact_id']] = civicrm_api('Contact', 'getvalue', array('version' => 3, 'sequential' => 1, 'contact_id' => $value['contact_id'], 'return' => 'sort_name'));

        asort($paid_staff);
      }
    } catch (CiviCRM_API3_Exception $ex) {}

    return $paid_staff;
  }

  /**
   * CRM_Dsa_Utils::getContactName()
   *
   * Method to get the contact name of a contact
   * Used for selection in configuration screen
   *
   * @return array $person
   */
  public static function getContactName($contactId){
    $person = '';
    try {
      $person = civicrm_api('Contact', 'getvalue', array('version' => 3, 'sequential' => 1, 'contact_id' => $contactId, 'return' => 'sort_name'));
    } catch (CiviCRM_API3_Exception $ex) {}

    return $person;
  }

  /**
   * CRM_Dsa_Utils::getDisplayName()
   *
   * Method to get the contact name of a contact
   * Used for selection in configuration screen
   *
   * @return array $person
   */
  public static function getDisplayName($contactId){
    $person = array();
    try {
      $person = civicrm_api('Contact', 'getvalue', array('version' => 3, 'sequential' => 1, 'contact_id' => $contactId, 'return' => 'display_name'));
    } catch (CiviCRM_API3_Exception $ex) {}

    if(!empty($person)){
      return $person;
    } else {
      return '';
    }
  }

  /**
   * CRM_Dsa_Utils::isDSATeamleader()
   *
   * Method to check whether the specified contactId is a DSA Teamleader or not
   *
   * @param mixed $contactId
   * @return boolean
   */
  public static function isDSATeamleader($contactId){
    $dao = CRM_Core_DAO::executeQuery('SELECT * FROM civicrm_dsa_teamleaders WHERE contact_id = %1', array(1 => array($contactId, 'Integer')));
    while($dao->fetch()){
      if($dao->contact_id == $contactId){
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * CRM_Dsa_Utils::getDSAAmount()
   *
   * Method to get the total amount of DSA for a specified caseId
   *
   * @param int $caseId
   * @return int $total_amount
   */
  public static function getDSAAmount($caseId){
    $total_amount = 0;
    $dao = CRM_Core_DAO::executeQuery('SELECT * FROM civicrm_dsa_compose WHERE case_id = %1', array(1 => array($caseId, 'Integer')));
    while($dao->fetch()){
      $total_amount = ($dao->amount_dsa+$dao->amount_briefing+$dao->amount_airport+$dao->amount_tranfer+$dao->amount_hotel+$dao->amount_visa+$dao->amount_medical+$dao->amount_other);
    }
    return $total_amount;
  }
}