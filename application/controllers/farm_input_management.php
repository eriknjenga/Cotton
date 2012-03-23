<?php
class Farm_Input_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_farm_inputs_v";
		$this -> base_params($data);
	}

	public function new_input() {
		$data['content_view'] = "add_farm_input_v";
		$data['quick_link'] = "add_input_product";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Farmer Inputs Management";
		$data['banner_text'] = "Farm Input Registration";
		$data['link'] = "admin";
		$this -> load -> view("demo_template", $data);
	}
	public function get_loan_movement_graph_data(){
				echo '<chart caption="Total Loans Outstanding" xAxisName="Month" yAxisName="Total Value (Millions)" showValues="0" decimals="0" formatNumberScale="0">
<set label="Jan" value="462"/>
<set label="Feb" value="857"/>
<set label="Mar" value="671"/>
<set label="Apr" value="494"/>
<set label="May" value="761"/>
<set label="Jun" value="960"/>
<set label="Jul" value="629"/>
<set label="Aug" value="622"/>
<set label="Sep" value="376"/>
<set label="Oct" value="494"/>
<set label="Nov" value="761"/>
<set label="Dec" value="960"/>
</chart>';
	}

}
