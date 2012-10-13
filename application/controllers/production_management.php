<?php
class Production_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> fetch_data();
	}

	public function search_lot_number() {
		$search_term = $this -> input -> post("search_value7");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$ticket = Production_Data::getSearchedLotNumber($db_search_term);
		$data['ticket'] = $ticket;
		$data['content_view'] = "list_lot_number_search_results_v";
		$this -> load -> view("demo_template", $data);
	}

	public function upload_interface() {
		$data['title'] = "Production Management";
		$data['content_view'] = "production_upload_v";
		$data['quick_link'] = "add_account";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);
	}

	public function fetch_data() {
		$this -> load -> library('csvreader');

		$filePath1 = '/sg.csv';
		$filePath2 = '/dr.csv';
		$resource1 = @fopen($filePath1, 'r');
		$resource2 = @fopen($filePath2, 'r');
		if (!$resource1 || !$resource2) {
			$data['content_view'] = "production_error";
			$data['link'] = "home";
			$this -> load -> view("demo_template", $data);
			return;
		}
		//start with the sow gin
		$data1 = $this -> csvreader -> parse_file($filePath1, false);
		$records1 = 0;
		if ($data1 == true) {
			$records = count($data1);
			foreach ($data1 as $row) {
				$production_data = new Production_Data();
				$production_data -> Ginnery = '1';
				$production_data -> Date = $row[0];
				$production_data -> Time = $row[1];
				$production_data -> Lot_Number = $row[2];
				$production_data -> Consecutive_Number = $row[3];
				$production_data -> Gross_Weight = $row[4];
				$production_data -> save();
			}
		}
		$file1 = fopen($filePath1, 'w');
		fclose($file1);
		$data['sow_records'] = $records1;
		//then the other ginnery
		$data2 = $this -> csvreader -> parse_file($filePath2, false);
		$records2 = 0;
		if ($data2 == true) {
			$records = count($data2);
			foreach ($data2 as $row) {
				$production_data = new Production_Data();
				$production_data -> Ginnery = '2';
				$production_data -> Date = $row[0];
				$production_data -> Time = $row[1];
				$production_data -> Lot_Number = $row[2];
				$production_data -> Consecutive_Number = $row[3];
				$production_data -> Gross_Weight = $row[4];
				$production_data -> save();
			}
		}
		$file2 = fopen($filePath2, 'w');
		fclose($file2);
		$data['other_records'] = $records2;
		$this -> base_params($data);
		return;
	}

	public function base_params($data) {
		$data['title'] = "Weighbridge Management";
		$data['content_view'] = "production_v";
		$data['quick_link'] = "add_account";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);
	}

}
