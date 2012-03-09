<?php
class Area_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_areas_v";
		$this -> base_params($data);
	}

	public function new_area() {
		$data['content_view'] = "add_area_v";
		$data['quick_link'] = "add_area";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Area Management";
		$data['banner_text'] = "Area Registration";
		$data['link'] = "area_management";
		$this -> load -> view("demo_template", $data);
	}

}
