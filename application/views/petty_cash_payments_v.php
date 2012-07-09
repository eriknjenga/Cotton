<script type="text/javascript">
	$(function() {
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script>
<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing all payments made to field cashiers on a particular date
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('petty_cash_payments/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="date">As at: </label>
			<input id="date"  name="date" type="text"  class="validate[required]"/>
			</p>
		<input type="submit" name="action" class="button"	value="Download Petty Cash Payments PDF" />
		<input type="submit" name="action" class="button"	value="Download Petty Cash Payments Excel" />
	</fieldset>
	</form>
</div>
