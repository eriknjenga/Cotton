<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing buying center summaries grouped by zones. The summaries include the purchases made by the center, cotton dispatched, cash issued to the center, e.t.c.
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('regional_summaries/download', $attributes);
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
		<input type="submit" name="action" class="button"	value="Download Zonal Summary PDF" />
		<input type="submit" name="action" class="button"	value="Download Zonal Summary Excel" />
	</fieldset>
	</form>
</div>
