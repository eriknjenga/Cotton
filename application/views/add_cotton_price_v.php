<script type="text/javascript">
	$(function() {
		$("#add_cotton_price").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script>
<?php
$attributes = array("method" => "post", "id" => "add_cotton_price");
echo form_open('price_management/save_price', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		New Cotton Price
	</legend>
	<p>
		<label for="date">Effective Date: </label>
		<input id="date"  name="date" type="text" class="validate[required]"/>
		<span class="field_desc">Enter date from which this price will be effective (<b>can be a future date</b>)</span>
	</p>
	<p>
		<label for="season">Season: </label>
		<input id="season" name="season" type="text" class="validate[required]"/>
		<span class="field_desc">Enter the affected season</span>
	</p>
	<p>
		<label for="price">Price: </label>
		<input id="price" name="price" type="text" class="validate[required]"/>
		<span class="field_desc">Enter the price to take effect on the specified date</span>
	</p>
	<p>
		<label for="zone">Zone</label>
		<select name="zone" id="zone">
			<option value="0">All Zones</option>
			<?php
foreach($regions as $region_object){
			?>
			<option value="<?php echo $region_object -> id;?>">
				<?php echo $region_object -> Region_Name;?>
				</option>
			<?php }?>
		</select>
		<span class="field_desc">Select the zone affected by this price</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>