<?php

// Include global helper functions to be accessible from within all objects
define('REASSIGNFILES_DIR',dirname(__FILE__));
require_once REASSIGNFILES_DIR.'/helpers/ReassignFilesFunctions.php';

/**
* ConditionalElements plugin.
*
* @package Omeka\Plugins\ReassignFiles
*/
class ReassignFilesPlugin extends Omeka_Plugin_AbstractPlugin
{
  // Define Hooks
  protected $_hooks = array(
    'initialize',
    'install',
    'uninstall',
    'after_save_item',
    'admin_items_form_files',
    'define_acl',
    'config_form',
    'config',
  );

  //Define Filters
  protected $_filters = array('admin_navigation_main');

  protected $_options = array(
    'reassign_files_delete_orphaned_items' => 1,
  );
  public function hookInitialize()
  {
    add_translation_source(dirname(__FILE__) . '/languages');
  }

  /**
  * Install the plugin.
  */
  public function hookInstall() {

    SELF::_installOptions();
  }

  /**
  * Uninstall the plugin.
  */
  public function hookUninstall() {

    SELF::_uninstallOptions();
  }
  /**
  * reassignfiles admin navigation filter
  */
  public function filterAdminNavigationMain($nav)
  {

    if(is_allowed('ReassignFiles_Index', 'index')) {
      $nav[] = array('label' => __('Reassign Files'), 'uri' => url('reassign-files'));
    }
    return $nav;
  }

  /*
  * Define ACL entry for reassignfiles controller.
  */
  public function hookDefineAcl($args)
  {
    $args['acl']->addResource('ReassignFiles_Index');
  }

  /**
  * Display the reassignfiles list on the  item form.
  * This simply adds a heading to the output
  */
  public function hookAdminItemsFormFiles()
  {
    echo '<h3>' . __('Add Files from Other Items') . '</h3>';
    $itemId = metadata('item', 'id');
    $fileNames = reassignFiles_getFileNames($itemId); // from helpers/ReassignFilesFunctions.php
    echo common('reassignfileslist', array( "fileNames" => $fileNames ), 'index');
  }

  public function hookAfterSaveItem($args)
  {
    if (!$args['post']) {
      return;
    }

    $record = $args['record'];
    $post = $args['post'];
    #echo "<pre>"; print_r($_POST); die("</pre>");

    // reassign the selected files from other items to the current item
    if (isset($post['reassignFilesFiles']) and (isset($post['itemId']))) {
      $itemID = ( $post['itemId'] ? $post['itemId'] : $args["record"]["id"] );
      $errMsg = reassignFiles_reassignFiles($itemID, $post['reassignFilesFiles']);
      # if ($errMsg) { $this->_helper->flashMessenger( $errMsg, 'error' ); }
      // ... turns out, we don't actually have a $this->_helper object here :-(
    }
  }

  /**
  * Display the plugin configuration form.
  */
  public static function hookConfigForm() {
    $deleteOrphanedItems = (int)(boolean) get_option('reassign_files_delete_orphaned_items');
    require dirname(__FILE__) . '/config_form.php';
  }

  /**
  * Handle the plugin configuration form.
  */
  public static function hookConfig() {
    $deleteOrphanedItems = (int)(boolean) $_POST['reassign_files_delete_orphaned_items'];
    set_option('reassign_files_delete_orphaned_items', $deleteOrphanedItems);
    $deleteOrphanedItemsNow = (int)(boolean) $_POST['reassign_files_delete_orphaned_items_now'];
    if ($deleteOrphanedItemsNow) { reassignFiles_deleteOrphans(); }
  }

  private function _batchDeleteOrphanedItems() {
    $db = get_db();
    #check file? itemrelation installed? title? description?
    $sql= "select id from `$db->Items` ";
    $items = $db->fetchAll($sql);
    #echo "<pre>"; print_r($items); die("</pre>");
  }

}
