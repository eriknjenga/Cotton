<?php
class Cash_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_cash_disbursements_v";
		$this -> base_params($data);
	}

	public function issue_cash() {
		$data['content_view'] = "add_cash_disbursement_v";
		$data['quick_link'] = "issue_cash";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Cash Management"; 
		$data['link'] = "cash_management";
		$this -> load -> view("demo_template", $data);
	}

}
