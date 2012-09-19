<script type="text/javascript">
	$(function() {
		$("#disburse_cash_input").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			maxDate : 0
		});
	});

</script>
<style>
	.small_text {
		width: 90px;
	}
	.smaller_text {
		width: 38px;
	}
	.filler-space {
		width: 206px;
		text-align: center;
	}
</style>
<?php	
if (isset($summary)) {
	$depot = $summary -> Depot; 
	$summary_number = $summary -> Summary_Number;
	$summary_id = $summary -> id;
	$date = $summary -> Date;
	$opening_bags = $summary -> Opening_Bags; 
	$opening_stock = $summary -> Opening_Stocks;
	$opening_cash = $summary -> Opening_Cash;
	$bags_received = $summary -> Bags_Received;
	$cash_received = $summary -> Cash_Received; 
	$start_ppv = $summary -> Start_Ppv;
	$end_ppv = $summary -> End_Ppv;
	$purchase_quantity = $summary -> Purchase_Quantity;
	$purchase_value = $summary -> Purchase_Value; 
	$input_deductions = $summary -> Input_Deductions;
	$cotton_deliveries = $summary -> Cotton_Deliveries;
	$delivery_note = $summary -> Delivery_Note;
	$closing_bags = $summary -> Closing_Bags; 
	$closing_stock = $summary -> Closing_Stock;
	$closing_cash = $summary -> Closing_Cash;
	$prepared_by = $summary -> Prepared_By;
	$batch = $summary -> Batch;
	$batch_status = $summary -> Batch_Status; 
	$adjustment = $summary -> Adjustment;
} else {
	$depot = ""; 
	$summary_number = "";
	$summary_id = "";
	$date = "";
	$opening_bags = ""; 
	$opening_stock = "";
	$opening_cash = "";
	$bags_received = "";
	$cash_received = ""; 
	$start_ppv = "";
	$end_ppv = "";
	$purchase_quantity = "";
	$purchase_value = ""; 
	$input_deductions = "";
	$cotton_deliveries = "";
	$delivery_note = "";
	$closing_bags = ""; 
	$closing_stock = "";
	$closing_cash = "";
	$prepared_by = "";
	$batch = "";
	$batch_status = "";
	$adjustment = "0";

}
$attributes = array("method" => "post", "id" => "disburse_cash_input");
echo form_open('buying_center_summary_management/save', $attributes);
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
<!-- Fieldset -->
<fieldset>
	<legend>
		New Buying Center Summary
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $summary_id;?>" />
	<p>
		<label for="summary_number">BCS Number: </label>
		<input id="summary_number"  name="summary_number" type="text"  value="<?php echo $summary_number;?>" class="validate[required]"/>
		<span class="field_desc">Enter the BCS Number for this transaction</span>
	</p>
	<p>
		<label for="depot">Buying Center</label>
		<select name="depot" id="depot" class="validate[required]">
			<option></option>
			<?php
foreach($depots as $depot_object){
			?>
			<option value="<?php echo $depot_object -> id;?>" <?php
			if ($depot_object -> id == $depot) {echo "selected";
			}
			?>><?php echo $depot_object -> Depot_Name . " - " . $depot_object -> Depot_Code;?></option>
			<?php
			$counter++;
			}
			?>
		</select>
		<span class="field_desc">Select the buying center affected by this transaction</span>
	</p>
	<p>
		<label for="date">Summary Date: </label>
		<input id="date" readonly="" name="date" type="text"  value="<?php echo $date;?>" class="validate[required]"/>
		<span class="field_desc">Enter the transaction date</span>
	</p>
	<p>
		<label for="opening_bags" class="small_text">Bags B/F: </label>
		<input id="opening_bags"  name="opening_bags" type="text"  value="<?php echo $opening_bags;?>" class="small_text"/>
		<label for="opening_stock" class="small_text">Stock B/F: </label>
		<input id="opening_stock"  name="opening_stock" type="text"  value="<?php echo $opening_stock;?>" class="small_text"/>
		<label for="opening_cash" class="small_text">Cash B/F: </label>
		<input id="opening_cash"  name="opening_cash" type="text"  value="<?php echo $opening_cash;?>" class="small_text"/>
	</p>
	<p>
		<label for="bags_received" class="small_text">Bags Received: </label>
		<input id="bags_received"  name="bags_received" type="text"  value="<?php echo $bags_received;?>" class="small_text"/>
		<label class="filler-space">-</label>
		<label for="cash_received" class="small_text">Cash Received: </label>
		<input id="cash_received"  name="cash_received" type="text"  value="<?php echo $cash_received;?>" class="small_text"/>
	</p>
	<p>
		<label for="start_ppv" class="smaller_text">PPV From: </label>
		<input id="start_ppv"  name="start_ppv" type="text"  value="<?php echo $start_ppv;?>" class="smaller_text"/>
		<label for="end_ppv" class="smaller_text">PPV To: </label>
		<input id="end_ppv"  name="end_ppv" type="text"  value="<?php echo $end_ppv;?>" class="smaller_text"/>
		<label for="purchase_quantity" class="small_text">Purchase Kgs: </label>
		<input id="purchase_quantity"  name="purchase_quantity" type="text"  value="<?php echo $purchase_quantity;?>" class="small_text"/>
		<label for="purchase_value" class="small_text">Purchase Tsh: </label>
		<input id="purchase_value"  name="purchase_value" type="text"  value="<?php echo $purchase_value;?>" class="small_text"/>
	</p>
	<p>
		<label class="filler-space">-</label>
		<label class="filler-space">-</label>
		<label for="input_deductions" class="small_text">Input Deductions: </label>
		<input id="input_deductions"  name="input_deductions" type="text"  value="<?php echo $input_deductions;?>" class="small_text"/>
	</p>
	<p>
		<label for="delivery_note" class="small_text">D/N No: </label>
		<input id="delivery_note"  name="delivery_note" type="text"  value="<?php echo $delivery_note;?>" class="small_text"/>
		<label for="cotton_deliveries" class="small_text">Stock Delivered: </label>
		<input id="cotton_deliveries"  name="cotton_deliveries" type="text"  value="<?php echo $cotton_deliveries;?>" class="small_text"/>
		<label class="filler-space">-</label>
	</p>
	<p>
		<label for="closing_bags" class="small_text">Bags C/F: </label>
		<input id="closing_bags"  name="closing_bags" type="text"  value="<?php echo $closing_bags;?>" class="small_text"/>
		<label for="closing_stock" class="small_text">Stock C/F: </label>
		<input id="closing_stock"  name="closing_stock" type="text"  value="<?php echo $closing_stock;?>" class="small_text"/>
		<label for="closing_cash" class="small_text">Cash C/F: </label>
		<input id="closing_cash"  name="closing_cash" type="text"  value="<?php echo $closing_cash;?>" class="small_text"/>
	</p>
	<p>
		<label for="prepared_by">Prepared By: </label>
		<input id="prepared_by" name="prepared_by" type="text"  value="<?php echo $prepared_by;?>"/>
		<span class="field_desc">Enter the person who prepared this summary</span>
	</p>
	<p>
		<label for="adjustment">Adjustment Entry?</label>
		<input class="adjustment" name="adjustment" id="adjustment" type="checkbox" value="1" <?php
		if ($adjustment == '1') {echo "checked";
		}
		?>/>
	</p>
	<p>
		<input class="button" type="reset" value="Reset">
		<input class="button" type="submit" value="Save & Add New" name="submit">
		<input class="button" type="submit" value="Save & View List" name="submit">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>