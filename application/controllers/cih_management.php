<?php
class CIH_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$data = array();
		$this -> listing($data);
	}

	public function new_cih_batch() {
		$data['content_view'] = "add_cih_batch_v";
		$data['quick_link'] = "add_cih_batch";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "CIH Batch Management";
		$data['banner_text'] = "New CIH Batch";
		$data['link'] = "cih_batch_management";
		$this -> load -> view("demo_template", $data);
	}

}
