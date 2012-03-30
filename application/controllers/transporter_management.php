<?php
class Transporter_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_transporters_v";
		$this -> base_params($data);
	}

	public function new_transporter() {
		$data['content_view'] = "add_transporter_v";
		$data['quick_link'] = "new_transporter";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Transporter Management"; 
		$data['link'] = "transporter_management";
		$this -> load -> view("demo_template", $data);
	}

}
