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

    $defaultLatest = $numExtension = 50;
    $numLatest = ( isset($_GET["numlatest"]) ? intval($_GET["numlatest"]) : $defaultLatest );
    $numLatest = ( $numLatest < $defaultLatest ? $defaultLatest : $numLatest );
    $numExtended = $numLatest + $numExtension;
    $extendedUrl = $_SERVER['REDIRECT_URL'] . "?numlatest=$numExtended";

    $this->view->numLatest = $numLatest;
    $this->view->numExtension = $numExtension;
    $this->view->extendedUrl = $extendedUrl;

    $itemNames = array();
    $sqlDb = get_db();
    $itemSelect = array(-1 => __('Select Below'));
    $query = "
      SELECT id
      FROM `$sqlDb->Items`
      ORDER BY modified DESC
      LIMIT $numLatest
    ";
    $itemIds = $sqlDb->fetchAll($query);
    foreach ($itemIds as $itemId) {
      $curItem = get_record_by_id('Item', $itemId["id"]);
      $itemSelect[$itemId["id"]] = metadata($curItem, array('Dublin Core', 'Title')) . " [#".$itemId["id"]."]";
    }
    $this->view->itemSelect = $itemSelect;
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
