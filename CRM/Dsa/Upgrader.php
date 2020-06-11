<?php

require_once 'dsa.optiongroup.inc.php';
require_once 'dsa.activitytype.inc.php';

/**
 * Collection of upgrade steps
 */
class CRM_Dsa_Upgrader extends CRM_Dsa_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed
   */
  public function install() {
    // WARNING: reinstall may fail due to several ALTER TABLE updates, which cannot be intercepted by try/catch!!
    // build tables for storage of DSA data
    $this->executeSqlFile('sql/build_tables.sql');

    // fill civicrm_country_pum table with additional country codes
    if (CRM_Core_DAO::checkTableExists('civicrm_country_pum')) {
      $this->executeSqlFile('sql/civicrm_country_pum_data.sql');
    }
    $this->executeCustomDataFile('xml/representative_payment.xml');
    // up-to-date to version 1020
  }

  /**
   * Upgrade 1001 - add table civicrm_dsa_compose
   * @date 14 May 2014
   */
  public function upgrade_1001() {
    $this->ctx->log->info('Applying update 1001 (add table civicrm_dsa_compose)');
    // table for dsa on main activity
    if (!CRM_Core_DAO::checkTableExists('civicrm_dsa_compose')) {
      $this->executeSqlFile('sql/civicrm_dsa_compose_1001.sql');
    }
    return TRUE;
  }

  /**
   * Upgrade 1002 - alter table civicrm_dsa_compose
   * @date 21 May 2014
   */
  public function upgrade_1002() {
    $this->ctx->log->info('Applying update 1002 (alter table civicrm_dsa_compose)');
    // alter table for dsa on main activity
    $this->executeSqlFile('sql/civicrm_dsa_compose_1002.sql');
    return TRUE;
  }

  /**
   * Upgrade 1003 - alter table civicrm_dsa_compose
   * @date 21 May 2014
   */
  public function upgrade_1003() {
    $this->ctx->log->info('Applying update 1003 (create table civicrm_dsa_payment)');
    // create table for dsa payments
    $this->executeSqlFile('sql/civicrm_dsa_payment_1003.sql');
    return TRUE;
  }

  /**
   * Upgrade 1004 - add option group general_ledger
   * @date 30 June 2014
   */
  public function upgrade_1004() {
    $this->ctx->log->info('Applying update 1004 (create/fill option group \'general_ledger\')');
    // re-run option group installer
    DSA_OptionGroup::install();
    return TRUE;
  }

  /**
   * Upgrade 1005 - add relationship_type_id to civicrm_dsa_compose
   * @date 1 July 2014
   */
  public function upgrade_1005() {
    $this->ctx->log->info('Applying update 1005 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1005.sql');
    return TRUE;
  }

  /**
   * Upgrade 1006 - add relationship_type_id to civicrm_dsa_compose
   * @date 2 July 2014
   */
  public function upgrade_1006() {
    $this->ctx->log->info('Applying update 1006 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1006.sql');
    return TRUE;
  }

  /**
   * Upgrade 1007 - add type to civicrm_dsa_compose
   *              - set type in all records to 1 (payment)
   * @date 7 July 2014
   */
  public function upgrade_1007() {
    $this->ctx->log->info('Applying update 1007 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1007.sql');
    return TRUE;
  }

  /**
   * Upgrade 1008 - add option values for option group general_ledger
   *              - add option group dsa_configuration
   *              - remove column amt_debriefing from civicrm_dsa_compose
   * @date 22 August 2014
   */
  public function upgrade_1008() {
    $this->ctx->log->info('Applying update 1008 (additional values for \'general_ledger\' / create/fill option group \'dsa_configuration\')');
    // re-run option group installer
    DSA_OptionGroup::install();

    $this->ctx->log->info('Applying update 1008 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1008.sql');
    return TRUE;
  }

  /**
   * Upgrade 1009 - additional columns in civicrm_dsa_compose to track invoice numbers/codes for creditation
   * @date 27 August 2014
   */
  public function upgrade_1009() {
    $this->ctx->log->info('Applying update 1009 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1009.sql');
    return TRUE;
  }

  /**
   * Upgrade 1010 - additional column in civicrm_dsa_compose to track the activity_id of the credited DSA
   * @date 28 August 2014
   */
  public function upgrade_1010() {
    $this->ctx->log->info('Applying update 1010 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1010.sql');
    return TRUE;
  }

  /**
   * Upgrade 1011 - add option values for dsa_configuration
   * @date 3 September 2014
   */
  public function upgrade_1011() {
    $this->ctx->log->info('Applying update 1011 (additional values for \'dsa_configuration\')');
    // re-run option group installer
    DSA_OptionGroup::install();
    return TRUE;
  }

  /**
   * Upgrade 1012 - implementation of representative payments
   * @date 11 Oktober 2014
   */
  public function upgrade_1012() {
    $this->ctx->log->info('Applying update 1012 (implementation of representative payments)');
    // create table civicrm_representative_compose
    $this->executeSqlFile('sql/civicrm_representative_compose_1012.sql');
    // re-run option group installer
    DSA_OptionGroup::install();
    // re-run activity type installer
    DSA_ActivityType::install();
    return TRUE;
  }

  /**
   * Upgrade 1013 - additional column in civicrm_dsa_compose to track the donor (for creditation)
   * @date 13 November 2014
   */
  public function upgrade_1013() {
    $this->ctx->log->info('Applying update 1013 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1013.sql');
    return TRUE;
  }

  /**
   * Upgrade 1014 - additional columns in civicrm_representative_compose for comments and donor
   * @date 17 November 2014
   */
  public function upgrade_1014() {
    $this->ctx->log->info('Applying update 1014 (alter table civicrm_representative_compose)');
    // alter table civicrm_representative_compose
    $this->executeSqlFile('sql/civicrm_representative_compose_1014.sql');
    return TRUE;
  }

  /**
   * Upgrade 1015 - job control using nl.pum.roster
   * @date 10 December 2014
   */
  public function upgrade_1015() {
    $this->ctx->log->info('Applying update 1015 (implementing job control using nl.pum.roster)');
    if (!CRM_Generic_Misc::generic_verify_extension('nl.pum.roster')) {
      CRM_Core_Error::fatal("Mandatory module nl.pum.roster is not enabled!");
      return FALSE;
    };
    $result = $this->_initiateRoster();
    if (!$result) {
      CRM_Core_Error::fatal("Required rosters could not be initiated");
    } else {
      return TRUE;
    }
  }

  public function upgrade_1016() {
    $this->executeCustomDataFile('xml/representative_payment.xml');
    return true;
  }

  public function upgrade_1017() {
    $activity_type_option_group = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => 'activity_type'));
    $rep_payment_id = civicrm_api3('OptionValue', 'getvalue', array('name' => 'Representative payment', 'option_group_id' => $activity_type_option_group, 'return' => 'value'));

    $activity_status_option_group = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => 'activity_status'));
    $paid_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'dsa_paid', 'option_group_id' => $activity_status_option_group));

    $sql = "INSERT INTO civicrm_value_rep_payment (entity_id, date_paid) (SELECT a.id as entity_id, a.activity_date_time as date_paid FROM civicrm_activity a WHERE activity_type_id = %1 AND status_id = %2)";
    $sqlParams[1] = array($rep_payment_id, 'Integer');
    $sqlParams[2] = array($paid_status_id, 'Integer');
    CRM_Core_DAO::executeQuery($sql, $sqlParams);

    return true;
  }

  /**
   * CRM_Dsa_Upgrader::upgrade_1018()
   * @date May 2020
   *
   * Add secondary approval for dsa above certain amount
   *
   * @return
   */
  public function upgrade_1018() {
    $this->ctx->log->info('Applying update 1018 (alter table civicrm_dsa_compose)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_compose_1018.sql');
    return TRUE;
  }

  /**
   * CRM_Dsa_Upgrader::upgrade_1019()
   * @date May 2020
   *
   * Add teamleaders for dsa approval
   *
   * @return
   */
  public function upgrade_1019() {
    $this->ctx->log->info('Applying update 1019 (add table civicrm_dsa_managersoperations)');
    // alter table civicrm_dsa_compose
    $this->executeSqlFile('sql/civicrm_dsa_managersoperations_1019.sql');
    return TRUE;
  }

  /**
   * CRM_Dsa_Upgrader::upgrade_1020()
   * @date May 2020
   *
   * Add dsa status rejected for dsa approval
   *
   * @return
   */
  public function upgrade_1020() {
    $this->ctx->log->info('Applying update 1020 (add dsa status Rejected)');

    try {
      $activity_status_option_group = civicrm_api3('OptionGroup', 'getvalue', array('return' => 'id', 'name' => 'activity_status'));

      $params = array(
        'version' => 3,
        'sequential' => 1,
        'option_group_id' => $activity_status_option_group,
        'label' => 'Rejected',
        'name' => 'dsa_rejected',
        'value' => 1505,
        'is_reserved' => 1,
        'is_active' => 1,
        'filter' => 0,
        'weight' => 1508,
        'description' => 'DSA activity rejected'
      );
      $result = civicrm_api3('OptionValue', 'create', $params);

      return $result;
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not create option value: dsa_rejected '.__METHOD__
          .', contact your system administrator. Error from API OptionValue create: '.$ex->getMessage());
    }

    return TRUE;
  }

  /**
   * Helper function to define rosters for the DSA- and Representative payment runs
   * Note: by default the roster will block both runs (next_run = <yesterday>)
   * Use civicrm/rosterview (having the right privileges for the rosters) to set a proper next run date
   */
  function _initiateRoster() {
    $params = array(
      'version' => 3,
      'q' => 'civicrm/ajax/rest',
      'sequential' => 1,
      'name' => 'DSA payment',
      'type' => 'w',
      'value' => '2,4',
      'min_interval' => 1,
      'next_run' => date('Y-m-d', strtotime('-1 days')),
      'privilege' => 'edit schedule for DSA payment',
    );
    $result = civicrm_api('Roster', 'set', $params);
    if (!empty($result['is_error'])) {
      return FALSE;
    }

    $params = array(
      'version' => 3,
      'q' => 'civicrm/ajax/rest',
      'sequential' => 1,
      'name' => 'Representative payment',
      'type' => 'm',
      'value' => 1,
      'min_interval' => 90,
      'next_run' => date('Y-m-d', strtotime('-1 days')),
      'privilege' => 'edit schedule for Representative payment',
    );
    $result = civicrm_api('Roster', 'set', $params);
    if (!empty($result['is_error'])) {
      return FALSE;
    }
    return TRUE;
  }


  /**
   * Example: Run an external SQL script when the module is uninstalled
   *
  public function uninstall() {
   $this->executeSqlFile('sql/myuninstall.sql');
  }

  /**
   * Example: Run a simple query when a module is enabled
   *
  public function enable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 1 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a simple query when a module is disabled
   *
  public function disable() {
    CRM_Core_DAO::executeQuery('UPDATE foo SET is_active = 0 WHERE bar = "whiz"');
  }

  /**
   * Example: Run a couple simple queries
   *
   * @return TRUE on success
   * @throws Exception
   *
  public function upgrade_4200() {
    $this->ctx->log->info('Applying update 4200');
    CRM_Core_DAO::executeQuery('UPDATE foo SET bar = "whiz"');
    CRM_Core_DAO::executeQuery('DELETE FROM bang WHERE willy = wonka(2)');
    return TRUE;
  } // */


  /**
   * Example: Run an external SQL script
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    // this path is relative to the extension base dir
    $this->executeSqlFile('sql/upgrade_4201.sql');
    return TRUE;
  } // */


  /**
   * Example: Run a slow upgrade process by breaking it up into smaller chunk
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4202() {
    $this->ctx->log->info('Planning update 4202'); // PEAR Log interface

    $this->addTask(ts('Process first step'), 'processPart1', $arg1, $arg2);
    $this->addTask(ts('Process second step'), 'processPart2', $arg3, $arg4);
    $this->addTask(ts('Process second step'), 'processPart3', $arg5);
    return TRUE;
  }
  public function processPart1($arg1, $arg2) { sleep(10); return TRUE; }
  public function processPart2($arg3, $arg4) { sleep(10); return TRUE; }
  public function processPart3($arg5) { sleep(10); return TRUE; }
  // */


  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  public function upgrade_4203() {
    $this->ctx->log->info('Planning update 4203'); // PEAR Log interface

    $minId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(min(id),0) FROM civicrm_contribution');
    $maxId = CRM_Core_DAO::singleValueQuery('SELECT coalesce(max(id),0) FROM civicrm_contribution');
    for ($startId = $minId; $startId <= $maxId; $startId += self::BATCH_SIZE) {
      $endId = $startId + self::BATCH_SIZE - 1;
      $title = ts('Upgrade Batch (%1 => %2)', array(
        1 => $startId,
        2 => $endId,
      ));
      $sql = '
        UPDATE civicrm_contribution SET foobar = whiz(wonky()+wanker)
        WHERE id BETWEEN %1 and %2
      ';
      $params = array(
        1 => array($startId, 'Integer'),
        2 => array($endId, 'Integer'),
      );
      $this->addTask($title, 'executeSql', $sql, $params);
    }
    return TRUE;
  } // */

}
