<?php

require_once 'CRM/Core/Page.php';

class CRM_Dsa_Page_DSAImport extends CRM_Core_Page {
	protected $_action = '';
	
	function run() {
		// define action (url parameter action)
		if ($_GET['action']) {
			$_action = htmlspecialchars($_GET['action']);
		} else {
			$_action =  '';
		}
		$_action = $this->_validateAction($_action);
		$this->assign('action', $_action);
		$this->assign('user_action', '');
		
		if ($_action=='result') {
			// process uploads
			$user_action = $this->_getUploadType(); // 'upload' or 'convert'
			$this->assign('user_action', $user_action);
			
			if ($this->_validateUpload()) {
				// input ok
				if ($user_action == 'convert') {
					if (!$this->_convertFiles()) {
						// import failure: redir back to ?action=convert
						$_action = $this->_validateAction('convert');
						$this->assign('action', $_action);
					} else {
						$this->assign('csv', $this->_buildCSVdata());
					}
				} elseif ($user_action == 'upload') {
					if (!$this->_importFile()) {
						// import failure: redir back to ?action=upload
						$_action = $this->_validateAction('upload');
						$this->assign('action', $_action);
					}
				} else {
					// post from an unknown / unsupported action
					$_action = $this->_validateAction('read');
					$this->assign('action', $_action);
				}
			} else {
				// validation failed
				// back to upload screen if possible or to ?action=read if not
				// how? -----------------------------------------------------------------------------------------
				if (($user_action == 'convert')
				|| ($user_action == 'upload')) {
					$_action = $this->_validateAction('$user_action');
					$this->assign('action', $_action);
				} else {
					$_action = $this->_validateAction('read');
					$this->assign('action', $_action);
				}
			}
		}
		
		if ($_action=='convert') {
		}
		
		if ($_action=='upload') {
		}
		
		if ($_action=='read') {
//			$country = 'ZA';
//			$dt = '2014-04-07'; // date('Y-m-d', time());
//			$result = $this->getActiveCountryRates('ZA', $dt);
//			dpm($result, 'active country rates for "'. $country . '"');
		}
		
		// for all actions: collect existing dsa batches
		$this->assign('dsaBatch', $this->_readExistingBatches());
		
		// for all actions: define labels etc. for use on page
		$this->assign('labels', $this->_setLabels($_action));
		
		CRM_Utils_System::setTitle(ts('DSA import'));
		
		parent::run();
	}
	
	/**
	 * Function to validate data uploaded for conversion
	 */
	private function _validateUpload() {
		$result = false;
		$validationResults = array();
		if (empty($_REQUEST)) {
			$validationResults[] = ts('No data received.');
		} else {
			//dpm($_REQUEST, 'REQUEST'); // -------------------------------------------------------
			//dpm($_FILES, 'FILES'); // -----------------------------------------------------------
			$user_action = $this->_getUploadType();
			if ($user_action == '') {
				$validationResults[] = ts('No "user_action" provided in submitted data -> cannot validate.');
			} elseif ($user_action=='convert') {
				// validate uploaded files
				if (count($_FILES)<>2) {
					$validationResults[] = ts('A two files upload is expected.');
				} else {
					foreach($_FILES as $value=>$file) {
						if ($file['type'] <> 'text/plain') {
							$validationResults[] = ts('File upload for ' . $value . ' should be ".txt"-format.');
						} elseif ($file['error'] <> 0) {
							$validationResults[] = ts('File upload for ' . $value . ' failed.');
						}
					}
				}				
			} elseif ($user_action == 'upload') {
				// validate uploaded file
				if (count($_FILES)<>1) {
					$validationResults[] = ts('A single file upload is expected.');
				} else {
					foreach($_FILES as $value=>$file) {
						if (($file['type'] <> 'text/plain') 
						and ($file['type'] <> 'text/csv')
						and ($file['type'] <> 'application/vnd.ms-excel')) {
							$validationResults[] = ts('File upload for ' . $value . ' should be ".txt" or ".csv"-format.');
						} elseif ($file['error'] <> 0) {
							$validationResults[] = ts('File upload for ' . $value . ' failed.');
						}
					}
				}
				// validate activation date // --------------------------------------------------------
				// ...
			} else {
				$validationResults[] = ts('Unsupported "user_action" provided in submitted data -> cannot validate.');
			}
		}
		if (count($validationResults)>0) {
			foreach($validationResults as $msg) {
				//CRM_Core_Error::Debug($msg);  // ------- beautify / warning / hook_civicrm_validateForm ? -----------
				CRM_Core_Session::setStatus($msg, 'alert');
			}
			$result = false;
		} else {
			$result = true;
		}
		return $result;
	}
	
	/**
	 * Function to return the type of upload
	 * typically 'upload' or 'convert'
	 */
	private function _getUploadType() {
		if (in_array('user_action', $_REQUEST)) {
			return '';
		} else {
			return strtolower($_REQUEST['user_action']);
		}
	}
	
	/**
	 * Function to retrieve existing (earlier imported) batches
	 */
    private function _readExistingBatches() {
		$params = array(
			'version' => 3,
			'sequential' => 1,
		);
		$result = civicrm_api('DsaBatch', 'get', $params);
		return $result;
	}
	
	/**
	 * Function to set labels for page fields
	 */
    private function _setLabels($_action) {
		// general
		$labels['date_format'] = CRM_Utils_Date::getDateFormat();
	
		// section regarding earlier imports
		$labels['existing']['header'] = ts('This is where earlier imports will be displayed');
		$labels['existing']['importdate'] = ts('Import Date');
		$labels['existing']['startdate'] = ts('Start Date');
		$labels['existing']['enddate'] = ts('End Date');
		$labels['existing']['rate'] = ts('Rate (EUR)');
		
		// section regarding new import
		if ($_action=='convert') {
			$labels['upload']['header'] = ts('This is where 2 UN DSA files get converted to CSV');
		} else {
			$labels['upload']['header'] = ts('This is where a new DSA file is imported');
		}
		$labels['upload']['convert_info'] = array(
			ts('Expected is 1 file, txt or csv, containing 4 ";"-separated columns:'),
			ts('- country (2 character ISO)'),
			ts('- UN location code (is ignored)'),
			ts('- location name'),
			ts('- DSA rate (in EUR)'),
			ts('The presence of 1 header line is assumed (1st line is skipped).')
		);
		$labels['upload']['results_info_convert'] = ts('Copy/paste the following lines to e.g. MS Excel for further processing.');
		$labels['upload']['results_info_report'] = ts('Summary:');
		$labels['upload']['results'] = ts('Results');
		$labels['upload']['file_dsa'] = ts('DSA file');
		$labels['upload']['file_rates'] = ts('Rates file');
		$labels['upload']['file_locations'] = ts('Locations file');
		$labels['upload']['activation_date'] = ts('Activation date');
		$labels['upload']['mandatory'] = ts('This field is required.');
		
		return $labels;
	}
	
	/**
	 * Function to validate and control the requested action
	 */
    private function _validateAction($request) {
		$request = strtolower($request);
		$validActions = array('convert', 'upload', 'result', 'read');
		if (!in_array($request, $validActions)) {
			$request = 'read';
		}
		return $request;
	}
	
	/**
	 * Function to process uploaded locations and rates files
	 */
	private function _convertFiles() {
		$result = true;
		$runTime = time();
		$fileNameRates = variable_get('file_private_path', conf_path() . '/files/private').'/dsarates'.date("Ymd_His", $runTime).'.txt';
		$fileNameLocations = variable_get('file_private_path', conf_path() . '/files/private').'/dsalocations'.date("Ymd_His", $runTime).'.txt';

		try {
			if (file_exists($fileNameRates)) {
				unlink($fileNameRates); // delete file
			}
			if (file_exists($fileNameRates)) {
				//if file still exists
				CRM_Utils_System::setUFMessage('Could not delete existing DSA rates file: '.$fileNameRates.'. Please check file permissions.');
				$result = FALSE;
				return $result;
			}

			if (file_exists($fileNameLocations)) {
				unlink($fileNameLocations); // delete file
			}
			if (file_exists($fileNameLocations)) {
				//if file still exists
				CRM_Utils_System::setUFMessage('Could not delete existing DSA locations file: '.$fileNameLocations.'. Please check file permissions.');
				$result = FALSE;
				return $result;
			}

			foreach($_FILES as $value=>$file) {
				if($value == 'file_locations') {
					move_uploaded_file($_FILES[$value]['tmp_name'], $fileNameLocations);
				} elseif ($value == 'file_rates') {
					move_uploaded_file($_FILES[$value]['tmp_name'], $fileNameRates);
				}
			}

			// empty conversion table
			$sql = 'TRUNCATE TABLE civicrm_dsa_convert';
			$dao = CRM_Core_DAO::executeQuery($sql);
			
			// import both uploaded files
			// file #1: locations
			// note: country code is a non-ISO UN format (ICSC) - check civicrm_country_pum
			
			$fileLocations=fopen($fileNameLocations, 'r');
			if (!$fileLocations) {
				CRM_Utils_System::setUFMessage('Could not open file "' . $fileNameLocations . '".');
			} else {
				$num = 0;
				while (!feof($fileLocations)) {
					$line = fgets($fileLocations);
					$num++;
					switch(strtoupper(substr($line, 0, 5))) {
						case 'HEADR':
							// skip header line
							break;
						case 'TRAIL':
							// skip trailer line
							break;
						case '':
							// skip empty line
							break;
						default:
							// line to be processed
							$locCode = trim(substr($line, 0, 4));
							$locCountry = trim(substr($line, 4, 3));
							$locExpiry = substr($line, 42, 10); // mm/dd/yyyy
							$locName = trim(substr($line, 52, 60));
							if (strpos($locName, '\'')>-1) {
								$locName = str_ireplace('\'', '\\\'', $locName);
							}
							$dt_parts = explode('/', $locExpiry);
							$dt_exp = date_create();
							date_date_set($dt_exp, $dt_parts[2], $dt_parts[0], $dt_parts[1]);
							if (date_format($dt_exp, 'Y-m-d') < date('Y-m-d')) {
								// expired
							} else {
								$sql = 'INSERT INTO civicrm_dsa_convert SET country=\'' . $locCountry . '\', code=\'' . $locCode . '\', location=\'' . $locName . '\'';
								$dao = CRM_Core_DAO::executeQuery($sql);
							}
					}
				}
				fclose($fileLocations);
			}

			// #2: rates
			$fileRates=fopen($fileNameRates, 'r');
			if (!$fileRates) {
				CRM_Utils_System::setUFMessage('Could not open file "' . $fileNameRates . '".');
			} else {
				$num = 0;
				$num2 = 0;
				while (!feof($fileRates)) {
					$line = fgets($fileRates);
					$num++;
					switch(strtoupper(substr($line, 0, 5))) {
						case 'HEADR':
							// skip header line
							break;
						case 'TRAIL':
							// skip trailer line
							break;
						case '':
							// skip empty line
							break;
						default:
							// line to be processed
							$ratePeriod = trim(substr($line, 7, 1));
							if ($ratePeriod<>'1') {
								// skip rates for visits over 30 days
							} else {
								// process rates for 1 to 30 days visits
								$num2++;
								$rateCode = trim(substr($line, 0, 4));
								$rateCountry = trim(substr($line, 4, 3));
								$rateAmount = trim(substr($line, 8, 10)); // is always INT
								$sql = 'UPDATE civicrm_dsa_convert SET rate=' . $rateAmount . ' WHERE country=\'' . $rateCountry . '\' AND code=\'' . $rateCode . '\'';
								$dao = CRM_Core_DAO::executeQuery($sql);
							}
					}
				}
				fclose($fileRates);
			}
			
		} catch(CiviCRM_API3_Exception $e) {
			$result = false;
		}

    try {
		  unlink($fileNameLocations);
		  unlink($fileNameRates);
    } catch(Exception $e) {
      CRM_Utils_System::setUFMessage('Could not delete DSA import files: '.$fileNameLocations.', '.$fileNameRates.'. Please check file permissions.');
    }

		return $result;
	}
	
	/**
	 * Function to convert data in civicrm_dsa_convert to csv format
	 */
	private function _buildCSVdata() {
		$txt = 'Country;Code;Location;USD rate' . PHP_EOL;
		//$sql = 'SELECT country, code, location, rate FROM civicrm_dsa_convert ORDER BY country, location';
		$sql = 'SELECT code.ISO2 country, data.code, data.location, data.rate FROM civicrm_dsa_convert data, civicrm_country_pum code WHERE code.ICSC = data.country ORDER BY country, location';
		$dao = CRM_Core_DAO::executeQuery($sql);
		while ($dao->fetch()) {
			$txt .= $dao->country . ';' . $dao->code . ';' . $dao->location . ';' . $dao->rate . PHP_EOL;
		}
		return $txt;
	}
	
	/**
	 * Function to process uploaded locations and rates files
	 */
	private function _importFile() {
		$result = true;
		$runTime = time();

		$fileName = variable_get('file_private_path', conf_path() . '/files/private').'/dsaimport_' . date("Ymd_His", $runTime) . '.txt';

		try {
			$date = $_REQUEST['activation_date'];
			if ($date=='') {
				$date='NULL';
			} else {
				// strangely: date is still formatted dd-mm-yyyy ----------------------------------------------------
				$date = explode('-', $date);
				$date = new DateTime($date[2] . '-' . $date[1] . '-'. $date[0]);
				$date = '\'' . $date->format('Y-m-d') . '\'';
			}
			$creation = date('Y-m-d H:i:s');
			foreach($_FILES as $value=>$file) {
				if (file_exists($fileName)) {
					unlink($fileName); // delete file
				}
				if (file_exists($fileName)) {
					//if file still exists
					CRM_Utils_System::setUFMessage('Could not delete existing DSA file: '.$fileName.'. Please check file permissions.');
					return FALSE;
				}
				move_uploaded_file($_FILES[$value]['tmp_name'], $fileName);
			}

			// if activationdate is provided, close all open batches on that same day (but what if multiple batches open on the same day?)
			// ...

			$file=fopen($fileName, 'r');
			if (!$file) {
				CRM_Utils_System::setUFMessage('Could not open file "' . $fileName . '".');
			} else {
				// create new batch
				$sql = 'INSERT INTO civicrm_dsa_batch (importdate, startdate) VALUES (\'' . $creation . '\', ' . $date . ')';
				$dao = CRM_Core_DAO::executeQuery($sql);
				// retrieve batch id
				$sql = 'SELECT id FROM civicrm_dsa_batch WHERE importdate=\'' . $creation . '\'';
				$dao = CRM_Core_DAO::executeQuery($sql);
				$dao->fetch();
				$batch_id = $dao->id;
				// import uploaded file (in EUR!)
				// important: country code is a non-ISO UN format (ICSC) - check civicrm_country_pum
			
				
				$elmCountry = 0;
				$elmCode = 1;
				$elmLocation = 2;
				$elmRate = 3;
				
				$num_line = 0;
				$num_count = 0;
				
				while (!feof($file)) {
					$line = fgets($file);
					$num_line ++;
					if ($num_line == 1) {
						// skip header line
					} elseif ($line == '') {
						// skip empty line
					} else {
						// line to be processed
						$data = explode(';', $line);
						
						if (strpos($data[$elmLocation], '\'')>-1) {
							$data[$elmLocation] = str_ireplace('\'', '\\\'', $data[$elmLocation]);
						}
						
						if (strpos($data[$elmRate], ',')>-1) {
							$data[$elmRate] = str_ireplace(',', '.', $data[$elmRate]);
						}
						
						$sql = 'INSERT INTO civicrm_dsa_rate SET batch_id=' . $batch_id . ', country=\'' . $data[$elmCountry] . '\', code=\'' . $data[$elmCode] . '\', location=\'' . $data[$elmLocation] . '\', rate=' . $data[$elmRate];
						$dao = CRM_Core_DAO::executeQuery($sql);
						$num_count ++;
					}
				}
				fclose($file);
			}
		} catch(CiviCRM_API3_Exception $e) {
			$result = false;
		}
		$report['batch_id'] = $batch_id;
		$report['num_lines'] = $num_line;
		$report['num_imported'] = $num_count;
		$this->assign('report', $report);
		return $result;
	}
	
	/**
	 * Function to retrieve all imported DSA batches
	 * Handler for API DsaBatch Get
	 */
	public static function getAllDSABatches() {
		$result = array();
		$dao = CRM_Core_DAO::executeQuery("SELECT * FROM civicrm_dsa_batch ORDER BY importdate DESC");
		while ($dao->fetch()) {
			$result[$dao->id] = self::_daoToArray($dao);
		}
		return $result;
	}
	
	/**
	 * Function to retrieve active DSA batch
	 */
	public static function getActiveDSABatch($dt=NULL) {
		$result = array();
		self::_dateDefault($dt);
		//$sql = "SELECT * FROM civicrm_dsa_batch WHERE startdate <= DATE( NOW()) AND (enddate IS NULL OR enddate > DATE( NOW())) ORDER BY startdate DESC, importdate DESC";
		$sql = "SELECT * FROM civicrm_dsa_batch WHERE startdate <= DATE('" . $dt . "') AND (enddate IS NULL OR enddate > DATE('" . $dt . "')) ORDER BY startdate DESC, importdate DESC";
//		dpm($sql, "sql");
		$dao = CRM_Core_DAO::executeQuery($sql);
		if ($dao->N == 0) {
			CRM_Core_Session::setStatus('No active DSA batches found!: '.$dt, 'Warning');
			return null;
		} elseif ($dao->N > 1) {
			CRM_Core_Session::setStatus($dao->N . ' Active DSA batches found!' . PHP_EOL . '(Proceeding using the most recently imported one)', 'Warning');
		}
		$dao->fetch();
		$result = self::_daoToArray($dao);
		return $result;
	}
	
	
	/**
	 * Function to all active locations/rates for all known countries
	 * Parameter represents a reference date.
	 * Used to bypass API permission problem in JS
	 */
	public static function getAllActiveRatesByDate($dt=NULL) {
		$result = array();
		self::_dateDefault($dt);
		$active_batch = self::getActiveDSABatch($dt);
//		dpm($active_batch, "Active batch");
		if (is_null($active_batch)) {
			return null;
		} else {
			$sql = 'SELECT c.id AS country_id,  r.country, r.id, r.rate, r.location, \'' . $dt . '\' AS ref_date FROM civicrm_dsa_rate r, civicrm_country c WHERE c.iso_code=r.country AND r.batch_id=' . $active_batch['id'] . ' ORDER BY country_id, location';
			$dao = CRM_Core_DAO::executeQuery($sql);
			$result = array();
			$recno = 0;
			while ($dao->fetch()) {
				$recno++;
				if ($recno==1) {
					$result['ref_date'] = $dao->ref_date;
					$result['countries'] = array();
				}
				if (!array_key_exists($dao->country_id, $result['countries'])) {
					$result['countries'][$dao->country_id] = array();
					$result['countries'][$dao->country_id]['iso'] = $dao->country;
					$result['countries'][$dao->country_id]['locations'] = array();
				}
				$result['countries'][$dao->country_id]['locations'][] = array(
					'id' => $dao->id,
					'location' => $dao->location,
					'rate' => $dao->rate,
				);
			}
			return $result;
		}
	}
	
	
	/**
	 * Function to collect all locations/rates for all known countries.
	 * Parameter represents a location id.
	 * The actual location set returned is the one containing the provided id
	 * This function will NOT return a ref_date
	 * Used to bypass API permission problem in JS
	 */
	public static function getAllRatesByLocationId($id=NULL) {
		if ((is_null($id)) || ($id=='') || ($id==0)) {
			// if no location id was provided, retrieve the list based on todays date
			return self::getAllActiveRatesByDate();
		} else {
			$result = array();
			$sql = 'SELECT c.id AS country_id, r2.country, r2.id, r2.rate, r2.location FROM civicrm_dsa_rate r2, civicrm_country c WHERE  r2.batch_id = (SELECT r1.batch_id FROM civicrm_dsa_rate r1 WHERE r1.id = ' . $id . ') AND c.iso_code = r2.country';
			$dao = CRM_Core_DAO::executeQuery($sql);
			$recno = 0;
			while ($dao->fetch()) {
				$recno++;
				if ($recno==1) {
					//$result['ref_date'] = $dao->ref_date;
					$result['countries'] = array();
				}
				if (!array_key_exists($dao->country_id, $result['countries'])) {
					$result['countries'][$dao->country_id] = array();
					$result['countries'][$dao->country_id]['iso'] = $dao->country;
					$result['countries'][$dao->country_id]['locations'] = array();
				}
				$result['countries'][$dao->country_id]['locations'][] = array(
					'id' => $dao->id,
					'location' => $dao->location,
					'rate' => $dao->rate,
				);
			}
			return $result;
		}
	}
	

	/**
	 * Function to retrieve active rates for a specified country
	 * API: DsaBatch -> get
	 */
	public static function getActiveCountryRates($country, $dt=NULL) {
		$result = array();
		self::_dateDefault($dt);
		$active_batch = self::getActiveDSABatch($dt);
//		dpm($active_batch, "Active batch");
		if (is_null($active_batch)) {
			return null;
		} else {
			if (is_numeric($country)) {
				$sql = 'SELECT r.*, \'' . $dt . '\' AS ref_date FROM civicrm_dsa_rate r, civicrm_country c WHERE c.iso_code=r.country AND r.batch_id=' . $active_batch['id'] . ' AND c.id=' . $country;
			} else {
				$sql = 'SELECT *, \'' . $dt . '\' AS ref_date FROM civicrm_dsa_rate WHERE country=\'' . $country . '\' AND batch_id=' . $active_batch['id'];
			}
			$dao = CRM_Core_DAO::executeQuery($sql);
			while ($dao->fetch()) {
				$result[$dao->id] = self::_daoToArray($dao);
			}
			return $result;
		}
	}
	
	/**
	 * Function to set null dates to today
	 */
	private static function _dateDefault(&$dt) {
		if (is_null($dt)) {
			$dt = date('Y-m-d');
		}
	}
	
	/**
	 * Function to populate array with dao
	 * !: curently only built to support columns from civicrm_dsa_batch
	 */
    private static function _daoToArray($dao) {
		$result = array();
		if (empty($dao)) {
			return $result;
		}
		if (isset($dao->id)) {
			$result['id'] = $dao->id;
		}
		if (isset($dao->batch_id)) {
			$result['batch_id'] = $dao->batch_id;
		}
		if (isset($dao->importdate)) {
			$result['importdate'] = $dao->importdate;
		}
		if (isset($dao->startdate)) {
			$result['startdate'] = $dao->startdate;
		}
		if (isset($dao->enddate)) {
			$result['enddate'] = $dao->enddate;
		}
		if (isset($dao->usd_to_eur)) {
			$result['eur'] = $dao->eur;
		}
		if (isset($dao->country)) {
			$result['country'] = $dao->country;
		}
		if (isset($dao->country_id)) {
			$result['country_id'] = $dao->country_id;
		}
		if (isset($dao->code)) {
			$result['code'] = $dao->code;
		}
		if (isset($dao->location)) {
			$result['location'] = $dao->location;
		}
		if (isset($dao->rate)) {
			$result['rate'] = $dao->rate;
		}
		if (isset($dao->ref_date)) {
			$result['date'] = $dao->ref_date;
		}
		if (isset($dao->concat)) {
			$result['concat'] = $dao->concat;
		}
		return $result;
    }
}
