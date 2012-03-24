<script type="text/javascript">
	$(function() {
		$("#add_region_input").validationEngine();
	});

</script>
<?php
if (isset($region)) {
	$region_code = $region -> Region_Code;
	$region_name = $region -> Region_Name; 
	$region_id = $region -> id;
} else {
	$region_code = "";
	$region_name = "";
	$region_id = ""; 
}
$attributes = array("method" => "post", "id" => "add_region_input");
echo form_open('region_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Region
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $region_id;?>" />
	<p>
		<label for="region_code">Region Code: </label>
		<input id="region_code"  name="region_code" type="text"  value="<?php echo $region_code;?>" class="validate[required]"/>
		<span class="field_desc">Enter the code for this Region</span>
	</p>
	<p>
		<label for="region_name">Region Name: </label>
		<input id="region_name" name="region_name" type="text" value="<?php echo $region_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this region</span>
	</p>
	 
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>