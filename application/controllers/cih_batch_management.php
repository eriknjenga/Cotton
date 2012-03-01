<?php
class CIH_Batch_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() { 
		$this -> listing();
	}
	public function listing() {
		$data['content_view'] = "list_cih_batches_v";
		$this -> base_params($data);
	}
	public function new_cih_batch() {
		$data['content_view'] = "add_cih_batch_v";
		$data['quick_link'] = "add_cih_batch";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "CIH Batch Management";
		$data['banner_text'] = "New CIH Batch";
		$data['link'] = "batch_management";
		$this -> load -> view("demo_template", $data);
	}

}
