<?php
class Report_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_levels = Access_Level::getTotalLevels();
		$levels = Access_Level::getPagedLevels($offset, $items_per_page);
		if ($number_of_levels > $items_per_page) {
			$config['base_url'] = base_url() . "report_management/listing/";
			$config['total_rows'] = $number_of_levels;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['levels'] = $levels;
		$data['title'] = "All Access Levels";
		$data['content_view'] = "list_access_levels_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function manage_reports($level) {
		$this -> load -> database();
		$data['level'] = $level;
		$data['level_object'] = Access_Level::getLevel($level);
		$sql = "select qr.*,q.id as menu,menu_text,description from quick_menu q left join quick_menu_user_right qr on qr.menu = q.id and access_level = '$level' where q.report = '1'";
		$query = $this -> db -> query($sql);
		$report_data = $query -> result_array();
		$data['reports'] = $report_data;
		$data['content_view'] = "level_reports_v";
		$this -> base_params($data);
	}

	public function save() {
		$this -> load -> database();
		$level = $this -> input -> post("level");
		$reports = $this -> input -> post("report");
		$sql = "select * from quick_menu q left join quick_menu_user_right qr on qr.menu = q.id and access_level = '$level' where q.report = '1'";
		$query = $this -> db -> query($sql);
		$report_data = $query -> result_array();
		foreach ($report_data as $report) {
			$report = $report['menu'];
			if (isset($report)) {
				$right = Quick_Menu_User_Right::getRight($level, $report);
				$right -> delete();
			}
		}
		foreach ($reports as $report) {
			if (isset($report)) {
				$right = new Quick_Menu_User_Right();
				$right -> Access_Level = $level;
				$right -> Menu = $report;
				$right -> save();
			}
		}
		redirect("report_management");
	}

	public function save_price() {
		$valid = $this -> validate_form();
		if ($valid) {
			$log = new System_Log();
			$date = $this -> input -> post("date");
			$season = $this -> input -> post("season");
			$price = $this -> input -> post("price");
			$log -> Log_Type = "1";
			$log -> Log_Message = "Created New Cotton Price Record {Effective Date: '" . $date . "' Season: '" . $season . "' Cotton Price: '" . $price . "'}";
			$cotton_price = new Cotton_Price();
			$cotton_price -> Date = $date;
			$cotton_price -> Price = $price;
			$cotton_price -> Season = $season;
			$cotton_price -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
		} else {
			$this -> new_price();
		}
		redirect("price_management/listing");
	}

	public function delete_price($id) {
		$price = Cotton_Price::getPrice($id);
		$price -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Cotton Price Record {Effective Date: '" . $price -> Date . "' Season: '" . $price -> Season . "' Cotton Price: '" . $price -> Price . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function base_params($data) {
		$data['title'] = "Report Management";
		$data['link'] = "report_management";

		$this -> load -> view("demo_template", $data);
	}

}
