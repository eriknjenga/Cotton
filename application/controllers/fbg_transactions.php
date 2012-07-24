<?php
class FBG_Transactions extends MY_Controller {
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
		$data['fbgs'] = FBG::getAll();
		$data['content_view'] = "fbg_transactions_v";
		$data['quick_link'] = "fbg_transactions";
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
			width: 100px;
			}
			</style>
			";
			$fbg = $this -> input -> post("fbg");
			$start_date = $this -> input -> post("start_date");
			$end_date = $this -> input -> post("end_date");
			$fbg_object = FBG::getFBG($fbg);
			//Get all the FBG input disbursements
			$sql_disbursements = "select * from disbursement d left join farm_input f on d.farm_input = f.id where fbg = '" . $fbg . "' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and batch_status = '2'";
			$query = $this -> db -> query($sql_disbursements);
			$fbg_disbursements = $query -> result_array();
			//echo the start of the table
			$data_buffer .= "<h3>FBG: " . $fbg_object -> Group_Name . "(" . $fbg_object -> Village_Object -> Name . ")</h3>";
			$data_buffer .= "<h4>Input Disbursements</h4>";
			$data_buffer .= "<table class='data-table'>";
			//$data_buffer .= $this -> echoTitles();
			$data_buffer .= "<tr><th>Transaction Date</th><th>Input</th><th>Invoice Number</th><th>Quantity</th><th>Unit Price</th><th>Total Value</th><th>Season</th></tr>";
			$total_loan_value = 0;
			foreach ($fbg_disbursements as $disbursement) {
				$total_loan_value += $disbursement['total_value'];
				$data_buffer .= "<tr><td>" . $disbursement['date'] . "</td><td>" . $disbursement['product_name'] . "</td><td>" . $disbursement['invoice_number'] . "</td><td>" . number_format($disbursement['quantity'] + 0) . "</td><td>" . (empty($disbursement['unit_price']) ? '-' : number_format($disbursement['unit_price'] + 0)) . "</td><td>" . number_format($disbursement['total_value'] + 0) . "</td><td>" . $disbursement['season'] . "</td></tr>";
			}
			$data_buffer .= "<tr><td><b>Totals</b></td><td>-</td><td>-</td><td>-</td><td>-</td><td>" . number_format($total_loan_value + 0) . "</td><td>-</td></tr>";
			$data_buffer .= "</table>";
			//Get all purchases from this fbg
			$sql_purchases = "select * from purchase p left join depot d on p.depot = d.id where p.fbg = '" . $fbg . "' and str_to_date(date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and batch_status = '2'";
			$query = $this -> db -> query($sql_purchases);
			$fbg_purchases = $query -> result_array();
			//echo the start of the table
			$data_buffer .= "<h4>Product Purchases</h4>";
			$data_buffer .= "<table class='data-table'>";
			//$data_buffer .= $this -> echoTitles();
			$data_buffer .= "<tr><th>BC Name</th><th>BC Code</th><th>Transaction Date</th><th>Purchases (Kgs)</th><th>Value(Tshs)</th><th>Total Recovered</th><th>Outstanding Balance</th></tr>";
			$total_value = 0;
			$total_kgs = 0;
			$total_recovered = 0;
			$outstanding_balance = $total_loan_value;
			foreach ($fbg_purchases as $purchase) {
				$total_value += $purchase['gross_value'];
				$total_kgs += $purchase['quantity'];
				$recoveries = ($purchase['loan_recovery'] + $purchase['farmer_reg_fee'] + $purchase['other_recoveries']);
				$total_recovered += $recoveries;
				$outstanding_balance -= $recoveries; 
				$data_buffer .= "<tr><td>" . $purchase['depot_name'] . "</td><td>" . $purchase['depot_code'] . "</td><td>" . $purchase['date'] . "</td><td>" . number_format($purchase['quantity'] + 0) . "</td><td>" . (empty($purchase['gross_value']) ? '-' : number_format($purchase['gross_value'] + 0)) . "</td><td>" . (empty($recoveries) ? '-' : number_format($recoveries + 0)) . "</td><td>" . number_format($outstanding_balance) . "</td></tr>";
			}
			$data_buffer .= "<tr><td><b>Totals:</b></td><td>-</td><td>-</td><td>" . number_format($total_kgs + 0) . "</td><td>" . number_format($total_value + 0) . "</td><td>" . number_format($total_recovered + 0) . "</td><td>-</td></tr>";
			$data_buffer .= "</table>";
			$data_buffer .= "<table>";
			$data_buffer .= "<tr><td><h3>Purchase Summary</h3></td><td><h3>Loan Summary</h3></td></tr>";
			$data_buffer .= "<tr><td><table class='data-table'>";
			$data_buffer .= "<tr><td><b>Total Purchases(Kgs.)</b></td><td>" . number_format($total_kgs) . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Value</b></td><td>" . number_format($total_value) . "</td></tr>";
			$data_buffer .= "</table></td>";
			$data_buffer .= "<td><table class='data-table'>";
			$data_buffer .= "<tr><td><b>Total Loaned</b></td><td>" . number_format($total_loan_value) . "</td></tr>";
			$data_buffer .= "<tr><td><b>Total Recovered</b></td><td>" . number_format($total_recovered) . "</td></tr>";
			$data_buffer .= "<tr></tr><tr><td><b>Outstanding Balance</b></td><td>" . number_format(($total_loan_value - $total_recovered)) . "</td></tr>";
			$data_buffer .= "</table></td></tr></table>";

//			echo $data_buffer;
			$log = new System_Log();
			$log -> Log_Type = "4";
			$log -> Log_Message = "Downloaded FBG Transactions PDF";
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			$this -> generatePDF($data_buffer, $start_date, $end_date);
		} else {
			$this -> view_transactions_interface();
		}

	}
 

	function generatePDF($data, $start_date, $end_date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>FBG Transactions</h3>";
		$html_title .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('FBG Transactions');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "FBG Transactions.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('fbg', 'FBG', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('end_date', 'End Date', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "FBG Management";
		$data['link'] = "home";

		$this -> load -> view("demo_template", $data);
	}

}
