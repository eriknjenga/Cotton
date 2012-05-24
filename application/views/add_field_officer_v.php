<script type="text/javascript">
	$(function() {
		$("#add_field_officer_input").validationEngine();
	});

</script>
<?php
if (isset($officer)) {
	$officer_code = $officer -> Officer_Code;
	$officer_name = $officer -> Officer_Name;
	$national_id = $officer -> National_Id; 
	$officer_id = $officer -> id;
	$region = $officer -> Region;
} else {
	$officer_code = "";
	$officer_name = "";
	$national_id = "";
	$officer_id = "";
	$region= ""; 
	
}
$attributes = array("method" => "post", "id" => "add_field_officer_input");
echo form_open('field_officer_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Field Extension Officer
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $officer_id;?>" />
	<p>
		<label for="officer_code">Officer Code: </label>
		<input id="officer_code"  name="officer_code" type="text"  value="<?php echo $officer_code;?>" class="validate[required]"/>
		<span class="field_desc">Enter the code for this Officer</span>
	</p>
	<p>
		<label for="officer_name">Officer Name: </label>
		<input id="officer_name" name="officer_name" type="text" value="<?php echo $officer_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this officer</span>
	</p>
	<p>
		<label for="national_id">National ID Number: </label>
		<input name="national_id" id="national_id" value="<?php echo $national_id;?>" type="text" class="validate[required]" />
		<span class="field_desc">Enter the National ID Number for this officer</span>
	</p>
	 <p>
		<label for="area">Zone</label>
		<select name="region" class="dropdown validate[required]" id="region">
			<option></option>
			<?php
foreach($regions as $region_object){
			?>
			<option value="<?php echo $region_object -> id;?>" <?php
				if ($region_object -> id == $region) {echo "selected";
				}
			?>><?php echo $region_object -> Region_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the zone covered by this field officer</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>