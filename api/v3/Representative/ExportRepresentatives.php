<?php
use CRM_Dsa_ExtensionUtil as E;

require_once "api/v3/PaymentMisc.php";

/**
 * Representative.ExportRepresentatives API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_representative_exportrepresentatives_spec(&$spec) {
  //$spec['magicword']['api.required'] = 1;
}

/**
 * Representative.ExportRepresentatives API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_representative_exportrepresentatives($params) {
  $representatives = array();

  $result_group_additional_data = civicrm_api('CustomGroup', 'getsingle', array('version' => 3, 'sequential' => 1, 'title' => 'Additional Data'));
  $result_group_bank_information = civicrm_api('CustomGroup', 'getsingle', array('version' => 3, 'sequential' => 1, 'title' => 'Bank Information'));

  $sql = "SELECT DISTINCT ct.id, ct.first_name, ct.middle_name, ct.last_name, m.email, ctd.shortname_14 AS 'shortname', ctd.initials_17 AS 'initials', adr.street_address, adr.postal_code, adr.city, cny.name AS 'country', cny.iso_code AS 'country_iso_code', bcny.iso_code AS 'bank_country_iso_code', bank.*, ahcy.name AS 'bank_accountholder_countryname'
          FROM civicrm_contact ct
          LEFT JOIN civicrm_address adr ON adr.contact_id = ct.id AND adr.is_primary = 1
          LEFT JOIN civicrm_country cny ON cny.id = adr.country_id
          LEFT JOIN civicrm_email m ON m.contact_id = ct.id
          LEFT JOIN {$result_group_additional_data['table_name']} ctd ON ctd.entity_id = ct.id
          LEFT JOIN {$result_group_bank_information['table_name']} bank ON bank.entity_id = ct.id
          LEFT JOIN civicrm_country bcny ON bcny.id = bank.bank_country_iso_code_55
          LEFT JOIN civicrm_country ahcy ON ahcy.id = bank.bank_country_iso_code_55
          LEFT JOIN civicrm_group_contact gc ON gc.contact_id = ct.id
          LEFT JOIN civicrm_group g ON g.id = gc.group_id
          WHERE m.is_primary = 1
            AND gc.group_id = (SELECT g.id FROM civicrm_group g WHERE g.title = 'Representatives')
            AND gc.status = 'Added'
          GROUP BY ct.id";

  $dao = CRM_Core_DAO::executeQuery($sql);

  while ($dao->fetch()) {
    $representatives[$dao->id] = array(
      'contact_id' => $dao->id,
      'first_name' => empty($dao->first_name)?'':$dao->first_name,
      'middle_name' => empty($dao->middle_name)?'':$dao->middle_name,
      'last_name' => empty($dao->last_name)?'':$dao->last_name,
      'email' => empty($dao->email)?'':$dao->email,
      'shortname' => empty($dao->shortname)?'':$dao->shortname,
      'initials' => empty($dao->initials)?'':$dao->initials,
      'street_address' => empty($dao->street_address)?'':$dao->street_address,
      'postal_code' => empty($dao->postal_code)?'':$dao->postal_code,
      'city' => empty($dao->city)?'':$dao->city,
      'country' => empty($dao->country)?'':$dao->country,
      'country_iso_code' => empty($dao->country_iso_code)?'':$dao->country_iso_code,
      'bank_account_iban_accountnumber' => (!empty($dao->iban_number_56)?$dao->iban_number_56:(!empty($dao->bank_account_number_448)?$dao->bank_account_number_448:'')),
      'bank_iban' => empty($dao->iban_number_56)?'':$dao->iban_number_56,
      'bank_accountnumber' => empty($dao->bank_account_number_448)?'':$dao->bank_account_number_448,
      'bank_countryisocode' => empty($dao->bank_country_iso_code)?'':$dao->bank_country_iso_code,
      'bank_name' => empty($dao->bank_name_53)?'':$dao->bank_name_53,
      'bank_city' => empty($dao->bank_city_54)?'':$dao->bank_city_54,
      'bank_bicswiftcode' => empty($dao->bic_swiftcode_57)?'':$dao->bic_swiftcode_57,
      'bank_accountholder_name' => empty($dao->accountholder_name_59)?'':$dao->accountholder_name_59,
      'bank_accountholder_address' => empty($dao->accountholder_address_60)?'':$dao->accountholder_address_60,
      'bank_accountholder_postalcode' => empty($dao->accountholder_postal_code_61)?'':$dao->accountholder_postal_code_61,
      'bank_accountholder_city' => empty($dao->accountholder_city_62)?'':$dao->accountholder_city_62,
      'bank_accountholder_country' => empty($dao->bank_accountholder_countryname)?'':$dao->bank_accountholder_countryname,
      'bank_foreign_bankaccount' => empty($dao->foreign_bank_account_427)?'':$dao->foreign_bank_account_427
    );
  }

  // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
  return civicrm_api3_create_success($representatives, $params, 'Representative', 'ExportRepresentatives');
}
