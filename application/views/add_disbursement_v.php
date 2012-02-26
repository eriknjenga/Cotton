<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Disburse Farm Inputs
		</legend>
		<p>
			<label for="farmer">Farmer: </label>
			<select name="farmer" class="dropdown">
				<option>Please select a farmer </option>
			</select>
			<span class="field_desc">Select Farmer</span>
		</p>
		<div style="margin-left:100px;">
			<p>
				<label for="gd_id">GD ID: </label>
				<input class="gd_id" name="gd_id" type="text" value=" " />
			</p>
			<p>
				<label for="cpc">CPC No.: </label>
				<input class="cpc" name="cpc" type="text" value=" " />
			</p>
			<p>
				<label for="first_name">Grower First Name: </label>
				<input class="first_name" name="first_name" type="text" value=" " />
			</p>
			<p>
				<label for="surname">Grower Surname: </label>
				<input class="surname" name="surname" type="text" value=" " />
			</p>
			<p>
				<label for="id">National ID: </label>
				<input class="id" name="id" type="text" value=" " />
			</p>
			<p>
				<label for="distributor_code">Distributor Code: </label>
				<input class="distributor_code" name="distributor_code" type="text" value=" " />
			</p>
			<p>
				<label for="distributor_name">Distributor Name: </label>
				<input class="distributor_name" name="distributor_name" type="text" value=" " />
			</p>
		</div>
		<table class="normal" style="margin:0 auto;">
			<caption>Farm Inputs Loaned</caption>
			<thead>
				<tr>
					<th>Invoice No.</th>
					<th>Date</th>
					<th>Input Name</th>
					<th>Quantity</th>
					<th>Total Value</th>
					<th>Season</th>
					<th>GD Batch</th>
					<th>ID Batch</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
					<input class="invoice_number" name="invoice_number" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="date" name="date" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<select name="farm_input" class="dropdown" style="width:50px">
						<option>Select Input </option>
					</select></td>
					<td>
					<input class="quantity" name="quantity" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="total_value" name="total_value" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="season" name="season" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="gd_batch" name="gd_batch" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="id_batch" name="id_batch" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input  class="button"   value="+" style="width:50px; text-align: center"/>
					</td>
				</tr>
			</tbody>
		</table>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>