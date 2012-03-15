<script type="text/javascript">
	$(document).ready(function() {
		$("#region_filter_graph_container").dialog({
			autoOpen : false,
			width : 800,
			height : 450,
		});
		$("#weighted_average_graph_container").dialog({
			autoOpen : false,
			width : 800,
			height : 450,
		});
		$("#average_price_graph_container").dialog({
			autoOpen : false,
			width : 800,
			height : 450,
		});
		
		$("#purchases_to_date_graph_container").dialog({
			autoOpen : false,
			width : 800,
			height : 450,
		});
		$("#loan_movement_graph_container").dialog({
			autoOpen : false,
			width : 800,
			height : 450,
		});
		$("#area_production_graph_container").dialog({
			autoOpen : false,
			width : 800,
			height : 450,
		});
		$("#region_filter_graph").click(function() {
			$("#region_filter_graph_container").dialog('open'); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSColumn3DLineDY.swf"?>", "ChartId", "750", "350", "0", "0");
			   var url = '<?php echo base_url()."region_management/get_region_graph_data"?>';
			   chart.setDataURL(url);		   
			   chart.render("region_filter_graph_content"); 
			return false;
		});
		$("#weighted_average_graph").click(function() {
			$("#weighted_average_graph_container").dialog('open'); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "750", "350", "0", "0");
			   var url = '<?php echo base_url()."tonnage_management/get_weighted_average_graph_data"?>';
			   chart.setDataURL(url);		   
			   chart.render("weighted_average_graph_content"); 
			return false;
		});
		$("#average_price_graph").click(function() {
			$("#average_price_graph_container").dialog('open'); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "750", "350", "0", "0");
			   var url = '<?php echo base_url()."purchase_management/get_average_price_graph_data"?>';
			   chart.setDataURL(url);		   
			   chart.render("average_price_graph_content"); 
			return false;
		});
		$("#purchases_to_date_graph").click(function() {
			$("#purchases_to_date_graph_container").dialog('open'); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Pie3D.swf"?>", "ChartId", "750", "350", "0", "0");
			   var url = '<?php echo base_url()."purchase_management/get_purchases_to_date_graph_data"?>';
			   chart.setDataURL(url);		   
			   chart.render("purchases_to_date_graph_content"); 
			return false;
		});
		$("#loan_movement_graph").click(function() {
			$("#loan_movement_graph_container").dialog('open'); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Column3D.swf"?>", "ChartId", "750", "350", "0", "0");
			   var url = '<?php echo base_url()."farm_input_management/get_loan_movement_graph_data"?>';
			   chart.setDataURL(url);		   
			   chart.render("loan_movement_graph_content"); 
			return false;
		});
		$("#area_production_graph").click(function() {
			$("#area_production_graph_container").dialog('open'); 
				var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/Line.swf"?>", "ChartId", "750", "350", "0", "0");
			   var url = '<?php echo base_url()."purchase_management/get_area_production_graph_data"?>';
			   chart.setDataURL(url);		   
			   chart.render("area_production_graph_content"); 
			return false;
		});
	});

</script>
<h1>Welcome, <span><?php echo $this -> session -> userdata('full_name');?></span>!</h1>
<p>
	What would you like to do today?
</p>
<div class="pad20">
	<!-- Big buttons -->
	<ul class="dash">
		<?php
$dashboards = $this -> session -> userdata('dashboard_items');
foreach($dashboards as $dashboard){
		?>
		<li>
			<a href ="<?php echo base_url() . $dashboard['url'];?>" title="<?php echo $dashboard['tooltip']?>" id="<?php echo $dashboard['dashboard_id']?>" class="tooltip" ><img src="<?php echo base_url() . 'Images/icons/' . $dashboard['icon'];?>" alt="" /><span><?php echo $dashboard['text']
				?></span></a>
		</li>
		<?php }?>
	</ul>
	<!-- End of Big buttons -->
</div>
<div id="region_filter_graph_container">
	<p>
		<label for="region">Filter by Region </label>
		<select name="region" class="dropdown">
			<option>Mumbwa Region </option>
			<option>Southern Region </option>
		</select> 
												<label>Show Comparison by:</label>
												<input type="radio">Months 
												<input type="radio">Years 
											</p>
	<div id="region_filter_graph_content"></div>
</div>
<div id="weighted_average_graph_container">
	<p>
		 
		 
												<label>Show Comparison by:</label>
												<input type="radio">Days
												<input type="radio">Weeks  
												<input type="radio">Months 
												<input type="radio">Years 
											</p>
	<div id="weighted_average_graph_content"></div>
</div>

<div id="average_price_graph_container">
	<p>
		 
		 
												<label>Show Comparison by:</label>
												<input type="radio">Days
												<input type="radio">Weeks  
												<input type="radio">Months 
												<input type="radio">Years 
											</p>
	<div id="average_price_graph_content"></div>
</div>
<div id="purchases_to_date_graph_container"> 
	<div id="purchases_to_date_graph_content"></div>
</div>
<div id="loan_movement_graph_container">
	<p>
		 
		 
												<label>Show Comparison by:</label>
												 
												<input type="radio">Weeks  
												<input type="radio">Months 
												<input type="radio">Years 
											</p>
	<div id="loan_movement_graph_content"></div>
</div>
<div id="area_production_graph_container">
	<p>
		<label for="region">Filter by Area </label>
		<select name="region" class="dropdown">
			<option>Chombwa </option>
			<option>Nangoma South </option>
			<option>Itezhi Tezhi </option>
			<option>Mvumbe </option>
			<option>Kafue </option>
		</select> 
												<label>Show Comparison by:</label>
												<input type="radio">Months 
												<input type="radio">Years 
											</p>
	<div id="area_production_graph_content"></div>
</div>
<hr />
