<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Add New Cash Voucher
		</legend>
		<p>
			<label for="first_name">Voucher Number</label>
			<input class="first_name" name="first_name" type="text" value=" " readonly=""/>
			<span class="field_desc">Enter the number of this voucher</span>
		</p>
		<p>
			<label for="gd">Select Buyer </label>
			<select>
				<option></option>
				<option>Joram Mwangi</option>
				<option>Kevin Nduati</option>
			</select>
			<span class="field_desc">Select the buyer who is to receive the cash</span>
		</p>
		<p>
			<label for="cpc">Select Field Cashier </label>
			<select>
				<option></option>
				<option>Antony Njoroge</option>
				<option>Steve Osine</option>
			</select>
			<span class="field_desc">Select the field cashier that is to deliver the cash</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>