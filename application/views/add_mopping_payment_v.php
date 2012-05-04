<form method="post" action="#">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			New Mopping Payment
		</legend>
		<p>
			<label for="batch_number">Voucher Number: </label>
			<input class="batch_number" name="batch_number" type="text" value=" " />
		</p>
		<p>
			<label for="date">Depot: </label>
						<select name="depot[]" class="dropdown depot validate[required]" id="depot_<?php echo $counter;?>" >
					<option></option>
					<?php
foreach($depots as $depot_object){
					?>
					<option value="<?php echo $depot_object -> id;?>"><?php echo $depot_object -> Depot_Name;?></option>
					<?php
					$counter++;
					}
					?>
				</select>
		</p>
				<p>
			<label for="date">Amount: </label>
			<input class="date" name="date" type="text" value=" " />
		</p>
		<p>
			<input class="button" type="submit" value="Submit">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>