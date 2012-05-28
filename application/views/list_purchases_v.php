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
	url = "<?php echo base_url().'purchase_management/delete_purchase/'?>
		" +$(this).attr("purchase");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<div class="message information close">
	<h2>Batch Information</h2>
	<p>
		You are viewing records for batch number: <b><?php echo $batch;?></b>
	</p>
</div>
<p>
<a href="<?php echo base_url().'purchase_management/search_depot'?>" class="button"><span class="ui-icon ui-icon-cart"></span>New Purchase</a>
</p>
<h1>Produce Purchases Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>DPN No.</th>
			<th>Date</th>
			<th>FBG</th>
			<th>Buyer</th>
			<th>Quantity</th>
			<th>Unit Price</th>
			<th>Cash Payed</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($purchases[0])) {
$counter = 1;
foreach ($purchases as $purchase) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
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
		<?php echo $purchase -> FBG_Object->Group_Name;?>
		</td>
		<td>
		<?php echo $purchase -> Buyer_Object->Name;?>
		</td>
		<td>
		<?php echo $purchase -> Quantity;?>
		</td>
				<td>
		<?php echo $purchase -> Unit_Price;?>
		</td>
		<td>
		<?php echo $purchase -> Net_Value;?>
		</td>
		<td><?php if($purchase->Batch_Status == 0){?><a href="<?php echo base_url()."purchase_management/edit_purchase/".$purchase->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" purchase = "<?php echo $purchase -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a><?php } else {echo "Closed/Posted Transaction";}?></td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>
<div title="Confirm Delete!" id="confirm_delete" style="width: 300px; height: 150px; margin: 5px auto 5px auto;">
	Are you sure you want to delete this record?
</div>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div><?php endif;?>