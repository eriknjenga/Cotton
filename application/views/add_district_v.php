<script type="text/javascript">
	$(function() {
		$("#add_district_input").validationEngine();
	});

</script>
<?php
if (isset($district)) { 
	$district_name = $district -> Name;
	$region = $district -> Region;
	$district_id = $district -> id;
} else {
	$district_name = "";
	$district_id = "";
	$region = ""; 

}
$attributes = array("method" => "post", "id" => "add_district_input");
echo form_open('district_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Add New District
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $district_id;?>" /> 
	<p>
		<label for="district_name">District Name: </label>
		<input id="district_name" name="district_name" type="text" value="<?php echo $district_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this District</span>
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
		<span class="field_desc">Select this district's zone</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>