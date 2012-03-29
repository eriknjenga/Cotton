<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Purchase Produce
		</legend>
		<fieldset>
			<legend>
				Details of <b>FBG 1</b>
			</legend>
			<p>
				<label><b>GD Id</b> </label>
				<label>254</label>
				<label><b>CPC Number</b> </label>
				<label>4456</label>
			</p>
			<p>
				<label><b>Farmer/Group Name</b> </label>
				<label>FBG group 1</label>
				<label><b>Hectares Available</b> </label>
				<label>200</label>
			</p>
			<p>
				<label><b>Field Officer Code</b> </label>
				<label>22546</label>
				<label><b>Field Officer Name</b> </label>
				<label>Brian Mwangi</label>
			</p>
		</fieldset>
		<table class="fullwidth">
			<caption>
				Farm Inputs Loaned
			</caption>
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
				</tr>
			</thead>
			<tbody>
				<tr>
					<td> 2265 </td>
					<td> 3/24/2012 </td>
					<td> Cotton Seeds (Grade 1) </td>
					<td> 2000 </td>
					<td> 30000 </td>
					<td> 2012 </td>
					<td> 4458 </td>
					<td> 6695 </td>
				</tr>
			</tbody>
		</table>
		<table class="fullwidth">
			<caption>
				Produce Purchased
			</caption>
			<thead>
				<tr>
					<th>Receipt No.</th>
					<th>Date</th>
					<th>Buyer</th>
					<th>Quantity</th>
					<th>Unit Price</th>
					<th>Loan Recovery</th>
					<th>Farmer Reg. Fee</th>
					<th>Other Recoveries</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
					<input class="receipt_number" name="receipt_number" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="purchase_date" name="purchase_date" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<select name="buyer" class="dropdown" style="width:50px">
						<option></option>
						<option>John Maina</option>
						<option>Steve Kawigira</option>
					</select></td>
					<td>
					<input class="purchase_quantity" name="purchase_quantity" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="unit_price" name="unit_price" type="text" value="100" readonly="" style="width: 50px"/>
					</td>
					<td>
					<input class="loan_recovery" name="loan_recovery" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="reg_fee" name="reg_fee" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input class="other" name="other" type="text" value=" " style="width: 50px"/>
					</td>
					<td>
					<input  class="button"   value="+" style="width:50px; text-align: center"/>
					</td>
				</tr>
			</tbody>
		</table>
		<p>
			<label for="depot_code">Depot Code: </label>
			<input class="depot_code" name="depot_code" type="text" value=" " style="width:100px;"/>
			<label for="purchased_value">Purchased Value: </label>
			<input class="purchased_value" name="purchased_value" type="text" value="0" style="width:100px;"/>
			<label for="net_value">Net Value: </label>
			<input class="net_value" name="net_value" type="text" value="0" style="width:100px;"/>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>