<script type="text/javascript">
	$(function() {
		$("#depot_transactions_form").validationEngine();
		$("#start_date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
		$("#end_date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script> 
<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing all transactions of a particular field cashier in a particular time frame. It includes, cash receipts, cash returns and cash disbursements to buying centers
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST","id"=>"depot_transactions_form");

	echo form_open('field_cashier_transactions/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="field_cashier">Field Cashier</label>
			<select name="field_cashier" id="field_cashier" class="validate[required]">
				<option></option>
				<?php
foreach($field_cashiers as $field_cashier){
				?>
				<option value="<?php echo $field_cashier -> id;?>"><?php echo $field_cashier -> Field_Cashier_Name;?></option>
				<?php }?>
			</select>
			</p>
			<p>
			<label for="start_date">From</label>
			<input id="start_date" name="start_date" type="text" class="validate[required]"/>
			</p>
			<p>
			<label for="end_date">To</label>
			<input id="end_date" name="end_date" type="text" class="validate[required]"/>
		</p>
		<input type="submit" name="action" class="button"	value="Download Cashier Transactions PDF" />
		<input type="submit" name="action" class="button"	value="Download Cashier Transactions Excel" />
	</fieldset>
	</form>
</div>
