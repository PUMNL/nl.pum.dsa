<?php

require_once 'dsa.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dsa_civicrm_config(&$config) {
  _dsa_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function dsa_civicrm_xmlMenu(&$files) {
  _dsa_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function dsa_civicrm_install() {
  return _dsa_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dsa_civicrm_uninstall() {
  return _dsa_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dsa_civicrm_enable() {
  return _dsa_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dsa_civicrm_disable() {
  return _dsa_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function dsa_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _dsa_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function dsa_civicrm_managed(&$entities) {
  return _dsa_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dsa_civicrm_caseTypes(&$caseTypes) {
  _dsa_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function dsa_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _dsa_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_navigationMenu
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function dsa_civicrm_navigationMenu( &$params ) {  
  foreach($params as $mainMenu=>$value) {
	if ($params[$mainMenu]['attributes']['name']=='Administer') {
		$maxKey = max(array_keys($params[$mainMenu]['child']));
		$params[$mainMenu]['child'][$maxKey+1] = array (
			'attributes' => array (
				'label'      => ts('DSA'),
				'name'       => 'DSA',
				'url'        => null,
				'permission' => 'administer CiviCRM',
				'operator'   => null,
				'separator'  => null,
				'parentID'   => $mainMenu,
				'navID'      => $maxKey+1,
				'active'     => 1
			),
			'child' =>  array (
				'1' => array (
					'attributes' => array (
						'label'      => ts('View imported batches'),
						'name'       => 'View imported batches',
						'url'        => 'civicrm/dsa/import&action=read',
						'permission' => 'administer CiviCRM',
						'operator'   => null,
						'separator'  => 1,
						'parentID'   => $maxKey+1,
						'navID'      => 1,
						'active'     => 1
					),
					'child' => null
				),
				'2' => array (
					'attributes' => array (
						'label'      => ts('Import locations and rates'),
						'name'       => 'Import locations and rates',
						'url'        => 'civicrm/dsa/import&action=upload',
						'permission' => 'administer CiviCRM',
						'operator'   => null,
						'separator'  => 1,
						'parentID'   => $maxKey+2,
						'navID'      => 1,
						'active'     => 1
					),
					'child' => null
				),
				'3' => array (
					'attributes' => array (
						'label'      => ts('Convert UN files to CSV'),
						'name'       => 'Convert UN files to CSV',
						'url'        => 'civicrm/dsa/import&action=convert',
						'permission' => 'administer CiviCRM',
						'operator'   => null,
						'separator'  => 1,
						'parentID'   => $maxKey+2,
						'navID'      => 1,
						'active'     => 1
					),
					'child' => null
				),
			)
		);
	} else {
		// off-scope menu entry in main menu level
	}
  }
}


/**
 * Implementation of hook_civicrm_buildForm
 *
 * Adds additional fields to the form
 */
function dsa_civicrm_buildForm($formName, &$form) {
//dpm($form, 'Pre DSA mod form data ' . $formName);
	/*
	echo '<pre>';
	print_r($form);
	//print_r($formName);
	echo '</pre>';
	exit();
	//*/
	
	$loadJs = false;
	switch($formName) {
		case 'CRM_Case_Form_Activity':
			if ($form->getVar('_activityTypeName')=='DSA') {
			
				/* civi applies version control on activities
				 * an activity can either be:
				 * - a new record (no activity_id, no original_id)
				 * - a 1st edit   (activity_id, no original_id)
				 * - a nth edit   (activity_id and a different original_id)
				 * DSA is always linked to the activities original id (either through original_id, or when NULL through activity_id)
				 */
				if (isset($form->_activityId)) {
					$activityId = $form->_activityId;
				} else {
					$activityId = NULL;
				}
				if (isset($form->_defaultValues['original_id'])) {
					$originalId = $form->_defaultValues['original_id'];
				} else {
					$originalId = NULL;
				}
				if (!is_null($originalId)) {
					$dsaId = $originalId;
				} else {
					$dsaId = $activityId; // could still be NULL
				} 
				
				// DSA fields are displayed using a custom .tpl-file
				// assume templates are in a templates folder relative to this file
				$templatePath = realpath(dirname(__FILE__)."/templates");
				// add template to the form
				CRM_Core_Region::instance('page-body')->add(
					array(
						'template' => "{$templatePath}/dsa_section.tpl"
					)
				);
				
				// Add DSA reference date to the form (locations/rates may vary per imported dsa batch - need to establish which batch was active at this date)
				$form->add('hidden', 'dsa_ref_dt', NULL, array('id'=> 'dsa_ref_dt'));
				// Add the country field element to the form (standard civi country list)
				$country = array('' => ts('- select -')) + CRM_Core_PseudoConstant::country();
				$form->addElement('select', 'dsa_country', ts('DSA Country'), $country);
				// Add the location field element to the form (JQuery is used to populate the list using country, dsa rates and ref date)
				$location = array('' => ts('- select -'));
				$form->addElement('select', 'dsa_location', ts('DSA Location'), $location);
				// Add hidden field to define which location should be set by default by JQuery within the template
				$form->add('hidden', 'dsa_load_location', NULL, array('id'=> 'dsa_load_location'));
				// Retrieve values for option_group "dsa_percentage"
				$percentage = array();
				$percentageDefault = NULL;
				$params = array(
					'version' => 3,
					'q' => 'civicrm/ajax/rest',
					'sequential' => 1,
					'option_group_name' => 'dsa_percentage',
				);
				try {
					$result = civicrm_api('OptionValue', 'get', $params);
					foreach($result['values'] as $pct) {
						//$percentage[] = HTML_QuickForm::createElement('radio', null, null, $pct['label'], $pct['value']); // warning regarding static function call (civi 4.4.5.)
						$percentage[$pct['value']]=$pct['label'];
						if ($pct['is_default']=='1') {
							$percentageDefault = $pct['value'];
						}
					}
				} catch(Exception $e) {
					// leave 0% available as only option
					//$percentage[] = HTML_QuickForm::createElement('radio', null, null, '0%', '0'); // warning regarding static function call
					$percentage['0']='0%';
				}
				// Add percentage field
				//$form->addGroup($percentage, 'dsa_percentage', ts('DSA Percentage'), '&nbsp;'); // bypassed due to warning regarding static function call (civi 4.4.5)
				$form->addElement('select', 'dsa_percentage', ts('DSA Percentage'), array('' => ts('- select -')) + $percentage);
				// Add number of days field
				$form->add('text', 'dsa_days', ts( 'DSA Days'));
				// Add DSA amount field
				$form->add('text', 'dsa_amount', ts( 'DSA Amount'));
				// Add Expense amount briefing field
				$form->add('text', 'dsa_briefing', ts( 'Expense Briefing'));
				// Add Expense amount debriefing field
				$form->add('text', 'dsa_debriefing', ts( 'Expense Debriefing'));
				// Add Expense amount airport field
				$form->add('text', 'dsa_airport', ts( 'Expense Airport'));
				// Add Expense amount transfer field
				$form->add('text', 'dsa_transfer', ts( 'Expense Transfer'));
				// Add Expense amount hotel field
				$form->add('text', 'dsa_hotel', ts( 'Expense Hotel'));
				// Add Expense amount visa field
				$form->add('text', 'dsa_visa', ts( 'Expense Visa'));
				// Add Expense amount outfit field
				$form->add('text', 'dsa_outfit', ts( 'Expense Outfit'));
				// Add Expense amount other field (incl description)
				$form->add('text', 'dsa_other', ts( 'Expense Other'));
				$form->add('textarea', 'dsa_other_description', ts( 'Expense Other Description'));
				// Add Expense advance amount field
				$form->add('text', 'dsa_advance', ts( 'Expense Advance'));
				// ?? Approval name/id
				// ?? Approval date/time
				
				// Default values for
				// - new creation - ok
				// - open
				// - edit
				// - creditation create
				// - creditation read
				// - creditation save
				
				if (is_null($dsaId)) {
					// Defaults for new DSA creation
					// Default DSA country: retrieve contacts primary adress' country (id)
					$params = array(
						'version' => 3,
						'q' => 'civicrm/ajax/rest',
						'sequential' => 1,
						'is_pimary' => 1,
						'contact_id' => $form->_currentlyViewedContactId,
					);
					try {
						$result = civicrm_api('Address', 'get', $params);
						foreach($result['values'] as $key=>$value) {
							if ($result['values'][$key]['is_primary']=='1') {
								$defaults['dsa_country'] = $result['values'][$key]['country_id'];
							}
						}
					} catch(Exception $e) {
						// just continue without default value
					}
					
					// Default DSA percentage
					if (!is_null($percentageDefault)) {
						$defaults['dsa_percentage'] = $percentageDefault;
					}
					// Default DSA days
					if (!is_null($percentageDefault)) {
						$defaults['dsa_days'] = 0;
					}
					// Default DSA amount
					if (!is_null($percentageDefault)) {
						$defaults['dsa_amount'] = 0;
					}
					// Default Briefing amount
					$defaults['dsa_briefing'] = 0;
					// Default Debriefing amount
					$defaults['dsa_debriefing'] = 0;
					// Default Airport amount
					$defaults['dsa_airport'] = 0;
					// Default Transfer amount
					$defaults['dsa_transfer'] = 0;
					// Default Hotel amount
					$defaults['dsa_hotel'] = 0;
					// Default Visa amount
					$defaults['dsa_visa'] = 0;
					// Default Outfit amount
					$defaults['dsa_outfit'] = 0;
					// Default Other amount
					$defaults['dsa_other'] = 0;
					// Default Advance amount
					$defaults['dsa_advance'] = 0;
				} else {
					// Defaults for editing an existing DSA record
					// get DSACompose met activity_id = $activityId
					//$sql = 'SELECT * FROM civicrm_dsa_compose WHERE activity_id=' . $activityId;
					$sql = 'SELECT cmp.*, rte.batch_id, rte.country, rte.rate, cnt.id as cy_id '
							. 'FROM civicrm_dsa_compose cmp, civicrm_dsa_rate rte, civicrm_country cnt '
							. 'WHERE cmp.activity_id=' . $dsaId . ' AND cmp.loc_id = rte.id AND rte.country = cnt.iso_code';
  
					$dao_defaults = CRM_Core_DAO::executeQuery($sql);
					$result = $dao_defaults->fetch();
//dpm($dao_defaults, 'DAO Defaults');
//dpm($result, 'Defaults Result');
					$defaults['dsa_country'] = $dao_defaults->cy_id;
					//$defaults['dsa_location'] = $dao_defaults->loc_id . '|' . $dao_defaults->rate;
					$defaults['dsa_load_location'] = $dao_defaults->loc_id . '|' . $dao_defaults->rate; // gets set when JQ does the initial location load
					$defaults['dsa_percentage'] = $dao_defaults->percentage;
					$defaults['dsa_days'] = $dao_defaults->days;
					$defaults['dsa_amount'] = $dao_defaults->amount_dsa;
					$defaults['dsa_briefing'] = $dao_defaults->amount_briefing;
					$defaults['dsa_debriefing'] = $dao_defaults->amount_debriefing;
					$defaults['dsa_airport'] = $dao_defaults->amount_airport;
					$defaults['dsa_transfer'] = $dao_defaults->amount_transfer;
					$defaults['dsa_hotel'] = $dao_defaults->amount_hotel;
					$defaults['dsa_visa'] = $dao_defaults->amount_visa;
					$defaults['dsa_outfit'] = $dao_defaults->amount_outfit;
					$defaults['dsa_other'] = $dao_defaults->amount_other;
					$defaults['dsa_other_description'] = $dao_defaults->description_other;
					$defaults['dsa_advance'] = $dao_defaults->amount_advance;
				}
				// Apply default values
				if ($defaults) {
					$form->setDefaults($defaults);
				}
			}
//dpm($form, 'Post dsa-mod form data for ' . $formName);
			break;
		}
	
	if ($loadJs) {
		//CRM_Core_Resources::singleton()->addScriptFile('nl.pum.dsa', 'js/dsa.js');
	}
	return;
}


/**
 * Implementation of hook_civicrm_buildForm
 *
 * Adds validation of added fields to the form
 */
function dsa_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
//dpm($fields, "HOOK_VALIDATEFORM: FIELDS ON ". $formName);
//dpm($form, "HOOK_VALIDATEFORM: ". $formName);
	switch($formName) {
		case 'CRM_Case_Form_Activity':
			if ($form->_activityTypeName == 'DSA') {
				$fldNames= array(
					'dsa_amount',
					'dsa_briefing',
					'dsa_debriefing',
					'dsa_airport',
					'dsa_transfer',
					'dsa_hotel',
					'dsa_visa',
					'dsa_outfit',
					'dsa_other',
					'dsa_advance');
				$result = TRUE;
				foreach($fldNames as $name) {
					$result = $result and _amountCheck($name, $fields, $errors);
				}
/*				if (!array_key_exists('dsa_other', $errors)) {
					if (($fields['dsa_other']!=0) && (trim($fields['dsa_other_description'])=='')) {
						$errors['dsa_other_description'] = ts('Please describe Expense Other');
						$result = FALSE;
					}
				} */
			}
			break;
	}
	return;
}


function _amountCheck($fieldName, $fields, &$errors) {
/* // CRM_Utils_Type::validate(&$field, T_MONEY)
	try {
		$fieldValue=$fields[$fieldName];
		if ($fieldValue=='') {
			$errors[$fieldName] = 'Please enter a valid amount';
		} elseif (!is_numeric($fieldValue)) {
			$errors[$fieldName] = 'Amount is not a numeric value';
			//
		} elseif (preg_match('/([a-zA-Z!@#$%\^&\*\(\)\[\]\{\}:;\'\"\`~\<\>\/\\\=\?\\+_|])/', $fieldValue)) {
			$errors[$fieldName] = 'Amount contains an invalid pattern';
		} elseif ($fieldValue<0) {
			$errors[$fieldName] = 'Please set minimum amount to: 0';
		} elseif ($fieldValue>99999.99) {
			$errors[$fieldName] = 'Please set maximum amount to: 99999.99';
		}
		return TRUE;
	} catch(Exception $e) {
		$errors[''] = 'Caught exception: ' . $e->getMessage();
	} */
}

/**
 * Implementation of hook_civicrm_postProcess
 *
 * Stores added fields in civicrm_dsa_compose
 */
function dsa_civicrm_postProcess( $formName, &$form ) {	
	switch($formName) {
		case 'CRM_Case_Form_Activity':
			/*
			echo '<pre>';
			print_r($form);
			//print_r($formName);
			echo '</pre>';
			exit();
			//*/
			
			// Determine activity id for additional dsa data
			if (isset($form->_defaultValues['original_id'])) {
				// >=3rd save: original id is maintained
				$dsaId = $form->_defaultValues['original_id'];
			} elseif (isset($form->_defaultValues['activity_id'])) {
				// 2nd save: no original_id known yet, use activity_id
				$dsaId = $form->_defaultValues['activity_id'];
			} else {
				// 1st save: no known id at all: retrieve activity_id by activity_type_id
				if (isset($form->_activityTypeId)) {
					$activityTypeId = $form->_activityTypeId;
					$sql = 'SELECT MAX(id) AS max_id FROM civicrm_activity WHERE activity_type_id=' . $activityTypeId;
				} else {
					$sql = 'SELECT MAX(id) AS max_id FROM civicrm_activity';
				}
				$dao = CRM_Core_DAO::executeQuery($sql);
				$dao->fetch();
				$dsaId = $dao->max_id;
			}
			
			// Determine action (number: add or update)
			if (isset($form->_action)) {
				$action = $form->_action;
			} else {
				$action = 0;
			}
			
			// html fieldname -> column name
			$required = array(
				'dsa_location'			=>	array(
											'column'	=> 'loc_id',
											'type'		=> 'text',
											),
				'dsa_percentage'		=>  array(
											'column'	=> 'percentage',
											'type'		=> 'number',
											),
				'dsa_days'				=>  array(
											'column'	=> 'days',
											'type'		=> 'number',
											),
				'dsa_amount'			=>  array(
											'column'	=> 'amount_dsa',
											'type'		=> 'number',
											),
				'dsa_briefing'			=>  array(
											'column'	=> 'amount_briefing',
											'type'		=> 'number',
											),
				'dsa_debriefing'		=>  array(
											'column'	=> 'amount_debriefing',
											'type'		=> 'number',
											),
				'dsa_airport'			=>  array(
											'column'	=> 'amount_airport',
											'type'		=> 'number',
											),
				'dsa_transfer'			=>  array(
											'column'	=> 'amount_transfer',
											'type'		=> 'number',
											),
				'dsa_hotel'				=>  array(
											'column'	=> 'amount_hotel',
											'type'		=> 'number',
											),
				'dsa_visa'				=>  array(
											'column'	=> 'amount_visa',
											'type'		=> 'number',
											),
				'dsa_outfit'			=>  array(
											'column'	=> 'amount_outfit',
											'type'		=> 'number',
											),
				'dsa_other'				=>  array(
											'column'	=> 'amount_other',
											'type'		=> 'number',
											),
				'dsa_other_description'	=>  array(
											'column'	=> 'description_other',
											'type'		=> 'text',
											),
				'dsa_advance'			=>  array(
											'column'	=> 'amount_advance',
											'type'		=> 'number',
											),
				'dsa_ref_dt'			=>	array(
											'column'	=> 'ref_date',
											'type'		=> 'text',
											),
			);
	
			/*
			echo '<pre>';
			print_r($required);
			echo '</pre>';
			//exit();
			//*/
	
			$input = array();
			if  (($action & CRM_Core_Action::$_names['add']) || ($action & CRM_Core_Action::$_names['update'])) {
				$input['activity_id'] = $dsaId;
				if (isset($form->_caseId)) {
					$input['case_id'] = $form->_caseId;
				}
//				if (isset($form->_pid)) {
//					$input['pid'] = $form->_pid; // for creditation: parent activity id
//				}
				if (isset($form->_cid)) {
					$input['cid'] = $form->_cid; // experts contact id
				}
				/*
				echo '<pre>';
				print_r($input);
				echo '</pre>';
				exit();
				//*/

				if (isset($form->_elements)) {
					$elm = $form->_elements;
					// filter dsa fields from the full list of submitted fields
					foreach($elm as $key=>$def) {
						if (isset($def->_attributes['name'])) {
							$name = $def->_attributes['name'];
							$value = NULL;
							if (array_key_exists($name, $required)) {
								if (strpos($name, 'dsa_') === 0) {
									$column = $required[$name];
									$type = $def->_type;
									switch ($type) {
										case 'textarea':
											$value=$def->_value;
											break;
										case 'select':
											$value=implode(',', array_values($def->_values));
											break;
										case 'text':
										case 'hidden':
											if (isset($def->_attributes['value'])) {
												$value=$def->_attributes['value'];
											} else {
												$value='';
											}
											break;
										default:
											$value = NULL;
									}
									if (!is_null($value)) {
										if ($column['type']=='number') {
											$value = floatval($value);
										} else {
											$value = '\'' . str_ireplace(array('\'', '\"'), array('\\\'', '\\\"'), $value) . '\'';
										}
									} else {
										$value = 'NULL';
									}
									
									$input[$column['column']] = $value;
								}
							}
						}
					}
				}
				
				/*
				echo '<pre>';
				print_r($input);
				//print_r($debug);
				echo '</pre>';
				exit();
				//*/
			}
			
			// update or insert? =====================================
			if  ($action & CRM_Core_Action::$_names['add']) {
				$sql = 'INSERT INTO civicrm_dsa_compose (' . implode(',', array_keys($input)) . ') VALUES (' . implode(',', array_values($input)) . ')';
			} elseif ($action & CRM_Core_Action::$_names['update']) {
				foreach($input as $fldNm=>$fldVal) {
					$input[$fldNm] = $fldNm . '=' . $fldVal;
				}
				$sql = 'UPDATE civicrm_dsa_compose SET ' . implode(',', array_values($input)) . ' WHERE ' . $input['activity_id'];
			}
			$result = CRM_Core_DAO::executeQuery($sql);
			/*
			echo '<pre>';
			print_r($sql);
			echo '</pre>';
			exit();
			//*/
			
			break;
	} // switch ($formName)
	return;
}
