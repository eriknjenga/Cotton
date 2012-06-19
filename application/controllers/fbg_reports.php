<?php
class FBG_Reports extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> aged_analysis();
	}

	public function aged_analysis($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['regions'] = Region::getAll();
		$data['content_view'] = "debtors_aged_analysis_v";
		$data['quick_link'] = "aged_analysis";
		$this -> base_params($data);
	}

	public function download_aged_analysis() {
		$regions = array();
		$region = $this -> input -> post("region");
		$action = $this -> input -> post("action");

		$date = date("m/d/Y");
		if ($region == 0) {
			//Get the region
			$regions = Region::getAll();
		} else {
			$regions = Region::getRegionArray($region);
		}
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Debt Analysis Excel") {
			$this -> download_analysis_excel($regions);
			return;
		}
		$this -> load -> database();
		$data_buffer = "";
		$total_debt = 0;
		$total_recoveries = 0;
		$total_debt_owing = 0;
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_debt'] = "";
			$region_summaries[$region -> id]['total_recoveries'] = "";
			$region_summaries[$region -> id]['total_debt_owing'] = "";
			$data_buffer .= "<tr><td><b>Zone: </b></td><td><b>" . $region -> Region_Name . "</b></td></tr>";
			$data_buffer .= $this -> echoTitles();
			$sql = "select cpc_number,group_name,hectares_available,chairman_name,v.name as village,(select sum(d.total_value) from disbursement d where d.fbg = f.id and batch_status = '2') as total_borrowed ,(select sum(gross_value-net_value) from purchase p where p.fbg = f.id and batch_status = '2') as total_recovered  from fbg f left join village v on f.village = v.id left join ward w on v.ward = w.id where w.region = '" . $region -> id . "' order by village asc";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $fbg_data) {
				$total_outstanding = $fbg_data['total_borrowed'] - $fbg_data['total_recovered'];
				$data_buffer .= "<tr><td>" . $fbg_data['cpc_number'] . "</td><td>" . $fbg_data['group_name'] . "</td><td>" . $fbg_data['chairman_name'] . "</td><td>" . $fbg_data['village'] . "</td><td>" . ($fbg_data['total_borrowed'] + 0) . "</td><td>" . ($fbg_data['total_recovered'] + 0) . "</td><td>" . $total_outstanding . "</td></tr>";
				$region_summaries[$region -> id]['total_debt'] += $fbg_data['total_borrowed'];
				$region_summaries[$region -> id]['total_recoveries'] += $fbg_data['total_recovered'];
				$region_summaries[$region -> id]['total_debt_owing'] += $total_outstanding;
			}
			$data_buffer .= "<tr><td>Totals</td><td>-</td><td>-</td><td>-</td><td>" . $region_summaries[$region -> id]['total_debt'] . "</td><td>" . $region_summaries[$region -> id]['total_recoveries'] . "</td><td>" . $region_summaries[$region -> id]['total_debt_owing'] . "</td></tr>";
			$total_debt += $region_summaries[$region -> id]['total_debt'];
			$total_recoveries += $region_summaries[$region -> id]['total_recoveries'];
			$total_debt_owing += $region_summaries[$region -> id]['total_debt_owing'];
		}
		$data_buffer .= "</table>";
		$data_buffer .= "<h1>Summaries</h1><table class='data-table'><tr><th></th><th>Total Debt</th><th>Total Recoveries</th><th>Total Debt Owing</th></tr>";
		$data_buffer .= "<tr><td>Totals</td><td>" . $total_debt . "</td><td>" . $total_recoveries . "</td><td>" . $total_debt_owing . "</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Debtors Aged Analysis PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $date);

	}

	public function download_analysis_excel($regions) {
		$this -> load -> database();
		$data_buffer = "";
		$total_debt = 0;
		$total_recoveries = 0;
		$total_debt_owing = 0;
		//echo the start of the table
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_debt'] = "";
			$region_summaries[$region -> id]['total_recoveries'] = "";
			$region_summaries[$region -> id]['total_debt_owing'] = "";
			$data_buffer .= "Zone: \t" . $region -> Region_Name . "\n";
			$data_buffer .= $this -> echo_excel_titles();
			$sql = "select cpc_number,group_name,hectares_available,chairman_name,v.name as village,(select sum(d.total_value) from disbursement d where d.fbg = f.id) as total_borrowed ,(select sum(gross_value-net_value) from purchase p where p.fbg = f.id) as total_recovered  from fbg f left join village v on f.village = v.id left join ward w on v.ward = w.id where w.region = '" . $region -> id . "' order by village asc";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $fbg_data) {
				$total_outstanding = $fbg_data['total_borrowed'] - $fbg_data['total_recovered'];
				$data_buffer .= $fbg_data['cpc_number'] . "\t" . $fbg_data['group_name'] . "\t" . $fbg_data['chairman_name'] . "\t" . $fbg_data['village'] . "\t" . ($fbg_data['total_borrowed'] + 0) . "\t" . ($fbg_data['total_recovered'] + 0) . "\t" . $total_outstanding . "\t\n";
				$region_summaries[$region -> id]['total_debt'] += $fbg_data['total_borrowed'];
				$region_summaries[$region -> id]['total_recoveries'] += $fbg_data['total_recovered'];
				$region_summaries[$region -> id]['total_debt_owing'] += $total_outstanding;
			}
			$data_buffer .= "Totals\t-\t-\t-\t" . $region_summaries[$region -> id]['total_debt'] . "\t" . $region_summaries[$region -> id]['total_recoveries'] . "\t" . $region_summaries[$region -> id]['total_debt_owing'] . "\t\n\n";
			$total_debt += $region_summaries[$region -> id]['total_debt'];
			$total_recoveries += $region_summaries[$region -> id]['total_recoveries'];
			$total_debt_owing += $region_summaries[$region -> id]['total_debt_owing'];
		} 
		$data_buffer .= "Summaries\tTotal Debt\tTotal Recoveries\tTotal Debt Owing\t\n";
		$data_buffer .= "Totals\t" . $total_debt . "\t" . $total_recoveries . "\t" . $total_debt_owing . "\n";
		//echo $data_buffer;
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Debt Analysis.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Debtors Aged Analysis Excell Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
	}

	public function echoTitles() {
		return "<tr><th>FBG No.</th><th>Name</th><th>Chairman</th><th>Village</th><th>Loaned to Date</th><th>Recovered to Date</th><th>Outstanding Balance</th></tr>";
	}

	public function echo_excel_titles() {
		return "FBG No.\tName\tChairman\tVillage\tLoaned to Date\tRecovered to Date\tOutstanding Balance\t\n";
	}

	function generatePDF($data, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h2 style='text-align:center; text-decoration:underline;'>Alliance Ginneries</h2>";

		$html_title .= "<h1 style='text-align:center; text-decoration:underline;'>Debtors Analysis</h1>";
		$html_title .= "<h3 style='text-align:center;'> as at: " . $date . "</h3>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('Debtors Analysis');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Debtors Analysis.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}