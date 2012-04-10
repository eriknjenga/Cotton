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
	url = "<?php echo base_url().'field_cashier_management/delete_field_cashier/'?>
		" +$(this).attr("field_cashier");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>Field Cashier Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Field Cashier Code</th>
			<th>Full Name</th>
			<th>Phone Number</th>
			<th>National ID</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($field_cashiers[0])) {
$counter = 1;
foreach ($field_cashiers as $field_cashier) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $field_cashier -> Field_Cashier_Code;?>
		</td>
		<td>
		<?php echo $field_cashier -> Field_Cashier_Name;?>
		</td>
				<td>
		<?php echo $field_cashier -> Phone_Number;?>
		</td>
				<td>
		<?php echo $field_cashier -> National_Id;?>
		</td>
		<td><a href="<?php echo base_url()."field_cashier_management/edit_field_cashier/".$field_cashier->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" field_cashier = "<?php echo $field_cashier -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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