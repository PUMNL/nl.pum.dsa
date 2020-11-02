<?php
use CRM_Dsa_ExtensionUtil as E;

require_once "api/v3/PaymentMisc.php";

/**
 * Representative.ProcessFixedFeePayments API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_representative_processfixedfeepayments_spec(&$spec) {
  //$spec['magicword']['api.required'] = 1;
}

/**
 * Representative.ProcessFixedFeePayments API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_representative_processfixedfeepayments($params) {
  //Get schedule for next payment
  $roster = 'Fixed fee payment for representatives';
  $msg_prefix = 'Processing '.$roster.' - ';

  $params = array(
    'version' => 3,
    'q' => 'civicrm/ajax/rest',
    'sequential' => 1,
    'name' => $roster,
  );
  $result = civicrm_api('Roster', 'IsAllowed', $params);

  if ($result['values']!=1) {
    $msg = $msg_prefix . 'execution prohibited by roster definition';
    CRM_Core_Error::debug_log_message($msg);
    throw new Exception($msg);
  }

  // message array - will be written to log after processing
  $warnings = array();
  $warnings[] = 'Passed check on roster.';

  /*
  * Update roster for next run.
  * This should be done directly after the scheduled job is started,
  * to prevent that the job will run a second time when cron is scheduled to run a short time schedule
  */
  $params = array(
    'version' => 3,
    'q' => 'civicrm/ajax/rest',
    'sequential' => 1,
    'name' => $roster,
  );
  $result = civicrm_api('Roster', 'ScheduleNext', $params);
  if ($result['values']!=1) {
    $msg = $msg_prefix.'failed to schedule next run: check roster ' . $roster;
    CRM_Core_Error::debug_log_message($msg);
    throw new Exception($msg);
  }

  //Get Payment configuration
  $params_fee_amount = array(
    'version' => 3,
    'sequential' => 1,
    'option_group_name' => 'rep_payment_configuration',
    'name' => 'fixed_representative_fee_amount',
  );
  $result_fee_amount = civicrm_api('OptionValue', 'getsingle', $params_fee_amount);
  (float)$fee_amount = (float)$result_fee_amount['value'];

  //Get sponsor for representative fee

  $params_sponsorcode_representative_fee = array(
    'version' => 3,
    'sequential' => 1,
    'option_group_name' => 'rep_payment_configuration',
    'name' => 'sponsorcode_for_representative_fee',
  );
  $result_sponsorcode_representative_fee = civicrm_api('OptionValue', 'getsingle', $params_sponsorcode_representative_fee);
  (int)$sponsor_code = (int)$result_sponsorcode_representative_fee['value'];

  if(!is_float($fee_amount) || empty($fee_amount)) {
    $msg = $msg_prefix . 'fee amount is not configured correctly';
    CRM_Core_Error::debug_log_message($msg);
    throw new Exception($msg);
  }
  if(!is_int($sponsor_code) || empty($sponsor_code)) {
    $msg = $msg_prefix . 'sponsor code is not configured correctly';
    throw new Exception($msg);
  }

  //Get list of representatives
  $params_representatives = array(
    'version' => 3,
    'sequential' => 1,
  );
  $result_representatives = civicrm_api('Representative', 'ExportRepresentatives', $params_representatives);

  if ($result_representatives['count'] <= 0) {
    $msg = $msg_prefix . 'no representatives ('.$result_representatives['count'].') found to process';
    CRM_Core_Error::debug_log_message($msg);
    throw new Exception($msg);
  }

  $msg = $msg_prefix . 'start processing records';
  CRM_Core_Error::debug_log_message($msg);
  // create new payment record and retrieve its id
  $runTime = time();
  $fileName = variable_get('file_private_path', conf_path() . '/files/private').'/rep_fee_' . date("Ymd_His", $runTime) . '.csv';
  $sqlTime = '\'' . date('Y-m-d H:i:s', $runTime) . '\'';
  $sql = "INSERT INTO civicrm_dsa_payment (timestamp) VALUES (" . $sqlTime . ")";
  $dao = CRM_Core_DAO::executeQuery($sql);

  $sql = "SELECT id FROM civicrm_dsa_payment WHERE timestamp=" . $sqlTime;
  $dao = CRM_Core_DAO::executeQuery($sql);
  if (!$dao->N == 1) {
    throw new API_Exception('Could not single out a payment record for timestamp ' . $sqlTime . ' - Export terminated.');
  }
  $result = $dao->fetch();
  $paymentId = $dao->id; // all _representative_fixedfee records should be marked with this id when processed

  // new file for export to Financial system (fin)
  $exportFin = fopen($fileName, 'x'); // write mode, error if file exists
  $exported = FALSE;
  if (!$exportFin) {
    throw new API_Exception('File "' . $fileName . '" already exists or could not be created - export terminated.');
  }

  if(!empty($fee_amount) && !empty($paymentId)){
    if(is_array($result_representatives['values']) && count($result_representatives['values']) > 0) {
      foreach($result_representatives['values'] as $key => $fields) {
        $process_record=TRUE;
        // payments: create a new invoice number
        $projTypeShort = 'C';
        $projTypeLong = 'CTM';

        if (empty($fields['country_iso_code'])){
          $warnings[] = $msg_prefix.'contact: '.$fields['contact_id'].' ('.$fields['first_name'].(!empty($fields['middle_name'])?' '.$fields['middle_name']:'').' '.$fields['last_name'].') - country_iso_code empty';
          //throw new Exception($msg_prefix.'contact: '.$fields['contact_id'].' - country_iso_code empty');
          $process_record = FALSE;
        }
        if (empty($fields['shortname'])){
          $warnings[] = $msg_prefix.'contact: '.$fields['contact_id'].' ('.$fields['first_name'].(!empty($fields['middle_name'])?' '.$fields['middle_name']:'').' '.$fields['last_name'].') - shortname empty';
          //throw new Exception($msg_prefix.'contact: '.$fields['contact_id'].' - shortname empty');
          $process_record = FALSE;
        }
        if (empty($fields['bank_accountnumber'])){
          $warnings[] = $msg_prefix.'contact: '.$fields['contact_id'].' ('.$fields['first_name'].(!empty($fields['middle_name'])?' '.$fields['middle_name']:'').' '.$fields['last_name'].') - bank account number empty';
          //throw new Exception($msg_prefix.'contact: '.$fields['contact_id'].' - bank account number empty');
          $process_record = FALSE;
        }
        if(empty($fields['bank_countryisocode'])){
          $warnings[] = $msg_prefix.'contact: '.$fields['contact_id'].' ('.$fields['first_name'].(!empty($fields['middle_name'])?' '.$fields['middle_name']:'').' '.$fields['last_name'].') - bank country iso code empty';
          //throw new Exception($msg_prefix.'contact: '.$fields['contact_id'].' - bank country iso code empty');
          $process_record = FALSE;
        }

        if ($process_record == TRUE) {
          try {
            $gl_repfixedfee = civicrm_api('OptionValue', 'getsingle', array('version' => 3, 'sequential' => 1, 'option_group_name' => 'general_ledger', 'name' => 'gl_fixed_representative_fee_amount'));
            $result_invoice = civicrm_api('Sequence', 'nextval', array('version' => 3, 'q' => 'civicrm/ajax/rest', 'sequential' => 1, 'name' => 'invoice_number'));
            $invoiceNumber =  _dsaSize('L', 1, ' ', TRUE,  FALSE, FALSE) .     // D for DSA
                              _dsaSize(date('y'),    2, ' ', TRUE,  FALSE, FALSE) .     // 14 for 2014; date of "preparation", not dsa payment! :=> civi: date of original activity
                              _dsaSize($result_invoice['values'],        4, '0', FALSE, FALSE, FALSE);      // sequence based: "123456" would return "2345", "12" would return "0001";
            $invoiceDate = date('d-m-Y');
            $sqlInvoiceDate = date('Ymd',strtotime($invoiceDate));
          } catch (Exception $e) {
            throw new Exception('Sequence \'invoice_number\' is not available.');
          }

          //Fee amount must be in cents
          $fee_amount_in_cents = '';
          if(is_double($fee_amount)){
            $fee_amount_in_cents = toPennies($fee_amount);
          } else if(is_int($fee_amount)){
            $fee_amount_in_cents = toPennies($fee_amount);
          }

          $params_volgnr = array(
            'version' => 3,
            'q' => 'civicrm/ajax/rest',
            'sequential' => 1,
            'name' => 'payment_line',
          );
          try {
            $result_volgnr = civicrm_api('Sequence', 'nextval', $params_volgnr);
            $lineNo = $result_volgnr['values'];
          } catch (Exception $e) {
            throw new Exception('Sequence \'payment_line\' is not available.');
          }

          try{
            $sql = "INSERT INTO civicrm_representative_fixedfee (contact_id,amount,payment_id, date, invoice_number) VALUES (%1,%2,%3,%4,%5)";
            CRM_Core_DAO::executeQuery($sql, array(1=>array((int)$fields['contact_id'], 'Integer'), 2=>array((float)$fee_amount,'Float'), 3=>array((int)$paymentId,'Integer'), 4=>array($sqlInvoiceDate,'String'),5=>array($invoiceNumber,'String')));
          } catch (Exception $e) {
            throw new Exception('Unable to add payment to table');
          }

          $returnValues[$fields['contact_id']] = array(
            array('Jaar' => _dsaSize(date('y'),                                              2,  '',  TRUE,   FALSE,  TRUE)),
            array('Db' => _dsaSize('I3',                                                     2,  '',  TRUE,   FALSE,  TRUE)),
            array('Mnd' => _dsaSize(date('n'),                                               2,  '',  TRUE,   FALSE,  TRUE)),
            array('Volgnr' => _dsaSize($lineNo,                                             35,  '',  TRUE,   FALSE,  TRUE)),
            array('GB' => _dsaSize($gl_repfixedfee['value'],                                 5, '0',  TRUE,   FALSE,  TRUE)),      //General ledger code for 'Fixed representative fee amount' (Option group General Ledger)
            array('KD' => _dsaSize($sponsor_code,                                           35,  '',  TRUE,   FALSE,  TRUE)),      //Sponsorcode for representative fee (Representative Payment Configuration)
            array('Project' => _dsaSize($fields['country_iso_code'].'00000'.$projTypeShort,  8,  '',  TRUE,   TRUE,   TRUE)),      //No project for fixed fee export, but normally case_sequence_country+case_sequence+case_sequence_type
            array('Projland_1' => _dsaSize($fields['country_iso_code'],                      3, ' ',  TRUE,   FALSE,  TRUE)),      //Country code
            array('FactD' => _dsaSize($invoiceDate,                                         10,  '',  TRUE,   FALSE,  TRUE)),      //export run date??
            array('DC_1' => _dsaSize('D',                                                    1,  '',  TRUE,   TRUE,   TRUE)),
            array('DC_2' => _dsaSize('+',                                                    1,  '',  TRUE,   TRUE,   TRUE)),
            array('Cred' => _dsaSize($fields['last_name'],                                  35,  '',  TRUE,   FALSE,  TRUE)),
            array('Bet_ref' => _dsaSize($fields['country_iso_code'].'00000'.$projTypeShort,  8,  '',  TRUE,   TRUE,   TRUE)),      //No project for fixed fee export, but normally case_sequence+" "+case_sequence_country
            array('Fact_nr' => _dsaSize($invoiceNumber,                                      7,  '',  TRUE,   FALSE,  TRUE)),
            array('Einddatum' => _dsaSize($invoiceDate,                                     10,  '',  TRUE,   FALSE,  TRUE)),      // export run date??
            array('FactBedr' => _dsaSize($fee_amount_in_cents,                              11,  '',  FALSE,  FALSE,  TRUE)),
            array('DC_3' => _dsaSize('+',                                                    1,  '',  TRUE,   TRUE,   TRUE)),
            array('Projnr_2' => _dsaSize('',                                                 0,  '',  TRUE,   TRUE,   TRUE)),      //No project for fixed fee export, but normally case_sequence
            array('Projland_2' => _dsaSize($fields['country_iso_code'],                      3, ' ',  TRUE,   FALSE,  TRUE)),
            array('Val' => _dsaSize('EUR',                                                   3,  '',  TRUE,   TRUE,   TRUE)),
            array('Shortn_cred' => _dsaSize($fields['shortname'],                            8,  '',  TRUE,   FALSE,  TRUE)),
            array('Name_cred' => _dsaSize($fields['last_name'].' '.$fields['initials'],     35, ' ',  TRUE,   FALSE,  TRUE)),
            array('BankAccHold' => _dsaSize('N',                                             1,  '',  TRUE,   TRUE,   TRUE)),
            array('Country_cred' => _dsaSize($fields['country_iso_code'],                    3, ' ',  TRUE,   FALSE,  TRUE)),
            array('Adres_cred' => _dsaSize($fields['street_address'],                       35, ' ',  TRUE,   FALSE,  TRUE)),
            array('Pc_City_cred' => _dsaSize($fields['postal_code'].' '.$fields['city'],    35, ' ',  TRUE,   FALSE,  TRUE)),
            array('BankAcc' => _dsaSize($fields['bank_accountnumber'],                      35, ' ',  TRUE,   FALSE,  TRUE)),
            array('ProjType' => _dsaSize($projTypeLong,                                      1,  '',  TRUE,   TRUE,   TRUE)),      //No project for fixed fee export, but normally case_sequence_type
            array('Shortn_cred' => _dsaSize($fields['shortname'],                            8, '',   TRUE,   FALSE,  TRUE)),      //two times the same csv field for Shortn_cred is a weird requirement from the payment system :(
            array('BankAccHolder' => _dsaSize($fields['bank_accountholder_name'],           35, ' ',  TRUE,   FALSE,  TRUE)),
            array('BankCntry' => _dsaSize($fields['bank_countryisocode'],                    3, '',   TRUE,   FALSE,  TRUE)),
            array('BankAccAdres' => _dsaSize($fields['bank_accountholder_address'],         35, ' ',  TRUE,   FALSE,  TRUE)),
            array('BankAccPC_city' => _dsaSize($fields['bank_accountholder_postalcode'].' '.$fields['bank_accountholder_city'], 35, ' ',  TRUE,   FALSE,  TRUE)),
            array('IBAN number' => _dsaSize($fields['bank_iban'],                           34, ' ',  TRUE,   FALSE,  TRUE)),
            array('BankName' => _dsaSize($fields['bank_name'],                              35, ' ',  TRUE,   FALSE,  TRUE)),
            array('BankCntry' => _dsaSize($fields['bank_countryisocode'],                    3, ' ',  TRUE,   FALSE,  TRUE)),
            array('BIC_SWIFT' => _dsaSize($fields['bank_bicswiftcode'],                     11, 'X',  TRUE,   FALSE,  TRUE)),
            array('Projecttype' => _dsaSize('CTM',                                           3,  '',  TRUE,   TRUE,   TRUE)),      //No project for fixed fee export, but normally case_sequence_type
            array('Sector' => _dsaSize('',                                                   0,  '',  TRUE,   TRUE,   TRUE)),      //No sector for fixed fee export, but normally main sector of expert
            array('Artikel' => _dsaSize($fields['country'],                                 64, '',   TRUE,   FALSE,  TRUE)),
            array('Omschrijving' => _dsaSize($fields['country'],                            64, '',   TRUE,   FALSE,  TRUE))
          );
        }
      }
    } else {
      $returnValues = array();
    }
  }

  $columnHeadersSet = 0;
  $columnHeaders = "";
  if(!empty($returnValues) && count($returnValues) > 0) {

    foreach($returnValues as $contactId => $payment){
      if ($columnHeadersSet == 0){
        $columnHeadersSet++;
        foreach($payment as $key => $fieldArray) {
          foreach($fieldArray as $fieldName => $fieldValue) {
            $columnHeaders .= $fieldName.';';
          }
        }
      }
    }

    $columnHeaders = substr($columnHeaders, 0, -1); // remove last ; from $columnHeaders
    fwrite($exportFin,$columnHeaders."\r\n");

    $paymentLine = '';

    foreach($returnValues as $contactId => $payment) {
      $paymentLine = '';

      foreach ($payment as $key => $fieldArray) {
        foreach($fieldArray as $fieldName => $fieldValue) {
          $paymentLine .= $fieldValue;
        }
      }

      $paymentLine = substr($paymentLine, 0, -1); // remove last ; from payment line

      fwrite($exportFin, $paymentLine . "\r\n");
      $exported = TRUE;
    }
  }
  // finalise / close file
  if (!$exported) {
    fwrite($exportFin, "\r"); // file will never be empty
  }
  fclose($exportFin);

  // read content for storage
  $exportFin = fopen($fileName, 'r');
  $contentFin = fread($exportFin, filesize($fileName));
  fclose($exportFin);

  try {
    // store
    $sql = 'UPDATE civicrm_dsa_payment SET filename=%1, filesize=%2, filetype=%3, content=%4 WHERE id=%5';
    $dao = CRM_Core_DAO::executeQuery($sql,array(1=>array(basename($fileName),'String'),2=>array(filesize($fileName),'Integer'), 3=>array('text/plain','String'), 4=>array($contentFin,'String'), 5=>array($paymentId,'Integer')));

    // retrieve FA mail address
    $dsa_config = array();
    $params = array(
      'version' => 3,
      'q' => 'civicrm/ajax/rest',
      'sequential' => 1,
      'option_group_name' => 'rep_payment_configuration',
      'return' => 'name,value',
    );

    try {
      $result = civicrm_api('OptionValue', 'get', $params);
      foreach($result['values'] as $value) {
        $dsa_config[$value['name']] = $value['value'];
      }
      $mailto = $dsa_config['mail_fa'];
      $subject = 'Representative fixed fee payment: ' . $fileName;
      $mailfrom = $dsa_config['mail_from'];
      $message = '';

      try {
        //define the headers we want passed. Note that they are separated with \r\n
        //to send HTML mail, the content-type header must be set
        $nl="\r\n";
        $headers = 'MIME-Version: 1.0' . $nl;
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . $nl;
        //additional headers
        $headers .= 'To: ' . $mailto . $nl;
        $headers .= 'From: ' . $mailfrom . $nl;
        $headers .= 'Reply-To: ' . $mailfrom . $nl;

        $url = CRM_Utils_System::url('civicrm/downloadpayments', 'payment=' . $paymentId, TRUE);
        $message = '<html>';
        $message .= '<head>';
        $message .= '<title>';
        $message .= 'Payment: ' . $fileName;
        $message .= '</title>';
        $message .= '</head>';
        $message .= '<body>';
        $message .= 'Download today\'s payments here: ';
        $message .= '<a href="' . $url . '">' . $fileName . '</a>';
        $message .= '</body>';
        $message .= '</html>';

        $mail_sent = mail( $mailto, $subject, $message, $headers );
        if (!$mail_sent) {
          throw new Exception('Failed sending email/attachment to FA');
        }

        // it is now safe to delete the file
        try {
          unlink($fileName);
        } catch (Exception $e) {
          throw new Exception('Could not delete file ' . $fileName);
        }
      } catch (Exception $e) {
        $warnings[] = $e->getMessage();
      }
    } catch (Exception $e) {
      $warnings[] = 'Value(s) missing in option group \'rep_payment_configuration\'';
    }
  } catch (Exception $e) {
    $warnings[] = $e->getMessage() . ' (while storing file ' . $fileName . ')';
  }


  foreach($warnings as $msg) {
    CRM_Core_Error::debug_log_message($msg_prefix . $msg);
  }

  CRM_Core_Error::debug_log_message($msg_prefix . 'Finished');

  //Api return values seperate array because $returnValues array is too big for job civicrm_job_log table
  //So use limited returnValues
  $apiReturnValues = array();
  foreach($returnValues as $contact_id => $value){
    $apiReturnValues[$contact_id] = $fee_amount;
  }

  return civicrm_api3_create_success($apiReturnValues, $params, 'Representative', 'ProcessFixedFeePayments');
}