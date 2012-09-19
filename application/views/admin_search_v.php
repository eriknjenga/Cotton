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
	echo form_open('depot_management/search', $attributes);
	?>
	<fieldset>
		<legend>
			Search Buying Center
		</legend>
		<p>
			<label for="search_value">Buying Center Name: </label>
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
	echo form_open('district_management/search_district', $attributes);
	?>
	<fieldset>
		<legend>
			Search District
		</legend>
		<p>
			<label for="search_value2">District Name : </label>
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
	echo form_open('ward_management/search_ward', $attributes);
	?>
	<fieldset>
		<legend>
			Search Ward
		</legend>
		<p>
			<label for="search_value3">Ward Name: </label>
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
	echo form_open('village_management/search_village', $attributes);
	?>
	<fieldset>
		<legend>
			Search Village
		</legend>
		<p>
			<label for="search_value4">Village Name: </label>
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
	echo form_open('user_management/search_user', $attributes);
	?>
	<fieldset>
		<legend>
			Search System User
		</legend>
		<p>
			<label for="search_value5">Search Value: </label>
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
	echo form_open('field_officer_management/search_feo', $attributes);
	?>
	<fieldset>
		<legend>
			Search FEO
		</legend>
		<p>
			<label for="search_value6">FEO Name: </label>
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
	echo form_open('agent_management/search_agent', $attributes);
	?>
	<fieldset>
		<legend>
			Search Input Agent
		</legend>
		<p>
			<label for="search_value7">Agent Name: </label>
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
	echo form_open('buyer_management/search_buyer', $attributes);
	?>
	<fieldset>
		<legend>
			Search Buyer
		</legend>
		<p>
			<label for="search_value8">Buyer Name: </label>
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
	echo form_open('field_cashier_management/search_cashier', $attributes);
	?>
	<fieldset>
		<legend>
			Search Field Cashier
		</legend>
		<p>
			<label for="search_value9">Cashier Name: </label>
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
	echo form_open('truck_management/search_truck', $attributes);
	?>
	<fieldset>
		<legend>
			Search Truck
		</legend>
		<p>
			<label for="search_value10">Truck Registration: </label>
			<input name="search_value10" type="text" value=""/>
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
	echo form_open('fbg_management/search_fbg_simple', $attributes);
	?>
	<fieldset>
		<legend>
			Search FBG
		</legend>
		<p>
			<label for="search_value11">FBG Name: </label>
			<input name="search_value11" type="text" value=""/>
		</p>
		<p>
			<input class="button submit" type="submit" value="Search" name="submit">
		</p>
		</form>
	</fieldset>
</div>