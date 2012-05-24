<?php
class Regional_Summaries extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_interface();
	}

	public function view_interface($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['regions'] = Region::getAll();
		$data['content_view'] = "regional_summaries_v";
		$data['quick_link'] = "view_interface";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
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
		if ($action == "Download Zonal Summary Excel") {
			$this -> downloadExcel($regions);
			return;
		}
		$this -> load -> database();
		$data_buffer = "";
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_cash_received'] = "";
			$region_summaries[$region -> id]['total_cash_paid'] = "";
			$region_summaries[$region -> id]['total_purchases_value'] = "";
			$region_summaries[$region -> id]['total_purchases_kg'] = "";
			$region_summaries[$region -> id]['total_dispatch'] = "";
			$data_buffer .= "<tr><td><b>Zone: </b></td><td><b>" . $region -> Region_Name . "</b></td></tr>";
			$data_buffer .= $this -> echoTitles();
			//Get data for each depot
			foreach ($region->Depot_Objects as $depot) {
				$sql = "select center_summaries.*,purchases_value/purchases_kg as avg_per_kg, cash_received - cash_paid-purchases_value as cash_balance,purchases_kg - dispatch as product_balance from(select cash_summary.*,coalesce(sum(w.net_weight),0) as dispatch from (select depot_summary.*,coalesce(sum(payments.amount),0) as cash_paid from (select depot_purchases.*,coalesce(sum(c.amount),0) as cash_received from(select depot_name,depot_code,depot,date as last_transaction_date,sum(quantity) as purchases_kg,coalesce(sum(gross_value),0) as purchases_value,coalesce(unit_price,0) as last_price from (select depot_details.*,date,coalesce(quantity,0) as quantity,coalesce(gross_value,0) as gross_value,unit_price from (select depot_name,depot_code,id as depot from depot where id = '" . $depot -> id . "') depot_details left join purchase p on p.depot = depot_details.depot and p.batch_status = '2' order by date desc) purchases_summary) depot_purchases left join field_cash_disbursement c on depot_purchases.depot = c.depot and c.batch_status = '2') depot_summary  left join mopping_payment payments on depot_summary.depot = payments.depot and payments.batch_status = '2') cash_summary left join weighbridge w on w.buying_center_code = depot_code where w.weighing_type = '2') center_summaries";

				$query = $this -> db -> query($sql);
				$depot_data = $query -> row_array();
				$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td>" . $depot_data['last_transaction_date'] . "</td><td>" . $depot_data['cash_received'] . "</td><td>" . $depot_data['cash_paid'] . "</td><td>" . $depot_data['purchases_value'] . "</td><td>" . $depot_data['purchases_kg'] . "</td><td>" . $depot_data['dispatch'] . "</td><td>" . $depot_data['avg_per_kg'] . "</td><td>" . $depot_data['cash_balance'] . "</td><td>" . $depot_data['product_balance'] . "</td><td>" . $depot_data['last_price'] . "</td></tr>";
				$region_summaries[$region -> id]['total_cash_received'] += $depot_data['cash_received'];
				$region_summaries[$region -> id]['total_cash_paid'] += $depot_data['cash_paid'];
				$region_summaries[$region -> id]['total_purchases_value'] += $depot_data['purchases_value'];
				$region_summaries[$region -> id]['total_purchases_kg'] += $depot_data['purchases_kg'];
				$region_summaries[$region -> id]['total_dispatch'] += $depot_data['dispatch'];
			}
		}
		$data_buffer .= "</table>";
		$data_buffer .= "<h1>Summaries</h1><table class='data-table'><tr><th>Zone</th><th>Total Cash Received</th><th>Total Cash Paid</th><th>Total Purchases (Tsh.)</th><th>Total Purchases (Kgs.)</th><th>Total Dispatch (Kgs.)</th><th>Avg. Per KG.</th><th>Total Cash Balance</th><th>Total Product Balance</th></tr>";
		foreach ($regions as $region) {
			$avg_per_kg = 0;
			if ($region_summaries[$region -> id]['total_purchases_value'] > 0 && $region_summaries[$region -> id]['total_purchases_kg'] > 0) {
				$avg_per_kg = $region_summaries[$region -> id]['total_purchases_value'] / $region_summaries[$region -> id]['total_purchases_kg'];
			}
			$data_buffer .= "<tr><td>" . $region -> Region_Name . "</td><td>" . $region_summaries[$region -> id]['total_cash_received'] . "</td><td>" . $region_summaries[$region -> id]['total_cash_paid'] . "</td><td>" . $region_summaries[$region -> id]['total_purchases_value'] . "</td><td>" . $region_summaries[$region -> id]['total_purchases_kg'] . "</td><td>" . $region_summaries[$region -> id]['total_dispatch'] . "</td><td>" . $avg_per_kg . "</td><td>" . ($region_summaries[$region -> id]['total_cash_received'] - $region_summaries[$region -> id]['total_cash_paid'] - $region_summaries[$region -> id]['total_purchases_value']) . "</td><td>" . ($region_summaries[$region -> id]['total_purchases_kg'] - $region_summaries[$region -> id]['total_dispatch']) . "</td></tr>";
		}
		$data_buffer .= "</table>";
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $date);

	}

	public function downloadExcel($regions) {
		$this -> load -> database();
		$data_buffer = "";
		//echo the start of the table
		$region_summaries = array();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_cash_received'] = "";
			$region_summaries[$region -> id]['total_cash_paid'] = "";
			$region_summaries[$region -> id]['total_purchases_value'] = "";
			$region_summaries[$region -> id]['total_purchases_kg'] = "";
			$region_summaries[$region -> id]['total_dispatch'] = "";
			$data_buffer .= "Zone: \t" . $region -> Region_Name . "\t\n";
			$data_buffer .= $this -> echoExcelTitles();
			//Get data for each depot
			foreach ($region->Depot_Objects as $depot) {
				$sql = "select center_summaries.*,purchases_value/purchases_kg as avg_per_kg, cash_received - cash_paid-purchases_value as cash_balance,purchases_kg - dispatch as product_balance from(select cash_summary.*,coalesce(sum(w.net_weight),0) as dispatch from (select depot_summary.*,coalesce(sum(payments.amount),0) as cash_paid from (select depot_purchases.*,coalesce(sum(c.amount),0) as cash_received from(select depot_name,depot_code,depot,date as last_transaction_date,sum(quantity) as purchases_kg,coalesce(sum(gross_value),0) as purchases_value,coalesce(unit_price,0) as last_price from (select depot_details.*,date,coalesce(quantity,0) as quantity,coalesce(gross_value,0) as gross_value,unit_price from (select depot_name,depot_code,id as depot from depot where id = '" . $depot -> id . "') depot_details left join purchase p on p.depot = depot_details.depot and p.batch_status = '2' order by date desc) purchases_summary) depot_purchases left join field_cash_disbursement c on depot_purchases.depot = c.depot and c.batch_status = '2') depot_summary  left join mopping_payment payments on depot_summary.depot = payments.depot and payments.batch_status = '2') cash_summary left join weighbridge w on w.buying_center_code = depot_code where w.weighing_type = '2') center_summaries";

				$query = $this -> db -> query($sql);
				$depot_data = $query -> row_array();
				$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['last_transaction_date'] . "\t" . $depot_data['cash_received'] . "\t" . $depot_data['cash_paid'] . "\t" . $depot_data['purchases_value'] . "\t" . $depot_data['purchases_kg'] . "\t" . $depot_data['dispatch'] . "\t" . $depot_data['avg_per_kg'] . "\t" . $depot_data['cash_balance'] . "\t" . $depot_data['product_balance'] . "\t" . $depot_data['last_price'] . "\t\n";
				$region_summaries[$region -> id]['total_cash_received'] += $depot_data['cash_received'];
				$region_summaries[$region -> id]['total_cash_paid'] += $depot_data['cash_paid'];
				$region_summaries[$region -> id]['total_purchases_value'] += $depot_data['purchases_value'];
				$region_summaries[$region -> id]['total_purchases_kg'] += $depot_data['purchases_kg'];
				$region_summaries[$region -> id]['total_dispatch'] += $depot_data['dispatch'];
			}
			$data_buffer .= "\n";
		}
		$data_buffer .= "Summaries\nZone\tTotal Cash Received\tTotal Cash Paid\tTotal Purchases (Tsh.)\tTotal Purchases (Kgs.)\tTotal Dispatch (Kgs.)\tAvg. Per KG.\tTotal Cash Balance\tTotal Product Balance\t\n";
		foreach ($regions as $region) {
			$avg_per_kg = 0;
			if ($region_summaries[$region -> id]['total_purchases_value'] > 0 && $region_summaries[$region -> id]['total_purchases_kg'] > 0) {
				$avg_per_kg = $region_summaries[$region -> id]['total_purchases_value'] / $region_summaries[$region -> id]['total_purchases_kg'];
			}
			$data_buffer .= $region -> Region_Name . "\t" . $region_summaries[$region -> id]['total_cash_received'] . "\t" . $region_summaries[$region -> id]['total_cash_paid'] . "\t" . $region_summaries[$region -> id]['total_purchases_value'] . "\t" . $region_summaries[$region -> id]['total_purchases_kg'] . "\t" . $region_summaries[$region -> id]['total_dispatch'] . "\t" . $avg_per_kg . "\t" . ($region_summaries[$region -> id]['total_cash_received'] - $region_summaries[$region -> id]['total_cash_paid'] - $region_summaries[$region -> id]['total_purchases_value']) . "\t" . ($region_summaries[$region -> id]['total_purchases_kg'] - $region_summaries[$region -> id]['total_dispatch']) . "\t\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Zonal Summaries.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;

	}

	public function echoTitles() {
		return "<tr><th>Buying Center</th><th>Last Purchase Date</th><th>Cash Received</th><th>Cash Paid</th><th>Purchases (Tsh.)</th><th>Purchases (Kgs.)</th><th>Dispatch (Kgs.)</th><th>Avg. Per KG.</th><th>Cash Balance</th><th>Product Balance</th><th>Last Price</th></tr>";
	}

	public function echoExcelTitles() {
		return "Buying Center\tLast Purchase Date\tCash Received\tCash Paid\tPurchases (Tsh.)\tPurchases (Kgs.)\tDispatch (Kgs.)\tAvg. Per KG.\tCash Balance\tProduct Balance\tLast Price\t\n";
	}

	function generatePDF($data, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h2 style='text-align:center; text-decoration:underline;'>Alliance Ginneries</h2>";

		$html_title .= "<h1 style='text-align:center; text-decoration:underline;'>Zonal Summaries</h1>";
		$html_title .= "<h3 style='text-align:center;'> as at: " . $date . "</h3>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('Zonal Summaries');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML('<br/>');
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Zonal Summaries.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
