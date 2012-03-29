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
		Add a New Route
	</legend>
	<p>
			<label for="cpc">Select Buyer: </label>
			<select>
				<option></option>
				<option>Thiga Memia</option>
				<option>Daniel Mwangi</option>
				<option>Peter Manjeru</option>
			</select>
			<span class="field_desc">Select the buyer responsible for this route</span>
		</p>
				<p>
			<label for="cpc">Route Code: </label>
			<input class="cpc" name="cpc" type="text" value=" " />
			<span class="field_desc">Enter the code for this route</span>
		</p>
		<p>
			<label for="cpc">Route Name: </label>
			<input class="cpc" name="cpc" type="text" value=" " />
			<span class="field_desc">Enter the name for this route</span>
		</p>
		<p>
			<label for="cpc">Depot Code: </label>
			<input class="cpc" name="cpc" type="text" value=" " />
			<span class="field_desc">Enter the code for the depot on this route</span>
		</p>
		<p>
			<label for="cpc">Depot Name: </label>
			<input class="cpc" name="cpc" type="text" value=" " />
			<span class="field_desc">Enter the name for the depot on this route</span>
		</p>
	<table class="normal" id="inputs_table" style="margin:0 auto;">
		<caption>
			Areas along this route
		</caption>
		<thead>
			<tr>
				<th>Area</th>
				 
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr input_row="1">
				 
				<td>
				<select name="farm_input[]" id="farm_input" class="dropdown farm_input validate[required]" style="width: 70px; padding:2px;">
					<option></option>
					<?php
foreach($areas as $area){
					?>
					<option value="<?php echo $area -> id;?>" ><?php echo $area -> Area_Name;?></option>
					<?php }?>
				</select></td>
				 
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