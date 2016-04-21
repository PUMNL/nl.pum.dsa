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
						'value'			=> 5500,
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
						'value'			=> 5200,
						'weight'		=> 30,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'BLP km',
						'name'			=> 'gl_blp_km',
						'value'			=> 5230,
						'weight'		=> 40,
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
						'value'			=> 5240,
						'weight'		=> 200,
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
					array(
						'label'			=> 'Mail FA',
						'name'			=> 'mail_fa',
						'value'			=> 'mailto.address@organisation.domain',
						'weight'		=> 40,
						'description'	=> 'Email address at financial department to send DSA payment lines to',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Mail From',
						'name'			=> 'mail_from',
						'value'			=> 'dsa.paymentprocessor <from.address@organisation.domain>',
						'weight'		=> 50,
						'description'	=> 'Defines the from-address when sending a payment email to the financial department',
						'default'		=> FALSE,
					),
				),
			),
			array(
				'group_name'	=>	'rep_payment_configuration',
				'group_label'	=>	'Representative Payment Configuration',
				'enable_level'	=>	'value',
				'values'		=>	array(
					array(
						'label'			=> 'Default payment amount',
						'name'			=> 'default_payment_amount',
						'value'			=> '400.00',
						'weight'		=> 10,
						'description'	=> 'Default amount for Representative Payment (e.g. \'400.00\')',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Mail FA',
						'name'			=> 'mail_fa',
						'value'			=> 'mailto.address@organisation.domain',
						'weight'		=> 40,
						'description'	=> 'Email address at financial department to send DSA payment lines to',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'Mail From',
						'name'			=> 'mail_from',
						'value'			=> 'representative.paymentprocessor <from.address@organisation.domain>',
						'weight'		=> 50,
						'description'	=> 'Defines the from-address when sending a payment email to the financial department',
						'default'		=> FALSE,
					),
				),
			),
			array(
				'group_name'	=>	'rep_payment_relationships',
				'group_label'	=>	'Representative Payment Relationships',
				'enable_level'	=>	'value',
				'values'		=>	array(
					array(
						'label'			=> 'Representative',
						'name'			=> 'Representative for',
						'value'			=> 'Representative',
						'weight'		=> 10,
						'description'	=> '',
						'default'		=> FALSE,
					),
				),
			),
		);
	}
	
	/*
	 * handler for hook_civicrm_install
	 * option groups are built in 2 cycles: (1) the groups themselves and (2) their values
	 * attempts to build the values in the same cycle as the groups often failed, possibly due to transactional processing
	 */
	static function install() {
		$created = array();
		$message = '';
		$required = self::required();
		
		// cycle 1:  create option groups
		foreach ($required as $optionGroup) {
			$optionGroupId = NULL;
			
			// verify if group exists
			$params = array(
				'sequential'	=> 1,
				'name'			=> $optionGroup['group_name'],
			);
			try {
				$result = civicrm_api3('OptionGroup', 'getsingle', $params);
				$optionGroupId = $result['id'];
			} catch (Exception $e) {
				// optiongroup not found: $optionGroupId remains NULL
			}
		
			// if group was not found: create it
			if (is_null($optionGroupId)) {
				$params = array(
					'sequential'	=> 1,
					'name'			=> $optionGroup['group_name'],
					'title'			=> $optionGroup['group_label'],
					'is_active'		=> 1,
					'description'	=> 'nl.pum.dsa',
				);
				try {
					$result = civicrm_api3('OptionGroup', 'create', $params);
					// group created: retrieve $customGroupId (perform an intentional new db request)
					$params = array(
						'sequential'	=> 1,
						'name'			=> $optionGroup['group_name'],
					);
					try {
						$result = civicrm_api3('OptionGroup', 'getsingle', $params);
						$optionGroupId = $result['id'];
						$created[] = $optionGroup['group_label'];
					} catch (Exception $e) {
						// optiongroup not found: $optionGroupId remains NULL
					}
				} catch (Exception $e) {
					// group not created: $optionGroupId remains NULL
				}
			}
		} // next $optionGroup
		
		if (count($created) > 0) {
			$message = "Option group ".implode(", ", $created)." succesfully created";
			CRM_Utils_System::setUFMessage($message);
		}
		
		usleep(1000);
		
		// cycle 2:  create option values
		foreach ($required as $optionGroup) {
			$created = array();
			$optionGroupId = NULL;
			
			// verify if group exists
			$params = array(
				'sequential'	=> 1,
				'name'			=> $optionGroup['group_name'],
			);
			try {
				$result = civicrm_api3('OptionGroup', 'getsingle', $params);
				$optionGroupId = $result['id'];
			} catch (Exception $e) {
				// optiongroup not found: $optionGroupId remains NULL
			}
			
			// create optionvalues (if option group exists)
			if (!is_null($optionGroupId)) {
				foreach ($optionGroup['values'] as $optionValue) {
					// verify if option value exists
					$params = array(
						'sequential'		=> 1,
						'option_group_id'	=> $optionGroupId,
						'name'				=> $optionValue['name'],
					);
					try {
						$result = civicrm_api3('OptionValue', 'getsingle', $params);
						// option value found
					} catch (Exception $e) {
						// option value NOT found
						$params = array(
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
						try {
							$result_val = civicrm_api3('OptionValue', 'create', $params);
							$created[] = $optionValue['label'];
						} catch (Exception $e) {
						}
					}
					
					//CRM_Utils_System::setUFMessage($optionValue['name']);
				} // next option value
			}
			
			if (count($created) > 0) {
				$message = 'Option group ' . $optionGroup['group_label'] . ': value(s) ' . implode(', ', $created) . ' succesfully created';
				CRM_Utils_System::setUFMessage($message);
			}
			
		} // next $optionGroup
	}
	
	
	/*
	 * handler for hook_civicrm_enable
	 */
	static function enable() {
		$required = self::required();
		// set all option groups to enabled
		foreach ($required as $optionGroup) {
			$params = array(
				'sequential' => 1,
				'name' => $optionGroup['group_name'],
			);
			$result = civicrm_api3('OptionGroup', 'getsingle', $params);
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
				'sequential' => 1,
				'name' => $optionGroup['group_name'],
			);
			$result = civicrm_api3('OptionGroup', 'getsingle', $params);
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