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

	public function register() {
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

}
