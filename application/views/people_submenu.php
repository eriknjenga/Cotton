<?php
if (!isset($sub_link)) {
	$sub_link = "";
}
?>
<div id="navbar" >
	<ul class="nav" style="background-color: black; margin-top:0">
		<li>
			<a  href = "<?php echo site_url('user_management')?>" class="<?php
			if ($sub_link == 'user_management') {echo "active";
			}
			?>" >System Users</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('field_officer_management')?>" class="<?php
			if ($sub_link == 'field_officer_management') {echo "active";
			}
			?>" >FEOs</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('agent_management')?>" class="<?php
			if ($sub_link == 'agent_management') {echo "active";
			}
			?>" >Agents</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('buyer_management')?>" class="<?php
			if ($sub_link == 'buyer_management') {echo "active";
			}
			?>" >Buyers</a>
		</li>
		<li>
			<a  href = "<?php echo site_url('field_cashier_management')?>" class="<?php
			if ($sub_link == 'field_cashier_management') {echo "active";
			}
			?>" >Field Cashiers</a>
		</li>
	</ul>
</div>