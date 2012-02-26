<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Register New Account
		</legend>
		<p>
			<label for="account_code">Account Code: </label>
			<input class="account_code" name="account_code" type="text" value=" " />
			<span class="field_desc">Enter the code for this account</span>
		</p>
		<p>
			<label for="account_name">Account Name: </label>
			<input class="account_name" name="account_name" type="text" value=" " />
			<span class="field_desc">Enter the name for this account</span>
		</p>
		<p>
			<label for="depot_code">Depot Code </label>
			<select name="depot_code" class="dropdown">
				<option>Please select a depot </option>
			</select>
			<span class="field_desc">Select the depot associated with this depot</span>
		</p>
		<p>
			<label for="description">Description: </label>
			<input class="description lf" name="description" type="text" value=" " />
			<span class="field_desc">Enter a brief description for this account</span>
		</p>
				<p>
			<label for="contact">Contact: </label>
			<input class="contact lf" name="contact" type="text" value=" " />
			<span class="field_desc">Enter the contact details associated with this account</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>