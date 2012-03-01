<?php
class Region_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_regions_v";
		$this -> base_params($data);
	}


	public function new_region() {
		$data['content_view'] = "add_region_v";
		$data['quick_link'] = "add_region";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Region Management";
		$data['banner_text'] = "New Region";
		$data['link'] = "admin";
		$this -> load -> view("demo_template", $data);
	}

}
