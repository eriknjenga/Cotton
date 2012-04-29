<script type="text/javascript">
	$(function() {
		$("#search_fbg").validationEngine();
	});

</script>
<?php
$attributes = array("method" => "post", "id" => "search_fbg");
echo form_open('depot_management/search', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		<?php echo $search_title;?>
	</legend>
	<p>
		<label for="search_value">Search Value </label>
		<input id="search_value"  name="search_value" type="text" value="" class="validate[required]"/>
		<span class="field_desc">Enter the value to search for</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>