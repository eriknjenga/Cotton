<?php
if (!$this -> session -> userdata('user_id')) {
	redirect("user_management/login");
}
if (!isset($quick_link)) {
	$quick_link = "";
}
if (!isset($link)) {
	$link = "";
}
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- Meta -->
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<!-- End of Meta -->
		<!-- Page title -->
		<title>Cotton Management System | Dashboard</title>
		<!-- End of Page title -->
		<!-- Libraries -->
		<link type="text/css" href="<?php echo base_url().'CSS/layout.css'?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url().'Scripts/jquery.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'Scripts/jquery-ui.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'Scripts/easyTooltip.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'Scripts/jquery.wysiwyg.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'Scripts/hoverIntent.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'Scripts/superfish.js'?>"></script> 
		<?php
		if (isset($script_urls)) {
			foreach ($script_urls as $script_url) {
				echo "<script src=\"" . $script_url . "\" type=\"text/javascript\"></script>";
			}
		}
	?>

<?php
if (isset($scripts)) {
	foreach ($scripts as $script) {
		echo "<script src=\"" . base_url() . "Scripts/" . $script . "\" type=\"text/javascript\"></script>";
	}
}
?>


 
<?php
if (isset($styles)) {
	foreach ($styles as $style) {
		echo "<link href=\"" . base_url() . "CSS/" . $style . "\" type=\"text/css\" rel=\"stylesheet\"/>";
	}
}
?>  
		<!-- End of Libraries -->
	</head>
	<body>
		<!-- Container -->
		<div id="container">
			<!-- Header -->
			<div id="header">
				<!-- Top -->
				<div id="top">
					<!-- Logo -->
					<div class="logo">
						<a href="#" title="Administration Home" class="tooltip"><img src="<?php echo base_url().'Images/logo.png'?>" alt="logo" /></a>
					</div>
					<!-- End of Logo -->
					<!-- Meta information -->
					<div class="meta">
						<p>
							Welcome, <?php echo $this -> session -> userdata('full_name');?>!
							<!--<a href="#" title="1 new private message from Elaine!" class="tooltip">1 new message!</a>-->
						</p>
						<ul>
							<li>
								<a href="<?php echo site_url("user_management/logout");?>" ><span class="ui-icon ui-icon-power"></span>Logout</a>
							</li>
							<li>
								<a href="user_management/change_password" title="Change My Password" class="tooltip"><span class="ui-icon ui-icon-wrench"></span>Change My Password</a>
							</li>
						</ul>
					</div>
					<!-- End of Meta information -->
				</div>
				<!-- End of Top-->
				<!-- The navigation bar -->
				<div id="navbar">
					<ul class="nav">
						<li >
							<a class="<?php
							if ($link == 'home') {echo "active";
							}
							?>" href="<?php echo base_url();?>">Dashboard</a>
						</li>
						<?php
$menus = $this -> menus -> userdata('menu_items'); 
foreach($menus as $menu){
						?>
						<li>
							<a  class="<?php
							if ($link == $menu['url'] || $menu['url'] == $link) {echo "active";
							}
						?>" href = "<?php echo base_url() . $menu['url'];?>" ><?php echo $menu['text'];?></a>
						</li>
						<?php }?>
					</ul>
				</div>
				<!-- End of navigation bar" --> 
				<!-- End of Search bar -->
			</div>
			<!-- End of Header -->
			<!-- Background wrapper -->
			<div id="bgwrap">
				<!-- Main Content -->
				<div id="content">
					<div id="main">
						<?php $this -> load -> view($content_view);?>
					</div>
				</div>
				<!-- End of Main Content -->
				<!-- Sidebar -->
				<div id="sidebar">
					<!-- Lists -->
					<h2>Quick Menu</h2>
					<ul>
						<?php 
$quick_menus = $this -> quick_menus -> userdata('quick_menu_items');
foreach($quick_menus as $quick_menu){
						?>
						<li class="<?php
						if ($quick_link == $quick_menu['indicator']) {echo " active ";
						}
						?>">
							<a href = "<?php echo base_url() . $quick_menu['url'];?>" ><?php echo $quick_menu['text'];?></a>
						</li>
						<?php }?>
					</ul>
				</div>
				<!-- End of Sidebar -->
			</div>
			<!-- End of bgwrap -->
		</div>
		<!-- End of Container -->
		<!-- Footer -->
		<div id="footer">
			<p class="mid">
				<a href="#" title="Top" class="tooltip">Top</a>&middot;<a href="#" title="Main Page" class="tooltip">Home</a>&middot;<a href="#" title="Change current settings" class="tooltip">Settings</a>&middot;<a href="#" title="End administrator session" class="tooltip">Logout</a>
			</p>
			<p class="mid">
				<!-- Change this to your own once purchased -->
				&copy; 2012. All rights reserved. <!-- -->
			</p>
		</div>
		<!-- End of Footer -->
	</body>
</html>
