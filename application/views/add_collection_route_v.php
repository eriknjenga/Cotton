<script type="text/javascript">
	$(function() {
		$("#route_form").validationEngine();
	});

</script>
<?php
if (isset($route)) {
	$route_code = $route -> Route_Code;
	$route_name = $route -> Route_Name;
	$route_id = $route -> id;
} else {
	$route_code = "";
	$route_name = "";
	$route_id = "";

}
$attributes = array("method" => "post", "id" => "route_form");
echo form_open('collection_route_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>

<!-- End of fieldset -->
<!-- Fieldset -->
<fieldset>
	<legend>
		Add a New Collection Route
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
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>