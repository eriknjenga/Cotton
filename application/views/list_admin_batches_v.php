<script type="text/javascript">
		var url = ""; 
	$(function() {
	$("#confirm_change").dialog( {
	height: 150,
	width: 400,
	modal: true,
	autoOpen: false,
	buttons: {
	"Change Ownership": function() {
	var new_user = $("#data_clerks").attr("value");	 
	change_ownership(new_user);
	},
	Cancel: function() {
	$( this ).dialog( "close" );
	}
	}
	} );

	$(".change").click(function(){
	url = "<?php echo base_url().'batch_management/change_ownership/'?>
		" +$(this).attr("batch");
		$("#confirm_change").dialog('open');
		});
		});
		function change_ownership(new_user){
		window.location = url+"/"+new_user;
		}
</script>
<h1>Batch Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>ID</th>
			<th>Owner</th>
			<th>Transaction Type</th>
			<th>Timestamp</th>
			<th>Entries</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($batches[0])) {
$counter = 1;
foreach ($batches as $batch) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
$statuses = array("Open","Closed","Posted"); 
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $batch -> id;?>
		</td>
				<td>
		<?php echo $batch -> User_Object -> Name;?>
		</td>
		<td>
		<?php echo $batch -> Transaction_Type_Object->Name." (".$statuses[$batch->Status].")";?>
		</td>
		<td>
		<?php echo date("d/m/y H:i:s", $batch -> Timestamp);?>
		</td>
		<td>
		<?php echo sizeof($batch -> Purchases) + sizeof($batch -> Disbursements);?>
		</td>
		<td><a href="<?php echo base_url()."batch_management/print_batch/".$batch->id?>" class="button"><span class="ui-icon ui-icon-print"></span>Print</a><?php if($batch->Status == 2){echo "Already Posted! ";}?><?php if($batch->Status != 2){?><a href="#" class="button change" batch = "<?php echo $batch -> id;?>"><span class="ui-icon ui-icon-transferthick-e-w"></span>Change Owner</a><?php }?></td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>
<div title="Change Batch Owner!" id="confirm_change" style="width: 300px; height: 150px; margin: 5px auto 5px auto;">
	Are you sure you want to change the owner of this batch?
	<p>
		<label for="data_clerks"><b>Select New Owner: </b></label>
		<select id="data_clerks">
			<?php 
			foreach($clerks as $clerk){?>
				<option value="<?php echo $clerk->id;?>"><?php echo $clerk->Name;?></option>
			<?php }
			?>
		</select>
	</p>
</div>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div><?php endif;?>