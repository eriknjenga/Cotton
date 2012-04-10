<script type="text/javascript">
	$(function() {
		$("#purchase_form").validationEngine();
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
		$(".quantity").keyup(function() {
			var date_object = $(this).closest("tr").find(".date");
			updateCottonPrice(date_object);
			updateNetCalculations();
		});
		$(".date").change(function() {
			updateCottonPrice($(this));
		});
		$(".depot").change(function() {
			$("#buyer").attr("value", "");
			$("#buyer_id").attr("value", "");
			$("#buyer").attr("value", $(this).find(":selected").attr("buyer"));
			$("#buyer_id").attr("value", $(this).find(":selected").attr("buyer_id"));
		});

		$("#loan_recovery").keyup(function() {
			updateNetCalculations();
		});
		$("#farmer_registration").keyup(function() {
			updateNetCalculations();
		});
		$("#other_recoveries").keyup(function() {
			updateNetCalculations();
		});
	});
	function updateNetCalculations() {
		var gross_total = $("#purchased_value").attr("value");
		var total_deductions = parseInt($("#loan_recovery").attr("value")) + parseInt($("#farmer_registration").attr("value")) + parseInt($("#other_recoveries").attr("value"));
		$("#total_deductions").attr("value", total_deductions);
		$("#net_value").attr("value", parseInt(gross_total) - parseInt(total_deductions));

	}

	function updateCottonPrice(date_object) {
		var prices = $("#cotton_prices").attr("prices");
		var price_dates = $("#cotton_prices").attr("price_dates");
		var price_dates_array = price_dates.split(",");
		var prices_array = prices.split(",");
		var selected_date_value = date_object.attr("value");
		var selected_date = new Date(selected_date_value);
		var difference = 0;
		var most_current_price = 0;
		var counter = 0;
		$.each(price_dates_array, function() {
			if(this.length > 0 && selected_date_value.length > 0) {
				var price_date = new Date(this);
				var day_difference = Math.floor((selected_date - price_date) / 86400000);
				if(day_difference >= 0 && (difference == 0 || day_difference < difference)) {
					difference = day_difference;
					most_current_price = prices_array[counter];
				}
			}
			counter++;
		});
		$("#price").attr("value", most_current_price);
		//Clear out the 'total value' field
		$("#purchased_value").attr("value", "");
		var quantity = date_object.closest("tr").find(".quantity").attr("value");
		var total_value = 0;
		if(parseInt(quantity) >= 0 && parseInt(most_current_price) > 0) {
			total_value = quantity * most_current_price;
			$("#purchased_value").attr("value", total_value);
		}
	}
</script>
<?php
if (isset($purchase)) {
	$fbg_id = $purchase -> FBG;
	$fbg = $purchase -> FBG_Object;
	$dpn = $purchase -> DPN;
	$date = $purchase -> Date;
	$depot = $purchase -> Depot;
	$quantity = $purchase -> Quantity;
	$total_value = $purchase -> Gross_Value;
	$season = $purchase -> Season;
	$loan_recovery = $purchase -> Loan_Recovery;
	$farmer_registration = $purchase -> Farmer_Reg_Fee;
	$other_recoveries = $purchase -> Other_Recoveries;
	$purchase_id = $purchase -> id;
	$price = $purchase -> Unit_Price;
	$buyer_id = $purchase -> Buyer;
	$buyer = $purchase -> Buyer_Object -> Name;

} else {
	$fbg_id = "";
	$dpn = "";
	$date = "";
	$depot = "";
	$quantity = "";
	$price = "";
	$total_value = "0";
	$season = "";
	$loan_recovery = "0";
	$farmer_registration = "0";
	$other_recoveries = "0";
	$purchase_id = "";
	$buyer = "";
	$buyer_id = "";
	

}
$total_deductions = $loan_recovery+$farmer_registration+$other_recoveries;
$net_value = $total_value - $total_deductions;
$attributes = array("method" => "post", "id" => "purchase_form");
echo form_open('purchase_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Details of <b><?php echo $fbg -> Group_Name;?></b>
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $purchase_id;?>" />
	<input type="hidden" name="fbg" value="<?php echo $fbg -> id;?>" />
	<input type="hidden" id="cotton_prices" prices="<?php
	foreach ($prices as $price_object) {
		echo $price_object -> Price . ',';
	}
	?>"
	price_dates="<?php
	foreach ($prices as $price_object) {
		echo $price_object -> Date . ',';
	}
	?>" />
	<input type="hidden" value="" id="product_price"/>
	<p>
		<label><b>Contract Number</b> </label>
		<label><?php echo $fbg -> CPC_Number;?></label>
	</p>
	<p>
		<label><b>Group Name</b> </label>
		<label><?php echo $fbg -> Group_Name;?></label>
		<label><b>Hectares Available</b> </label>
		<label><?php echo $fbg -> Hectares_Available;?></label>
	</p>
	<p>
		<label><b>Field Officer Code</b> </label>
		<label><?php echo $fbg -> Officer_Object -> Officer_Code;?></label>
		<label><b>Field Officer Name</b> </label>
		<label><?php echo $fbg -> Officer_Object -> Officer_Name;?></label>
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
		</tr>
	</thead>
	<tbody>
		<?php
foreach($disbursements as $disbursement){
		?>
		<tr>
			<td><?php echo $disbursement -> Invoice_Number;?></td>
			<td><?php echo $disbursement -> Date;?></td>
			<td><?php echo $disbursement -> Farm_Input_Object -> Product_Name;?></td>
			<td><?php echo $disbursement -> Quantity;?></td>
			<td><?php echo $disbursement -> Total_Value;?></td>
			<td><?php echo $disbursement -> Season;?></td>
		</tr>
		<?php }?>
	</tbody>
</table>
<table class="fullwidth" id="purchases_table" style="margin:0 auto;">
	<caption>
		Produce Purchased
	</caption>
	<thead>
		<tr>
			<th>DPN</th>
			<th>Date</th>
			<th>Depot</th>
			<th>Quantity</th>
			<th>Unit Price</th>
			<th>Season</th>
			<th>Loan Recovery</th>
			<th>Farmer Reg. Fee</th>
			<th>Other Recoveries</th>
		</tr>
	</thead>
	<tbody>
		<tr input_row="1">
			<td>
			<input id="dpn" name="dpn" type="text" value="<?php echo $dpn;?>" class="dpn validate[required]" style="width: 40px; padding:2px;"/>
			</td>
			<td>
			<input class="date validate[required]" id="date" name="date" type="text" value="<?php echo $date;?>" style="width: 40px; padding:2px;"/>
			</td>
			<td>
			<select name="depot" id="depot" class="dropdown depot validate[required]" style="width: 70px; padding:2px;">
				<option></option>
				<?php
foreach($depots as $depot_object){
				?>
				<option buyer = "<?php echo $depot_object -> Buyer_Object -> Name;?>" buyer_id = "<?php echo $depot_object -> Buyer_Object -> id;?>"
				value="<?php echo $depot_object -> id;?>" <?php
				if ($depot_object -> id == $depot) {echo "selected";
				}
				?> ><?php echo $depot_object -> Depot_Name;?></option>
				<?php }?>
			</select></td>
			<td>
			<input class="quantity validate[required,number]" name="quantity" id="quantity"  type="text" value="<?php echo $quantity;?>" style="width: 40px; padding:2px;"/>
			</td>
			<td>
			<input class="price validate[required,number]" readonly="" name="price" id="price"  type="text" value="<?php echo $price;?>" style="width: 40px; padding:2px;"/>
			</td>
			<td>
			<input class="season" name="season" id="season" type="text" value="<?php echo $season;?>" style="width: 40px; padding:2px;"/>
			</td>
			<td>
			<input  class="loan_recovery" name="loan_recovery" id="loan_recovery" type="text" value="<?php echo $loan_recovery;?>" style="width: 40px; padding:2px;"/>
			</td>
			<td>
			<input class="farmer_registration" name="farmer_registration" id="farmer_registration" type="text" value="<?php echo $farmer_registration;?>" style="width: 40px; padding:2px;"/>
			</td>
			<td>
			<input class="other_recoveries" name="other_recoveries" id="other_recoveries" type="text" value="<?php echo $other_recoveries;?>" style="width: 40px; padding:2px;"/>
			</td>
		</tr>
	</tbody>
</table>
<p></p>
<p>
	<label for="buyer">Buyer: </label>
	<input class="buyer"  id="buyer" type="text" value="<?php echo $buyer;?>" style="width:100px;"/>
	<input name="buyer" id="buyer_id" type="hidden" value="<?php echo $buyer_id;?>" style="width:100px;"/>
	<label for="purchased_value">Purchased Value: </label>
	<input class="purchased_value" name="purchased_value" id="purchased_value" type="text" value="<?php echo $total_value;?>" style="width:100px;"/>
	<label for="total_deductions">Total Deductions: </label>
	<input class="total_deductions" name="total_deductions" id="total_deductions" type="text" value="<?php echo $total_deductions;?>" style="width:100px;"/>
	<label for="net_value">Net Value: </label>
	<input class="net_value" name="net_value" id="net_value" type="text" value="<?php echo $net_value;?>" style="width:100px;"/>
</p>
<p>
	<input class="button" type="submit" value="Submit">
	<input class="button" type="reset" value="Reset">
</p>
</fieldset> <!-- End of fieldset -->
</form>