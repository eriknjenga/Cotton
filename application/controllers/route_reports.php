<?php
class Route_Reports extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_collections_interface();
	}

	public function view_collections_interface($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['routes'] = Route::getAll();
		$data['content_view'] = "route_collections_v";
		$data['quick_link'] = "collections_interface";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$routes = array();
		$route = $this -> input -> post("route");
		$action = $this -> input -> post("action");

		$date = date("m/d/Y");
		if ($route == 0) {
			//Get the region
			$routes = Route::getAll();
		} else {
			$routes = Route::getRouteArray($route);
		}
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Route Collections Excel") {
			$this -> downloadExcel($routes);
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
			width: 120px;
			}
			</style>
			";
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$route_summaries = array();
		foreach ($routes as $route) {
			$route_summaries = array();
			$route_summaries['total_purchases'] = 0;
			$route_summaries['total_dispatched'] = 0;
			$route_summaries['total_stock_balance'] = 0;
			$data_buffer .= "<tr><td><b>Route: </b></td><td><b>" . $route -> Route_Name . "</b></td></tr>";
			$data_buffer .= $this -> echoTitles();
			//Get data for each depot
			foreach ($route->Route_Depot_Objects as $route_depot) {
				if ($route_depot -> Deleted == "0") {
					$sql = "select center_details.*,(total_purchased-total_dispatched) as stock_balance from (select d.id, depot_code, depot_name, capacity, distance,coalesce(sum(quantity+free_farmer_quantity),0) as total_purchased,max(str_to_date(date,'%m/%d/%Y'))as last_purchase_date, (select coalesce(sum(net_weight),0) as total_dispatched from weighbridge w where w.buying_center_code = d.depot_code and w.weighing_type = '2') as total_dispatched from depot d  left join purchase p on p.depot = d.id and p.batch_status = '2' where d.id = '" . $route_depot -> id . "') center_details";
					$query = $this -> db -> query($sql);
					$depot_data = $query -> row_array();
					$data_buffer .= "<tr><td>" . $depot_data['depot_code'] . "</td><td>" . $depot_data['depot_name'] . "</td><td>" . $depot_data['distance'] . "</td><td>" . $depot_data['last_purchase_date'] . "</td><td>" . number_format($depot_data['total_purchased'] + 0) . "</td><td>" . number_format($depot_data['total_dispatched'] + 0) . "</td><td>" . number_format($depot_data['stock_balance'] + 0) . "</td><td>" . number_format(($depot_data['capacity'] * 1000) + 0) . "</td></tr>";
					$route_summaries['total_purchases'] += $depot_data['total_purchased'];
					$route_summaries['total_dispatched'] += $depot_data['total_dispatched'];
					$route_summaries['total_stock_balance'] += $depot_data['stock_balance'];
				}
			}
			$data_buffer .= "<tr></tr><tr><td><b>Totals</b></td><td>-</td><td>-</td><td>-</td><td>" . number_format($route_summaries['total_purchases'] + 0) . "</td><td>" . number_format($route_summaries['total_dispatched'] + 0) . "</td><td>" . number_format($route_summaries['total_stock_balance'] + 0) . "</td><td>-</td></tr>";
		}

		$data_buffer .= "</table>";
		//echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Route Collections PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date);

	}

	public function downloadExcel($routes) {
		$this -> load -> database();
		$data_buffer = "";
		//echo the start of the table
		$route_summaries = array();
		foreach ($routes as $route) {
			$route_summaries = array();
			$route_summaries['total_purchases'] = "";
			$route_summaries['total_dispatched'] = "";
			$route_summaries['total_stock_balance'] = "";
			$data_buffer .= "Route: \t" . $route -> Route_Name . "\n";
			$data_buffer .= $this -> echoExcelTitles();
			//Get data for each depot
			foreach ($route->Route_Depot_Objects as $route_depot) {
				if ($route_depot ->  Deleted == "0") {
					$sql = "select center_details.*,(total_purchased-total_dispatched) as stock_balance from (select d.id, depot_code, depot_name, capacity, distance,coalesce(sum(quantity+free_farmer_quantity),0) as total_purchased,max(str_to_date(date,'%m/%d/%Y'))as last_purchase_date, (select coalesce(sum(net_weight),0) as total_dispatched from weighbridge w where w.buying_center_code = d.depot_code and w.weighing_type = '2') as total_dispatched from depot d  left join purchase p on p.depot = d.id and p.batch_status = '2' where d.id = '" . $route_depot -> id . "') center_details";
					$query = $this -> db -> query($sql);
					$depot_data = $query -> row_array();
					$data_buffer .= $depot_data['depot_code'] . "\t" . $depot_data['depot_name'] . "\t" . $depot_data['distance'] . "\t" . $depot_data['last_purchase_date'] . "\t" . $depot_data['total_purchased'] . "\t" . $depot_data['total_dispatched'] . "\t" . $depot_data['stock_balance'] . "\t" . ($depot_data['capacity'] * 1000) . "\t\n";
					$route_summaries['total_purchases'] += $depot_data['total_purchased'];
					$route_summaries['total_dispatched'] += $depot_data['total_dispatched'];
					$route_summaries['total_stock_balance'] += $depot_data['stock_balance'];
				}
			}
			$data_buffer .= "\nTotals\t-\t-\t-\t" . $route_summaries['total_purchases'] . "\t" . $route_summaries['total_dispatched'] . "\t" . $route_summaries['total_stock_balance'] . "\t-\t\n\n";
		}

		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Route Collection Summaries.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Route Collections Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<tr><th>Code</th><th>Buying Center</th><th>Distance (KMS)</th><th>Date of Last Purchase</th><th>Purchases (KGs)</th><th>Dispatched (KGs)</th><th>Stock Balance (KGs)</th><th>Capacity (KGs)</th></tr>";
	}

	public function echoExcelTitles() {
		return "Code\tBuying Center\tDistance (KMS)\tDate of Last Purchase\tPurchases (KGs)\tDispatched (KGs)\tStock Balance (KGs)\tCapacity (KGs)\t\n";
	}

	function generatePDF($data, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Route Collection Summaries</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('Route Collection Summaries');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Route Collection Summaries.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
