<?php
class Store_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_stores_v";
		$this -> base_params($data);
	}

	public function new_store() {
		$data['content_view'] = "add_store_v";
		$data['quick_link'] = "add_store";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Store Management";
		$data['banner_text'] = "Add Store";
		$data['link'] = "area_management";
		$this -> load -> view("demo_template", $data);
	}

}
