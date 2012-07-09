<script type="text/javascript">
	$(function() {
		$("#deliveries_report_form").validationEngine();
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
		A report showing cotton purchases from FBGs in a particular time frame. This report shows the value purchased, inputs deducted, gross amount payed, e.t.c. The FBGs are grouped by Zones
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "deliveries_report_form");

	echo form_open('deliveries_report/download_deliveries_report', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="region">Select Zone</label>
			<select name="region" id="region">
				<option value="0">All Zones</option>
				<?php
foreach($regions as $region){
				?>
				<option value="<?php echo $region -> id;?>"><?php echo $region -> Region_Name;?></option>
				<?php }?>
			</select>
		</p>
		<p>
			<label for="start_date">From</label>
			<input name="start_date" id="start_date" class="validate[required]"/>
		</p>
		<p>
			<label for="end_date">To</label>
			<input name="end_date" id="end_date" class="validate[required]"/>
		</p>
		<input type="submit" name="action" class="button"	value="Download Deliveries Report PDF" />
		<input type="submit" name="action" class="button"	value="Download Deliveries Report Excel" />
	</fieldset>
	</form>
</div>
