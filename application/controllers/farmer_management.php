<?php
class Farmer_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$data = array();
		$this -> listing($data);
	}

	public function register() {
		$data['content_view'] = "add_farmer_v";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Farmer Management";
		$data['banner_text'] = "Farmer Registration";
		$data['link'] = "farmer_management";
		$this -> load -> view("demo_template", $data);
	}

}
