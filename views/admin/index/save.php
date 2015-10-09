<?php
$pageTitle = __('Save Changes');
echo head(array('title'=>$pageTitle));
echo flash();
$itemId = 0;
$files = null;
if (isset($_POST['reassignFilesItem'])) { $itemId = intval($_POST['reassignFilesItem']); }
if (isset($_POST['reassignFilesFiles'])) {$files = $_POST['reassignFilesFiles']; }
?>
<section class="seven columns alpha">
	<fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
		<div class="field">
		<?php if ($itemId<=0) { ?>
			<h3><?php echo __("No item selected."); ?></h3>
			<p><?php echo __("Please select an existing item to reassign files to."); ?></p>
		<?php } else if (is_null($files)) {  ?>
			<h3><?php echo __("No file(s) selected."); ?></h3>
			<p><?php echo __("Please select one or more files to reassign to the selected item."); ?></p>
		<?php } else { ?>
			<p><?php echo __("The selected file or files were successfully reassigned to the selected item."); ?></p>
		<?php }; ?>
			<a href="<?php echo html_escape(url('reassign-files/index')); ?>" class="add big green button" ><?php echo __('Back'); ?></a>
		</div>
	</fieldset>
</section>
<?php echo foot(); ?>
