<div class="message information close">
	<h2>Please Note!</h2>
	<p>
		This action will download all incremental data from the 2 ginneries since the last import. Once you confirm this action, it is not reversible
	</p>
</div>
<div id="filter">
	<?php
	$attributes = array("method" => "POST", "id" => "missing_dpn");

	echo form_open('production_management/fetch_data', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?> 
		<input type="submit" name="surveillance" class="button"	value="I am Sure. Import" /> 
	</form>
</div>