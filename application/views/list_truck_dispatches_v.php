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
	url = "<?php echo base_url().'truck_dispatch_management/delete_dispatch/'?>
		" +$(this).attr("dispatch");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>DPN Sequence Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Buying Center</th>
			<th>Dispatch Date</th>
			<th>Truck Sent</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($dispatches[0])) {
$counter = 1;
foreach ($dispatches as $dispatch) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $dispatch -> Depot_Object -> Depot_Code . " - " . $dispatch -> Depot_Object -> Depot_Name;?>
		</td>
		<td>
		<?php echo $dispatch -> Date;?>
		</td>
		<td>
		<?php echo $dispatch -> Truck;?>
		</td>
		<td><a href="<?php echo base_url()."truck_dispatch_management/edit_dispatch/".$dispatch->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" dispatch = "<?php echo $dispatch -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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