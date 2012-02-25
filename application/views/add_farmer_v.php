<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Register New Farmer
		</legend>
		<p>
			<label for="gd">GD Id: </label>
			<input class="gd" name="gd" type="text" value="small input field" />
			<span class="field_desc">Enter the GD ID for this farmer</span>
		</p>
		<p>
			<label for="cpc">CPC Number: </label>
			<input class="cpc" name="cpc" type="text" value="small input field" />
			<span class="field_desc">Enter the CPC Number for this farmer</span>
		</p>
		<p>
			<label for="title">Title: </label>
			<select name="title" class="dropdown">
				<option>Please select an option</option>
				<option>Mr.</option>
				<option>Mrs.</option>
				<option>Miss</option>
			</select>
			<span class="field_desc">Select an appropriate title for this farmer</span>
		</p>
		<p>
			<label for="first_name">First Name: </label>
			<input class="first_name" name="first_name" type="text" value="small input field" />
			<span class="field_desc">Enter the First Name for this farmer</span>
		</p>
		<p>
			<label for="surname">Surname: </label>
			<input class="surname" name="surname" type="text" value="small input field" />
			<span class="field_desc">Enter the Surname for this farmer</span>
		</p>
		<p>
			<label for="mf">National ID No.: </label>
			<input class="national_id" name="national_id" type="text" value="medium input field" />
			<span class="field_desc">Enter the National ID Number for this farmer</span>
		</p>
		<p>
			<label for="distributor">Distributor Code </label>
			<select name="distributor" class="dropdown">
				<option>Please select a distributor</option> 
			</select>
			<span class="field_desc">Select the distributor responsible for this farmer</span>
		</p>
				<p>
			<label for="mf">Hectares Available: </label>
			<input class="hectares_available" name="national_id" type="text" value="medium input field" />
			<span class="field_desc">Enter the hectares available for this farmer</span>
		</p>
		 
	</fieldset>
	<!-- End of fieldset -->
</form>