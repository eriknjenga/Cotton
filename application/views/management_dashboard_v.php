<script type="text/javascript">
$(function() {
	$("#larger_daily_purchases_graph_container").dialog( {
			height: 520,
			width: 980,
			modal: true,
			autoOpen: false
	});
	$("#daily_purchases_from").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#daily_purchases_to").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#larger_daily_dispatches_graph_container").dialog( {
			height: 520,
			width: 980,
			modal: true,
			autoOpen: false
	});
	$("#daily_dispatches_from").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#daily_dispatches_to").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#larger_daily_sg_graph_container").dialog( {
			height: 520,
			width: 980,
			modal: true,
			autoOpen: false
	});
	$("#daily_sg_from").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#daily_sg_to").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#larger_daily_rg_graph_container").dialog( {
			height: 520,
			width: 980,
			modal: true,
			autoOpen: false
	});
	$("#daily_rg_from").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});
	$("#daily_rg_to").datepicker({
			defaultDate : new Date(),
			changeYear : true,
			changeMonth : true,
			dateFormat: 'dd-mm-yy'
	});	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "daily_trend", "460", "300", "0", "0");
	var url = '<?php echo base_url()."purchase_management/getDailyTrend/"?>'; 
	chart.setDataURL(url);
	chart.render("daily_purchases");
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "daily_trend", "460", "300", "0", "0");
	var url = '<?php echo base_url()."weighbridge_management/getDailyTrend/"?>'; 
	chart.setDataURL(url);
	chart.render("daily_dispatches");
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "daily_trend", "460", "300", "0", "0");
	var url = '<?php echo base_url()."production_management/getDailyTrend/1/"?>'; 
	chart.setDataURL(url);
	chart.render("daily_sow");
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "daily_trend", "460", "300", "0", "0");
	var url = '<?php echo base_url()."production_management/getDailyTrend/2/"?>'; 
	chart.setDataURL(url);
	chart.render("daily_roller");
	$(".view_larger_graph").click(function(){
			var id  = $(this).attr("id"); 
			if(id == "daily_purchases_graph"){
				$("#larger_daily_purchases_graph_container").dialog('option', 'title', 'Daily Purchases Trend');
				$("#larger_daily_purchases_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");
				var url = '<?php echo base_url()."purchase_management/getDailyTrend/"?>'; 
				chart.setDataURL(url);
				chart.render("daily_purchases_larger_graph");
			}
			if(id == "daily_dispatches_graph"){
				$("#larger_daily_dispatches_graph_container").dialog('option', 'title', 'Daily Dispatches Trend');
				$("#larger_daily_dispatches_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");
				var url = '<?php echo base_url()."weighbridge_management/getDailyTrend/"?>'; 
				chart.setDataURL(url);
				chart.render("daily_dispatches_larger_graph");
			}
			if(id == "daily_sow_graph"){
				$("#larger_daily_sg_graph_container").dialog('option', 'title', 'Daily Sow Gin Production Trend');
				$("#larger_daily_sg_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");
				var url = '<?php echo base_url()."production_management/getDailyTrend/1/"?>'; 
				chart.setDataURL(url);
				chart.render("daily_sg_larger_graph");
			}
			if(id == "daily_roller_graph"){
				$("#larger_daily_rg_graph_container").dialog('option', 'title', 'Daily Roller Gin Production Trend');
				$("#larger_daily_rg_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");
				var url = '<?php echo base_url()."production_management/getDailyTrend/2"?>'; 
				chart.setDataURL(url);
				chart.render("daily_rg_larger_graph");
			}
	});
	$("#refresh_daily_purchases_graph").click(function(){
				var date_from = $("#daily_purchases_from").attr("value");
				var date_to = $("#daily_purchases_to").attr("value"); 
				$("#larger_daily_purchases_graph_container").dialog('option', 'title', 'Daily Purchases Trend');
				$("#larger_daily_purchases_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");	
				var url = '<?php echo base_url()?>purchase_management/getDailyTrend/'+date_from+'/'+date_to+''; 
				chart.setDataURL(url);
				chart.render("daily_purchases_larger_graph");
	});
	$("#refresh_daily_dispatches_graph").click(function(){
				var date_from = $("#daily_dispatches_from").attr("value");
				var date_to = $("#daily_dispatches_to").attr("value"); 
				$("#larger_daily_dispatches_graph_container").dialog('option', 'title', 'Daily Dispatches Trend');
				$("#larger_daily_dispatches_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");	
				var url = '<?php echo base_url()?>weighbridge_management/getDailyTrend/'+date_from+'/'+date_to+''; 
				chart.setDataURL(url);
				chart.render("daily_dispatches_larger_graph");
	});
	$("#refresh_daily_sg_graph").click(function(){
				var date_from = $("#daily_sg_from").attr("value");
				var date_to = $("#daily_sg_to").attr("value"); 
				$("#larger_daily_sg_graph_container").dialog('option', 'title', 'Daily Sow Gin Production Trend');
				$("#larger_daily_sg_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");
				var url = '<?php echo base_url()?>production_management/getDailyTrend/1/'+date_from+'/'+date_to+''; 
				chart.setDataURL(url);
				chart.render("daily_sg_larger_graph");
	});
	$("#refresh_daily_rg_graph").click(function(){
				var date_from = $("#daily_rg_from").attr("value");
				var date_to = $("#daily_rg_to").attr("value"); 
				$("#larger_daily_rg_graph_container").dialog('option', 'title', 'Daily Roller Gin Production Trend');
				$("#larger_daily_rg_graph_container").dialog("open");
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "900", "450", "0", "0");
				var url = '<?php echo base_url()?>production_management/getDailyTrend/2/'+date_from+'/'+date_to+''; 
				chart.setDataURL(url);
				chart.render("daily_rg_larger_graph");
	});
});
</script>
<style>
	.top_graphs_container{
		width:980px;
		margin: 0 auto;
		overflow: hidden;
	}
	.larger_graph{
		float: right;
	}
	.larger_graph_container{
		width:950px;
		height: 500px;
	}
	.graph{
		width:460px;
		float:left; 
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
	h3{
		margin-left:100px;
		text-decoration: underline;
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
<h3>Daily Purchases Trend</h3>
<div class="larger_graph">
	
	<a class="link view_larger_graph" id="daily_purchases_graph">Enlarge to Filter</a>
</div>
<div id = "daily_purchases" title="Daily Purchases Graph" ></div>
</div>
<div class="graph">
<h3>Daily Dispatches Trend</h3>
<div class="larger_graph">
	<a class="link view_larger_graph" id="daily_dispatches_graph">Enlarge to Filter</a>
</div>
<div id = "daily_dispatches" title="Daily Dispatches Graph" ></div>
</div>  
<div class="graph">
<h3>Daily Sow Gin Production Trend</h3>
<div class="larger_graph">
	<a class="link view_larger_graph" id="daily_sow_graph">Enlarge to Filter</a>
</div>
<div id = "daily_sow" title="Daily SG Production Graph" ></div>
</div> 
<div class="graph">
<h3>Daily Roller Gin Production Trend</h3>
<div class="larger_graph">
	<a class="link view_larger_graph" id="daily_roller_graph">Enlarge to Filter</a>
</div>
<div id = "daily_roller" title="Daily RG Production Graph" ></div>
</div> 
</div> 
<div id="larger_daily_purchases_graph_container">
<div id="daily_purchases_filter">
	<b>From: </b><input type="text" id="daily_purchases_from" /> <b>To: </b><input type="text" id="daily_purchases_to" />
	<input type="button" id="refresh_daily_purchases_graph" value="Filter Graph" class="button"/>
</div>
<div id="daily_purchases_larger_graph"></div>
</div>
<div id="larger_daily_dispatches_graph_container">
<div id="daily_dispatches_filter">
	<b>From: </b><input type="text" id="daily_dispatches_from" /> <b>To: </b><input type="text" id="daily_dispatches_to" />
	<input type="button" id="refresh_daily_dispatches_graph" value="Filter Graph" class="button"/>
</div>
<div id="daily_dispatches_larger_graph"></div>
</div>

<div id="larger_daily_sg_graph_container">
<div id="daily_sg_filter">
	<b>From: </b><input type="text" id="daily_sg_from" /> <b>To: </b><input type="text" id="daily_sg_to" />
	<input type="button" id="refresh_daily_sg_graph" value="Filter Graph" class="button"/>
</div>
<div id="daily_sg_larger_graph"></div>
</div>
<div id="larger_daily_rg_graph_container">
<div id="daily_rg_filter">
	<b>From: </b><input type="text" id="daily_rg_from" /> <b>To: </b><input type="text" id="daily_rg_to" />
	<input type="button" id="refresh_daily_rg_graph" value="Filter Graph" class="button"/>
</div>
<div id="daily_rg_larger_graph"></div>
</div>