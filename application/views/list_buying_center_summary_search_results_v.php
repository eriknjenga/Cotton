<h1>BC Summary Search Results</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Summary No.</th>
			<th>Date</th>
			<th>BC Name</th>
			<th>BC Code</th>
			<th>Closing Bags</th>
			<th>Closing Stock</th>
			<th>Closing Cash</th>
			<th>Batch</th>
			<th>Batch Status</th>
			<th>Batch Owner</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($summaries[0])) {
$counter = 1;
foreach ($summaries as $summary) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $summary -> Summary_Number;?>
		</td>
		<td>
		<?php echo $summary -> Date;?>
		</td>
		<td>
		<?php echo $summary -> Depot_Object -> Depot_Name;?>
		</td>
		<td>
		<?php echo $summary -> Depot_Object -> Depot_Code;?>
		</td>
		<td>
		<?php echo $summary -> Closing_Bags;?>
		</td>
		<td>
		<?php echo $summary -> Closing_Stock;?>
		</td>
		<td>
		<?php echo $summary -> Closing_Cash;?>
		</td>
		<td>
		<?php echo $summary -> Batch;?>
		</td>
		<td><?php if($summary->Batch_Status == 0){?>Open<?php } else if ($summary->Batch_Status == 1) {echo "Closed";} else if ($summary->Batch_Status == 2) {echo "Posted";}?></td>
		<td>
		<?php echo $summary -> Batch_Object -> User_Object -> Name;?>
		</td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>