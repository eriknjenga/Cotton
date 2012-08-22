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
		A report showing all buying center transactions i.e. Cash Receipts, Cash Expenses, Purchases (Tsh.),Purchases (Kgs), Dispatches (Kgs) in a particular time frame
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST","id"=>"depot_transactions_form");

	echo form_open('depot_reports/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="depot">Buying Center</label>
			<select name="depot" id="depot" class="validate[required]">
				<option></option>
				<?php
foreach($depots as $depot){
				?>
				<option value="<?php echo $depot -> id;?>"><?php echo $depot -> Depot_Name." (".$depot -> Depot_Code.")";?></option>
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
		<input type="submit" name="surveillance" class="button"	value="Download Buying Center Transactions" />
	</fieldset>
	</form>
</div>
