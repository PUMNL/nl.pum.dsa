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
  
  
  
}