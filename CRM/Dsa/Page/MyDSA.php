<?php
use CRM_Dsa_ExtensionUtil as E;

class CRM_Dsa_Page_MyDSA extends CRM_Core_Page {
  private $_userContactId = NULL;
  private $_approverId = NULL;

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    $this->setPageConfiguration();
    $this->initializePager();

    //Get contact id of current user
    global $user;
    try {
      $this->current_user = civicrm_api3('UFMatch', 'getsingle', array('uf_id' => $user->uid));
    } catch (Exception $e){
      CRM_Core_Error::debug_log_message('Unable to retrieve CiviCRM Contact for Drupal ID: '.$user->uid);
    }

    if (($this->_approverId != $this->_userContactId) && (CRM_Core_Permission::check('view others claims') == FALSE)) {
      CRM_Core_Session::setStatus('Sorry, you are not allowed to view dsa for this user', 'DSA', 'error');
    } else {
      if(CRM_Dsa_Utils::isDSATeamleader($this->current_user['contact_id'])){
        $myDSA = $this->getMyDSA($this->_approverId);
        CRM_Utils_System::setTitle(ts("Approve or reject dsa"));
        $this->assign('myDSAs', $myDSA);
      }
    }

    parent::run();
  }

  /**
   * Function to get my dsa
   *
   * @param int $contactId
   * @return array $myDSA
   * @access protected
   */
  protected function getMyDSA($contactId) {
    $myDSA = array();
    list($offset, $limit) = $this->_pager->getOffsetAndRowCount();

    //Get contact id of current user
    global $user;
    try {
      $this->current_user = civicrm_api3('UFMatch', 'getsingle', array('uf_id' => $user->uid));
    } catch (Exception $e){
      CRM_Core_Error::debug_log_message('Unable to retrieve CiviCRM Contact for Drupal ID: '.$user->uid);
    }

    $query = "
SELECT act.is_current_revision as 'dsa_act_cur_revision',cact.is_current_revision as 'dsa_cact_cur_revision', ov.name as 'dsa_act_status', act.status_id as 'dsa_act_status_id', ovdsa.name as 'cred_act_status', cact.status_id as 'cred_act_status_id', rel_prof.contact_id_b as 'project_officer_id',profct.display_name as 'project_officer_name', dsact.id as 'dsa_contact_id', dsact.display_name as 'dsa_contact_name', act.id as 'act_id',act.subject as 'act_subj', act.activity_date_time as 'act_datetime', cact.id as 'cred_id', cact.subject as 'cred_subj',cact.activity_date_time as 'cred_datetime', cc.contact_id as 'case_contact_id', cny.name as 'client_country',clcc.contact_id as 'client_cid',dsa.*
FROM civicrm_dsa_compose dsa
LEFT JOIN civicrm_case_contact cc ON cc.case_id = dsa.case_id
LEFT JOIN civicrm_value_travel_parent tp ON tp.entity_id = dsa.case_id
LEFT JOIN civicrm_case_contact clcc ON clcc.case_id = tp.case_id
LEFT JOIN civicrm_activity act ON act.id = dsa.activity_id
LEFT JOIN civicrm_activity cact ON cact.original_id = act.id
LEFT JOIN civicrm_contact dsact ON dsact.id = dsa.contact_id
LEFT JOIN civicrm_address adr ON adr.contact_id = cc.contact_id AND adr.is_primary = 1
LEFT JOIN civicrm_country cny ON cny.id = adr.country_id
LEFT JOIN civicrm_option_value ov ON act.status_id = ov.value AND ov.option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'activity_status')
LEFT JOIN civicrm_option_value ovdsa ON cact.status_id = ovdsa.value AND ovdsa.option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'activity_status')
LEFT JOIN civicrm_relationship rel_prof ON rel_prof.case_id = cc.case_id AND rel_prof.is_active = 1 AND rel_prof.case_id = dsa.case_id AND rel_prof.relationship_type_id = (SELECT id FROM civicrm_relationship_type rt WHERE name_b_a = 'Case Coordinator')
LEFT JOIN civicrm_contact profct ON profct.id = rel_prof.contact_id_b
WHERE
  payment_id IS NULL
  AND (dsa.secondary_approval_approved IS NULL)
  AND (dsa.amount_dsa+dsa.amount_briefing+dsa.amount_airport+dsa.amount_transfer+dsa.amount_hotel+dsa.amount_visa+dsa.amount_medical+dsa.amount_other) >= 2000
  AND ( (act.status_id = (SELECT value FROM civicrm_option_value WHERE option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'activity_status') AND name = 'dsa_payable')
          AND act.is_current_revision = 1)
        OR
        (cact.status_id = (SELECT value FROM civicrm_option_value WHERE option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'activity_status') AND name = 'dsa_payable')
          AND cact.is_current_revision = 1)
  )
  /*AND rel_prof.contact_id_b = %1*/
LIMIT %2, %3";

    $queryParams = array(
      1 => array($this->current_user['contact_id'], 'Integer'),
      2 => array($offset, 'Integer'),
      3 => array($limit, 'Integer')
    );
    $dao = CRM_Core_DAO::executeQuery($query, $queryParams);

    while ($dao->fetch()) {
      $row = array();
      $row['dsa_id'] = $dao->id;
      $row['case_id'] = $dao->case_id;
      $row['activity_id'] = !empty($dao->cred_id)?$dao->cred_id:$dao->activity_id;
      $row['case_contact_id'] = $dao->case_contact_id;
      $row['client_contact_id'] = $dao->client_cid;
      $row['project_officer_id'] = $dao->project_officer_id;
      $row['project_officer_name'] = $dao->project_officer_name;
      $row['project_officer_url'] = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$dao->project_officer_id}");
      $row['client_country'] = $dao->client_country;
      $row['total_dsa_amount'] = ($dao->amount_dsa+$dao->amount_briefing+$dao->amount_airport+$dao->amount_transfer+$dao->amount_hotel+$dao->amount_visa+$dao->amount_medical+$dao->amount_other);
      $row['dsa_contact_name_url'] = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$dao->dsa_contact_id}");
      $row['dsa_contact_name'] = CRM_Threepeas_Utils::getContactName($dao->dsa_contact_id);
      $row['dsa_contact_id'] = $dao->dsa_contact_id;
      $row['submitted_by_cid_url'] = CRM_Utils_System::url('civicrm/contact/view/case', "reset=1&action=view&id={$dao->case_id}&cid={$dao->case_contact_id}");
      $row['submitted_by'] = CRM_Threepeas_Utils::getContactName($dao->approval_cid);
      $row['submitted_date'] = $dao->approval_datetime;
      $row['url_dsa_approve'] = CRM_Utils_System::url('civicrm/dsa/approval', "id={$dao->id}&aid={$row['activity_id']}&case_id={$dao->case_id}&cid={$dao->case_contact_id}&action=approve");
      $row['url_dsa_reject'] = CRM_Utils_System::url('civicrm/dsa/approval', "id={$dao->id}&aid={$row['activity_id']}&case_id={$dao->case_id}&cid={$dao->case_contact_id}&action=reject");
      $row['actions'] = $this->setRowActions($dao->case_id, $dao->case_contact_id);
      if($dao->type == 1){
        $row['dsa_or_creditdsa'] = "DSA";
      } else if($dao->type == 3){
        $row['dsa_or_creditdsa'] = "Credit DSA";
      }
      $myDSA[$dao->activity_id] = $row;
    }

    return $myDSA;
  }

  /**
   * Function to set the row action urls and links for each row
   *
   * @param int $claimId
   * @return array $actions
   * @access protected
   */
  protected function setRowActions($case_id, $case_contact_id) {
    $actions = array();
    $manageCaseUrl = CRM_Utils_System::url('civicrm/contact/view/case', "reset=1&action=view&cid={$case_contact_id}&id={$case_id}", true);
    $actions[] = '<a class="action-item" title="Manage Case" href="'.$manageCaseUrl.'">Manage Case</a>';
    return $actions;
  }

  /**
   * Function to set the page configuration
   *
   * @access protected
   */
  protected function setPageConfiguration() {
    CRM_Utils_System::setTitle(ts("My DSA"));
    $session = CRM_Core_Session::singleton();
    $this->_userContactId = $session->get('userID');
    $session->pushUserContext(CRM_Utils_System::url('civicrm/dsa/mydsa', 'reset=1', true));
  }

  /**
   * Method to initialize pager
   *
   * @access protected
   */
  protected function initializePager() {
    $config = CRM_Expenseclaims_Config::singleton();

    try {
      $params = array(
        'total' => CRM_Core_DAO::singleValueQuery("
          SELECT COUNT(*)
          FROM civicrm_dsa_compose dsa
          LEFT JOIN civicrm_case_contact cc ON cc.case_id = dsa.case_id
          WHERE dsa.secondary_approval_approved IS NULL AND (dsa.amount_dsa+dsa.amount_briefing+dsa.amount_airport+dsa.amount_transfer+dsa.amount_hotel+dsa.amount_visa+dsa.amount_medical+dsa.amount_other) >= 2000"
        ),
        'rowCount' => 20,
        'status' => ts('MyDSA %%StatusMessage%%'),
        'buttonBottom' => 'PagerBottomButton',
        'buttonTop' => 'PagerTopButton',
        'pageID' => $this->get(CRM_Utils_Pager::PAGE_ID),
      );

      $this->_pager = new CRM_Utils_Pager($params);
      $this->assign_by_ref('pager', $this->_pager);
    } catch (Exception $ex) {

    }
  }
}
