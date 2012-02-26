<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Add New Area
		</legend>
		<p>
			<label for="area_code">Area Code: </label>
			<input class="area_code" name="area_code" type="text" value=" " />
			<span class="field_desc">Enter the area code for this area</span>
		</p>
		<p>
			<label for="area_name">Area Name: </label>
			<input class="area_name" name="area_name" type="text" value=" " />
			<span class="field_desc">Enter the area name for this depot</span>
		</p>
		<p>
			<label for="region_code">Region Code </label>
			<select name="region_code" class="dropdown">
				<option>Please select a region</option>
			</select>
			<span class="field_desc">Select the region for this area</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>