<?php
class Depot_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$data = array();
		$this -> listing($data);
	}

	public function new_depot() {
		$data['content_view'] = "add_depot_v";
		$data['quick_link'] = "add_depot";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Depots Management";
		$data['banner_text'] = "Depot Registration";
		$data['link'] = "depot_management";
		$this -> load -> view("demo_template", $data);
	}

}
