<script type="text/javascript">
	$(function() {
		$("#route_form").validationEngine();
		$(".add").click(function() {
			var cloned_object = $('#depots_table tr:last').clone(true);
			var depot_row = cloned_object.attr("depot_row");
			var next_depot_row = parseInt(depot_row) + 1;
			cloned_object.attr("depot_row", next_depot_row);
			var depot_id = "depot_" + next_depot_row; 
			cloned_object.find(".depot").attr("id", depot_id);
			cloned_object.insertAfter('#depots_table tr:last');
			return false;
		});
	});

</script>
<?php
if (isset($route)) {
	$route_code = $route -> Route_Code;
	$route_name = $route -> Route_Name;
	$field_cashier = $route -> Field_Cashier;
	$route_id = $route -> id;
} else {
	$route_code = "";
	$route_name = "";
	$field_cashier = "";
	$route_id = "";

}
$attributes = array("method" => "post", "id" => "route_form");
echo form_open('route_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>

<!-- End of fieldset -->
<!-- Fieldset -->
<fieldset>
	<legend>
		Add a New Route
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $route_id;?>" />
	<p>
		<label for="route_code">Route Code: </label>
		<input id="route_code"  name="route_code" type="text"  value="<?php echo $route_code;?>" class="validate[required]"/>
		<span class="field_desc">Enter the code for this Route</span>
	</p>
	<p>
		<label for="route_name">Route Name: </label>
		<input id="route_name"  name="route_name" type="text"  value="<?php echo $route_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Route</span>
	</p>
	<p>
		<label for="field_cashier">Field Cashier</label>
		<select name="field_cashier" class="dropdown" id="field_cashier">
			<option></option>
			<?php

foreach($field_cashiers as $field_cashier_object){
			?>
			<option value="<?php echo $field_cashier_object -> id;?>" <?php
			if ($field_cashier_object -> id == $field_cashier) {echo "selected";
			}
			?>><?php echo $field_cashier_object -> Field_Cashier_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select this route's field cashier</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>