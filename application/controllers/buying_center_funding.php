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
			width: 700px;
			border-collapse:separate;
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
		//echo the start of the table
		$data_buffer .= "<h3>Zone: " . $region_object -> Region_Name . "</h3>";
		$data_buffer .= "<table class='data-table'>";
		$procurement_estimate_total = 0;
		$procurement_value_total = 0;
		$cash_balance_total = 0;
		$release_total = 0;

		$data_buffer .= $this -> echoTitles($history);

		//Get all the depots in this region
		$sql_funding = "select funding_prediction.* from (select funding.*,(((coalesce(total_purchase,0)/" . $history . ")*" . $cycle . "*" . $price . ") - coalesce(((coalesce(total_received) -(coalesce(total_purchases,0)+coalesce(total_returned,0)))),0)) as procurement_estimate from (select depot_summaries.*,(select sum(gross_value+free_farmer_value) from purchase where depot = depot_summaries.depot and batch_status = '2') as total_purchases,(select sum(amount) from field_cash_disbursement where depot = depot_summaries.depot and batch_status = '2') as total_received,(select sum(amount) from buying_center_receipt where depot = depot_summaries.depot and batch_status = '2') as total_returned from (select depot_purchase.*, date_format(max(str_to_date(date,'%m/%d/%Y')),'%d/%m/%Y') as last_purchase_date,sum(quantity+free_farmer_quantity) as total_purchase, unit_price as last_price from (select d.id as depot,cash_disbursement_route as route,d.depot_name,depot_code,quantity,free_farmer_quantity, date,unit_price from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on p.depot = d.id and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between DATE_SUB(str_to_date('" . $date . "','%m/%d/%Y'), INTERVAL " . $history . " day) and str_to_date('" . $date . "','%m/%d/%Y') where r.id = '" . $region . "' and d.deleted = '0' order by str_to_date(date,'%m/%d/%Y') desc) depot_purchase group by depot) depot_summaries) funding)funding_prediction order by cast(route as signed) asc,cast(procurement_estimate as signed) desc;";
		$bc_funding = $this -> db -> query($sql_funding);
		//Get data for each depot
		$current_route = 0;
		$route_projected = array(0);
		$route_procurement_value = array(0);
		$route_cash_balance = array(0);
		$route_release_amount = array(0);
		$row_counter = 0;
		$routes = array();
		foreach ($bc_funding->result_array() as $depot_data) {
			if ($row_counter == 0) {
				$current_route = $depot_data['route'];
				$routes[$row_counter] = $current_route;
				$route_projected[$depot_data['route']] = 0;
				$route_procurement_value[$depot_data['route']] = 0;
				$route_cash_balance[$depot_data['route']] = 0;
				$route_release_amount[$depot_data['route']] = 0;
			}
			$row_counter++;
			$avg_per_day = round($depot_data['total_purchase'] / $history);
			$procurement_estimate = ($avg_per_day * $cycle);
			$procurement_value = ($procurement_estimate * $price);
			$cash_balance = ($depot_data['total_received'] - $depot_data['total_purchases'] - $depot_data['total_returned']);
			$release_amount = ($procurement_value - $cash_balance) * $factor;
			if (strlen($release_amount) > 0) {
				$release_amount = ceil($release_amount / $nearest) * $nearest;
			}
			//If the route has changed, show a sub totals row
			if ($depot_data['route'] != $current_route) {
				$data_buffer .= "<tr><th>Sub-totals:</th><th>-</th><th>-</th><th>-</th><th>-</th><th>-</th><th class='amount'>" . (empty($route_projected[$current_route]) ? '-' : number_format($route_projected[$current_route] + 0)) . "</th><th>-</th><th class='amount'>" . (empty($route_procurement_value[$current_route]) ? '-' : number_format($route_procurement_value[$current_route] + 0)) . "</th><th class='amount'>" . (empty($route_cash_balance[$current_route]) ? '-' : number_format($route_cash_balance[$current_route] + 0)) . "</th><th class='amount'>" . (empty($route_release_amount[$current_route]) ? '-' : number_format($route_release_amount[$current_route] + 0)) . "</th></tr>";
				$current_route = $depot_data['route'];
				$routes[$row_counter] = $current_route;
				$route_projected[$depot_data['route']] = 0;
				$route_procurement_value[$depot_data['route']] = 0;
				$route_cash_balance[$depot_data['route']] = 0;
				$route_release_amount[$depot_data['route']] = 0;
			}

			//Update the subtotals for this route
			$route_projected[$depot_data['route']] += $procurement_estimate;
			$route_procurement_value[$depot_data['route']] += $procurement_value;
			$route_cash_balance[$depot_data['route']] += $cash_balance;
			$route_release_amount[$depot_data['route']] += $release_amount;

			$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td>" . $depot_data['depot_code'] . "</td><td>" . $depot_data['route'] . "</td><td>" . (empty($depot_data['last_purchase_date']) ? '-' : $depot_data['last_purchase_date']) . "</td><td class='amount'>" . (empty($depot_data['total_purchase']) ? '-' : number_format($depot_data['total_purchase'] + 0)) . "</td><td class='amount'>" . (empty($avg_per_day) ? '-' : number_format($avg_per_day + 0)) . "</td><td class='amount'>" . (empty($procurement_estimate) ? '-' : number_format($procurement_estimate + 0)) . "</td><td class='amount'>" . (empty($depot_data['last_price']) ? '-' : number_format($depot_data['last_price'] + 0)) . "</td><td class='amount'>" . (empty($procurement_value) ? '-' : number_format($procurement_value + 0)) . "</td><td class='amount'>" . (empty($cash_balance) ? '-' : number_format($cash_balance + 0)) . "</td><td class='amount'>" . (empty($release_amount) ? '0' : number_format($release_amount + 0)) . "</td></tr>";
			$procurement_estimate_total += $procurement_estimate;
			$procurement_value_total += $procurement_value;
			$cash_balance_total += $cash_balance;
			$release_total += $release_amount;
		}
		$data_buffer .= "<tr><th>Sub-totals:</th><th>-</th><th>-</th><th>-</th><th>-</th><th>-</th><th class='amount'>" . (empty($route_projected[$current_route]) ? '-' : number_format($route_projected[$current_route] + 0)) . "</th><th>-</th><th class='amount'>" . (empty($route_procurement_value[$current_route]) ? '-' : number_format($route_procurement_value[$current_route] + 0)) . "</th><th class='amount'>" . (empty($route_cash_balance[$current_route]) ? '-' : number_format($route_cash_balance[$current_route] + 0)) . "</th><th class='amount'>" . (empty($route_release_amount[$current_route]) ? '-' : number_format($route_release_amount[$current_route] + 0)) . "</th></tr>";
		$data_buffer .= "</table>";
		$data_buffer .= "<h3>Summaries</h3><table class='data-table'><tr><th>Route</th><th>Total Procurement Kgs.</th><th>Total Procurement Value</th><th>Total Cash Balance</th><th>Release Amount</th></tr>";
		foreach ($routes as $route) {
			$data_buffer .= "<tr><td class='center'>" . (empty($route) ? '-' : $route) . "</td><td class='amount'>" . (empty($route_projected[$route]) ? '-' : number_format($route_projected[$route] + 0)) . "</td><td class='amount'>" . (empty($route_procurement_value[$route]) ? '-' : number_format($route_procurement_value[$route] + 0)) . "</td><td class='amount'>" . (empty($route_cash_balance[$route]) ? '-' : number_format($route_cash_balance[$route] + 0)) . "</td><td class='amount'>" . (empty($route_release_amount[$route]) ? '-' : number_format($route_release_amount[$route] + 0)) . "</td></tr>";
		}$data_buffer .= "<tr><th>Grand Totals: </th><th class='amount'>" . (empty($procurement_estimate_total) ? '-' : number_format($procurement_estimate_total + 0)) . "</th><th class='amount'>" . (empty($procurement_value_total) ? '-' : number_format($procurement_value_total + 0)) . "</th><th class='amount'>" . (empty($cash_balance_total) ? '-' : number_format($cash_balance_total + 0)) . "</th><th class='amount'>" . (empty($release_total) ? '-' : number_format($release_total + 0)) . "</th></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Buying Center Funding Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//echo $data_buffer;
		$this -> generatePDF($data_buffer, $date, $history, $cycle, $nearest, $price, $factor);

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
		$data_buffer .= $this -> echoExcelTitles($history);
		//Get all the depots in this region
		$sql_funding = "select funding_prediction.* from (select funding.*,(((coalesce(total_purchase,0)/" . $history . ")*" . $cycle . "*" . $price . ") - coalesce(((coalesce(total_received) -(coalesce(total_purchases,0)+coalesce(total_returned,0)))),0)) as procurement_estimate from (select depot_summaries.*,(select sum(gross_value+free_farmer_value) from purchase where depot = depot_summaries.depot and batch_status = '2') as total_purchases,(select sum(amount) from field_cash_disbursement where depot = depot_summaries.depot and batch_status = '2') as total_received,(select sum(amount) from buying_center_receipt where depot = depot_summaries.depot and batch_status = '2') as total_returned from (select depot_purchase.*, date_format(max(str_to_date(date,'%m/%d/%Y')),'%d/%m/%Y') as last_purchase_date,sum(quantity+free_farmer_quantity) as total_purchase, unit_price as last_price from (select d.id as depot,cash_disbursement_route as route,d.depot_name,depot_code,quantity,free_farmer_quantity, date,unit_price from depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on p.depot = d.id and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between DATE_SUB(str_to_date('" . $date . "','%m/%d/%Y'), INTERVAL " . $history . " day) and str_to_date('" . $date . "','%m/%d/%Y') where r.id = '" . $region . "' and d.deleted = '0' order by str_to_date(date,'%m/%d/%Y') desc) depot_purchase group by depot) depot_summaries) funding)funding_prediction order by cast(route as signed) asc,cast(procurement_estimate as signed) desc;";
		$bc_funding = $this -> db -> query($sql_funding);
		//Get data for each depot
		$current_route = 0;
		$route_projected = array(0);
		$route_procurement_value = array(0);
		$route_cash_balance = array(0);
		$route_release_amount = array(0);
		$row_counter = 0;
		$routes = array();
		foreach ($bc_funding->result_array() as $depot_data) {
			if ($row_counter == 0) {
				$current_route = $depot_data['route'];
				$routes[$row_counter] = $current_route;
				$route_projected[$depot_data['route']] = 0;
				$route_procurement_value[$depot_data['route']] = 0;
				$route_cash_balance[$depot_data['route']] = 0;
				$route_release_amount[$depot_data['route']] = 0;
			}
			$row_counter++;
			$avg_per_day = round($depot_data['total_purchase'] / $history);
			$procurement_estimate = ($avg_per_day * $cycle);
			$procurement_value = ($procurement_estimate * $price);
			$cash_balance = ($depot_data['total_received'] - $depot_data['total_purchases'] - $depot_data['total_returned']);
			$release_amount = ($procurement_value - $cash_balance) * $factor;
			if (strlen($release_amount) > 0) {
				$release_amount = ceil($release_amount / $nearest) * $nearest;
			}
			//If the route has changed, show a sub totals row
			if ($depot_data['route'] != $current_route) {
				$data_buffer .= "Sub-totals:\t\t\t\t\t\t" . $route_projected[$current_route] . "\t\t" . $route_procurement_value[$current_route] . "\t" . $route_cash_balance[$current_route] . "\t" . $route_release_amount[$current_route]. "\t\n\n";
				$current_route = $depot_data['route'];
				$routes[$row_counter] = $current_route;
				$route_projected[$depot_data['route']] = 0;
				$route_procurement_value[$depot_data['route']] = 0;
				$route_cash_balance[$depot_data['route']] = 0;
				$route_release_amount[$depot_data['route']] = 0;
			}

			//Update the subtotals for this route
			$route_projected[$depot_data['route']] += $procurement_estimate;
			$route_procurement_value[$depot_data['route']] += $procurement_value;
			$route_cash_balance[$depot_data['route']] += $cash_balance;
			$route_release_amount[$depot_data['route']] += $release_amount;

			$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['depot_code'] . "\t" . $depot_data['route'] . "\t" . $depot_data['last_purchase_date'] . "\t" . $depot_data['total_purchase'] . "\t" . $avg_per_day . "\t" . $procurement_estimate . "\t" . $depot_data['last_price'] . "\t" . $procurement_value . "\t" . $cash_balance . "\t" . $release_amount . "\t\n";
			$procurement_estimate_total += $procurement_estimate;
			$procurement_value_total += $procurement_value;
			$cash_balance_total += $cash_balance;
			$release_total += $release_amount;
		}
		$data_buffer .= "\nSub-totals:\t\t\t\t\t\t" . $route_projected[$current_route] . "\t\t" . $route_procurement_value[$current_route] . "\t" . $route_cash_balance[$current_route] . "\t" . $route_release_amount[$current_route]. "\t\n";
		$data_buffer .= "\nSummaries\nRoute\tTotal Procurement Kgs.\tTotal Procurement Value\tTotal Cash Balance\tRelease Amount\t\n";
		foreach ($routes as $route) {
			$data_buffer .= (empty($route) ? '-' : $route) . "\t" .$route_projected[$route] . "\t" . $route_procurement_value[$route] . "\t" . $route_cash_balance[$route]. "\t" . $route_release_amount[$route] . "\n";
		}$data_buffer .= "\nGrand Totals: \t" . $procurement_estimate_total . "\t" . $procurement_value_total . "\t" . $cash_balance_total . "\t" . $release_total . "\n";
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

	public function echoTitles($history) {
		return "<thead><tr><th>BC Name</th><th>BC Code</th><th>Cashier Route</th><th>Last Purchase Date</th><th>Last $history Days Purchases</th><th>Avg./Day</th><th>Procurement Kgs.</th><th>Projected Price</th><th>Procurement Value</th><th>Current Cash Balance</th><th>Release Amount</th></tr></thead>";
	}

	public function echoExcelTitles($history) {
		return "BC Name\tBC Code\tCashier Route\tLast Purchase Date\tLast $history Days Purchases\tAvg./Day\tProcurement Kgs.\tProjected Price\tProcurement Value\tCurrent Cash Balance\tRelease Amount\t\n";
	}

	function generatePDF($data, $date, $history, $cycle, $nearest, $price, $factor) {
		$date = date('d/m/Y', strtotime($date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>BC Funding</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . " for the next " . $cycle . " days using " . $history . " days historical data and " . $price . " as the projected price and a factor of " . $factor . ". Rounded to the nearest " . number_format($nearest) . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('BC Funding');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|{PAGENO}|BC Funding Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */
		$this -> mpdf -> WriteHTML($html_title);
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
