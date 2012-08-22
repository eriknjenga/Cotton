<h1>CIH(c) Search Results</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>CIH(c) No.</th>
			<th>Date</th>
			<th>Field Cashier</th>
			<th>Amount</th>
			<th>Batch</th>
			<th>Batch Status</th>
			<th>Batch Owner</th>
			<th>Adjustment</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($cih[0])) {
$counter = 1;
foreach ($cih as $cih_result) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
$adjustment = "No";
if($cih_result->Adjustment == "1"){
	$adjustment = "Yes";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $cih_result -> CIH;?>
		</td>
		<td>
		<?php echo $cih_result -> Date;?>
		</td>
		<td>
		<?php echo $cih_result -> Field_Cashier_Object -> Field_Cashier_Name;?>
		</td>
		<td>
		<?php echo $cih_result -> Amount;?>
		</td>
		<td>
		<?php echo $cih_result -> Batch;?>
		</td>
		<td><?php if($cih_result->Batch_Status == 0){?>Open<?php } else if ($cih_result->Batch_Status == 1) {echo "Closed";} else if ($cih_result->Batch_Status == 2) {echo "Posted";}?></td>
		<td>
		<?php echo $cih_result -> Batch_Object->User_Object->Name;?>
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