<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Add New Weighbridge Ticket
		</legend>
		<p>
			<label for="first_name">Ticket Number</label>
			<input class="first_name" name="first_name" type="text" value=" " readonly=""/>
			<span class="field_desc">Enter the number of this ticket</span>
		</p>
		<p>
			<label for="gd">Select Truck </label>
			<select>
				<option></option>
				<option>KBG 445W</option>
				<option>KAV 448T</option>
			</select>
			<span class="field_desc">Select the truck that was is carrying the cotton</span>
		</p>
		<p>
			<label for="cpc">Select Transporter </label>
			<select>
				<option></option>
				<option>Antony Njoroge</option>
				<option>Steve Osine</option>
			</select>
			<span class="field_desc">Select the transporter that was driving</span>
		</p>
		<p>
			<label for="first_name">Weight In</label>
			<input class="first_name" name="first_name" type="text" value=" " />
			<span class="field_desc">Enter the weight of the truck before the cotton is offloaded</span>
		</p>
		<p>
			<label for="first_name">Weight Out</label>
			<input class="first_name" name="first_name" type="text" value=" " />
			<span class="field_desc">Enter the weight of the truck after the cotton is offloaded</span>
		</p>
		<p>
			<label for="first_name">Cotton Weight</label>
			<input class="first_name" name="first_name" type="text" value=" " readonly=""/>
			<span class="field_desc">This displays the weight of the cotton that was ferried by the truck</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>