<?php
class Inputs_Disbursement_Report extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> inputs_disbursement();
	}

	public function inputs_disbursement($data = null) {
		$this -> load -> database();
		$sql = "select distinct season from disbursement where batch_status = '2'";
		$query = $this -> db -> query($sql);

		if ($data == null) {
			$data = array();
		}
		$data['inputs'] = Farm_Input::getAll();
		$data['seasons'] = $query -> result_array();
		$data['content_view'] = "inputs_disbursement_report_v";
		$data['quick_link'] = "inputs_disbursement";
		$this -> base_params($data);
	}

	public function download_inputs_disbursement() {
		$inputs = array();
		$input = $this -> input -> post("input");
		$action = $this -> input -> post("action");
		$season = $this -> input -> post("season");
		$date = date("m/d/Y");

		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Inputs Disbursement Excel") {
			$this -> download_disbursement_excel($input, $season);
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
			width: 70px;
			}
			</style>
			";
		$total_debt = 0;
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$data_buffer .= $this -> echoTitles();
		if ($input == 0) {
			$sql = "SELECT d.quantity,d.date,f.hectares_available,f.cpc_number,f.group_name,f.chairman_name,d.invoice_number,v.name as village,r.region_name,i.product_name,d.unit_price,d.total_value FROM `disbursement` d left join fbg f on d.fbg = f.id left join farm_input i on d.farm_input = i.id left join village v on v.id = f.village left join ward w on v.ward = w.id left join region r on w.region = r.id where d.season = '" . $season . "' and d.batch_status = '2' order by f.id asc";
		} else {
			$sql = "SELECT d.quantity,d.date,f.hectares_available,f.cpc_number,f.group_name,f.chairman_name,d.invoice_number,v.name as village,r.region_name,i.product_name,d.unit_price,d.total_value FROM `disbursement` d left join fbg f on d.fbg = f.id left join farm_input i on d.farm_input = i.id left join village v on v.id = f.village left join ward w on v.ward = w.id left join region r on w.region = r.id where d.season = '" . $season . "' and d.batch_status = '2' and d.farm_input = '" . $input . "' order by f.id asc";
		}
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $fbg_data) {
			$data_buffer .= "<tr><td>" . $fbg_data['date'] . "</td><td>" . $fbg_data['cpc_number'] . "</td><td>" . $fbg_data['group_name'] . "</td><td>" . $fbg_data['chairman_name'] . "</td><td>" . $fbg_data['village'] . "</td><td>" . $fbg_data['region_name'] . "</td><td>" . number_format($fbg_data['hectares_available'] + 0) . "</td><td>" . $fbg_data['product_name'] . "</td><td>" . $fbg_data['invoice_number'] . "</td><td>" . number_format($fbg_data['quantity'] + 0) . "</td><td>" . number_format($fbg_data['unit_price'] + 0) . "</td><td>" . number_format($fbg_data['total_value'] + 0) . "</td></tr>";
			$total_debt += $fbg_data['total_value'];
		}
		$data_buffer .= "<tr><td>Totals</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>" . number_format($total_debt + 0) . "</td></tr>";
		$data_buffer .= "</table>";
		//echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Detailed Inputs Disbursement PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date, $season);

	}

	public function download_disbursement_excel($input, $season) {
		$this -> load -> database();
		$data_buffer = "";
		$total_debt = 0;
		$data_buffer .= $this -> echo_excel_titles();
		if ($input == 0) {
			$sql = "SELECT d.quantity,d.date,f.hectares_available,f.cpc_number,f.group_name,f.chairman_name,d.invoice_number,v.name as village,r.region_name,i.product_name,d.unit_price,d.total_value FROM `disbursement` d left join fbg f on d.fbg = f.id left join farm_input i on d.farm_input = i.id left join village v on v.id = f.village left join ward w on v.ward = w.id left join region r on w.region = r.id where d.season = '" . $season . "' and d.batch_status = '2' order by f.id asc";
		} else {
			$sql = "SELECT d.quantity,d.date,f.hectares_available,f.cpc_number,f.group_name,f.chairman_name,d.invoice_number,v.name as village,r.region_name,i.product_name,d.unit_price,d.total_value FROM `disbursement` d left join fbg f on d.fbg = f.id left join farm_input i on d.farm_input = i.id left join village v on v.id = f.village left join ward w on v.ward = w.id left join region r on w.region = r.id where d.season = '" . $season . "' and d.batch_status = '2' and d.farm_input = '" . $input . "' order by f.id asc";
		}
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $fbg_data) {
			$data_buffer .= $fbg_data['date'] . "\t" . $fbg_data['cpc_number'] . "\t" . $fbg_data['group_name'] . "\t" . $fbg_data['chairman_name'] . "\t" . $fbg_data['village'] . "\t" . $fbg_data['region_name'] . "\t" . $fbg_data['hectares_available'] . "\t" . $fbg_data['product_name'] . "\t" . $fbg_data['invoice_number'] . "\t" . $fbg_data['quantity'] . "\t" . $fbg_data['unit_price'] . "\t" . $fbg_data['total_value'] . "\t\n";
			$total_debt += $fbg_data['total_value'];
		}
		$data_buffer .= "Totals\t-\t-\t-\t-\t-\t-\t-\t-\t-\t-\t" . $total_debt . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Detailed Inputs Disbursement.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Detailed Inputs Disbursement Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		echo $data_buffer;
	}

	public function echoTitles() {
		return "<tr><th>Date</th><th>FBG No.</th><th>Group Name</th><th>Chairman</th><th>Village</th><th>Zone</th><th>Acreage</th><th>Input</th><th>Invoice</th><th>Quantity</th><th>Unit Price</th><th>Total Value</th></tr>";
	}

	public function echo_excel_titles() {
		return "Date\tFBG No.\tGroup Name\tChairman\tVillage\tZone\tAcreage\tInput\tInvoice\tQuantity\tUnit Price\tTotal Value\t\n";
	}

	function generatePDF($data, $date, $season) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Input Disbursements Report</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . " for the " . $season . " season</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('Detailed Inputs Disbursement');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Detailed Inputs Disbursement.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
