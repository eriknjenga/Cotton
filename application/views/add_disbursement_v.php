<script type="text/javascript">
	$(function() {
		$("#disbursement_form").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
		$(".farm_input").change(function() {
			updateInputPrice($(this));
			limitFarmInputs();
		});
		$(".farmer_farm_input").change(function() {
			console.log($(this));
			updateFarmerInputPrice($(this));
		});
		$(".quantity").keyup(function() {
			var input_object = $(this).closest("tr").find(".farm_input");
			updateInputPrice(input_object);
		});
		$(".farmer_quantity").keyup(function() {
			var input_object = $(this).closest("tr").find(".farmer_farm_input");
			updateFarmerInputPrice(input_object);
		});
		$(".date").change(function() {
			var farm_input = $(this).closest("tr").find(".farm_input");
			$.each($(".farm_input"), function() {
				updateInputPrice($(this));
			});
			limitFarmInputs();
			$.each($(".farmer_farm_input"), function() {
				updateFarmerInputPrice($(this));
			});
		});
		$(".add").click(function() {
			var cloned_object = $('#inputs_table tr:last').clone(true);
			var input_row = cloned_object.attr("input_row");
			var next_input_row = parseInt(input_row) + 1;
			cloned_object.attr("input_row", next_input_row);
			var invoice_id = "invoice_number_" + next_input_row;
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
			cloned_object.insertAfter('#inputs_table tr:last');
			return false;
		});
		$(".farmer_add").click(function() {
			var cloned_object = $('#farmers_table tr:last').clone(true);
			var farmer_row = cloned_object.attr("farmer_row");
			var next_farmer_row = parseInt(farmer_row) + 1;
			cloned_object.attr("farmer_row", next_farmer_row);
			var farmer_id = "farmer_" + next_farmer_row;
			var farmer_farm_input_id = "farmer_farm_input_" + next_farmer_row;
			var farmer_quantity_id = "farmer_quantity_" + next_farmer_row;
			var farmer_total_value_id = "farmer_total_value_" + next_farmer_row;
			cloned_object.find(".farmer").attr("id", farmer_id);
			cloned_object.find(".farmer_farm_input").attr("id", farmer_farm_input_id);
			cloned_object.find(".farmer_quantity").attr("id", farmer_quantity_id);
			cloned_object.find(".farmer_total_value").attr("id", farmer_quantity_id);
			cloned_object.find(".farmer_quantity").attr("value", "");
			cloned_object.find(".farmer_total_value").attr("value", "");
			cloned_object.insertAfter('#farmers_table tr:last');
			return false;
		});
	});
	function limitFarmInputs() {
		//Empty list of inputs for farmers
		$('.farmer_farm_input > option').each(function() {
			$(this).remove();
		});
		$(".farmer_farm_input").append("<option></option>");
		//Loop through selected list of farm inputs
		$('.farm_input > option:selected').each(function() {
			$(this).clone().appendTo('.farmer_farm_input');
		});
	}

	function updateInputPrice(input_object) {
		var date_object = $("#date");
		var prices = input_object.find(":selected").attr("prices");
		var price_dates = input_object.find(":selected").attr("price_dates");
		if(prices == null || price_dates == null) {
			return;
		}
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
		input_object.find(":selected").attr("current_price", most_current_price);
		//Clear out the 'total value' field
		input_object.closest("tr").find(".total_value").attr("value", "");
		var quantity = input_object.closest("tr").find(".quantity").attr("value");
		var total_value = 0;
		if(parseInt(quantity) >= 0 && parseInt(most_current_price) > 0) {
			total_value = quantity * most_current_price;
			input_object.closest("tr").find(".total_value").attr("value", total_value);
		}
	}

	function updateFarmerInputPrice(input_object) {
		//Clear out the 'total value' field
		input_object.closest("tr").find(".farmer_total_value").attr("value", "");
		var quantity = input_object.closest("tr").find(".farmer_quantity").attr("value");
		var most_current_price = input_object.find(":selected").attr("current_price");
		var total_value = 0;
		if(parseInt(quantity) >= 0 && parseInt(most_current_price) > 0) {
			total_value = quantity * most_current_price;
			input_object.closest("tr").find(".farmer_total_value").attr("value", total_value);
		}
	}
</script>
<?php
if (isset($disbursement)) {
$fbg_id = $disbursement -> FBG;
$fbg_name = $disbursement -> FBG_Object -> Group_Name;
$invoice_number = $disbursement -> Invoice_Number;
$date = $disbursement -> Date;
$farm_input = $disbursement -> Farm_Input;
$quantity = $disbursement -> Quantity;
$total_value = $disbursement -> Total_Value;
$season = $disbursement -> Season;
$disbursement_id = $disbursement -> id;
$agent = $disbursement -> Agent;

} else {
$fbg_name = $fbg -> Group_Name;
$fbg_id = $fbg -> id;
$invoice_number = "";
$date = "";
$farm_input = "";
$quantity = "";
$total_value = "";
$season = "";
$disbursement_id = "";
$agent = "";

}
$attributes = array("method" => "post", "id" => "disbursement_form");
echo form_open('disbursement_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
if(isset($batch_information)){
?>
<div class="message information close">
	<h2>Batch Information</h2>
	<p>
		<?php echo $batch_information;?>
	</p>
</div>
<?php }?>
<!-- End of fieldset -->
<!-- Fieldset -->
<fieldset>
	<legend>
		Disburse Farm Inputs to <b><?php echo $fbg_name;?></b>
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $disbursement_id;?>" />
	<input type="hidden" name="fbg" value="<?php echo $fbg_id;?>" />
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
		<span class="field_desc">Select the agent who delivered inputs to this FBG</span>
	</p>
	<p>
		<label for="invoice_number">Invoice Number </label>
		<input id="invoice_number" name="invoice_number" type="text" value="<?php echo $invoice_number;?>" class="validate[required]" />
		<span class="field_desc">Enter the <b>Invoice Number</b> for this transaction</span>
	</p>
	<p>
		<label for="date">Transaction Date</label>
		<input class="date validate[required]" id="date" name="date" type="text" value="<?php echo $date;?>"/>
		<span class="field_desc">Enter the <b>Date</b> for this transaction</span>
	</p>
	<table class="normal" id="inputs_table" style="margin:0 auto;">
		<caption>
			Farm Inputs Loaned
		</caption>
		<thead>
			<tr>
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
				<select name="farm_input[]" id="farm_input" class="dropdown farm_input validate[required]" style="width: 70px; padding:2px;">
					<option></option>
					<?php
foreach($farm_inputs as $farm_input_object){
					?>
					<option prices="<?php
					foreach ($farm_input_object->Prices as $price) {echo $price -> Price . ',';
					}
					?>" price_dates="<?php
					foreach ($farm_input_object->Prices as $price) {echo date('m/d/Y', $price -> Timestamp) . ',';
					}
					?>"
					value="<?php echo $farm_input_object -> id;?>" <?php
					if ($farm_input_object -> id == $farm_input) {echo "selected";
					}
					?> ><?php echo $farm_input_object -> Product_Name;?></option>
					<?php }?>
				</select></td>
				<td>
				<input class="quantity validate[required,number]" name="quantity[]" id="quantity"  type="text" value="<?php echo $quantity;?>" style="width: 60px; padding:2px;"/>
				</td>
				<td>
				<input readonly="" class="total_value" name="total_value[]" id="total_value" type="text" value="<?php echo $total_value;?>" style="width: 60px; padding:2px;"/>
				</td>
				<td>
				<input class="season validate[required]" name="season[]" id="season" type="text" value="<?php echo $season;?>" style="width: 60px; padding:2px;"/>
				</td>
				<td>
				<input  class="add button"   value="+" style="width:20px; text-align: center"/>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="normal" id="farmers_table" style="margin:0 auto;">
		<caption>
			Farmers Loaned
		</caption>
		<thead>
			<tr>
				<th>Farmer</th>
				<th>Input Name</th>
				<th>Quantity</th>
				<th>Total Value</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			//Check if we are editing and if so, add the relevant rows
			?>
			<tr farmer_row="1">
				<td>
				<input class="farmer validate[required]" name="farmer[]" id="farmer" type="text" value="<?php echo "";?>"/>
				</td>
				<td>
				<select name="farmer_farm_input[]" id="farmer_farm_input" class="dropdown farmer_farm_input validate[required]" style="width: 70px; padding:2px;">
					<option></option>
				</select></td>
				<td>
				<input class="farmer_quantity validate[required,number]" name="farmer_quantity[]" id="farmer_quantity"  type="text" value="<?php echo $quantity;?>" style="width: 60px; padding:2px;"/>
				</td>
				<td>
				<input readonly="" class="farmer_total_value" name="farmer_total_value[]" id="farmer_total_value" type="text" value="<?php echo $total_value;?>" style="width: 60px; padding:2px;"/>
				</td>
				<td>
				<input  class="farmer_add button"   value="+" style="width:20px; text-align: center"/>
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<input class="button" type="reset" value="Reset">
		<input class="button" type="submit" value="Save & Add New" name="submit">
		<input class="button" type="submit" value="Save & View List" name="submit">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>