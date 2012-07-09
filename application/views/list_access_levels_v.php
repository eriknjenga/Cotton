<h1>Access Level Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Level Name</th>
			<th>Description</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($levels[0])) {
$counter = 1;
foreach ($levels as $level) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $level -> Level_Name;?>
		</td>
		<td>
		<?php echo $level -> Description;?>
		</td>
		<td><a href="<?php echo base_url()."report_management/manage_reports/".$level->id?>" class="button"><span class="ui-icon ui-icon-note"></span>Manage Reports</a></td>
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