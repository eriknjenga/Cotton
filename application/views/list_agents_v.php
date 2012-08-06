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
	url = "<?php echo base_url().'agent_management/delete_agent/'?>
		" +$(this).attr("agent");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<?php 
$this->load->view("people_submenu");
?>
<h1>Agent Listing</h1><a href="<?php echo base_url()."agent_management/print_agents";?>" class="button"><span class="ui-icon ui-icon-print"></span>Print</a>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Code</th>
			<th>Name</th> 
			<th>National Id</th>  
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($agents[0])) {
$counter = 1;
foreach ($agents as $agent) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $agent -> Agent_Code;?>
		</td>
		<td>
		<?php echo $agent -> First_Name." ".$agent -> Surname;?>
		</td> 
		<td>
		<?php echo $agent ->National_Id;?>
		</td> 
		<td><a href="<?php echo base_url()."agent_management/edit_agent/".$agent->id?>" class="button"><span class="ui-icon ui-icon-pencil"></span>Edit</a><a href="#" class="button delete" agent = "<?php echo $agent -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
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