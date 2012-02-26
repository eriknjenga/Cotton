<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Register New Distributor
		</legend>
		<p>
			<label for="distributor_code">Distributor Code: </label>
			<input class="distributor_code" name="distributor_code" type="text" value=" " />
			<span class="field_desc">Enter the code for this distributor</span>
		</p>
		<p>
			<label for="first_name">First Name: </label>
			<input class="first_name" name="first_name" type="text" value=" " />
			<span class="field_desc">Enter the first name for this distributor</span>
		</p>
		<p>
			<label for="surname">Surname: </label>
			<input class="surname" name="surname" type="text" value=" " />
			<span class="field_desc">Enter the surname for this distributor</span>
		</p>
		<p>
			<label for="national_id">National ID No.: </label>
			<input class="national_id" name="national_id" type="text" value=" " />
			<span class="field_desc">Enter the national id number for this distributor</span>
		</p>
		<p>
			<label for="area_code">Area Code </label>
			<select name="area_code" class="dropdown">
				<option>Please select an area </option>
			</select>
			<span class="field_desc">Select the area represented by this distributor</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>