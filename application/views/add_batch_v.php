<script type="text/javascript">
	$(function() {
		$("#add_batch_input").validationEngine();
	});

</script>
<?php
$attributes = array("method" => "post", "id" => "add_batch_input");
echo form_open('batch_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Add New Transaction Batch
	</legend>
	<p>
		<label for="transaction_type">Select Transaction Type </label>
		<select class="validate[required]" id="transaction_id" name="transaction_id">
			<option></option>
			<?php
foreach($transaction_types as $type){
			?>
			<option value="<?php echo $type -> id;?>"><?php echo $type -> Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the type of transactions you wish to save in this batch</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>