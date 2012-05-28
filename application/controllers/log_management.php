<?php
class Log_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($type = 0, $offset = 0) {
		$items_per_page = 20;
		$number_of_logs = System_Log::getTotalLogs($type);
		$logs = System_Log::getPagedLogs($type, $offset, $items_per_page);
		if ($number_of_logs > $items_per_page) {
			$config['base_url'] = base_url() . "log_management/listing/" . $type . "/";
			$config['total_rows'] = $number_of_logs;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['logs'] = $logs;
		$data['title'] = "All System Logs";
		$data['content_view'] = "list_system_logs_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function view_log_details($log_id) {
		$log = System_Log::getLog($log_id);
		$data['log'] = $log;
		$data['content_view'] = "view_log_details_v";
		$this -> base_params($data);
	}

	public function base_params($data) {
		$data['title'] = "Security Log Management";
		$data['link'] = "log_management";

		$this -> load -> view("demo_template", $data);
	}

}
