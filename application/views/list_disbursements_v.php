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
	url = "<?php echo base_url().'disbursement_management/delete_disbursement/'?>
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
	<a href="<?php echo base_url().'disbursement_management/search_fbg'?>" class="button"><span class="ui-icon ui-icon-transferthick-e-w"></span>New Disbursement</a>
</p>
<h1>Input Disbursements Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Invoice No.</th>
			<th>Date</th>
			<th>FBG Name</th>
			<th>Village</th> 
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
		<td><?php if($disbursement->Batch_Status == 0){?><a href="<?php echo base_url()."disbursement_management/edit_disbursement/".$disbursement->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" disbursement = "<?php echo $disbursement -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a><?php } else {echo "Closed/Posted Transaction";}?></td>
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