<?php
class Buying_Center_Purchases extends MY_Controller {
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
		$data['content_view'] = "buying_center_purchases_v";
		$data['quick_link'] = "buying_center_purchases";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$action = $this -> input -> post("action");
		$date = $this -> input -> post("date");
		//Get the region
		$regions = Region::getAll();
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download BC Purchases Excel") {
			$this -> downloadExcel($regions, $date);
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
			width: 100px;
			}
			</style>
			";
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$region_summaries = array();
		$total_quantity = 0;
		$total_value = 0;
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_quantity'] = "";
			$region_summaries[$region -> id]['total_value'] = "";
			$data_buffer .= "<tr><td><b>Zone: </b></td><td><b>" . $region -> Region_Name . "</b></td></tr>";
			$data_buffer .= $this -> echoTitles();
			//Get data for each zone
			$sql = "select (case when d.Deleted ='1' then concat(depot_name,' (Closed)') else case when d.Deleted = '0' then depot_name end  end) as depot_name , r.region_name , p.dpn,p.date,p.quantity,p.gross_value,(p.gross_value/p.quantity) as avg_price from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on d.id = p.depot and str_to_date(p.date,'%m/%d/%Y') = str_to_date('" . $date . "','%m/%d/%Y')  where r.id = '" . $region -> id . "' ";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $depot_data) {
				//(empty($disbursement['unit_price']) ? '-' : number_format($disbursement['unit_price'] + 0))
				$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td>" . (empty($depot_data['dpn']) ? '-' :$depot_data['dpn']) . "</td><td>" . (empty($depot_data['quantity']) ? '-' : number_format($depot_data['quantity'] + 0)) . "</td><td>" . (empty($depot_data['gross_value']) ? '-' : number_format($depot_data['gross_value'] + 0)) . "</td><td>" . (empty($depot_data['avg_price']) ? '-' : number_format($depot_data['avg_price'] + 0)) . "</td></tr>";
				$region_summaries[$region -> id]['total_quantity'] += $depot_data['quantity'];
				$region_summaries[$region -> id]['total_value'] += $depot_data['gross_value'];
			}
		}
		$data_buffer .= "</table>";
		$data_buffer .= "<h3>Summaries</h3><table class='data-table'><tr><th>Zone</th><th>Total Quantity Purchased</th><th>Total Value Purchased</th><th>Average Price</th></tr>";
		foreach ($regions as $region) {
			$avg_per_kg = 0;
			if ($region_summaries[$region -> id]['total_value'] > 0 && $region_summaries[$region -> id]['total_quantity'] > 0) {
				$avg_per_kg = $region_summaries[$region -> id]['total_value'] / $region_summaries[$region -> id]['total_quantity'];
			}
			$data_buffer .= "<tr><td>" . $region -> Region_Name . "</td><td>" . (empty($region_summaries[$region -> id]['total_quantity']) ? '-' : number_format($region_summaries[$region -> id]['total_quantity'] + 0)) . "</td><td>" .(empty($region_summaries[$region -> id]['total_value']) ? '-' : number_format($region_summaries[$region -> id]['total_value'] + 0)). "</td><td>" . (empty($avg_per_kg) ? '-' : number_format($avg_per_kg+ 0)). "</td></tr>";
		}
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Buying Center Purchases Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $date);

	}

	public function downloadExcel($regions, $date) {
		$this -> load -> database();
		$data_buffer = "";
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_quantity'] = "";
			$region_summaries[$region -> id]['total_value'] = "";
			$data_buffer .= "Zone: \t" . $region -> Region_Name . "\t\n";
			$data_buffer .= $this -> echoExcelTitles();
			//Get data for each zone
			$sql = "select (case when d.Deleted ='1' then concat(depot_name,' (Closed)') else case when d.Deleted = '0' then depot_name end  end) as depot_name , r.region_name , p.dpn,p.date,p.quantity,p.gross_value,(p.gross_value/p.quantity) as avg_price from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on d.id = p.depot and str_to_date(p.date,'%m/%d/%Y') = str_to_date('" . $date . "','%m/%d/%Y')  where r.id = '" . $region -> id . "' ";
			$query = $this -> db -> query($sql);
			foreach ($query->result_array() as $depot_data) {
				$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['dpn'] . "\t" . $depot_data['quantity'] . "\t" . $depot_data['gross_value'] . "\t" . $depot_data['avg_price'] . "\t\n";
				$region_summaries[$region -> id]['total_quantity'] += $depot_data['quantity'];
				$region_summaries[$region -> id]['total_value'] += $depot_data['gross_value'];
			}
		}
		$data_buffer .= "\n";
		$data_buffer .= "Summaries\nZone\tTotal Quantity Purchased\tTotal Value Purchased\tAverage Price\t\n";
		foreach ($regions as $region) {
			$avg_per_kg = 0;
			if ($region_summaries[$region -> id]['total_value'] > 0 && $region_summaries[$region -> id]['total_quantity'] > 0) {
				$avg_per_kg = $region_summaries[$region -> id]['total_value'] / $region_summaries[$region -> id]['total_quantity'];
			}
			$data_buffer .= $region -> Region_Name . "\t" . $region_summaries[$region -> id]['total_quantity'] . "\t" . $region_summaries[$region -> id]['total_value'] . "\t" . $avg_per_kg . "\t\n";
		}
		$data_buffer .= "\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=BC Purchases.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Buying Center Summaries Report Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<tr><th>Buying Center</th><th>DPN #</th><th>Quantity (Kgs.)</th><th>Total Value (Tsh.)</th><th>Avg. Price</th></tr>";
	}

	public function echoExcelTitles() {
		return "Buying Center\tDPN #\tQuantity (Kgs.)\tTotal Value (Tsh.)\tAvg. Price\t\n";
	}

	function generatePDF($data, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Buying Center Purchases</h3>";
		$html_title .= "<h5 style='text-align:center;'> Purchases on: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('BC Purchases');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "BC Purchases.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
