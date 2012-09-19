<style>
	.search_box {
		width: 250px;
		float: left;
		margin-right: 25px;
	}
</style>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('purchase_management/search_dps', $attributes);
	?>
	<fieldset>
		<legend>
			Search DPS
		</legend>
		<p>
			<label for="search_value">DPS Number: </label>
			<input name="search_value" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('cash_management/search_cih', $attributes);
	?>
	<fieldset>
		<legend>
			Search CIH(c)
		</legend>
		<p>
			<label for="search_value2">Voucher Number: </label>
			<input name="search_value2" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('field_cash_management/search_cih', $attributes);
	?>
	<fieldset>
		<legend>
			Search CIH(b)
		</legend>
		<p>
			<label for="search_value3">Voucher Number: </label>
			<input name="search_value3" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('field_cash_management/search_bcr', $attributes);
	?>
	<fieldset>
		<legend>
			Search BCR
		</legend>
		<p>
			<label for="search_value4">BCR Number: </label>
			<input name="search_value4" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('cash_receipt_management/search_receipt', $attributes);
	?>
	<fieldset>
		<legend>
			Search FC Cash Return Receipt
		</legend>
		<p>
			<label for="search_value5">Receipt Number: </label>
			<input name="search_value5" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('buying_center_receipt_management/search_receipt', $attributes);
	?>
	<fieldset>
		<legend>
			Search BC Cash Return Receipt
		</legend>
		<p>
			<label for="search_value6">Receipt Number: </label>
			<input name="search_value6" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('weighbridge_management/search_ticket', $attributes);
	?>
	<fieldset>
		<legend>
			Search Weighbridge Ticket
		</legend>
		<p>
			<label for="search_value7">Ticket Number: </label>
			<input name="search_value7" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('disbursement_management/search_invoice', $attributes);
	?>
	<fieldset>
		<legend>
			Search Input Invoice
		</legend>
		<p>
			<label for="search_value8">Invoice Number: </label>
			<input name="search_value8" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('loan_recovery_receipt_management/search_receipt', $attributes);
	?>
	<fieldset>
		<legend>
			Search Input Invoice
		</legend>
		<p>
			<label for="search_value9">Invoice Number: </label>
			<input name="search_value9" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>
<div class="search_box">
	<?php
	$attributes = array("method" => "post");
	echo form_open('buying_center_summary_management/search_summary', $attributes);
	?>
	<fieldset>
		<legend>
			Search Buying Center Summary
		</legend>
		<p>
			<label for="search_value10">BCS Number: </label>
			<input name="search_value10" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>