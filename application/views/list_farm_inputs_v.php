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
	url = "<?php echo base_url().'farm_input_management/delete_input/'?>
		" +$(this).attr("farm_input");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<h1>Farm Inputs Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Product Code</th>
			<th>Product Name</th>
			<th>Product Description</th>
			<th>Unit Price</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($inputs[0])) {
$counter = 1;
foreach ($inputs as $input) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $input -> Product_Code;?>
		</td>
		<td>
		<?php echo $input -> Product_Name;?>
		</td>
		<td>
		<?php echo $input -> Product_Description;?>
		</td>
		<td>
		<?php echo $input -> Unit_Price;?></td>
		<td><a href="<?php echo base_url()."farm_input_management/edit_input/".$input->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" farm_input = "<?php echo $input -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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