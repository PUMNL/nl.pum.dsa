<?php

require_once 'dsa.optiongroup.inc.php';

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
	// tables for storage of DSA base details
	if (!CRM_Core_DAO::checkTableExists('civicrm_dsa_batch')) {
		$this->executeSqlFile('sql/civicrm_dsa_batch.sql');
	}
	if (!CRM_Core_DAO::checkTableExists('civicrm_dsa_rate')) {
		$this->executeSqlFile('sql/civicrm_dsa_rate.sql');
	}
	// table for conversion of UN DSA guidelines
	if (!CRM_Core_DAO::checkTableExists('civicrm_dsa_convert')) {
		$this->executeSqlFile('sql/civicrm_dsa_convert.sql');
	}
	// table for conversion of UN country codes (alpha 3) to ISO (alpha 2)
	if (!CRM_Core_DAO::checkTableExists('civicrm_country_pum')) {
		$this->executeSqlFile('sql/civicrm_country_pum.sql');
	}
	// tables for controlling scheduled tasks (DSA and  Representative Renumeration)
	if (!CRM_Core_DAO::checkTableExists('civicrm_pum_scheduling_group')) {
		$this->executeSqlFile('sql/civicrm_pum_scheduling_group.sql');
	}
	if (!CRM_Core_DAO::checkTableExists('civicrm_pum_scheduling_value')) {
		$this->executeSqlFile('sql/civicrm_pum_scheduling_value.sql');
	}
	// fill civicrm_country_pum table with additional country codes
	if (CRM_Core_DAO::checkTableExists('civicrm_country_pum')) {
		$this->executeSqlFile('sql/civicrm_country_pum_data.sql');
	}
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
	 *				- add option group dsa_configuration
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
