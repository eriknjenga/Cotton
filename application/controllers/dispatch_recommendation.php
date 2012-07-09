<?php
class Dispatch_Recommendation extends MY_Controller {
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
		$data['content_view'] = "dispatch_recommendation_v";
		$data['quick_link'] = "dispatch_recommendation";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$action = $this -> input -> post("action");
		$date = $this -> input -> post("date");
		$maximum_stock = $this -> input -> post("maximum_stock");
		$capacity_percentage = $this -> input -> post("capacity_percentage");

		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Dispatch Recommendation Excel") {
			$this -> downloadExcel($date, $maximum_stock, $capacity_percentage);
			return;
		}
		$capacity_percentage = $capacity_percentage / 100;
		$maximum_stock = $maximum_stock * 1000;
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
		$total_balance = 0;
		$data_buffer .= $this -> echoTitles();
		//Get data for each zone
		$sql = "select recommendation.*,datediff(str_to_date('" . $date . "','%m/%d/%Y'),last_dispatch_date) as days_since_dispatch,datediff(str_to_date('" . $date . "','%m/%d/%Y'),date_closed) as days_since_closure from (select depot_dispatches.*,(total_purchased-total_dispatched) as product_balance,str_to_date(date_closed,'%m/%d/%Y') as date_closed from(select depot_purchases.* ,coalesce(sum(net_weight),0) as total_dispatched,max(str_to_date(transaction_date,'%d/%m/%Y')) as last_dispatch_date from (select d.id as depot_id,d.depot_code,d.depot_name,d.capacity,v.name as village,w.name as ward,r.region_name as zone,coalesce(sum(quantity),0) as total_purchased,max(str_to_date(date,'%m/%d/%Y')) as last_purchase_date from  depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on p.depot = d.id where d.deleted != '1' group by d.id) depot_purchases left join weighbridge w on w.buying_center_code = depot_code and w.weighing_type='2' group by depot_id) depot_dispatches left join depot_closure dc on depot_id = dc.depot  having product_balance>" . $maximum_stock . " or product_balance>(capacity*1000*" . $capacity_percentage . ") or date_closed>last_dispatch_date) recommendation order by days_since_dispatch desc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$days_pending = '';
			if ($depot_data['days_since_closure'] != null) {
				$days_pending = $depot_data['days_since_closure'];
				$days_pending += 1;
			} else if ($depot_data['days_since_dispatch'] != null) {
				$days_pending = $depot_data['days_since_dispatch'];
				$days_pending += 1;
			}
			$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td>" . $depot_data['village'] . "</td><td>" . $depot_data['ward'] . "</td><td>" . $depot_data['zone'] . "</td><td>" . $depot_data['capacity'] . "</td><td>" . number_format($depot_data['product_balance'] + 0) . "</td><td>" . $depot_data['last_dispatch_date'] . "</td><td>" . $depot_data['date_closed'] . "</td><td>" . $days_pending . "</td></tr>";
			$total_balance += $depot_data['product_balance'];
		}
		$data_buffer .= "<tr><td>Totals: </td><td>-</td><td>-</td><td>-</td><td>-</td><td>" . number_format($total_balance + 0) . "</td><td>-</td><td>-</td><td>-</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Dispatch Recommendation Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date, $maximum_stock, $capacity_percentage);

	}

	public function downloadExcel($date, $maximum_stock, $capacity_percentage) {
		$this -> load -> database();
		$data_buffer = "";
		$capacity_percentage = $capacity_percentage / 100;
		$maximum_stock = $maximum_stock * 1000;
		$this -> load -> database();
		$data_buffer = "";
		//echo the start of the table
		$total_balance = 0;
		$data_buffer .= $this -> echoExcelTitles();
		//Get data for each zone
		$sql = "select recommendation.*,datediff(str_to_date('" . $date . "','%m/%d/%Y'),last_dispatch_date) as days_since_dispatch,datediff(str_to_date('" . $date . "','%m/%d/%Y'),date_closed) as days_since_closure from (select depot_dispatches.*,(total_purchased-total_dispatched) as product_balance,str_to_date(date_closed,'%m/%d/%Y') as date_closed from(select depot_purchases.* ,coalesce(sum(net_weight),0) as total_dispatched,max(str_to_date(transaction_date,'%d/%m/%Y')) as last_dispatch_date from (select d.id as depot_id,d.depot_code,d.depot_name,d.capacity,v.name as village,w.name as ward,r.region_name as zone,coalesce(sum(quantity),0) as total_purchased,max(str_to_date(date,'%m/%d/%Y')) as last_purchase_date from  depot d left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id left join purchase p on p.depot = d.id where d.deleted != '1' group by d.id) depot_purchases left join weighbridge w on w.buying_center_code = depot_code and w.weighing_type='2' group by depot_id) depot_dispatches left join depot_closure dc on depot_id = dc.depot  having product_balance>" . $maximum_stock . " or product_balance>(capacity*1000*" . $capacity_percentage . ") or date_closed>last_dispatch_date) recommendation order by days_since_dispatch desc";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$days_pending = '';
			if ($depot_data['days_since_closure'] != null) {
				$days_pending = $depot_data['days_since_closure'];
				$days_pending += 1;
			} else if ($depot_data['days_since_dispatch'] != null) {
				$days_pending = $depot_data['days_since_dispatch'];
				$days_pending += 1;
			}

			$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['village'] . "\t" . $depot_data['ward'] . "\t" . $depot_data['zone'] . "\t" . $depot_data['capacity'] . "\t" . $depot_data['product_balance'] . "\t" . $depot_data['last_dispatch_date'] . "\t" . $depot_data['date_closed'] . "\t" . $days_pending . "\t\n";
			$total_balance += $depot_data['product_balance'];
		}
		$data_buffer .= "Totals: \t-\t-\t-\t-\t" . $total_balance . "\t-\t-\t-\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Dispatch Recommendation.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Buying Center Dispatch Recommendation Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<tr><th>Buying Center</th><th>Village</th><th>Ward</th><th>Zone</th><th>Capacity</th><th>Product Balance</th><th>Last Dispatch Date</th><th>Closed On</th><th>Days Pending</th></tr>";
	}

	public function echoExcelTitles() {
		return "Buying Center\tVillage\tWard\tZone\tCapacity\tProduct Balance\tLast Dispatch Date\tClosed On\tDays Pending\t\n";
	}

	function generatePDF($data, $date, $capacity, $percentage) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Buying Center Dispatch Recommendation</h3>";
		$html_title .= "<h5 style='text-align:center;'> Generated on " . $date . " using: " . ($capacity / 1000) . " Tonnes as Maximum Stock Balance and " . ($percentage * 100) . "% as occupied center capacity</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('Zonal Summaries');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Buying Center Dispatch Recommendation.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
