<script type="text/javascript">
	$(function() {
		$("#add_ward_input").validationEngine();
	});

</script>
<?php
if (isset($ward)) { 
	$ward_name = $ward -> Name;
	$region = $ward -> Region;
	$ward_id = $ward -> id;
} else {
	$ward_name = "";
	$ward_id = "";
	$region = ""; 

}
$attributes = array("method" => "post", "id" => "add_ward_input");
echo form_open('ward_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Add New Ward
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $ward_id;?>" /> 
	<p>
		<label for="ward_name">Ward Name: </label>
		<input id="ward_name" name="ward_name" type="text" value="<?php echo $ward_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Ward</span>
	</p>
	<p>
		<label for="region">Zone</label>
		<select name="region" class="dropdown validate[required]" id="region">
			<option></option>
			<?php 
			foreach($regions as $region_object){?>
				<option value="<?php echo $region_object->id;?>" <?php if($region_object->id == $region){echo "selected";}?>><?php echo $region_object->Region_Name;?></option>
			<?php }
			?>
		</select>
		<span class="field_desc">Select this ward's zone</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>