<?php
class CRM_Dsa_BAO_DSAManagersOperations extends CRM_Dsa_DAO_DSAManagersOperations {
  /**
   * Function to get values
   *
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params=array()) {
    $result = array();
    $DSAManagersOperations = new CRM_Dsa_BAO_DSAManagersOperations();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $key => $value) {
        if (isset($fields[$key])) {
          $DSAManagersOperations->$key = $value;
        }
      }
    }
    $DSAManagersOperations->find();
    while ($DSAManagersOperations->fetch()) {
      $row = array();
      CRM_Core_DAO::storeValues($DSAManagersOperations, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }
  /**
   * Function to add or update dsa manager operations
   *
   * @param array $params
   * @return array $result
   * @access public
   * @throws Exception when params is empty
   * @static
   */
  public static function add($params) {
    $result = array();

    if (empty($params)) {
      throw new Exception('Params can not be empty when adding or updating a Dsa Manager Operations');
    }

    $DSAManagersOperations = new CRM_Dsa_BAO_DSAManagersOperations();
    $fields = self::fields();
    foreach ($params as $key => $value) {
      if (isset($fields[$key])) {
        $DSAManagersOperations->$key = $value;
      }
    }
    $DSAManagersOperations->save();
    CRM_Core_DAO::storeValues($DSAManagersOperations, $result);

    if (is_numeric($params['id'])) {
      CRM_Utils_Hook::post('edit', 'Cluster', $params['id'], $cluster);
    }
    else {
      CRM_Utils_Hook::post('create', 'Cluster', NULL, $cluster);
    }

    return $result;
  }
}