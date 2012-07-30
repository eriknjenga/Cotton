<script type="text/javascript">
	$(function() {
		$("#bc_purchases").validationEngine();
		$("#start_date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
		$("#end_date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});	});

</script>
<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing cotton purchases from all buying centers in the specified date range
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "bc_purchases");

	echo form_open('buying_center_purchases/download', $attributes);
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
			<input name="start_date" id="start_date" class="validate[required]"/>
		</p>
		<p>
			<label for="end_date">To</label>
			<input name="end_date" id="end_date" class="validate[required]"/>
		</p>
		<input type="submit" name="action" class="button"	value="Download BC Purchases PDF" />
		<input type="submit" name="action" class="button"	value="Download BC Purchases Excel" />
	</fieldset>
	</form>
</div>
