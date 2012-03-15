<?php
class Purchase_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_purchases_v";
		$this -> base_params($data);
	}

	public function new_purchase() {
		$data['content_view'] = "add_purchase_v";
		$data['quick_link'] = "add_purchase";
		$this -> base_params($data);
	}

	public function get_average_price_graph_data() {
		echo '<chart caption="Average Buying Price" xAxisName="Month" yAxisName="Price per Tonne" showValues="0" decimals="0" formatNumberScale="0">
<set label="Jan" value="25000"/>
<set label="Feb" value="24500"/>
<set label="Mar" value="26000"/>
<set label="Apr" value="26000"/>
<set label="May" value="25000"/>
<set label="Jun" value="25500"/>
<set label="Jul" value="26000"/>
<set label="Aug" value="28000"/>
<set label="Sep" value="26000"/>
<set label="Oct" value="25500"/>
<set label="Nov" value="26500"/>
<set label="Dec" value="26000"/>
</chart>';
	}

	public function get_purchases_to_date_graph_data() {
		echo '<chart palette="4" caption="Total Purchases to Date (in Tonnes) from 1st Jan."     enableRotation="1" bgColor="99CCFF,FFFFFF" bgAlpha="40,100" bgRatio="0,100" bgAngle="360" showBorder="1" startingAngle="70">
<set label="Mumbwa Region" value="1700"/>
<set label="Southern Region" value="1200"/>
<set label="Nothern Region" value="1800"/>
<set label="Mapanza" value="800" />
<set label="Siavonga" value="1000" />
<set label="Mvumbe" value="2600" isSliced="1"/>
<set label="Situmbeko" value="1100"/>  
</chart>';
	}
		public function get_area_production_graph_data() {
		echo '<chart caption="Total Area Production" xAxisName="Month" yAxisName="Total Tonnage" showValues="0" decimals="0" formatNumberScale="0">
<set label="Jan" value="17400"/>
<set label="Feb" value="19800"/>
<set label="Mar" value="21800"/>
<set label="Apr" value="23800"/>
<set label="May" value="29600"/>
<set label="Jun" value="27600"/>
<set label="Jul" value="31800"/>
<set label="Aug" value="39700"/>
<set label="Sep" value="37800"/>
<set label="Oct" value="21900"/>
<set label="Nov" value="32900"/>
<set label="Dec" value="39800"/>
</chart>';
	}
	

	public function base_params($data) {
		$data['title'] = "Produce Purchase Management";
		$data['banner_text'] = "New Purchase";
		$data['link'] = "purchase_management";
		$this -> load -> view("demo_template", $data);
	}

}
