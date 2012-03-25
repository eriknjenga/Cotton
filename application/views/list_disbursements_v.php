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
		" +$(this).attr("area");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>Farmer Input Loans Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Invoice No.</th>
			<th>Date</th>
			<th>FBG Name</th>
			<th>Input</th>
			<th>Quantity</th>
			<th>Total Value</th>
			<th>Season</th>
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
		<?php echo $disbursement -> FBG_Object->Group_Name;?>
		</td>
		<td>
		<?php echo $disbursement -> Farm_Input_Object->Product_Name;?>
		</td>
		<td>
		<?php echo $disbursement -> Quantity;?>
		</td>
		<td>
		<?php echo $disbursement -> Total_Value;?>
		</td>
		<td>
		<?php echo $disbursement -> Season;?>
		</td>
		<td><a href="<?php echo base_url()."disbursement_management/edit_disbursement/".$disbursement->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" area = "<?php echo $disbursement -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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