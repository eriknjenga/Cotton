<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Add New Depot
		</legend>
		<p>
			<label for="depot_code">Depot Code: </label>
			<input class="depot_code" name="depot_code" type="text" value=" " />
			<span class="field_desc">Enter the depot code for this depot</span>
		</p>
		<p>
			<label for="depot_name">Depot Name: </label>
			<input class="depot_name" name="depot_name" type="text" value=" " />
			<span class="field_desc">Enter the depot name for this depot</span>
		</p>
		<p>
			<label for="distributor">Distributor Code </label>
			<select name="distributor" class="dropdown">
				<option>Please select a distributor</option>
			</select>
			<span class="field_desc">Select the distributor responsible for this depot</span>
		</p>
		<p>
			<label for="area_code">Area Code </label>
			<select name="area_code" class="dropdown">
				<option>Please select an area code</option>
			</select>
			<span class="field_desc">Select the area code of this depot</span>
		</p>
				<p>
			<label for="route">Route </label>
			<select name="route" class="dropdown">
				<option>Please select a route</option>
			</select>
			<span class="field_desc">Select the route for this depot</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>