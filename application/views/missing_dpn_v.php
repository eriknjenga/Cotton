<script type="text/javascript">
	$(function() {
		$("#missing_dpn").validationEngine();
	});

</script>
<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing all missing dpn number for a particular buying center.
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "missing_dpn");

	echo form_open('missing_dpn/download', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="depot">Buying Center</label>
			<select name="depot" id="depot" class="validate[required]">
				<option></option>
				<?php
foreach($depots as $depot){
				?>
				<option value="<?php echo $depot -> id;?>"><?php echo $depot -> Depot_Name . " (" . $depot -> Village_Object -> Name . ")";?></option>
				<?php }?>
			</select>
			<span>The buying center to analyze DPNs for</span>
		</p>
		<p>
			<label for="season">Season</label>
			<select name="season" id="season" class="validate[required]">
				<option value=""></option>
				<?php
foreach($seasons as $season){
				?>
				<option value="<?php echo $season['season'];?>"><?php echo $season['season'];?></option>
				<?php }?>
			</select>
			<span>The season to analyze for</span>
		</p>
		<p>
			<label for="start">Sequence Start</label>
			<input id="start" name="start" type="text" class="validate[required]"/>
			<span>The start sequence of the DPN book at the center</span>
		</p>
		<p>
			<label for="end">Sequence End</label>
			<input id="end" name="end" type="text" class="validate[required]"/>
			<span>The end sequence of the DPN book at the center or the latest DPN received at the ginnery</span>
		</p>
		<input type="submit" name="surveillance" class="button"	value="Download Missing DPNs" />
	</fieldset>
	</form>
</div>
