<?php
class Buying_Center_Funding extends MY_Controller {
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
		$data['content_view'] = "buying_center_funding_v";
		$data['quick_link'] = "buying_center_funding";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {

		$date = $this -> input -> post("date");
		$region = $this -> input -> post("region");
		$history = $this -> input -> post("history");
		$cycle = $this -> input -> post("cycle");
		$price = $this -> input -> post("price");
		$action = $this -> input -> post("action");
		$nearest = $this -> input -> post("nearest");
		$factor = $this -> input -> post("factor");
		$region_object = Region::getRegion($region);
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download BC Funding Excel") {
			$this -> downloadExcel($region, $date, $history, $cycle, $nearest, $price, $factor);
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
			.right{
				text-align: right;
			}
			</style>
			";
		//echo the start of the table
		$data_buffer .= "<h3>Zone: " . $region_object -> Region_Name . "</h3>";
		$data_buffer .= "<table class='data-table'>";
		$procurement_estimate_total = 0;
		$procurement_value_total = 0;
		$cash_balance_total = 0;
		$release_total = 0;	
		
		$data_buffer .= $this -> echoTitles();
		
		//Get all the depots in this region
		$sql_funding = "select depot_summaries.*,(select sum(gross_value+free_farmer_value) from purchase where depot = depot_summaries.depot) as total_purchases,(select sum(amount) from field_cash_disbursement where depot = depot_summaries.depot) as total_received,(select sum(amount) from buying_center_receipt where depot = depot_summaries.depot) as total_returned from (select depot_purchase.*, max(str_to_date(date,'%m/%d/%Y')) as last_purchase_date,sum(quantity+free_farmer_quantity) as total_purchase, unit_price as last_price from (select d.id as depot,cash_disbursement_route as route,d.depot_name,quantity,free_farmer_quantity, date,unit_price from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on p.depot = d.id and str_to_date(date,'%m/%d/%Y') between DATE_SUB(str_to_date('" . $date . "','%m/%d/%Y'), INTERVAL " . $history . " day) and str_to_date('" . $date . "','%m/%d/%Y') where r.id = '" . $region . "' and d.deleted = '0' order by str_to_date(date,'%m/%d/%Y') desc) depot_purchase group by depot) depot_summaries";
		$bc_funding = $this -> db -> query($sql_funding);
		//Get data for each depot
		foreach ($bc_funding->result_array() as $depot_data) {
			$avg_per_day = round($depot_data['total_purchase'] / $history);
			$procurement_estimate = ($avg_per_day * $cycle);
			$procurement_value = ($procurement_estimate * $price);
			$cash_balance = ($depot_data['total_received'] - $depot_data['total_purchases'] - $depot_data['total_returned']);
			$release_amount = ($procurement_value - $cash_balance) * $factor;
			if ($release_amount > 0) {
				$release_amount = ceil($release_amount / $nearest) * $nearest;
			}
			//(empty($depot_data['quantity']) ? '-' : number_format($depot_data['quantity'] + 0))
			$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td>" . $depot_data['route'] . "</td><td>" . (empty($depot_data['last_purchase_date']) ? '-' : $depot_data['last_purchase_date']) . "</td><td class='right'>" . (empty($depot_data['total_purchase']) ? '-' : number_format($depot_data['total_purchase'] + 0)) . "</td><td class='right'>" . (empty($avg_per_day) ? '-' : number_format($avg_per_day + 0)) . "</td><td class='right'>" . (empty($procurement_estimate) ? '-' : number_format($procurement_estimate + 0)) . "</td><td class='right'>" . (empty($depot_data['last_price']) ? '-' : number_format($depot_data['last_price'] + 0)) . "</td><td class='right'>" . (empty($procurement_value) ? '-' : number_format($procurement_value + 0)) . "</td><td class='right'>" . (empty($cash_balance) ? '-' : number_format($cash_balance + 0)) . "</td><td class='right'>" . (empty($release_amount) ? '-' : number_format($release_amount + 0)) . "</td></tr>";
			$procurement_estimate_total += $procurement_estimate;
			$procurement_value_total += $procurement_value;
			$cash_balance_total += $cash_balance;
			$release_total += $release_amount;
		}
		$data_buffer .= "<tr><td>Totals: </td><td>-</td><td>-</td><td>-</td><td>-</td><td class='right'>" . (empty($procurement_estimate_total) ? '-' : number_format($procurement_estimate_total + 0)) . "</td><td>-</td><td class='right'>" . (empty($procurement_value_total) ? '-' : number_format($procurement_value_total + 0)) . "</td><td class='right'>" . (empty($cash_balance_total) ? '-' : number_format($cash_balance_total + 0)) . "</td><td class='right'>" . (empty($release_total) ? '-' : number_format($release_total + 0)) . "</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Buying Center Funding Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $date, $history, $cycle, $nearest,$price,$factor);

	}

	public function downloadExcel($region, $date, $history, $cycle, $nearest, $price, $factor) {
		$this -> load -> database();
		$region_object = Region::getRegion($region);
		$data_buffer = "";
		$procurement_estimate_total = 0;
		$procurement_value_total = 0;
		$cash_balance_total = 0;
		$release_total = 0;
		$data_buffer .= "Zone:\t" . $region_object -> Region_Name . "\t\n";
		$data_buffer .= $this -> echoExcelTitles();
		//Get all the depots in this region
		$sql_funding = "select depot_summaries.*,(select sum(gross_value+free_farmer_value) from purchase where depot = depot_summaries.depot) as total_purchases,(select sum(amount) from field_cash_disbursement where depot = depot_summaries.depot) as total_received,(select sum(amount) from buying_center_receipt where depot = depot_summaries.depot) as total_returned from (select depot_purchase.*, max(str_to_date(date,'%m/%d/%Y')) as last_purchase_date,sum(quantity+free_farmer_quantity) as total_purchase, unit_price as last_price from (select d.id as depot,cash_disbursement_route as route,d.depot_name,quantity,free_farmer_quantity, date,unit_price from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on p.depot = d.id and str_to_date(date,'%m/%d/%Y') between DATE_SUB(str_to_date('" . $date . "','%m/%d/%Y'), INTERVAL " . $history . " day) and str_to_date('" . $date . "','%m/%d/%Y') where r.id = '" . $region . "' and d.deleted = '0' order by str_to_date(date,'%m/%d/%Y') desc) depot_purchase group by depot) depot_summaries";
		$bc_funding = $this -> db -> query($sql_funding);
		//Get data for each depot
		foreach ($bc_funding->result_array() as $depot_data) {
			$avg_per_day = round($depot_data['total_purchase'] / $history);
			$procurement_estimate = ($avg_per_day * $cycle);
			$procurement_value = ($procurement_estimate * $price);
			$cash_balance = ($depot_data['total_received'] - $depot_data['total_purchases'] - $depot_data['total_returned']);
			$release_amount = ($procurement_value - $cash_balance) * $factor;
			if ($release_amount > 0) {
				$release_amount = ceil($release_amount / $nearest) * $nearest;
			}
			$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['route'] . "\t" . $depot_data['last_purchase_date'] . "\t" . $depot_data['total_purchase'] . "\t" . $avg_per_day . "\t" . $procurement_estimate . "\t" . $depot_data['last_price'] . "\t" . $procurement_value . "\t" . $cash_balance . "\t" . $release_amount . "\t\n";
			$procurement_estimate_total += $procurement_estimate;
			$procurement_value_total += $procurement_value;
			$cash_balance_total += $cash_balance;
			$release_total += $release_amount;
		}
		$data_buffer .= "\nTotals: \t-\t-\t-\t-\t" . $procurement_estimate_total . "\t-\t" . $procurement_value_total . "\t" . $cash_balance_total . "\t" . $release_total . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=BC Funding.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Buying Center Funding Report Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<tr><th>Buying Center</th><th>Cash Route</th><th>Last Purchase Date</th><th>Historical Purchases</th><th>Avg. Kgs.</th><th>Projected Estimate</th><th>Projected Price</th><th>Procurement Value</th><th>Cash Balance</th><th>Release Amount</th></tr>";
	}

	public function echoExcelTitles() {
		return "Buying Center\tCash Route\tLast Purchase Date\tHistorical Purchases\tAvg. Kgs.\tProjected Estimate\tProjected Price\tProcurement Value\tCash Balance\tRelease Amount\t\n";
	}

	function generatePDF($data, $date, $history, $cycle, $nearest, $price,$factor) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>BC Funding</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . " for the next " . $cycle . " days using " . $history . " days historical data and " . $price . " as the projected price and a factor of ".$factor.". Rounded to the nearest " . number_format($nearest) . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('BC Funding');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "BC Funding.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
