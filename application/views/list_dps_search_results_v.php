<h1>DPS Search Results</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>DPN No.</th>
			<th>Date</th>
			<th>BC</th>
			<th>BC Code</th>
			<th>FBG</th>
			<th>FBG Gross Value</th>
			<th>Loan Recovery</th>
			<th>Free Farmer Value</th>
			<th>Batch</th>
			<th>Batch Status</th>
			<th>Batch Owner</th>
			<th>Adjustment</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($dps[0])) {
$counter = 1;
foreach ($dps as $purchase) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
$adjustment = "No";
if($purchase->Adjustment == "1"){
$adjustment = "Yes";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $purchase -> DPN;?>
		</td>
		<td>
		<?php echo $purchase -> Date;?>
		</td>
		<td>
		<?php echo $purchase -> Depot_Object -> Depot_Name;?>
		</td>
		<td>
		<?php echo $purchase -> Depot_Object -> Depot_Code;?>
		</td>
		<td>
		<?php echo $purchase -> FBG_Object -> Group_Name;?>
		</td>
		<td>
		<?php echo $purchase -> Gross_Value;?>
		</td>
		<td>
		<?php echo $purchase -> Loan_Recovery;?>
		</td>
		<td>
		<?php echo $purchase -> Free_Farmer_Value;?>
		</td>
		<td>
		<?php echo $purchase -> Batch;?>
		</td>
		<td><?php if($purchase->Batch_Status == 0){?>Open<?php } else if ($purchase->Batch_Status == 1) {echo "Closed";} else if ($purchase->Batch_Status == 2) {echo "Posted";}?></td>
		<td>
		<?php echo $purchase -> Batch_Object -> User_Object -> Name;?>
		</td>
		<td>
		<?php echo $adjustment;?>
		</td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>