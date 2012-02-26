<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Register New Farm Input
		</legend>
		<p>
			<label for="product_code">Product Code: </label>
			<input class="product_code" name="product_code" type="text" value=" " />
			<span class="field_desc">Enter the product code for this input</span>
		</p>
		<p>
			<label for="product_name">Product Name: </label>
			<input class="product_name" name="product_name" type="text" value=" " />
			<span class="field_desc">Enter the product name for this input</span>
		</p>
		<p>
			<label for="product_desc">Product Description: </label>
			<input class="product_desc lf" name="product_desc" type="text" value=" " />
			<span class="field_desc">Enter the product name for this input</span>
		</p>
		<p>
			<label for="unit_price">Unit Price: </label>
			<input class="unit_price" name="unit_price" type="text" value=" " />
			<span class="field_desc">Enter the unit price for this item</span>
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>