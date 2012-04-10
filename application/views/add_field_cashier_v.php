<script type="text/javascript">
	$(function() {
		$("#add_field_cashier_input").validationEngine();
	});

</script>
<?php
if (isset($field_cashier)) {
	$field_cashier_code = $field_cashier -> Field_Cashier_Code;
	$name = $field_cashier -> Field_Cashier_Name;
	$national_id = $field_cashier -> National_Id;
	$phone_number = $field_cashier -> Phone_Number;
	$field_cashier_id = $field_cashier -> id;
} else {
	$field_cashier_code = "";
	$name = "";
	$national_id = "";
	$phone_number = "";
	$field_cashier_id = "";
}
$attributes = array("method" => "post", "id" => "add_field_cashier_input");
echo form_open('field_cashier_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Field Cashier
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $field_cashier_id;?>" />
	<p>
		<label for="field_cashier_code">Field Cashier Code: </label>
		<input id="field_cashier_code"  name="field_cashier_code" type="text"  value="<?php echo $field_cashier_code;?>" class="validate[required]"/>
		<span class="field_desc">Enter the code for this Field Cashier</span>
	</p>
	<p>
		<label for="field_cashier_name">Full Name: </label>
		<input id="field_cashier_name" name="field_cashier_name" type="text" value="<?php echo $name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Field Cashier</span>
	</p>
		<p>
		<label for="phone_number">Phone Number: </label>
		<input id="phone_number" name="phone_number" type="text" value="<?php echo $phone_number;?>" class="validate[required]"/>
		<span class="field_desc">Enter the phone number for this Field Cashier</span>
	</p>
		<p>
		<label for="national_id">National Id Number: </label>
		<input id="national_id" name="national_id" type="text" value="<?php echo $national_id;?>" class="validate[required]"/>
		<span class="field_desc">Enter the national Id number for this Field Cashier</span>
	</p> 
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>