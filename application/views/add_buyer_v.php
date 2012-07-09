<script type="text/javascript">
	$(function() {
		$("#add_buyer_input").validationEngine();
	});

</script>
<?php
if (isset($buyer)) {
	$buyer_code = $buyer -> Buyer_Code;
	$name = $buyer -> Name;
	$national_id = $buyer -> National_Id;
	$phone_number = $buyer -> Phone_Number;
	$buyer_id = $buyer -> id;
} else {
	$buyer_code = "";
	$name = "";
	$national_id = "";
	$phone_number = "";
	$buyer_id = "";
}
$attributes = array("method" => "post", "id" => "add_buyer_input");
echo form_open('buyer_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Buyer
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $buyer_id;?>" />
	<p>
		<label for="buyer_code">Buyer Code: </label>
		<input id="buyer_code"  name="buyer_code" type="text"  value="<?php echo $buyer_code;?>" />
		<span class="field_desc">Enter the code for this Buyer</span>
	</p>
	<p>
		<label for="name">Full Name: </label>
		<input id="name" name="name" type="text" value="<?php echo $name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Buyer</span>
	</p>
		<p>
		<label for="phone_number">Phone Number: </label>
		<input id="phone_number" name="phone_number" type="text" value="<?php echo $phone_number;?>" />
		<span class="field_desc">Enter the phone number for this Buyer</span>
	</p>
		<p>
		<label for="national_id">National Id Number: </label>
		<input id="national_id" name="national_id" type="text" value="<?php echo $national_id;?>"  />
		<span class="field_desc">Enter the national Id number for this Buyer</span>
	</p> 
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>