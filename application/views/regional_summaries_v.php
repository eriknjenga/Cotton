<script type="text/javascript">
	$(function() {
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script>
<style type="text/css">
	#filter {
		border: 2px solid #DDD;
		display: block;
		width: 80%;
		margin: 10px auto;
	}
	.filter_input {
		border: 1px solid black;
	}
</style>
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
			<label for="region">Select Region</label>
			<select name="region" id="region">
				<option value="0">All Regions</option>
				<?php
foreach($regions as $region){
				?>
				<option value="<?php echo $region -> id;?>"><?php echo $region -> Region_Name;?></option>
				<?php }?>
			</select>
			<label for="date">Transaction Date</label>
			<input id="date" name="date" type="text"/>
		</p>
		<input type="submit" name="surveillance" class="button"	value="Download Regional Summary" />
	</fieldset>
	</form>
</div>
