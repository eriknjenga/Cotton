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
		A report showing cotton purchases from all buying centers on a particular date.
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

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
			<label for="date">On: </label>
			<input id="date"  name="date" type="text"  class="validate[required]"/>
			</p>
		<input type="submit" name="action" class="button"	value="Download BC Purchases PDF" />
		<input type="submit" name="action" class="button"	value="Download BC Purchases Excel" />
	</fieldset>
	</form>
</div>
