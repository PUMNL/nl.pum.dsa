<?php

use CRM_Dsa_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Dsa_Form_DSAManagersOperations extends CRM_Core_Form {
  protected $managers_operations;

  function preProcess() {
    CRM_Utils_System::setTitle('DSA Managers Operations');
    $managers_operations = CRM_Dsa_BAO_DSAManagersOperations::getValues();
    $this->managers_operations = $managers_operations;
    $this->setDefaultValues();
    parent::preProcess();
  }

  public function buildQuickForm() {
    $dsaManagerOperationsSelect = $this->addElement('advmultiselect', 'managers_operations', ts('Select Manager Operations'), CRM_Dsa_Utils::getPaidStaff(),
      array('id' => 'managers_operations','class' => 'advmultselect', 'size' => 10, 'style' => 'width:auto;'),TRUE);
    $dsaManagerOperationsSelect->setButtonAttributes('add', array('value' => ts('Add Manager Operations')." >>"));
    $dsaManagerOperationsSelect->setButtonAttributes('remove', array('value' => "<< ".ts('Remove Manager Operations')));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE),
      array('type' => 'cancel', 'name' => ts('Cancel'))));

    $this->assign('elementNames', $this->getRenderableElementNames());

    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $defaults = array();

    foreach($this->managers_operations as $key => $value){
      $defaults['managers_operations'][] = $value['contact_id'];
    }

    return $defaults;
  }

  public function postProcess() {
    $values = $this->exportValues();

    CRM_Core_DAO::executeQuery('TRUNCATE TABLE civicrm_dsa_managersoperations');

    if(is_array($values['managers_operations'])){
      foreach($values['managers_operations'] as $key => $contact_id) {
        $this->managers_operations['contact_id'] = (int)$contact_id;
        CRM_Dsa_BAO_DSAManagersOperations::add($this->managers_operations);
      }
    }

    CRM_Utils_System::redirect('/civicrm/dsa/managers_operations?reset=1');

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
