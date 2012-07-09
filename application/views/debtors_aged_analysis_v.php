<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing FBG loan amount, total amount recovered to date and total outstanding balance. This is grouped by Zones
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('fbg_reports/download_aged_analysis', $attributes);
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
		<input type="submit" name="action" class="button"	value="Download Debt Analysis PDF" />
		<input type="submit" name="action" class="button"	value="Download Debt Analysis Excel" />
	</fieldset>
	</form>
</div>
