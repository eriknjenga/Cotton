<script type="text/javascript">
	$(function() {
		$("#add_depot_input").validationEngine();
	});

</script>
<?php
if (isset($depot)) {
	$depot_code = $depot -> Depot_Code;
	$depot_name = $depot -> Depot_Name;
	$buyer = $depot -> Buyer;
	$region = $depot -> Region;
	$capacity = $depot -> Capacity;
	$distance = $depot -> Distance;
	$depot_id = $depot -> id;
} else {
	$depot_code = "";
	$depot_name = "";
	$buyer = "";
	$region = "";
	$capacity = "";
	$distance = "";
	$depot_id = "";

}
$attributes = array("method" => "post", "id" => "add_depot_input");
echo form_open('depot_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Add new Buying Center
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $depot_id;?>" />
	<p>
		<label for="depot_code">Code: </label>
		<input id="depot_code"  name="depot_code" type="text"  value="<?php echo $depot_code;?>" class="validate[required]"/>
		<span class="field_desc">Enter the code for this Buying Center</span>
	</p>
	<p>
		<label for="depot_name">Name: </label>
		<input id="depot_name" name="depot_name" type="text" value="<?php echo $depot_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Buying Center</span>
	</p>
	<p>
		<label for="capacity">Capacity (Tonnes): </label>
		<input id="capacity" name="capacity" type="text" value="<?php echo $capacity;?>"/>
		<span class="field_desc">Enter the tonnage capacity for this Buying Center</span>
	</p>
	<p>
		<label for="distance">Distance (KM): </label>
		<input id="distance" name="distance" type="text" value="<?php echo $distance;?>"/>
		<span class="field_desc">Enter the distance of this buying center from the ginnery</span>
	</p>
	<p>
		<label for="buyer">Buyer</label>
		<select name="buyer" class="dropdown validate[required]" id="buyer">
			<option></option>
			<?php
foreach($buyers as $buyer_object){
			?>
			<option value="<?php echo $buyer_object -> id;?>" <?php
			if ($buyer_object -> id == $buyer) {echo "selected";
			}
			?>><?php echo $buyer_object -> Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the buyer responsible for this Buying Center</span>
	</p>
	<p>
		<label for="region">Zone</label>
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
		<span class="field_desc">Select this Buying Center's Zone</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>