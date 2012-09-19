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

		$date = date("d/m/Y");
		if ($region == 0) {
			//Get the region
			$regions = Region::getAll();
		} else {
			$regions = Region::getRegionArray($region);
		}
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download BC Summaries Excel") {
			$this -> downloadExcel($regions);
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
		$status = array("0" => "O", "2" => "C");
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$region_summaries = array();
		$total_cash_received = 0;
		$total_cash_paid = 0;
		$total_purchases_value = 0;
		$total_purchases_kg = 0;
		$total_dispatch = 0;
		$total_cash_returned = 0;
		$total_avg_distance = 0;
		$data_buffer .= $this -> echoTitles();
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_cash_received'] = 0;
			$region_summaries[$region -> id]['total_purchases_value'] = 0;
			$region_summaries[$region -> id]['total_purchases_kg'] = 0;
			$region_summaries[$region -> id]['total_dispatch'] = 0;
			$region_summaries[$region -> id]['total_avg_distance'] = 0;
			$region_summaries[$region -> id]['total_cash_returned'] = 0;
			$data_buffer .= "<tr><td><b>Zone: </b></td><td colspan='10'><b>" . $region -> Region_Name . "</b></td></tr>";

			$sql_regional_depots = "select center_summaries.*,(purchases_value/purchases_kg) as avg_per_kg, (coalesce(cash_received,0) - coalesce(purchases_value,0)-coalesce(cash_returned,0)) as cash_balance,(coalesce(purchases_kg,0) - coalesce(dispatch,0)) as product_balance from(select cash_summary.*,sum(w.net_weight) as dispatch from (select depot_summary.*,sum(bcr.amount) as cash_returned  from (select depot_purchases.*,sum(c.amount) as cash_received from(select depot_name,depot_code,depot,deleted,cash_disbursement_route,distance,date_format(last_transaction_date,'%d/%m/%Y') as last_transaction_date,sum(quantity) as purchases_kg,sum(gross_value) as purchases_value,unit_price as last_price from (select depot_name,depot.id as depot,depot_code,depot.deleted as deleted,cash_disbursement_route,distance,max(str_to_date(date,'%m/%d/%Y')) as last_transaction_date,sum(quantity+free_farmer_quantity) as quantity,sum(gross_value+free_farmer_value) as gross_value,unit_price from purchase p left join depot on p.depot = depot.id and p.batch_status = '2' and depot.deleted != '1' left join village v on depot.village = v.id left join ward w on v.ward = w.id where w.region = '" . $region -> id . "'  group by depot.id) purchases_summary group by depot) depot_purchases left join field_cash_disbursement c on depot_purchases.depot = c.depot and c.batch_status = '2' group by depot) depot_summary left join buying_center_receipt bcr on depot_summary.depot = bcr.depot and bcr.batch_status = '2'  group by depot) cash_summary left join weighbridge w on w.buying_center_code = depot_code and w.weighing_type = '2' group by depot_code) center_summaries order by cast(product_balance as signed) desc";
			$region_depots = $this -> db -> query($sql_regional_depots);
			//Get data for each depot
			foreach ($region_depots->result_array() as $depot_data) {
				$avg_distance = ($depot_data['purchases_kg'] * $depot_data['distance']);
				$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td class='center'>" . $depot_data['depot_code'] . "</td><td class='center'>" . $status[$depot_data['deleted']] . "</td><td class='center'>" . $depot_data['cash_disbursement_route'] . "</td><td class='center'>" . (empty($depot_data['last_transaction_date']) ? '-' : $depot_data['last_transaction_date']) . "</td><td class='amount'>" . (empty($depot_data['cash_received']) ? '-' : number_format($depot_data['cash_received'] + 0)) . "</td><td class='amount'>" . (empty($depot_data['cash_returned']) ? '-' : number_format($depot_data['cash_returned'] + 0)) . "</td><td class='amount'>" . (empty($depot_data['purchases_value']) ? '-' : number_format($depot_data['purchases_value'] + 0)) . "</td><td class='amount'>" . (empty($depot_data['purchases_kg']) ? '-' : number_format($depot_data['purchases_kg'] + 0)) . "</td><td class='amount'>" . (empty($depot_data['dispatch']) ? '-' : number_format($depot_data['dispatch'] + 0)) . "</td><td class='center'>" . (empty($depot_data['avg_per_kg']) ? '-' : number_format($depot_data['avg_per_kg'] + 0)) . "</td><td class='amount'>" . (empty($depot_data['cash_balance']) ? '-' : number_format($depot_data['cash_balance'] + 0)) . "</td><td class='amount'>" . (empty($depot_data['product_balance']) ? '-' : number_format($depot_data['product_balance'] + 0)) . "</td><td class='center'>" . (empty($depot_data['last_price']) ? '-' : number_format($depot_data['last_price'] + 0)) . "</td></tr>";
				$region_summaries[$region -> id]['total_cash_received'] += $depot_data['cash_received'];
				$region_summaries[$region -> id]['total_purchases_value'] += $depot_data['purchases_value'];
				$region_summaries[$region -> id]['total_purchases_kg'] += $depot_data['purchases_kg'];
				$region_summaries[$region -> id]['total_dispatch'] += $depot_data['dispatch'];
				$region_summaries[$region -> id]['total_cash_returned'] += $depot_data['cash_returned'];
				$region_summaries[$region -> id]['total_avg_distance'] += $avg_distance;
			}
		}
		$data_buffer .= "</table>";
		$data_buffer .= "<h3>Summaries</h3><table class='data-table'><tr><th>Zone</th><th>Total Cash Received</th><th>Total Cash Returned</th><th>Total Purchases (Tsh.)</th><th>Total Purchases (Kgs.)</th><th>Total Dispatch (Kgs.)</th><th>Avg. Price/KG.</th><th>Total Cash Balance</th><th>Total Stock Balance</th><th>Avg. Distance</th></tr>";
		foreach ($regions as $region) {
			$avg_per_kg = 0;
			if ($region_summaries[$region -> id]['total_purchases_value'] > 0 && $region_summaries[$region -> id]['total_purchases_kg'] > 0) {
				$avg_per_kg = $region_summaries[$region -> id]['total_purchases_value'] / $region_summaries[$region -> id]['total_purchases_kg'];
			}
			$total_cash_received += $region_summaries[$region -> id]['total_cash_received'];
			$total_cash_returned += $region_summaries[$region -> id]['total_cash_returned'];
			$total_purchases_value += $region_summaries[$region -> id]['total_purchases_value'];
			$total_purchases_kg += $region_summaries[$region -> id]['total_purchases_kg'];
			$total_dispatch += $region_summaries[$region -> id]['total_dispatch'];
			$total_avg_distance += $region_summaries[$region -> id]['total_avg_distance'];
			$zone_avg_distance = 0;
			if ($region_summaries[$region -> id]['total_avg_distance'] > 0 && $region_summaries[$region -> id]['total_purchases_kg'] > 0) {
				$zone_avg_distance = number_format(($region_summaries[$region -> id]['total_avg_distance'] / $region_summaries[$region -> id]['total_purchases_kg']), 3);
			}
			$data_buffer .= "<tr><td>" . $region -> Region_Name . "</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_cash_received']) . "</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_cash_returned']) . "</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_purchases_value']) . "</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_purchases_kg']) . "</td><td class='amount'>" . number_format($region_summaries[$region -> id]['total_dispatch']) . "</td><td class='amount'>" . number_format($avg_per_kg) . "</td><td class='amount'>" . number_format(($region_summaries[$region -> id]['total_cash_received'] - $region_summaries[$region -> id]['total_purchases_value'])) . "</td><td class='amount'>" . number_format(($region_summaries[$region -> id]['total_purchases_kg'] - $region_summaries[$region -> id]['total_dispatch'])) . "</td><td class='amount'>" . $zone_avg_distance . "</td></tr>";
		}
		$avg_per_kg = 0;
		if ($total_purchases_value > 0 && $total_purchases_kg > 0) {
			$avg_per_kg = $total_purchases_value / $total_purchases_kg;
		}
		$grand_avg_distance = 0;
		if ($total_avg_distance > 0 && $total_purchases_kg > 0) {
			$grand_avg_distance = number_format(($total_avg_distance / $total_purchases_kg), 3);
		}
		$data_buffer .= "<tr></tr><tr><td><b>Grand Totals</b></td><td class='amount'>" . number_format($total_cash_received) . "</td><td class='amount'>" . number_format($total_cash_returned) . "</td><td class='amount'>" . number_format($total_purchases_value) . "</td><td class='amount'>" . number_format($total_purchases_kg) . "</td><td class='amount'>" . number_format($total_dispatch) . "</td><td class='center'>" . number_format($avg_per_kg) . "</td><td class='amount'>" . number_format(($total_cash_received - $total_cash_paid - $total_purchases_value)) . "</td><td class='amount'>" . number_format(($total_purchases_kg - $total_dispatch)) . "</td><td class='amount'>" . $grand_avg_distance . "</td></tr>";
		$data_buffer .= "</table>";
		//echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Buying Center Summaries Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date);

	}

	public function downloadExcel($regions) {
		$this -> load -> database();
		$data_buffer = "";
		$status = array("0" => "O", "2" => "C");
		//echo the start of the table
		$region_summaries = array();
		$total_cash_received = 0;
		$total_cash_paid = 0;
		$total_purchases_value = 0;
		$total_purchases_kg = 0;
		$total_cash_returned = 0;
		$total_dispatch = 0;
		$total_avg_distance = 0;
		foreach ($regions as $region) {
			$region_summaries[$region -> id] = array();
			$region_summaries[$region -> id]['total_cash_received'] = "";
			$region_summaries[$region -> id]['total_purchases_value'] = "";
			$region_summaries[$region -> id]['total_purchases_kg'] = "";
			$region_summaries[$region -> id]['total_dispatch'] = "";
			$region_summaries[$region -> id]['total_avg_distance'] = 0;
			$region_summaries[$region -> id]['total_cash_returned'] = 0;
			$data_buffer .= "Zone: \t" . $region -> Region_Name . "\t\n";
			$data_buffer .= $this -> echoExcelTitles();
			$sql_regional_depots = "select center_summaries.*,(purchases_value/purchases_kg) as avg_per_kg, (coalesce(cash_received,0) - coalesce(purchases_value,0)-coalesce(cash_returned,0)) as cash_balance,(coalesce(purchases_kg,0) - coalesce(dispatch,0)) as product_balance from(select cash_summary.*,sum(w.net_weight) as dispatch from (select depot_summary.*,sum(bcr.amount) as cash_returned  from (select depot_purchases.*,sum(c.amount) as cash_received from(select depot_name,depot_code,depot,deleted,cash_disbursement_route,distance,date_format(last_transaction_date,'%d/%m/%Y') as last_transaction_date,sum(quantity) as purchases_kg,sum(gross_value) as purchases_value,unit_price as last_price from (select depot_name,depot.id as depot,depot_code,depot.deleted as deleted,cash_disbursement_route,distance,max(str_to_date(date,'%m/%d/%Y')) as last_transaction_date,sum(quantity+free_farmer_quantity) as quantity,sum(gross_value+free_farmer_value) as gross_value,unit_price from purchase p left join depot on p.depot = depot.id and p.batch_status = '2' and depot.deleted != '1' left join village v on depot.village = v.id left join ward w on v.ward = w.id where w.region = '" . $region -> id . "'  group by depot.id) purchases_summary group by depot) depot_purchases left join field_cash_disbursement c on depot_purchases.depot = c.depot and c.batch_status = '2' group by depot) depot_summary left join buying_center_receipt bcr on depot_summary.depot = bcr.depot and bcr.batch_status = '2'  group by depot) cash_summary left join weighbridge w on w.buying_center_code = depot_code and w.weighing_type = '2' group by depot_code) center_summaries order by cast(product_balance as signed) desc";
			$region_depots = $this -> db -> query($sql_regional_depots);
			//Get data for each depot
			foreach ($region_depots->result_array() as $depot_data) {
				$avg_distance = ($depot_data['purchases_kg'] * $depot_data['distance']);
				$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['depot_code'] . "\t" . $status[$depot_data['deleted']] . "\t" . $depot_data['cash_disbursement_route'] . "\t" . $depot_data['last_transaction_date'] . "\t" . $depot_data['cash_received'] . "\t" .$depot_data['cash_returned'] . "\t" . $depot_data['purchases_value'] . "\t" . $depot_data['purchases_kg'] . "\t" . $depot_data['dispatch'] . "\t" . $depot_data['avg_per_kg'] . "\t" . $depot_data['cash_balance'] . "\t" . $depot_data['product_balance'] . "\t" . $depot_data['last_price'] . "\t\n";
				$region_summaries[$region -> id]['total_cash_received'] += $depot_data['cash_received'];
				$region_summaries[$region -> id]['total_cash_returned'] += $depot_data['cash_returned'];
				$region_summaries[$region -> id]['total_purchases_value'] += $depot_data['purchases_value'];
				$region_summaries[$region -> id]['total_purchases_kg'] += $depot_data['purchases_kg'];
				$region_summaries[$region -> id]['total_dispatch'] += $depot_data['dispatch'];
				$region_summaries[$region -> id]['total_avg_distance'] += $avg_distance;

			}
			$data_buffer .= "\n";
		}
		$data_buffer .= "Summaries\nZone\tTotal Cash Received\tTotal Cash Returned\tTotal Purchases (Tsh.)\tTotal Purchases (Kgs.)\tTotal Dispatch (Kgs.)\tAvg. Per KG.\tTotal Cash Balance\tTotal Product Balance\tTotal Avg. Distance\t\n";
		foreach ($regions as $region) {
			$avg_per_kg = 0;
			$total_cash_received += $region_summaries[$region -> id]['total_cash_received'];
			$total_cash_returned += $region_summaries[$region -> id]['total_cash_returned'];
			$total_purchases_value += $region_summaries[$region -> id]['total_purchases_value'];
			$total_purchases_kg += $region_summaries[$region -> id]['total_purchases_kg'];
			$total_dispatch += $region_summaries[$region -> id]['total_dispatch'];
			$total_avg_distance += $region_summaries[$region -> id]['total_avg_distance'];
			if ($region_summaries[$region -> id]['total_purchases_value'] > 0 && $region_summaries[$region -> id]['total_purchases_kg'] > 0) {
				$avg_per_kg = $region_summaries[$region -> id]['total_purchases_value'] / $region_summaries[$region -> id]['total_purchases_kg'];
			}
			$zone_avg_distance = 0;
			if ($region_summaries[$region -> id]['total_avg_distance'] > 0 && $region_summaries[$region -> id]['total_purchases_kg'] > 0) {
				$zone_avg_distance = number_format(($region_summaries[$region -> id]['total_avg_distance'] / $region_summaries[$region -> id]['total_purchases_kg']), 3);
			}
			$data_buffer .= $region -> Region_Name . "\t" . $region_summaries[$region -> id]['total_cash_received'] ."\t". $region_summaries[$region -> id]['total_cash_returned'] . "\t" . $region_summaries[$region -> id]['total_purchases_value'] . "\t" . $region_summaries[$region -> id]['total_purchases_kg'] . "\t" . $region_summaries[$region -> id]['total_dispatch'] . "\t" . $avg_per_kg . "\t" . ($region_summaries[$region -> id]['total_cash_received'] - $region_summaries[$region -> id]['total_purchases_value']) . "\t" . ($region_summaries[$region -> id]['total_purchases_kg'] - $region_summaries[$region -> id]['total_dispatch']) . "\t" . $zone_avg_distance . "\t\n";
		}
		$avg_per_kg = 0;
		if ($total_purchases_value > 0 && $total_purchases_kg > 0) {
			$avg_per_kg = $total_purchases_value / $total_purchases_kg;
		}
		$grand_avg_distance = 0;
		if ($total_avg_distance > 0 && $total_purchases_kg > 0) {
			$grand_avg_distance = number_format(($total_avg_distance / $total_purchases_kg), 3);
		}
		$data_buffer .= "\nGrand Totals\t" . $total_cash_received . "\t" .$total_cash_returned."\t". $total_purchases_value . "\t" . $total_purchases_kg . "\t" . $total_dispatch . "\t" . $avg_per_kg . "\t" . ($total_cash_received - $total_cash_paid - $total_purchases_value) . "\t" . ($total_purchases_kg - $total_dispatch) . "\t" . $grand_avg_distance . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=BC Summaries.xls");
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
		return "<thead><tr><th>BC Name</th><th>BC Code</th><th>Status</th><th>Cashier Route</th><th>Last Purchase Date</th><th>Cash Received</th><th>Cash Returned</th><th>Purchases (Tsh.)</th><th>Purchases (Kgs.)</th><th>Dispatch (Kgs.)</th><th>Avg. Price/KG.</th><th>Cash Balance</th><th>Stock Balance</th><th>Last Price</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "Buying Center\tBC Code\tStatus\tCashier Route\tLast Purchase Date\tCash Received\tCash Returned\tPurchases (Tsh.)\tPurchases (Kgs.)\tDispatch (Kgs.)\tAvg. Price/KG.\tCash Balance\tStock Balance\tLast Price\t\n";
	}

	function generatePDF($data, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>BC Summaries</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
		$this -> mpdf -> SetTitle('BC Summaries');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|{PAGENO}|Buying Center Summaries Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "BC Summaries.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
