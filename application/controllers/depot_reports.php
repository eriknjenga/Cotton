<?php
class Depot_Reports extends MY_Controller {
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
			$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			}
			table.data-table td {
			width: 70px;
			font-size:11;
			}
			table.data-table th {
			width: 70px;
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
			$depot_id = $this -> input -> post("depot");
			$start_date = $this -> input -> post("start_date");
			$end_date = $this -> input -> post("end_date");
			$depot = Depot::getDepot($depot_id);
			$sql = "select cih as document_number,'CIH' as document_type,date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as transaction_date,amount as cash_received,'' as dispatch,'' as purchase_value,'' as purchase_kg,'' as recoveries,'' as free_farmer_purchase_kg,'' as free_farmer_purchase_value,'' as cash_returned from field_cash_disbursement where depot = '" . $depot_id . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') union all select receipt_number as document_number,'Receipt No.' as document_type,date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as transaction_date,'','','','','','','',amount as cash_returned from buying_center_receipt where depot = '" . $depot_id . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') union all select ticket_number as document_number,'WB Ticket' as document_type, transaction_date, '', net_weight as dispatch,'','','','','','' from weighbridge w where w.buying_center_code = '" . $depot -> Depot_Code . "' and str_to_date(transaction_date,'%d/%m/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') union all select dpn as document_number,'DPN' as document_type, date_format(str_to_date(date,'%m/%d/%Y'),'%d/%m/%Y') as transaction_date,'','',p.gross_value as gross_value, p.quantity as quantity,loan_recovery as recoveries,p.free_farmer_quantity,p.free_farmer_value,'' from purchase p where p.depot = '" . $depot_id . "' and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by str_to_date(transaction_date,'%d/%m/%Y')  asc";
			$query = $this -> db -> query($sql);
			$depot_transactions = $query -> result_array();
			//echo the start of the table
			$data_buffer .= "<h3>Buying Center: " . $depot -> Depot_Name . " (" . $depot -> Depot_Code . ")</h3>";
			$data_buffer .= "<table class='data-table'>";
			$data_buffer .= $this -> echoTitles();
			$total_cash_received = 0;
			$total_purchases = 0;
			$total_purchased_weight = 0;
			$total_dispatched = 0;
			$total_recoveries = 0;
			$total_returned = 0;
			foreach ($depot_transactions as $transaction) {
				$total_cash_received += $transaction['cash_received'];
				$total_purchases += ($transaction['purchase_value'] + $transaction['free_farmer_purchase_value']);
				$total_purchased_weight += ($transaction['purchase_kg'] + $transaction['free_farmer_purchase_kg']);
				$total_dispatched += $transaction['dispatch'];
				$total_recoveries += $transaction['recoveries'];
				$total_returned += $transaction['cash_returned'];
				$data_buffer .= "<tr><td class='center'>" . $transaction['transaction_date'] . "</td><td class='center'>" . $transaction['document_type'] . "</td><td class='center'>" . $transaction['document_number'] . "</td><td class='amount'>" . (empty($transaction['cash_received']) ? '-' : number_format($transaction['cash_received'] + 0)) . "</td><td class='amount'>" . (empty($transaction['purchase_kg']) ? '-' : number_format($transaction['purchase_kg'] + 0)) . "</td><td class='amount'>" . (empty($transaction['purchase_value']) ? '-' : number_format($transaction['purchase_value'] + 0)) . "</td><td class='amount'>" . (empty($transaction['free_farmer_purchase_kg']) ? '-' : number_format($transaction['free_farmer_purchase_kg'] + 0)) . "</td><td class='amount'>" . (empty($transaction['free_farmer_purchase_value']) ? '-' : number_format($transaction['free_farmer_purchase_value'] + 0)) . "</td><td class='amount'>" . (empty($transaction['dispatch']) ? '-' : number_format($transaction['dispatch'] + 0)) . "</td><td class='amount'>" . (empty($transaction['recoveries']) ? '-' : number_format($transaction['recoveries'] + 0)) . "</td><td class='amount'>" . (empty($transaction['cash_returned']) ? '-' : number_format($transaction['cash_returned'] + 0)) . "</td></tr>";
			}
			$data_buffer .= "</table>";

			$data_buffer .= "<table>";
			$data_buffer .= "<tr><td><h3>Cash Summary</h3></td><td><h3>Weight Summary</h3></td></tr>";

			$data_buffer .= "<tr><td><table class='data-table'>";
			$data_buffer .= "<tr><td><b>Total Cash Received</b></td><td class='amount'>" . number_format($total_cash_received) . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Purchases</b></td><td class='amount'>" . number_format($total_purchases) . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Cash Returned</b></td><td class='amount'>" . number_format($total_returned) . "</td></tr>";
			$data_buffer .= "<tr></tr><tr><td><b>Balance</b></td><td class='amount'>" . number_format(($total_cash_received - $total_purchases - $total_returned)) . "</td></tr>";
			$data_buffer .= "<tr></tr><tr><td><b>Total Loan Recoveries</b></td><td class='amount'>" . number_format($total_recoveries) . "</td></tr>";
			$data_buffer .= "</table></td>";
			$data_buffer .= "<td><table class='data-table'>";
			$data_buffer .= "<tr><td><b>Total Purchases</b></td><td class='amount'>" . number_format($total_purchased_weight) . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Dispatches</b></td><td class='amount'>" . number_format($total_dispatched) . "</td></tr>";
			$data_buffer .= "<tr></tr><tr><td><b>Balance</b></td><td class='amount'>" . number_format(($total_purchased_weight - $total_dispatched)) . "</td></tr>";
			$data_buffer .= "</table></td></tr></table>";
			$sql_fbg_purchases = "SELECT fbg, group_name,cpc_number, sum(quantity+free_farmer_quantity) as total_quantity,sum(gross_value+free_farmer_value) as total_value, sum(loan_recovery) as total_recoveries FROM `purchase` p left join fbg f on p.fbg = f.id  where depot = '" . $depot_id . "' and batch_status = '2' group by fbg";
			$query = $this -> db -> query($sql_fbg_purchases);
			$fbg_transactions = $query -> result_array();
			$total_quantity = 0;
			$total_value = 0;
			$total_recoveries = 0;
			$data_buffer .= "<tr><td><h3>Farmer Summaries</h3></td></tr>";
			$data_buffer .= "<table class='data-table'><tr><th>FBG Name</th><th>Group Number</th><th>Total Quantity Brought</th><th>Total Value Brought</th><th>Total Loan Recovered</th></tr>";
			foreach ($fbg_transactions as $fbg_transaction) {
				$total_quantity += $fbg_transaction['total_quantity'];
				$total_value += $fbg_transaction['total_value'];
				$total_recoveries += $fbg_transaction['total_recoveries'];
				$data_buffer .= "<tr><td>" . (empty($fbg_transaction['group_name']) ? '-' : $fbg_transaction['group_name']) . "</td><td class='center'>" . (empty($fbg_transaction['cpc_number']) ? '-' : $fbg_transaction['cpc_number']) . "</td><td class='amount'>" . (empty($fbg_transaction['total_quantity']) ? '-' : number_format($fbg_transaction['total_quantity'] + 0)) . "</td><td class='amount'>" . (empty($fbg_transaction['total_value']) ? '-' : number_format($fbg_transaction['total_value'] + 0)) . "</td><td class='amount'>" . (empty($fbg_transaction['total_recoveries']) ? '-' : number_format($fbg_transaction['total_recoveries'] + 0)) . "</td></tr>";
			}
			$data_buffer .= "<tr><td><b>Totals:</b></td><td class='center'>-</td><td class='amount'>" . number_format($total_quantity + 0) . "</td><td class='amount'>" . number_format($total_value + 0) . "</td><td class='amount'>" . number_format($total_recoveries + 0) . "</td></tr>";
			$data_buffer .= "</table>";
			//echo $data_buffer;
			$log = new System_Log();
			$log -> Log_Type = "4";
			$log -> Log_Message = "Downloaded Buying Center Transactions PDF";
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			//echo $data_buffer;
			$log -> save();
			$this -> generatePDF($data_buffer, $start_date, $end_date);
		} else {
			$this -> view_transactions_interface();
		}

	}

	public function echoTitles() {
		return "<thead><tr><th>Transaction Date</th><th>Doc. Type</th><th>Doc. Number</th><th>Cash Received</th><th>FBG Purchases (Kgs.)</th><th>FBG Purchases (Tsh.)</th><th>Free-Farmer Purchases (Kgs.)</th><th>Free-Farmer Purchases (Tsh.)</th><th>Dispatch (Kgs.)</th><th>Loan Recoveries (Tsh.)</th><th>Cash Returned</th></tr></thead>";
	}

	function generatePDF($data, $start_date, $end_date) {
		$start_date = date('d/m/Y', strtotime($start_date));
		$end_date = date('d/m/Y', strtotime($end_date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Buying Center Transactions</h3>";
		$html_title .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Depot Transactions');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|{PAGENO}|Buying Center Transactions Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
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
