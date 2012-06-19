<script type="text/javascript">
	$(function() {
		$("#add_village_input").validationEngine();
	});

</script>
<?php
if (isset($village)) { 
	$village_name = $village -> Name;
	$ward = $village -> Ward;
	$village_id = $village -> id;
} else {
	$village_name = "";
	$village_id = "";
	$ward = ""; 

}
$attributes = array("method" => "post", "id" => "add_village_input");
echo form_open('village_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Add New Village
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $village_id;?>" /> 
	<p>
		<label for="village_name">Village Name: </label>
		<input id="village_name" name="village_name" type="text" value="<?php echo $village_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Village</span>
	</p>
	<p>
		<label for="ward">Ward</label>
		<select name="ward" class="dropdown validate[required]" id="ward">
			<option></option>
			<?php 
			foreach($wards as $ward_object){?>
				<option value="<?php echo $ward_object->id;?>" <?php if($ward_object->id == $ward){echo "selected";}?>><?php echo $ward_object->Name." (".$ward_object -> Region_Object->Region_Name.")";?></option>
			<?php }
			?>
		</select>
		<span class="field_desc">Select this village's ward</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>