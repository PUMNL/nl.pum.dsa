<?php

/**
 * Representative.ProcessPayments API
 */
 
require_once "api/v3/PaymentMisc.php";

global $charMod;

/**
 * Representative.ProcessPayments API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_representative_processpayments_spec(&$spec) {
/*
  $spec['magicword']['api.required'] = 1;
*/
}

/**
 * Representative.ProcessPayments API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_representative_processpayments($params) {

  global $charMod;

  // verify schedule
  $roster = 'Representative payment';
  $params = array(
	'version' => 3,
	'q' => 'civicrm/ajax/rest',
	'sequential' => 1,
	'name' => $roster,
	);
  $result = civicrm_api('Roster', 'IsAllowed', $params);
  if ($result['values']!=1) {
    $msg = 'Processing representative payments - execution prohibited by roster definition: ' . $roster;
	CRM_Core_Error::debug_log_message($msg);
	throw new Exception($msg);
  }
  
  // message array - will be written to log after processing
  $warnings = array();
  $warnings[] = 'Passed check on roster.';
  
  // create new payment record and retrieve its id
  $runTime = time();
  $fileName = 'rep_' . date("Ymd_His", $runTime) . '.txt'; //===== need to add a path; is not root folder of CMS ========================================
  $sqlTime = '\'' . date('Y-m-d H:i:s', $runTime) . '\'';
  $sql = "INSERT INTO civicrm_dsa_payment (timestamp) VALUES (" . $sqlTime . ")";
  $dao = CRM_Core_DAO::executeQuery($sql);
  
  $sql = "SELECT id FROM civicrm_dsa_payment WHERE timestamp=" . $sqlTime;
  $dao = CRM_Core_DAO::executeQuery($sql);
  if (!$dao->N == 1) {
	throw new API_Exception('Could not single out a payment record for timestamp ' . $sqlTime . ' - Export terminated.');
  }
  $result = $dao->fetch();
  $paymentId = $dao->id; // all -dsa_compose records should be marked with this id when processed

  // cache countryname vs ISO code
  $country = _dsa_AlterCountryISOCodes(_dsa_GetCountryISOCodes());
  
  // fetch DSA statusses
  $statusLst = _getDsaStatusList();
  if (!array_key_exists('dsa_payable', $statusLst) || !array_key_exists('dsa_paid', $statusLst)) {
    // cannot look for payable activities or cannot mark them paid
	throw new API_Exception('Mandatory status for DSA is not defined - export terminated.');
  }
  
  // character replacement definitions
  $charMod = _charReplacementBuild();
  
  // new file for export to Financial system (fin)
  $exportFin = fopen($fileName, 'x'); // write mode, error if file exists
  $exported = FALSE;
  if (!$exportFin) {
	throw new API_Exception('File "' . $fileName . '" already exists - export terminated.');
  }
  
  // empty string to build an overall payment report
  $reportDsa = '';
  
  // empty string to build a payment report for the expert (participant)
  $reportExpert = '';
  
  // general ledger ("grootboek') codes
  $gl = _dsa_generalLedgerCodes();
  
  // standard record set (keys in dutch) containing run-specific details ==========================
  // fields not yet in the right order!
  $finrec_std = array(
	'Boekjaar'				=> date('y', $runTime),											// 14 for 2014
	'Dagboek'				=> 'I3',														// I3 for Representative payments
	'Periode'				=> date('m', $runTime),											// 06 for June
	'Datum'					=> date('d-m-Y', $runTime),										// today
	'Bedrag'				=> '0',															// not in use (10 * "0")
	'Filler1'				=> ' ',															// not in use (9 * " ")
	'Filler2'				=> '',															// not in use (13 * " ")
	'FactuurNrRunType'		=> 'L',															// L for Representative payments
  );
  
  // fetch payable representative payments
  $daoDsa = _dao_retrievePayableRepresentatives($statusLst);

    // loop: representative payments
  while ($daoDsa->fetch()) {
	try {
//dpm($daoDsa, 'dsa record');
		$process_record=TRUE;
		$donor_id = ''; // track donor id for creditation; not donor code
		// check if address (country id), approver id and approval date are available
		if (empty($daoDsa->country_id)) {
			$warnings[] = 'No primary address found for contact ' . $daoDsa->display_name . ' (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ', ' . $daoDsa->subject . ')';
			$process_record=FALSE;
		}
		if (empty($daoDsa->Shortname)) {
			$warnings[] = 'No shortname found for contact ' . $daoDsa->display_name . ' (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ', ' . $daoDsa->subject . ')';
			$process_record=FALSE;
		}
		if (empty($daoDsa->Bank_Account_Number) || empty($daoDsa->Bank_Country_ISO_Code)) {
			$warnings[] = 'No bank account details found for contact ' . $daoDsa->display_name . ' (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ', ' . $daoDsa->subject . ')';
			$process_record=FALSE;
		}
		if (empty($daoDsa->approval_datetime) || empty($daoDsa->approver_name)) {
			$warnings[] = 'No DSA approval details found (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ', ' . $daoDsa->subject . ')';
			$process_record=FALSE;
		}
		if (empty($daoDsa->case_sequence) || empty($daoDsa->case_type) || empty($daoDsa->case_country)) {
			$warnings[] = 'No PUM main activity number found (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ', ' . $daoDsa->subject . ')';
			$process_record=FALSE;
		}
		if (empty($daoDsa->Donor_code)) {
			$warnings[] = 'No donor code found (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ', ' . $daoDsa->subject . ')';
			$process_record=FALSE;
		}
		if ($process_record) {
			// prepare an empty array to collect all payment lines for a single record (export only complete record)
			$recExportFin = array();
			
			// prepare am empty array to record changes to columns in civicrm_dsa_compose
			$recSql_DsaCompose = array();
			
			// add activity specific details ==============================================================
			// fields not yet in the right order!
			// based on (extension of / copy of) the run specific details $finrec_std
			$finrec_act = $finrec_std;
			$finrec_act['Kostendrager']			= $daoDsa->case_country;						// country code main activity
			$finrec_act['Kostenplaats']			= trim(implode(
													array($daoDsa->case_country, $daoDsa->case_sequence, $daoDsa->case_type),
													''));										// project number CCNNNNNT (country, number, type)
			$finrec_act['Sponsorcode']			= $daoDsa->Donor_code;							// sponsor code ("10   " for DGIS, where "600  " would be preferred)
			$finrec_act['OmschrijvingA']		= $daoDsa->last_name;							// description fragment: surname
			$finrec_act['OmschrijvingB']		= $daoDsa->case_sequence;						// description fragment: main activity number
			$finrec_act['OmschrijvingC']		= $daoDsa->case_country;						// description fragment: country
			$finrec_act['FactuurNrYear']		= date('y', strtotime($daoDsa->original_date_time));
																								// 14 for 2014; date of "preparation", not dsa payment! :=> civi: date of original activity
			$finrec_act['FactuurDatum']			= date('d-m-Y', strtotime($daoDsa->original_date_time));
																								// creation date (dd-mm-yyyy) of DSA activity (in Notes 1st save when in status "preparation") :=> civi: date of original activity
			$recSql_DsaCompose['donor_id']		= $daoDsa->Donor_id;							// sponsor id (not sponsor code; store in civicrm_dsa_compose for creditation purposes)

			// payments vs. creditation
			if ($daoDsa->type == '1') {
			
				// payments: create a new invoice number 
				$params = array(
					'version' => 3,
					'q' => 'civicrm/ajax/rest',
					'sequential' => 1,
					'name' => 'invoice_number',
				);
				try {
					$result = civicrm_api('Sequence', 'nextval', $params);
					$nextVal = $result['values'];
				} catch (Exception $e) {
					throw new Exception('Sequence \'invoice_number\' is not available.');
				}
				$finrec_act['FactuurNr']			= $nextVal;									// sequence based: "123456" would return "2345", "12" would return "0001"
	
				$recSql_DsaCompose['invoice_number'] = 
					_dsaSize($finrec_act['FactuurNrRunType'],	1, ' ', TRUE,  FALSE) .			// D for DSA
					_dsaSize($finrec_act['FactuurNrYear'],		2, ' ', TRUE,  FALSE) .			// 14 for 2014; date of "preparation", not dsa payment! :=> civi: date of original activity
					_dsaSize($finrec_act['FactuurNr'],			4, '0', FALSE, FALSE); 			// sequence based: "123456" would return "2345", "12" would return "0001";

			} else {
			
				// creditation: reuse the original invoice number 
				$invoice_number = $daoDsa->invoice_number;
				$finrec_act['FactuurNrRunType'] = substr($invoice_number, 0, 1);				// expected: stored D for DSA
				$finrec_act['FactuurNrYear'] = substr($invoice_number, 1, 2);					// expected stored 14 for 2014
				$finrec_act['FactuurNr']  = substr($invoice_number, -4); 						// expected: stored sequence number
				
			} // payments vs. creditation 
			
			$finrec_act['Kenmerk']				= trim(implode(
													array($daoDsa->case_sequence, $daoDsa->case_country),
													''));										// project number NNNNNCC (number, country)
			$finrec_act['CrediteurNr']			= $daoDsa->Shortname;							// experts shortname (8 char)
			$finrec_act['Shortname']			= $daoDsa->Shortname;							// experts shortname (8 char)
			$finrec_act['NaamOrganisatie']		= trim(implode(
													array( $daoDsa->middle_name, $daoDsa->last_name, $daoDsa->Initials),
													' '));										// experts name (e.g. van Oranje-Nassau W.A.)
			$finrec_act['Taal']					= 'N';											// always "N"
			$finrec_act['Land']					= $country[$daoDsa->country_id]['iso_code'];	// experts country of residence
			$finrec_act['Adres1']				= $daoDsa->street_address;						// extperts street + number
			$finrec_act['Adres2']				= trim(implode(
													array( $daoDsa->postal_code, $daoDsa->postal_code_suffix, $daoDsa->city), 
													' '));										// extperts zip + city
			$finrec_act['BankRekNr']			= ltrim($daoDsa->Bank_Account_Number, '0');		// experts bank account: number (not IBAN)
			$finrec_act['Soort']				= $daoDsa->case_type;							// main activity (case) type (1 character)
			$finrec_act['Rekeninghouder']		= $daoDsa->Accountholder_name;					// bank account holder: name
			$finrec_act['RekeninghouderLand']	= $country[$daoDsa->Accountholder_country]['iso_code'];
																								// bank account holder: country (ISO2)
			$finrec_act['RekeninghouderAdres1']	= $daoDsa->Accountholder_address;				// bank account holder: street + number
			$finrec_act['RekeninghouderAdres2']	= $daoDsa->Accountholder_postal_code . ' '
													.  $daoDsa->Accountholder_city;				// bank account holder: zip + city
			$finrec_act['IBAN']					= $daoDsa->IBAN_nummer;							// bank account: IBAN
			$finrec_act['Banknaam']				= $daoDsa->Bank_Name;							// bank name
			$finrec_act['BankPlaats']			= $daoDsa->Bank_City;							// bank city
			$finrec_act['BankLand']				= $country[$daoDsa->Bank_Country_ISO_Code]['iso_code'];
																								// bank country (ISO2)
			$finrec_act['BIC']					= $daoDsa->BIC_Swiftcode;						// experts bank account: BIC/Swift code
			
			// loop through the individual payment amounts for this activity
			$amt_type = '';
			//for ($n=1; $n<36; $n++) {
			$n = 5; { // (full) representative payment; no 2nd payment; no creditation
				// skip if amount = 0
				// add amount specific details ============================================================
				// fields not yet in the right order: that's handeled by _dsa_concatValues()
				// based on (extension of / copy of) the activity specific details $finrec_act
				
				$finrec_amt = $finrec_act;
				switch ($daoDsa->type) {
					case '1':
						$finrec_amt['DC']				= 'D';									// D for payment
						$finrec_amt['PlusMin']			= '+';									// + for payment
						$finrec_amt['FactuurPlusMin']	= '+';									// + for payment
						break;
					case '2':
						$finrec_amt['DC']				= 'X';									// X for settlement (earlier called 'Debriefing DSA') - the desire to have this option implemented was abandoned while building this extension
						$finrec_amt['PlusMin']			= '-';									// - for creditation
						$finrec_amt['FactuurPlusMin']	= '-';									// - for creditation
						break;
					case '3':
						$finrec_amt['DC']				= 'C';									// C for full creditation (not actually used for representative payments)
						$finrec_amt['PlusMin']			= '-';									// - for creditation
						$finrec_amt['FactuurPlusMin']	= '-';									// - for creditation
						break;
				}
				
				$amt_type = ($n<10?$n:chr($n+55)); // 1='1', 2='2, ... 9='9', 10='A', 11='B', ... 35='Z'
				$case_type = $daoDsa->case_name;
				$gl_key = '';
				switch ($amt_type) {
/*					case '1': // DSA amount
						$amt = _ifnull($daoDsa->amount_dsa, 0);
						$column = 'invoice_dsa';
						switch ($case_type) {
							case 'BLP':
								$gl_key = 'gl_blp';
								break;
							default:
								$gl_key = 'gl_dsa';
						}
						break;
	
					case '3': // Medical (a.k.a. Outfit Allowance)
						$amt = _ifnull($daoDsa->amount_medical, 0);
						$column = 'invoice_medical';
						$gl_key = 'gl_outfit';
						break;
	
					case '4': // Advance amounts (also: Acquisition Advance Amount)
						$amt = _ifnull($daoDsa->amount_advance, 0);
						$column = 'invoice_advance';
						switch ($case_type) {
							case 'BLP':
								$gl_key = 'gl_blp_adv';
								break;
							default:
								$gl_key = 'gl_def_adv';
						}
						break;
*/						
					case '5': // Representative payment
						$amt = _ifnull($daoDsa->amount_rep, 0);
						$column = 'invoice_rep';
						$gl_key = 'gl_lr';
						break;

/*					case '6': // OV PUM briefing/debriefing
						$amt = _ifnull($daoDsa->amount_briefing, 0);
						$column = 'invoice_briefing';
						$gl_key = 'gl_pum_ov_brf_debrf';
						break;
						
					case '7': // OV Airport (was: Km Airport / Schiphol)
						$amt = _ifnull($daoDsa->amount_airport, 0);
						$column = 'invoice_airport';
						$gl_key = 'gl_pum_ov_airp';
						break;
					
					case '8': // Transfer amount
						$amt = _ifnull($daoDsa->amount_transfer, 0);
						$column = 'invoice_transfer';
						$gl_key = 'gl_transfer';
						break;
						
					case '9': // Hotel
						$amt = _ifnull($daoDsa->amount_hotel, 0);
						$column = 'invoice_hotel';
						$gl_key = 'gl_hotel';
						break;
						
					case 'A': // Visa
						$amt = _ifnull($daoDsa->amount_visa, 0);
						$column = 'invoice_visa';
						$gl_key = 'gl_visa';
						break;
					
					case 'B': // Other
						$amt = _ifnull($daoDsa->amount_other, 0);
						$column = 'invoice_other';
						$gl_key = 'gl_other';
						break;
					
					case 'C': // Meal / Parking
						$amt = 0;
						$column = '';
						$gl_key = '';
						break;
					
					case 'D': // Debriefing settlement
						$amt = 0;
						$column = '';
						$gl_key = '';
						break;
					
					case 'X': // Training/BLP payment guest
						$amt = 0;
						$column = '';
						$gl_key = '';
						break;
					
					case 'Y': // Training/BLP payment expert/organisation costs
						$amt = 0;
						$column = '';
						$gl_key = '';
						break;
					
					case 'Z': // Reserved for secondpayment LR remueration
						$amt = 0;
						$column = '';
						$gl_key = '';
						break;
*/						
					default:
						// no action
						$amt = 0;
						$column = '';
						$gl_key = '';
				}
				
				if ($gl_key=='') {
					// no action
				} elseif ($amt==0) {
					//if amount is 0, set status to paid
					$sql = 'UPDATE civicrm_activity SET status_id=' . $statusLst['dsa_paid'] . ' WHERE id=' . $daoDsa->act_id;
					$dao = CRM_Core_DAO::executeQuery($sql);															
				} elseif (!array_key_exists($gl_key, $gl)) {
					// a controlled way out: raise an error causing the code to skip the entire payment record
					throw new Exception ('Unknown key for General Ledger: ' . $gl_key);
				} else {
					// continue construction of payment line
					$finrec_amt['GrootboekNr']		= $gl[$gl_key];								// code from _dsa_generalLedgerCodes() - amount specific
					$finrec_amt['FactuurNrAmtType']	= $amt_type;								// represents type of amount in a single character: 1-9, A-Z
					$finrec_amt['FactuurBedrag']	= round($amt, 2, PHP_ROUND_HALF_UP) * 100;	// payment amount in EUR cents (123,456 -> 12346)
					$finrec_amt['ValutaCode']		= 'EUR';									// always EUR
					$params = array(
						'version' => 3,
						'q' => 'civicrm/ajax/rest',
						'sequential' => 1,
						'name' => 'payment_line',
					);
					try {
						$result = civicrm_api('Sequence', 'nextval', $params);
						$lineNo = $result['values'];
					} catch (Exception $e) {
						throw new Exception('Sequence \'payment_line\' is not available.');
					}
					$finrec_amt['Boekstuk']			= $lineNo;									// Sequence; per amount field
					
					$recSql_DsaCompose[$column] = $amt_type;
					
					// append Fin exportline to array
					$recExportFin[] = _dsa_concatValues($finrec_amt);
				}
			}
			
			// if paymentlines exist in temporary arrays:
			// - update record in civicrm_dsa_compose to store reference to the payment
			// - update status in civicrm_activity
			// - write payment lines to file if payment lines exist
			if (!empty($recExportFin)) {
				// update dsa record
				$recSql_DsaCompose['payment_id'] = $paymentId;
				foreach($recSql_DsaCompose as $key=>$value) {
					$recSql_DsaCompose[$key] = $key . '=\'' . $value . '\'';
				}
				$sql = 'UPDATE civicrm_representative_compose SET ' . implode(',', $recSql_DsaCompose) . ' WHERE id=' . $daoDsa->dsa_id;
				$dao = CRM_Core_DAO::executeQuery($sql);

				// update activity record
				$sql = 'UPDATE civicrm_activity SET status_id=' . $statusLst['dsa_paid'] . ' WHERE id=' . $daoDsa->act_id;
				$dao = CRM_Core_DAO::executeQuery($sql);
				
				// write temp string to file
				foreach($recExportFin as $paymentLine) {
					fwrite($exportFin, $paymentLine . "\r\n");
					$exported = TRUE;
				}
			}

		} // fetch next payable DSA record
	} catch (Exception $e) {
		// an error occurred -> stop processing the current payment record and continue on the next
		$warnings[] = $e->getMessage() . ' (case ' . $daoDsa->case_id . ', activity ' . $daoDsa->act_id . ')';
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
	$sql = 'UPDATE civicrm_dsa_payment SET filename=\'' . $fileName . '\', filesize=' . filesize($fileName) . ', filetype=\'text/plain\', content=\'' . $contentFin . '\' WHERE id=' . $paymentId;
	$dao = CRM_Core_DAO::executeQuery($sql);
	
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
		$subject = 'Representative payment: ' . $fileName;
		$mailfrom = $dsa_config['mail_from'];
		$message = '';
/*
		//$attachment = chunk_split(base64_encode($contentFin));
		// send email
		// http://stackoverflow.com/questions/12301358/send-attachments-with-php-mail (Ragnesh Chauhan)
		try {
			//create a boundary string. It must be unique so we use the MD5 algorithm to generate a random hash
			$random_hash = md5(date('r', time()));
			//define the headers we want passed. Note that they are separated with \r\n
			$headers = "From: " . $mailfrom . "\r\nReply-To: " . $mailfrom;
			//add boundary string and mime type specification
			$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
			//read the atachment file contents into a string, encode it with MIME base64 and split it into smaller chunks
			$attachment = chunk_split(base64_encode(file_get_contents($fileName))); // ?????????????????????????????????
			//define the body of the message.
			ob_start(); //Turn on output buffering
			?>
--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"

--PHP-alt-<?php echo $random_hash; ?> 
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

DSA Payments file:

--PHP-alt-<?php echo $random_hash; ?>--

--PHP-mixed-<?php echo $random_hash; ?> 
Content-Type: application/txt; name="<?php echo $fileName; ?>" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

<?php echo $attachment; ?>
--PHP-mixed-<?php echo $random_hash; ?>--
<?php
			//copy current buffer contents into $message variable and delete current output buffer
			$message = ob_get_clean();
			//send the email
*/
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
  
  $msg_prefix = 'Processing representative payments - ';
  foreach($warnings as $msg) {
	CRM_Core_Error::debug_log_message($msg_prefix . $msg);
  }
//dpm($warnings, 'Warnings'); // should be handled differently when running as scheduled job ==================================================

  // update roster for next run
  $params = array(
	'version' => 3,
	'q' => 'civicrm/ajax/rest',
	'sequential' => 1,
	'name' => $roster,
  );
  $result = civicrm_api('Roster', 'ScheduleNext', $params);
  if ($result['values']!=1) {
    $msg = 'Processing representative payments - failed to schedule next run: check roster ' . $roster;
	CRM_Core_Error::debug_log_message($msg);
	throw new Exception($msg);
  }
  
  // to do:
  // - overall payment details
  // - message per expert
  
  
}


/*
 * Function to query for payable dsa records along with the experts' details
 * Returns dao-object from which records can be fetched
 */
function _dao_retrievePayableRepresentatives($statusLst) {
	// tables/columns definitions for custom fields for contact type Staff (e.g. Representative)
	// custom data table and columns are added to the basic query to fetch payment details along with the representatives' details
	$tbl = array(
		'Additional_Data'	=> _getCustomTableInfo('Additional_Data'),	// contains field "Initials"
		'Bank_Information'	=> _getCustomTableInfo('Bank_Information'),	// contains several fields related to bankaccounts
		'Donor_details_FA'  => _getCustomTableInfo('Donor_details_FA'),	// contains donor (sponsor) code
		'sponsor_code'      => _getCustomTableInfo('sponsor_code'),	// contains donor (sponsor) code
	);
		
	// query for all active Representative payment activities in status Payable
	// the original limitation skip payments when the cases end date is exceeded by 9 mnd + 7 days is NOT included
	$sql = '
SELECT
      \'--META-->\' AS \'_META\',
      cac.case_id AS case_id,
      act.id AS act_id,
      con.id AS participant_id,
      dsa.approval_cid AS approver_id,
      dsa.id AS dsa_id,
      \'--CASE-->\' AS \'_CASE\',
      ovl3.name AS case_name,
      num.case_sequence,
      num.case_type,
      num.case_country,
      \'--DONOR-->\' AS \'_DONOR\',
      dnr.id AS \'Donor_id\',
      dnr.display_name AS donor_name,
      ' . $tbl['Donor_details_FA']['sql_columns'] . ',
      \'--ACTIVITY-->\' AS \'_ACTIVITY\',
      act.*,
      \'--ORG_ACTIVITY-->\' AS \'_ORG_ACTIVITY\',
      org.activity_date_time AS original_date_time,
      \'--REP-->\' AS \'_REP\',
      dsa.type,
      dsa.amount_rep,
      dsa.approval_datetime,
      dsa.invoice_number,
      \'--APPROVER-->\' AS \'_APPROVER\',
      apr.display_name AS approver_name,
      \'--PARTICIPANT->\' AS \'_PARTICIPANT\',
      con.first_name,
      con.middle_name,
      con.last_name,
      con.prefix_id,
      con.suffix_id,
      con.email_greeting_id,
      con.email_greeting_custom,
      con.sort_name,
      con.display_name,
      con.legal_name,
      \'--ADDRESS-->\' AS \'_ADDRESS\',
      adr.street_address,
      adr.supplemental_address_1,
      adr.supplemental_address_2,
      adr.supplemental_address_3,
      adr.city,
      adr.country_id,
      adr.state_province_id,
      adr.postal_code_suffix,
      adr.postal_code,
      adr.usps_adc,
      \'--ADDITIONAL_DATA-->\' AS \'_ADDITIONAL_DATA\',
      ' . $tbl['Additional_Data']['sql_columns'] . ',
	  \'--BANK-->\' AS \'_BANK\',
      ' . $tbl['Bank_Information']['sql_columns'] . ',
	  \'--END--\' AS \'_END\'
FROM
      civicrm_activity                 act,
      civicrm_activity                 org,
      civicrm_case_activity            cac,
      civicrm_case                     cas
      LEFT JOIN civicrm_case_pum       num
        ON num.entity_id = cas.id           /* join main activity (case) numbering */
      LEFT JOIN civicrm_donor_link     dlk
        ON dlk.entity_id = cas.id AND
           dlk.entity = \'Case\' AND
           dlk.is_fa_donor = 1
      LEFT JOIN civicrm_contribution   ctr
        ON ctr.id = dlk.donation_entity_id
      LEFT JOIN civicrm_contact        dnr
        ON dnr.id = ctr.contact_id          /*civicrm_value_case_sponsor_code.sponsor*/
      LEFT JOIN ' . $tbl['Donor_details_FA']['group_table'] . '
        ON ' . $tbl['Donor_details_FA']['group_table'] . '.entity_id = dnr.id /* join case donor to case activity */
      ,
      civicrm_representative_compose   dsa
      LEFT JOIN civicrm_contact        con
        ON con.id = dsa.contact_id
      LEFT JOIN civicrm_address        adr
        ON adr.contact_id = con.id
       AND adr.is_primary = 1
      LEFT JOIN civicrm_contact        apr
        ON apr.id = dsa.approval_cid
      LEFT JOIN ' . $tbl['Additional_Data']['group_table'] . '
        ON ' . $tbl['Additional_Data']['group_table'] . '.entity_id = con.id /* additional custom data for contact */
      LEFT JOIN ' . $tbl['Bank_Information']['group_table'] . '
        ON ' . $tbl['Bank_Information']['group_table'] . '.entity_id = con.id /* additional custom data for bank information */
      ,
      civicrm_option_group             ogp3,
      civicrm_option_value             ovl3
WHERE
      act.is_current_revision = 1
  AND dsa.activity_id = ifnull(act.original_id, act.id)
  AND org.id = ifnull(act.original_id, act.id)
  AND act.activity_type_id IN (
         SELECT
               ovl1.value
         FROM
               civicrm_option_group ogp1,
               civicrm_option_value ovl1
         WHERE
               ogp1.name = \'activity_type\'
           AND ovl1.option_group_id = ogp1.id
           AND ovl1.name = \'Representative payment\'
         )
  AND act.status_id IN (
         SELECT
               ovl2.value
         FROM
               civicrm_option_group ogp2,
               civicrm_option_value ovl2
         WHERE
               ogp2.name = \'activity_status\'
           AND ovl2.option_group_id = ogp2.id
           AND ovl2.name = \'dsa_payable\'
         )
  AND cac.activity_id = act.id
  AND cas.id = cac.case_id /* join case data through case activities */
  AND ogp3.name = \'case_type\'
  AND ovl3.option_group_id = ogp3.id
  AND ovl3.value = cas.case_type_id /* join case type name */
ORDER BY
  con.id,
  num.case_sequence
	';
	
//dpm($tbl, 'Details custom tables');
//dpm(array($sql), 'Representative Payment $sql');
  
	$dao = CRM_Core_DAO::executeQuery($sql);
	
	return $dao;
}
