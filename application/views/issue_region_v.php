<script type="text/javascript">
	$(function() {
		$("#disbursement_form").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
		$(".farm_input").change(function() {
			var date_object = $(this).closest("tr").find(".date");
			updateInputPrice($(this), date_object);
		});
		$(".quantity").keyup(function() {
			var date_object = $(this).closest("tr").find(".date");
			var input_object = $(this).closest("tr").find(".farm_input");
			updateInputPrice(input_object, date_object);
		});
		$(".date").change(function() {
			var farm_input = $(this).closest("tr").find(".farm_input");
			updateInputPrice(farm_input, $(this));
		});
		$(".add").click(function() {
			var cloned_object = $('#inputs_table tr:last').clone(true);
			var input_row = cloned_object.attr("input_row");
			var next_input_row = parseInt(input_row) + 1;
			cloned_object.attr("input_row", next_input_row);
			var invoice_id = "invoice_number_" + next_input_row;
			var date_id = "date_" + next_input_row;
			var farm_input_id = "farm_input_" + next_input_row;
			var quantity_id = "quantity_" + next_input_row;
			var total_value_id = "total_value_" + next_input_row;
			var season_id = "season_" + next_input_row;
			cloned_object.find(".invoice_number").attr("id", invoice_id);
			cloned_object.find(".farm_input").attr("id", farm_input_id);
			cloned_object.find(".quantity").attr("id", quantity_id);
			cloned_object.find(".total_value").attr("id", total_value_id);
			cloned_object.find(".quantity").attr("value", "");
			cloned_object.find(".total_value").attr("value", "");
			cloned_object.find(".season").attr("id", season_id);
			var date_selector = "#" + date_id;

			$(date_selector).datepicker({
				defaultDate : new Date(),
				changeYear : true,
				changeMonth : true
			});
			cloned_object.insertAfter('#inputs_table tr:last');
			refreshDatePickers();
			return false;
		});
	});
	function refreshDatePickers() {
		var counter = 0;
		$('.date').each(function() {
			var new_id = "date_" + counter;
			$(this).attr("id", new_id);
			$(this).datepicker("destroy");
			$(this).not('.hasDatePicker').datepicker();
			counter++;

		});
	}

	function updateInputPrice(input_object, date_object) {
		var prices = input_object.find(":selected").attr("prices");
		var price_dates = input_object.find(":selected").attr("price_dates");
		var price_dates_array = price_dates.split(",");
		var prices_array = prices.split(",");
		var selected_date_value = date_object.attr("value");
		var selected_date = new Date(selected_date_value);
		var difference = 0;
		var most_current_price = 0;
		var counter = 0;
		$.each(price_dates_array, function() {
			if(this.length > 0 && selected_date_value.length > 0) {
				var price_date = new Date(this);
				var day_difference = Math.floor((selected_date - price_date) / 86400000);
				if(day_difference >= 0 && (difference == 0 || day_difference < difference)) {
					difference = day_difference;
					most_current_price = prices_array[counter];
				}
			}
			counter++;
		});
		//Clear out the 'total value' field
		input_object.closest("tr").find(".total_value").attr("value", "");
		var quantity = input_object.closest("tr").find(".quantity").attr("value");
		var total_value = 0;
		if(parseInt(quantity) >= 0 && parseInt(most_current_price) > 0) {
			total_value = quantity * most_current_price;
			input_object.closest("tr").find(".total_value").attr("value", total_value);
		}
	}
</script>
<?php
if (isset($issue)) {
	$delivery_note_number = $issue -> Delivery_Note_Number;
	$date = $issue -> Date;
	$farm_input = $issue -> Farm_Input;
	$quantity = $issue -> Quantity;
	$total_value = $issue -> Total_Value;
	$season = $issue -> Season;
	$issue_id = $issue -> id;
	$agent = $issue -> Agent;
	$region = $issue -> Region;

} else {
	$delivery_note_number = "";
	$date = "";
	$farm_input = "";
	$quantity = "";
	$total_value = "";
	$season = "";
	$issue_id = "";
	$agent = "";
	$region = "";

}
$attributes = array("method" => "post", "id" => "disbursement_form");
echo form_open('region_input_issue_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- End of fieldset -->
<!-- Fieldset -->
<fieldset>
	<legend>
		Issue Farm Inputs to Region
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $issue_id;?>" />
	<p>
		<label for="agent">Agent</label>
		<select name="agent" class="dropdown validate[required]" id="agent">
			<option></option>
			<?php
foreach($agents as $agent_object){
			?>
			<option value="<?php echo $agent_object -> id;?>" <?php
			if ($agent_object -> id == $agent) {echo "selected";
			}
			?>><?php echo $agent_object -> First_Name . " " . $agent_object -> Surname;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the agent who delivered these inputs</span>
	</p>
	<p>
		<label for="region">Region</label>
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
		<span class="field_desc">Select the region that these inputs were delivered</span>
	</p>

	<p>
		<label for="delivery_note_number">Delivery Note Number: </label>
		<input id="delivery_note_number" name="delivery_note_number" type="text" value="<?php echo $delivery_note_number;?>" class="validate[required]" />
		<span class="field_desc">Enter the <b>Delivery Note Number</b> for this transaction</span>
	</p>
	<table class="normal" id="inputs_table" style="margin:0 auto;">
		<caption>
			Farm Inputs Issued
		</caption>
		<thead>
			<tr>
				
				<th>Date</th>
				<th>Input Name</th>
				<th>Quantity</th>
				<th>Total Value</th>
				<th>Season</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr input_row="1">
				<td>
				<input class="date validate[required]" id="date" name="date[]" type="text" value="<?php echo $date;?>" style="width: 100px; padding:2px;"/>
				</td>
				<td>
				<select name="farm_input[]" id="farm_input" class="dropdown farm_input validate[required]" style="width: 70px; padding:2px;">
					<option></option>
					<?php
foreach($farm_inputs as $farm_input_object){
					?>
					<option prices="<?php
					foreach ($farm_input_object->Prices as $price) {echo $price -> Price . ',';
					}
					?>" price_dates="<?php
					foreach ($farm_input_object->Prices as $price) {echo date('d/m/Y', $price -> Timestamp) . ',';
					}
					?>"
					value="<?php echo $farm_input_object -> id;?>" <?php
					if ($farm_input_object -> id == $farm_input) {echo "selected";
					}
					?> ><?php echo $farm_input_object -> Product_Name;?></option>
					<?php }?>
				</select></td>
				<td>
				<input class="quantity validate[required,number]" name="quantity[]" id="quantity"  type="text" value="<?php echo $quantity;?>" style="width: 100px; padding:2px;"/>
				</td>
				<td>
				<input readonly="" class="total_value" name="total_value[]" id="total_value" type="text" value="<?php echo $total_value;?>" style="width: 100px; padding:2px;"/>
				</td>
				<td>
				<input class="season validate[required]" name="season[]" id="season" type="text" value="<?php echo $season;?>" style="width: 100px; padding:2px;"/>
				</td>
				<td>
				<input  class="add button"   value="+" style="width:20px; text-align: center"/>
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>