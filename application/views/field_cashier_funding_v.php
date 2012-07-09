<script type="text/javascript">
	$(function() {
		$("#field_cashier_funding").validationEngine();
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
		A report showing showing funding predictions for all buying centers under a field cashier's route based on average daily purchases
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST","id" => "field_cashier_funding");

	echo form_open('field_cashier_funding/download', $attributes);
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
			<label for="cashier">Select Field Cashier</label>
			<select name="cashier" id="cashier">
				<?php
foreach($cashiers as $cashier){
				?>
				<option value="<?php echo $cashier -> id;?>"><?php echo $cashier -> Field_Cashier_Name;?></option>
				<?php }?>
			</select>
			<span class="field_desc">The field cashier to calculate the funding required</span>
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
			<label for="nearest">Nearest</label>
			<input id="nearest" name="nearest" type="text" value="500000" class="validate[required,custom[integer]]"/>
			<span class="field_desc">The funding requirement will be rounded off to this value in <b>Tsh.</b> (500,000 by default)</span>
		</p>
		<input type="submit" name="action" class="button"	value="Download Field Cashier Funding PDF" />
		<input type="submit" name="action" class="button"	value="Download Field Cashier Funding Excel" />
	</fieldset>
	</form>
</div>
