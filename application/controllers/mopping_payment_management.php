<?php
class Mopping_Payment_Management extends MY_Controller {
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
	public function new_payment() {
		$data['content_view'] = "add_mopping_payment_v";
		$data['quick_link'] = "add_mopping_payment";
		$data['depots'] = Depot::getAll();
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Mopping Payments";
		$data['banner_text'] = "New Payment";
		$data['link'] = "mopping_payment_management";
		$this -> load -> view("demo_template", $data);
	}

}
