<?php
if (!isset($sub_link)) {
	$sub_link = "";
}
?>
<div id="navbar" >
	<ul class="nav" style="background-color: black; margin-top:0">
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
		<li>
			<a  href = "<?php echo site_url('route_management')?>" class="<?php
			if ($sub_link == 'route_management') {echo "active";
			}
			?>" >Purchasing Routes</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('cash_route_management')?>" class="<?php
			if ($sub_link == 'cash_route_management') {echo "active";
			}
			?>" >Cash Disbursement Routes</a>
		</li>
	</ul>
</div>