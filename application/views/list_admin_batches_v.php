<?php 
if(!isset($batch_listing)){
	$batch_listing = "";
}
?>
<style>
	.link-active{
		color: #ff6b08;
	}
</style>
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
<div id="top_container">
	<div style="float: left; width:500px;padding:5px;">
		<?php
		$attributes = array("method" => "post");
		echo form_open('batch_management/search', $attributes);
		echo validation_errors('
		<p class="form_error">
			', '
		</p>
		');
		?> <label for="batch_id">Batch Id: </label>
		<input id="batch_id"  name="batch_id" type="text"/>
		<input class="button" type="submit" value="Go to Batch">
	</form>
	</div>
</div>
<h1>Batch Listing</h1>Type: <a class="link <?php if($batch_listing == "0"){echo "link-active";}?>" href="<?php echo base_url()."batch_management/listing/0"?>">Open</a> | <a class="link <?php if($batch_listing =="1"){echo "link-active";}?>" href="<?php echo base_url()."batch_management/listing/1"?>">Closed</a> | <a class="link <?php if($batch_listing == "2"){echo "link-active";}?>" href="<?php echo base_url()."batch_management/listing/2"?>">Posted</a>
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
		<?php
		//Check type of batch and get total number of transactions
		if ($batch -> Transaction_Type_Object -> Indicator == "purchases") {
			echo sizeof($batch -> Purchases);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "input_disbursements") {
			echo sizeof($batch -> Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "agent_input_disbursements") {
			echo sizeof($batch -> Agent_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "buying_center_receipts") {
			echo sizeof($batch -> Buying_Center_Receipts);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "cash_receipts") {
			echo sizeof($batch -> Cash_Receipts);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "cihc") {
			echo sizeof($batch -> Cash_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "cihb") {
			echo sizeof($batch -> Field_Cash_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "input_transfers") {
			echo sizeof($batch -> Region_Disbursements);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "mopping_payments") {
			echo sizeof($batch -> Mopping_Payments);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "loan_recovery_receipts") {
			echo sizeof($batch -> Loan_Recovery_Cash_Receipts);
		} else if ($batch -> Transaction_Type_Object -> Indicator == "buying_center_summaries") {
			echo sizeof($batch -> Buying_Center_Summaries);
		}
		?>
		</td>
		<td><a href="<?php echo base_url()."batch_management/print_batch/excel/".$batch->id?>" class="button"><span class="ui-icon ui-icon-print"></span>Excel</a><a href="<?php echo base_url()."batch_management/print_batch/pdf/".$batch->id?>" class="button"><span class="ui-icon ui-icon-print"></span>PDF</a><?php if($batch->Status == 2){echo "Already Posted! ";}?><?php if($batch->Status != 2){?><a href="#" class="button change" batch = "<?php echo $batch -> id;?>"><span class="ui-icon ui-icon-transferthick-e-w"></span>Change Owner</a><?php }?></td>
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