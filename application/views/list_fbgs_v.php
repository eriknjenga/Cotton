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
	url = "<?php echo base_url().'fbg_management/delete_fbg/'?>
		" +$(this).attr("fbg");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>FBG Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>GD</th>
			<th>CPC</th>
			<th>Group Name</th>
			<th>Distributor</th>
			<th>Hectares</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($fbgs[0])) {
$counter = 1;
foreach ($fbgs as $fbg) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $fbg -> GD_Id;?>
		</td>
		<td>
		<?php echo $fbg -> CPC_Number;?>
		</td>
		<td>
		<?php echo $fbg -> Group_Name;?>
		</td>

		<td>
		<?php echo $fbg -> Distributor_Object -> First_Name . " " . $fbg -> Distributor_Object -> Surname;?>
		</td>
		<td>
		<?php echo $fbg -> Hectares_Available;?>
		</td>
		<td><a href="<?php echo base_url()."fbg_management/edit_fbg/".$fbg->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" fbg = "<?php echo $fbg -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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