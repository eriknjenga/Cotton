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
	url = "<?php echo base_url().'depot_management/delete_depot/'?>
		" +$(this).attr("depot");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>Buying Centers Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Code</th>
			<th>Name</th>
			<th>Capacity</th>
			<th>Village</th>
			<th>Buyer</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($depots[0])) {
$counter = 1;
foreach ($depots as $depot) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $depot -> Depot_Code;?>
		</td>
		<td>
		<?php echo $depot -> Depot_Name;?>
		</td>
		<td>
		<?php echo $depot -> Capacity;?>
		</td>
		<td>
		<?php echo $depot -> Village_Object -> Name." (".$depot -> Village_Object->Ward_Object->Name.")";?>
		</td>
		<td>
		<?php echo $depot -> Buyer_Object -> Name;?>
		</td>
		<td>
		<?php
		if($this -> session -> userdata('user_indicator') == "system_administrator"){?>
		<a href="<?php echo base_url()."depot_management/edit_depot/".$depot->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" depot = "<?php echo $depot -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a>
		<?php }
			else{
		?>
		<a href="<?php echo base_url()."purchase_management/record_purchase/".$depot->id?>" class="button"><span class="ui-icon ui-icon-clipboard"></span>Record Purchases</a>
		<?php }?>
		</td>
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