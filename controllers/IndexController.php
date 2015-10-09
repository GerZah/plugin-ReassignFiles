<?php
/**
* @package ReassignFiles
*/

/**
* Controller for ReassignFiles admin pages.
*
* @package ReassignFiles
*/
class ReassignFiles_IndexController extends Omeka_Controller_AbstractActionController
{
  /**
  * Front admin page.
  */
  public function indexAction() {
    $this->view->files = reassignFiles_getFileNames(); // from helpers/ReassignFilesFunctions.php
  }

  public function saveAction()
  {
    if ($this->getRequest()->isPost()){
      if (isset($_POST['reassignFilesFiles']) and (isset($_POST['reassignFilesItem']))) {
        $errMsg = reassignFiles_reassignFiles($_POST['reassignFilesItem'], $_POST['reassignFilesFiles']);
        if ($errMsg) { $this->_helper->flashMessenger( $errMsg, 'error' ); }
      }
    }
  }
}
