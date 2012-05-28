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
	url = "<?php echo base_url().'price_management/delete_price/'?>
		" +$(this).attr("price");
		$("#confirm_delete").dialog('open');
		});
		});
		function delete_record(){
		window.location = url;
		}
</script>
<p>
<a href="<?php echo base_url().'price_management/new_price'?>" class="button"><span class="ui-icon ui-icon-cart"></span>New Price</a>
</p>
<h1>Cotton Price Listing</h1>
<table class="fullwidth">
	<thead>
		<tr>
			<th>Price Effective From</th>
			<th>Season</th>
			<th>Price</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
if (isset($prices[0])) {
$counter = 1;
foreach ($prices as $price) {
$rem = $counter % 2;
if ($rem == 0) {
$class = "even";
} else {
$class = "odd";
}
		?><tr class="<?php echo $class;?>
		">
		<td>
		<?php echo $price -> Date;?>
		</td>
		<td>
		<?php echo $price -> Season;?>
		</td>
		<td>
		<?php echo $price -> Price;?>
		</td>
		<td><a href="#" class="button delete" price = "<?php echo $price -> id;?>"><span class="ui-icon ui-icon-trash"></span>Delete</a></td>
		</tr>
		<?php

		$counter++;
		}
		}
		?>
	</tbody>
</table>
<div title="Confirm Delete!" id="confirm_delete" style="width: 300px; height: 150px; margin: 5px auto 5px auto;">
	Are you sure you want to delete this record? <br/><br/>Note: <b>All purchases recorded using this price will remain unchanged!</b>
</div>
<?php if (isset($pagination)):
?>
<div style="width:450px; margin:0 auto 60px auto">
	<?php echo $pagination;?>
</div><?php endif;?>