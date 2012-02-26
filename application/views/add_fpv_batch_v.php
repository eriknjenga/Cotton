<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			FPV Batch Details
		</legend>
		<p>
			<label for="batch_number">FPV Batch No.: </label>
			<input class="batch_number" name="batch_number" type="text" value=" " />
		</p>
		<p>
			<label for="date">Date: </label>
			<input class="date" name="date" type="text" value=" " />
		</p>
		<table class="normal" style="margin:0 auto;">
			<caption>
				FPV Batch Details
			</caption>
			<thead>
				<tr>
					<th>FPV No.</th>
					<th>Date</th>
					<th>Buyer</th>
					<th>Details</th>
					<th>Value</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
					<input class="fpv_number" name="fpv_number" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="date" name="date" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<select name="buyer" class="dropdown" style="width:50px">
						<option>Select Input </option>
					</select></td>
					<td>
					<select name="details" class="dropdown" style="width:50px">
						<option>Select Input </option>
					</select></td>
					<td>
					<input class="value" name="value" type="text" value=" " style="width: 50px"/>
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