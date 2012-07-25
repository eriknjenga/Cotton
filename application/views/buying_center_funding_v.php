<script type="text/javascript">
	$(function() {
		$("#bc_funding").validationEngine();
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
		A report showing showing funding predictions for all buying centers in a zone based on average daily purchases
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "bc_funding");

	echo form_open('buying_center_funding/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="date">Date</label>
			<input id="date" name="date" type="text" class="validate[required]"/>
			<span class="field_desc">The date for report generation</span>
		</p>
		<p>
			<label for="region">Select Zone</label>
			<select name="region" id="region" class="validate[required]">
				<option value="">--select--</option>
				<?php
foreach($regions as $region){
				?>
				<option value="<?php echo $region -> id;?>"><?php echo $region -> Region_Name;?></option>
				<?php }?>
			</select>
			<span class="field_desc">The zone to calculate the funding required</span>
		</p>
		<p>
			<label for="history">Historical Baseline</label>
			<input id="history" name="history" type="text" value="3" class="validate[required]"/>
			<span class="field_desc">The average daily purchases for this number of <b>days</b> will be used to calculate funding requirements (3 days by default)</span>
		</p>
		<p>
			<label for="cycle">Funding Cycle</label>
			<input id="cycle" name="cycle" type="text" class="validate[required]"/>
			<span class="field_desc">The number of <b>days</b> the centers should be funded for</span>
		</p>
		<p>
			<label for="price">Estimated Future Price</label>
			<input id="price" name="price" type="text" class="validate[required]"/>
			<span class="field_desc">The forcasted price that the buying centers in this zone will use</span>
		</p>
		<p>
			<label for="nearest">Nearest</label>
			<input id="nearest" name="nearest" type="text" value="10000" class="validate[required,custom[integer]]"/>
			<span class="field_desc">The funding requirement will be rounded off to this value in <b>Tsh.</b> (10,000 by default)</span>
		</p>
		<p>
			<label for="factor">Factor</label>
			<input id="factor" name="factor" type="text" value="1" class="validate[required]"/>
			<span class="field_desc">What factor of the requirement is to be issued? By default, it's <b>1.0</b> i.e. 100% of the requirement will be disbursed</span>
		</p>
		<input type="submit" name="action" class="button"	value="Download BC Funding PDF" />
		<input type="submit" name="action" class="button"	value="Download BC Funding Excel" />
	</fieldset>
	</form>
</div>
