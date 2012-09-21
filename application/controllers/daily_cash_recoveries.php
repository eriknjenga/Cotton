<?php
class Daily_Cash_Recoveries extends MY_Controller {
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
		$data['content_view'] = "daily_cash_recoveries_v";
		$data['quick_link'] = "daily_cash_recoveries";
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
		if ($action == "Download Daily Cash Recoveries Excel") {
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
		$total_amount = 0;
		$data_buffer .= $this -> echoTitles();
		//Get data for each zone
		$sql = "select receipt_number, date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as date, fbg, amount, received_from, group_name, cpc_number,adjustment from loan_recovery_receipt l left join fbg f on l.fbg = f.id and l.batch_status = '2' where str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by str_to_date(date,'%m/%d/%Y') asc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $fbg_data) {
			$data_buffer .= "<tr><td>" . $fbg_data['group_name'] . "</td><td class='center'>" . (empty($fbg_data['cpc_number']) ? '-' : $fbg_data['cpc_number']) . "</td><td class='center'>" . $fbg_data['date'] . "</td><td class='center'>" . $fbg_data['receipt_number'] . "</td><td class='amount'>" . number_format($fbg_data['amount'] + 0) . "</td><td class='center'>" . (empty($fbg_data['received_from']) ? '-' : $fbg_data['received_from']) . "</td></tr>";
			$total_amount += $fbg_data['amount'];
		}
		$data_buffer .= "<tr><td class='center'><b>Total Recoveries</b></td><td class='center'>-</td><td class='center'>-</td><td class='center'>-</td><td class='amount'>" . number_format($total_amount + 0) . "</td><td class='center'>-</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Daily Cash Recoveries Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $start_date, $end_date);

	}

	public function downloadExcel($start_date, $end_date) {
		$this -> load -> database();
		$data_buffer = "";
		$total_amount = 0; 
		$data_buffer .= $this -> echoExcelTitles();
		//Get data for each zone
		$sql = "select receipt_number, date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as date, fbg, amount, received_from, group_name, cpc_number,adjustment from loan_recovery_receipt l left join fbg f on l.fbg = f.id and l.batch_status = '2' where str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by str_to_date(date,'%m/%d/%Y') asc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $fbg_data) {
			$data_buffer .= $fbg_data['group_name'] . "\t" . $fbg_data['cpc_number']. "\t" . $fbg_data['date'] . "\t" . $fbg_data['receipt_number'] . "\t" . $fbg_data['amount'] . "\t" . $fbg_data['received_from'] ."\n";
			$total_amount += $fbg_data['amount']; 
		}
		$data_buffer .= "\nTotals: \t-\t-\t-\t" . $total_amount . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Daily Cash Recoveries.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Daily Cash Recoveries Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<thead><tr><th>Group Name</th><th>Group Number</th><th>Transaction Date</th><th>Receipt Number</th><th>Amount</th><th>Received From</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "Group Name\tGroup Number\tTransaction Date\tReceipt Number\tAmount\tReceived From\t\n";
	}

	function generatePDF($data, $start_date, $end_date) {
		$start_date = date('d/m/Y', strtotime($start_date));
		$end_date = date('d/m/Y', strtotime($end_date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Daily Cash Recoveries</h3>";
		$html_title .= "<h5 style='text-align:center;'> From " . $start_date . " to " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Daily Cash Recoveries');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|-{PAGENO}-|Daily Cash Recoveries Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Daily Cash Recoveries.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
