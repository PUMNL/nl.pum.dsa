<?php
class CRM_Dsa_BAO_DsaTeamleaders extends CRM_Dsa_DAO_DsaTeamleaders {
  /**
   * Function to get values
   *
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $dsaTeamleaders = new CRM_Dsa_BAO_DsaTeamleaders();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $key => $value) {
        if (isset($fields[$key])) {
          $dsaTeamleaders->$key = $value;
        }
      }
    }
    $dsaTeamleaders->find();
    while ($dsaTeamleaders->fetch()) {
      $row = array();
      CRM_Core_DAO::storeValues($dsaTeamleaders, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }
  /**
   * Function to add or update dsa teamleaders
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
      throw new Exception('Params can not be empty when adding or updating a Dsa Teamleader');
    }

    $dsaTeamleaders = new CRM_Dsa_BAO_DsaTeamleaders();
    $fields = self::fields();
    foreach ($params as $key => $value) {
      if (isset($fields[$key])) {
        $dsaTeamleaders->$key = $value;
      }
    }
    $dsaTeamleaders->save();
    CRM_Core_DAO::storeValues($dsaTeamleaders, $result);

    if (is_numeric($params['id'])) {
      CRM_Utils_Hook::post('edit', 'Cluster', $params['id'], $cluster);
    }
    else {
      CRM_Utils_Hook::post('create', 'Cluster', NULL, $cluster);
    }

    return $result;
  }
}