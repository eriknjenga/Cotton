<!DOCTYPE html>
<html>
	<!-- Mirrored from unithemes.net/preview/wide_admin/login.html by HTTrack Website Copier/3.x [XR&CO'2010], Sun, 05 Feb 2012 16:14:07 GMT -->
	<head>
		<!-- Meta -->
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<!-- End of Meta -->
		<!-- Page title -->
		<title><?php echo $title;?></title>
		<!-- End of Page title -->
		<!-- Libraries -->
		<link type="text/css" href="<?php echo base_url().'CSS/login.css'?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url().'CSS/jquery-ui.css'?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url().'Scripts/jquery.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'Scripts/jquery-ui.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'Scripts/easyTooltip.js'?>"></script>
		<!-- End of Libraries -->
	</head>
	<body>
		<div id="container">
			<div class="logo">
				<a href="#"><img src="<?php echo base_url().'Images/logo.png'?>" alt="" /></a>
			</div>
			<div id="box">
				<?php
				echo validation_errors('<p class="error">', '</p>');
				if (isset($invalid)) {
					echo "<p class='error'>Invalid Credentials. Please try again</p>";
				} else if (isset($inactive)) {
					echo "<p class='error'>The Account is not active. Seek help from the Administrator</p>";
				}
				?>
				<form action="<?php echo base_url().'user_management/authenticate'?>" method="post"  >
					<p class="main">
						<label>Username: </label>
						<input name="username" value="" />
						<label>Password: </label>
						<input type="password" name="password" value="">
					</p>
					<p class="space">
						<span>
							<input type="checkbox" />
							Remember me</span>
						<input type="submit" value="Login" class="login" />
					</p>
				</form>
			</div>
		</div>
	</body>
</html>
