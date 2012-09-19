<script type="text/javascript">
	$(function() {
		$("#mopping_payment_input").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			maxDate: 0
		});
	});

</script>
<?php
if (isset($payment)) {
	$amount = $payment -> Amount;
	$voucher_number = $payment -> Voucher_Number;
	$depot = $payment -> Depot;
	$payment_id = $payment -> id;
	$date = $payment -> Date;
} else {
	$amount = "";
	$payment_id = "";
	$voucher_number = "";
	$depot = "";
	$date = "";

}
$attributes = array("method" => "post", "id" => "mopping_payment_input");
echo form_open('mopping_payment_management/save', $attributes);
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
		New Buying Center Expense
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $payment_id;?>" />
	<p>
		<label for="voucher_number">Voucher Number: </label>
		<input id="voucher_number"  name="voucher_number" type="text"  value="<?php echo $voucher_number;?>" class="validate[required]"/>
		<span class="field_desc">Enter the Voucher Number for this transaction</span>
	</p>
	<p>
		<label for="date">Transaction Date: </label>
		<input id="date" readonly=""  name="date" type="text"  value="<?php echo $date;?>" class="validate[required]"/>
		<span class="field_desc">Enter the transaction date</span>
	</p>
	<p>
		<label for="depot">Buying Center</label>
		<select name="depot" class="dropdown depot validate[required]" id="depot" >
			<option></option>
			<?php
foreach($depots as $depot_object){
			?>
			<option value="<?php echo $depot_object -> id;?>" <?php
			if ($depot_object -> id == $depot) {echo "selected";
			}
				?>><?php echo $depot_object -> Depot_Name;?></option>
			<?php
			$counter++;
			}
			?>
		</select>
		<span class="field_desc">Select the affected buying center</span>
	</p>
	<p>
		<label for="amount">Amount Used: </label>
		<input id="amount" name="amount" type="text" value="<?php echo $amount;?>" class="validate[required,custom[integer]]"/>
		<span class="field_desc">Enter the amount used</span>
	</p>
	<p>
		<input class="button" type="reset" value="Reset">
		<input class="button" type="submit" value="Save & Add New" name="submit">
		<input class="button" type="submit" value="Save & View List" name="submit">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>