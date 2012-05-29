<style>
	.create {
		border-color: green !important;
		border-width: 2px !important;
	}
	.edit {
		border-color: yellow !important;
		border-width: 2px !important;
	}
	.delete {
		border-color: red !important;
		border-width: 2px !important;
	}
	.report {
		border-color: blue !important;
		border-width: 2px !important;
	}

</style>
<?php 
if ($log -> Log_Type == "1") {
	$type = "create";
}
if ($log -> Log_Type == "2") {
	$type = "edit";
}
if ($log -> Log_Type == "3") {
	$type = "delete";
}
if ($log -> Log_Type == "4") {
	$type = "report";
}
?>
<div class="cols3 column">
	<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="">
		<div class="portlet-header ui-widget-header ui-corner-all">
			<span class="ui-icon ui-icon-circle-arrow-s"></span>Security Log Details
		</div>
		<div class="portlet-content" >
			<table class="fullwidth <?php echo $type;?>">
				<tr>
					<td><b>User</b></td><td><?php echo $log -> Creator -> Name;?></td>
				</tr>
				<tr>
					<td><b>Action</b></td><td><?php echo $log -> Log_Message;?></td>
				</tr>
				<tr>
					<td><b>Timestamp</b></td><td><?php echo date("m/d/Y H:i:s", $log -> Timestamp);?></td>
				</tr>
			</table>
		</div>
	</div>
</div>