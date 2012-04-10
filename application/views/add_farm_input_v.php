<script type="text/javascript">
	$(function() {
		$("#add_farm_input").validationEngine();
	});

</script>
<?php
if (isset($input)) {
	$product_code = $input -> Product_Code;
	$product_name = $input -> Product_Name;
	$product_desc = $input -> Product_Description; 
	$product_id = $input -> id;
} else {
	$product_code = "";
	$product_name = "";
	$product_desc = ""; 
	$product_id = "";
}
$attributes = array("method" => "post", "id" => "add_farm_input");
echo form_open('farm_input_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Register New Farm Input
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $product_id;?>" />
	<p>
		<label for="product_code">Product Code: </label>
		<input id="product_code"  name="product_code" type="text"  value="<?php echo $product_code;?>" class="validate[required]"/>
		<span class="field_desc">Enter the product code for this input</span>
	</p>
	<p>
		<label for="product_name">Product Name: </label>
		<input id="product_name" name="product_name" type="text" value="<?php echo $product_name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the product name for this input</span>
	</p>
	<p>
		<label for="product_desc">Product Description: </label>
		<input class="product_desc lf" name="product_desc" value="<?php echo $product_desc;?>" type="text"  />
		<span class="field_desc">Enter a short description for this input</span>
	</p>
	<p>
		<label for="unit_price">Unit Price: </label>
		<input id="unit_price" name="unit_price" type="text"class="validate[required]"/>
		<span class="field_desc">Enter the unit price for this item</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>