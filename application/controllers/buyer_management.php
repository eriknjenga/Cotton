<?php
class Buyer_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_buyers_v";
		$this -> base_params($data);
	}

	public function new_buyer() {
		$data['content_view'] = "add_buyer_v";
		$data['quick_link'] = "add_buyer";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['banner_text'] = "Buyer Registration";
		$data['link'] = "buyer_management";
		$this -> load -> view("demo_template", $data);
	}

}
