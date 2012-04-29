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
			<th>ID</th>
			<th>Transaction Type</th>
			<th>Started On</th>
			<th>Opened By</th>
			<th>Entries</th>
			<th>Posted By</th>
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
		<?php echo $batch -> Transaction_Type_Object -> Name;?>
		</td>
		<td>
		<?php echo date("d/m/y H:i:s", $batch -> Timestamp);?>
		</td>
		<td>
		<?php echo $batch -> User_Object -> Name;?>
		</td>
		<td>
		<?php echo sizeof($batch -> Purchases) + sizeof($batch -> Disbursements);?>
		</td>
		<td>
		<?php echo $batch -> Validator_Object -> Name;?>
		</td>
		<td><a href="<?php echo base_url()."batch_management/print_batch/".$batch->id?>" class="button"><span class="ui-icon ui-icon-print"></span>Print</a><?php if($batch->Status != "2"){?><a href="<?php echo base_url()."batch_management/post_batch/".$batch->id?>" class="button" style="background: none; background-color: green; border-color: green;"><span class="ui-icon ui-icon-locked"></span>Post</a><?php } else {echo "Posted!";}?></td>
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