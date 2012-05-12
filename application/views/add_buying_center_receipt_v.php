<script type="text/javascript">
	$(function() {
		$("#buying_center_receipt_input").validationEngine();
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
	$buyer = $receipt -> Buyer;
	$receipt_id = $receipt -> id;
	$date = $receipt -> Date;
} else {
	$amount = "";
	$receipt_id = "";
	$receipt_number = "";
	$buyer = "";
	$date = "";

}
$attributes = array("method" => "post", "id" => "buying_center_receipt_input");
echo form_open('buying_center_receipt_management/save', $attributes);
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
		New Buying Center Receipt
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
		<label for="buyer">Buyer</label>
		<select name="buyer" class="dropdown validate[required]" id="buyer">
			<option></option>
			<?php
foreach($buyers as $buyer_object){
			?>
			<option value="<?php echo $buyer_object -> id;?>" <?php
			if ($buyer_object -> id == $buyer) {echo "selected";
			}
			?>><?php echo $buyer_object -> Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the recipient of the cash</span>
	</p>
	<p>
		<label for="amount">Amount Returned: </label>
		<input id="amount" name="amount" type="text" value="<?php echo $amount;?>" class="validate[required,custom[integer]]"/>
		<span class="field_desc">Enter the amount returned</span>
	</p>
	<p>
		<input class="button" type="reset" value="Reset">
		<input class="button" type="submit" value="Save & Add New" name="submit">
		<input class="button" type="submit" value="Save & View List" name="submit">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>