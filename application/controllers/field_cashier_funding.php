<?php
class Field_Cashier_Funding extends MY_Controller {
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
		$data['cashiers'] = Field_Cashier::getAll();
		$data['content_view'] = "field_cashier_funding_v";
		$data['quick_link'] = "field_cashier_funding";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {

		$date = $this -> input -> post("date");
		$cashier = $this -> input -> post("cashier");
		$history = $this -> input -> post("history");
		$cycle = $this -> input -> post("cycle");
		$price = $this -> input -> post("price");
		$action = $this -> input -> post("action");
		$nearest = $this -> input -> post("nearest");
		$cashier_object = Field_Cashier::getFieldCashier($cashier);
		//Check if the user requested an excel sheet; if so, call the responsible function
		if ($action == "Download Field Cashier Funding Excel") {
			$this -> downloadExcel($cashier, $date, $history, $cycle, $nearest, $price);
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
		//echo the start of the table
		$data_buffer .= "<table class='data-table'>";
		$procurement_estimate_total = 0;
		$procurement_value_total = 0;
		$cash_balance_total = 0;
		$release_total = 0;
		$data_buffer .= "<tr><td><b>Field Cashier: </b></td><td><b>" . $cashier_object -> Field_Cashier_Name . "</b></td></tr>";
		$data_buffer .= $this -> echoTitles();
		//Get all the depots in this region
		$sql_funding = "select funding_prediction.* from (select funding.*,(((coalesce(total_purchase,0)/" . $history . ")*" . $cycle . "*" . $price . ") - coalesce(((coalesce(total_received) -(coalesce(total_purchases,0)+coalesce(total_returned,0)))),0)) as procurement_estimate from (select depot_summaries.*,(select sum(gross_value+free_farmer_value) from purchase where depot = depot_summaries.depot and batch_status = '2') as total_purchases,(select sum(amount) from field_cash_disbursement where depot = depot_summaries.depot and batch_status = '2') as total_received,(select sum(amount) from buying_center_receipt where depot = depot_summaries.depot and batch_status = '2') as total_returned from (select depot_purchase.*, max(str_to_date(date,'%m/%d/%Y')) as last_purchase_date,sum(quantity+free_farmer_quantity) as total_purchase, unit_price as last_price from (select d.id as depot,cash_disbursement_route as route,d.depot_name,d.depot_code,quantity,free_farmer_quantity, date,unit_price from depot d left join cash_route on d.cash_disbursement_route = cash_route.id left join field_cashier f on cash_route.field_cashier = f.id left join purchase p on p.depot = d.id and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between DATE_SUB(str_to_date('" . $date . "','%m/%d/%Y'), INTERVAL " . $history . " day) and str_to_date('" . $date . "','%m/%d/%Y') where f.id = '" . $cashier . "' and d.deleted = '0' order by str_to_date(date,'%m/%d/%Y') desc) depot_purchase group by depot) depot_summaries) funding)funding_prediction order by cast(procurement_estimate as signed) desc;";
		$bc_funding = $this -> db -> query($sql_funding);
		//Get data for each depot
		foreach ($bc_funding->result_array() as $depot_data) {
			$avg_per_day = round($depot_data['total_purchase'] / $history);
			$procurement_estimate = ($avg_per_day * $cycle);
			$procurement_value = ($procurement_estimate * $price);
			$cash_balance = ($depot_data['total_received'] - $depot_data['total_purchases'] - $depot_data['total_returned']);
			$release_amount = $procurement_value - $cash_balance;
			if (strlen($release_amount) > 0) {
				$release_amount = ceil($release_amount / $nearest) * $nearest;
			}
			$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td class='center'>" . $depot_data['depot_code'] . "</td><td class='center'>" . $depot_data['route'] . "</td><td class='amount'>" . number_format($release_amount + 0) . "</td></tr>";
			$procurement_estimate_total += $procurement_estimate;
			$procurement_value_total += $procurement_value;
			$cash_balance_total += $cash_balance;
			$release_total += $release_amount;
		}
		$data_buffer .= "<tr></tr><tr><td>Totals: </td><td class='center'>-</td><td class='center'>-</td><td class='amount'>" . number_format($release_total + 0) . "</td></tr>";
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Field Cashier Funding Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $date, $history, $cycle, $nearest, $price);
	}

	public function downloadExcel($cashier, $date, $history, $cycle, $nearest, $price) {
		$this -> load -> database();
		$cashier_object = Field_Cashier::getFieldCashier($cashier);
		$data_buffer = "";
		$procurement_estimate_total = 0;
		$procurement_value_total = 0;
		$cash_balance_total = 0;
		$release_total = 0;

		$data_buffer .= "Field Cashier: \t" . $cashier_object -> Field_Cashier_Name . "\t\n";
		$data_buffer .= $this -> echoExcelTitles();
		//Get all the depots in this region
		$sql_funding = "select funding_prediction.* from (select funding.*,(((coalesce(total_purchase,0)/" . $history . ")*" . $cycle . "*" . $price . ") - coalesce(((coalesce(total_received) -(coalesce(total_purchases,0)+coalesce(total_returned,0)))),0)) as procurement_estimate from (select depot_summaries.*,(select sum(gross_value+free_farmer_value) from purchase where depot = depot_summaries.depot and batch_status = '2') as total_purchases,(select sum(amount) from field_cash_disbursement where depot = depot_summaries.depot and batch_status = '2') as total_received,(select sum(amount) from buying_center_receipt where depot = depot_summaries.depot and batch_status = '2') as total_returned from (select depot_purchase.*, max(str_to_date(date,'%m/%d/%Y')) as last_purchase_date,sum(quantity+free_farmer_quantity) as total_purchase, unit_price as last_price from (select d.id as depot,cash_disbursement_route as route,d.depot_name,d.depot_code,quantity,free_farmer_quantity, date,unit_price from depot d left join cash_route on d.cash_disbursement_route = cash_route.id left join field_cashier f on cash_route.field_cashier = f.id left join purchase p on p.depot = d.id and batch_status = '2' and str_to_date(date,'%m/%d/%Y') between DATE_SUB(str_to_date('" . $date . "','%m/%d/%Y'), INTERVAL " . $history . " day) and str_to_date('" . $date . "','%m/%d/%Y') where f.id = '" . $cashier . "' and d.deleted = '0' order by str_to_date(date,'%m/%d/%Y') desc) depot_purchase group by depot) depot_summaries) funding)funding_prediction order by cast(procurement_estimate as signed) desc;";
		$bc_funding = $this -> db -> query($sql_funding);
		//Get data for each depot
		foreach ($bc_funding->result_array() as $depot_data) {
			$avg_per_day = round($depot_data['total_purchase'] / $history);
			$procurement_estimate = ($avg_per_day * $cycle);
			$procurement_value = ($procurement_estimate * $price);
			$cash_balance = ($depot_data['total_received'] - $depot_data['total_purchases'] - $depot_data['total_returned']);
			$release_amount = $procurement_value - $cash_balance;
			if (strlen($release_amount) > 0) {
				$release_amount = ceil($release_amount / $nearest) * $nearest;
			}
			$data_buffer .= $depot_data['depot_name'] . "\t" . $depot_data['depot_code'] . "\t" . $depot_data['route'] . "\t" . $release_amount . "\t\n";
			$procurement_estimate_total += $procurement_estimate;
			$procurement_value_total += $procurement_value;
			$cash_balance_total += $cash_balance;
			$release_total += $release_amount;
		}
		$data_buffer .= "\nTotals: \t-\t-\t" . $release_total . "\t\n";
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=Field Cashier Funding.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Field Cashier Funding Report Excel Sheet";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

	}

	public function echoTitles() {
		return "<thead><tr><th>BC Name</th><th>BC Code</th><th>Route</th><th>Estimated Release Amount</th></tr></thead>";
	}

	public function echoExcelTitles() {
		return "BC Name\tBC Code\tRoute\tEstimated Release Amount\t\n";
	}

	function generatePDF($data, $date, $history, $cycle, $nearest, $price) {
		$date = date('d/m/Y', strtotime($date));
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-40px;'>Field Cashier Funding</h3>";
		$html_title .= "<h5 style='text-align:center;'> as at: " . $date . " for the next " . $cycle . " days using " . $history . " days historical data and " . $price . " as the forecasted price. Rounded to the nearest " . number_format($nearest) . "</h5>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Field Cashier Funding');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|{PAGENO}|Field Cashier Funding Report');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Field Cashier Funding.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
