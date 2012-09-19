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
	url = "<?php echo base_url().'buying_center_summary_management/delete_summary/'?>
		" +$(this).attr("receipt");
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
	<a href="<?php echo base_url().'buying_center_summary_management/new_summary'?>" class="button"><span class="ui-icon ui-icon-arrowthickstop-1-w"></span>New Buying Center Summary</a>
</p>
<h1>Buying Center Summaries Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Summary No.</th>
			<th>Date</th>
			<th>Buying Center</th>
			<th>Prepared By</th>
			<th>Opening Cash</th> 
			<th>Closing Cash</th> 
			<th>Action</th>
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
		<?php echo $summary ->Depot_Object -> Depot_Name;?>
		</td> 
		<td>
		<?php echo $summary -> Prepared_By;?>
		</td> 
		<td>
		<?php echo $summary -> Opening_Cash;?>
		</td>
		<td>
		<?php echo $summary -> Closing_Cash;?>
		</td>  
		<td><a href="<?php echo base_url()."buying_center_summary_management/edit_summary/".$summary->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" receipt = "<?php echo $summary -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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