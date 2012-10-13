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
		$data['type'] = $type;
		$data['title'] = "All System Logs";
		$data['content_view'] = "list_system_logs_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function download($type) {
		$this -> load -> database(); 
		if ($type > 0) {
			$sql = "select * from system_log s left join user u on s.user = u.id where log_type = '".$type."' order by s.id desc";
		} else {
			$sql = "select * from system_log s left join user u on s.user = u.id order by s.id desc";
		}
		$log_result = $this -> db -> query($sql);;
		$logs = $log_result->result_array();
		$log_types = array("1" => "Record Creation", 2 => "Record Modification", 3 => "Record Deletion", 4 => "Report Download");
		$data_buffer = "Log Type\tUser\tLog Message\tTimestamp\t\n";
		foreach ($logs as $log) {
			$data_buffer .= $log_types[$log['log_type']] . "\t" . $log['Name'] . "\t" . $log['log_message'] . "\t" . date("d/m/Y h:i:s", $log['timestamp']) . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Logs.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
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
