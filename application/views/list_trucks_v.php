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
	url = "<?php echo base_url().'truck_management/delete_truck/'?>
		" +$(this).attr("truck");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>Truck Listing</h1><a href="<?php echo base_url() . "truck_management/print_trucks";?>" class="button"><span class="ui-icon ui-icon-print"></span>Print</a>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Number Plate</th>
			<th>Category</th>
			<th>Capacity</th>
			<th>Agreed Rate (/ton/km)</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($trucks[0])) {
$categories = array(1=>"Alliance Truck",2=>"Contracted Truck");
$counter = 1;
foreach ($trucks as $truck) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $truck -> Number_Plate;?>
		</td>
		<td>
		<?php echo $categories[$truck -> Category];?>
		</td>
		<td>
		<?php echo $truck -> Capacity;?>
		</td>
		<td>
		<?php echo $truck -> Agreed_Rate;?>
		</td>
		<td><a href="<?php echo base_url()."truck_management/edit_truck/".$truck->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" truck = "<?php echo $truck -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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