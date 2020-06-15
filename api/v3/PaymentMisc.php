<?php

/* ************************************************** *
 * This file contains functions that shared between   *
 *   - DSA ProcessPayment API, and                    *
 *   - Representative ProcessPayment API              *
 * ************************************************** *
 */



/*
 * Function to mimic implode($ar), but implicitly modify the values before concatenation:
 * - replace / remove odd characters
 * - extend the length of values using certain characters (likely ' '  or '0') at either the left or the right of the value (string)
 * - trim each value down to a certain size
 */
function _dsa_concatValues($ar) {
  // field order and size is fixed!
  // modifications may render all output useless for the financial system!

  // prefilter chars in accountnumber and IBAN
  $ar['BankRekNr'] = _charFilterNumbers($ar['BankRekNr']);  // experts bank account number (not IBAN) can only contain [0-9]
  $ar['IBAN'] = _charFilterUpperCaseNum($ar['IBAN'], TRUE); // bank account IBAN can only contain [0-9A-Z]
  $ar['BIC'] = _charFilterUpperCaseNum($ar['BIC'], TRUE);   // BIC/Swift code can only contain [0-9A-Z]

  return
    _dsaSize($ar['Boekjaar'],              2, '',   TRUE,   FALSE,  TRUE) . // 14 for 2014
    _dsaSize($ar['Dagboek'],               2, '',   TRUE,   FALSE,  TRUE) . // I1 for DSA, I3 for Representative payment
    _dsaSize($ar['Periode'],               2, '',   TRUE,   FALSE,  TRUE) . // 06 for June
    _dsaSize($ar['Boekstuk'],              4, '0',  FALSE,  FALSE,  TRUE) . // #### sequence (must be unique per month)
    _dsaSize($ar['GrootboekNr'],           5, '0',  TRUE,   FALSE,  TRUE) . // general ledger code
    _dsaSize($ar['Sponsorcode'],           5, '',   TRUE,   FALSE,  TRUE) . // "10   " for DGIS where "610  " would be preferred
    _dsaSize($ar['Kostenplaats'],          8, '',   TRUE,   FALSE,  TRUE) . // project number CCNNNNNT (country, number, type)
    _dsaSize($ar['Kostendrager'],          8, '',   TRUE,   FALSE,  TRUE) . // country code main activity (8 chars!)
    _dsaSize($ar['Datum'],                10, '',   TRUE,   FALSE,  TRUE) . // today
    _dsaSize($ar['DC'],                    1, '',   TRUE,   FALSE,  TRUE) . // D for payment, C for full creditation. Ther will be NO partial creditation.
    _dsaSize($ar['PlusMin'],               1, '',   TRUE,   TRUE,   TRUE) . // + for payment, - for creditation
    //_dsaSize($ar['Bedrag'],             10, '',   FALSE,  FALSE) .        // not in use: 10 digit numeric 0
    //_dsaSize($ar['Filler1'],             9, ' ',  TRUE,   FALSE) .        // not in use: 9 spaces
    _dsaSize($ar['OmschrijvingA'],        10, '',   TRUE,   FALSE,  TRUE) . // description fragment: surname
    //_dsaSize(' ',                        1, '',   TRUE,   FALSE) .        // description fragment: additional space
    _dsaSize($ar['OmschrijvingB'].' '.$ar['OmschrijvingC'], 8, '', TRUE,  FALSE, TRUE) .  // description fragment: main activity number ("NNNNN ")
    //_dsaSize($ar['OmschrijvingC'],       3, '',   TRUE,   FALSE) .        // description fragment: country ("CC ")
    //_dsaSize($ar['Filler2'],            13, '',   TRUE,   FALSE) .        // not in use: 13 spaces
    _dsaSize($ar['FactuurNrRunType'].$ar['FactuurNrYear']._dsaSize($ar['FactuurNr'], 5, '0', FALSE, FALSE, FALSE), 8, '', TRUE, FALSE, TRUE) .  // D for DSA, L for Representative payment
    //_dsaSize($ar['FactuurNrYear'],       2, '',   TRUE,   FALSE) .        // 14 for 2014; date of "preparation", not dsa payment! :=> civi: date of original activity
    //_dsaSize($ar['FactuurNr'],           4, '0',  FALSE,  FALSE) .        // sequence based: "123456" would return "2345", "12" would return "0001"
    //_dsaSize($ar['FactuurNrAmtType'],    1, '',   TRUE,   FALSE) .        // represents type of amount in a single character: 1-9, A-Z
    _dsaSize($ar['FactuurDatum'],         10, '',   TRUE,   FALSE,  TRUE) . // creation date (dd-mm-yyyy) of DSA activity (in Notes 1st save when in status "preparation") :=> civi: date of original activity
    _dsaSize($ar['FactuurBedrag'],        11, '',   FALSE,  FALSE,  TRUE) . // payment amount in EUR cents (123,456 -> 12346)
    _dsaSize($ar['FactuurPlusMin'],        1, '',   TRUE,   TRUE,   TRUE) . // + for payment, - for creditation
    _dsaSize($ar['OmschrijvingB'],         8, '',   TRUE,   TRUE,   TRUE) . // Project number
    _dsaSize($ar['OmschrijvingC'],         2, '',   TRUE,   TRUE,   TRUE) . // Country code
    //_dsaSize($ar['Kenmerk'],            12, '',   TRUE,   FALSE) .        // project number NNNNNCC (number, country)
    _dsaSize($ar['ValutaCode'],            3, '',   TRUE,   FALSE,  TRUE) . // always EUR
    _dsaSize($ar['CrediteurNr'],           8, '',   TRUE,   FALSE,  TRUE) . // experts shortname (8 char)
    _dsaSize($ar['NaamOrganisatie'],      35, ' ',  TRUE,   FALSE,  TRUE) . // experts name (e.g. "van Oranje-Nassau W.A.")
    _dsaSize($ar['Taal'],                  1, '',   TRUE,   FALSE,  TRUE) . // always "N"
    _dsaSize($ar['Land'],                  3, ' ',  TRUE,   FALSE,  TRUE) . // ISO2
    _dsaSize($ar['Adres1'],               35, ' ',  TRUE,   FALSE,  TRUE) . // experts street + number
    _dsaSize($ar['Adres2'],               35, ' ',  TRUE,   FALSE,  TRUE) . // experts zip + city
    _dsaSize($ar['BankRekNr'],            25, '',   TRUE,   FALSE,  TRUE) . // experts bank account: number (not IBAN)
    _dsaSize($ar['Soort'],                 1, '',   TRUE,   FALSE,  TRUE) . // main activity (case) type (1 character)
    _dsaSize($ar['Shortname'],             8, '',   TRUE,   FALSE,  TRUE) . // experts shortname (8 char)
    _dsaSize($ar['Rekeninghouder'],       35, ' ',  TRUE,   FALSE,  TRUE) . // bank account holder: name
    _dsaSize($ar['RekeninghouderLand'],    3, '',   TRUE,   FALSE,  TRUE) . // bank account holder: country (ISO2)
    _dsaSize($ar['RekeninghouderAdres1'], 35, ' ',  TRUE,   FALSE,  TRUE) . // bank account holder: street + number
    _dsaSize($ar['RekeninghouderAdres2'], 35, ' ',  TRUE,   FALSE,  TRUE) . // bank account holder: zip + city
    _dsaSize($ar['IBAN'],                 34, ' ',  TRUE,   FALSE,  TRUE) . // bank account: IBAN
    _dsaSize($ar['Banknaam'],             35, ' ',  TRUE,   FALSE,  TRUE) . // bank name
    //_dsaSize($ar['BankPlaats'],         35, '',   TRUE,   FALSE) .        // bank city
    _dsaSize($ar['BankLand'],              3, ' ',  TRUE,   FALSE,  TRUE) . // bank country (ISO2)
    _dsaSize($ar['BIC'],                  11, 'X',  TRUE,   FALSE,  TRUE) . // experts bank account: BIC/Swift code
    _dsaSize($ar['Sector'],              128, '',   TRUE,   FALSE,  TRUE) . // experts main sector
    _dsaSize($ar['Artikel'],              64, '',   TRUE,   FALSE,  TRUE) . // client country
    _dsaSize($ar['Omschrijving'],         64, '',   TRUE,   FALSE,  FALSE); // client country
}

/*
function _getCustomDefinitions() {
  // prepare result array
  $result = array();
  // custom field group "Additional Data" ---------------------------------------------------------------------------
  // retrieve table name for custom group
  $sql = "SELECT id, name, table_name FROM civicrm_custom_group WHERE name = 'Additional_Data'";
  $dao = CRM_Core_DAO::executeQuery($sql);
  if (!$dao->N == 1) {
    return NULL;
  }
  $dao->fetch();
  $group_name = $dao->name;
  $tbl = $dao->table_name;
  $group_id = $dao->id;
  $result[$group_name] = array(
    'table' => $tbl,
    'group_id' => $group_id,
    'columns' => '',
    'fields' => array(),
  );
  // retrieve fieldnames for custom fields
  $sql = "SELECT name, label, column_name FROM civicrm_custom_field WHERE custom_group_id = " . $group_id;
  $dao = CRM_Core_DAO::executeQuery($sql);
  if (!$dao >= 1) {
    return NULL;
  }
  while ($dao->fetch()) {
    $result[$group_name]['columns'] .=
      ($result[$group_name]['columns'] == '' ? '' : (', ' . PHP_EOL)) .
      ($tbl . '.' . $dao->column_name . ' as ' . $dao->name);
    $result[$group_name]['fields'][$dao->name] = array(
      'label' => $dao->label,
      'column_name' => $dao->column_name,
    );
  }
  // custom field group "Bank_Information" --------------------------------------------------------------------------
  // retrieve table name for custom group
  $sql = "SELECT id, name, table_name FROM civicrm_custom_group WHERE name = 'Bank_Information'";
  $dao = CRM_Core_DAO::executeQuery($sql);
  if (!$dao->N == 1) {
    return NULL;
  }
  $dao->fetch();
  $group_name = $dao->name;
  $tbl = $dao->table_name;
  $group_id = $dao->id;
  $result[$group_name] = array(
    'table' => $tbl,
    'group_id' => $group_id,
    'columns' => '',
    'fields' => array(),
  );
  // retrieve fieldnames for custom fields
  $sql = "SELECT name, label, column_name FROM civicrm_custom_field WHERE custom_group_id = " . $group_id;
  $dao = CRM_Core_DAO::executeQuery($sql);
  if (!$dao >= 1) {
    return NULL;
  }
  while ($dao->fetch()) {
    $result[$group_name]['columns'] .=
      ($result[$group_name]['columns'] == '' ? '' : (', ' . PHP_EOL)) .
      ($tbl . '.' . $dao->column_name . ' as ' . $dao->name);
    $result[$group_name]['fields'][$dao->name] = array(
      'label' => $dao->label,
      'column_name' => $dao->column_name,
    );
  }
  // return all results in an array
  return $result;
}
*/


function _getDsaStatusList() {
  $sql = '
    SELECT  ogv.name, ogv.value
    FROM  civicrm_option_value ogv, civicrm_option_group ogp
    WHERE ogv.option_group_id = ogp.id
    AND   ogp.name = \'activity_status\'
    AND   ogv.name IN (\'dsa_payable\', \'dsa_paid\')
    ';
  $dao = CRM_Core_DAO::executeQuery($sql);
  $result = array();
  while ($dao->fetch()) {
    $result[$dao->name] = $dao->value;
  }
  return $result;
}


/*
 * Function that builds (and returns) an associative array containing
 * - an array of special characters (to be replaced)
 * - an array (same size) of their replacement strings
 */
function _charReplacementBuild() {
  $arCharReplacement = array(
    'originals' => array(),
    'substitutes' => array(),
  );
  _charReplacementAdd($arCharReplacement, '·‡‰„‚',  'a');
  _charReplacementAdd($arCharReplacement, '¡¿ƒ√¬',  'A');
  _charReplacementAdd($arCharReplacement, 'ÈËÎÍ',   'e');
  _charReplacementAdd($arCharReplacement, '…»À ',   'E');
  _charReplacementAdd($arCharReplacement, 'ÌÏÔÓ',   'i');
  _charReplacementAdd($arCharReplacement, 'ÕÃœŒ',   'I');
  _charReplacementAdd($arCharReplacement, 'ÛÚˆıÙ¯', 'o');
  _charReplacementAdd($arCharReplacement, '”“÷’‘ÿ', 'O');
  _charReplacementAdd($arCharReplacement, '˙˘¸˚',   'u');
  _charReplacementAdd($arCharReplacement, '⁄Ÿ‹€',   'U');
  _charReplacementAdd($arCharReplacement, '˝ˇ',     'y');
  _charReplacementAdd($arCharReplacement, '›ü',     'Y');
  _charReplacementAdd($arCharReplacement, 'Ê',      'ae');
  _charReplacementAdd($arCharReplacement, '∆',      'AE');
  _charReplacementAdd($arCharReplacement, 'Ò',      'n');
  _charReplacementAdd($arCharReplacement, '—',      'N');
  _charReplacementAdd($arCharReplacement, 'Á',      'c');
  _charReplacementAdd($arCharReplacement, '«',      'C');
  _charReplacementAdd($arCharReplacement, '?',      'ij');
  _charReplacementAdd($arCharReplacement, '?',      'IJ');
  return $arCharReplacement;
}


/*
 * Helper function for _charReplaceBuild()
 * Maps each character in $orgChars to the string in $replacementStr
 */
function _charReplacementAdd(&$arCharReplacement, $orgChars, $replacementStr) {
  for($i=0; $i<mb_strlen($orgChars); $i++) {
    $arCharReplacement['originals'][] = mb_substr($orgChars, $i, 1);
    $arCharReplacement['substitutes'][] = $replacementStr;
  }
}


/*
 * Function to replace all occurances of the elements in array $removeCharAr
 * within string $data by the (1:1) mapped elements in array $replacementCharAr
 */
function charReplacement($data, $removeCharAr, $replacementCharAr, $skipFilter=FALSE) {
  // Warning: $data may either grow or shrink in size
  // step 1: substitition of known characters
  $data = str_replace($removeCharAr, $replacementCharAr, $data); // replace all 'known' special characters by their 'known' replacement strings
  // step 2: removal of all other illegal characters
  if (!$skipFilter) {
    $data = PREG_REPLACE("/[^0-9a-zA-Z \/\-]/i", '', $data); // remove all remaining special characters
  }
  return $data;
}

/*
 * Function to remove all characters other than 0-9
 */
function _charFilterNumbers($dataStr) {
  $dataStr = PREG_REPLACE("/[^0-9]/i", '', $dataStr);
  return $dataStr;
}

/*
 * Function to remove all characters other than 0-9 or A-Z
 */
function _charFilterUpperCaseNum($dataStr, $forceUpperCase=FALSE) {
  if ($forceUpperCase) {
    $dataStr = strtoupper($dataStr);
  }
  $dataStr = PREG_REPLACE("/[^0-9A-Z]/i", '', $dataStr);
  return $dataStr;
}

/*
 * Function to resize a value to a certain length for export to a fixed row-size file
 * contains implicit replacement of odd characters
 *
 * parameters:
 * $value: the value to parse into an export line
 * $size: the length in which $value should be parsed
 * $fillChar: the character to use to increase $values length if neccessary
 * $alignLeft: if TRUE: $fillChar is appended, if FALSE $fillChar is prepended
 * $skipFilter: if TRUE: no additional removal of "unknown" character is performed. If FALSE, only certain characters are returned (by charReplacement())
 */
function _dsaSize($value='', $size, $fillChar=' ', $alignLeft=TRUE, $skipFilter=FALSE, $addSemicolon=TRUE) {
  global $charMod;
  // replace odd characters
  $value = charReplacement($value, $charMod['originals'], $charMod['substitutes'], $skipFilter);
  // verify or set the desired length of the value
  if(is_numeric($size)) {
    $size = intval($size, 10);
  } else {
    $size = strlen($value);
  }
  // prepare filler string to append to value
  if ($size > strlen($value)) {
    $len = $size - strlen($value);
    $filler = substr(str_repeat($fillChar, $len), 0, $len);
  } else {
    $filler = '';
  }
  // set value to the intended size
  if($alignLeft) {
    $value .= $filler;
  } else {
    $value = $filler . $value;
  }

  $value = substr($value, 0, $size);

  //Sometimes we need multiple fields after each other without a semicolon
  if ($addSemicolon == TRUE) {
    $value .= ';';
  }
  return $value;
}


/*
 * Function to provide general ledger codes
 * should be replaced by a pre-filled table + query
 * limit is set as civi 4.4.4 does not handle limit 0 as unlimited
 */
function _dsa_generalLedgerCodes() {
  $params = array(
    'version' => 3,
    'q' => 'civicrm/ajax/rest',
    'option_group_name' => 'general_ledger',
    'return' => 'name,value',
    'options' => array(
      'limit' => 1000,
    ),
  );
  $result = civicrm_api('OptionValue', 'get', $params);
//dpm($result, 'API result');
  $code_tbl = array();
//dpm($result['values'], 'API values');
  foreach($result['values'] as $value) {
    $code_tbl[$value['name']] = $value['value'];
  }
//dpm($code_tbl, 'general ledger codes');
  return $code_tbl;
}

/*
 * Function to provide a translation table: country id to country ISO code and -name
 * limit is set as civi 4.4.4 does not handle limit 0 as unlimited
 */
function _dsa_GetCountryISOCodes() {
  $params = array(
    'version' => 3,
    'q' => 'civicrm/ajax/rest',
    'options' => array(
      'limit' => 5000,
    ),
    'return' => 'iso_code,name',
  );
  $result = civicrm_api('Country', 'get', $params);
  return $result['values'];
}


/*
 * Function to alter some country ISO codes for use in the FA system
 * Parameter $countryAr directly or indirectly originates from _dsa_GetCountryISOCodes()
 */
function _dsa_AlterCountryISOCodes($countryAr) {
/* -- currently under discussion: #750 ---------------
  foreach($countryAr as $country) {
  switch($country['iso_code']) {
    case 'DE':
      $countryAr[$country['id']]['iso_code'] = 'DU';
      break;
//    case 'UK':
//      $countryAr[$country['id']]['iso_code'] = 'GB';
//      break;
  }
  }
*/
  return $countryAr;
}




/*
 * Equivalent function for mySQL 'ifnull(a,b)'
 * if returns a if not NULL, otherwise returns b
 */
function _ifnull($a, $b=null) {
    return is_null($a)?$b:$a;
}