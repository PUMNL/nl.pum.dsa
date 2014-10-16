<?php

class DSA_ActivityType {
	
	/*
	 * returns the definitions for DSA activity types
	 */
	static function required() {
		return array(
			array(
				'component' => 'CiviCase',
				'label' => 'DSA',
				'description' => 'Use this activity to calculate and prepare DSA payments and creditations.'
			),
			array(
				'component' => 'CiviCase',
				'label' => 'Representative payment',
				'description' => 'Use this activity to prepare payments to representatives.'
			),
		);
	}
	
	/*
	 * handler for hook_civicrm_install
	 */
	static function install() {
		// basically action types are option values as well
		$created = array();
		$required = self::required();
		$existingComponents = self::_existingComponents();
		$componentId = NULL;
				
		// retrieve activity_type group id
		$optionGroupId = self::_getActivityTypeGroupId();
		
		if (is_null($optionGroupId)) {
			// optiongroup not found: cannot proceed
			CRM_Utils_System::setUFMessage('Could not find option group "activity_type" - no activity types processed');
		} else {
			// optiongroup found: use it as option_group_id in option values
			foreach ($required as $activityType) {
				$componentId = NULL;
				
				// translate component to id (null = 'Contacts OR Cases')
				if (array_key_exists($activityType['component'], $existingComponents)) {
					$componentId = $existingComponents[$activityType['component']];
				}
				
				// verify if optionvalue/activity type exists
				$params = array(
					'version'			=> 3,
					'sequential'		=> 1,
					'option_group_id'	=> $optionGroupId,
					'label'				=> $activityType['label'],
				);
				$result = civicrm_api('OptionValue', 'getsingle', $params);
				
				if (in_array('is_error', $result)) {
					// retrieve current maximum value -> raise 1
					$new_value = round(self::_getMaxValue($optionGroupId)) + 1;

					// create optionvalue for activity type
					$params = array(
						'version'			=> 3,
						'sequential'		=> 1,
						'option_group_id'	=> $optionGroupId,
						'component_id'		=> $componentId,
						'label'				=> $activityType['label'],
						'name'				=> $activityType['label'],
						'value'				=> $new_value,
						'description'		=> 'nl.pum.dsa - ' . $activityType['description'],
						'is_reserved'		=> TRUE,
						'is_active'			=> TRUE,
					);
					$result = civicrm_api('OptionValue', 'create', $params);
					// result could be checked / reported here
				} else {
					// optiongroup exists - no further action
				}
			}
		}
	}
	
	/*
	 * handler for hook_civicrm_enable
	 */
	static function enable() {
		$required = self::required();
		
		// retrieve activity_type group id
		$optionGroupId = self::_getActivityTypeGroupId();
		
		if (is_null($optionGroupId)) {
			// optiongroup not found: cannot proceed
			CRM_Utils_System::setUFMessage('Could not find option group "activity_type" - no activity types processed');
		} else {
			// optiongroup found: use it as option_group_id in option values
			foreach ($required as $activityType) {
				// set all existing entries to enabled
				$params = array(
					'version' => 3,
					'sequential' => 1,
					'option_group_id' => $optionGroupId,
					'label' => $activityType['label'],
				);
				$result = civicrm_api('OptionValue', 'get', $params);
				if (array_key_exists('id', $result)) {
					$qryEnable = "UPDATE civicrm_option_value SET is_active=1 WHERE option_group_id=" . $optionGroupId . " AND id=" . $result['id'];
					CRM_Core_DAO::executeQuery($qryEnable);
				} else {
					// id not available: nothing to enable
				}
			}
		}
	}
	
	/*
	 * handler for hook_civicrm_disable
	 */
	static function disable() {
		$required = self::required();
		
		// retrieve activity_type group id
		$optionGroupId = self::_getActivityTypeGroupId();
		
		if (is_null($optionGroupId)) {
			// optiongroup not found: cannot proceed
			CRM_Utils_System::setUFMessage('Could not find option group "activity_type" - no activity types processed');
		} else {
			// optiongroup found: use it as option_group_id in option values
			foreach ($required as $activityType) {
				// set all existing entries to disabled
				$params = array(
					'version' => 3,
					'sequential' => 1,
					'option_group_id' => $optionGroupId,
					'label' => $activityType['label'],
				);
				$result = civicrm_api('OptionValue', 'get', $params);
				if (array_key_exists('id', $result)) {
					$qryDisable = "UPDATE civicrm_option_value SET is_active=0 WHERE option_group_id=" . $optionGroupId . " AND id=" . $result['id'];
					CRM_Core_DAO::executeQuery($qryDisable);
				} else {
					// id not available: nothing to enable
				}
			}
		}
	}
	
	/*
	 * handler for hook_civicrm_uninstall
	 */
	static function uninstall() {
	}
	
	private static function _existingComponents() {
		// store existing components in an array from which component_ids can easily be retrieved (components not available through API yet)
		$components = array();
		$qryComponents = 'select id, name from civicrm_component';
		$dao = CRM_Core_DAO::executeQuery($qryComponents);
		while ($dao->fetch()) {
			$components[$dao->name] = $dao->id;
		}
		return $components;
	}
	
	private static function _getMaxValue($group_id) {
		$max = NULL;
		try {
			$sql= "SELECT MAX(value * 1) as max FROM civicrm_option_value WHERE option_group_id=" . $group_id;
			$daoResult = CRM_Core_DAO::executeQuery($sql);
			$qryResult = $daoResult -> fetch();
			$max = $daoResult->max;
			return round($max);
		} catch (CiviCRM_API3_Exception $e) {
			return NULL;
		}
	}
	
	private static function _getActivityTypeGroupId() {
		// retrieve activity_type group id
		$params = array(
			'version'		=> 3,
			'sequential'	=> 1,
			'name'			=> 'activity_type',
		);
		$result = civicrm_api('OptionGroup', 'getsingle', $params);
		if (in_array('is_error', $result)) {
			// optiongroup not found: cannot proceed
			CRM_Utils_System::setUFMessage('Could not find option group "activity_type" - no activity types processed');
			return NULL;
		} else {
			// optiongroup found: use id (as option_group_id in option values)
			return $result['id'];
		}
	}
}