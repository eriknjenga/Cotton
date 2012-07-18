<?php
class Field_Cashier_Transactions extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_transactions_interface();
	}

	public function view_transactions_interface($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['field_cashiers'] = Field_Cashier::getAll();
		$data['content_view'] = "field_cashier_transactions_v";
		$data['quick_link'] = "field_cashier_transactions";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$this -> load -> database();

		$valid = true;
		if ($valid) {
			$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			}
			table.data-table td {
			width: 100px;
			}
			</style>
			";
			$field_cashier = $this -> input -> post("field_cashier");
			$start_date = $this -> input -> post("start_date");
			$end_date = $this -> input -> post("end_date");
			$cashier_details = Field_Cashier::getFieldCashier($field_cashier);
			$sql = "SELECT cih as document_number , str_to_date(date,'%m/%d/%Y') as transaction_date, amount as cash_received, '' as cash_paid, 'Center Distribution' as message,'' as bcr FROM cash_disbursement where field_cashier  = '" . $field_cashier . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y')  union all (select cih as document_number,str_to_date(date,'%m/%d/%Y') as transaction_date,'',amount as cash_paid,concat('Cash Paid To ',d.depot_name) as message,receipt as bcr from field_cash_disbursement f left join depot d on f.depot = d.id where field_cashier = '" . $field_cashier . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y'))  union all (select receipt_number as document_number,str_to_date(date,'%m/%d/%Y') as transaction_date,'',amount as cash_paid,'Cash Return' as message,'' as bcr from cash_receipt where field_cashier = '" . $field_cashier . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y'))  order by transaction_date asc";
			$balance_sql = "select total_received - (total_paid+total_returned) as balance from (select (select sum(amount) from cash_disbursement where field_cashier = '$field_cashier' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') < str_to_date('" . $start_date . "','%m/%d/%Y')) as total_received,(select sum(amount) from field_cash_disbursement where field_cashier = '$field_cashier' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') < str_to_date ('" . $start_date . "','%m/%d/%Y')) as total_paid,(select sum(amount) from cash_receipt where field_cashier = '$field_cashier' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') < str_to_date ('" . $start_date . "','%m/%d/%Y')) as total_returned) balances";
			$balance_query = $this -> db -> query($balance_sql);
			$cashier_balance = $balance_query -> result_array();
			$transactions_query = $this -> db -> query($sql);
			$cashier_transactions = $transactions_query -> result_array();
			$total_cash_received = 0;
			$total_cash_paid = 0;
			$balance = $cashier_balance[0]['balance'] + 0;
			//echo the start of the table
			$data_buffer .= "<h5>Field Cashier: " . $cashier_details -> Field_Cashier_Name . "</h5>";
			$data_buffer .= "<h5>Balance at start of period: " . $balance . "</h5>";
			$data_buffer .= "<table class='data-table'>";
			$data_buffer .= $this -> echoTitles();

			foreach ($cashier_transactions as $transaction) {
				$total_cash_received += $transaction['cash_received'];
				$total_cash_paid += $transaction['cash_paid'];
				$balance += $transaction['cash_received'];
				$balance -= $transaction['cash_paid'];
				$data_buffer .= "<tr><td>" . $transaction['transaction_date'] . "</td><td>" . $transaction['document_number'] . "</td><td>" . $transaction['bcr'] . "</td><td>" . $transaction['message'] . "</td><td>" . number_format($transaction['cash_received'] + 0) . "</td><td>" . number_format($transaction['cash_paid'] + 0) . "</td><td>" . number_format($balance + 0) . "</td></tr>";
			}
			$data_buffer .= "</table>";
			$data_buffer .= "<h3>Cash Summary</h3><table class='data-table'>";
			$data_buffer .= "<tr><td><b>Total Cash Received</b></td><td>" . number_format($total_cash_received) . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Cash Distributed/Returned</b></td><td>" . number_format($total_cash_paid) . "</td></tr>";
			$data_buffer .= "<tr></tr><tr><td><b>Balance</b></td><td>" . number_format($balance) . "</td></tr>";
			$data_buffer .= "</table>";
			//echo $data_buffer;
			$log = new System_Log();
			$log -> Log_Type = "4";
			$log -> Log_Message = "Downloaded Buying Center Transactions PDF";
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			$this -> generatePDF($data_buffer, $start_date, $end_date);
		} else {
			$this -> view_transactions_interface();
		}

	}

	public function echoTitles() {
		return "<tr><th>Transaction Date</th><th>Doc. Number</th><th>BCR</th><th>Details</th><th>Cash Received</th><th>Cash Paid</th><th>Balance</th></tr>";
	}

	function generatePDF($data, $start_date, $end_date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Field Cashier Transactions</h3>";
		$html_title .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Field Cashier Transactions');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Field Cashier Transactions.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('field_cashier', 'Field Cashier', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('end_date', 'End Date', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Field Cashier Management";
		$data['link'] = "home";

		$this -> load -> view("demo_template", $data);
	}

}
