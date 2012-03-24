<script type="text/javascript">
	$(function() {
		$("#add_area_input").validationEngine();
	});

</script>
<?php
if (isset($area)) {
	$area_code = $area -> Area_Code;
	$area_name = $area -> Area_Name;
	$region = $area -> Region;
	$area_id = $area -> id;
} else {
	$area_code = "";
	$area_name = "";
	$region = "";
	$area_id = "";

}
$attributes = array("method" => "post", "id" => "add_area_input");
echo form_open('area_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Area
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $area_id;?>" />
	<p>
		<label for="area_code">Area Code: </label>
		<input id="area_code"  name="area_code" type="text"  value="<?php echo $area_code;?>" class="validate[required]"/>
		<span class="field_desc">Enter the code for this Area</span>
	</p>
	<p>
		<label for="area_name">Area Name: </label>
		<input id="area_name" name="area_name" type="text" value="<?php echo $area_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Area</span>
	</p>
	<p>
		<label for="region">Region</label>
		<select name="region" class="dropdown validate[required]" id="region">
			<option></option>
			<?php 
			foreach($regions as $region_object){?>
				<option value="<?php echo $region_object->id;?>" <?php if($region_object->id == $region){echo "selected";}?>><?php echo $region_object->Region_Name;?></option>
			<?php }
			?>
		</select>
		<span class="field_desc">Select this area's region</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>