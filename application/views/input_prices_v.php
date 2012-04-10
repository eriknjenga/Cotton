<script type="text/javascript">
	$(document).ready(function() {
		refreshDatePickers();
		$(".add").click(function() { 
			var cloned_object = $('#prices tr:last').clone(true);
			var price_row = cloned_object.attr("price_row");
			var next_price_row = parseInt(price_row) + 1; 
			cloned_object.attr("price_row", next_price_row);
			var date_id = "date_" + next_price_row;
			cloned_object.find(".date").attr("id", date_id);
			var date_selector = "#" + date_id;

			$(date_selector).datepicker({
				defaultDate : new Date(),
				changeYear : true,
				changeMonth : true
			});
			cloned_object.insertAfter('#prices tr:last');
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
$attributes = array("method" => "post", "id" => "input_prices_form");
echo form_open('farm_input_management/save_prices', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<h1>Unit Prices for <?php if(isset($input_prices[0])){}echo $input_prices[0]->Input->Product_Name;?></h1>
<fieldset>
	<input type="hidden" name="farm_input" value="<?php echo $input;?>" />
<table   id="prices">
	<th class="subsection-title" colspan="13">Input Prices</th>
	<tr>
		<th>Date</th>
		<th>Price</th>
		<th>Add New</th>
	</tr>
	<?php if(isset($input_prices[0])){
		$counter = 0;
foreach($input_prices as $input_price){

	?>
	<tr price_row="<?php echo $counter;?>">
		<td>
		<input type="text" name="dates[]" class="date" id="date_<?php echo $counter;?>" value="<?php echo date("m/d/Y",$input_price -> Timestamp);?>" />
		</td>
		<td>
		<input type="text" name="prices[]" class="price" value="<?php echo $input_price -> Price;?>"/>
		</td>
		<td>
		<input type="button" class="add button" value="Add"/>
		</td>
	</tr>
	<?php
	$counter++;
	}//end foreach loop
	}//endif
	else{
	?>
	<tr price_row="0">
		<td>
		<input type="text" name="dates[]" class="date" />
		</td>
		<td>
		<input type="text" name="prices[]" class="price" />
		</td>
		<td>
		<input type="button" class="add button" value="Add"/>
		</td>
	</tr>
	<?php
	}
	?>
</table>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>