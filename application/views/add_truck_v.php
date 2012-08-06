<script type="text/javascript">
	$(function() {
		$("#add_truck_input").validationEngine();
		$("#category").change(function() {
			if($(this).attr("value") == "1") {
				$("#agreed_rate").css("display", "none");
			} else if($(this).attr("value") == "2") {
				$("#agreed_rate").css("display", "block");
			}
		});
		$("#category").change();
	});

</script>
<?php
if (isset($truck)) {
	$number_plate = $truck -> Number_Plate;
	$category = $truck -> Category;
	$capacity = $truck -> Capacity;
	$agreed_rate = $truck -> Agreed_Rate;
	$truck_id = $truck -> id;
} else {
	$number_plate = "";
	$category = "";
	$capacity = "";
	$agreed_rate = "";
	$truck_id = "";
}
$attributes = array("method" => "post", "id" => "add_truck_input");
echo form_open('truck_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Truck
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $truck_id;?>" />
	<p>
		<label for="number_plate">Number Plate: </label>
		<input id="number_plate" name="number_plate" type="text" value="<?php echo $number_plate;?>" class="validate[required]" />
		<span class="field_desc">Enter the number plate for this truck</span>
	</p>
	<p>
		<label for="category">Category: </label>
		<select name="category"  class="validate[required]" id="category">
			<option value=""></option>
			<option value="1" <?php
				if ($category == "1") {echo "Selected";
				}
			?>>Alliance Truck</option>
			<option value="2" <?php
				if ($category == "2") {echo "Selected";
				}
			?>>Contracted Truck</option>
		</select>
		<span class="field_desc">Select the appropriate category for this truck</span>
	</p>
	<p>
		<label for="capacity">Capacity: </label>
		<input id="capacity" name="capacity" type="text" value="<?php echo $capacity;?>" class="validate[required]"/>
		<span class="field_desc">Enter the capacity for this truck</span>
	</p>
	<p id="agreed_rate" style="display: none">
		<label for="agreed_rate">Agreed Rate (/ton/km): </label>
		<input id="agreed_rate" name="agreed_rate" type="text" value="<?php echo $agreed_rate;?>" class="validate[required]"/>
		<span class="field_desc">Enter the charging rate to be charged per tonne per kilometer agreed with the truck owner</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form> 