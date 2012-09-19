<?php
class Daily_Purchase_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_interface();
	}

	public function view_interface($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "daily_purchases_v";
		$data['quick_link'] = "daily_purchases";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$action = $this -> input -> post("action");

		$date = date("d/m/Y");
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Daily Purchases Excel") {
			$this -> downloadExcel();
			return;
		}
		$this -> load -> database();
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 1000px;
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
		$data_buffer .= $this -> echoTitles();
		//Get all the depots in this region
		$sql_purchase_summaries = "SELECT date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as date,sum(loan_recovery) as total_recoveries,sum(gross_value) as total_value,sum(free_farmer_value) as total_free_farmer_value,sum(quantity) as total_quantity,sum(free_farmer_quantity) as total_free_farmer_quantity,count(distinct dpn) as buying_centers FROM `purchase` where batch_status = '2' group by date order by str_to_date(date,'%m/%d/%Y') asc";
		$purchase_summaries = $this -> db -> query($sql_purchase_summaries);
		$cumulative_free_farmer_quantity = 0;
		$cumulative_free_farmer_value = 0;
		$cumulative_fbg_quantity = 0;
		$cumulative_fbg_value = 0;
		$cumulative_recoveries = 0;
		foreach ($purchase_summaries->result_array() as $summary) {
			$cumulative_free_farmer_quantity += $summary['total_free_farmer_quantity'];
			$cumulative_free_farmer_value += $summary['total_free_farmer_value'];
			$cumulative_fbg_quantity += $summary['total_quantity'];
			$cumulative_fbg_value += $summary['total_value'];
			$cumulative_recoveries += $summary['total_recoveries'];
			$data_buffer .= "<tr><td class='center'>" . $summary['date'] . "</td><td class='center'>" . $summary['buying_centers'] . "</td><td class='amount'>" . number_format($summary['total_free_farmer_quantity'] + 0) . "</td><td class='amount'>" . number_format($cumulative_free_farmer_quantity + 0) . "</td><td class='amount'>" . number_format($summary['total_free_farmer_value'] + 0) . "</td><td class='amount'>" . number_format($cumulative_free_farmer_value + 0) . "</td><td class='amount'>" . number_format($summary['total_quantity'] + 0) . "</td><td class='amount'>" . number_format($cumulative_fbg_quantity + 0) . "</td><td class='amount'>" . number_format($summary['total_value'] + 0) . "</td><td class='amount'>" . number_format($cumulative_fbg_value + 0) . "</td><td class='amount'>" . number_format($summary['total_recoveries'] + 0) . "</td><td class='amount'>" . number_format($cumulative_recoveries + 0) . "</td></tr>";
			//$data_buffer .= $summary['date'] . "\t" . $summary['buying_centers'] . "\t" . $summary['total_free_farmer_quantity'] . "\t" . $cumulative_free_farmer_quantity . "\t" . $summary['total_free_farmer_value'] . "\t" . $cumulative_free_farmer_value . "\t" . $summary['total_quantity'] . "\t" . $cumulative_fbg_quantity . "\t" . $summary['total_value'] . "\t" . $cumulative_fbg_value . "\t" . $summary['total_recoveries'] . "\t" . $cumulative_recoveries . "\n";
		}
		$data_buffer .= "</table>";
		//echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Daily Purchases Summary Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date);
		//echo $data_buffer;
	}

	public function downloadExcel() {
		$this -> load -> database();
		$data_buffer = "";
		$data_buffer .= $this -> echoExcelTitles();
		//Get all the depots in this region
		$sql_purchase_summaries = "SELECT date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as date,sum(loan_recovery) as total_recoveries,sum(gross_value) as total_value,sum(free_farmer_value) as total_free_farmer_value,sum(quantity) as total_quantity,sum(free_farmer_quantity) as total_free_farmer_quantity,count(distinct dpn) as buying_centers FROM `purchase` where batch_status = '2' group by date order by str_to_date(date,'%m/%d/%Y') asc";
		$purchase_summaries = $this -> db -> query($sql_purchase_summaries);
		$cumulative_free_farmer_quantity = 0;
		$cumulative_free_farmer_value = 0;
		$cumulative_fbg_quantity = 0;
		$cumulative_fbg_value = 0;
		$cumulative_recoveries = 0;
		foreach ($purchase_summaries->result_array() as $summary) {
			$cumulative_free_farmer_quantity += $summary['total_free_farmer_quantity'];
			$cumulative_free_farmer_value += $summary['total_free_farmer_value'];
			$cumulative_fbg_quantity += $summary['total_quantity'];
			$cumulative_fbg_value += $summary['total_value'];
			$cumulative_recoveries += $summary['total_recoveries'];
			$data_buffer .= $summary['date'] . "\t" . $summary['buying_centers'] . "\t" . $summary['total_free_farmer_quantity'] . "\t" . $cumulative_free_farmer_quantity . "\t" . $summary['total_free_farmer_value'] . "\t" . $cumulative_free_farmer_value . "\t" . $summary['total_quantity'] . "\t" . $cumulative_fbg_quantity . "\t" . $summary['total_value'] . "\t" . $cumulative_fbg_value . "\t" . $summary['total_recoveries'] . "\t" . $cumulative_recoveries . "\n";
		}
		$data_buffer .= "\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Daily Purchases Summary.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Daily Purchases Summary Report Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<thead><tr><th>Date</th><th>Visited Buying Centers</th><th>Free Farmer Kgs.</th><th>Cumulative Free Farmer Kgs.</th><th>Free Farmer Tsh.</th><th>Cumulative Free Farmer Tsh.</th><th>FBG Purchases Kgs.</th><th>Cumulative FBG Kgs.</th><th>FBG Purchases Tsh.</th><th>Cumulative FBG Tsh.</th><th>Loan Recoveries (Tsh.)</th><th>Cumulative Recoveries (Tsh.)</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "Date\tVisited Buying Centers\tFree Farmer Kgs.\tCumulative Free Farmer Kgs.\tFree Farmer Tsh.\tCumulative Free Farmer Tsh.\tFBG Purchases Kgs.\tCumulative FBG Kgs.\tFBG Purchases Tsh.\tCumulative FBG Tsh.\tLoan Recoveries (Tsh.)\tCumulative Recoveries (Tsh.)\t\n";
	}

	function generatePDF($data, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Daily Purchases Summary</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('Daily Purchases Summary');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|{PAGENO}|Daily Purchases Summary Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Daily Purchases Summary.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
