<?php
class Geography_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() { 
		redirect("region_management");
	}
}
