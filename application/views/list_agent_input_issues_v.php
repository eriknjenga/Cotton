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
	url = "<?php echo base_url().'agent_input_issue_management/delete_issue/'?>
		" +$(this).attr("issue");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>Agent Inputs Issued Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Delivery Note No.</th>
			<th>Date</th>
			<th>Agent Name</th>
			<th>Input</th>
			<th>Quantity</th>
			<th>Total Value</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($issues[0])) {
$counter = 1;
foreach ($issues as $issue) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $issue -> Delivery_Note_Number;?>
		</td>
		<td>
		<?php echo $issue -> Date;?>
		</td>
		<td>
		<?php echo $issue -> Agent_Object->First_Name." ".$issue -> Agent_Object->Surname;?>
		</td>
		<td>
		<?php echo $issue -> Farm_Input_Object->Product_Name;?>
		</td>
		<td>
		<?php echo $issue -> Quantity;?>
		</td>
		<td>
		<?php echo $issue -> Total_Value;?>
		</td>
		<td><a href="<?php echo base_url()."agent_input_issue_management/edit_issue/".$issue->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" issue = "<?php echo $issue -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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