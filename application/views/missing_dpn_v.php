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
		<input type="submit" name="surveillance" class="button"	value="Download Missing DPNs" />
	</fieldset>
	</form>
</div>
