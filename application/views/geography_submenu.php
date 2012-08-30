<?php
if (!isset($sub_link)) {
	$sub_link = "";
}
?>
<style>
	#navbar ul.sub-nav { 
		height: 40px; 
		line-height: 20px;
	}
	.double-line{
		line-height: 12px;
	}
</style>
<div id="navbar" >
	<ul class="nav sub-nav" style="background-color: black; margin-top:0">
		<li>
			<a  href = "<?php echo site_url('region_management')?>" class="<?php
			if ($sub_link == 'region_management') {echo "active";
			}
			?>" >Zones</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('district_management')?>" class="<?php
			if ($sub_link == 'district_management') {echo "active";
			}
			?>" >Districts</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('ward_management')?>" class="<?php
			if ($sub_link == 'ward_management') {echo "active";
			}
			?>" >Wards</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('village_management')?>" class="<?php
			if ($sub_link == 'village_management') {echo "active";
			}
			?>" >Villages</a>
		</li>
		<li class="double-line">
			<a  href = "<?php echo site_url('route_management')?>" class="<?php
			if ($sub_link == 'route_management') {echo "active";
			}
			?>" >Purchasing</br>Routes</a>
		</li>
		<li class="double-line">
			<a  href = "<?php echo site_url('cash_route_management')?>" class="<?php
			if ($sub_link == 'cash_route_management') {echo "active";
			}
			?>" >Cashier</br>Routes</a>
		</li>
		<li class="double-line">
			<a  href = "<?php echo site_url('collection_route_management')?>" class="<?php
			if ($sub_link == 'collection_route_management') {echo "active";
			}
			?>" >Collection</br>Routes</a>
		</li>
		<li class="double-line">
			<a  href = "<?php echo site_url('audit_route_management')?>" class="<?php
			if ($sub_link == 'audit_route_management') {echo "active";
			}
			?>" >Audit</br>Routes</a>
		</li>
	</ul>
</div>