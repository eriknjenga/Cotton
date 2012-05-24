<?php
class Depot_Reports extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_interface();
	}

	public function view_transactions_interface($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['depots'] = Depot::getAll();
		$data['content_view'] = "depot_transactions_v";
		$data['quick_link'] = "depot_transactions";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$this -> load -> database();

		$valid = $this -> validate_form();
		if ($valid) {
			$data_buffer = "";
			$depot_id = $this -> input -> post("depot");
			$start_date = $this -> input -> post("start_date");
			$end_date = $this -> input -> post("end_date");
			$depot = Depot::getDepot($depot_id);
			$sql = "SELECT voucher_number as document_number,str_to_date(date,'%m/%d/%Y') as transaction_date,amount as cash_paid, '' as cash_received,'' as dispatch,'' as purchase_value,'' as purchase_kg FROM mopping_payment where depot = '" . $depot_id . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y')  union all (select cih as document_number,str_to_date(date,'%m/%d/%Y') as transaction_date,'',amount as cash_received,'','','' from field_cash_disbursement where depot = '" . $depot_id . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y')) union all select  ticket_number as document_number, str_to_date(transaction_date,'%d/%m/%Y'), '', '', net_weight as dispatch,'' ,'' from weighbridge w where w.buying_center_code = (select depot_code from depot where id = '" . $depot_id . "' and str_to_date(transaction_date,'%d/%m/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y')) union all select dpn as document_number, str_to_date(date,'%m/%d/%Y') as transaction_date,'','','',p.gross_value, p.quantity from purchase p where p.depot = '" . $depot_id . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by transaction_date asc";
			$query = $this -> db -> query($sql);
			$depot_transactions = $query -> result_array();
			//echo the start of the table
			$data_buffer.="<h3>Buying Center: ".$depot->Depot_Name."</h3>";
			$data_buffer .= "<table class='data-table'>";
			$data_buffer .= $this -> echoTitles();
			$total_cash_received = 0;
			$total_cash_paid = 0;
			$total_purchases = 0;
			$total_purchased_weight = 0;
			$total_dispatched = 0;
			foreach ($depot_transactions as $transaction) {
				$total_cash_received += $transaction['cash_received'];
				$total_cash_paid += $transaction['cash_paid'];
				$total_purchases += $transaction['purchase_value'];
				$total_purchased_weight += $transaction['purchase_kg'];
				$total_dispatched += $transaction['dispatch'];
				$data_buffer .= "<tr><td>" . $transaction['transaction_date'] . "</td><td>" . $transaction['document_number'] . "</td><td>" . $transaction['cash_received'] . "</td><td>" . $transaction['cash_paid'] . "</td><td>" . $transaction['purchase_value'] . "</td><td>" . $transaction['purchase_kg'] . "</td><td>" . $transaction['dispatch'] . "</td></tr>";
			}
			$data_buffer .= "</table>";
			$data_buffer .= "<h1>Cash Summary</h1><table class='data-table'>";
			$data_buffer .= "<tr><td><b>Total Cash Received</b></td><td>" . $total_cash_received . "</td></tr>";
			$data_buffer .= "<tr><td><b>Other Payments</b></td><td>" . $total_cash_paid . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Purchases</b></td><td>" . $total_purchases . "</td></tr>";
			$data_buffer .= "<tr></tr><tr><td><b>Balance</b></td><td>" . ($total_cash_received - $total_cash_paid - $total_purchases) . "</td></tr>";
			$data_buffer .= "</table>";

			$data_buffer .= "</table>";
			$data_buffer .= "<h1>Weight Summary</h1><table class='data-table'>";
			$data_buffer .= "<tr><td><b>Total Purchases</b></td><td>" . $total_purchased_weight . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Dispatches</b></td><td>" . $total_dispatched . "</td></tr>"; 
			$data_buffer .= "<tr></tr><tr><td><b>Balance</b></td><td>" . ($total_purchased_weight - $total_dispatched) . "</td></tr>";
			$data_buffer .= "</table>";
			//echo $data_buffer;
			$this -> generatePDF($data_buffer, $start_date,$end_date);
		} else {
			$this -> view_transactions_interface();
		}

	}

	public function echoTitles() {
		return "<tr><th>Transaction Date</th><th>Doc. Number</th><th>Cash Received</th><th>Cash Paid</th><th>Purchases (Tsh.)</th><th>Purchases (Kgs.)</th><th>Dispatch (Kgs.)</th></tr>";
	}

	function generatePDF($data, $start_date,$end_date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h2 style='text-align:center; text-decoration:underline;'>Alliance Ginneries</h2>";

		$html_title .= "<h1 style='text-align:center; text-decoration:underline;'>Buying Center Transactions</h1>";
		$html_title .= "<h3 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h3>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('Depot Transactions');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Buying Center Transactions.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('depot', 'Depot', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('end_date', 'End Date', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "home";

		$this -> load -> view("demo_template", $data);
	}

}
