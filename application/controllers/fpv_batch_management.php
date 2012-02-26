<?php
class FPV_Batch_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$data = array();
		$this -> listing($data);
	}

	public function new_fpv_batch() {
		$data['content_view'] = "add_fpv_batch_v";
		$data['quick_link'] = "add_fpv_batch";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "FPV Batch Management";
		$data['banner_text'] = "New FPV Batch";
		$data['link'] = "fpv_batch_management";
		$this -> load -> view("demo_template", $data);
	}

}
