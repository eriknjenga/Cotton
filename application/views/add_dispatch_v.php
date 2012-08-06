<script type="text/javascript">
	$(function() {
		$("#dispatch_form").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script>
<div id="filter">
	<?php
	if (isset($dispatch)) {
		$depot_id = $dispatch -> Depot;
		$date = $dispatch -> Date;
		$truck = $dispatch -> Truck;
		$dispatch_id = $dispatch -> id;
	} else {
		$depot_id = "";
		$date = "";
		$truck = "";
		$dispatch_id = "";
	}
	$attributes = array("method" => "POST", "id" => "dispatch_form");

	echo form_open('truck_dispatch_management/save', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<input type="hidden" name="editing_id" value="<?php echo $dispatch_id;?>" />
		<p>
			<label for="depot">Buying Center</label>
			<select name="depot" id="depot" class="validate[required]">
				<option></option>
				<?php
foreach($depots as $depot){
				?>
				<option value="<?php echo $depot -> id;?>" <?php
				if ($depot -> id == $depot_id) {echo "selected";
				}
				?>><?php echo $depot -> Depot_Name . " (" . $depot -> Depot_Code . ")";?></option>
				<?php }?>
			</select>
		</p>
		<p>
			<label for="date">Dispatch Date</label>
			<input id="date" name="date" type="text" value="<?php echo $date;?>" class="validate[required]"/>
		</p>
		<p>
			<label for="truck">Truck Number</label>
			<select name="truck" id="truck" class="validate[required]">
				<option></option>
				<?php
foreach($trucks as $truck_object){
				?>
				<option value="<?php echo $truck_object -> id;?>" <?php
				if ($truck_object -> id == $truck) {echo "selected";
				}
				?>><?php echo $truck_object -> Number_Plate . " (" . $truck_object -> Capacity . "T)";?></option>
				<?php }?>
			</select>
		</p>
		<input type="submit" name="assign" class="button"	value="Save Dispatch Info" />
	</fieldset>
	</form>
</div>
