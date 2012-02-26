<?php
class Account_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$data = array();
		$this -> listing($data);
	}

	public function new_account() {
		$data['content_view'] = "add_account_v";
		$data['quick_link'] = "add_account";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Account Management";
		$data['banner_text'] = "New Account";
		$data['link'] = "account_management";
		$this -> load -> view("demo_template", $data);
	}

}
