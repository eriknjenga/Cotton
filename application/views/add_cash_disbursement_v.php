<script type="text/javascript">
	$(function() {
		$("#disburse_cash_input").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			maxDate: 0
		});
	});

</script>
<?php
if (isset($disbursement)) {
	$amount = $disbursement -> Amount;
	$field_cashier = $disbursement -> Field_Cashier;
	$cih = $disbursement -> CIH;
	$disbursement_id = $disbursement -> id;
	$date = $disbursement -> Date;
	$adjustment = $disbursement -> Adjustment;
} else {
	$amount = "";
	$field_cashier = "";
	$cih = "";
	$disbursement_id = "";
	$date = "";
	$adjustment = "0";

}
$attributes = array("method" => "post", "id" => "disburse_cash_input");
echo form_open('cash_management/save', $attributes);
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
		Disburse Cash
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $disbursement_id;?>" />
	<p>
		<label for="cih">CIH(c) Voucher Number: </label>
		<input id="cih"  name="cih" type="text"  value="<?php echo $cih;?>" class="validate[required]"/>
		<span class="field_desc">Enter the CIH(c) Voucher Number for this transaction</span>
	</p>
	<p>
		<label for="date">Transaction Date: </label>
		<input id="date" readonly=""  name="date" type="text"  value="<?php echo $date;?>" class="validate[required]"/>
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
		<span class="field_desc">Select the recipient of the cash</span>
	</p>
	<p>
		<label for="amount">Amount Issued: </label>
		<input id="amount" name="amount" type="text" value="<?php echo $amount;?>" class="validate[required,custom[integer]]"/>
		<span class="field_desc">Enter the amount issued</span>
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