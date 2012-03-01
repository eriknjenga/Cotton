<?php
class Account_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_accounts_v";
		$this -> base_params($data);
	}


	public function new_account() {
		$data['content_view'] = "add_account_v";
		$data['quick_link'] = "add_account";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Account Management";
		$data['banner_text'] = "New Account";
		$data['link'] = "admin";
		$this -> load -> view("demo_template", $data);
	}

}
