<?php
class Dormant_Buying_Centers extends MY_Controller {
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
		$data['content_view'] = "dormant_buying_centers_v";
		$data['quick_link'] = "dormant_buying_centers";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$action = $this -> input -> post("action");
		$days = $this -> input -> post("days");
		$date = date("m/d/Y");
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Dormant Buying Centers Excel") {
			$this -> downloadExcel($days);
			return;
		}
		$this -> load -> database();
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			min-width: 1000px;
			}
			table.data-table td {
			width: 120px;
			}
			</style>
			";
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$total_purchased = 0;
		$total_dispatched = 0;
		$total_balance = 0;
		$data_buffer .= $this -> echoTitles();
		//Get data for each zone
		$sql = "select depot_summary.*, (coalesce(total_purchases,0)-coalesce(total_dispatched,0)) as product_balance,datediff(str_to_date('" . $date . "','%m/%d/%Y') , str_to_date(last_purchase,'%m/%d/%Y')) as days_dormant from(select d.id,depot_name,v.name as village,w.name as ward,r.region_name as zone,(select date from purchase p where depot = d.id order by date desc limit 1) last_purchase,(select sum(quantity) from purchase p where depot = d.id) total_purchases,(select sum(net_weight) from weighbridge w where buying_center_code = d.depot_code and weighing_type = '2') total_dispatched from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id  where d.deleted = '0') depot_summary having days_dormant>='" . $days . "' order by days_dormant desc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td>" . $depot_data['village'] . "</td><td>" . $depot_data['ward'] . "</td><td>" . $depot_data['zone'] . "</td><td>" . $depot_data['last_purchase'] . "</td><td>" . number_format($depot_data['total_purchases']+0) . "</td><td>" . number_format($depot_data['total_dispatched']+0) . "</td><td>" . number_format($depot_data['product_balance']+0) . "</td><td>" . $depot_data['days_dormant'] . "</td></tr>";
			$total_purchased += $depot_data['total_purchases'];
			$total_dispatched += $depot_data['total_dispatched'];
			$total_balance += $depot_data['product_balance'];
		}
		$data_buffer .= "<tr></tr><tr><td>Totals:</td><td>-</td><td>-</td><td>-</td><td>-</td><td>" . number_format($total_purchased+0) . "</td><td>" . number_format($total_dispatched+0) . "</td><td>" . number_format($total_balance+0) . "</td><td>-</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Dormant Buying Centers Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date, $days);

	}

	public function downloadExcel($days) {
		$this -> load -> database();
		$data_buffer = "";
		$total_purchased = 0;
		$total_dispatched = 0;
		$total_balance = 0;
		$date = date("m/d/Y");
		$data_buffer .= $this -> echoExcelTitles();
		//Get data for each zone
		$sql = "select depot_summary.*, (coalesce(total_purchases,0)-coalesce(total_dispatched,0)) as product_balance,datediff(str_to_date('" . $date . "','%m/%d/%Y') , str_to_date(last_purchase,'%m/%d/%Y')) as days_dormant from(select d.id,depot_name,v.name as village,w.name as ward,r.region_name as zone,(select date from purchase p where depot = d.id order by date desc limit 1) last_purchase,(select sum(quantity) from purchase p where depot = d.id) total_purchases,(select sum(net_weight) from weighbridge w where buying_center_code = d.depot_code and weighing_type = '2') total_dispatched from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id  where d.deleted = '0') depot_summary having days_dormant>='" . $days . "' order by days_dormant desc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['village'] . "\t" . $depot_data['ward'] . "\t" . $depot_data['zone'] . "\t" . $depot_data['last_purchase'] . "\t" . $depot_data['total_purchases'] . "\t" . $depot_data['total_dispatched'] . "\t" . $depot_data['product_balance'] . "\t" . $depot_data['days_dormant'] . "\t\n";
			$total_purchased += $depot_data['total_purchases'];
			$total_dispatched += $depot_data['total_dispatched'];
			$total_balance += $depot_data['product_balance'];
		}
		$data_buffer .= "\nTotals:\t-\t-\t-\t-\t" . $total_purchased . "\t" . $total_dispatched . "\t" . $total_balance . "\t-\t";
		$data_buffer .= "\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Dormant Buying Centers.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Dormant Buying Centers Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<tr><th>Buying Center</th><th>Village</th><th>Ward</th><th>Zone</th><th>Last Purchase Date</th><th>Total Purchases</th><th>Total Dispatched</th><th>Product Balance</th><th>Days Dormant</th></tr>";
	}

	public function echoExcelTitles() {
		return "Buying Center\tVillage\tWard\tZone\tLast Purchase Date\tTotal Purchases\tTotal Dispatched\tProduct Balance\tDays Dormant\t\n";
	}

	function generatePDF($data, $date, $days) {

		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Dormant Buying Centers</h3>";
		$html_title .= "<h3 style='text-align:center;'> Dormant for " . $days . " days As of: " . $date . "</h3>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('Zonal Summaries');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Dormant Buying Centers.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
