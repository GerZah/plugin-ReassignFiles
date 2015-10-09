<div class="field">
  <div class="two columns alpha">
    <?php echo get_view()->formLabel('reassign_files_delete_orphaned_items', __('Auto-Delete Orphaned Items')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
      echo __('Check this if you want to automatically delete items that become "orphaned" after reassigning their files to other items.<br>'.
              'This will affect only those items that afterwards<br>'.
              '<ul>'.
              '<li>do not have any associated files left,</li>'.
              '<li>are neither subject nor object in a relationship (in case the "Item Relations" plugin is installed),</li>'.
              '<li>and contain no metadata whatsoever (i.e. entered text, not even a title).</li>'.
              '</ul>'.
              '<em>Please note:</em> This is often the case for files that were bulk-added through the "Dropbox" plugin.');
      ?>
    </p>
    <?php echo get_view()->formCheckbox('reassign_files_delete_orphaned_items', null, array('checked' => $deleteOrphanedItems)); ?>
  </div>
  <div class="two columns alpha">
    <?php echo get_view()->formLabel('reassign_files_delete_orphaned_items_now', __('One-Time Orphaned Items Check')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
      echo __('Check this to initiate the search for orphaned items and their deletion <em>now</em> and exactly <em>once</em>.<br>'.
              'This will be carried out as soon as you click on "Save Changes".');
      ?>
    </p>
    <?php echo get_view()->formCheckbox('reassign_files_delete_orphaned_items_now', null, array('checked' => false)); ?>
  </div>
</div>
