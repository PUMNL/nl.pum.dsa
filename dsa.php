<?php

require_once 'dsa.civix.php';
require_once 'CRM/Dsa/Page/DSAImport.php';
require_once 'dsa.activitytype.inc.php';
require_once 'dsa.optiongroup.inc.php';

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
  DSA_OptionGroup::install();
  DSA_ActivityType::install();
  return _dsa_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dsa_civicrm_uninstall() {
  // added option groups, option values and activity types are not deleted!
  return _dsa_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dsa_civicrm_enable() {
  DSA_OptionGroup::enable();
  DSA_ActivityType::enable();
  return _dsa_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dsa_civicrm_disable() {
  DSA_OptionGroup::disable();
  DSA_ActivityType::disable();
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
// dpm($form, 'Pre DSA mod form data ' . $formName);

	$loadJs = false;
	switch($formName) {
		case 'CRM_Case_Form_CaseView':
			// avoid creation of a 2nd DSA activity in a status other than "Paid"
			// DISABLED as additional DSA activities can be made for other 'roles' (can't tell yet which participant will be selected)
		/*
			// obtain case id (if available)
			if (isset($form->_caseID)) {
				$caseId = $form->_caseID;
				// all current revisions of the current case' activities of type DSA in an unfinished status
				$dao_activities = _dao_findOpenDsaActivities($caseId);
//dpm($dao_activities, 'DAO activities');
				if ($dao_activities->N > 0) {
					// DSA activities found: disallow creation of another one
					foreach($form->_elements as $key=>$value) {
						if ($value->_attributes) {
							if ($value->_attributes['name']) {
								if ($value->_attributes['name']=='activity_type_id') {
									foreach($value->_options as $opt_key=>$opt_val) {
										if ($opt_val['text']=='DSA') {
											unset($form->_elements[$key]->_options[$opt_key]);
											//$form->_elements[$key]->_options[$opt_key]
										}
									}
								}
							}
						}
					}
				}
			}
		*/
			break;
		case 'CRM_Case_Form_Activity':
			if ($form->getVar('_activityTypeName')=='DSA') {
			
				/* civi applies version control on activities
				 * an activity can either be:
				 * - a new record (no activity_id, no original_id)
				 * - a 1st edit   (activity_id, no original_id)
				 * - a nth edit   (activity_id and a different original_id)
				 * DSA (in civicrm_dsa_compose) is always linked to the activities original id (either through original_id, or when NULL through activity_id)
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
				// Form structure =============================================
				// Add a hidden field to hold the payment type (from participant selection)
				$form->add('hidden', 'dsa_type', NULL, array('id'=> 'dsa_type'));
				// Add DSA reference date to the form (locations/rates may vary per imported dsa batch - need to establish which batch was active at this date)
				$form->add('hidden', 'dsa_ref_dt', NULL, array('id'=> 'dsa_ref_dt'));
				// Add a hidden field to hold a complete list of locations/rates for each country
				$form->add('hidden', 'dsa_location_lst', NULL, array('id'=> 'dsa_location_lst'));
				// Add a hidden field to hold the selected location id
				$form->add('hidden', 'dsa_location_id', NULL, array('id'=> 'dsa_location_id'));
				// Add a hidden field to hold a complete list of participators in this case 
				// Retrieve all this cases participants
				if (isset($form->_caseId)) {
					$role_ar = _getCaseParticipantList($form->_caseId);
				}
				$participant_options = array('' => ts('- select -')) + $role_ar;
				$form->addElement('select', 'dsa_participant', ts('Participant'), $participant_options);
				// Add a hidden field to hold the selected participants contact id
				$form->add('hidden', 'dsa_participant_id', NULL, array('id'=> 'dsa_participant_id'));
				// Add a hidden field to hold the selected participants relationsship_type_id
				$form->add('hidden', 'dsa_participant_role', NULL, array('id'=> 'dsa_participant_role'));
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
				// Add a field to hold approval details
				$form->add('text', 'dsa_approval', ts( 'Approval'), array('id'=> 'dsa_approval'));
				
				// Default values for
				// - new creation - ok
				// - open
				// - edit
				// - creditation create
				// - creditation read
				// - creditation save
				
				// Default field values =======================================
				// Three scenario's here: a creation of a new activity, editing an existing and validation failure
				if ($form->_flagSubmitted) {
					// Defaults in case of a validation error
					// All submitted values are present: leave most to civi, except:
					//$defaults['dsa_load_location'] = $form->_submitValues['dsa_location'];
				} elseif (is_null($dsaId)) {
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
					
					// Retieve all available locations / rates for the specified reference date (if any)
					$rateData = CRM_Dsa_Page_DSAImport::getAllActiveRatesByDate();
					$defaults['dsa_location_lst'] = json_encode($rateData);
					
					
					 
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
						$defaults['dsa_amount'] = '0.00';
					}
					// Default Briefing amount
					$defaults['dsa_briefing'] = '0.00';
					// Default Debriefing amount
					$defaults['dsa_debriefing'] = '0.00';
					// Default Airport amount
					$defaults['dsa_airport'] = '0.00';
					// Default Transfer amount
					$defaults['dsa_transfer'] = '0.00';
					// Default Hotel amount
					$defaults['dsa_hotel'] = '0.00';
					// Default Visa amount
					$defaults['dsa_visa'] = '0.00';
					// Default Outfit amount
					$defaults['dsa_outfit'] = '0.00';
					// Default Other amount
					$defaults['dsa_other'] = '0.00';
					// Default Advance amount
					$defaults['dsa_advance'] = '0.00';
					// Default approval details
					$defaults['dsa_approval'] = '';
				} else {
					// Defaults for editing an existing DSA record
					// get DSACompose met activity_id = $activityId
					$sql = '
SELECT
  cmp.*,
  rte.batch_id,
  rte.country,
  rte.rate,
  cnt.id AS cy_id,
  con.display_name AS approver_name
FROM
  civicrm_dsa_compose cmp
	LEFT JOIN civicrm_contact con
  	ON cmp.approval_cid = con.id,
  civicrm_dsa_rate rte,
  civicrm_country cnt
WHERE
  cmp.activity_id = ' . $dsaId . ' AND
  cmp.loc_id = rte.id AND
  rte.country = cnt.iso_code
					';
//dpm($sql, '$sql (fetch default values)');
					$dao_defaults = CRM_Core_DAO::executeQuery($sql);
					$result = $dao_defaults->fetch();
					$loc_id = $dao_defaults->loc_id;
					$dt = $dao_defaults->ref_date;
					// Retieve all available locations / rates for the specified location
					$rateData = CRM_Dsa_Page_DSAImport::getAllRatesByLocationId($loc_id); // should ref_date from previous query be used?
					$defaults['dsa_location_lst'] = json_encode($rateData);
					$defaults['dsa_country'] = $dao_defaults->cy_id;
					$defaults['dsa_load_location'] = $dao_defaults->loc_id . '|' . $dao_defaults->rate; // gets set when JQ does the initial location load
					$defaults['dsa_location_id'] = $dao_defaults->loc_id;
					$defaults['dsa_participant_id'] = $dao_defaults->contact_id;
					$defaults['dsa_participant_role'] = $dao_defaults->relationship_type_id;
					$defaults['dsa_participant'] = $dao_defaults->contact_id . '|' . $dao_defaults->relationship_type_id . '|' . $dao_defaults->type;
					$defaults['dsa_type'] = $dao_defaults->type;
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
					if (is_null($dao_defaults->approval_cid)) {
						$defaults['dsa_approval'] = '';
					} else {
						$defaults['dsa_approval'] = 'Approved ' . $dao_defaults->approval_datetime . ' by ' . $dao_defaults->approver_name;
					}
				}
				
				// Apply default values
				if (isset($defaults)) {
					$form->setDefaults($defaults);
				}
				
				// Apply filter on status list
				// retrieve a complete list of available activity statusses
				$arStatusLst = _retrieveActivityStatusList();
				// Find field "status_id" and its value
				$fldStatus = _findFieldByName($form, 'status_id');
				// Modify the offered activity status list depending on activity_type and current status
				if (!empty($fldStatus)) {
					if (isset($fldStatus['id'])) {
						$fldVal = $fldStatus['obj']->_values[0];
						$fldOptions = $fldStatus['obj']->_options; // use existing options list for modifications: might contain a filter from other modules
						
						// convert options to a DSA-specific options list
						switch ($arStatusLst[$fldVal]['name']) {
							case 'Scheduled':
							case 'Cancelled':
							case 'Not Required':
							case 'dsa_payable':
								$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required', 'dsa_payable');
								$addSelect = TRUE;
								break;
							case 'dsa_paid':
								$allowedStatusName = array('dsa_paid');
								$addSelect = FALSE;
								break;
							default:
								$allowedStatusName = array($arStatusLst[$fldVal]);
								$addSelect = TRUE;
						}
						
						// apply modifictions
						$newOptions = _leaveAllowedStatusOptions($fldOptions, $allowedStatusName, $addSelect);
						$form->_elements[$fldStatus['id']]->_options = $newOptions;
					}
				}
			} else {
				// CRM_Case_Form_Activity but not type DSA
				// Apply filter on status list
				// retrieve a complete list of available activity statusses
				$arStatusLst = _retrieveActivityStatusList();
				// Find field "status_id" and its value
				$fldStatus = _findFieldByName($form, 'status_id');
				// Modify the offered activity status list
				if (!empty($fldStatus)) {
					if (isset($fldStatus['id'])) {
						$fldVal = $fldStatus['obj']->_values[0];
						$fldOptions = $fldStatus['obj']->_options; // use existing options list for modifications: might contain a filter from other modules
						
						// convert options to a DSA-specific options list
						$removeStatusName = array('dsa_payable', 'dsa_paid');
						$addSelect = TRUE;
						
						// apply modifictions
						$newOptions = _removeDisallowedStatusOptions($fldOptions, $removeStatusName, $addSelect);
						$form->_elements[$fldStatus['id']]->_options = $newOptions;
					}
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
//echo '<pre>';
//dpm($fields, "HOOK_VALIDATEFORM: FIELDS ON ". $formName);
//echo '================================';
//print_r ($fields);
//echo '================================';
//dpm($form, "HOOK_VALIDATEFORM: ". $formName);
//print_r ($form);
//echo '================================';
//echo '</pre>';
//$errors['dsa_participant'] = 'Save blocked for debugging purposes';
//exit();
	switch($formName) {
		case 'CRM_Case_Form_Activity':
			if ($form->_activityTypeName == 'DSA') {
				// participant
				if ($fields['dsa_participant'] == '') {
					$errors['dsa_participant'] = 'Please select a participant';
				}
				//===========================================================================================================
/*
				// only open DSA activity within case for this dsa_participant_id?
				$caseId = $form->_caseId;
				$activityId = $form->_activityId;
				$participant_id = $fields['dsa_participant_id'];
				// all current revisions of the current case' activities of type DSA in an unfinished status
				try {
//					$dao_activities = _dao_findOpenDsaActivities($caseId, $activityId, $participant_id);
				} catch(Exception $e) {
					// ignore
				}
//echo '<pre>';
//print_r ($dao_activities);
//echo '</pre>';
//exit();
				if ($dao_activities->N > 0) {
					$errors['status_id'] = 'Only one \'open\' DSA activity allowed.';
				}
*/
				//===========================================================================================================

				// days
				$fieldValue=trim($fields['dsa_days']);	
				if ((!CRM_Utils_Type::validate($fieldValue, 'Positive', False)) && (!$fieldValue==0)) {
					$errors['dsa_days'] = 'Please enter a valid number of days';
				}
				// amounts
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
				
				if (!array_key_exists('dsa_other', $errors)) {
					if (($fields['dsa_other']!=0) && (trim($fields['dsa_other_description'])=='')) {
						$errors['dsa_other_description'] = ts('Please describe Expense Other');
						$result = FALSE;
					}
				}
			}
			break;
	}
	return;
}


function _amountCheck($fieldName, $fields, &$errors) {
 	try {
		$fieldValue=trim($fields[$fieldName]);
		if ((!CRM_Utils_Type::validate($fieldValue, 'Float', False)) && (!$fieldValue==0))  {
			$errors[$fieldName] = 'Please enter a valid amount';
		} elseif ($fieldValue<0) {
			$errors[$fieldName] = 'Minimum amount is: 0';
		} elseif ($fieldValue>99999.99) {
			$errors[$fieldName] = 'Maximum amount is: 99999.99';
		}
		return TRUE;
	} catch(Exception $e) {
		$errors[''] = 'Caught exception: ' . $e->getMessage();
	}
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
//exit();
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
				'dsa_type'				=>	array(
											'column'	=> 'type',
											'type'		=> 'number',
											),
				'dsa_participant_id'	=>	array(
											'column'	=> 'contact_id',
											'type'		=> 'text',
											),
				'dsa_participant_role'	=>	array(
											'column'	=> 'relationship_type_id',
											'type'		=> 'text',
											),
				'dsa_location_id'			=>	array(
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
			// add / remove approver
			$approver_id = $form->_currentUserId;
			$statusList = _retrieveActivityStatusList();
			switch ($statusList[$form->_submitValues['status_id']]['name']) {
			case 'dsa_payable':
				// set approver
				$input['approval_cid'] = $approver_id;
				$input['approval_datetime'] = 'now()';
				break;
			case 'dsa-paid':
				// lease as is
				break;
			default:
				// reset approver
				$input['approval_cid'] = 'NULL';
				$input['approval_datetime'] = 'NULL';
			};
/*
echo '=statusList======================================';
echo '<pre>';
print_r($statusList);
echo '</pre>';
*/
			
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
//	exit();
	return;
}

function _dao_findOpenDsaActivities($caseId, $activityId=NULL, $participant_id=NULL) {
	// function will look within the case for other DSA activities (excluding a 'current' one), holding the same participant id
	if (is_null($activityId)) {
		$activityId = '\'NULL\'';
	} elseif ($activityId=='') {
		$activityId = '\'NULL\'';
	}
	$sql = '
		SELECT	ca.*
		FROM	civicrm_case_activity ca,
				civicrm_activity act
		WHERE	ca.case_id = ' . $caseId . '
		AND		act.id = ca.activity_id
		AND		act.activity_type_id IN (
					SELECT	vl.value
					FROM	civicrm_option_group gp,
							civicrm_option_value vl
					WHERE	gp.name = \'activity_type\'
					AND		gp.id = vl.option_group_id
					AND		vl.name = \'DSA\'
					)
		AND		act.is_current_revision = 1
		AND		act.status_id NOT IN (
					SELECT	vl.value
					FROM	civicrm_option_group gp,
							civicrm_option_value vl
					WHERE	gp.name = \'activity_status\'
					AND		gp.id = vl.option_group_id
					AND		vl.name IN (\'dsa_paid\', \'Cancelled\', \'Not Required\')
					)
		AND		dsa.activity_id = ifnull(act.original_id, act.id)
		AND		ca.activity_id NOT IN (' . $activityId . ')
		AND		dsa.contact_id IN (' . $participant_id . ')
		';
	$dao_activities = CRM_Core_DAO::executeQuery($sql);
	return $dao_activities;
}

/*
 * Function to retrieve all values from option group "activity_status"
 * Values are returned in associative array, 
 * ar['status_id'] = array( 'name' => <status_name>, 'label' => <status_label> )
 */

function _retrieveActivityStatusList() {
	// Retrieve all possible values for option group "activity_status" values
	$params = array(
		'version' => 3,
		'q' => 'civicrm/ajax/rest',
		'sequential' => 1,
		'option_group_name' => 'activity_status',
	);
	$dao_options = civicrm_api('OptionValue', 'get', $params);

	// build translation array: id -> name
	$arStatus = array();
	foreach($dao_options['values'] as $dao_key=>$dao_value) {
		$arStatus[$dao_value['value']] = array(
			'name' => $dao_value['name'],
			'label' => $dao_value['label'],
		);
	}

	return $arStatus;
}

/*
 * Function to modify the list of options for activity status
 * Options present in $currentOptionsList, but NOT listed in $allowedNamesAr will be removed from the list
 * Use $keepEmptyFirst to keep "- SELECT -" in $currentOptionsList (if available)
 * Used to remove standard activity statusses from DSA activities
 */
function _leaveAllowedStatusOptions($currentOptionsList, $allowedNamesAr, $keepEmptyFirst=TRUE) {
	// build a translation list: option names to option labels
	$nameToLabel = array();
	$arStatus = _retrieveActivityStatusList();
	foreach($arStatus as $key=>$value) {
		$nameToLabel[$value['name']] = $value['label'];
	}
	
	// add allowed options to options list -if available-
	$resultAr = array();
	$n=-1;
//dpm($currentOptionsList, '>> $currentOptionsList');
//dpm($allowedNamesAr, '>> $allowedNamesAr');
	foreach($currentOptionsList as $key_opt=>$value_opt) {
		$n++;
		$currentName = $value_opt['text'];
		if (($n==0) && ($value_opt['attr']['value']=='') && ($keepEmptyFirst)) {
			$resultAr[] = $value_opt;
		} else {
			foreach($allowedNamesAr as $key_allow=>$value_allow) {
				if ($nameToLabel[$value_allow]==$currentName) {
					$resultAr[] = $value_opt;
				}
			}
		}
	}
			
	// Apply allowed options to status_id field
	return $resultAr;
}

/*
 * Function to modify the list of options for activity status
 * Options present in $currentOptionsList that are listed in $removeNamesAr as well will be removed from the list
 * Use $keepEmptyFirst to keep "- SELECT -" in $currentOptionsList (if available)
 * Used to remove special DSA statusses from non-DSA activities
 */
function _removeDisallowedStatusOptions($currentOptionsList, $removeNamesAr, $keepEmptyFirst=TRUE) {
	// build a translation list: option names to option labels
	$nameToLabel = array();
	$arStatus = _retrieveActivityStatusList();
	foreach($arStatus as $key=>$value) {
		$nameToLabel[$value['name']] = $value['label'];
	}
	
	// add allowed options to options list -if available-
	$resultAr = array();
	$n=-1;
	foreach($currentOptionsList as $key_opt=>$value_opt) {
		$n++;
		$currentName = $value_opt['text'];
		$keep = TRUE;
		if (($n==0) && ($value_opt['attr']['value']=='')) {
			if (!$keepEmptyFirst) {
				$keep = FALSE;
			}
		} else {
			foreach($removeNamesAr as $key_remove=>$value_remove) {
				if ($nameToLabel[$value_remove]==$currentName) {
					$keep = FALSE;
				}
			}
		}
		if ($keep) {
			$resultAr[] = $value_opt;
		}
	}
			
	// Apply allowed options to status_id field
	return $resultAr;
}

/* Function to find a field in the form definition $form
 * returns both the fields element id within $form->_elements and the corresponding field object
 */
function _findFieldByName($form, $fldName) {
	$result = array();
	foreach($form->_elements as $elmNo=>$elmObj) {
		if ($elmObj->_attributes) {
			if ($elmObj->_attributes['name']) {
				if ($elmObj->_attributes['name'] == $fldName) {
					$result['id'] = $elmNo;
					$result['obj'] = $elmObj;
				}
			}
		}
	}
	return $result;
}

function _getCaseParticipantList($case_id) {
	$sql = '
		SELECT	rs.contact_id_b as contact_id,
				rt.label_b_a as role,
				rs.relationship_type_id as type_id,
				ct.display_name as name,
				\'1\' as payment_type,
				\'Payment\' as payment_description
		FROM	civicrm_relationship rs,
				civicrm_relationship_type rt,
				civicrm_contact ct
		WHERE	rs.case_id = ' . $case_id . '
				  and	rt.id = rs.relationship_type_id
				  and	rs.contact_id_b = ct.id
		ORDER BY
				role,
				name
		'; // for creditation: UNION DISTINCT (SELECT...) with the current DSA's contact/role; THEN sort
		$dao_role = CRM_Core_DAO::executeQuery($sql);
		$role_ar = array();
		while($dao_role->fetch()) {
			$role_ar[$dao_role->contact_id . '|' . $dao_role->type_id . '|' . $dao_role->payment_type] = ts($dao_role->payment_description) . ': ' . $dao_role->name . ' (' . ts($dao_role->role) . ')';
		}
		return $role_ar;
}
