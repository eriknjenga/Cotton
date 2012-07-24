<script type="text/javascript">
	$(function() {
		$("#dpn_sequence_form").validationEngine();
	});

</script>
<div id="filter">
	<?php
	if (isset($sequence)) {
		$depot_id = $sequence -> Depot;
		$first = $sequence -> First;
		$last = $sequence -> Last;
		$season = $sequence -> Season;
		$sequence_id = $sequence -> id;
	} else {
		$depot_id = "";
		$first = "";
		$last = "";
		$season = "";
		$sequence_id = "";
	}
	$attributes = array("method" => "POST", "id" => "dpn_sequence_form");

	echo form_open('dpn_sequence_management/save', $attributes);
	echo validation_errors('
<p class="form_error">', '</p>
');
	?>
	<fieldset>
		<legend>
			Select Filter Options
		</legend>
		<input type="hidden" name="editing_id" value="<?php echo $sequence_id;?>" />
		<p>
			<label for="depot">Buying Center</label>
			<select name="depot" id="depot" class="validate[required]">
				<option></option>
				<?php
foreach($depots as $depot){
				?>
				<option value="<?php echo $depot -> id;?>" <?php if($depot->id == $depot_id){echo "selected";}?>><?php echo $depot -> Depot_Name . " (" . $depot -> Village_Object -> Name . ") (" . $depot -> Depot_Code . ")";?></option>
				<?php }?>
			</select>
		</p>
		<p>
			<label for="first">First Sequence Number</label>
			<input id="first" name="first" type="text" value="<?php echo $first;?>" class="validate[required]"/>
		</p>
		<p>
			<label for="last">Last Sequence Number</label>
			<input id="last" name="last" type="text" value="<?php echo $last;?>" class="validate[required]"/>
		</p>
		<p>
			<label for="season">Season</label>
			<input id="season" name="season" type="text" value="<?php echo $season;?>" class="validate[required]"/>
		</p>
		<input type="submit" name="assign" class="button"	value="Assign DPN Range" />
	</fieldset>
	</form>
</div>
