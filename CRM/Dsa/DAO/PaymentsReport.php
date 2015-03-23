<?php

class CRM_Dsa_DAO_PaymentsReport extends CRM_Core_DAO {
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  static $_export = null;
  /**
   * empty definition for virtual function
   */
  static function getTableName() {
    return 'civicrm_dsa_payment';
  }
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields() {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true
        ) ,
        'timestamp' => array(
          'name' => 'timestamp',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
        ) ,
        'filename' => array(
          'name' => 'filename',
          'type' => CRM_Utils_Type::T_TEXT,
        ),
        'filesize' => array(
          'name' => 'filesize',
          'type' => CRM_Utils_Type::T_INT,
        ),
        'filetype' => array(
          'name' => 'filetype',
          'type' => CRM_Utils_Type::T_TEXT,
        ),
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the array key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys() {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'timestamp' => 'timestamp',
        'filename' => 'filename',
        'filesize' => 'filesize',
        'filetype' => 'filetype',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * returns the list of fields that can be exported
   *
   * @access public
   * return array
   * @static
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['activity'] = & $fields[$name]; // ??
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}