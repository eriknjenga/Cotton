<?php
class Truck_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_trucks_v";
		$this -> base_params($data);
	}

	public function new_truck() {
		$data['content_view'] = "add_truck_v";
		$data['quick_link'] = "new_truck";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Truck Management"; 
		$data['link'] = "truck_management";
		$this -> load -> view("demo_template", $data);
	}

}
