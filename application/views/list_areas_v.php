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
	url = "<?php echo base_url().'area_management/delete_area/'?>
		" +$(this).attr("area");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>Area Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Area Code</th>
			<th>Area Name</th> 
			<th>Region</th> 
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($areas[0])) {
$counter = 1;
foreach ($areas as $area) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $area -> Area_Code;?>
		</td>
		<td>
		<?php echo $area -> Area_Name;?>
		</td> 
				<td>
		<?php echo $area -> Region_Object->Region_Name;?>
		</td> 
		<td><a href="<?php echo base_url()."area_management/edit_area/".$area->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" area = "<?php echo $area -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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