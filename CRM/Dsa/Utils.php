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
	
	return 
		$filter->filteredResize($ar['Boekjaar'],				 2, ' ', TRUE,  FALSE) .	// 14 for 2014
		$filter->filteredResize($ar['Dagboek'],					 2, ' ', TRUE,  FALSE) .	// I1 for DSA, I3 for Representative payment
		$filter->filteredResize($ar['Periode'],					 2, ' ', TRUE,  FALSE) .	// 06 for June
		$filter->filteredResize($ar['Boekstuk'],				 4, '0', FALSE, FALSE) .	// #### sequence (must be unique per month)
		$filter->filteredResize($ar['GrootboekNr'],				 4, ' ', TRUE,  FALSE) .	// general ledger code
		$filter->filteredResize($ar['Sponsorcode'],				 5, ' ', TRUE,  FALSE) .	// "10   " for DGIS where "610  " would be preferred
		$filter->filteredResize($ar['Kostenplaats'],			 8, ' ', TRUE,  FALSE) .	// project number CCNNNNNT (country, number, type)
		$filter->filteredResize($ar['Kostendrager'],			 8, ' ', TRUE,  FALSE) .	// country code main activity (8 chars!)
		$filter->filteredResize($ar['Datum'],					10, ' ', TRUE,  FALSE) .	// today
		$filter->filteredResize($ar['DC'],						 1, ' ', TRUE,  FALSE) .	// D for payment, C for full creditation. Ther will be NO partial creditation.
		$filter->filteredResize($ar['PlusMin'],					 1, ' ', TRUE,  TRUE)  .	// + for payment, - for creditation
		$filter->filteredResize($ar['Bedrag'],					10, '0', FALSE, FALSE) .	// not in use: 10 digit numeric 0
		$filter->filteredResize($ar['Filler1'],					 9, ' ', TRUE,  FALSE) .	// not in use: 9 spaces
		$filter->filteredResize($ar['OmschrijvingA'],			10, ' ', TRUE,  FALSE) .	// description fragment: surname
		$filter->filteredResize(' ',							 1, ' ', TRUE,  FALSE) .	// description fragment: additional space
		$filter->filteredResize($ar['OmschrijvingB'],			 6, ' ', TRUE,  FALSE) .	// description fragment: main activity number ("NNNNN ")
		$filter->filteredResize($ar['OmschrijvingC'],			 3, ' ', TRUE,  FALSE) .	// description fragment: country ("CC ")
		$filter->filteredResize($ar['Filler2'],					13, ' ', TRUE,  FALSE) .	// not in use: 13 spaces
		$filter->filteredResize($ar['FactuurNrRunType'],		 1, ' ', TRUE,  FALSE) .	// D for DSA, L for Representative payment
		$filter->filteredResize($ar['FactuurNrYear'],			 2, ' ', TRUE,  FALSE) .	// 14 for 2014; date of "preparation", not dsa payment! :=> civi: date of original activity
		$filter->filteredResize($ar['FactuurNr'],				 4, '0', FALSE, FALSE) .	// sequence based: "123456" would return "2345", "12" would return "0001"
		$filter->filteredResize($ar['FactuurNrAmtType'],		 1, ' ', TRUE,  FALSE) .	// represents type of amount in a single character: 1-9, A-Z
		$filter->filteredResize($ar['FactuurDatum'],			10, ' ', TRUE,  FALSE) .	// creation date (dd-mm-yyyy) of DSA activity (in Notes 1st save when in status "preparation") :=> civi: date of original activity
		$filter->filteredResize($ar['FactuurBedrag'],			11, '0', FALSE, FALSE) .	// payment amount in EUR cents (123,456 -> 12346)
		$filter->filteredResize($ar['FactuurPlusMin'],			 1, ' ', TRUE,  TRUE)  .	// + for payment, - for creditation
		$filter->filteredResize($ar['Kenmerk'],					12, ' ', TRUE,  FALSE) .	// project number NNNNNCC (number, country)
		$filter->filteredResize($ar['ValutaCode'],				 3, ' ', TRUE,  FALSE) .	// always EUR
		$filter->filteredResize($ar['CrediteurNr'],				 8, ' ', TRUE,  FALSE) .	// experts shortname (8 char)
		$filter->filteredResize($ar['NaamOrganisatie'],			35, ' ', TRUE,  FALSE) .	// experts name (e.g. "van Oranje-Nassau W.A.")
		$filter->filteredResize($ar['Taal'],					 1, ' ', TRUE,  FALSE) .	// always "N"
		$filter->filteredResize($ar['Land'],					 3, ' ', TRUE,  FALSE) .	// ISO2
		$filter->filteredResize($ar['Adres1'],					35, ' ', TRUE,  FALSE) .	// experts street + number
		$filter->filteredResize($ar['Adres2'],					35, ' ', TRUE,  FALSE) .	// experts zip + city
		$filter->filteredResize($ar['BankRekNr'],				25, ' ', TRUE,  FALSE) .	// experts bank account: number (not IBAN)
		$filter->filteredResize($ar['Soort'],					 1, ' ', TRUE,  FALSE) .	// main activity (case) type (1 character)
		$filter->filteredResize($ar['Shortname'],				 8, ' ', TRUE,  FALSE) .	// experts shortname (8 char)
		$filter->filteredResize($ar['Rekeninghouder'],			35, ' ', TRUE,  FALSE) .	// bank account holder: name
		$filter->filteredResize($ar['RekeninghouderLand'],		20, ' ', TRUE,  FALSE) .	// bank account holder: country (ISO2)
		$filter->filteredResize($ar['RekeninghouderAdres1'],	35, ' ', TRUE,  FALSE) .	// bank account holder: street + number
		$filter->filteredResize($ar['RekeninghouderAdres2'],	35, ' ', TRUE,  FALSE) .	// bank account holder: zip + city
		$filter->filteredResize(strtoupper($ar['IBAN']),		34, ' ', TRUE,  FALSE) .	// bank account: IBAN
		$filter->filteredResize($ar['Banknaam'],				35, ' ', TRUE,  FALSE) .	// bank name
		$filter->filteredResize($ar['BankPlaats'],				35, ' ', TRUE,  FALSE) .	// bank city
		$filter->filteredResize($ar['BankLand'],				 3, ' ', TRUE,  FALSE) .	// bank country (ISO2)
		$filter->filteredResize(strtoupper($ar['BIC']),			11, 'X', TRUE,  FALSE);		// experts bank account: BIC/Swift code
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

      foreach ($dsaStatusList as $statusKey => $statusValue) {
        $message = 'Status key en value  is ' . $statusKey . ' ; ' . $statusValue;
        CRM_Core_DAO::executeQuery('INSERT INTO ehtest SET message = %1', array(1 => array($message, 'String')));
      }

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
}