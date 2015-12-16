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
    $this->view->files = ReassignFilesPlugin::reassignFiles_getFileNames();
  }

  public function saveAction()
  {
    if ($this->getRequest()->isPost()){
      if (isset($_POST['reassignFilesFiles']) and (isset($_POST['reassignFilesItem']))) {
        $errMsg = ReassignFilesPlugin::reassignFiles_reassignFiles($_POST['reassignFilesItem'], $_POST['reassignFilesFiles']);
        if ($errMsg) { $this->_helper->flashMessenger( $errMsg, 'error' ); }
      }
    }
  }
}
