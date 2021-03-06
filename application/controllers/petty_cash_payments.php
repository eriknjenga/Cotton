<?php
class Petty_Cash_Payments extends MY_Controller {
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
		$data['content_view'] = "petty_cash_payments_v";
		$data['quick_link'] = "petty_cash_payments";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$action = $this -> input -> post("action");
		$date = $this -> input -> post("date");
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Petty Cash Payments Excel") {
			$this -> downloadExcel($date);
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
		$sql = "SELECT cih,field_cashier_name,date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as date,amount,'Centre Distribution' as paid_for FROM `cash_disbursement` c left join field_cashier f on c.field_cashier = f.id where str_to_date(date,'%m/%d/%Y') = str_to_date('" . $date . "','%m/%d/%Y')";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= "<tr><td class='center'>" . $depot_data['cih'] . "</td><td class='center'>" . $depot_data['date'] . "</td><td class='center'>" . $depot_data['field_cashier_name'] . "</td><td class='amount'>" . number_format($depot_data['amount'] + 0) . "</td><td class='center'>" . $depot_data['paid_for'] . "</td></tr>";
			$total_amount += $depot_data['amount'];
		}
		$data_buffer .= "<tr></tr><tr><td>Total Amount</td><td class='center'>-</td><td class='center'>-</td><td class='amount'>" . number_format($total_amount + 0) . "</td><td class='center'>-</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded CIH Payments to FC Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $date);

	}

	public function downloadExcel($date) {
		$this -> load -> database();
		$data_buffer = "";
		$total_amount = 0;
		$data_buffer .= $this -> echoExcelTitles();
		//Get data for each zone
		$sql = "SELECT cih,field_cashier_name,date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as date,amount,'Centre Distribution' as paid_for FROM `cash_disbursement` c left join field_cashier f on c.field_cashier = f.id where str_to_date(date,'%m/%d/%Y') = str_to_date('" . $date . "','%m/%d/%Y')";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= $depot_data['cih'] . "\t" . $depot_data['date'] . "\t" . $depot_data['field_cashier_name'] . "\t" . $depot_data['amount'] . "\t" . $depot_data['paid_for'] . "\t\n";
			$total_amount += $depot_data['amount'];
		}
		$data_buffer .= "\nTotal Amount\t-\t-\t" . $total_amount . "\t-\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=CIH Payments to FC.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Petty Cash Payments Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<thead><tr><th>CIH Number</th><th>Date</th><th>Field Cashier</th><th>Amount</th><th>Paid For</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "CIH Number\tDate\tField Cashier\tAmount\tPaid For\t\n";
	}

	function generatePDF($data, $date) {
		$date = date('d/m/Y', strtotime($date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>CIH Payments to FC</h3>";
		$html_title .= "<h5 style='text-align:center;'> For " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> shrink_tables_to_fit = 1;
		$this -> mpdf -> SetTitle('CIH Payments to FC');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|-{PAGENO}-|Petty Cash Payments Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "CIH Payments to FC.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
