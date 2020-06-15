<?php

require_once 'CRM/Core/Page.php';

class CRM_Dsa_Page_DownloadPayments extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle(ts('Download Payment'));

    if (!CRM_Core_Permission::check('download payments')) {
      // user has not privilige to download financial data
      $this->assign('message', ts('You are not authorised to download payment details.'));
    } else {
      // see if the requested payment record exists
      $paymentid = $_GET['payment'];
      $sql = "SELECT filename, filetype, filesize, content FROM civicrm_dsa_payment WHERE id = " . $paymentid;
      $dao = CRM_Core_DAO::executeQuery($sql);
      if ($dao->N == 0) {
        // payment id does not exist
        $this->assign('message', ts('Payment id') . ' ' . $paymentid . ' ' . ts('could not be found'));
      } else {
        // payment record exists: retrieve details
        $dao->fetch();
        $this->assign('message', ts('Downloading') . ' ' . $dao->filename); // just in case the page should ever get displayed
        // present download and abort the build of a new page
        header("Content-type: text/csv");
        header("Content-Transfer-Encoding: base64");
        print($dao->content);
        header("Content-Disposition: attachment; filename=" . $dao->filename);
        exit();
      }
    }

    parent::run();
  }
}
