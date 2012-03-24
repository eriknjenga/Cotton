<script type="text/javascript">
	$(function() {
		$("#add_distributor_input").validationEngine();
	});

</script>
<?php
if (isset($distributor)) {
	$distributor_code = $distributor -> Distributor_Code;
	$first_name = $distributor -> First_Name;
	$surname = $distributor -> Surname;
	$national_id = $distributor -> National_Id;
	$area = $distributor -> Area;
	$distributor_id = $distributor -> id;
} else {
	$distributor_code = "";
	$first_name = "";
	$surname = "";
	$national_id = "";
	$area = "";
	$distributor_id = "";
}
$attributes = array("method" => "post", "id" => "add_distributor_input");
echo form_open('distributor_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Distributor
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $distributor_id;?>" />
	<p>
		<label for="distributor_code">Distributor Code: </label>
		<input id="distributor_code" name="distributor_code" type="text" value="<?php echo $distributor_code;?>" class="validate[required]" />
		<span class="field_desc">Enter the code for this distributor</span>
	</p>
	<p>
		<label for="first_name">First Name: </label>
		<input id="first_name" name="first_name" type="text" value="<?php echo $first_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the first name for this distributor</span>
	</p>
	<p>
		<label for="surname">Surname: </label>
		<input id="surname" name="surname" type="text" value="<?php echo $surname;?>" class="validate[required]"/>
		<span class="field_desc">Enter the surname for this distributor</span>
	</p>
	<p>
		<label for="national_id">National ID No.: </label>
		<input id="national_id" name="national_id" type="text" value="<?php echo $national_id;?>" class="validate[required]"/>
		<span class="field_desc">Enter the national id number for this distributor</span>
	</p>
	<p>
		<label for="area">Area</label>
		<select name="area" class="dropdown validate[required]" id="area">
			<option></option>
			<?php
foreach($areas as $area_object){
			?>
			<option value="<?php echo $area_object -> id;?>" <?php
				if ($area_object -> id == $area) {echo "selected";
				}
			?>><?php echo $area_object -> Area_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the area covered by this distributor</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form> 