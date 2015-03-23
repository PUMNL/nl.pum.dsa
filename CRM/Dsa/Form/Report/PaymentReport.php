<?php


class CRM_Dsa_Form_Report_PaymentReport extends CRM_Report_Form {

  /**
   * constructor function
   */
  function __construct() {
	$this->_columns = array(
		'civicrm_dsa_payment' => array(
			'dao' => 'CRM_Dsa_DAO_PaymentsReport',
			'fields' => array(
				'id' => array(
					'title' => ts('id'),
					'name' => 'id',
					'alias' => 'payment_id',
					'dbAlias' => 'id',
					'no_display' => TRUE,
					'default' => TRUE,
					'required' => TRUE,
				),
				'timestamp' => array(
					'title' => ts('timestamp'),
					'name' => 'timestamp',
					'alias' => 'payment_timestamp',
					'dbAlias' => 'timestamp',
					'default' => TRUE,
				),
				'filename' => array(
					'title' => ts('file name'),
					'name' => 'filename',
					'alias' => 'payment_filename',
					'dbAlias' => 'filename',
					'default' => TRUE,
					'required' => TRUE,
				),
				'filesize' => array(
					'title' => ts('file size'),
					'name' => 'filesize',
					'alias' => 'payment_filesize',
					'dbAlias' => 'filesize',
					'default' => FALSE,
				),
				'filetype' => array(
					'title' => ts('type'),
					'name' => 'filetype',
					'alias' => 'payment_filetype',
					'dbAlias' => 'filetype',
					'default' => FALSE,
				),
			),
			'filters' => array(
				'timestamp' => array(
					'title' => ts('timestamp'),
					'type' => CRM_Utils_Type::T_DATE,
					'operatorType' => CRM_Report_Form::OP_DATE,
				),
			),
			'order_bys' => array(
				'timestamp' => array(
					'title' => ts('timestamp'),
					'name' => 'timestamp',
					'default' => TRUE,
					'default_order' => 'DESC',
				),
			),
		),
	);
    parent::__construct();
  }

  /**
   * select function
   */
  function select($recordType = NULL) {
	$select = $this->_columnHeaders = array( );
	foreach ( $this->_columns as $tableName => $table ) {
		if ( array_key_exists('fields', $table) ) {
			foreach ( $table['fields'] as $fieldName => $field ) {
				if (
					CRM_Utils_Array::value( 'required', $field ) ||
					CRM_Utils_Array::value( $fieldName, $this->_params['fields'] )
				) {
					$select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
 
					// initializing columns as well
					$this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value( 'type', $field );
					$this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
				}
			}
		}
	}
	$this->_select = "SELECT " . implode( ', ', $select ) . " ";
  }

  /**
   * from function
   */
  function from($recordType = NULL) {
	$this->_from = "FROM civicrm_dsa_payment {$this->_aliases['civicrm_dsa_payment']}";
  }  

  /**
   * from function
   */
  function where() {
    $clauses = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table)) {
        foreach ($table['filters'] as $fieldName => $field) {
          $clause = NULL;
          if (CRM_Utils_Array::value('operatorType', $field) & CRM_Utils_Type::T_DATE) { // no other operator types currently in use
            $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
            $from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
            $to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

            $clause = $this->dateClause($field['name'], $relative, $from, $to, $field['type']);
          }

          if (!empty($clause)) {
            $clauses[] = $clause;
          }
        }
      }
    }
    if (empty($clauses)) {
      $this->_where = "WHERE (1)";
    } else {
      $this->_where = "WHERE " . implode(' AND ', $clauses);
    }
  }
  
  /**
   * postProcess function
   */
  function postProcess() {
     parent::postProcess( );
  }
 
  /**
   * alterDisplay function
   * apply modifications to the bare report format
   */
  function alterDisplay(&$rows) {
	$allowDownload = CRM_Core_Permission::check('download payments');
	if ($allowDownload) {
		foreach ($rows as $rowNum => $row) {
			$this->_alterFilename($row, $rowNum, $rows);
		}
    }
  }
  
  /**
   * _alterFilename function
   * adds a download link to the filename-column and a tool tip when hovering over the link
   */
  private function _alterFilename($row, $rowNum, &$rows) {
	$tbl_prefix = 'civicrm_dsa_payment_';
	$column_id = $tbl_prefix . 'id';
	$column_filename = $tbl_prefix . 'filename';
	if (array_key_exists($column_id, $row) && array_key_exists($column_filename, $row)) {
		$url = CRM_Utils_System::url('civicrm/downloadpayments', 'payment=' . $row[$column_id], $this->_absoluteUrl);
		$rows[$rowNum][$column_filename . '_link'] = $url;
		$rows[$rowNum][$column_filename . '_hover'] = ts('Download') . ' ' . $row[$column_filename];
    }
  }
  
}
