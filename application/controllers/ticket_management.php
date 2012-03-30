<?php
class Ticket_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> listing();
	}

	public function listing() {
		$data['content_view'] = "list_tickets_v";
		$this -> base_params($data);
	}

	public function new_ticket() {
		$data['content_view'] = "add_ticket_v";
		$data['quick_link'] = "new_ticket";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Ticket Management"; 
		$data['link'] = "ticket_management";
		$this -> load -> view("demo_template", $data);
	}

}
