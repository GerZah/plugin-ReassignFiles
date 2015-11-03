<?php

/**
* Returns all fileNames as data source for the multi-select box
*/
function reassignFiles_getFileNames($filterItemID = 0)
{
  $filterItemId = intval($filterItemID);
  $filterItemInfix = ( $filterItemId > 0 ? "AND f.item_id <> $filterItemID" : "" );

  $fileNames = array();
  $db = get_db();
  $select = "SELECT et.text AS itemName, f.original_filename AS original_filename, f.item_id AS itemId, f.id AS fileId
  FROM {$db->File} f
  LEFT JOIN {$db->ElementText} et
  ON f.item_id = et.record_id
  WHERE (et.element_id = 50 or et.element_id IS NULL)
  $filterItemInfix
  GROUP BY f.id";

  $files = $db->fetchAll($select);
  foreach ($files as $file) {
    $fileNames[$file['fileId']] = $file['original_filename'].
    ' - '.( $file['itemName'] ? $file['itemName'] : "[".__("Untitled Item")."]" ).
    ' [#'.$file['itemId'].
    '/'.$file['fileId'].']';
  }
  return $fileNames;
}

/**
* Do the actual work: Reassign the $files (specified by their file IDs towards one target item ID
*/
function reassignFiles_reassignFiles($itemID, $files) {
  $errMsg = false;
  $itemID = intval($itemID); // typecast / filter item ID for strange characters

  if ($itemID) {
    $db = get_db();
    // make sure that the target item actually exists in the database
    $targetExists = $db->fetchOne("SELECT count(*) FROM `$db->Items` where id=$itemID");

    if ($targetExists) {
      $fileIDs = array();
      foreach($files as $file) {
        $fileID = intval($file); // typecast / filter file IDs for strange characters
        if ($fileID) { $fileIDs[] = $fileID; }
      }

      if ($fileIDs) { // at least one?
        $fileIDs = implode(",", $fileIDs);
        $deleteOrphanedItems = (int)(boolean) get_option('reassign_files_delete_orphaned_items');

        // 1st: If applicable, figure out which items might be orphaned after the reassign
        $potentialOrphans = array();
        if ($deleteOrphanedItems) {
          $sql = "SELECT item_id from `$db->File` where id IN ($fileIDs)";
          $potentialOrphans = $db->fetchAll($sql);
        }

        // 2nd: Actually reassign the files
        $sql = "UPDATE `$db->File` set item_id = $itemID where id IN ($fileIDs)";
        $db->query($sql); // let's do this

        // 3rd: If applicable, take care of orphans, i.e. delete them
        if ($deleteOrphanedItems) { reassignFiles_deleteOrphans($potentialOrphans); }

        # $errMsg = $sql;
      }
      else { $errMsg = __('Please select one or more files to reassign to the selected item.'); }
    }
    else { $errMsg = __('Please select an existing item to reassign files to.'); }
  }
  else { $errMsg = __('Please select an item to reassign files to.'); }

  return $errMsg;
}

/**
* If applicable, check if items who just had files assigned to other items are now "empty" and, if so, delete them
*/
function reassignFiles_deleteOrphans($potentialOrphans = false) {
  $db = get_db();

  $idInfix="WHERE true"; // Sanity: Check _all_ items

  if ( is_array($potentialOrphans) ) { // Received an array of item IDs?
    $justIds = array();
    foreach($potentialOrphans as $potentialOrphan) { $justIds[] = $potentialOrphan["item_id"]; }
    if ($justIds) {
      $justIdString = implode(",", $justIds);
      $idInfix = "WHERE it.id in ($justIdString)"; // limiting the items to be searched
    }
    else { $idInfix="WHERE false"; } // No IDs? Then don't search at all
  }

  // Huge left join query:
  // - Find those items (in case they are still existent)
  // - without any element texts
  // - without any other files assigned to them
  // - (if applicable) without item relations participation

  // Is ItemRelations installed? In that case: Create infixes to check items' relations as well
  $irJoin = $irWhere = "";
  if (reassignFiles_withItemRelations()) {
    $irJoin = "LEFT JOIN `$db->ItemRelationsRelations` ir
               ON it.id = ir.subject_item_id OR it.id = ir.object_item_id";
    $irWhere = "AND ir.subject_item_id IS NULL
                AND ir.object_item_id IS NULL";
  }

  $sql = "SELECT it.id
          FROM `$db->Items` it
          LEFT JOIN {$db->ElementText} et
          ON it.id = et.record_id AND et.element_id<>50
          LEFT JOIN `$db->File` f
          ON it.id = f.item_id
          $irJoin
          $idInfix
          AND et.record_id IS NULL
          AND f.item_id IS NULL
          $irWhere
          AND it.item_type_id IS NULL";
  $orphans = $db->fetchAll($sql);

  if ($orphans) {
    foreach($orphans as $orphan) {
      $orphanObject = $db->getTable('Item')->find($orphan["id"]);
      $orphanObject->delete();
    }
  }
}

/**
* Checks if ItemRelations is installed, so it could be taken into account during orphaned items search
*/
function reassignFiles_withItemRelations() {
  $db = get_db();
  $sql = "select 1 from `$db->ItemRelationsRelations` limit 1";

  $withItemRelations = false;
  try { $withItemRelations = ($db->fetchOne($sql) === 1); }
  catch (Exception $e) { $withItemRelations=false; }

  return $withItemRelations;
}