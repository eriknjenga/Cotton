<script type="text/javascript">
	$(function() {
		$("#date").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true
		});
	});

</script>
<form method="post" action="<?php echo base_url().'depot_management/save_closure'?>">
	<!-- Fieldset -->
	<fieldset>
		<legend>
			Close Buying Center
		</legend>
		<input type="hidden" name="buying_center" value="<?php echo $depot -> id;?>" />
		<p>
			<label><B>Buying Center: </B></label>
			<label><?php echo $depot -> Depot_Name;?></label>
		</p>
		<p>
			<label><B>Center Code: </B></label>
			<label><?php echo $depot -> Depot_Code;?></label>
		</p>
		<p>
			<label><B>Village: </B></label>
			<label><?php echo $depot -> Village_Object -> Name;?></label>
		</p>
		<p>
			<label><B>Buyer: </B></label>
			<label><?php echo $depot -> Buyer_Object -> Name;?></label>
		</p>
		<p>
			<label for="date">Date Closed</label>
			<input id="date" name="date" type="text"/>
		</p>
		<p>
			<label for="reason">Reason for Closure</label>
			<textarea name="reason"></textarea>
		</p>
		<p>
			<input class="button" type="submit" value="Save">
			<input class="button" type="reset" value="Reset">
		</p>
	</fieldset>
	<!-- End of fieldset -->
</form>