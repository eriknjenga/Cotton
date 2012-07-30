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
	url = "<?php echo base_url().'district_management/delete_district/'?>
		" +$(this).attr("district");
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
<h1>District Listing</h1><a href="<?php echo base_url()."district_management/print_districts";?>" class="button"><span class="ui-icon ui-icon-print"></span>Print</a>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Name</th>
			<th>Zone</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($districts[0])) {
$counter = 1;
foreach ($districts as $district) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $district -> Name;?>
		</td>
		<td>
		<?php echo $district -> Region_Object->Region_Name;?>
		</td>
		<td><a href="<?php echo base_url()."district_management/edit_district/".$district->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" district = "<?php echo $district -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
		</tr>
		<?php $counter++;
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