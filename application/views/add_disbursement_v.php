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
if (isset($disbursement)) {
	$fbg_id = $disbursement -> FBG;
	$fbg = $disbursement -> FBG_Object;
	$invoice_number = $disbursement -> Invoice_Number;
	$date = $disbursement -> Date;
	$farm_input = $disbursement -> Farm_Input;
	$quantity = $disbursement -> Quantity;
	$total_value = $disbursement -> Total_Value;
	$season = $disbursement -> Season;
	$gd_batch = $disbursement -> GD_Batch;
	$id_batch = $disbursement -> ID_Batch;
	$disbursement_id = $disbursement -> id;
	$agent = $disbursement -> Agent;

} else {
	$fbg_id = "";
	$invoice_number = "";
	$date = "";
	$farm_input = "";
	$quantity = "";
	$total_value = "";
	$season = "";
	$gd_batch = "";
	$id_batch = "";
	$disbursement_id = "";
	$agent = "";

}
$attributes = array("method" => "post", "id" => "disbursement_form");
echo form_open('disbursement_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Details of <b><?php echo $fbg -> Group_Name;?></b>
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $disbursement_id;?>" />
	<input type="hidden" name="fbg" value="<?php echo $fbg -> id;?>" />
	<input type="hidden" value="" id="product_price"/>
	<p>
		<label><b>Contract Number</b> </label>
		<label><?php echo $fbg -> CPC_Number;?></label>
	</p>
	<p>
		<label><b>Group Name</b> </label>
		<label><?php echo $fbg -> Group_Name;?></label>
		<label><b>Hectares Available</b> </label>
		<label><?php echo $fbg -> Hectares_Available;?></label>
	</p>
	<p>
		<label><b>Field Officer Code</b> </label>
		<label><?php echo $fbg -> Officer_Object -> Officer_Code;?></label>
		<label><b>Field Officer Name</b> </label>
		<label><?php echo $fbg -> Officer_Object -> Officer_Name;?></label>
	</p>
</fieldset>
<!-- End of fieldset -->
<!-- Fieldset -->
<fieldset>
	<legend>
		Disburse Farm Inputs
	</legend>
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
	<table class="normal" id="inputs_table" style="margin:0 auto;">
		<caption>
			Farm Inputs Loaned
		</caption>
		<thead>
			<tr>
				<th>Invoice No.</th>
				<th>Date</th>
				<th>Input Name</th>
				<th>Quantity</th>
				<th>Total Value</th>
				<th>Season</th>
				<th>GD Batch</th>
				<th>ID Batch</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr input_row="1">
				<td>
				<input id="invoice_number" name="invoice_number[]" type="text" value="<?php echo $invoice_number;?>" class="invoice_number validate[required]" style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="date validate[required]" id="date" name="date[]" type="text" value="<?php echo $date;?>" style="width: 40px; padding:2px;"/>
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
				<input class="quantity validate[required,number]" name="quantity[]" id="quantity"  type="text" value="<?php echo $quantity;?>" style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input readonly="" class="total_value" name="total_value[]" id="total_value" type="text" value="<?php echo $total_value;?>" style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="season validate[required]" name="season[]" id="season" type="text" value="<?php echo $season;?>" style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="gd_batch" name="gd_batch[]" id="gd_batch" type="text" value="<?php echo $gd_batch;?>" style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="id_batch" name="id_batch[]" id="id_batch" type="text" value="<?php echo $id_batch;?>" style="width: 40px; padding:2px;"/>
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