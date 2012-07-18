<script type="text/javascript">
	$(function() {
		$("#fbg_transactions_form").validationEngine();
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
		A report showing all fbg transactions i.e. Input Receipts and Cotton Sales
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST","id"=>"fbg_transactions_form");

	echo form_open('fbg_transactions/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="fbg">FBG</label>
			<select name="fbg" id="fbg" class="validate[required]">
				<option></option>
				<?php
foreach($fbgs as $fbg){
				?>
				<option value="<?php echo $fbg -> id;?>"><?php echo $fbg -> Group_Name." (".$fbg -> Village_Object->Name.")";?></option>
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
