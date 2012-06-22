<script type="text/javascript">
$(function() {
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column3D.swf"?>", "zonal_purchases", "600", "350", "0", "0");
	var url = '<?php echo base_url()."region_management/getRegionalPurchases/"?>'; 
	chart.setDataURL(url);
	chart.render("zonal_purchases");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "daily_trend", "600", "350", "0", "0");
	var url = '<?php echo base_url()."purchase_management/getDailyTrend/"?>'; 
	chart.setDataURL(url);
	chart.render("daily_purchase_trend");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column3D.swf"?>", "outstanding_debt", "600", "350", "0", "0");
	var url = '<?php echo base_url()."disbursement_management/getOutstandingDebt/"?>'; 
	chart.setDataURL(url);
	chart.render("outstanding_debt");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column3D.swf"?>", "input_disbursements", "600", "350", "0", "0");
	var url = '<?php echo base_url()."disbursement_management/getTotalInputDisbursements/"?>'; 
	chart.setDataURL(url);
	chart.render("input_disbursements");
	
	var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "price_movement", "600", "350", "0", "0");
	var url = '<?php echo base_url()."price_management/getPriceTrend/"?>'; 
	chart.setDataURL(url);
	chart.render("price_movement");
});
</script>
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