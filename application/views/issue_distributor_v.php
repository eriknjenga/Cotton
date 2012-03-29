<script type="text/javascript">
	$(function() {
		$("#disbursement_form").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
		$(".farm_input").change(function() {
			$(this).closest("tr").find(".total_value").attr("value", "");
			var quantity = $(this).closest("tr").find(".quantity").attr("value");
			var total_value = 0;
			if(parseInt(quantity) >= 0 && parseInt($(this).find(":selected").attr("price")) > 0) {
				total_value = quantity * $(this).find(":selected").attr("price");
				$(this).closest("tr").find(".total_value").attr("value", total_value);
			}
		});
		$(".quantity").keyup(function() {
			$(this).closest("tr").find(".total_value").attr("value", "");
			var price = $(this).closest("tr").find(".farm_input").find(":selected").attr("price");
			var total_value = 0;
			if(parseInt(price) >= 0 && parseInt($(this).attr("value")) > 0) {
				total_value = price * $(this).attr("value");
				$(this).closest("tr").find(".total_value").attr("value", total_value);
			}
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
</script>
<?php
$attributes = array("method" => "post", "id" => "disbursement_form");
echo form_open('disbursement_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>

<!-- End of fieldset -->
<!-- Fieldset -->
<fieldset>
	<legend>
		Disburse Farm Inputs
	</legend>
	<label for="distributor">Select Distributor</label>
	<select name="distributor" id="distributor" class="dropdown distributor validate[required]" style="width: 70px; padding:2px;">
		<option></option>
		<?php
foreach($distributors as $distributor){
		?>
		<option value="<?php echo $distributor -> id;?>" ><?php echo $distributor -> First_Name." ".$distributor -> Surname;?></option>
		<?php }?>
	</select>
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
				<input id="invoice_number" name="invoice_number[]" type="text"   class="invoice_number validate[required]" style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="date validate[required]" id="date" name="date[]" type="text"   style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<select name="farm_input[]" id="farm_input" class="dropdown farm_input validate[required]" style="width: 70px; padding:2px;">
					<option></option>
					<?php
foreach($farm_inputs as $farm_input_object){
					?>
					<option value="<?php echo $farm_input_object -> id;?>" price="<?php echo $farm_input_object -> Unit_Price;?>"><?php echo $farm_input_object -> Product_Name;?></option>
					<?php }?>
				</select></td>
				<td>
				<input class="quantity validate[required,number]" name="quantity[]" id="quantity"  type="text"   style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input readonly="" class="total_value" name="total_value[]" id="total_value" type="text"   style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="season validate[required]" name="season[]" id="season" type="text"   style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="gd_batch" name="gd_batch[]" id="gd_batch" type="text"   style="width: 40px; padding:2px;"/>
				</td>
				<td>
				<input class="id_batch" name="id_batch[]" id="id_batch" type="text"   style="width: 40px; padding:2px;"/>
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