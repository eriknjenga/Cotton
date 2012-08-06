<?php
class People_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() { 
		redirect("user_management");
	}
}
