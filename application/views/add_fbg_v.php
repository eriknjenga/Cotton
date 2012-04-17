<script type="text/javascript">
	$(function() {
		$("#add_fbg_input").validationEngine();
	});

</script>
<?php
if (isset($fbg)) {
	$cpc_number = $fbg -> CPC_Number;
	$group_name = $fbg -> Group_Name;
	$chairman_name = $fbg -> Chairman_Name;
	$chairman_phone = $fbg -> Chairman_Phone;
	$secretary_name = $fbg -> Secretary_Name;
	$secretary_phone = $fbg -> Secretary_Phone;
	$field_officer = $fbg -> Field_Officer;
	$hectares_available = $fbg -> Hectares_Available;
	$fbg_id = $fbg -> id;
} else {
	$cpc_number = "";
	$group_name = "";
	$field_officer = "";
	$hectares_available = "";
	$chairman_name = "";
	$chairman_phone = "";
	$secretary_name = "";
	$secretary_phone = "";
	$fbg_id = "";
}
$attributes = array("method" => "post", "id" => "add_fbg_input");
echo form_open('fbg_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Farmer Business Group
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $fbg_id;?>" />
	<p>
		<label for="cpc_number">FBG Contract Number: </label>
		<input id="cpc_number" name="cpc_number" type="text" value="<?php echo $cpc_number;?>" class="validate[required]" />
		<span class="field_desc">Enter the FBG Contract Number for this FBG</span>
	</p>
	<p>
		<label for="group_name">FBG Name: </label>
		<input id="group_name" name="group_name" type="text" value="<?php echo $group_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this group</span>
	</p>
	<p>
		<label for="chairman_name">Group Chairman's Name: </label>
		<input id="chairman_name" name="chairman_name" type="text" value="<?php echo $chairman_name;?>"/>
		<span class="field_desc">Enter the name of this group's Chairman</span>
	</p>
	</p>
	<p>
		<label for="chairman_phone">Group Chairman's Phone: </label>
		<input id="chairman_phone" name="chairman_phone" type="text" value="<?php echo $chairman_phone;?>"/>
		<span class="field_desc">Enter the phone number of this group's Chairman</span>
	</p>
	</p>
	<p>
		<label for="secretary_name">Group Secretary's Name: </label>
		<input id="secretary_name" name="secretary_name" type="text" value="<?php echo $secretary_name;?>"/>
		<span class="field_desc">Enter the name of this group's Secretary</span>
	</p>
	</p>
	<p>
		<label for="secretary_phone">Group Secretary's Name: </label>
		<input id="secretary_phone" name="secretary_phone" type="text" value="<?php echo $secretary_phone;?>"/>
		<span class="field_desc">Enter the phone number of this group's Secretary</span>
	</p>
	<p>
		<label for="field_officer">Field Extension Officer</label>
		<select name="field_officer" class="dropdown validate[required]" id="feo">
			<option></option>
			<?php
foreach($field_officers as $officer){
			?>
			<option value="<?php echo $officer -> id;?>" <?php
			if ($officer -> id == $field_officer) {echo "selected";
			}
			?>><?php echo $officer -> Officer_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the FEO who recruited this FBG</span>
	</p>
	<p>
		<label for="hectares_available">Hectares Available: </label>
		<input id="hectares_available" name="hectares_available" type="text" value="<?php echo $hectares_available;?>" class="validate[required]"/>
		<span class="field_desc">Enter the hectares offered by this FBG</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form> 