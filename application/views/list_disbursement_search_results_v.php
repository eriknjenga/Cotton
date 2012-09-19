<h1>Invoice Search Results</h1>
<table class="fullwidth">
	<thead>
			<tr>
			<th>Invoice No.</th>
			<th>Date</th>
			<th>FBG Name</th>
			<th>Village</th>
			<th>Season</th>
			<th>Batch</th>
			<th>Batch Status</th>
			<th>Batch Owner</th> 
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($disbursements[0])) {
$counter = 1;
foreach ($disbursements as $disbursement) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $disbursement -> Invoice_Number;?>
		</td>
		<td>
		<?php echo $disbursement -> Date;?>
		</td>
		<td>
		<?php echo $disbursement -> FBG_Object -> Group_Name;?>
		</td>
		<td>
		<?php echo $disbursement -> FBG_Object->Village_Object -> Name;?>
		</td>
		<td>
		<?php echo $disbursement -> Season;?>
		</td>
		<td>
		<?php echo $disbursement -> ID_Batch;?>
		</td>
		<td><?php if($disbursement->Batch_Status == 0){?>Open<?php } else if ($disbursement->Batch_Status == 1) {echo "Closed";} else if ($disbursement->Batch_Status == 2) {echo "Posted";}?></td>
		<td>
		<?php echo $disbursement -> Batch_Object -> User_Object -> Name;?>
		</td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>