<script type="text/javascript">
	$(function() {
		$("#disburse_cash_input").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script>
<?php
if (isset($disbursement)) {
	$amount = $disbursement -> Amount;
	$field_cashier = $disbursement -> Field_Cashier;
	$receipt = $disbursement -> Receipt;
	$buyer = $disbursement -> Buyer;
	$cih = $disbursement -> CIH;
	$disbursement_id = $disbursement -> id;
	$date = $disbursement -> Date;
} else {
	$amount = "";
	$field_cashier = "";
	$cih = "";
	$disbursement_id = "";
	$receipt = "";
	$buyer = "";
	$date = "";

}
$attributes = array("method" => "post", "id" => "disburse_cash_input");
echo form_open('field_cash_management/save', $attributes);
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
		New Field Payment
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $disbursement_id;?>" />
	<p>
		<label for="cih">CIH(b) Voucher Number: </label>
		<input id="cih"  name="cih" type="text"  value="<?php echo $cih;?>" class="validate[required]"/>
		<span class="field_desc">Enter the CIH(b) Voucher Number for this transaction</span>
	</p>
	<p>
		<label for="receipt">Buying Center Receipt Number: </label>
		<input id="receipt"  name="receipt" type="text"  value="<?php echo $receipt;?>" class="validate[required]"/>
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
		<label for="amount">Amount Issued: </label>
		<input id="amount" name="amount" type="text" value="<?php echo $amount;?>" class="validate[required,custom[integer]]"/>
		<span class="field_desc">Enter the amount issued</span>
	</p>
	<p> 
		<input class="button" type="reset" value="Reset">
		<input class="button" type="submit" value="Save & Add New" name="submit">
		<input class="button" type="submit" value="Save & View List" name="submit">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>