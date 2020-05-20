<?php

use CRM_Dsa_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Dsa_Form_DSATeamleaders extends CRM_Core_Form {
  protected $teamleaders;

  function preProcess() {
    CRM_Utils_System::setTitle('DSA Teamleaders');
    $teamleaders = CRM_Dsa_BAO_DsaTeamleaders::getValues();
    $this->teamleaders = $teamleaders;
    $this->setDefaultValues();
    parent::preProcess();
  }

  public function buildQuickForm() {
    $dsaTeamleadersSelect = $this->addElement('advmultiselect', 'teamleaders', ts('Select Teamleaders'), CRM_Dsa_Utils::getPaidStaff(),
      array('id' => 'teamleaders','class' => 'advmultselect', 'size' => 10, 'style' => 'width:auto;'),TRUE);
    $dsaTeamleadersSelect->setButtonAttributes('add', array('value' => ts('Add teamleader')." >>"));
    $dsaTeamleadersSelect->setButtonAttributes('remove', array('value' => "<< ".ts('Remove teamleader')));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE),
      array('type' => 'cancel', 'name' => ts('Cancel'))));

    $this->assign('elementNames', $this->getRenderableElementNames());

    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $defaults = array();

    foreach($this->teamleaders as $key => $value){
      $defaults['teamleaders'][] = $value['contact_id'];
    }

    return $defaults;
  }

  public function postProcess() {
    $values = $this->exportValues();

    CRM_Core_DAO::executeQuery('TRUNCATE TABLE civicrm_dsa_teamleaders');

    if(is_array($values['teamleaders'])){
      foreach($values['teamleaders'] as $key => $contact_id) {
        $this->teamleaders['contact_id'] = (int)$contact_id;
        CRM_Dsa_BAO_DsaTeamleaders::add($this->teamleaders);
      }
    }

    CRM_Utils_System::redirect('/civicrm/dsa/teamleaders?reset=1');

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
