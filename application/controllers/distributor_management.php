<?php
class Distributor_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_distributors_v";
		$this -> base_params($data);
	}

	public function new_distributor() {
		$data['content_view'] = "add_distributor_v";
		$data['quick_link'] = "add_distributor";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Distributor Management";
		$data['banner_text'] = "Distributor Registration";
		$data['link'] = "admin";
		$this -> load -> view("demo_template", $data);
	}

}
