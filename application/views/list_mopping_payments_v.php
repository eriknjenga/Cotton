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
	url = "<?php echo base_url().'mopping_payment_management/delete_payment/'?>
		" +$(this).attr("payment");
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
	<a href="<?php echo base_url().'mopping_payment_management/new_payment'?>" class="button"><span class="ui-icon ui-icon-suitcase"></span>New Buying Center Expense</a>
</p>
<h1>Buying Center Expenses Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Voucher No.</th>
			<th>Date</th>
			<th>Buying Center</th>
			<th>Amount</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($payments[0])) {
$counter = 1;
foreach ($payments as $payment) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $payment -> Voucher_Number;?>
		</td>
		<td>
		<?php echo $payment -> Date;?>
		</td>
		<td>
		<?php echo $payment -> Depot_Object -> Depot_Name;?>
		</td>
		<td>
		<?php echo $payment -> Amount;?>
		</td>
		<td><a href="<?php echo base_url()."mopping_payment_management/edit_payment/".$payment->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" payment = "<?php echo $payment -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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