<?php
class Truck_Summaries extends MY_Controller {
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
		$data['content_view'] = "truck_summaries_v";
		$data['quick_link'] = "truck_summaries";
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
		if ($action == "Download Truck Summaries Excel") {
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
		$sql = "SELECT vehicle_number, SUM( distance ) AS total_distance, SUM( net_weight ) AS total_delivered FROM weighbridge w LEFT JOIN depot d ON w.buying_center_code = d.depot_code WHERE weighing_type =  '2' AND STR_TO_DATE( transaction_date,  '%d/%m/%Y' ) BETWEEN STR_TO_DATE(  '" . $start_date . "',  '%m/%d/%Y' )AND STR_TO_DATE(  '" . $end_date . "',  '%m/%d/%Y' )GROUP BY vehicle_number";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= "<tr><td>" . $depot_data['vehicle_number'] . "</td><td class='center'>" . number_format($depot_data['total_distance'] + 0) . "</td><td class='amount'>" . number_format($depot_data['total_delivered'] + 0) . "</td></tr>";
			$total_weight += $depot_data['total_delivered'];
			$total_distance += $depot_data['total_distance'];
		}
		$data_buffer .= "<tr></tr><tr><td>Totals</td><td class='center'>" . number_format($total_distance + 0) . "</td><td class='amount'>" . number_format($total_weight + 0) . "</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Truck Summaries Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $start_date, $end_date);

	}

	public function downloadExcel($start_date, $end_date) {
		$this -> load -> database();
		$data_buffer = "";
		$total_weight = 0;
		$total_distance = 0;
		$data_buffer .= $this -> echoExcelTitles();
		//Get data for each zone
		$sql = "SELECT vehicle_number, SUM( distance ) AS total_distance, SUM( net_weight ) AS total_delivered FROM weighbridge w LEFT JOIN depot d ON w.buying_center_code = d.depot_code WHERE weighing_type =  '2' AND STR_TO_DATE( transaction_date,  '%d/%m/%Y' ) BETWEEN STR_TO_DATE(  '" . $start_date . "',  '%m/%d/%Y' )AND STR_TO_DATE(  '" . $end_date . "',  '%m/%d/%Y' )GROUP BY vehicle_number";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= $depot_data['vehicle_number'] . "\t" . $depot_data['total_distance'] . "\t" . $depot_data['total_delivered'] . "\t\n";
			$total_weight += $depot_data['total_delivered'];
			$total_distance += $depot_data['total_distance'];
		}
		$data_buffer .= "\nTotals\t" . $total_distance . "\t" . $total_weight . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Truck Summaries.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Truck Summaries Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<thead><tr><th>Vehicle Number</th><th>Total Distance Covered (Kms.)</th><th>Total Cotton Delivered (Kgs.)</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "Vehicle Number\tTotal Distance Covered (Kms.)\tTotal Cotton Delivered (Kgs.)\t\n";
	}

	function generatePDF($data, $start_date, $end_date) {
		$start_date = date('d/m/Y', strtotime($start_date));
		$end_date = date('d/m/Y', strtotime($end_date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Truck Summaries</h3>";
		$html_title .= "<h5 style='text-align:center;'> Between " . $start_date . " and " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Truck Summaries');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|-{PAGENO}-|Truck Summaries Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Truck Summaries.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
