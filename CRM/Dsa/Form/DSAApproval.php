<?php

use CRM_Dsa_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Dsa_Form_DSAApproval extends CRM_Core_Form {
  protected $dsaId = NULL;
  protected $activityId = NULL;
  protected $caseId = NULL;
  protected $caseContactId = NULL;

  function preProcess() {

  }

  /**
   * CRM_Dsa_Form_DSAApproval::buildQuickForm()
   *
   * Form for approval or disapproval of dsa
   *
   * @return void
   */
  public function buildQuickForm() {
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

    $session = CRM_Core_Session::singleton();
    $this->dsaId = CRM_Utils_Request::retrieve('id', 'Integer');
    $this->activityId = CRM_Utils_Request::retrieve('aid', 'Integer');
    $this->caseId = CRM_Utils_Request::retrieve('case_id', 'Integer');
    $this->caseContactId = CRM_Utils_Request::retrieve('cid', 'Integer');

    $mydsaUrl = CRM_Utils_System::url('civicrm/dsa/mydsa', '', TRUE);
    $current_contact = $session->getLoggedInContactID();
    $action = !empty($_GET['action'])?$_GET['action']:'';

    if(CRM_Dsa_Utils::isDSAManagerOperations($current_contact) && !empty($action) && ($action == 'approve')) {
      //Set db field to approved
      if (!empty($current_contact) && !empty($this->dsaId)) {
        $sql = "UPDATE `civicrm_dsa_compose` SET `secondary_approval_cid` = '".$current_contact."', `secondary_approval_datetime` = NOW(), `secondary_approval_approved` = 1 WHERE `id` = '" . $this->dsaId."'";
        $dao = CRM_Core_DAO::executeQuery($sql);
        //Set activity status back to payable
        $sql = "UPDATE `civicrm_activity` SET `status_id` = '".CRM_Dsa_Utils::getActivityStatusPayable()."' WHERE ((`original_id` = '".$this->activityId."' AND `is_current_revision` = 1) OR (`id` = '" . $this->activityId."'))";
        $dao = CRM_Core_DAO::executeQuery($sql);

        $this->sendMailToProf(TRUE, $this->dsaId, $this->caseId, $this->activityId, $current_contact, $this->caseContactId);

        $session->setStatus('DSA approved', 'DSA approved', 'success');
        CRM_Utils_System::redirect($mydsaUrl);
      } else {
        $session->setStatus('Current contactId or dsaId could not be found', 'DSA approval is missing details', 'error');
      }
    }
    else if(CRM_Dsa_Utils::isDSAManagerOperations($current_contact) && !empty($action) && ($action == 'reject')){

      if (!empty($current_contact) && !empty($this->dsaId)) {
        //Set db field to rejected
        $sql = "UPDATE `civicrm_dsa_compose` SET `secondary_approval_cid` = '".$current_contact."', `secondary_approval_datetime` = NOW(), `secondary_approval_approved` = 0 WHERE `id` = '" . $this->dsaId."'";
        $dao = CRM_Core_DAO::executeQuery($sql);

        //Set activity status to rejected
        $sql = "UPDATE `civicrm_activity` SET `status_id` = '".CRM_Dsa_Utils::getActivityStatusDsaRejected()."' WHERE ((`original_id` = '".$this->activityId."' AND `is_current_revision` = 1) OR (`id` = '" . $this->activityId."'))";
        $dao = CRM_Core_DAO::executeQuery($sql);

        $this->sendMailToProf(FALSE, $this->dsaId, $this->caseId, $this->activityId, $current_contact, $this->caseContactId);

        $session->setStatus('DSA rejected', 'DSA rejected', 'success');
        CRM_Utils_System::redirect($mydsaUrl);
      } else {
        $session->setStatus('Current contactId or dsaId could not be found', 'DSA rejection is missing details', 'error');
      }
    } else {
      $session->setStatus(ts('You are not authorized to approve or reject DSA for this user.'), ts('Not authorized'), 'error');
      CRM_Utils_System::redirect($mydsaUrl);
    }

    parent::buildQuickForm();
  }

  /**
   * CRM_Dsa_Form_DSAApproval::sendMailToProf()
   *
   * Function to send an e-mail to the project officer when dsa is approved or rejected
   *
   * @param mixed $isApproved
   * @param mixed $dsaId
   * @param mixed $caseId
   * @param mixed $activityId
   * @param mixed $current_contact
   * @param mixed $caseContactId
   * @return void
   */
  function sendMailToProf($isApproved, $dsaId, $caseId, $activityId, $current_contact, $caseContactId) {
    global $base_url;
    $prof = CRM_Dsa_Utils::getProjectOfficerFromCase($caseId);
    $approval_contact_cid = CRM_Core_DAO::singleValueQuery("SELECT `secondary_approval_cid` FROM `civicrm_dsa_compose` WHERE `id` = %1", array(1=>array((int)$dsaId, 'Integer')));
    $approval_contact_name = CRM_Dsa_Utils::getDisplayName($approval_contact_cid);

    try {
      $domain = civicrm_api('Domain', 'getsingle', array('version' => 3, 'sequential' => 1));
      $case_subject = civicrm_api('Case', 'getvalue', array('version' => 3, 'sequential' => 1, 'id' => $caseId, 'return' => 'subject'));

      $dsa_config = array();
      $params_dsa_config = array(
        'version' => 3,
        'q' => 'civicrm/ajax/rest',
        'sequential' => 1,
        'option_group_name' => 'dsa_configuration',
        'return' => 'name,value',
      );

      $result = civicrm_api('OptionValue', 'get', $params_dsa_config);

      foreach($result['values'] as $value) {
        $dsa_config[$value['name']] = $value['value'];
      }

      $mail_from = $domain['from_email'];
    } catch (Exception $e) {
      throw new Exception('Failed sending email');
    }

    if($isApproved == TRUE) {
      //Mail prof approved
      foreach($prof as $cid) {
        try {
          $params_email = array(
            'version' => 3,
            'sequential' => 1,
            'contact_id' => $cid,
            'is_primary' => 1
          );
          $result_email = civicrm_api('Email', 'getsingle', $params_email);

          $params_contact = array(
            'version' => 3,
            'sequential' => 1,
            'contact_id' => $cid
          );
          $result_contact = civicrm_api('Contact', 'getsingle', $params_contact);
        } catch(Exception $e){
          $result_contact['first_name'] = 'sir/madam';
        }

        $mail_to = $result_email['email'];

        if(!empty($result_email['email'])){
          $mail_subject = 'DSA for case '.$caseId.' has been approved';

          $nl="\r\n";
          $mail_headers = 'MIME-Version: 1.0' . $nl;
          $mail_headers .= 'Content-type: text/html; charset=iso-8859-1' . $nl;
          $mail_headers .= 'From: ' . $mail_from . $nl;
          $mail_headers .= 'Reply-To: ' . $mail_from . $nl;

          $mail_message = '<html>';
          $mail_message .= '<head>';
          $mail_message .= '<title>';
          $mail_message .= $mail_subject;
          $mail_message .= '</title>';
          $mail_message .= '</head>';
          $mail_message .= '<body>';
          $mail_message .= 'Dear '.$result_contact['first_name'].',<br />';
          $mail_message .= '<br />';
          $mail_message .= 'Your DSA amount for case: <a href="'.$base_url.'/civicrm/contact/view/case?reset=1&action=view&cid='.$caseContactId.'&id='.$caseId.'">'.$caseId.'</a> with a total amount of: &euro; '.CRM_Dsa_Utils::getDSAAmount($caseId).' has been approved by: '.$approval_contact_name.'.';
          $mail_message .= '<br />';
          $mail_message .= '<a href="'.$base_url.'/civicrm/contact/view/case?reset=1&action=view&cid='.$caseContactId.'&id='.$caseId.'">Link to case</a><br />';
          $mail_message .= '<br />';
          $mail_message .= 'Kind regards,';
          $mail_message .= '<br />';
          $mail_message .= 'Procus';
          $mail_message .= '</body>';
          $mail_message .= '</html>';

          $mail_sent = mail($mail_to, $mail_subject, $mail_message, $mail_headers);

          if (!$mail_sent) {
            CRM_Core_Session::setStatus('Failed sending email to project officer', 'error');
          }
        } else {
          CRM_Core_Error::debug_log_message('Unable to find E-mail of project officer in DSA Approval of case: '.$caseId.'. Approval Status: '.$isApproved);
        }
      }
    } else {
      //Mail prof rejected
      foreach($prof as $cid) {
        try {
          $params_email = array(
            'version' => 3,
            'sequential' => 1,
            'contact_id' => $cid,
            'is_primary' => 1
          );
          $result_email = civicrm_api('Email', 'getsingle', $params_email);

          $params_contact = array(
            'version' => 3,
            'sequential' => 1,
            'contact_id' => $cid
          );
          $result_contact = civicrm_api('Contact', 'getsingle', $params_contact);
        } catch(Exception $e){
          $result_contact['first_name'] = 'sir/madam';
        }

        $mail_to = $result_email['email'];

        if(!empty($result_email['email'])){
          $mail_subject = 'DSA for case '.$caseId.' has been rejected';

          $nl="\r\n";
          $mail_headers = 'MIME-Version: 1.0' . $nl;
          $mail_headers .= 'Content-type: text/html; charset=iso-8859-1' . $nl;
          $mail_headers .= 'From: ' . $mail_from . $nl;
          $mail_headers .= 'Reply-To: ' . $mail_from . $nl;

          $mail_message = '<html>';
          $mail_message .= '<head>';
          $mail_message .= '<title>';
          $mail_message .= $mail_subject;
          $mail_message .= '</title>';
          $mail_message .= '</head>';
          $mail_message .= '<body>';
          $mail_message .= 'Dear '.$result_contact['first_name'].',<br />';
          $mail_message .= '<br />';
          $mail_message .= 'Your DSA amount for case: <a href="'.$base_url.'/civicrm/contact/view/case?reset=1&action=view&cid='.$caseContactId.'&id='.$caseId.'">'.$caseId.'</a> with a total amount of: &euro; '.CRM_Dsa_Utils::getDSAAmount($caseId).' has been rejected by: '.$approval_contact_name.'.';
          $mail_message .= '<br />';
          $mail_message .= 'Please make the necessary changes and set the activity back to payable.';
          $mail_message .= '<br />';
          $mail_message .= '<a href="'.$base_url.'/civicrm/contact/view/case?reset=1&action=view&cid='.$caseContactId.'&id='.$caseId.'">Link to case</a><br />';
          $mail_message .= '<br />';
          $mail_message .= 'Kind regards,';
          $mail_message .= '<br />';
          $mail_message .= 'Procus';
          $mail_message .= '</body>';
          $mail_message .= '</html>';

          $mail_sent = mail($mail_to, $mail_subject, $mail_message, $mail_headers);

          if (!$mail_sent) {
            CRM_Core_Session::setStatus('Failed sending email to project officer', 'error');
          }
        } else {
          CRM_Core_Error::debug_log_message('Unable to find E-mail of project officer in DSA Rejection of case: '.$caseId.'. Approval Status: '.$isApproved);
        }
      }
    }
  }

  public function postProcess() {
    $values = $this->exportValues();

    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
