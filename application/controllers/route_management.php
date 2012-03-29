<?php
class Route_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_routes_v";
		$this -> base_params($data);
	}

	public function new_route() {
		$data['content_view'] = "add_route_v";
		$data['quick_link'] = "new_route";
		$data['areas'] = Area::getAll();
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Route Management";
		$data['banner_text'] = "New Route";
		$data['link'] = "route_management";
		$this -> load -> view("demo_template", $data);
	}

}
