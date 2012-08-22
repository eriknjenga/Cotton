<script type="text/javascript">
	$(function() {
		$("#cash_receipt_input").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script>
<?php
if (isset($receipt)) {
	$amount = $receipt -> Amount;
	$receipt_number = $receipt -> Receipt_Number;
	$field_cashier = $receipt -> Field_Cashier;
	$receipt_id = $receipt -> id;
	$date = $receipt -> Date;
	$adjustment = $receipt -> Adjustment;
} else {
	$amount = "";
	$receipt_id = "";
	$receipt_number = "";
	$field_cashier = "";
	$date = "";
	$adjustment = "0";

}
$attributes = array("method" => "post", "id" => "cash_receipt_input");
echo form_open('cash_receipt_management/save', $attributes);
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
		New Field Cash Receipt
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $receipt_id;?>" />
	<p>
		<label for="receipt">Receipt Number: </label>
		<input id="receipt_number"  name="receipt_number" type="text"  value="<?php echo $receipt_number;?>" class="validate[required]"/>
		<span class="field_desc">Enter the Buying Center Receipt Number for this transaction</span>
	</p>
	<p>
		<label for="date">Transaction Date: </label>
		<input id="date"  name="date" type="text"  value="<?php echo $date;?>" class="validate[required]"/>
		<span class="field_desc">Enter the transaction date</span>
	</p>
	<p>
		<label for="field_cashier">Field Cashier</label>
		<select name="field_cashier" class="dropdown validate[required]" id="field_cashier">
			<option></option>
			<?php
foreach($field_cashiers as $field_cashier_object){
			?>
			<option value="<?php echo $field_cashier_object -> id;?>" <?php
			if ($field_cashier_object -> id == $field_cashier) {echo "selected";
			}
			?>><?php echo $field_cashier_object -> Field_Cashier_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the origin of the cash</span>
	</p>
	<p>
		<label for="amount">Amount Returned: </label>
		<input id="amount" name="amount" type="text" value="<?php echo $amount;?>" class="validate[required,custom[integer]]"/>
		<span class="field_desc">Enter the amount returned</span>
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