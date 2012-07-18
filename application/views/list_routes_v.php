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
	url = "<?php echo base_url().'route_management/delete_route/'?>
		" +$(this).attr("route");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<?php 
$this->load->view("geography_submenu");
?>
<h1>Purchase Route Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Route Code</th>
			<th>Route Name</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($routes[0])) {
$counter = 1;
foreach ($routes as $route) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $route -> Route_Code;?>
		</td>
		<td>
		<?php echo $route -> Route_Name;?>
		</td>
		<td><a href="<?php echo base_url()."route_management/edit_route/".$route->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" route = "<?php echo $route -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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