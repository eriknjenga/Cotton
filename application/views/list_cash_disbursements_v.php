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
	url = "<?php echo base_url().'cash_management/delete_disbursement/'?>
		" +$(this).attr("disbursement");
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
	<a href="<?php echo base_url().'cash_management/issue_cash'?>" class="button"><span class="ui-icon ui-icon-transferthick-e-w"></span>Issue Cash</a>
</p>
<h1>Cash Disbursement Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>CIH(c) Voucher No.</th>
			<th>Field Cashier</th>
			<th>Amount</th>
			<th>Date</th>
			<th>Action</th>
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
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $disbursement -> CIH;?>
		</td>
		<td>
		<?php echo $disbursement -> Field_Cashier_Object -> Field_Cashier_Name;?>
		</td>
		<td>
		<?php echo $disbursement -> Amount;?>
		</td>
		<td>
		<?php echo $disbursement -> Date;?>
		</td>
		<td><a href="<?php echo base_url()."cash_management/edit_disbursement/".$disbursement->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" disbursement = "<?php echo $disbursement -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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