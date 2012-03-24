<script type="text/javascript">
	$(function() {
		$("#add_fbg_input").validationEngine();
	});

</script>
<?php
if (isset($fbg)) {
	$gd_id = $fbg -> GD_Id;
	$cpc_number = $fbg -> CPC_Number;
	$group_name = $fbg -> Group_Name; 
	$distributor = $fbg -> Distributor;
	$hectares_available = $fbg -> Hectares_Available;
	$fbg_id = $fbg -> id;
} else {
	$gd_id = "";
	$cpc_number = "";
	$group_name = ""; 
	$distributor = "";
	$hectares_available = "";
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
		<label for="gd_id">GD Id: </label>
		<input id="gd_id" name="gd_id" type="text" value="<?php echo $gd_id;?>" class="validate[required]" />
		<span class="field_desc">Enter the GD Id for this FBG</span>
	</p>
		<p>
		<label for="cpc_number">CPC Number: </label>
		<input id="cpc_number" name="cpc_number" type="text" value="<?php echo $cpc_number;?>" class="validate[required]" />
		<span class="field_desc">Enter the CPC Number for this FBG</span>
	</p>
	<p>
		<label for="group_name">Group Name: </label>
		<input id="group_name" name="group_name" type="text" value="<?php echo $group_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the group name for this FBG</span>
	</p>
	<p>
		<label for="distributor">Distributor</label>
		<select name="distributor" class="dropdown validate[required]" id="distributor">
			<option></option>
			<?php
foreach($distributors as $distributor_object){
			?>
			<option value="<?php echo $distributor_object -> id;?>" <?php
			if ($distributor_object -> id == $distributor) {echo "selected";
			}
			?>><?php echo $distributor_object -> First_Name." ".$distributor_object -> Surname;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the distributor responsible for this FBG</span>
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