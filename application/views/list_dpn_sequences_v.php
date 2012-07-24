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
	url = "<?php echo base_url().'dpn_sequence_management/delete_sequence/'?>
		" +$(this).attr("sequence");
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
			<th>First Number</th>
			<th>Last Number</th>
			<th>Season</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($sequences[0])) {
$counter = 1;
foreach ($sequences as $sequence) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $sequence -> Depot_Object -> Depot_Code . " - " . $sequence -> Depot_Object -> Depot_Name;?>
		</td>
		<td>
		<?php echo $sequence -> First;?>
		</td>
		<td>
		<?php echo $sequence -> Last;?>
		</td>
		<td>
		<?php echo $sequence -> Season;?>
		</td>
		<td><a href="<?php echo base_url()."dpn_sequence_management/edit_sequence/".$sequence->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" sequence = "<?php echo $sequence -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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