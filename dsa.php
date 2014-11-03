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
//dpm($form, 'Pre DSA mod form data ' . $formName);
//dpm(CRM_Core_Permission::check('create DSA activity'), 'Permission create DSA activity');
//CRM_Core_Error::debug('START DEBUG -> ' . $formName, $form);

	$loadJs = false;
	switch($formName) {
		case 'CRM_Case_Form_CaseView':
			// avoid creation of DSA by unauthorised people
			foreach($form->_elements as $key=>$value) {
				if ($value->_attributes) {
					if ($value->_attributes['name']) {
						if ($value->_attributes['name']=='activity_type_id') {
							foreach($value->_options as $opt_key=>$opt_val) {
								if ($opt_val['text']=='DSA') {
									if (!CRM_Core_Permission::check('create DSA activity')) {
										unset($form->_elements[$key]->_options[$opt_key]);
										//$form->_elements[$key]->_options[$opt_key]
									}
								}
								if ($opt_val['text']=='Representative payment') {
									if (!CRM_Core_Permission::check('create Representative payment activity')) {
										unset($form->_elements[$key]->_options[$opt_key]);
										//$form->_elements[$key]->_options[$opt_key]
									}
								}
							}
						}
					}
				}
			}
			
		
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
				_dsa_buildform_dsa($formName, $form);
			} elseif ($form->getVar('_activityTypeName')=='Representative payment') {
				_dsa_buildform_representative_payment($formName, $form);
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

		case 'CRM_Case_Form_ActivityChangeStatus':
			// debugging to block status modification in cases activity list
//CRM_Core_Error::debug($formName, $form);
/*
dpm($form, $formName);
dpm($_GET, '$_GET');
dpm($_POST, '$_POST');
dpm($_REQUEST, '$_REQUEST');
			$key = CRM_Core_DAO::escapeString(CIVICRM_SITE_KEY);
dpm($key, 'site key');
			$actId = 702;
			$sql = "select SHA1(CONCAT('" . $key . "', '" . $actId . "'))";
dpm($sql, '$sql');
			$dao = CRM_Core_DAO::executeQuery($sql);
dpm($dao, 'dao1');
			$dao->fetch();
dpm($dao, 'dao2');
			*/
//dpm(CRM_Utils_Recent::get());
/*
SELECT
  SUBSTR(SHA1(CONCAT('b68bfcc3cd3d99c0a33725a836cd82e6',act.id)), 1, 7) AS 'SHA1',
  act.*
FROM
  civicrm_activity act
*/
			break;

//			default:
//dpm($formName, $form);
		}
	
	if ($loadJs) {
		//CRM_Core_Resources::singleton()->addScriptFile('nl.pum.dsa', 'js/dsa.js');
	}
	return;
}

/**
 * executive function for hook_civicrm_buildForm when $formName = 'DSA'
 */
function _dsa_buildform_dsa($formName, &$form) {
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

	if (is_null($dsaId)) {
		$allowEdit = CRM_Core_Permission::check('create DSA activity');
	} else {
		$allowEdit = CRM_Core_Permission::check('edit DSA activity');
	}
	
	/* with a known $dsaId, we can find out if additional dsa data is already present
	 * if so, it should be retrieved for presentation
	 * if not, we should prepare default values
	 */
	if (is_null($dsaId)) {
		$dsaIsDefined = FALSE;
	} else {
		$sqlDsaRecord = 'SELECT count(case_id) as recCount FROM civicrm_dsa_compose WHERE activity_id = ' . $dsaId;
		$daoDsaRecord = CRM_Core_DAO::executeQuery($sqlDsaRecord);
		$daoDsaRecord->fetch();
		$dsaIsDefined = ($daoDsaRecord->recCount>0); // FALSE if recCount==0, otherwise TRUE
	}
	
	// main activity (case) contains start- and enddate as custom field
	$ma = array(
		'start' => NULL,
		'end' => NULL,
		'days' => 0,
	);

	// retrieve table names and columns for custom groups
	$tbl = array();
	$tbl['main_activity_info'] = _getCustomTableInfo('main_activity_info'); // sitiation prior to the introduction of Travel case
	$tbl['Info_for_DSA'] = _getCustomTableInfo('Info_for_DSA'); // situation intended for Travel case
//dpm($tbl);	
	$sql = "
SELECT
  cas.id,
  " . $tbl['main_activity_info']['sql_columns'] . ",
  " . $tbl['Info_for_DSA']['sql_columns'] . "
FROM
  civicrm_case cas
  LEFT JOIN " . $tbl['main_activity_info']['group_table'] . "
    ON " . $tbl['main_activity_info']['group_table'] . ".entity_id = cas.id
  LEFT JOIN " . $tbl['Info_for_DSA']['group_table'] . "
    ON " . $tbl['Info_for_DSA']['group_table'] . ".entity_id = cas.id
WHERE
  cas.id = " . $form->_caseId . "
	";
	$dao = CRM_Core_DAO::executeQuery($sql);
	if (!$dao->N == 1) {
		// leave main_start and main_end empty; leave main_days to 0
	} else {
		$dao->fetch();
		if (!is_null($dao->Start_date) && !is_null($dao->End_date)) {
			$ma['start'] = date_create($dao->Start_date);
			$ma['end'] = date_create($dao->End_date);
		} else {
			if (!is_null($dao->main_activity_start_date)) {
				$ma['start'] = date_create($dao->main_activity_start_date);
			}
			if (!is_null($dao->main_activity_end_date)) {
				$ma['end'] = date_create($dao->main_activity_end_date);
			}
		}
	}
	if (is_null($ma['start']) || is_null($ma['end'])) {
		// leave main_days to 0
	} else {
		$ma['days'] = date_diff($ma['end'], $ma['start']);
		$ma['days'] = $ma['days']->days + 1;
	}
//dpm($result, '$result');
//dpm($ma);
	
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
		$role_ar = _getCaseParticipantList_dsa($form->_caseId, $form->_activityId);
	}
	$participant_options = array('' => ts('- select -')) + $role_ar;
	$form->addElement('select', 'dsa_participant', ts('Participant'), $participant_options);
	// Add a hidden field to hold the selected participants contact id
	$form->add('hidden', 'dsa_participant_id', NULL, array('id'=> 'dsa_participant_id'));
	// Add a hidden field to hold the selected participants relationsship_type_id
	$form->add('hidden', 'dsa_participant_role', NULL, array('id'=> 'dsa_participant_role'));
	// Add a hidden fields to hold main activity startdate, enddate and no. of days (i.e. end - start + 1)
	$form->add('hidden', 'main_dates', NULL, array('id'=> 'main_dates'));
	$form->add('hidden', 'main_days', NULL, array('id'=> 'main_days'));
	// Add hiddenfields to hold invoice numbers
	$form->add('hidden', 'invoice_number', NULL, array('id'=> 'invoice_number', 'label'=>ts('Invoice Number')));
	$form->add('hidden', 'invoice_dsa', NULL, array('id'=> 'invoice_dsa', 'label'=>ts('Invoice code DSA')));
	$form->add('hidden', 'invoice_briefing', NULL, array('id'=> 'invoice_briefing', 'label'=>ts('Invoice code Briefing')));
	$form->add('hidden', 'invoice_airport', NULL, array('id'=> 'invoice_airport', 'label'=>ts('Invoice code Airport')));
	$form->add('hidden', 'invoice_transfer', NULL, array('id'=> 'invoice_transfer', 'label'=>ts('Invoice code Transfer')));
	$form->add('hidden', 'invoice_hotel', NULL, array('id'=> 'invoice_hotel', 'label'=>ts('Invoice code Hotel')));
	$form->add('hidden', 'invoice_visa', NULL, array('id'=> 'invoice_visa', 'label'=>ts('Invoice code Visa')));
	$form->add('hidden', 'invoice_medical', NULL, array('id'=> 'invoice_medical', 'label'=>ts('Invoice code Medical')));
	$form->add('hidden', 'invoice_other', NULL, array('id'=> 'invoice_other', 'label'=>ts('Invoice code Other')));
	$form->add('hidden', 'invoice_advance', NULL, array('id'=> 'invoice_advance', 'label'=>ts('Invoice code Advance')));
	// Add hidden field to control which fields can be edited (jquery, when form is first displayed)
	$form->add('hidden', 'restrictEdit', NULL, array('id'=> 'restrictEdit'));
	// Add hidden field to store details for creditations
	$form->add('hidden', 'credit_data', NULL, array('id'=> 'credit_data'));
	// Add hidden field to store credited activity id
	$form->add('hidden', 'credit_act_id', NULL, array('id'=> 'credit_act_id'));

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
	$form->add('text', 'dsa_days', ts('DSA Days'));
	// Add DSA amount field
	$form->add('text', 'dsa_amount', ts('DSA Amount'));
	// Add Expense amount briefing field
	$form->add('text', 'dsa_briefing', ts('Expense Briefing / Debriefing'));
	// Add Expense amount debriefing field
	//$form->add('text', 'dsa_debriefing', ts('Expense Debriefing'));
	// Add Expense amount airport field
	$form->add('text', 'dsa_airport', ts('Expense Airport'));
	// Add Expense amount transfer field
	$form->add('text', 'dsa_transfer', ts('Expense Transfer'));
	// Add Expense amount hotel field
	$form->add('text', 'dsa_hotel', ts('Expense Hotel'));
	// Add Expense amount visa field
	$form->add('text', 'dsa_visa', ts('Expense Visa'));
	// Add Expense amount medical field
	$form->add('text', 'dsa_medical', ts('Expense Medical'));
	// Add Expense amount other field (incl description)
	$form->add('text', 'dsa_other', ts('Expense Other'));
	$form->add('textarea', 'dsa_other_description', ts('Expense Other Description'));
	// Add Expense advance amount field
	$form->add('text', 'dsa_advance', ts('Expense Advance'));
	// Add a field to hold approval details
	$form->add('text', 'dsa_approval', ts('Approval'), array('id'=>'dsa_approval'));
	
	// Default values for
	// - new creation - ok
	// - open
	// - edit
	// - creditation create
	// - creditation read
	// - creditation save
	
	// Default field values =======================================
	// apply default values for details from the case record, regardless of create/edit/read/validate mode
	if (is_null($ma['start']) || is_null($ma['end'])) {
		$defaults['main_dates'] = ' (' . ts('no start date or no end date available') . ')';
		$defaults['main_days'] = 0;
	} else {
		$defaults['main_dates'] = ' ' . ts('days from start date') . ' ' . $ma['start']->format('Y-m-d') . ' ' . ts('to end date') . ' ' . $ma['end']->format('Y-m-d');
		$defaults['main_days'] = $ma['days'];
	}
	/* For most of the form, there are three scenario's here:
	   - manual creation of a new activity,
	   - editing an existing activity without additional dsa data present (automatically generated ones),
	   - editing an existing (additional dsa data alredy present) and
	   - validation failure
	*/
	if ($form->_flagSubmitted) {
		// Defaults in case of a validation error
		// All submitted values are present: leave most to civi except dsa_location_lst:
		// That field was removed in jquery (temlate) and needs to be reapplied, or the location won't be reloaded properly
		$loc_id = $form->_submitValues['dsa_location_id'];
		$rateData = CRM_Dsa_Page_DSAImport::getAllRatesByLocationId($loc_id); // should ref_date from previous query be used?
		$defaults['dsa_location_lst'] = json_encode($rateData);
		
	} elseif (!$dsaIsDefined) { //(is_null($dsaId)) {
		// Defaults for new DSA creation (and for automatically generated DSA activities without additional data in civicrm_dsa_compose)
		// Default DSA country (part 1): retrieve contacts primary adress' country (id)
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
		// Default DSA country (part 2): no country derived from primary address: possibly a country project
		if (!isset($defaults['dsa_country'])) {
			$params = array(
				'version' => 3,
				'q' => 'civicrm/ajax/rest',
				'sequential' => 1,
				'contact_id' => $form->_currentlyViewedContactId,
			);
			try {
				$result = civicrm_api('Contact', 'get', $params);
				if (($result['values']['0']['contact_type'] == 'Organization') && ($result['values']['0']['contact_sub_type'][0] == 'Country')) {
					foreach(CRM_Core_PseudoConstant::country() as $key=>$value) {
						if ($value == $result['values']['0']['organization_name']) {
							$defaults['dsa_country'] = $key;
						}
					}
				}
			} catch(Exception $e) {
				// just continue without default value
			}
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
			$defaults['dsa_days'] = $ma['days'];
		}
		// Default DSA amount
		if (!is_null($percentageDefault)) {
			$defaults['dsa_amount'] = '0.00';
		}
		// Default Briefing amount
		$defaults['dsa_briefing'] = '0.00';
		// Default Debriefing amount
		//$defaults['dsa_debriefing'] = '0.00';
		// Default Airport amount
		$defaults['dsa_airport'] = '0.00';
		// Default Transfer amount
		$defaults['dsa_transfer'] = '0.00';
		// Default Hotel amount
		$defaults['dsa_hotel'] = '0.00';
		// Default Visa amount
		$defaults['dsa_visa'] = '0.00';
		// Default Medical amount
		$params = array(
			'version' => 3,
			'q' => 'civicrm/ajax/rest',
			'sequential' => 1,
			'option_group_name' => 'dsa_configuration',
			'name' => 'default_medical_amount',
			'return' => 'name,value',
		);
		try {
			$result = civicrm_api('OptionValue', 'getsingle', $params);
			$defaults['dsa_medical'] = $result['value'];
		} catch (Exception $e) {
			$defaults['dsa_medical'] = '0.00';
		}
		// Default Other amount
		$defaults['dsa_other'] = '0.00';
		// Default Advance amount
		$defaults['dsa_advance'] = '0.00';
		// Default approval details
		$defaults['dsa_approval'] = '';
		// Default flag to allow editing of all fields
		$defaults['restrictEdit'] = '0';
		// Details for creditation of existing (paid) DSA activities (for jQuery to retrieve and process)
		$defaults['credit_data'] = _creditationValues($role_ar);
		
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
    ON cmp.approval_cid = con.id
  LEFT JOIN civicrm_dsa_rate rte
    ON cmp.loc_id = rte.id
  LEFT JOIN civicrm_country cnt
    ON rte.country = cnt.iso_code
WHERE
  cmp.activity_id = ' . $dsaId . '
		';
//dpm($sql, '$sql (fetch default values)');
		$dao_defaults = CRM_Core_DAO::executeQuery($sql);
		$result = $dao_defaults->fetch();
//dpm($dao_defaults, "dao defaults");
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
		if ($dao_defaults->type == 3) {
			$defaults['dsa_participant'] = $dao_defaults->contact_id . '|' . $dao_defaults->relationship_type_id . '|' . $dao_defaults->type . '|' . $dao_defaults->credited_activity_id;
		} else {
			$defaults['dsa_participant'] = $dao_defaults->contact_id . '|' . $dao_defaults->relationship_type_id . '|' . $dao_defaults->type . '|0';
		}
		
//dpm($defaults['dsa_participant'], 'Default participant');
		$defaults['dsa_type'] = $dao_defaults->type;
		$defaults['dsa_percentage'] = $dao_defaults->percentage;
		$defaults['dsa_days'] = $dao_defaults->days;
		$defaults['dsa_amount'] = $dao_defaults->amount_dsa;
		$defaults['dsa_briefing'] = $dao_defaults->amount_briefing;
		//$defaults['dsa_debriefing'] = $dao_defaults->amount_debriefing;
		$defaults['dsa_airport'] = $dao_defaults->amount_airport;
		$defaults['dsa_transfer'] = $dao_defaults->amount_transfer;
		$defaults['dsa_hotel'] = $dao_defaults->amount_hotel;
		$defaults['dsa_visa'] = $dao_defaults->amount_visa;
		$defaults['dsa_medical'] = $dao_defaults->amount_medical;
		$defaults['dsa_other'] = $dao_defaults->amount_other;
		$defaults['dsa_other_description'] = $dao_defaults->description_other;
		$defaults['dsa_advance'] = $dao_defaults->amount_advance;
		if (is_null($dao_defaults->approval_cid)) {
			$defaults['dsa_approval'] = '';
		} else {
			$defaults['dsa_approval'] = 'Approved ' . $dao_defaults->approval_datetime . ' by ' . $dao_defaults->approver_name;
		}
		// Details for creditation of existing (paid) DSA activities (for jQuery to retrieve and process)
		$defaults['credit_data'] = _creditationValues($role_ar);
		$defaults['invoice_number'] = $dao_defaults->invoice_number;
		$defaults['invoice_dsa'] = $dao_defaults->invoice_dsa;
		$defaults['invoice_briefing'] = $dao_defaults->invoice_briefing;
		$defaults['invoice_airport'] = $dao_defaults->invoice_airport;
		$defaults['invoice_transfer'] = $dao_defaults->invoice_transfer;
		$defaults['invoice_hotel'] = $dao_defaults->invoice_hotel;
		$defaults['invoice_visa'] = $dao_defaults->invoice_visa;
		$defaults['invoice_medical'] = $dao_defaults->invoice_medical;
		$defaults['invoice_other'] = $dao_defaults->invoice_other;
		$defaults['invoice_advance'] = $dao_defaults->invoice_advance;
		
	}
	
	// Apply filter on status list
	// retrieve a complete list of available activity statusses
	$arStatusLst = _retrieveActivityStatusList();
	// Find field "status_id" and its value
	$fldStatus = _findFieldByName($form, 'status_id');

	// Modify the offered activity status list depending on activity_type and current status
	// and decide which fields should be eitable: 0=edit all (restrict none), 1=edit status only (restrict the rest), 2=edit none (restict all)
	$restrictEdit = '0';
	if (!empty($fldStatus)) {
		if (isset($fldStatus['id'])) {
			$fldVal = $fldStatus['obj']->_values[0];
			$fldOptions = $fldStatus['obj']->_options; // use existing options list for modifications: might contain a filter from other modules
			
			// convert options to a DSA-specific options list
			switch ($arStatusLst[$fldVal]['name']) {
				case 'Scheduled':
				case 'Cancelled':
				case 'Not Required':
					if (CRM_Core_Permission::check('approve DSA activity')) {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required', 'dsa_payable');
					} else {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required');
					}
					$addSelect = TRUE;
					$restrictEdit = '0';
					break;
				case 'dsa_payable':
					if (CRM_Core_Permission::check('approve DSA activity')) {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required', 'dsa_payable'); // allow status payable for approver
					} elseif (CRM_Core_Permission::check('edit DSA activity')) {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required', 'dsa_payable'); // allow status payable for editor as that is the current status
					} else {
						$allowedStatusName = array('dsa_payable'); // current status only
					}
					$addSelect = TRUE;
					$restrictEdit = '1';
					break;
				case 'dsa_paid':
					$allowedStatusName = array('dsa_paid'); // current status only
					$addSelect = FALSE;
					$restrictEdit = '2';
					break;
				default:
					$allowedStatusName = array($arStatusLst[$fldVal]);
					$addSelect = TRUE;
					$restrictEdit = '0';
			}
			
			// if creation / editing was not allowed, then present read mode
			if (!$allowEdit) {
				$restrictEdit = '2';
			}
			
			// apply modifictions
			$newOptions = _leaveAllowedStatusOptions($fldOptions, $allowedStatusName, $addSelect);
			$form->_elements[$fldStatus['id']]->_options = $newOptions;
		}
	}
	$defaults['restrictEdit'] = $restrictEdit;
	
	// Apply default values
	if (isset($defaults)) {
		$form->setDefaults($defaults);
	}

}

/**
 * executive function for hook_civicrm_buildForm when $formName = 'Representative payment'
 */
function _dsa_buildform_representative_payment($formName, &$form) {
	/* civi applies version control on activities
	 * an activity can either be:
	 * - a new record (no activity_id, no original_id)
	 * - a 1st edit   (activity_id, no original_id)
	 * - a nth edit   (activity_id and a different original_id)
	 * Representative payment (in civicrm_representative_compose) is always linked to the activities original id (either through original_id, or when NULL through activity_id)
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

	if (is_null($dsaId)) {
		$allowEdit = CRM_Core_Permission::check('create Representative payment activity');
	} else {
		$allowEdit = CRM_Core_Permission::check('edit Representative payment activity');
	}
	
	/* with a known $dsaId, we can find out if additional Representative payment data is already present
	 * if so, it should be retrieved for presentation
	 * if not, we should prepare default values
	 */
	if (is_null($dsaId)) {
		$dsaIsDefined = FALSE;
	} else {
		$sqlDsaRecord = 'SELECT count(case_id) as recCount FROM civicrm_representative_compose WHERE activity_id = ' . $dsaId;
		$daoDsaRecord = CRM_Core_DAO::executeQuery($sqlDsaRecord);
		$daoDsaRecord->fetch();
		$dsaIsDefined = ($daoDsaRecord->recCount>0); // FALSE if recCount==0, otherwise TRUE
	}
		
	// Representative payment fields are displayed using a custom .tpl-file
	// assume templates are in a templates folder relative to this file
	$templatePath = realpath(dirname(__FILE__)."/templates");
	// add template to the form
	CRM_Core_Region::instance('page-body')->add(
		array(
			'template' => "{$templatePath}/representative_payment_section.tpl"
		)
	);
	// Form structure =============================================
	// Add a hidden field to hold the payment type (from participant selection)
	$form->add('hidden', 'dsa_type', NULL, array('id'=> 'dsa_type'));
	// Add a hidden field to hold a complete list of participators in this case 
	// Retrieve all this cases participants
	if (isset($form->_caseId)) {
		$role_ar = _getCaseParticipantList_representative_payment($form->_caseId, $form->_activityId);
	}
	$participant_options = array('' => ts('- select -')) + $role_ar;
	$form->addElement('select', 'dsa_participant', ts('Participant'), $participant_options);
	// Add a hidden field to hold the selected participants contact id
	$form->add('hidden', 'dsa_participant_id', NULL, array('id'=> 'dsa_participant_id'));
	// Add a hidden field to hold the selected participants relationsship_type_id
	$form->add('hidden', 'dsa_participant_role', NULL, array('id'=> 'dsa_participant_role'));
	// Add hidden fields to hold invoice numbers
	$form->add('hidden', 'invoice_number', NULL, array('id'=> 'invoice_number', 'label'=>ts('Invoice Number')));
	$form->add('hidden', 'invoice_rep', NULL, array('id'=> 'invoice_dsa', 'label'=>ts('Invoice code DSA')));
	// Add hidden field to control which fields can be edited (jquery, when form is first displayed)
	$form->add('hidden', 'restrictEdit', NULL, array('id'=> 'restrictEdit'));
	// Add Representative payment amount field
	$form->add('text', 'dsa_amount', ts('Representative Amount'));
	// Add a field to hold approval details
	$form->add('text', 'dsa_approval', ts('Approval'), array('id'=>'dsa_approval'));
	
	// Default values for
	// - new creation - ok
	// - open
	// - edit
	
	// Default field values =======================================
	/* For most of the form, there are three scenario's here:
	   - manual creation of a new activity,
	   - editing an existing activity without additional rep data present (automatically generated ones),
	   - editing an existing (additional rep data alredy present) and
	   - validation failure
	*/
	if ($form->_flagSubmitted) {
		// Defaults in case of a validation error
		// All submitted values are present: leave to civi
		
	} elseif (!$dsaIsDefined) { //(is_null($dsaId)) {
		// Defaults for new Representative payment creation (and for automatically generated Representative payment activities without additional data in civicrm_representative_compose)
		// Default Representative payment amount
		$params = array(
			'version' => 3,
			'q' => 'civicrm/ajax/rest',
			'sequential' => 1,
			'option_group_name' => 'rep_payment_configuration',
			'name' => 'default_payment_amount',
			'return' => 'name,value',
		);
		try {
			$result = civicrm_api('OptionValue', 'getsingle', $params);
			$defaults['dsa_amount'] = $result['value'];
		} catch (Exception $e) {
			$defaults['dsa_amount'] = '0.00';
		}
		// Default approval details
		$defaults['dsa_approval'] = '';
		// Default flag to allow editing of all fields
		$defaults['restrictEdit'] = '0';
		
	} else {
		// Defaults for editing an existing DSA record
		// get DSACompose met activity_id = $activityId
		$sql = '
SELECT
  cmp.*,
  con.display_name AS approver_name
FROM
  civicrm_representative_compose cmp
  LEFT JOIN civicrm_contact con
    ON cmp.approval_cid = con.id
WHERE
  cmp.activity_id = ' . $dsaId . '
		';
//dpm($sql, '$sql (fetch default values)');
		$dao_defaults = CRM_Core_DAO::executeQuery($sql);
		$result = $dao_defaults->fetch();
//dpm($dao_defaults, "dao defaults");
		$defaults['dsa_participant_id'] = $dao_defaults->contact_id;
		$defaults['dsa_participant_role'] = $dao_defaults->relationship_type_id;
		if ($dao_defaults->type == 3) {
			// left in the code, although rep payments currently have no automated way of creditation
			$defaults['dsa_participant'] = $dao_defaults->contact_id . '|' . $dao_defaults->relationship_type_id . '|' . $dao_defaults->type . '|' . $dao_defaults->credited_activity_id;
		} else {
			$defaults['dsa_participant'] = $dao_defaults->contact_id . '|' . $dao_defaults->relationship_type_id . '|' . $dao_defaults->type . '|0';
		}
		
//dpm($defaults['dsa_participant'], 'Default participant');
		$defaults['dsa_type'] = $dao_defaults->type;
		$defaults['dsa_amount'] = $dao_defaults->amount_rep;
		// Details for creditation of existing (paid) DSA activities (for jQuery to retrieve and process)
		//$defaults['credit_data'] = _creditationValues($role_ar);
		if (is_null($dao_defaults->approval_cid)) {
			$defaults['dsa_approval'] = '';
		} else {
			$defaults['dsa_approval'] = 'Approved ' . $dao_defaults->approval_datetime . ' by ' . $dao_defaults->approver_name;
		}
		$defaults['invoice_number'] = $dao_defaults->invoice_number;
		$defaults['invoice_rep'] = $dao_defaults->invoice_rep;
		
	}
	
	// Apply filter on status list
	// retrieve a complete list of available activity statusses
	$arStatusLst = _retrieveActivityStatusList();
	// Find field "status_id" and its value
	$fldStatus = _findFieldByName($form, 'status_id');

	// Modify the offered activity status list depending on activity_type and current status
	// and decide which fields should be eitable: 0=edit all (restrict none), 1=edit status only (restrict the rest), 2=edit none (restict all)
	$restrictEdit = '0';
	if (!empty($fldStatus)) {
		if (isset($fldStatus['id'])) {
			$fldVal = $fldStatus['obj']->_values[0];
			$fldOptions = $fldStatus['obj']->_options; // use existing options list for modifications: might contain a filter from other modules
			
			// convert options to a Representative payment-specific options list
			switch ($arStatusLst[$fldVal]['name']) {
				case 'Scheduled':
				case 'Cancelled':
				case 'Not Required':
					if (CRM_Core_Permission::check('approve Representative payment activity')) {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required', 'dsa_payable');
					} else {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required');
					}
					$addSelect = TRUE;
					$restrictEdit = '0';
					break;
				case 'dsa_payable':
					if (CRM_Core_Permission::check('approve Representative payment activity')) {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required', 'dsa_payable'); // allow status payable for approver
					} elseif (CRM_Core_Permission::check('edit Representative payment activity')) {
						$allowedStatusName = array('Scheduled', 'Cancelled', 'Not Required', 'dsa_payable'); // allow status payable for editor as that is the current status
					} else {
						$allowedStatusName = array('dsa_payable'); // current status only
					}
					$addSelect = TRUE;
					$restrictEdit = '1';
					break;
				case 'dsa_paid':
					$allowedStatusName = array('dsa_paid'); // current status only
					$addSelect = FALSE;
					$restrictEdit = '2';
					break;
				default:
					$allowedStatusName = array($arStatusLst[$fldVal]);
					$addSelect = TRUE;
					$restrictEdit = '0';
			}
			
			// if creation / editing was not allowed, then present read mode
			if (!$allowEdit) {
				$restrictEdit = '2';
			}
			
			// apply modifictions
			$newOptions = _leaveAllowedStatusOptions($fldOptions, $allowedStatusName, $addSelect);
			$form->_elements[$fldStatus['id']]->_options = $newOptions;
		}
	}
	$defaults['restrictEdit'] = $restrictEdit;
	
	// Apply default values
	if (isset($defaults)) {
		$form->setDefaults($defaults);
	}
}

/**
 * Implementation of hook_civicrm_validateForm
 *
 * Adds validation of added fields to the form
 */
function dsa_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
//echo '<pre>';
////dpm($fields, "HOOK_VALIDATEFORM: FIELDS ON ". $formName);
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
			switch($form->_activityTypeName) {
				case 'DSA':
					_dsa_validateform_dsa( $formName, $fields, $files, $form, $errors );
					break;
					
				case 'Representative payment':
					_dsa_validateform_representative_payment( $formName, $fields, $files, $form, $errors );
					break;
			}
			break;
	}
	return;
}


function _dsa_validateform_dsa( $formName, &$fields, &$files, &$form, &$errors ) {
	// participant
	if ($fields['dsa_participant'] == '') {
		$errors['dsa_participant'] = 'Please select a participant';
	} else {
		//===========================================================================================================

		// only open DSA activity within case for this dsa_participant_id?
		$caseId = $form->_caseId;
		$activityId = $form->_activityId;
		$participant_id = $fields['dsa_participant_id'];
		// all current revisions of the current case' activities of type DSA in an unfinished status
		try {
			$dao_activities = _dao_findOpenDsaActivities($caseId, $activityId, $participant_id);
		} catch(Exception $e) {
			// ignore
		}
//echo '<pre>';
//print_r ($dao_activities);
//echo '</pre>';
//exit();
		if ($dao_activities->N > 0) {
			$errors['status_id'] = 'Only one \'open\' DSA activity per person allowed.';
		}

		//===========================================================================================================
	}
	if ($fields['dsa_type'] == 1) {
		// days
		$fieldValue=trim($fields['dsa_days']);	
		if ((!CRM_Utils_Type::validate($fieldValue, 'Positive', False)) && (!$fieldValue==0)) {
			$errors['dsa_days'] = ts('Please enter a valid number of days');
		}
		// amounts
		$fldNames= array(
			'dsa_amount',
			'dsa_briefing',
			'dsa_airport',
			'dsa_transfer',
			'dsa_hotel',
			'dsa_visa',
			'dsa_medical',
			'dsa_other',
			'dsa_advance'); // 'dsa_debriefing',
		$result = TRUE;
		foreach($fldNames as $name) {
			$result = $result and _amountCheck($name, $fields, $errors);
		}
	
		// minimum DSA amount
		if ( (!array_key_exists('dsa_amount', $errors))  &&	(!array_key_exists('dsa_days', $errors)) ) {
			$params = array(
				'version' => 3,
				'q' => 'civicrm/ajax/rest',
				'sequential' => 1,
				'option_group_name' => 'dsa_configuration',
				'name' => 'minimum_dsa_amount',
				'return' => 'name,value',
			);
			try {
				$result = civicrm_api('OptionValue', 'getsingle', $params);
				$minimumAmount = $result['value'];
			} catch (Exception $e) {
				$minimumAmount = '0.00';
			}
			if ( ($fields['dsa_amount'] / $fields['dsa_days']) < ($minimumAmount * $fields['dsa_percentage'] / 100) ) {
				$errors['dsa_amount'] = ts('Minimum 100% daily DSA amount is') . ' ' . $minimumAmount;
			}
		}
		
		
		// if 'other' amount is filled out, a description is required as well
		if (!array_key_exists('dsa_other', $errors)) {
			if (($fields['dsa_other']!=0) && (trim($fields['dsa_other_description'])=='')) {
				$errors['dsa_other_description'] = ts('Please describe Expense Other');
				$result = FALSE;
			}
		}
	
	} else {
		// $fields['dsa_type'] = 3; creditation
		if ($fields['credit_act_id'] == '') {
			$errors['dsa_participant'] = ts('Unexpected error: could not obtain activity id for creditation');
		}
	}
}

function _dsa_validateform_representative_payment( $formName, &$fields, &$files, &$form, &$errors ) {
	// participant
	if ($fields['dsa_participant'] == '') {
		$errors['dsa_participant'] = 'Please select a participant';
	} else {
		//===========================================================================================================

		// only open Representative payment activity within case for this dsa_participant_id?
		$caseId = $form->_caseId;
		$activityId = $form->_activityId;
		$participant_id = $fields['dsa_participant_id'];
		// all current revisions of the current case' activities of type DSA in an unfinished status
		try {
			$dao_activities = _dao_findOpenRepPaymentActivities($caseId, $activityId, $participant_id);
		} catch(Exception $e) {
			// ignore
		}
//echo '<pre>';
//print_r ($dao_activities);
//echo '</pre>';
//exit();
		if ($dao_activities->N > 0) {
			$errors['status_id'] = 'Only one \'open\' payment per representative allowed.';
		}

		//===========================================================================================================
	}
	if ($fields['dsa_type'] == 1) {
		// amounts
		$fldNames= array(
			'dsa_amount',
		);
		$result = TRUE;
		foreach($fldNames as $name) {
			$result = $result and _amountCheck($name, $fields, $errors);
		}
	
	} else {
		// $fields['dsa_type'] = 3; creditation (not used for Representative payment)
	}
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
 * Implementation of hook_civicrm_pre
 *
 * Restores the status of an activity when changed using the status link in a cases activity list (formName = 'CRM_Case_Form_ActivityChangeStatus')
 */
function dsa_civicrm_pre( $op, $objectName, $id, &$params ) {
	switch ($objectName) {
		case 'Activity':
			// notes:
			// 'new activity' from case obviously results in $op = 'create'
			// 'edit' activity from a case results in $op = 'create'
			// hopefully 'status change' from case / activity view is the only time that $op=='edit' occurs
			if (isset($params['original_id'])) {
				if ($op == 'edit') {
					/* at this point a new version of the activity is being built
					 * in the new version we need to deny an uncontrolled / odd status change
					 * i.e. restore the original status
					 */
					// determine original status
					$params = array(
						'q' => 'civicrm/ajax/rest',
						'sequential' => 1,
						'id' => $params['original_id'],
					);
					$result = civicrm_api3('Activity', 'getsingle', $params);
					// set old status to new version (i.e. deny status change)
					$params['status_id'] = $result['status_id'];
/*					$session = CRM_Core_Session::singleton();
					$session::setStatus(ts('Status change denied - please use Edit instead'), ts('Access denied'), 'info', array('expires'=>0));
					// reload screen to get the message displayed
					CRM_Utils_System::redirect(CRM_Utils_System::refererPath());
*/
				}
			} else {
				// original_id is not defined
				// $op is either 'new' (no need to supress status change, or $op = 'edit' (likely: new case is created from webform)
			}
			break;
			
		default:
	}
}

function dsa_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
/*
	if ($objectName=='Activity') {
		if ($op=='edit') {
			// add message
			$session = CRM_Core_Session::singleton();
			$session::setStatus(ts('Status change denied - please use Edit instead'), ts('Access denied'), 'info', array('expires'=>0));
			// reload screen to get the message displayed
			CRM_Utils_System::redirect(CRM_Utils_System::refererPath());
		}
	}
*/
}

/**
 * Implementation of hook_civicrm_postProcess
 *
 * Stores added fields in civicrm_dsa_compose
 */
function dsa_civicrm_postProcess( $formName, &$form ) {	
	switch($formName) {
		case 'CRM_Case_Form_Activity':
			switch($form->getVar('_activityTypeName')) {
				case 'DSA':
					_dsa_postprocess_dsa( $formName, $form );
					break;
					
				case 'Representative payment':
					_dsa_postprocess_representative_payment( $formName, $form );
					break;
						
			} // switch activityTypeName
			break;
			
		default:
			
	} // switch ($formName)
	return;
}

function _dsa_postprocess_dsa( $formName, &$form ) {
//echo '<pre>';
//print_r($form);
//print_r($formName);
//echo '</pre>';
//exit();

	$dao = NULL;		
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

// -----
	if ($form->_submitValues['dsa_type'] == 1) {
		
		// use submitted credit_act_id to collect data from original payment (if available)
		$sql = '
SELECT
  dsa.case_id,
  dsa.contact_id,
  dsa.relationship_type_id,
  dsa.loc_id,
  dsa.percentage,
  dsa.days,
  dsa.amount_dsa,
  dsa.amount_briefing,
  dsa.amount_airport,
  dsa.amount_transfer,
  dsa.amount_hotel,
  dsa.amount_visa,
  dsa.amount_medical,
  dsa.amount_other,
  dsa.description_other,
  dsa.amount_advance,
  dsa.ref_date,
  dsa.invoice_number,
  dsa.approval_cid,
  dsa.approval_datetime,
  dsa.approval_cid as my_approval_cid,
  dsa.approval_datetime as my_approval_datetime
FROM
  civicrm_activity act,
  civicrm_dsa_compose dsa
WHERE
  act.id = ' . $dsaId . ' AND
  ifnull(act.original_id, act.id) = dsa.activity_id
		';
		$dao = CRM_Core_DAO::executeQuery($sql);
		$result = $dao->fetch();
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
			/*
			'dsa_debriefing'		=>  array(
										'column'	=> 'amount_debriefing',
										'type'		=> 'number',
										),
			*/
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
			'dsa_medical'			=>  array(
										'column'	=> 'amount_medical',
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
//			if (isset($form->_pid)) {
//				$input['pid'] = $form->_pid; // for creditation: parent activity id
//			}
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
										$value = _strParseSql($value);
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
	
	} else {
		// $form->_submitValues['dsa_type'] = 3 for creditation
		// use submitted credit_act_id to collect fields from original payment
		$sql = '
SELECT
  org.case_id,
  org.contact_id,
  org.relationship_type_id,
  org.loc_id,
  org.percentage,
  org.days,
  org.amount_dsa,
  org.amount_briefing,
  org.amount_airport,
  org.amount_transfer,
  org.amount_hotel,
  org.amount_visa,
  org.amount_medical,
  org.amount_other,
  org.description_other,
  org.amount_advance,
  org.ref_date,
  org.invoice_number,
  org.approval_cid,
  org.approval_datetime,
  dsa.approval_cid as my_approval_cid,
  dsa.approval_datetime as my_approval_datetime
FROM
  civicrm_activity act
  LEFT JOIN civicrm_dsa_compose dsa ON dsa.credited_activity_id = ' . $form->_submitValues['credit_act_id'] . '
  ,civicrm_dsa_compose org
WHERE
  act.id = ' . $form->_submitValues['credit_act_id'] . ' AND
  ifnull(act.original_id, act.id) = org.activity_id
		';
		$dao = CRM_Core_DAO::executeQuery($sql);
		$result = $dao->fetch();
		$input = array();
		if  (($action & CRM_Core_Action::$_names['add']) || ($action & CRM_Core_Action::$_names['update'])) {
			$input['type'] = $form->_submitValues['dsa_type'];
			$input['case_id'] = $dao->case_id;
			$input['activity_id'] = $dsaId;
			$input['contact_id'] = $dao->contact_id;
			$input['relationship_type_id'] = $dao->relationship_type_id;
			$input['loc_id'] = $dao->loc_id;
			$input['percentage'] = $dao->percentage;
			$input['days'] = $dao->days;
			$input['amount_dsa'] = $dao->amount_dsa;
			$input['amount_briefing'] = $dao->amount_briefing;
			$input['amount_airport'] = $dao->amount_airport;
			$input['amount_transfer'] = $dao->amount_transfer;
			$input['amount_hotel'] = $dao->amount_hotel;
			$input['amount_visa'] = $dao->amount_visa;
			$input['amount_medical'] = $dao->amount_medical;
			$input['amount_other'] = $dao->amount_other;
			$input['description_other'] = _strParseSql($dao->description_other);
			$input['amount_advance'] = $dao->amount_advance;
			if (is_null($dao->ref_date)) {
				$input['ref_date'] = _strParseSql('NULL');
			} else {
				$input['ref_date'] = _strParseSql($dao->ref_date);
			}
			// payment_id is added in the payment process
			$input['invoice_number'] = _strParseSql($dao->invoice_number); // creditations reuse the original invoice numbers
			$input['credited_activity_id'] = $form->_submitValues['credit_act_id'];
		}
	}
// --------

	// add / remove approver
	$approver_id = $form->_currentUserId;
	$statusList = _retrieveActivityStatusList();
	switch ($statusList[$form->_submitValues['status_id']]['name']) {
	case 'dsa_payable':
		// set approver - but only when not set yet
		if (!isset($dao->my_approval_cid)) {
			$input['approval_cid'] = $approver_id;
			$input['approval_datetime'] = 'now()';
		} else {
			// leave as is
		}
		break;
	case 'dsa_paid':
		// leave as is
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
	$sqlDsaRecord = 'SELECT count(case_id) as recCount FROM civicrm_dsa_compose WHERE activity_id = ' . $dsaId;
	$daoDsaRecord = CRM_Core_DAO::executeQuery($sqlDsaRecord);
	$daoDsaRecord->fetch();
	$dsaIsDefined = ($daoDsaRecord->recCount>0); // FALSE if recCount==0, otherwise TRUE

	//if  ($action & CRM_Core_Action::$_names['add']) {
	if (!$dsaIsDefined) {
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
}

function _dsa_postprocess_representative_payment( $formName, &$form ) {
//echo '<pre>';
//print_r($form);
//print_r($formName);
//echo '</pre>';
//exit();

	$dao = NULL;
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

// -----
	if ($form->_submitValues['dsa_type'] == 1) {
		
		// use submitted credit_act_id to collect data from original payment (if available)
		$sql = '
SELECT
  dsa.case_id,
  dsa.contact_id,
  dsa.relationship_type_id,
  dsa.amount_rep,
  dsa.invoice_number,
  dsa.approval_cid,
  dsa.approval_datetime
FROM
  civicrm_activity act,
  civicrm_representative_compose dsa
WHERE
  act.id = ' . $dsaId . ' AND
  ifnull(act.original_id, act.id) = dsa.activity_id
		';
		$dao = CRM_Core_DAO::executeQuery($sql);
		$result = $dao->fetch();
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
			'dsa_amount'			=>  array(
										'column'	=> 'amount_rep',
										'type'		=> 'number',
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
//			if (isset($form->_pid)) {
//				$input['pid'] = $form->_pid; // for creditation: parent activity id
//			}
			if (isset($form->_cid)) {
				$input['cid'] = $form->_cid; // representatives contact id
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
										$value = _strParseSql($value);
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
	
	} else {
		// $form->_submitValues['dsa_type'] = 3 for creditation
		// not in use for Representative payment
	}
// --------

	// add / remove approver
	$approver_id = $form->_currentUserId;
	$statusList = _retrieveActivityStatusList();
	switch ($statusList[$form->_submitValues['status_id']]['name']) {
	case 'dsa_payable':
		// set approver - but only when not set yet
		if (!isset($dao->approval_cid)) {
			$input['approval_cid'] = $approver_id;
			$input['approval_datetime'] = 'now()';
		} else {
			// leave as is
		}
		break;
	case 'dsa_paid':
		// leave as is
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
	$sqlDsaRecord = 'SELECT count(case_id) as recCount FROM civicrm_representative_compose WHERE activity_id = ' . $dsaId;
	$daoDsaRecord = CRM_Core_DAO::executeQuery($sqlDsaRecord);
	$daoDsaRecord->fetch();
	$dsaIsDefined = ($daoDsaRecord->recCount>0); // FALSE if recCount==0, otherwise TRUE

	//if  ($action & CRM_Core_Action::$_names['add']) {
	if (!$dsaIsDefined) {
		$sql = 'INSERT INTO civicrm_representative_compose (' . implode(',', array_keys($input)) . ') VALUES (' . implode(',', array_values($input)) . ')';
	} elseif ($action & CRM_Core_Action::$_names['update']) {
		foreach($input as $fldNm=>$fldVal) {
			$input[$fldNm] = $fldNm . '=' . $fldVal;
		}
		$sql = 'UPDATE civicrm_representative_compose SET ' . implode(',', array_values($input)) . ' WHERE ' . $input['activity_id'];
	}
	$result = CRM_Core_DAO::executeQuery($sql);
/*
echo '<pre>';
print_r($sql);
echo '</pre>';
exit();
//*/

}

/*
 * Helper function to prepare text values for use in sql by
 * - escaping existing quotes and
 * - enclosing the text in new quotes
 * NULL values remain in tact
 */
function _strParseSql($value='') {
	if (is_null($value)) {
		return NULL;
	} else {
		return '\'' . str_ireplace(array('\'', '\"'), array('\\\'', '\\\"'), $value) . '\'';
	}
}

function _dao_findOpenDsaActivities($caseId, $activityId=NULL, $participant_id=NULL) {
	// function will look within the case for other DSA activities (excluding a 'current' one), holding the same participant id
	if (is_null($activityId)) {
		$activityId = '\'NULL\'';
	} elseif ($activityId=='') {
		$activityId = '\'NULL\'';
	}
	$sql = '
SELECT act.subject, dsa.*
  FROM civicrm_case_activity cac,
       civicrm_activity act,
       civicrm_representative_compose dsa
 WHERE     cac.case_id = ' . $caseId . '
       AND act.id = cac.activity_id
       AND act.activity_type_id IN (
	                                SELECT     vl.value
                                      FROM     civicrm_option_group gp,
                                               civicrm_option_value vl
                                     WHERE     gp.name = \'activity_type\'
                                           AND gp.id = vl.option_group_id
                                           AND vl.name = \'Representative payment\'
                                    )
       AND act.is_current_revision = 1
       AND act.status_id NOT IN (
                                    SELECT     vl.value
                                      FROM     civicrm_option_group gp,
                                               civicrm_option_value vl
                                     WHERE     gp.name = \'activity_status\'
                                           AND gp.id = vl.option_group_id
                                           AND vl.name IN (\'dsa_paid\', \'Cancelled\',  \'Not Required\')
                                )
       AND dsa.activity_id = ifnull(act.original_id, act.id)
       AND IF(isnull(' . $participant_id . '), TRUE, dsa.contact_id = ' . $participant_id . ') /* return all when participant is not specified */
       AND dsa.activity_id NOT IN (
	                                SELECT     ifnull(ac2.original_id, ac2.id) AS dsa_id
                                     FROM      civicrm_activity ac2
                                    WHERE      ac2.id = ifnull(' . $activityId . ', 0)
                                  ) /* if provided, exclude activity (or its predecessor or successor) from results */
    ';
	$dao_activities = CRM_Core_DAO::executeQuery($sql);
	return $dao_activities;
}

function _dao_findOpenRepPaymentActivities($caseId, $activityId=NULL, $participant_id=NULL) {
		// function will look within the case for other DSA activities (excluding a 'current' one), holding the same participant id
	if (is_null($activityId)) {
		$activityId = '\'NULL\'';
	} elseif ($activityId=='') {
		$activityId = '\'NULL\'';
	}
	$sql = '
SELECT act.subject, dsa.*
  FROM civicrm_case_activity cac,
       civicrm_activity act,
       civicrm_representative_compose dsa
 WHERE     cac.case_id = ' . $caseId . '
       AND act.id = cac.activity_id
       AND act.activity_type_id IN (
	                                SELECT     vl.value
                                      FROM     civicrm_option_group gp,
                                               civicrm_option_value vl
                                     WHERE     gp.name = \'activity_type\'
                                           AND gp.id = vl.option_group_id
                                           AND vl.name = \'Representative payment\'
                                    )
       AND act.is_current_revision = 1
       AND act.status_id NOT IN (
                                    SELECT     vl.value
                                      FROM     civicrm_option_group gp,
                                               civicrm_option_value vl
                                     WHERE     gp.name = \'activity_status\'
                                           AND gp.id = vl.option_group_id
                                           AND vl.name IN (\'dsa_paid\', \'Cancelled\',  \'Not Required\')
                                )
       AND dsa.activity_id = ifnull(act.original_id, act.id)
       AND IF(isnull(' . $participant_id . '), TRUE, dsa.contact_id = ' . $participant_id . ') /* return all when participant is not specified */
       AND dsa.activity_id NOT IN (
	                                SELECT     ifnull(ac2.original_id, ac2.id) AS dsa_id
                                     FROM      civicrm_activity ac2
                                    WHERE      ac2.id = ifnull(' . $activityId . ', 0)
                                  ) /* if provided, exclude activity (or its predecessor or successor) from results */
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
		'options' => array(
			'limit' => 5000,
		),
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

function _getCaseParticipantList_dsa($case_id, $activity_id) {
//dpm($case_id, 'case_id');
//dpm($activity_id, 'activity_id');

	// dsa activities could be referenced by $activity_id
	// in that case the offered participants list is limited to the participant saved earlier (to avoid mixing up payment- and creditation amounts)
	// however, if the activity is made through case management (defined in XML), we have an activity_id, but no participant yet. In that case: disregard the activity id (set to NULL).
	// find out by first looking for a participant
	if (!is_null($activity_id)) {
		$sql_participant = '
SELECT
  act.id,
  ifnull(act.original_id, act.id) dsa_id,
  ifnull(dsa.contact_id, 0) contact_id
FROM
  civicrm_activity act
  LEFT JOIN civicrm_dsa_compose dsa
    ON dsa.activity_id = ifnull(act.original_id, act.id)
WHERE
  act.id = ' . $activity_id;
  
//dpm($sql, 'sql');
		$dao_participant = CRM_Core_DAO::executeQuery($sql_participant);
		$dao_participant->fetch();
		if ($dao_participant->contact_id == 0) {
			$activity_id = NULL;
		}
	}
	
	// 2 scenarios now:
	// - activity id is (no longer) available -> prepare a participants options based on case members and existing dsa activities
	// - activity id and participant are known -> prepare a single option list with only that participant
	
	$ar_options = array();
	if (is_null($activity_id)) {
		// activity_id is not available -> use case_id to build a participant options list based on case roles and existing DSA activities
		$sql_role = '
SELECT	rs.contact_id_b as contact_id,
		rt.label_b_a as role,
		rs.relationship_type_id as type_id,
		ct.display_name as name
FROM	civicrm_relationship rs,
		civicrm_relationship_type rt,
		civicrm_contact ct
WHERE	rs.case_id = ' . $case_id . '
		  and	rt.id = rs.relationship_type_id
		  and	rs.contact_id_b = ct.id
ORDER BY
		contact_id,
		role,
		name
		';
//dpm($sql_role, 'sql role');
		$dao_role = CRM_Core_DAO::executeQuery($sql_role);
		while($dao_role->fetch()) {
			// by default, the array is set-up as if no dsa records will be found and can be created
			if (!array_key_exists($dao_role->contact_id, $ar_options)) {
				$ar_options[$dao_role->contact_id] = array();
			}
			$ar_options[$dao_role->contact_id][$dao_role->type_id] = array(
				'contact'		=> $dao_role->name,
				'role'			=> ts($dao_role->role),
				'dsa_type'		=> '1',
				'description'	=> 'Payment',
				'dsa'			=> array(),
				'dsa_id'		=> 0
			);
		}
		
		$sql_dsa = '
SELECT
  dsa.*,
  cas.id case_id,
  act.id activity_id,
  act.status_id,
  ogv2.name status_name,
  con.display_name,
  ifnull(rst.label_b_a, \'-\') AS role
FROM
  civicrm_case cas,
  civicrm_case_activity cact,
  civicrm_activity act,
  civicrm_dsa_compose dsa
  left join civicrm_relationship_type rst on  rst.id = dsa.relationship_type_id,
  civicrm_option_value ogv2,
  civicrm_option_group ogp2,
  civicrm_contact con
WHERE
  cas.id = ' . $case_id . ' AND
  cact.case_id = cas.id AND
  act.id = cact.activity_id AND
  act.is_current_revision = 1 AND
  ogp2.name = \'activity_status\' AND
  ogv2.option_group_id = ogp2.id AND
  act.status_id = ogv2.value AND
  con.id = dsa.contact_id AND
  dsa.activity_id = ifnull(
                      act.original_id,
                      act.id) AND
  act.activity_type_id IN (SELECT
                             ogv1.value
                           FROM
                             civicrm_option_value ogv1,
                             civicrm_option_group ogp1
                           WHERE
                             ogv1.option_group_id = ogp1.id AND
                             ogp1.name = \'activity_type\' AND
                             ogv1.name = \'DSA\')
ORDER BY
  dsa.contact_id,
  dsa.activity_id
		';
//dpm($sql_dsa, 'sql_dsa');
		$dao_dsa = CRM_Core_DAO::executeQuery($sql_dsa);
		while($dao_dsa->fetch()) {
			$contact = $dao_dsa->contact_id;
			if (!array_key_exists($contact, $ar_options)) {
				$ar_options[$contact] = array();
			}
			$role = $dao_dsa->relationship_type_id;
			if (!array_key_exists($role, $ar_options[$contact])) {
				$ar_options[$contact][$role] = array();
			}
			if (!array_key_exists('contact', $ar_options[$contact][$role])) {
				$ar_options[$contact][$role]['contact'] = $dao_dsa->display_name;
			}
			if (!array_key_exists('role', $ar_options[$contact][$role])) {
				$ar_options[$contact][$role]['role'] = $dao_dsa->role;
			}
			
			// note regarding dsa_type:
			// 1 indicates a payment,
			// 3 indicated a creditation
			// 0 is used below to suppress to suppress any dsa creation (for a participant / contact)
			// 2 was intended for settlement, but was abandoned while building this extension
			if ($dao_dsa->activity_id == $activity_id) {
				$ar_options[$contact][$role]['dsa_type'] = $dao_dsa->type;
				$ar_options[$contact][$role]['description'] = ($dao_dsa->type==1?'Payment':'Credit');
				$ar_options[$contact][$role]['dsa_id'] = ($dao_dsa->type==1?'0':$dao_dsa->credited_activity_id);
			} elseif ($dao_dsa->status_name != 'dsa_paid') {
				// dsa found, but status not paid -> will have to process existing record before starting a new one
				$ar_options[$contact][$role]['dsa_type'] = 0;
				$ar_options[$contact][$role]['description'] = '';
				$ar_options[$contact][$role]['dsa_id'] = 0;
			} elseif ($dao_dsa->type == 1) {
				// payment is paid -> can be credited
				$ar_options[$contact][$role]['dsa_type'] = 3;
				$ar_options[$contact][$role]['description'] = 'Credit';
				$ar_options[$contact][$role]['dsa_id'] = $dao_dsa->activity_id; // on activity: original activity id of earlier payment
			} elseif ($dao_dsa->type == 3) {
				// creditation marked paid -> can start new payment
				$ar_options[$contact][$role]['dsa_type'] = 1;
				$ar_options[$contact][$role]['description'] = 'Payment';
				$ar_options[$contact][$role]['dsa_id'] = 0;
			} else {
				// should not occur: not paid and an unknown type -> block new DSA until issue is solved
				$ar_options[$contact][$role]['dsa_type'] = 0;
				$ar_options[$contact][$role]['description'] = '';
				$ar_options[$contact][$role]['dsa_id'] = 0;
			}

			// for creditation: track payment details
			if ($ar_options[$contact][$role]['dsa_type'] == 3) {
				$ar_options[$contact][$role]['dsa'] = $dao_dsa;
			} else {
				$ar_options[$contact][$role]['dsa'] = array();
			}
		}
	} else {
		// $activity_id and participant are available: options list should be restricted to that single option
		// this should avoid showing a participants creditation amounts on other another participants payment activity
		$sql_dsa = '
SELECT
  dsa.*,
  act.id activity_id,
  act.status_id,
  ogv.name status_name,
  con.display_name,
  ifnull(rst.label_b_a, \'-\') AS role
FROM
  civicrm_activity act,
  civicrm_dsa_compose dsa
  left join civicrm_relationship_type rst on  rst.id = dsa.relationship_type_id,
  civicrm_option_value ogv,
  civicrm_option_group ogp,
  civicrm_contact con
WHERE
  act.id = ' . $activity_id . ' AND
  ogp.name = \'activity_status\' AND
  ogv.option_group_id = ogp.id AND
  act.status_id = ogv.value AND
  con.id = dsa.contact_id AND
  dsa.activity_id = ifnull(
                      act.original_id,
                      act.id)
		';
		$dao_dsa = CRM_Core_DAO::executeQuery($sql_dsa);
		while($dao_dsa->fetch()) {
			$contact = $dao_dsa->contact_id;
			$ar_options[$contact] = array();

			$role = $dao_dsa->relationship_type_id;
			$ar_options[$contact][$role] = array();

			$ar_options[$contact][$role]['contact'] = $dao_dsa->display_name;
			$ar_options[$contact][$role]['role'] = $dao_dsa->role;
						
			$ar_options[$contact][$role]['dsa_type'] = $dao_dsa->type;
			$ar_options[$contact][$role]['description'] = ($dao_dsa->type==1?'Payment':'Credit');
			$ar_options[$contact][$role]['dsa_id'] = ($dao_dsa->type==1?'0':$dao_dsa->credited_activity_id);

			// for creditation: track payment details
			if ($ar_options[$contact][$role]['dsa_type'] == 3) {
				$ar_options[$contact][$role]['dsa'] = $dao_dsa;
			} else {
				$ar_options[$contact][$role]['dsa'] = array();
			}
		}
	}
	
//dpm($ar_options, '#ar_options');
	
	$result = array();
	foreach($ar_options as $contact=>$contact_data) {
		foreach($contact_data as $role=>$role_data) {
//			dpm($role_data, 'role data');
//		is_null($activity_id)
			if ( ($role_data['dsa_type']=='1') || ($role_data['dsa_type']=='3') ) {
				$result[$contact . '|' . $role . '|' . $role_data['dsa_type'] . '|' . $role_data['dsa_id']] = ts($role_data['description']) . ': ' . $role_data['contact'] . ' (' . ts($role_data['role']) . ')';
			}
		}
	}
	asort($result);
	
	return $result; // for creditation: need to add dsa amounts and original activity_id as well

/*	
	// original code:
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
*/
}

function _getCaseParticipantList_representative_payment($case_id, $activity_id) {
	// dsa activities could be referenced by $activity_id
	// in that case the offered participants list is limited to the participant saved earlier (to avoid mixing up payment- and creditation amounts)
	// however, if the activity is made through case management (defined in XML), we have an activity_id, but no participant yet. In that case: disregard the activity id (set to NULL).
	// find out by first looking for a participant
	if (!is_null($activity_id)) {
		$sql_participant = '
SELECT
  act.id,
  ifnull(act.original_id, act.id) dsa_id,
  ifnull(dsa.contact_id, 0) contact_id
FROM
  civicrm_activity act
  LEFT JOIN civicrm_representative_compose dsa
    ON dsa.activity_id = ifnull(act.original_id, act.id)
WHERE
  act.id = ' . $activity_id;
  
		$dao_participant = CRM_Core_DAO::executeQuery($sql_participant);
		$dao_participant->fetch();
		if ($dao_participant->contact_id == 0) {
			$activity_id = NULL;
		}
	}
	
	// 2 scenarios now:
	// - activity id is (no longer) available -> prepare a participants options based on case members and existing dsa activities
	// - activity id and participant are known -> prepare a single option list with only that participant
	
	$ar_options = array();
	if (is_null($activity_id)) {
		// activity_id is not available -> use case_id to build a participant options list based on case roles and existing Representative payment activities
		// restrict results to predefined relationship types
		$restrict_role = array();
		$params = array(
			'version' => 3,
			'q' => 'civicrm/ajax/rest',
			'sequential' => 1,
			'option_group_name' => 'rep_payment_relationships',
		);
		$result = civicrm_api('OptionValue', 'get', $params);
		if (array_key_exists('values', $result)) {
			foreach($result['values'] as $value) {
				$restrict_role[] = '\'' . addslashes($value['value']) . '\'';
			}
		} else {
			// no results -> no participants will be displayed
			$restrict_role[] = '\'\'';
		}
		$sql_role = '
SELECT	rs.contact_id_b as contact_id,
		rt.label_b_a as role,
		rs.relationship_type_id as type_id,
		ct.display_name as name
FROM	civicrm_relationship rs,
		civicrm_relationship_type rt,
		civicrm_contact ct
WHERE	rs.case_id = ' . $case_id . '
		and rt.id = rs.relationship_type_id
		and rs.contact_id_b = ct.id
		and rt.name_b_a in (' . implode(', ', $restrict_role) . ')
ORDER BY
		contact_id,
		contact_id,
		role,
		name
		';
		$dao_role = CRM_Core_DAO::executeQuery($sql_role);
		while($dao_role->fetch()) {
			// by default, the array is set-up as if no dsa records will be found and can be created
			if (!array_key_exists($dao_role->contact_id, $ar_options)) {
				$ar_options[$dao_role->contact_id] = array();
			}
			$ar_options[$dao_role->contact_id][$dao_role->type_id] = array(
				'contact'		=> $dao_role->name,
				'role'			=> ts($dao_role->role),
				'dsa_type'		=> '1',
				'description'	=> 'Payment',
				'dsa'			=> array(),
				'dsa_id'		=> 0
			);
		}
		
		$sql_dsa = '
SELECT
  dsa.*,
  cas.id case_id,
  act.id activity_id,
  act.status_id,
  ogv2.name status_name,
  con.display_name,
  ifnull(rst.label_b_a, \'-\') AS role
FROM
  civicrm_case cas,
  civicrm_case_activity cact,
  civicrm_activity act,
  civicrm_representative_compose dsa
  left join civicrm_relationship_type rst on  rst.id = dsa.relationship_type_id,
  civicrm_option_value ogv2,
  civicrm_option_group ogp2,
  civicrm_contact con
WHERE
  cas.id = ' . $case_id . ' AND
  cact.case_id = cas.id AND
  act.id = cact.activity_id AND
  act.is_current_revision = 1 AND
  ogp2.name = \'activity_status\' AND
  ogv2.option_group_id = ogp2.id AND
  act.status_id = ogv2.value AND
  con.id = dsa.contact_id AND
  dsa.activity_id = ifnull(
                      act.original_id,
                      act.id) AND
  act.activity_type_id IN (SELECT
                             ogv1.value
                           FROM
                             civicrm_option_value ogv1,
                             civicrm_option_group ogp1
                           WHERE
                             ogv1.option_group_id = ogp1.id AND
                             ogp1.name = \'activity_type\' AND
                             ogv1.name = \'Representative payment\')
ORDER BY
  dsa.contact_id,
  dsa.activity_id
		';
		
		
		$dao_dsa = CRM_Core_DAO::executeQuery($sql_dsa);
		while($dao_dsa->fetch()) {
			$contact = $dao_dsa->contact_id;
			if (!array_key_exists($contact, $ar_options)) {
				$ar_options[$contact] = array();
			}
			$role = $dao_dsa->relationship_type_id;
			if (!array_key_exists($role, $ar_options[$contact])) {
				$ar_options[$contact][$role] = array();
			}
			if (!array_key_exists('contact', $ar_options[$contact][$role])) {
				$ar_options[$contact][$role]['contact'] = $dao_dsa->display_name;
			}
			if (!array_key_exists('role', $ar_options[$contact][$role])) {
				$ar_options[$contact][$role]['role'] = $dao_dsa->role;
			}
			
			// note regarding dsa_type:
			// 1 indicates a payment,
			// 3 indicated a creditation
			// 0 is used below to suppress any dsa creation (for a participant / contact)
			// 2 was intended for settlement, but was abandoned while building this extension
			if ($dao_dsa->activity_id == $activity_id) {
				$ar_options[$contact][$role]['dsa_type'] = $dao_dsa->type;
				$ar_options[$contact][$role]['description'] = ($dao_dsa->type==1?'Payment':'Credit');
				$ar_options[$contact][$role]['dsa_id'] = ($dao_dsa->type==1?'0':$dao_dsa->credited_activity_id);
			} elseif ($dao_dsa->status_name != 'dsa_paid') {
				// payment found, but status not paid -> will have to process existing record before starting a new one
				$ar_options[$contact][$role]['dsa_type'] = 0;
				$ar_options[$contact][$role]['description'] = '';
				$ar_options[$contact][$role]['dsa_id'] = 0;
			} elseif ($dao_dsa->type == 1) {
				// payment is paid, but for representative payments, there is no creditation
				$ar_options[$contact][$role]['dsa_type'] = 0;
				$ar_options[$contact][$role]['description'] = '';
				$ar_options[$contact][$role]['dsa_id'] = 0;
			} elseif ($dao_dsa->type == 3) {
				// creditation marked paid -> can start new payment
				//$ar_options[$contact][$role]['dsa_type'] = 1;
				//$ar_options[$contact][$role]['description'] = 'Payment';
				//$ar_options[$contact][$role]['dsa_id'] = 0;
			} else {
				// should not occur: not paid and an unknown type -> block new DSA until issue is solved
				$ar_options[$contact][$role]['dsa_type'] = 0;
				$ar_options[$contact][$role]['description'] = '';
				$ar_options[$contact][$role]['dsa_id'] = 0;
			}

			// for creditation: track payment details
			if ($ar_options[$contact][$role]['dsa_type'] == 3) {
				$ar_options[$contact][$role]['dsa'] = $dao_dsa;
			} else {
				$ar_options[$contact][$role]['dsa'] = array();
			}
		}

	} else {
		// $activity_id and participant are available: options list should be restricted to that single option
		// this should avoid showing a participants creditation amounts on other another participants payment activity
		$sql_dsa = '
SELECT
  dsa.*,
  act.id activity_id,
  act.status_id,
  ogv.name status_name,
  con.display_name,
  ifnull(rst.label_b_a, \'-\') AS role
FROM
  civicrm_activity act,
  civicrm_representative_compose dsa
  left join civicrm_relationship_type rst on  rst.id = dsa.relationship_type_id,
  civicrm_option_value ogv,
  civicrm_option_group ogp,
  civicrm_contact con
WHERE
  act.id = ' . $activity_id . ' AND
  ogp.name = \'activity_status\' AND
  ogv.option_group_id = ogp.id AND
  act.status_id = ogv.value AND
  con.id = dsa.contact_id AND
  dsa.activity_id = ifnull(
                      act.original_id,
                      act.id)
		';
		$dao_dsa = CRM_Core_DAO::executeQuery($sql_dsa);
		while($dao_dsa->fetch()) {
			$contact = $dao_dsa->contact_id;
			$ar_options[$contact] = array();

			$role = $dao_dsa->relationship_type_id;
			$ar_options[$contact][$role] = array();

			$ar_options[$contact][$role]['contact'] = $dao_dsa->display_name;
			$ar_options[$contact][$role]['role'] = $dao_dsa->role;
						
			$ar_options[$contact][$role]['dsa_type'] = $dao_dsa->type;
			$ar_options[$contact][$role]['description'] = ($dao_dsa->type==1?'Payment':'Credit');
			$ar_options[$contact][$role]['dsa_id'] = ($dao_dsa->type==1?'0':$dao_dsa->credited_activity_id);

			// for creditation: track payment details
			if ($ar_options[$contact][$role]['dsa_type'] == 3) {
				$ar_options[$contact][$role]['dsa'] = $dao_dsa;
			} else {
				$ar_options[$contact][$role]['dsa'] = array();
			}
		}
	}
	
//dpm($ar_options, '#ar_options');
	
	$result = array();
	foreach($ar_options as $contact=>$contact_data) {
		foreach($contact_data as $role=>$role_data) {
//			dpm($role_data, 'role data');
//		is_null($activity_id)
			if ( ($role_data['dsa_type']=='1') || ($role_data['dsa_type']=='3') ) {
				$result[$contact . '|' . $role . '|' . $role_data['dsa_type'] . '|' . $role_data['dsa_id']] = ts($role_data['description']) . ': ' . $role_data['contact'] . ' (' . ts($role_data['role']) . ')';
			}
		}
	}
	asort($result);
//dpm($result);
	
	return $result; // for creditation: need to add dsa amounts and original activity_id as well

/*	
	// original code:
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
*/
}

function _creditationValues($role_ar) {
	// Details for creditation of existing (paid) DSA activities (for jQuery to retrieve and process)
	$activity = array();
	foreach($role_ar as $role_key=>$role_disp) {
		$act_details = explode('|', $role_key);
		if ($act_details[2] == '3') {
			// creditation option
			$activity[] = $act_details[3]; // represents the original activity id of the earlier payment
		}
	}
	
//dpm($activity, 'activity');
	if (!empty($activity)) {
//dpm($activity, 'activities for creditation');
		$sql=' 
SELECT
  act.id act_id,
  act.original_id,
  ifnull(act.original_id, act.id) dsa_id,
  act.subject,
  dsa.*
FROM
  civicrm_activity act,
  civicrm_dsa_compose dsa
WHERE
  act.id IN (' . implode(', ', $activity) . ') AND
  dsa.activity_id = ifnull(act.original_id, act.id)
		';
		$dao = CRM_Core_DAO::executeQuery($sql);
		$credit_data = array();
		while($dao->fetch()) {
//dpm($dao, '$dao');
			$credit_line = array();
			$credit_line[] = $dao->act_id;
			$credit_line[] = $dao->dsa_id;
			$credit_line[] = $dao->invoice_number;
			$credit_line[] = $dao->loc_id;
			$credit_line[] = $dao->percentage;
			$credit_line[] = $dao->days;
			$credit_line[] = $dao->amount_dsa;
			$credit_line[] = $dao->amount_briefing;
			$credit_line[] = $dao->amount_airport;
			$credit_line[] = $dao->amount_transfer;
			$credit_line[] = $dao->amount_hotel;
			$credit_line[] = $dao->amount_visa;
			$credit_line[] = $dao->amount_medical;
			$credit_line[] = $dao->amount_other;
			$credit_line[] = str_replace(
								array('|', '#'),
								array('_', '_'),
								$dao->description_other);
			$credit_line[] = $dao->amount_advance;
			$credit_line[] = $dao->ref_date;
			$credit_data[] = implode('|', $credit_line);
		}
		return implode('#', $credit_data);
	} else {
		return '';
	}
}

/**
 * Implementation of hook_civicrm_permission
 *
 * Adds a privilege DSA creation
 */
function dsa_civicrm_permission( &$permissions ) {
	$prefix = ts('CiviCRM DSA') . ': '; // name of extension or module
	$permissions = array(
		'create DSA activity' => $prefix . ts('create DSA activity'),
		'edit DSA activity' => $prefix . ts('edit DSA activity'),
		'approve DSA activity' => $prefix . ts('approve DSA activity'),
		//'delete DSA activity' => $prefix . ts('delete DSA activity'),
		'create Representative payment activity' => $prefix . ts('create Representative payment activity'),
		'edit Representative payment activity' => $prefix . ts('edit Representative payment activity'),
		'approve Representative payment activity' => $prefix . ts('approve Representative payment activity'),
		//'delete Representative payment activity' => $prefix . ts('delete Representative payment activity'),
	); // NB: note the convention of using delete in ComponentName, plural for edits
}

/*
 * helper function to supply table- column names for custom fields
 */
function _getCustomTableInfo($customGroupName) {
	// default return value
	$customTable = array(
		'group_id' => NULL,
		'group_name' => NULL,
		'group_table' => NULL,
		'columns' => array(),
		'sql_columns' => '',
	);
	
	// retrieve table name for custom group
	$sql = 'SELECT id, name, table_name FROM civicrm_custom_group WHERE name = \'' . $customGroupName . '\'';
	$dao = CRM_Core_DAO::executeQuery($sql);
	if (!$dao->N == 1) {
		// leave empty
	} else {
		$dao->fetch();
		$customTable['group_id'] = $dao->id;
		$customTable['group_name'] = $dao->name;
		$customTable['group_table'] = $dao->table_name;
		// retrieve fieldnames for custom fields
		$sql = "SELECT name, label, column_name FROM civicrm_custom_field WHERE custom_group_id = " . $customTable['group_id'];
		$dao = CRM_Core_DAO::executeQuery($sql);
		if ($dao->N >= 1) {
			$sql_cols = array();
			while ($dao->fetch()) {
				$customTable['columns'][$dao->name] = array(
					'name' => $dao->name,
					'label' => $dao->label,
					'column_name' => $dao->column_name,
				);
				$sql_cols[] = $customTable['group_table'] . '.' . $dao->column_name . ' AS \'' . $dao->name . '\'';
			}
		} else {
			// leave columns empty
		}
		if (!empty($sql_cols)) {
			$customTable['sql_columns'] = implode(',' . PHP_EOL, $sql_cols);
		}
	}
	return $customTable;
}


