<script type="text/javascript">
	$(function() {
		$("#cash_recoveries").validationEngine();
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
		A report showing all cash recoveries from FBGs in a specified date range
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "cash_recoveries");

	echo form_open('daily_cash_recoveries/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend> 
		<p>
			<label for="start_date">From</label>
			<input id="start_date" name="start_date" type="text" class="validate[required]"/>
		</p>
		<p>
			<label for="end_date">To</label>
			<input id="end_date" name="end_date" type="text" class="validate[required]"/>
		</p>
		<input type="submit" name="action" class="button"	value="Download Daily Cash Recoveries PDF" />
		<input type="submit" name="action" class="button"	value="Download Daily Cash Recoveries Excel" />
	</fieldset>
	</form>
</div>
