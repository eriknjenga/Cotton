<h1>Weighbridge Search Results</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Ticket No.</th>
			<th>Transaction Date</th>
			<th>Vehicle Number</th>
			<th>BC</th>
			<th>BC Code</th>
			<th>Bags</th>
			<th>First Weight</th>
			<th>Second Weight</th>
			<th>Net Weight</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($ticket[0])) {
$counter = 1;
foreach ($ticket as $ticket_result) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $ticket_result -> Ticket_Number;?>
		</td>
		<td>
		<?php echo $ticket_result -> Transaction_Date;?>
		</td>
		<td>
		<?php echo $ticket_result -> Vehicle_Number;?>
		</td> 
		<td>
		<?php echo $ticket_result -> Depot_Object -> Depot_Name;?>
		</td>
		<td>
		<?php echo $ticket_result -> Depot_Object -> Depot_Code;?>
		</td> 
		<td>
		<?php echo $ticket_result -> Number_Of_Bags;?>
		</td>
		<td>
		<?php echo $ticket_result -> First_Weight;?>
		</td>
		<td>
		<?php echo $ticket_result -> Second_Weight;?>
		</td>
		<td>
		<?php echo $ticket_result -> Net_Weight;?>
		</td>
		
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>