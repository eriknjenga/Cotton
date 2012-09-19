	<style>
		.ui-autocomplete {
			overflow-y: auto;
			/* prevent horizontal scrollbar */
			overflow-x: hidden;
			/* add padding to account for vertical scrollbar */
			padding-right: 20px;
			max-width: 200px;
			font-size: 14px;
		}
		/* IE 6 doesn't support max-height
		 * we use height instead, but this forces the menu to always be this tall
		 */
		* html .ui-autocomplete {
			width: 200px;
		}
	</style>
<script type="text/javascript">
	$(function() {
		$("#loan_recovery_receipt_input").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			maxDate: 0
		});
		$("#fbg").autocomplete({
			source : "<?php echo base_url();?>fbg_management/autocomplete_fbg",
				minLength : 1,
				select: function( event, ui ) {
				$( "#fbg" ).val( ui.item.label );
				$( "#fbg_id" ).val( ui.item.value );
				return false;
				}
		});
	});

</script>
<?php
if (isset($receipt)) {
	$amount = $receipt -> Amount;
	$receipt_number = $receipt -> Receipt_Number;
	$fbg = $receipt -> FBG;
	$fbg_name = $receipt -> FBG_Object -> Group_Name;
	$receipt_id = $receipt -> id;
	$received_from = $receipt -> Received_From;
	$date = $receipt -> Date;
	$adjustment = $receipt -> Adjustment;
} else {
	$amount = "";
	$receipt_id = "";
	$receipt_number = "";
	$fbg = "";
	$fbg_name = "";
	$received_from = "";
	$date = "";
	$adjustment = "0";

}
$attributes = array("method" => "post", "id" => "loan_recovery_receipt_input");
echo form_open('loan_recovery_receipt_management/save', $attributes);
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
		New Loan Recovery Receipt
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $receipt_id;?>" />
	<input type="hidden" name="fbg_id" id="fbg_id" value="<?php echo $fbg;?>" />
	<p>
		<label for="receipt">Receipt Number: </label>
		<input id="receipt_number"  name="receipt_number" type="text"  value="<?php echo $receipt_number;?>" class="validate[required]"/>
		<span class="field_desc">Enter the Buying Center Receipt Number for this transaction</span>
	</p>
	<p>
		<label for="date">Receipt Date: </label>
		<input id="date" readonly=""  name="date" type="text"  value="<?php echo $date;?>" class="validate[required]"/>
		<span class="field_desc">Enter the transaction date</span>
	</p>
	<p>
		<label for="fbg">FBG Name:</label>
		<input id="fbg" name="fbg" type="text" value="<?php echo $fbg_name;?>" />
		<span class="field_desc">Select the affected FBG account</span>
	</p>
	<p>
		<label for="amount">Amount Returned: </label>
		<input id="amount" name="amount" type="text" value="<?php echo $amount;?>" class="validate[required,custom[integer]]"/>
		<span class="field_desc">Enter the amount returned</span>
	</p>
	<p>
		<label for="received_from">Received From: </label>
		<input id="received_from" name="received_from" type="text" value="<?php echo $received_from;?>" class="validate[required]"/>
		<span class="field_desc">Enter the person who brought the cash</span>
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