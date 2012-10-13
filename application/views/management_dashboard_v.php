<script type="text/javascript">
$(function() {
	$("#larger_daily_purchases_graph_container").dialog( {
			height: 520,
			width: 980,
			modal: true,
			autoOpen: false
	});
	$("#date_from").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#date_to").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "daily_trend", "970", "600", "0", "0");
	var url = '<?php echo base_url()."activity_graph/getDailyTrend/purchases-dispatches-seeds-sg-rg-"?>'; 
	chart.setDataURL(url);
	chart.render("activity_graph");
	$("#filter_graph").click(function(){
				var graphs_string = "";
				$(".graphs").each(function(index,item) {
					if($(this).is(':checked')){
						var graph_value = $(this).attr("value");
				  		graphs_string += graph_value+"-";
					}		 
				});
				console.log(graphs_string);
				var date_from = $("#date_from").attr("value");
				var date_to = $("#date_to").attr("value"); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "900", "600", "0", "0");	
				var url = '<?php echo base_url()?>activity_graph/getDailyTrend/'+graphs_string+'/'+date_from+'/'+date_to+''; 
				chart.setDataURL(url);
				chart.render("activity_graph");
	});
});
</script>
<style>
	.top_graphs_container{
		width:980px;
		margin: 0 auto;
		overflow: hidden;
	}
	.graph{
		width:970px;  
	}
	.graph_title{
		letter-spacing: 1px;
		font-size: 10px;
		font-weight: bold;
		margin: 0 auto;
		width:300px;
	}
	#notifications_panel{
		width:100%;
	}
	.message{
		width:300px;
	}
	.notification_link{
		text-decoration: none;
		float:left;
		margin: 5px;
	}
	h2{
		margin-left:100px; 
	}
</style>
<div id="notifications_panel">
	<a href="<?php echo site_url('dormant_buying_centers');?>" class="notification_link">
	<div class="message information close">
	<h2>Dormant Centers</h2>
	<p>
		A total of <?php echo $total_dormant;?> have been dormant for more than 2 days
	</p>
</div>
</a>
<a href="<?php echo site_url('missing_dpn');?>" class="notification_link">
	<div class="message information close">
	<h2>Missing DPNs</h2>
	<p>
		A total of <?php echo $centers_missing;?> Buying Centers are missing DPNs
	</p>
</div>
	</a>
</div>
<div class="top_graphs_container">
<div class="graph">
<h2>Daily Activity</h2> 
<div id="daily_rg_filter">
	<b>From: </b><input type="text" id="date_from" /> <b>To: </b><input type="text" id="date_to" />
	<fieldset style="width:400px; display: inline;"><legend>Graphs to Display</legend><input type="checkbox" class="graphs" checked value="purchases"/>Purchases<input type="checkbox" class="graphs" checked value="dispatches"/>Dispatches<input type="checkbox" class="graphs" checked value="seeds"/>Seeds<input type="checkbox" class="graphs" checked value="sg"/>Sow Gin<input type="checkbox" class="graphs" checked value="rg"/>Roller Gin</fieldset>
	
	<input type="button" id="filter_graph" value="Filter Graph" class="button"/>
</div>
<div id = "activity_graph" title="Daily Activity Graph" ></div>
</div>

</div>