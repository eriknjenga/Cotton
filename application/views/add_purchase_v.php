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
	$(".submit").click(function(){ 
			var free_totals = checkFreeTotals();
			var fbg_totals = checkFbgTotals();
			if(free_totals == true && fbg_totals == true){
				$("#purchase_form").validationEngine(); 
			}
			else{
				return false;
			}
			
		});
		
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
				$("#purchased_value").keyup(function() {
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
				if(parseInt(gross_total) != '') {
				$("#net_value").attr("value", parseInt(gross_total) - parseInt(total_deductions));
				//$("#purchased_value").attr("value", total_value);
				}
				}
				function checkFreeTotals() {
				var quantity = $("#free_farmer").attr("value");
				var unit_price = $("#price").attr("value");
				var total_value = $("#free_farmer_value").attr("value");
				//Check if there are actual values in these variables
				if(parseInt(quantity)% 1 === 0 && parseInt(unit_price) % 1 === 0 &&parseInt(total_value)% 1 === 0) {
					$theoretical_total = quantity*unit_price;
					if($theoretical_total != total_value){ 
						$("#free_discrepancy_message").html("The wrong quantity or total value was indicated for the free farmer section");
						$("#free_discrepancy_container").slideDown("slow");
						return false;
					}
					else{
						$("#free_discrepancy_container").slideUp("slow");
						return true;
					}
				}
				else{
					$("#free_discrepancy_message").html("Confirm the transaction date to pick the correct unit price");
					$("#free_discrepancy_container").slideDown("slow");
					return false;
				}
				}
				function checkFbgTotals() {
				var quantity = $("#quantity").attr("value");
				var unit_price = $("#price").attr("value");
				var total_value = $("#purchased_value").attr("value");
				//Check if there are actual values in these variables
				if(parseInt(quantity)% 1 === 0 && parseInt(unit_price) % 1 === 0 &&parseInt(total_value)% 1 === 0) {
					$theoretical_total = quantity*unit_price;
					if($theoretical_total != total_value){
						$("#fbg_discrepancy_message").html("The wrong quantity or total value was indicated for the fbg section");
						$("#fbg_discrepancy_container").slideDown("slow");
						return false;
					}
					else{
						$("#fbg_discrepancy_container").slideUp("slow");
						return true;
					}
				}
				else{ 
					$("#fbg_discrepancy_message").html("Confirm the transaction date to pick the correct unit price");
					$("#fbg_discrepancy_container").slideDown("slow");
					return false;
				}
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
				//$("#purchased_value").attr("value", "");
				var quantity = $("#quantity").attr("value");
				var total_value = 0;
				if(parseInt(quantity) != 0 && parseInt(most_current_price) > 0) {
				total_value = quantity * most_current_price;
				//$("#purchased_value").attr("value", total_value);
				}
				else if(parseInt(quantity) == 0){
					$("#purchased_value").attr("value", 0);
				}
				}
</script>
<style>
	.dps_details{
		width: 42%;
		float:left;
		margin:5px;
	} 
	.details_panel{
		width: 100%; 
		overflow: hidden;
	}
</style>
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
	$free_farmer = $purchase -> Free_Farmer_Quantity;
	$free_farmer_value = $purchase -> Free_Farmer_Value;

} else {
	$fbg = "";
	$fbg_name = "";
	$dpn = "";
	$date = "";
	$depot_id = $depot -> id;
	$quantity = "0";
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
	$free_farmer = "0";
	$free_farmer_value = "0";

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
<input name="buyer" id="buyer_id" type="hidden" value="<?php echo $buyer_id;?>" style="width:100px;"/>
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
<div class="details_panel">
<div class="dps_details">
	<label><b>Buying Center</b></label>
	<label style="width: auto"><?php echo $depot -> Depot_Name . " (" . $depot -> Village_Object -> Name . ")";?></label>
	</div>
	<div class="dps_details">
		<label><b>Buyer at Site</b> </label>
		<label style="width: auto"><?php echo $depot -> Buyer_Object -> Name . " (" . $depot -> Buyer_Object -> Buyer_Code . ")";?></label>
	</div>
	</div>
	<div class="details_panel">
<div class="dps_details">
	<p>
	<label for="dpn">DPN</label>
	<input id="dpn" name="dpn" type="text" value="<?php echo $dpn;?>" class="dpn validate[required]"/> 
</p>
<p>
	<label for="season">Season</label>
	<input class="season validate[required]" name="season" id="season" type="text" value="<?php echo $season;?>"/> 
</p>
</div>
<div class="dps_details">
	<p>
	<label for="date">Transaction Date</label>
	<input class="date validate[required]" id="date" name="date" type="text" value="<?php echo $date;?>"/> 
</p>
<p>
	<label for="price">Unit Price</label>
	<input readonly="" name="price" id="price"  type="text" value="<?php echo $price;?>" />
	<span class="field_desc">(Dependent on the date)</span>
</p>
</div>
</div>



<div class="details_panel">
<fieldset class="dps_details">
<legend>Free-farmer Purchases</legend>	
<p>
	<label for="free_farmer">Total Quantity</label>
	<input name="free_farmer" id="free_farmer"  type="text" value="<?php echo $free_farmer;?>" class="validate[required,custom[integer]]"/> 
</p>
<p>
	<label for="free_farmer">Total Value</label>
	<input name="free_farmer_value" id="free_farmer_value"  type="text" value="<?php echo $free_farmer_value;?>" class="validate[required]"/> 
</p>
</fieldset>
<fieldset class="dps_details">
<legend>FBG Purchases</legend>
<p>
	<label for="fbg">FBG</label>
	<input id="fbg" name="fbg" type="text" value="<?php echo $fbg_name;?>" /> 
</p>
<p>
	<label for="quantity">FBG Quantity</label>
	<input name="quantity" id="quantity"  type="text" value="<?php echo $quantity;?>" class="validate[required,custom[integer]]"/> 
</p>	
<p>
	<label for="loan_recovery">Loan Recovery</label>
	<input name="loan_recovery" id="loan_recovery" type="text" value="<?php echo $loan_recovery;?>" class="loan_recovery validate[required,custom[integer]]"/> 
</p>
<p>
	<label for="farmer_registration">Farmer Reg. Fees</label>
	<input name="farmer_registration" id="farmer_registration" type="text" value="<?php echo $farmer_registration;?>" class="farmer_registration validate[required,custom[integer]]"/> 
</p>
<p>
	<label for="other_recoveries">Other Recoveries</label>
	<input name="other_recoveries" id="other_recoveries" type="text" value="<?php echo $other_recoveries;?>" class="other_recoveries validate[required,custom[integer]]"/> 
</p>
<p>
	<label for="purchased_value">Total Value</label>
	<input name="purchased_value" id="purchased_value" type="text" value="<?php echo $total_value;?>"   class="purchased_value validate[required,custom[integer]]"/>
</p>
<p>
	<label for="total_deductions">Total Deductions: </label>
	<input name="total_deductions" id="total_deductions" type="text" value="<?php echo $total_deductions;?>" class="total_deductions validate[required,custom[integer]]" />
</p>
<p>
	<label for="net_value">Net Value: </label>
	<input name="net_value" id="net_value" type="text" value="<?php echo $net_value;?>"  class="net_value validate[required,custom[integer]]"/>
</p>
</fieldset>
</div>
<div class="message error close" id="free_discrepancy_container" style="display:none">
	<h2>Free Farmer Discrepancy Detected</h2>
	<p id="free_discrepancy_message">
		 
	</p>
</div>
<div class="message error close" id="fbg_discrepancy_container" style="display:none">
	<h2>FBG Discrepancy Detected</h2>
	<p id="fbg_discrepancy_message">
		 
	</p>
</div>
<p>
	<input class="button" type="reset" value="Reset">
	<input class="button submit" type="submit" value="Save & Add New From Buying Center" name="submit">
	<input class="button submit" type="submit" value="Save & View List" name="submit">
</p>
</fieldset> <!-- End of fieldset -->
</form>