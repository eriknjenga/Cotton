<?php
class Search_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> search_page();
	}

	public function search_page() {
		$data['content_view'] = "document_search_v";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Search Engine";
		$data['banner_text'] = "Search Document";
		$data['link'] = "search_management";
		$this -> load -> view("demo_template", $data);
	}

}
