<script type="text/javascript">
	$(function() {

	});

</script>
<style>
	.create {
		background-color: green !important;
	}
	.edit {
		background-color: yellow !important;
	}
	.delete {
		background-color: red !important;
	}
	.report {
		background-color: blue !important;
	}
</style>
<div style="width:20px; height:20px; background-color: green;float:left"></div> <div style="float:left; padding:5px;"><a class="link" href="<?php echo base_url()."log_management/listing/1";?>">Creations</a></div>
<div style="width:20px; height:20px; background-color: yellow;float:left"></div> <div style="float:left; padding:5px;"><a class="link" href="<?php echo base_url()."log_management/listing/2";?>">Edits</a></div>
<div style="width:20px; height:20px; background-color: red;float:left"></div> <div style="float:left; padding:5px;"><a class="link" href="<?php echo base_url()."log_management/listing/3";?>">Deletions</a></div>
<div style="width:20px; height:20px; background-color: blue;float:left"></div> <div style="float:left; padding:5px;"><a class="link" href="<?php echo base_url()."log_management/listing/4";?>">Report Downloads</a></div>
<h1>Security Log Listing</h1>

<table class="fullwidth">
	<thead>
		<tr>
			<th>Badge</th>
			<th>User</th>
			<th>Details</th>
			<th>Timestamp</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($logs[0])) {
$counter = 1;
foreach ($logs as $log) {
$type = "";
if($log->Log_Type == "1"){
$type = "create";
}
if($log->Log_Type == "2"){
$type = "edit";
}
if($log->Log_Type == "3"){
$type = "delete";
}
if($log->Log_Type == "4"){
$type = "report";
}
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td class="<?php echo $type;?>"></td>
		<td>
		<?php echo $log -> Creator -> Name;?>
		</td>
		<td>
		<?php echo substr($log -> Log_Message, 0, 40) . " ...";?>
		</td>
		<td>
		<?php echo date("d/m/Y h:i:s", $log -> Timestamp);?>
		</td>
		<td><a href="<?php echo base_url()."log_management/view_log_details/".$log->id?>" class="button"><span class="ui-icon ui-icon-clipboard"></span>Details</a></td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div><?php endif;?>