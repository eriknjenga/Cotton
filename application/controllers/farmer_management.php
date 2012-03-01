<?php
class Farmer_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_farmers_v";
		$this -> base_params($data);
	}

	public function register() {
		$data['content_view'] = "add_farmer_v";
		$data['quick_link'] = "register_farmer";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Farmer Management";
		$data['banner_text'] = "Farmer Registration";
		$data['link'] = "admin";
		$this -> load -> view("demo_template", $data);
	}

}
