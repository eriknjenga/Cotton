<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing a list of buying centers that have been dormant for a specified minimum number of days (6 days by default).
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('dormant_buying_centers/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="days">Days Dormant: </label>
			<input id="days"  name="days" type="text"  class="validate[required]" value="2"/>
			</p>
		<input type="submit" name="action" class="button"	value="Download Dormant Buying Centers PDF" />
		<input type="submit" name="action" class="button"	value="Download Dormant Buying Centers Excel" />
	</fieldset>
	</form>
</div>
