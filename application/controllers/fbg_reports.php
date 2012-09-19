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
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			}
			table.data-table td {
			width: 120px;
			}
			.right-align{
				text-align:right;
			}
			</style>
			";
		$total_debt = 0;
		$total_recoveries = 0;
		$total_cash_recoveries = 0;
		$total_debt_owing = 0;
		$total_bought = 0;
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_debt'] = 0;
			$region_summaries[$region -> id]['total_recoveries'] = 0;
			$region_summaries[$region -> id]['total_cash_recoveries'] = 0;
			$region_summaries[$region -> id]['total_debt_owing'] = 0;
			$region_summaries[$region -> id]['total_bought'] = 0;
			$data_buffer .= "<tr><td><b>Zone: </b></td><td><b>" . $region -> Region_Name . "</b></td></tr>";
			$data_buffer .= $this -> echoTitles();
			$sql = "select cpc_number,group_name,hectares_available,chairman_name,v.name as village,(select sum(d.total_value) from disbursement d where d.fbg = f.id and batch_status='2') as total_borrowed,(select sum(lr.amount) from loan_recovery_receipt lr where lr.fbg = f.id and batch_status='2') as total_cash_recovered,sum(loan_recovery) as total_recovered,sum(p.quantity) as total_bought  from fbg f left join purchase p on f.id = p.fbg and p.batch_status = '2' left join village v on f.village = v.id left join ward w on v.ward = w.id where w.region = '" . $region -> id . "' group by f.id order by village asc";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $fbg_data) {
				$total_outstanding = $fbg_data['total_borrowed'] - $fbg_data['total_recovered'] - $fbg_data['total_cash_recovered'];
				$data_buffer .= "<tr><td>" . $fbg_data['cpc_number'] . "</td><td>" . $fbg_data['group_name'] . "</td><td>" . $fbg_data['chairman_name'] . "</td><td>" . $fbg_data['village'] . "</td><td class='right-align'>" . (empty($fbg_data['total_borrowed']) ? '-' : number_format($fbg_data['total_borrowed'] + 0)) . "</td><td class='right-align'>" . (empty($fbg_data['total_recovered']) ? '-' : number_format($fbg_data['total_recovered'] + 0)) . "</td><td class='right-align'>" . (empty($fbg_data['total_cash_recovered']) ? '-' : number_format($fbg_data['total_cash_recovered'] + 0)) . "</td><td class='right-align'>" . (empty($total_outstanding) ? '-' : number_format($total_outstanding + 0)) . "</td><td class='right-align'>" . (empty($fbg_data['total_bought']) ? '-' : number_format($fbg_data['total_bought'] + 0)) . "</td></tr>";
				$region_summaries[$region -> id]['total_debt'] += $fbg_data['total_borrowed'];
				$region_summaries[$region -> id]['total_recoveries'] += $fbg_data['total_recovered'];
				$region_summaries[$region -> id]['total_cash_recoveries'] += $fbg_data['total_cash_recovered'];
				$region_summaries[$region -> id]['total_debt_owing'] += $total_outstanding;
				$region_summaries[$region -> id]['total_bought'] += $fbg_data['total_bought'];
			}
			$data_buffer .= "<tr><td>Totals</td><td>-</td><td>-</td><td>-</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_debt'] + 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_recoveries'] + 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_cash_recoveries'] + 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_debt_owing'] + 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_bought'] + 0) . "</td></tr>";
			$total_debt += $region_summaries[$region -> id]['total_debt'];
			$total_recoveries += $region_summaries[$region -> id]['total_recoveries'];
			$total_cash_recoveries += $region_summaries[$region -> id]['total_cash_recoveries'];
			$total_debt_owing += $region_summaries[$region -> id]['total_debt_owing'];
			$total_bought += $region_summaries[$region -> id]['total_bought'];
		}
		$data_buffer .= "</table>";
		$data_buffer .= "<h3>Summaries</h3><table class='data-table'><tr><th></th><th>Total Debt</th><th>Total Crop Recoveries</th><th>Total Cash Recoveries</th><th>Total Debt Owing</th><th>Total KGs Bought</th></tr>";
		foreach ($regions as $region) {
			$data_buffer .= "<tr><td>".$region -> Region_Name ."</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_debt'] + 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_recoveries'] + 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_cash_recoveries']+ 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_debt_owing'] + 0) . "</td><td class='right-align'>" . number_format($region_summaries[$region -> id]['total_bought'] + 0) . "</td></tr>";
		}
		$data_buffer .= "<tr><td>Grand Totals</td><td class='right-align'>" . number_format($total_debt + 0) . "</td><td class='right-align'>" . number_format($total_recoveries + 0) . "</td><td class='right-align'>" . number_format($total_cash_recoveries + 0) . "</td><td class='right-align'>" . number_format($total_debt_owing + 0) . "</td><td class='right-align'>" . number_format($total_bought + 0) . "</td></tr>";
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
		$total_cash_recoveries = 0;
		$total_debt_owing = 0;
		$total_bought = 0;
		//echo the start of the table
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_debt'] = 0;
			$region_summaries[$region -> id]['total_recoveries'] = 0;
			$region_summaries[$region -> id]['total_cash_recoveries'] = 0;
			$region_summaries[$region -> id]['total_debt_owing'] = 0;
			$region_summaries[$region -> id]['total_bought'] = 0;
			$data_buffer .= "Zone: \t" . $region -> Region_Name . "\n";
			$data_buffer .= $this -> echo_excel_titles();
			$sql = "select cpc_number,group_name,hectares_available,chairman_name,v.name as village,(select sum(d.total_value) from disbursement d where d.fbg = f.id and batch_status='2') as total_borrowed,(select sum(lr.amount) from loan_recovery_receipt lr where lr.fbg = f.id and batch_status='2') as total_cash_recovered,sum(loan_recovery) as total_recovered,sum(p.quantity) as total_bought  from fbg f left join purchase p on f.id = p.fbg and p.batch_status = '2' left join village v on f.village = v.id left join ward w on v.ward = w.id where w.region = '" . $region -> id . "' group by f.id order by village asc";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $fbg_data) {
				$total_outstanding = $fbg_data['total_borrowed'] - $fbg_data['total_recovered'] - $fbg_data['total_cash_recovered'];
				$data_buffer .= $fbg_data['cpc_number'] . "\t" . $fbg_data['group_name'] . "\t" . $fbg_data['chairman_name'] . "\t" . $fbg_data['village'] . "\t" . ($fbg_data['total_borrowed'] + 0) . "\t" . ($fbg_data['total_recovered'] + 0) . "\t" . ($fbg_data['total_cash_recovered'] + 0) . "\t" . $total_outstanding . "\t" . $fbg_data['total_bought'] . "\t\n";
				$region_summaries[$region -> id]['total_debt'] += $fbg_data['total_borrowed'];
				$region_summaries[$region -> id]['total_recoveries'] += $fbg_data['total_recovered'];
				$region_summaries[$region -> id]['total_cash_recoveries'] += $fbg_data['total_cash_recovered'];
				$region_summaries[$region -> id]['total_debt_owing'] += $total_outstanding;
				$region_summaries[$region -> id]['total_bought'] += $fbg_data['total_bought'];
			}
			$data_buffer .= "Totals\t-\t-\t-\t" . $region_summaries[$region -> id]['total_debt'] . "\t" . $region_summaries[$region -> id]['total_recoveries'] . "\t" . $region_summaries[$region -> id]['total_cash_recoveries'] . "\t" . $region_summaries[$region -> id]['total_debt_owing'] . "\t" . $region_summaries[$region -> id]['total_bought'] . "\t\n\n";
			$total_debt += $region_summaries[$region -> id]['total_debt'];
			$total_recoveries += $region_summaries[$region -> id]['total_recoveries'];
			$total_cash_recoveries += $region_summaries[$region -> id]['total_cash_recoveries'];
			$total_debt_owing += $region_summaries[$region -> id]['total_debt_owing'];
			$total_bought += $region_summaries[$region -> id]['total_bought'];
		}
		$data_buffer .= "Zone\tTotal Debt\tTotal Recoveries (Crop)\tTotal Recoveries (Cash)\tTotal Debt Owing\tTotal KGs Bought\t\n";
		foreach ($regions as $region) {
			$data_buffer .= $region -> Region_Name . "\t" . $region_summaries[$region -> id]['total_debt'] . "\t" . $region_summaries[$region -> id]['total_recoveries'] . "\t" . $region_summaries[$region -> id]['total_cash_recoveries'] . "\t" . $region_summaries[$region -> id]['total_debt_owing'] . "\t" . $region_summaries[$region -> id]['total_bought'] . "\t\n";
		}
		$data_buffer .= "\nGrand Totals\t" . $total_debt . "\t" . $total_recoveries . "\t" . $total_cash_recoveries . "\t" . $total_debt_owing . "\t" . $total_bought . "\n";
		//echo $data_buffer;
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Debt Analysis.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Debtors Aged Analysis Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
	}

	public function echoTitles() {
		return "<tr><th>FBG No.</th><th>Name</th><th>Chairman</th><th>Village</th><th>Loaned to Date</th><th>Crop Recovered (Kgs.)</th><th>Cash Recovered</th><th>Outstanding Balance</th><th>KGs Bought to Date</th></tr>";
	}

	public function echo_excel_titles() {
		return "FBG No.\tName\tChairman\tVillage\tLoaned to Date\tCrop Recovered (Kgs.)\tCash Recovered\tOutstanding Balance\tKGs Bought to Date\t\n";
	}

	function generatePDF($data, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>FBG Debt Analysis</h3>";
		$html_title .= "<h3 style='text-align:center;'> as at: " . $date . "</h3>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Debtors Analysis');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
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
