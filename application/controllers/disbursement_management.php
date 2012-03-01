<?php
class Disbursement_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> disbursement_listing();
	}

	public function disbursement_listing() {
		$data['content_view'] = "list_disbursement_v";
		$this -> base_params($data);
	}

	public function loan_returns_listing() {
		$data['content_view'] = "list_loan_returns_v";
		$this -> base_params($data);
	}

	public function new_disbursement() {
		$data['content_view'] = "add_disbursement_v";
		$data['quick_link'] = "add_disbursement";
		$this -> base_params($data);
	}

	public function new_input_return() {
		$data['content_view'] = "add_input_return_v";
		$data['quick_link'] = "add_input_return";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Input Disbursement Management";
		$data['banner_text'] = "New Disbursement";
		$data['link'] = "disbursement_management";
		$this -> load -> view("demo_template", $data);
	}

}
