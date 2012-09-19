<h1>Loan Recovery Receipt Search Results</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Receipt No.</th>
			<th>Date</th>
			<th>FBG</th>
			<th>Received From</th>
			<th>Amount</th>
			<th>Batch</th>
			<th>Batch Status</th>
			<th>Batch Owner</th>
			<th>Adjustment</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($receipt[0])) {
$counter = 1;
foreach ($receipt as $receipt_result) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
$adjustment = "No";
if($receipt_result->Adjustment == "1"){
$adjustment = "Yes";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $receipt_result -> Receipt_Number;?>
		</td>
		<td>
		<?php echo $receipt_result -> Date;?>
		</td>
		<td>
		<?php echo $receipt_result -> FBG_Object -> Group_Name;?>
		</td>
		<td>
		<?php echo $receipt_result -> Received_From;?>
		</td>
		<td>
		<?php echo $receipt_result -> Amount;?>
		</td>
		<td>
		<?php echo $receipt_result -> Batch;?>
		</td>
		<td><?php if($receipt_result->Batch_Status == 0){?>Open<?php } else if ($receipt_result->Batch_Status == 1) {echo "Closed";} else if ($receipt_result->Batch_Status == 2) {echo "Posted";}?></td>
		<td>
		<?php echo $receipt_result -> Batch_Object -> User_Object -> Name;?>
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