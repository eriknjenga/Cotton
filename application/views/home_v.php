<?php
if ($this -> session -> userdata('user_indicator')=="management") {
	redirect("home_controller/management_dashboard");
}
?>
<h1>Welcome, <span><?php echo $this -> session -> userdata('full_name');?></span>!</h1>
<p>
	What would you like to do today?
</p>
<div class="pad20">
	<!-- Big buttons -->
	<ul class="dash">
		<?php
$dashboards = $dashboards['dashboard_items'];
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
