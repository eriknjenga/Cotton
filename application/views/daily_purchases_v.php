<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing daily and cumulative purchases for each day in the purchasing period. It also shows the number of reporting buying centers.
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('daily_purchase_management/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<input type="submit" name="action" class="button"	value="Download Daily Purchases PDF" />
		<input type="submit" name="action" class="button"	value="Download Daily Purchases Excel" />
	</fieldset>
	</form>
</div>
