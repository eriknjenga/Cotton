<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing all buying center cotton purchases/dispatches grouped by routes
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('route_reports/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="route">Select Route</label>
			<select name="route" id="route">
				<option value="0">All Routes</option>
				<?php
foreach($routes as $route){
				?>
				<option value="<?php echo $route -> id;?>"><?php echo $route -> Route_Name;?></option>
				<?php }?>
			</select> 
		</p>
		<input type="submit" name="action" class="button"	value="Download Route Collections PDF" />
		<input type="submit" name="action" class="button"	value="Download Route Collections Excel" />
	</fieldset>
	</form>
</div>
