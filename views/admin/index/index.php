<?php
queue_js_file('items');
queue_js_file('tabs');
queue_css_file('reassignfiles');
echo head(array('title' => __('Reassign Files to Item'), 'bodyclass' => 'reassignfiles'));
?>
<?php echo flash(); ?>
<div class="drawer-contents">
  <form method="post" action="<?php echo url('reassign-files/index/save'); ?>">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2><?php echo __("Step 1: Select Item"); ?></h2>
      <div class="field">
        <p><?php echo __("Please select an existing item to reassign files to."); ?></p>
        <p>
        <?php
          echo
            sprintf(__("<em>Please note:</em> Currently displaying %d latest modified items."), $numLatest)
            . " <a href='$extendedUrl'>"
            . "[" . sprintf(__("Click here to display %d more."), $numExtension) . "]"
            . "</a>"
          ;
        ?>
        </p>
      </div>
      <div class="inputs three columns omega">
        <?php echo $this->formSelect('reassignFilesItem', false, array('multiple' => false), $itemSelect); ?>
      </div>
      <div>
      </div>
    </fieldset>
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-fields-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <h2><?php echo __("Step 2: Select Files to Reassign"); ?></h2>
      <div class="field">
        <p><?php echo __("Please select one or more files to be reassigned to the above selected item."); ?></p>
      </div>
      <div class="inputs four columns omega">
        <?php echo $this->formSelect('reassignFilesFiles[]', null, array('multiple' => true, 'size' => 10, 'style' => 'width: 600px;'), $files);
        ?>
      </div>
    </fieldset>
    <div class="field">
      <button type="submit" name="reassign-button" class="add big green button"><?php echo __("Reassign Files"); ?></button>
    </div>
  </div>
</form>
<?php echo foot();
