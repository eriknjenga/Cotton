<?php
class Field_Cashier_Summaries extends MY_Controller {
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
		$data['content_view'] = "field_cashier_summaries_v";
		$data['quick_link'] = "field_cashier_summaries";
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$action = $this -> input -> post("action");

		$date = date("m/d/Y");
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Field Cashier Summary Excel") {
			$this -> downloadExcel();
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
		$total_cash_received = 0;
		$total_cash_paid = 0;
		$total_balance = 0;
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$data_buffer .= $this -> echoTitles();
		//Get all the field cashiers
		$sql_cashiers = "SELECT id,field_cashier_name FROM `field_cashier`";
		$cashiers = $this -> db -> query($sql_cashiers);
		//Get data for each depot
		foreach ($cashiers->result_array() as $cashier) {
			$field_cashier = $cashier['id'];
			$sql = "select balances.*,total_received - (total_paid+total_returned) as balance from (select (select coalesce(sum(amount),0) from cash_disbursement where field_cashier = '$field_cashier' and batch_status = '2') as total_received,(select coalesce(sum(amount),0) from field_cash_disbursement where field_cashier = '$field_cashier' and batch_status = '2') as total_paid,(select coalesce(sum(amount),0) from cash_receipt where field_cashier = '$field_cashier' and batch_status = '2' ) as total_returned) balances";
			$query = $this -> db -> query($sql);
			$summary_data = $query -> row_array();
			$data_buffer .= "<tr><td>" . $cashier['field_cashier_name'] . "</td><td class='amount'>" . number_format($summary_data['total_received'] + 0) . "</td><td  class='amount'>" . number_format(($summary_data['total_paid'] + $summary_data['total_returned']) + 0) . "</td><td class='amount'>" . number_format($summary_data['balance'] + 0) . "</td></tr>";
			$total_cash_received += $summary_data['total_received'];
			$total_cash_paid += ($summary_data['total_paid'] + $summary_data['total_returned']);
			$total_balance += $summary_data['balance'];
		}
		$data_buffer .= "<tr></tr><tr><td>Totals: </td><td class='amount'>" . number_format($total_cash_received + 0) . "</td><td class='amount'>" . number_format($total_cash_paid + 0) . "</td><td class='amount'>" . number_format($total_balance + 0) . "</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Field Cashier Summaries Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date);

	}

	public function downloadExcel() {
		$this -> load -> database();
		$data_buffer = "";
		$total_cash_received = 0;
		$total_cash_paid = 0;
		$total_balance = 0;
		//echo the start of the table
		$data_buffer .= $this -> echoExcelTitles();
		//Get all the field cashiers
		$sql_cashiers = "SELECT id,field_cashier_name FROM `field_cashier`";
		$cashiers = $this -> db -> query($sql_cashiers);
		//Get data for each depot
		foreach ($cashiers->result_array() as $cashier) {
			$field_cashier = $cashier['id'];
			$sql = "select balances.*,total_received - (total_paid+total_returned) as balance from (select (select coalesce(sum(amount),0) from cash_disbursement where field_cashier = '$field_cashier' and batch_status = '2') as total_received,(select coalesce(sum(amount),0) from field_cash_disbursement where field_cashier = '$field_cashier' and batch_status = '2') as total_paid,(select coalesce(sum(amount),0) from cash_receipt where field_cashier = '$field_cashier' and batch_status = '2' ) as total_returned) balances";
			$query = $this -> db -> query($sql);
			$summary_data = $query -> row_array();
			$data_buffer .= $cashier['field_cashier_name'] . "\t" . $summary_data['total_received'] . "\t" . ($summary_data['total_paid'] + $summary_data['total_returned']) . "\t" . $summary_data['balance'] . "\t\n";
			$total_cash_received += $summary_data['total_received'];
			$total_cash_paid += ($summary_data['total_paid'] + $summary_data['total_returned']);
			$total_balance += $summary_data['balance'];
		}
		$data_buffer .= "\nTotals: \t" . $total_cash_received . "\t" . $total_cash_paid . "\t" . $total_balance . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Field Cashier Summaries.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Field Cashier Summaries Report Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<thead><tr><th>Field Cashier</th><th>Cash Received</th><th>Cash Paid</th><th>Balance</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "Field Cashier\tCash Received\tCash Paid\tBalance\t\n";
	}

	function generatePDF($data, $date) {
		$date = date('d/m/Y', strtotime($date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Field Cashier Summaries</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Field Cashier Summaries');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|{PAGENO}|Field Cashier Summaries Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Field Cashier Summaries.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
