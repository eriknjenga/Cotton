<?php
class Purchase_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}
	public function listing() {
		$data['content_view'] = "list_purchases_v";
		$this -> base_params($data);
	}
	public function new_purchase() {
		$data['content_view'] = "add_purchase_v";
		$data['quick_link'] = "add_purchase";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Produce Purchase Management";
		$data['banner_text'] = "New Purchase";
		$data['link'] = "purchase_management";
		$this -> load -> view("demo_template", $data);
	}

}
