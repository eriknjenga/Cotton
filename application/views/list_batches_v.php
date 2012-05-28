<script type="text/javascript">
		var url = "";
	$(function() {
	$("#confirm_delete").dialog( {
	height: 150,
	width: 300,
	modal: true,
	autoOpen: false,
	buttons: {
	"Delete Record": function() {
	delete_record();
	},
	Cancel: function() {
	$( this ).dialog( "close" );
	}
	}

	} );

	$(".delete").click(function(){
	url = "<?php echo base_url().'batch_management/delete_batch/'?>
		" +$(this).attr("batch");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<p>
	<a href="<?php echo base_url().'batch_management/new_batch'?>" class="button"><span class="ui-icon ui-icon-note"></span>New Batch</a>
</p>
<h1>Batch Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>ID</th>
			<th>Transaction Type</th>
			<th>Timestamp</th>
			<th>Entries</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($batches[0])) {
$counter = 1;
foreach ($batches as $batch) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
$statuses = array("Open","Closed","Posted");
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $batch -> id;?>
		</td>
		<td>
		<?php echo $batch -> Transaction_Type_Object -> Name . " (" . $statuses[$batch -> Status] . ")";?>
		</td>
		<td>
		<?php echo date("d/m/y H:i:s", $batch -> Timestamp);?>
		</td>
		<td>
		<?php
		//Check type of batch and get total number of transactions
		if ($batch -> Transaction_Type_Object -> Indicator == "purchases") {
			echo sizeof($batch -> Purchases);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "input_disbursements") {
			echo sizeof($batch -> Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "agent_input_disbursements") {
			echo sizeof($batch -> Agent_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "buying_center_receipts") {
			echo sizeof($batch -> Buying_Center_Receipts);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "cash_receipts") {
			echo sizeof($batch -> Cash_Receipts);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "cihc") {
			echo sizeof($batch -> Cash_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "cihb") {
			echo sizeof($batch -> Field_Cash_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "input_transfers") {
			echo sizeof($batch -> Region_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "mopping_payments") {
			echo sizeof($batch -> Mopping_Payments);
		}
		?>
		</td>
		<td><a href="<?php echo base_url()."batch_management/print_batch/".$batch->id?>" class="button"><span class="ui-icon ui-icon-print"></span>Print</a><?php if($batch->Status != 2){?><a href="<?php echo base_url()."batch_management/enter_batch/".$batch->id?>" class="button" style="background: none; background-color: green; border-color: green;"><span class="ui-icon ui-icon-folder-open"></span>Open</a><?php } if($batch->Status == 0){?><a href="<?php echo base_url()."batch_management/close_batch/".$batch->id?>" class="button" style="background: none; background-color: #E01B1B; border-color: #E01B1B;"><span class="ui-icon ui-icon-folder-collapsed"></span>Close</a><?php } else if($batch->Status == 2){echo "Already Posted! ";}?><?php if($batch->Status != 2){?><a href="#" class="button delete" batch = "<?php echo $batch -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a><?php }?></td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>
<div title="Confirm Delete!" id="confirm_delete" style="width: 300px; height: 150px; margin: 5px auto 5px auto;">
	Are you sure you want to delete this batch plus all its transactions?
</div>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div><?php endif;?>