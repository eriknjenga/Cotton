<?php
class Region_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_regions_v";
		$this -> base_params($data);
	}

	public function new_region() {
		$data['content_view'] = "add_region_v";
		$data['quick_link'] = "add_region";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Region Management";
		$data['banner_text'] = "New Region";
		$data['link'] = "admin";
		$this -> load -> view("demo_template", $data);
	}

	public function get_region_graph_data() {
		echo '<chart palette="1" caption="Region Performance for Mumbwa Region" shownames="1" showvalues="0" sYAxisValuesDecimals="2" connectNullData="0" PYAxisName="Total Value of Loans" SYAxisName="Total Value of Purchases" numDivLines="4" formatNumberScale="0">
<categories>
<category label="January"/>
<category label="February"/>
<category label="March"/>
<category label="April"/>
<category label="May"/>
</categories> 
<dataset seriesName="Total Loan Value" color="F6BD0F" showValues="0">
<set value="57401.85"/>
<set value="41941.19"/>
<set value="45263.37"/>
<set value="117320.16"/>
<set value="114845.27" dashed="1"/>
</dataset>
<dataset seriesName="Total Purchase Value" color="8BBA00" showValues="0" parentYAxis="S">
<set value="45000"/>
<set value="44835"/>
<set value="42835"/>
<set value="77557"/>
<set value="92633"/>
</dataset>
</chart>';
	}

}
