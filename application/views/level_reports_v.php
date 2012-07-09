<?php
$attributes = array("method" => "post", "id" => "add_area_input");
echo form_open('report_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<h2>Reports for <?php echo $level_object->Level_Name?></h2>
<fieldset>
	<legend>
		Access Level Report
	</legend>
	<input type="hidden" name="level" value="<?php echo $level;?>" />
<?php 
foreach($reports as $report){?>
<p>
<input name="report[]" type="checkbox" value="<?php echo $report['menu'];?>" <?php if($report['access_level'] == $level){echo "checked";}?>><?php echo $report['menu_text'];?>
</p>
<?php }
?>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>