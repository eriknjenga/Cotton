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
<h1>Batch Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Batch ID</th>
			<th>Created On</th> 
			<th>Number of Transactions</th> 
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
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $batch -> id;?>
		</td>
		<td>
		<?php echo date("d-m-Y H:i:s",$batch -> Timestamp);?>
		</td> 
				<td>
		<?php echo sizeof($batch -> Purchases) + sizeof($batch -> Disbursements);?>
		</td> 
		<td><a href="<?php echo base_url()."batch_management/print_batch/".$batch->id?>" class="button"><span class="ui-icon ui-icon-print"></span>Print</a><a href="#" class="button delete" batch = "<?php echo $batch -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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