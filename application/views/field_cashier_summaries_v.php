<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing cash summaries for all field cashiers. It shows the total amount they've been issued with, total amount they've disbursed/returned and their balance
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('field_cashier_summaries/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Type of Report
		</legend>
		<input type="submit" name="action" class="button"	value="Download Field Cashier Summary PDF" />
		<input type="submit" name="action" class="button"	value="Download Field Cashier Summary Excel" />
	</fieldset>
	</form>
</div>
