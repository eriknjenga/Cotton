<?php
class Daily_Dispatches extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> view_interface();
	}

	public function view_interface($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "daily_dispatches_v";
		$data['quick_link'] = "daily_dispatches";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$action = $this -> input -> post("action");
		$start_date = $this -> input -> post("start_date");
		$end_date = $this -> input -> post("end_date");
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Daily Dispatches Excel") {
			$this -> downloadExcel($start_date, $end_date);
			return;
		}
		$this -> load -> database();
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			}
			table.data-table td {
			width: 50px;
			font-size:11;
			}
			table.data-table th {
			width: 50px;
			font-size:11;
			}
			.amount{
				text-align:right;
			}
			.center{
				text-align:center;
			}			
			</style>
			";
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$total_weight = 0;
		$total_distance = 0;
		$data_buffer .= $this -> echoTitles();
		//Get data for each zone
		$sql = "select transaction_date,vehicle_number,depot_code,depot_name,distance, buying_center_code,net_weight,ticket_number from weighbridge w left join depot d on w.buying_center_code = d.depot_code where weighing_type = '2' and str_to_date(transaction_date,'%d/%m/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by str_to_date(transaction_date,'%d/%m/%Y') asc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= "<tr><td class='center'>" . $depot_data['transaction_date'] . "</td><td class='center'>" . $depot_data['vehicle_number'] . "</td><td class='center'>" . $depot_data['ticket_number'] . "</td><td class='center'>" . $depot_data['depot_name'] . "</td><td class='center'>" . $depot_data['distance'] . "</td><td class='amount'>" . number_format($depot_data['net_weight'] + 0) . "</td></tr>";
			$total_weight += $depot_data['net_weight'];
			$total_distance += $depot_data['distance'];
		}
		$data_buffer .= "<tr></tr><tr><td>Totals: </td><td class='center'>-</td><td class='center'>-</td><td  class='center'>-</td><td  class='center'>" . number_format($total_distance + 0) . "</td><td class='amount'>" . number_format($total_weight + 0) . "</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Daily Dispatches Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $start_date, $end_date);

	}

	public function downloadExcel($start_date, $end_date) {
		$this -> load -> database();
		$data_buffer = "";
		$total_weight = 0;
		$total_distance = 0;
		$data_buffer .= $this -> echoExcelTitles();
		//Get data for each zone
		$sql = "select transaction_date,vehicle_number,depot_code,depot_name,distance, buying_center_code,net_weight,ticket_number from weighbridge w left join depot d on w.buying_center_code = d.depot_code where weighing_type = '2' and str_to_date(transaction_date,'%d/%m/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by str_to_date(transaction_date,'%d/%m/%Y') asc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= $depot_data['transaction_date'] . "\t" . $depot_data['vehicle_number'] . "\t" . $depot_data['ticket_number'] . "\t" . $depot_data['depot_name'] . "\t" . $depot_data['distance'] . "\t" . $depot_data['net_weight'] . "\t\n";
			$total_weight += $depot_data['net_weight'];
			$total_distance += $depot_data['distance'];
		}
		$data_buffer .= "\nTotals: \t-\t-\t-\t" . $total_distance . "\t" . $total_weight . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Daily Dispatches.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Daily Dispatches Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<thead><tr><th>Transaction Date</th><th>Vehicle Number</th><th>Ticket Number</th><th>Buying Center</th><th>Distance (Kms)</th><th>Net Weight (Kgs.)</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "Transaction Date\tVehicle Number\tTicket Number\tBuying Center\tDistance (Kms)\tNet Weight (Kgs.)\t\n";
	}

	function generatePDF($data, $start_date, $end_date) {
		$start_date = date('d/m/Y', strtotime($start_date));
		$end_date = date('d/m/Y', strtotime($end_date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Daily Dispatches</h3>";
		$html_title .= "<h5 style='text-align:center;'> From " . $start_date . " to " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Daily Dispatches');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|-{PAGENO}-|Daily Dispatches Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Daily Dispatches.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
