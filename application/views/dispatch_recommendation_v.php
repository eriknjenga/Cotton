<script type="text/javascript">
	$(function() {
		$("#dispatch_recommendation").validationEngine();
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
		A report showing a recommendation of buying centers that need to have their cotton picked up by the ginnery based on certain parameters. This recommendation is for a particular date.
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "dispatch_recommendation");

	echo form_open('dispatch_recommendation/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="date">Date of Recommendation: </label>
			<input id="date"  name="date" type="text"  class="validate[required]"/>
			<span class="field_desc">The date to generate a recommendation for</span>
		</p>
		<p>
			<label for="maximum_stock">Stock Balance: </label>
			<input id="maximum_stock"  name="maximum_stock" type="text" value="10" class="validate[required]"/>
			<span class="field_desc">Centers having a stock balance (in <b>Tonnes</b>) above this will be recommended for dispatch</span>
		</p>
		<p>
			<label for="capacity_percentage">% of Store Full: </label>
			<input id="capacity_percentage"  name="capacity_percentage" type="text" value="100" class="validate[required]"/>
			<span class="field_desc">Centers having this <b>Percentage</b> of their capacity full will be recommended for dispatch</span>
		</p>
		<input type="submit" name="action" class="button"	value="Download Dispatch Recommendation PDF" />
		<input type="submit" name="action" class="button"	value="Download Dispatch Recommendation Excel" />
	</fieldset>
	</form>
</div>
