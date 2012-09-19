<script type="text/javascript">
$(function() {
	/*var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column2D.swf"?>", "zonal_purchases", "600", "350", "0", "0");
	var url = '<?php echo base_url()."region_management/getRegionalPurchases/"?>'; 
	chart.setDataURL(url);
	chart.render("zonal_purchases");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "daily_trend", "600", "350", "0", "0");
	var url = '<?php echo base_url()."purchase_management/getDailyTrend/"?>'; 
	chart.setDataURL(url);
	chart.render("daily_purchase_trend");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column2D.swf"?>", "outstanding_debt", "600", "350", "0", "0");
	var url = '<?php echo base_url()."disbursement_management/getOutstandingDebt/"?>'; 
	chart.setDataURL(url);
	chart.render("outstanding_debt");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column2D.swf"?>", "input_disbursements", "600", "350", "0", "0");
	var url = '<?php echo base_url()."disbursement_management/getTotalInputDisbursements/"?>'; 
	chart.setDataURL(url);
	chart.render("input_disbursements");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "price_movement", "600", "350", "0", "0");
	var url = '<?php echo base_url()."price_management/getPriceTrend/"?>'; 
	chart.setDataURL(url);
	chart.render("price_movement");
	*/
});
</script>
<style>
	.graph_container{
		width: 600px; 
		float: left;
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
<div class="graph_container">
	<div id="zonal_purchases"></div>
</div>

<div class="graph_container">
	<div id="daily_purchase_trend"></div>
</div>

<div class="graph_container">
	<div id="outstanding_debt"></div>
</div>

<div class="graph_container">
	<div id="input_disbursements"></div>
</div>

<div class="graph_container">
	<div id="price_movement"></div>
</div>