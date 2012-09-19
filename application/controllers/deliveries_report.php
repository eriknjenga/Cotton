<?php
class Deliveries_Report extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> deliveries_report();
	}

	public function deliveries_report($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['regions'] = Region::getAll();
		$data['content_view'] = "deliveries_report_v";
		$data['quick_link'] = "deliveries_report";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download_deliveries_report() {
		$regions = array();
		$region = $this -> input -> post("region");
		$start_date = $this -> input -> post("start_date");
		$end_date = $this -> input -> post("end_date");
		$action = $this -> input -> post("action");

		$date = date("m/d/Y");
		if ($region == 0) {
			//Get the region
			$regions = Region::getAll();
		} else {
			$regions = Region::getRegionArray($region);
		}
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Deliveries Report Excel") {
			$this -> download_deliveries_excel($regions, $start_date, $end_date);
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
		$total_delivered = 0;
		$total_gross = 0;
		$total_recoveries = 0;
		$total_net = 0;
		//echo the start of the table
		$data_buffer .= "<table class='data-table bpmTopicC'>";
		$data_buffer .= $this -> echoTitles();
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_delivered'] = 0;
			$region_summaries[$region -> id]['total_gross'] = 0;
			$region_summaries[$region -> id]['total_recovered'] = 0;
			$region_summaries[$region -> id]['total_net'] = 0;
			$data_buffer .= "<tr><td><b>Zone: </b></td><td colspan='2'><b>" . $region -> Region_Name . "</b></td></tr>";
			$sql = "SELECT date_format(str_to_date(p.date,'%m/%d/%Y'),'%d/%m/%Y') as date,cpc_number,group_name,v.name as village,p.dpn,p.quantity,p.unit_price,p.gross_value,(p.loan_recovery) as total_recoveries,p.net_value FROM `fbg` f left join village v on v.id = f.village left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on f.id = p.fbg and p.batch_status = '2' where r.id = '" . $region -> id . "' and str_to_date(p.date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by str_to_date(p.date,'%m/%d/%Y') desc";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $fbg_data) {
				$data_buffer .= "<tr><td class='center'>" . $fbg_data['date'] . "</td><td class='center'>" . $fbg_data['cpc_number'] . "</td><td>" . $fbg_data['group_name'] . "</td><td>" . $fbg_data['village'] . "</td><td class='center'>" . $fbg_data['dpn'] . "</td><td class='amount'>" . (empty($fbg_data['quantity']) ? '-' : number_format($fbg_data['quantity'])) . "</td><td class='center'>" . (empty($fbg_data['unit_price']) ? '-' : number_format($fbg_data['unit_price'])) . "</td><td class='amount'>" . (empty($fbg_data['gross_value']) ? '-' : number_format($fbg_data['gross_value'])) . "</td><td class='amount'>" . (empty($fbg_data['total_recoveries']) ? '-' : number_format($fbg_data['total_recoveries'])) . "</td><td class='amount'>" . (empty($fbg_data['net_value']) ? '-' : number_format($fbg_data['net_value'])) . "</td></tr>";
				$region_summaries[$region -> id]['total_delivered'] += $fbg_data['quantity'];
				$region_summaries[$region -> id]['total_gross'] += $fbg_data['gross_value'];
				$region_summaries[$region -> id]['total_recovered'] += $fbg_data['total_recoveries'];
				$region_summaries[$region -> id]['total_net'] += $fbg_data['net_value'];
			}
			$data_buffer .= "<tr><td>Totals</td><td>-</td><td>-</td><td>-</td><td>-</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_delivered'] + 0) . "</td><td>-</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_gross'] + 0) . "</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_recovered'] + 0) . "</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_net'] + 0) . "</td></tr>";
			$total_delivered += $region_summaries[$region -> id]['total_delivered'];
			$total_gross += $region_summaries[$region -> id]['total_gross'];
			$total_recoveries += $region_summaries[$region -> id]['total_recovered'];
			$total_net += $region_summaries[$region -> id]['total_net'];
		}
		$data_buffer .= "</table>";
		$data_buffer .= "<h3>Summaries</h3><table class='data-table'><tr><th></th><th>Total Delivered</th><th>Total Gross Value</th><th>Total Recoveries</th><th>Total Amount Paid</th></tr>";
		$data_buffer .= "<tr><td>Totals</td><td class='amount'>" . number_format($total_delivered + 0) . "</td><td class='amount'>" . number_format($total_gross + 0) . "</td><td class='amount'>" . number_format($total_recoveries + 0) . "</td><td class='amount'>" . number_format($total_net + 0) . "</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Deliveries Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $start_date, $end_date);

	}

	public function download_deliveries_excel($regions, $start_date, $end_date) {
		$this -> load -> database();
		$data_buffer = "";
		$total_delivered = 0;
		$total_gross = 0;
		$total_recoveries = 0;
		$total_net = 0;
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_delivered'] = "";
			$region_summaries[$region -> id]['total_gross'] = "";
			$region_summaries[$region -> id]['total_recovered'] = "";
			$region_summaries[$region -> id]['total_net'] = "";
			$data_buffer .= "Zone: \t" . $region -> Region_Name . "\n";
			$data_buffer .= $this -> echo_excel_titles();
			$sql = "SELECT date_format(str_to_date(p.date,'%m/%d/%Y'),'%d/%m/%Y') as date,cpc_number,group_name,v.name as village,p.dpn,p.quantity,p.unit_price,p.gross_value,(p.loan_recovery) as total_recoveries,p.net_value FROM `fbg` f left join village v on v.id = f.village left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on f.id = p.fbg and p.batch_status = '2' where r.id = '" . $region -> id . "' and str_to_date(p.date,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') order by str_to_date(p.date,'%m/%d/%Y') desc";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $fbg_data) {
				$data_buffer .= $fbg_data['date'] . "\t" . $fbg_data['cpc_number'] . "\t" . $fbg_data['group_name'] . "\t" . $fbg_data['village'] . "\t" . $fbg_data['dpn'] . "\t" . $fbg_data['quantity'] . "\t" . $fbg_data['unit_price'] . "\t" . $fbg_data['gross_value'] . "\t" . $fbg_data['total_recoveries'] . "\t" . $fbg_data['net_value'] . "\t\n";
				$region_summaries[$region -> id]['total_delivered'] += $fbg_data['quantity'];
				$region_summaries[$region -> id]['total_gross'] += $fbg_data['gross_value'];
				$region_summaries[$region -> id]['total_recovered'] += $fbg_data['total_recoveries'];
				$region_summaries[$region -> id]['total_net'] += $fbg_data['net_value'];
			}
			$data_buffer .= "Totals\t-\t-\t-\t-\t" . $region_summaries[$region -> id]['total_delivered'] . "\t-\t" . $region_summaries[$region -> id]['total_gross'] . "\t" . $region_summaries[$region -> id]['total_recovered'] . "\t" . $region_summaries[$region -> id]['total_net'] . "\t\n\n";
			$total_delivered += $region_summaries[$region -> id]['total_delivered'];
			$total_gross += $region_summaries[$region -> id]['total_gross'];
			$total_recoveries += $region_summaries[$region -> id]['total_recovered'];
			$total_net += $region_summaries[$region -> id]['total_net'];
		}
		$data_buffer .= "\nSummaries\n\tTotal Delivered\tTotal Gross Value\tTotal Recoveries\tTotal Amount Paid\t\n";
		$data_buffer .= "Totals\t" . $total_delivered . "\t" . $total_gross . "\t" . $total_recoveries . "\t" . $total_net . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Deliveries Report.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Deliveries Report Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
	}

	public function echoTitles() {
		return "<thead><tr class='headerrow'><th>Date</th><th>FBG No.</th><th>Group Name</th><th>Village</th><th>DPS</th><th>KGs Delivered</th><th>Unit Price</th><th>Gross Value</th><th>Recoveries</th><th>Net Payment</th></tr></thead>";
	}

	public function echo_excel_titles() {
		return "Date\tFBG No.\tGroup Name\tVillage\tDPS\tKGs Delivered\tUnit Price\tGross Value\tRecoveries\tNet Payment\t\n";
	}

	function generatePDF($data, $start_date, $end_date) {
		$start_date = date('d/m/Y', strtotime($start_date));
		$end_date = date('d/m/Y', strtotime($end_date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Cotton Deliveries Report</h3>";
		$html_title .= "<h5 style='text-align:center;'> between: " . $start_date . " and " . $end_date . "</h5>";
		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('Deliveries Report');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|{PAGENO}|Cotton Deliveries Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);

		$report_name = "Deliveries Report.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
