<style>
	.ui-autocomplete {
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		/* add padding to account for vertical scrollbar */
		padding-right: 20px;
		max-width: 400px;
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
$("#add_depot_input").validationEngine();
$("#buyer").keyup(function(event) {
if(event.keyCode == 8 && $("#buyer").attr("value") == ""){
$("#buyer_id").attr("value","");
}
});
$("#village").autocomplete({
source : "<?php echo base_url();?>fbg_management/autocomplete_village",
	minLength : 1,
	select: function( event, ui ) {
	$( "#village" ).val( ui.item.label );
	$( "#village_id" ).val( ui.item.value );
	return false;
	}
	});
	$("#buyer").autocomplete({
	source : "<?php echo base_url();?>buyer_management/autocomplete_buyer",
	minLength : 1,
	select: function( event, ui ) {
	$( "#buyer" ).val( ui.item.label );
	$( "#buyer_id" ).val( ui.item.value );
	return false;
	}
	});
	});
</script>
<?php
if (isset($depot)) {
	$depot_code = $depot -> Depot_Code;
	$depot_name = $depot -> Depot_Name;
	$buyer = $depot -> Buyer;
	$village = $depot -> Village_Object -> Name;
	$village_id = $depot -> Village;
	$capacity = $depot -> Capacity;
	$distance = $depot -> Distance;
	$depot_id = $depot -> id;
	$buyer_id = $depot -> Buyer;
	$purchase_route = $depot -> Purchase_Route;
	$cash_disbursement_route = $depot -> Cash_Disbursement_Route;
	$buyer = $depot -> Buyer_Object -> Name;
	$fbg = $depot -> FBG;
	$acre_yield = $depot -> Acre_Yield;
	$acreage = $depot -> Acreage;
} else {
	$depot_code = "";
	$depot_name = "";
	$buyer = "";
	$village = "";
	$village_id = "";
	$buyer = "";
	$buyer_id = "";
	$purchase_route = "";
	$cash_disbursement_route = "";
	$capacity = "";
	$distance = "";
	$depot_id = "";
	$fbg = "0";
	$acre_yield = "";
	$acreage = "";
}
$attributes = array("method" => "post", "id" => "add_depot_input");
echo form_open('depot_management/save', $attributes);
echo validation_errors('
<p class="form_error">
	', '
</p>
');
?> <!-- Fieldset -->
<fieldset>
	<legend>
		Add new Buying Center
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $depot_id;?>" />
	<input type="hidden" name="village_id" id="village_id" value="<?php echo $village_id;?>" />
	<input type="hidden" name="buyer_id" id="buyer_id" value="<?php echo $buyer_id;?>" />
	<p>
		<label for="depot_code">Code: </label>
		<input id="depot_code"  name="depot_code" type="text"  value="<?php echo $depot_code;?>" />
		<span class="field_desc">Enter the code for this Buying Center</span>
	</p>
	<p>
		<label for="depot_name">Name: </label>
		<input id="depot_name" name="depot_name" type="text" value="<?php echo $depot_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the name for this Buying Center</span>
	</p>
	<p>
		<label for="buyer">Buyer</label>
		<input id="buyer" name="buyer" type="text" value="<?php echo $buyer;?>"/>
		<span class="field_desc">Enter the buyer responsible for this center</span>
	</p>
	<p>
		<label for="village">Village</label>
		<input id="village" name="village" type="text" value="<?php echo $village;?>" class="village"/>
		<span class="field_desc">Enter the village where this Center is located</span>
	</p>
	<p>
		<label for="purchase_route">Purchase Route</label>
		<select name="purchase_route" class="dropdown" id="purchase_route">
			<option></option>
			<?php
foreach($purchase_routes as $route_object){?>
			<option value="<?php echo $route_object -> id;?>" <?php

			if ($route_object -> id == $purchase_route) {echo "selected";

			}
			?>><?php echo $route_object -> Route_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the purchase route for this Buying Center</span>
	</p>
	<p>
		<label for="cash_disbursement_route">Cash Disbursement Route</label>
		<select name="cash_disbursement_route" class="dropdown" id="cash_disbursement_route">
			<option></option>
			<?php
foreach($cash_disbursement_routes as $route_object){?>
			<option value="<?php echo $route_object -> id;?>" <?php

			if ($route_object -> id == $cash_disbursement_route) {echo "selected";

			}
			?>><?php echo $route_object -> Route_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select the cash disbursement route for this Buying Center</span>
	</p>
	<p>
		<label for="fbg">FBG/Non-FBG: </label>
		<input id="fbg" name="fbg" type="radio" value="1" <?php
			if ($fbg == "1") {echo "checked";
			};
		?>/>
		FBG
		<input id="fbg" name="fbg" type="radio" value="0" <?php
			if ($fbg == "0") {echo "checked";
			};
		?>/>
		Non-FBG
	<p>
		<label for="capacity">Capacity (Tonnes): </label>
		<input id="capacity" name="capacity" type="text" value="<?php echo $capacity;?>"/>
		<span class="field_desc">Enter the tonnage capacity for this Buying Center</span>
	</p>
	<p>
		<label for="distance">Distance (KM): </label>
		<input id="distance" name="distance" type="text" value="<?php echo $distance;?>"/>
		<span class="field_desc">Enter the distance of this buying center from the ginnery</span>
	</p>
	<p>
		<label for="acreage">Acreage</label>
		<input id="acreage" name="acreage" type="text" value="<?php echo $acreage;?>" class="acreage"/>
		<span class="field_desc">Enter the number of acres under this buying center</span>
	</p>
	<p>
		<label for="acre_yield">Yield/Acre</label>
		<input id="acre_yield" name="acre_yield" type="text" value="<?php echo $acre_yield;?>" class="acre_yield"/>
		<span class="field_desc">Enter the yield per acre expected from this buying center</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>