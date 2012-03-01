<?php
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
		<script type="text/javascript" src="<?php echo base_url().'Scripts/custom.js'?>"></script>
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
						<a href="#" title="Administration Home" class="tooltip"><img src="<?php echo base_url().'Images/logo.png'?>" alt="farm2" /></a>
					</div>
					<!-- End of Logo -->
					<!-- Meta information -->
					<div class="meta">
						<p>
							Welcome, Abdullah Taib! <!--<a href="#" title="1 new private message from Elaine!" class="tooltip">1 new message!</a>-->
						</p>
						<ul>
							<li>
								<a href="#" title="End administrator session" class="tooltip"><span class="ui-icon ui-icon-power"></span>Logout</a>
							</li>
							<li>
								<a href="#" title="Change current settings" class="tooltip"><span class="ui-icon ui-icon-wrench"></span>Settings</a>
							</li>
							<li>
								<a href="#" title="Go to your account" class="tooltip"><span class="ui-icon ui-icon-person"></span>My account</a>
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
						<li>
							<a class="<?php
							if ($link == 'disbursement_management') {echo "active";
							}
							?>" href="<?php echo site_url("disbursement_management");?>">Farmer Loans</a>
							<ul>
								<li>
									<a href="<?php echo site_url("disbursement_management");?>">Input Loans</a>
								</li>
								<li>
									<a href="<?php echo site_url("disbursement_management/loan_returns_listing");?>">Input Loan Returns</a>
								</li>
							</ul>
						</li>
						<li>
							<a class="<?php
							if ($link == 'purchase_management') {echo "active";
							}
							?>" href="<?php echo site_url("purchase_management/listing");?>">Produce Purchases</a>
						</li>
						<li>
							<a class="<?php
							if ($link == 'batch_management') {echo "active";
							}
							?>" href="<?php echo site_url("cih_batch_management");?>">Batches</a>
							<ul>
								<li>
									<a href="<?php echo site_url("cih_batch_management");?>">CIH Batches</a>
								</li>
								<li>
									<a href="<?php echo site_url("fpv_batch_management");?>">FPV Batches</a>
								</li>
							</ul>
						</li>
						<li>
							<a class="<?php
							if ($link == 'admin') {echo "active";
							}
							?>" href="<?php echo site_url("farmer_management");?>">Administration</a>
							<ul>
								<li>
									<a href="<?php echo site_url("farmer_management");?>">Farmers</a>
								</li>
								<li>
									<a href="<?php echo site_url("user_management");?>">Users</a>
								</li>
								<li>
									<a href="<?php echo site_url("farm_input_management");?>">Farm Inputs</a>
								</li>
								<li>
									<a href="<?php echo site_url("depot_management");?>">Depots</a>
								</li>
								<li>
									<a href="<?php echo site_url("area_management");?>">Areas</a>
								</li>
								<li>
									<a href="<?php echo site_url("distributor_management");?>">Distributors</a>
								</li>
								<li>
									<a href="<?php echo site_url("account_management");?>">Accounts</a>
								</li>
								<li>
									<a href="<?php echo site_url("region_management");?>">Regions</a>
								</li>
							</ul>
						</li>
						<!--<li>
							<a href="#">Reports</a>
							<ul>
								<li>
									<a href="#">Report 1</a>
								</li>
								<li>
									<a href="#">Report 2</a>
								</li>
								<li>
									<a href="#">Report 3</a>
								</li>
								<li>
									<a href="#">Report 4</a>
								</li>
							</ul>
						</li> -->
					</ul>
				</div>
				<!-- End of navigation bar" -->
				<!-- Search bar -->
				<div id="search">
					<form action="" method="POST">
						<p>
							<input type="submit" value="" class="but" />
							<input type="text" name="q" value="Search the farm2 system" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" />
						</p>
					</form>
				</div>
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
						<li class="<?php
						if ($quick_link == "register_farmer") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('farmer_management/register')?>" >Register Farmer</a>
						</li>
						<li class="<?php
						if ($quick_link == "register_user") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('user_management/register')?>" >Add User</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_input_product") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('farm_input_management/register')?>" >Add Input Product</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_depot") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('depot_management/new_depot')?>" >Add New Depot</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_area") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('area_management/new_area')?>" >Add New Area</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_distributor") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('distributor_management/register')?>" >Add Distributor</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_region") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('region_management/new_region')?>" >Add Region</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_account") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('account_management/new_account')?>" >Add an Account</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_disbursement") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('disbursement_management/new_disbursement')?>" >Disburse Inputs</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_purchase") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('purchase_management/new_purchase')?>" >Add Purchases</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_input_return") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('disbursement_management/new_input_return')?>" >Add Input Return</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_cih_batch") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('cih_batch_management/new_cih_batch')?>" >Add CIH Batch</a>
						</li>
						<li class="<?php
						if ($quick_link == "add_fpv_batch") {echo "active";
						}
						?>">
							<a href="<?php echo site_url('fpv_batch_management/new_fpv_batch')?>" >Add FPV Batch</a>
						</li>
					</ul>
					<!-- End of Lists -->
					<!--<h2>Accordion</h2>
					Accordion
					<div id="accordion">
					<div>
					<h3><a href="#" title="First slide" class="tooltip">First</a></h3>
					<div>
					Praesent augue urna, vehicula sed sollicitudin quis, dignissim nec est. Quisque dignissim lorem at metus vehicula ut feugiat eros vestibulum. Suspendisse ultrices, massa luctus aliquam faucibus, sem quam fermentum nisl, non posuere quam nunc vel tellus.
					</div>
					</div>
					<div>
					<h3><a href="#" title="Second slide" class="tooltip">Second</a></h3>
					<div>
					Sed sem elit, porttitor quis vestibulum ut, euismod id purus. Praesent vulputate dolor vel nisi mattis sollicitudin. Curabitur placerat quam at sem tempor ac sodales nunc dapibus. Nullam mi purus, adipiscing in facilisis sed, posuere ut ipsum.
					</div>
					</div>
					<div>
					<h3><a href="#" title="Third slide" class="tooltip">Third</a></h3>
					<div>
					Praesent augue urna, vehicula sed sollicitudin quis, dignissim nec est. Quisque dignissim lorem at metus vehicula ut feugiat eros vestibulum. Suspendisse ultrices, massa luctus aliquam faucibus, sem quam fermentum nisl, non posuere quam nunc vel tellus.
					</div>
					</div>
					</div>-->
					<!-- End of Accordion-->
					<!--<h2>Datepicker</h2>
					Datepicker
					<div id="datepicker"></div>
					<!-- End of Datepicker -->
					<!-- Sortable Dialogs
					<h2>Sortable Dialogs</h2>
					<div class="sort">
					<div class="box ui-widget ui-widget-content ui-corner-all portlet">
					<div class="portlet-header">
					Sortable 1
					</div>
					<div class="portlet-content">
					<p>
					This is a sortable dialog. Praesent augue urna, vehicula sed sollicitudin quis, dignissim nec est.
					</p>
					</div>
					</div>
					<div class="box ui-widget ui-widget-content ui-corner-all portlet">
					<div class="portlet-header">
					Sortable 2
					</div>
					<div class="portlet-content">
					<p>
					This is a sortable dialog. Praesent augue urna, vehicula sed sollicitudin quis, dignissim nec est.
					</p>
					</div>
					</div>
					<div class="box ui-widget ui-widget-content ui-corner-all portlet">
					<div class="portlet-header">
					Sortable 3
					</div>
					<div class="portlet-content">
					<p>
					This is a sortable dialog. Praesent augue urna, vehicula sed sollicitudin quis, dignissim nec est.
					</p>
					</div>
					</div>
					</div>
					End of Sortable Dialogs -->
					<!-- Statistics
					<h2>Statistics</h2>
					<p>
					<b>Articles:</b> 2201
					</p>
					<p>
					<b>Comments:</b> 17092
					</p>
					<p>
					<b>Users:</b> 3788
					</p>
					End of Statistics -->
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
