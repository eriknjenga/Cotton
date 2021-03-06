<div class="message information close">
	<h2>Report Description</h2>
	<p>
		A report showing a detailed listing of disbursements of inputs to FBGs in a particular season. This listing can be filtered to show disbursements of a particular input
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST");

	echo form_open('inputs_disbursement_report/download_inputs_disbursement', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<p>
			<label for="input">Select Farm Input</label>
			<select name="input" id="input">
				<option value="0">All Inputs</option>
				<?php
foreach($inputs as $input){
				?>
				<option value="<?php echo $input -> id;?>"><?php echo $input -> Product_Name;?></option>
				<?php }?>
			</select>
			<label for="season">Select Season</label>
			<select name="season" id="season">
				<?php
foreach($seasons as $season){
				?>
				<option value="<?php echo $season ['season'];?>"><?php echo  $season ['season'];?></option>
				<?php }?>
			</select>
		</p>
		<input type="submit" name="action" class="button"	value="Download Inputs Disbursement Excel" />
	</fieldset>
	</form>
</div>
