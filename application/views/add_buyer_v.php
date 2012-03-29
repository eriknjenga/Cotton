<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Register New Buyer
		</legend> 
		<p>
			<label for="cpc">Buyer Code: </label>
			<input class="cpc" name="cpc" type="text" value=" " />
			<span class="field_desc">Enter the buyer code for this buyer</span>
		</p>
	
		<p>
			<label for="first_name">Buyer Name: </label>
			<input class="first_name" name="first_name" type="text" value=" " />
			<span class="field_desc">Enter this buyer's name</span>
		</p>
		<p>
			<label for="surname">Phone Number</label>
			<input class="surname" name="surname" type="text" value=" " />
			<span class="field_desc">Enter the phone number for this buyer</span>
		</p>
		<p>
			<label for="mf">National ID No.: </label>
			<input class="national_id" name="national_id" type="text" value=" " />
			<span class="field_desc">Enter the National ID Number for this buyer</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>