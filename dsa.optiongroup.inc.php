<?php

class DSA_OptionGroup {
	
	/*
	 * returns the definitions for dsa option groups
	 */
	static function required() {
		return array(
			array(
				'group_name'	=>	'dsa_percentage',
				'group_label'	=>	'DSA Percentage',
				'enable_level'	=>	'group',
				'values'		=>	array(
					array(
						'label'			=> '0%',
						'name'			=> '0%',
						'value'			=> '0',
						'weight'		=> 10,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> '20%',
						'name'			=> '20%',
						'value'			=> '20',
						'weight'		=> 20,
						'description'	=> '',
						'default'		=> TRUE,
					),
					array(
						'label'			=> '100%',
						'name'			=> '100%',
						'value'			=> '100',
						'weight'		=> 30,
						'description'	=> '',
						'default'		=> FALSE,
					),
				),
			),
			array(
				'group_name'	=>	'activity_status',
				'group_label'	=>	'Activity Status',
				'enable_level'	=>	'value',
				'values'		=>	array(
					array(
						'label'			=> 'Payable',
						'name'			=> 'dsa_payable',
						'value'			=> 1501,
						'weight'		=> 1501,
						'description'	=> 'DSA activity approved for payment',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Paid',
						'name'			=> 'dsa_paid',
						'value'			=> 1502,
						'weight'		=> 1502,
						'description'	=> 'DSA activity paid',
						'default'		=> FALSE,
					),
				),
			),
			array(
				'group_name'	=>	'general_ledger',
				'group_label'	=>	'General Ledger',
				'enable_level'	=>	'value',
				'values'		=>	array(
					array(
						'label'			=> 'LR',
						'name'			=> 'gl_lr',
						'value'			=> 493,
						'weight'		=> 10,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Voorschot BLP',
						'name'			=> 'gl_blp_adv',
						'value'			=> 1411,
						'weight'		=> 20,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'BLP',
						'name'			=> 'gl_blp',
						'value'			=> 5600,
						'weight'		=> 30,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'BLP km',
						'name'			=> 'gl_blp_km',
						'value'			=> 5640,
						'weight'		=> 40,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Voorschot Training',
						'name'			=> 'gl_trn_adv',
						'value'			=> 1410,
						'weight'		=> 40,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Training',
						'name'			=> 'gl_trn',
						'value'			=> 5500,
						'weight'		=> 50,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Training km',
						'name'			=> 'gl_trn_km',
						'value'			=> 5540,
						'weight'		=> 60,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'DSA',
						'name'			=> 'gl_dsa',
						'value'			=> 5100,
						'weight'		=> 70,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Outfit',
						'name'			=> 'gl_outfit',
						'value'			=> 5110,
						'weight'		=> 80,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Voorschot default',
						'name'			=> 'gl_def_adv',
						'value'			=> 1412,
						'weight'		=> 90,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'PUM km',
						'name'			=> 'gl_pum_km_brf',
						'value'			=> 5140,
						'weight'		=> 100,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'PUM km debriefing',
						'name'			=> 'gl_pum_km_debr',
						'value'			=> 5141,
						'weight'		=> 110,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'PUM km airport',
						'name'			=> 'gl_pum_km_airp',
						'value'			=> 5020,
						'weight'		=> 120,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'DSA transfer',
						'name'			=> 'gl_transfer',
						'value'			=> 5000,
						'weight'		=> 130,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Hotel',
						'name'			=> 'gl_hotel',
						'value'			=> 5120,
						'weight'		=> 140,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Visa',
						'name'			=> 'gl_visa',
						'value'			=> 5030,
						'weight'		=> 150,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Other',
						'name'			=> 'gl_other',
						'value'			=> 5130,
						'weight'		=> 160,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Meal / parking',
						'name'			=> 'gl_meal_parking',
						'value'			=> 5133,
						'weight'		=> 170,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Debriefing verrekening',
						'name'			=> 'gl_settle',
						'value'			=> 5105,
						'weight'		=> 180,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'BLP ov',
						'name'			=> 'gl_blp_ov',
						'value'			=> 5645,
						'weight'		=> 200,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Training ov',
						'name'			=> 'gl_trn_ov',
						'value'			=> 5545,
						'weight'		=> 210,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'PUM ov',
						'name'			=> 'gl_pum_ov_brf_debrf',
						'value'			=> 5145,
						'weight'		=> 220,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'PUM ov airport',
						'name'			=> 'gl_pum_ov_airp',
						'value'			=> 5025,
						'weight'		=> 230,
						'description'	=> '',
						'default'		=> FALSE,
					),
				),
			),
			array(
				'group_name'	=>	'dsa_configuration',
				'group_label'	=>	'DSA Configuration',
				'enable_level'	=>	'value',
				'values'		=>	array(
					array(
						'label'			=> 'Payment offset',
						'name'			=> 'payment_offset',
						'value'			=> 10,
						'weight'		=> 10,
						'description'	=> 'Maximum no. of days prior to main activity start date from which payment is due',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Default medical amount',
						'name'			=> 'default_medical_amount',
						'value'			=> '80.00',
						'weight'		=> 20,
						'description'	=> 'Default amount for Expense Medical (e.g. \'80.00\')',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Minimum DSA amount',
						'name'			=> 'minimum_dsa_amount',
						'value'			=> '75.00',
						'weight'		=> 30,
						'description'	=> 'Minimum DSA amount (1 day at 100%)',
						'default'		=> FALSE,
					),
				),
			),
		);
	}
	
	/*
	 * handler for hook_civicrm_install
	 */
	static function install() {
		$created = array();
		$required = self::required();
		
		foreach ($required as $optionGroup) {
			$optionGroupId = NULL;
			
			// verify if group exists
			$params = array(
				'version'		=> 3,
				'sequential'	=> 1,
				'name'			=> $optionGroup['group_name'],
			);
			$result = civicrm_api('OptionGroup', 'getsingle', $params);

			if (in_array('is_error', $result)) {
				// optiongroup not found: $optionGroupId remains NULL
			} else {
				// optiongroup found: use id
				$optionGroupId = $result['id'];
			}
		
			// if group was not found: create it
			if (is_null($optionGroupId)) {
				$params = array(
					'version'		=> 3,
					'sequential'	=> 1,
					'name'			=> $optionGroup['group_name'],
					'title'			=> $optionGroup['group_label'],
					'is_active'		=> 1,
					'description'	=> 'nl.pum.dsa',
				);
				$result = civicrm_api('OptionGroup', 'create', $params);
				
				if($result['is_error'] == 1) {
					// group not created: $optionGroupId remains NULL
					CRM_Utils_System::setUFMessage('Error creating Option Group "' . $optionGroup['group_name'] . '": ' . $result['error_message']);
				} else {
					// group created: retrieve $customGroupId
					$value = array_pop($result['values']);
					$optionGroupId = $value['id'];
					$created[] = $optionGroup['group_name'];
				}
			}
			
			// create optionvalues (if option group exists)
			if (is_null($optionGroupId)) {
				// message was raised earlier, when group was not created - no further action here
			} else {
				foreach ($optionGroup['values'] as $optionValue) {
					// verify if option value exists
					$params = array(
						'version'			=> 3,
						'sequential'		=> 1,
						'option_group_id'	=> $optionGroupId,
						'label'				=> $optionValue['label'],
					);
					$result = civicrm_api('OptionValue', 'getsingle', $params);
					
					if (in_array('is_error', $result)) {
						// create optionvalue
						$params = array(
							'version'			=> 3,
							'sequential'		=> 1,
							'option_group_id'	=> $optionGroupId,
							'label'				=> $optionValue['label'],
							'name'				=> $optionValue['name'],
							'value'				=> $optionValue['value'],
							'weight'			=> $optionValue['weight'],
							'description'		=> $optionValue['description'],
							'is_reserved'		=> TRUE,
							'is_active'			=> TRUE,
							'is_default'		=> $optionValue['default'],
						);
						$result = civicrm_api('OptionValue', 'create', $params);
CRM_Utils_System::setUFMessage($optionValue['name']);
						// result could be checked / reported here
					} else {
						// optiongroup exists - no further action
					}
				}
			}
			
		} // next $optionGroup
		
		$message = "Option group ".implode(", ", $created)." succesfully created";
		CRM_Utils_System::setUFMessage($message);
	}
	
	/*
	 * handler for hook_civicrm_enable
	 */
	static function enable() {
		$required = self::required();
		// set all option groups to enabled
		foreach ($required as $optionGroup) {
			$params = array(
				'version' => 3,
				'sequential' => 1,
				'name' => $optionGroup['group_name'],
			);
			$result = civicrm_api('OptionGroup', 'getsingle', $params);
			if (!array_key_exists('id', $result)) {
				// optiongroup not found: cannot enable
				$group_id = NULL;
			} else {
				// optiongroup found: proceed
				$group_id = $result['id'];
				if ($optionGroup['enable_level']=='group') {
					
					$qryEnable = "UPDATE civicrm_option_group SET is_active=1 WHERE name='" . $optionGroup['group_name'] . "'";
					CRM_Core_DAO::executeQuery($qryEnable);
				} elseif ($optionGroup['enable_level']=='value') {
					// enable the values within the group
					foreach ($optionGroup['values'] as $optionValue) {
						$qryEnable = "UPDATE civicrm_option_value SET is_active=1 WHERE option_group_id='" . $group_id . "' AND name='" . $optionValue['name'] . "'";
						CRM_Core_DAO::executeQuery($qryEnable);
					}
				} else {
					// cannot decide what to enable
				}
			}
		}
	}
	
	/*
	 * handler for hook_civicrm_disable
	 */
	static function disable() {
		$required = self::required();
		// set all option groups to enabled
		foreach ($required as $optionGroup) {
			$params = array(
				'version' => 3,
				'sequential' => 1,
				'name' => $optionGroup['group_name'],
			);
			$result = civicrm_api('OptionGroup', 'getsingle', $params);
			if (!array_key_exists('id', $result)) {
				// optiongroup not found: cannot enable
				$group_id = NULL;
			} else {
			// optiongroup found: proceed
				$group_id = $result['id'];
				if ($optionGroup['enable_level']=='group') {
					$qryEnable = "UPDATE civicrm_option_group SET is_active=0 WHERE name='" . $optionGroup['group_name'] . "'";
					CRM_Core_DAO::executeQuery($qryEnable);
				} elseif ($optionGroup['enable_level']=='value') {
					// disable the values within the group
					foreach ($optionGroup['values'] as $optionValue) {
						$qryDisable = "UPDATE civicrm_option_value SET is_active=0 WHERE option_group_id='" . $group_id . "' AND name='" . $optionValue['name'] . "'";
						CRM_Core_DAO::executeQuery($qryDisable);
					}
				} else {
					// cannot decide what to enable
				}
			}
		}
	}
	
	/*
	 * handler for hook_civicrm_uninstall
	 */
	static function uninstall() {
	}
	
}