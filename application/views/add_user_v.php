<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Register New System User
		</legend>
		<p>
			<label for="first_name">First Name: </label>
			<input class="first_name" name="first_name" type="text" value=" " />
			<span class="field_desc">Enter the First Name for this user</span>
		</p>
		<p>
			<label for="surname">Surname: </label>
			<input class="surname" name="surname" type="text" value=" " />
			<span class="field_desc">Enter the Surname for this user</span>
		</p>
		<p>
			<label for="username">Username: </label>
			<input class="username" name="username" type="text" value=" " />
			<span class="field_desc">Enter the Username for this user</span>
		</p>
		<p>
			<label for="mf">Password: </label>
			<input class="national_id" name="national_id" type="text" value=" " />
			<span class="field_desc">Enter the National ID Number for this farmer</span>
		</p>
		<p>
			<label for="access_level">Access Level</label>
			<select name="access_level" class="dropdown">
				<option>Please select the access level</option>
				<option>System Administrator</option>
				<option>Distributor</option>
				<option>Data Clerk</option>
			</select>
			<span class="field_desc">Select the access level for this user</span>
		</p>
		 
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>