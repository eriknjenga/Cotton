<script type="text/javascript">
	$(function() {
		$("#truck_summaries").validationEngine();
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
		A report showing summary deliveries of all trucks in  specified period
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "truck_summaries");

	echo form_open('truck_summaries/download', $attributes);
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
		<p>
			<label for="trucks">Trucks</label> 
			<input id="trucks" name="trucks" type="radio" value="all" class="validate[required]" checked=""/> All
			<input id="trucks" name="trucks" type="radio" value="alliance" class="validate[required]"/> Alliance Only
			<input id="trucks" name="trucks" type="radio" value="contracted" class="validate[required]"/> Contracted Only
		</p>
		<input type="submit" name="action" class="button"	value="Download Truck Summaries PDF" />
		<input type="submit" name="action" class="button"	value="Download Truck Summaries Excel" />
	</fieldset>
	</form>
</div>
