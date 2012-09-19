<style>
	.truck_selection option {
		color: green;
	}
	.truck_selection option.red {
		color: red;
	}
</style>
<?php
$attributes = array("method" => "POST", "id" => "dispatch_form");

echo form_open('truck_dispatch_management/save_batch', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Buying Center</th>
			<th>Center Code</th>
			<th>Route</th>
			<th>Distance</th>
			<th>Capacity (Tonnes)</th>
			<th>Product Balance</th>
			<th>Ratio</th>
			<th>Days Pending</th>
			<th>Truck</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($recommendation[0])) {
$counter = 1;
foreach ($recommendation as $center_recommendation) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
$days_pending = '';
if ($center_recommendation['days_since_closure'] != null) {
$days_pending = $center_recommendation['days_since_closure'];
$days_pending += 1;
} else if ($center_recommendation['days_since_last_purchase'] != null) {
$days_pending = $center_recommendation['days_since_last_purchase'];
$days_pending += 1;
}

		?><tr class="<?php echo $class;?>
		">
		<td>
		<input type="hidden" name="depot[]" value="<?php echo $center_recommendation['depot_id'] ?>" />
		<?php echo $center_recommendation['depot_name'];?>
		</td>
		<td>
		<?php echo $center_recommendation['depot_code'];?>
		</td>
		<td>
		<?php echo $center_recommendation['purchase_route'];?>
		</td>
		<td>
		<?php echo $center_recommendation['distance'];?>
		</td>
		<td>
		<?php echo $center_recommendation['depot_capacity'];?>
		</td>
		<td>
		<?php echo $center_recommendation['product_balance'];?>
		</td>
		<td>
		<?php echo number_format(($center_recommendation['ratio']+0),2);?>
		</td>
		<td>
		<?php echo $days_pending;?>
		</td>
		<td>
		<select class="truck_selection" name="truck_choice[]">
		<option></option>
		<?php foreach($trucks as $truck){?>
		<option <?php
		if ($center_recommendation['product_balance'] > ($truck -> Capacity * 1000)) {echo "disabled class='red'";
		}
		?> value="<?php echo $truck -> id;?>"><?php echo $truck -> Number_Plate . " (" . $truck -> Capacity . ")";?></option>
		<?php }?>
		</select>
		</td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>
<input type="submit" name="assign" class="button"	value="Save Dispatch Info" />
</form>