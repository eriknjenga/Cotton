	<style>
		.ui-autocomplete {
			overflow-y: auto;
			/* prevent horizontal scrollbar */
			overflow-x: hidden;
			/* add padding to account for vertical scrollbar */
			padding-right: 20px;
			max-width: 200px;
			font-size: 14px;
		}
		/* IE 6 doesn't support max-height
		 * we use height instead, but this forces the menu to always be this tall
		 */
		* html .ui-autocomplete {
			width: 200px;
		}
	</style>
<script type="text/javascript">
					$(function() {
		$("#purchase_form").validationEngine();
		$("#fbg").autocomplete({
			source : "<?php echo base_url();?>fbg_management/autocomplete_fbg",
				minLength : 1,
				select: function( event, ui ) {
				$( "#fbg" ).val( ui.item.label );
				$( "#fbg_id" ).val( ui.item.value );
				return false;
				}
				});
				$("#date").datepicker({
				defaultDate : new Date(),
				changeYear : true,
				changeMonth : true
				});
				$("#quantity").keyup(function() {
				var date_object = $("#date");
				updateCottonPrice(date_object);
				updateNetCalculations();
				});
				$("#date").change(function() {
				updateCottonPrice($(this));
				updateNetCalculations();
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
				$.each(price_dates_array, function(key, value) {
				if(value.length > 0 && selected_date_value.length > 0) {
				var price_date = new Date(value);
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
				var quantity = $("#quantity").attr("value");
				var total_value = 0;
				if(parseInt(quantity) != 0 && parseInt(most_current_price) > 0) {
				total_value = quantity * most_current_price;
				$("#purchased_value").attr("value", total_value);
				}
				}
</script>
<?php
if (isset($purchase)) {
	$depot = $purchase -> Depot_Object;
	$fbg = $purchase -> FBG;
	$fbg_name = $purchase -> FBG_Object -> Group_Name;
	$dpn = $purchase -> DPN;
	$date = $purchase -> Date;
	$depot_id = $purchase -> Depot;
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
	$batch = $purchase -> Batch;

} else {
	$fbg = "";
	$fbg_name = "";
	$dpn = "";
	$date = "";
	$depot_id = $depot -> id;
	$quantity = "";
	$price = "";
	$total_value = "0";
	$season = "";
	$loan_recovery = "0";
	$farmer_registration = "0";
	$other_recoveries = "0";
	$purchase_id = "";
	$buyer = $depot -> Buyer_Object -> Name;
	$buyer_id = $depot -> Buyer;
	$batch = "";

}
$total_deductions = $loan_recovery + $farmer_registration + $other_recoveries;
$net_value = $total_value - $total_deductions;
$attributes = array("method" => "post", "id" => "purchase_form");
echo form_open('purchase_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
if(isset($batch_information)){
?>
<div class="message information close">
	<h2>Batch Information</h2>
	<p>
		<?php echo $batch_information;?>
	</p>
</div>
<?php }

?>

</p>
<input type="hidden" name="editing_id" value="<?php echo $purchase_id;?>" />
<input type="hidden" name="depot" value="<?php echo $depot_id;?>" />
<input type="hidden" name="fbg_id" id="fbg_id" value="<?php echo $fbg;?>" />
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
<fieldset>
	<legend>
		Recording Purchases For <b><?php echo $depot -> Depot_Name . " (" . $depot -> Village_Object -> Name . ")";?></b>
	</legend>
	<p>
		<label><b>Buyer at Site</b> </label>
		<label><?php echo $depot -> Buyer_Object -> Name . " (" . $depot -> Buyer_Object -> Buyer_Code . ")";?></label>
	</p>
</fieldset>
<p>
	<label for="fbg">FBG</label>
	<input id="fbg" name="fbg" type="text" value="<?php echo $fbg_name;?>" />
	<span class="field_desc">Enter the relevant <b>FBG</b> for this transaction</span>
</p>
<p>
	<label for="dpn">DPN</label>
	<input id="dpn" name="dpn" type="text" value="<?php echo $dpn;?>" class="dpn validate[required]"/>
	<span class="field_desc">Enter the <b>DPN</b> for this transaction</span>
</p>
<p>
	<label for="date">Transaction Date</label>
	<input class="date validate[required]" id="date" name="date" type="text" value="<?php echo $date;?>"/>
	<span class="field_desc">Enter the <b>Date</b> for this transaction</span>
</p>

<p>
	<label for="quantity">Quantity</label>
	<input name="quantity" id="quantity"  type="text" value="<?php echo $quantity;?>"/>
	<span class="field_desc">Enter the <b>Quantity</b> purchased</span>
</p>
<p>
	<label for="price">Unit Price</label>
	<input readonly="" name="price" id="price"  type="text" value="<?php echo $price;?>" />
	<span class="field_desc">Enter the <b>price</b> that the produce was purchased for</span>
</p>
<p>
	<label for="season">Season</label>
	<input class="season" name="season" id="season" type="text" value="<?php echo $season;?>"/>
	<span class="field_desc">Enter the <b>season</b> that the produce was purchased</span>
</p>
<p>
	<label for="loan_recovery">Loan Recovery</label>
	<input  class="loan_recovery" name="loan_recovery" id="loan_recovery" type="text" value="<?php echo $loan_recovery;?>"/>
	<span class="field_desc">Enter the <b>loan recovery</b> amount that was deducted from the total amount payable to the FBG</span>
</p>
<p>
	<label for="farmer_registration">Farmer Registration Fees</label>
	<input class="farmer_registration" name="farmer_registration" id="farmer_registration" type="text" value="<?php echo $farmer_registration;?>"/>
	<span class="field_desc">Enter the <b>farmer registration fees</b> that were deducted from the total amount payable to the FBG</span>
</p>
<p>
	<label for="other_recoveries">Other Recoveries</label>
	<input class="other_recoveries" name="other_recoveries" id="other_recoveries" type="text" value="<?php echo $other_recoveries;?>"/>
	<span class="field_desc">Enter any <b>other recoveries</b> that were deducted from the total amount payable to the FBG</span>
</p>
<p>
	<label for="buyer">Buyer: </label>
	<input readonly="" class="buyer"  id="buyer" type="text" value="<?php echo $buyer;?>" style="width:100px;"/>
	<input name="buyer" id="buyer_id" type="hidden" value="<?php echo $buyer_id;?>" style="width:100px;"/>
	<label for="purchased_value">Purchased Value: </label>
	<input class="purchased_value" name="purchased_value" id="purchased_value" type="text" value="<?php echo $total_value;?>" style="width:100px;"/>
	<label for="total_deductions">Total Deductions: </label>
	<input class="total_deductions" name="total_deductions" id="total_deductions" type="text" value="<?php echo $total_deductions;?>" style="width:100px;"/>
	<label for="net_value">Net Value: </label>
	<input class="net_value" name="net_value" id="net_value" type="text" value="<?php echo $net_value;?>" style="width:100px;"/>
</p>
<p>
	<input class="button" type="reset" value="Reset">
	<input class="button" type="submit" value="Save & Add New From Buying Center" name="submit">
	<input class="button" type="submit" value="Save & View List" name="submit">
</p>
</fieldset> <!-- End of fieldset -->
</form>