<script type="text/javascript">
	$(function() {
		$("#add_agent_input").validationEngine();
	});

</script>
<?php
if (isset($agent)) {
	$agent_code = $agent -> Agent_Code;
	$first_name = $agent -> First_Name;
	$surname = $agent -> Surname;
	$national_id = $agent -> National_Id; 
	$agent_id = $agent -> id;
} else {
	$agent_code = "";
	$first_name = "";
	$surname = "";
	$national_id = ""; 
	$agent_id = "";
}
$attributes = array("method" => "post", "id" => "add_agent_input");
echo form_open('agent_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Agent
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $agent_id;?>" />
	<p>
		<label for="agent_code">Agent Code: </label>
		<input id="agent_code" name="agent_code" type="text" value="<?php echo $agent_code;?>" class="validate[required]" />
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
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form> 