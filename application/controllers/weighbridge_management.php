<?php
class Weighbridge_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> fetch_data();
	}

	public function search_ticket() {
		$search_term = $this -> input -> post("search_value7");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$ticket = Weighbridge::getSearchedTicket($db_search_term);
		$data['ticket'] = $ticket;
		$data['content_view'] = "list_weighbridge_ticket_search_results_v";
		$this -> load -> view("demo_template", $data);
	}

	public function upload_interface() {
		$data['title'] = "Weighbridge Management";
		$data['content_view'] = "weighbridge_upload_v";
		$data['quick_link'] = "add_account";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);
	}

	public function fetch_data() {
		$this -> load -> library('csvreader');

		$filePath = '/weigh.csv';
		$resource = @fopen($filePath, 'r');
		if (!$resource) {
			$data['content_view'] = "weighbridge_error";
			$data['link'] = "home";
			$this -> load -> view("demo_template", $data);
			return;
		}

		$data = $this -> csvreader -> parse_file($filePath, false);
		$records = 0;
		if ($data == true) {
			$records = count($data);
			foreach ($data as $row) {
				$weighbridge_data = new Weighbridge();
				$weighbridge_data -> Vehicle_Number = $row[0];
				$weighbridge_data -> Weighing_Mode = $row[1];
				$weighbridge_data -> Weighing_Type = $row[2];
				$weighbridge_data -> Charge_Type = $row[3];
				$weighbridge_data -> Vehicle_Type = $row[4];
				$weighbridge_data -> Buying_Center_Code = $row[5];
				$weighbridge_data -> Product_Code = $row[6];
				$weighbridge_data -> Cell = $row[7];
				$weighbridge_data -> Category = $row[8];
				$weighbridge_data -> Containment = $row[9];
				$weighbridge_data -> Transporter = $row[10];
				$weighbridge_data -> Destination = $row[11];
				$weighbridge_data -> UDF_1 = $row[12];
				$weighbridge_data -> Number_Of_Bags = $row[13];
				$weighbridge_data -> Conversion = $row[14];
				$weighbridge_data -> Adjusted_First_Weight = $row[15];
				$weighbridge_data -> Adjusted_Second_Weight = $row[16];
				$weighbridge_data -> UDF_6 = $row[17];
				$weighbridge_data -> UDF_7 = $row[18];
				$weighbridge_data -> UDF_8 = $row[19];
				$weighbridge_data -> Station_ID = $row[20];
				$weighbridge_data -> First_Weight = $row[21];
				$weighbridge_data -> First_Weight_Unit = $row[22];
				$weighbridge_data -> First_Weight_Date = $row[23];
				$weighbridge_data -> First_Weight_Time = $row[24];
				$weighbridge_data -> First_Weight_Consec_No = $row[25];
				$weighbridge_data -> First_Weight_ID = $row[26];
				$weighbridge_data -> Second_Weight = $row[27];
				$weighbridge_data -> Second_Weight_Unit = $row[28];
				$weighbridge_data -> Second_Weight_Date = $row[29];
				$weighbridge_data -> Second_Weight_Time = $row[30];
				$weighbridge_data -> Second_Weight_Consec_No = $row[31];
				$weighbridge_data -> Second_Weight_Id = $row[32];
				$weighbridge_data -> Net_Weight = $row[33];
				$weighbridge_data -> Adjusted_Net_Weight = $row[34];
				$weighbridge_data -> Transaction_Date = $row[35];
				$weighbridge_data -> Transaction_Time = $row[36];
				$weighbridge_data -> Ticket_Number = $row[37];
				$weighbridge_data -> Transaction_Number = $row[38];
				$weighbridge_data -> Total_Charge = $row[39];
				$weighbridge_data -> Operator = $row[40];
				$weighbridge_data -> save();
			}
		}
		$file = fopen($filePath, 'w');
		fclose($file);
		$data['records'] = $records;
		$this -> base_params($data);
		return;
	}

	function getDailyTrend($from = "", $to = "") {
		if ($from == "") {
			$from = date('d-m-Y', strtotime('-7 days', date('U')));
		}
		if ($to == "") {
			$to = date('d-m-Y');
		}
		$label_from = date('M-d-Y', strtotime($from));
		$label_to = date('M-d-Y', strtotime($to));
		$this -> load -> database();
		$sql = "SELECT sum(net_weight) as total_dispatches,transaction_date FROM `weighbridge` w where weighing_type = '2'  and str_to_date(transaction_date,'%d/%m/%Y') between str_to_date('" . $from . "','%d-%m-%Y') and str_to_date('" . $to . "','%d-%m-%Y') group by str_to_date(transaction_date,'%d/%m/%Y') order by str_to_date(transaction_date,'%d/%m/%Y') asc";
		echo $sql;
		$query = $this -> db -> query($sql);
		$purchasing_data = $query -> result_array();
		$chart = '<chart caption="Daily Dispatches Trend" subcaption="From ' . $label_from . ' to ' . $label_to . '" xAxisName="Day" yAxisName="Dispatches (Kgs.)" showValues="0" showBorder="0" showAlternateHGridColor="0" divLineAlpha="10"  bgColor="FFFFFF" exportEnabled="1" exportHandler="'.base_url().'Scripts/FusionCharts/ExportHandlers/PHP/FCExporter.php" exportAtClient="0" exportAction="download" >';
		foreach ($purchasing_data as $data) {
			$formatted_date = DateTime::createFromFormat('d/m/Y', $data['transaction_date']);
			$date = $formatted_date->format('M-d');
			$chart .= '<set label="' . $date . '" value="' . $data['total_dispatches'] . '"/>';
		}
		$chart .= '
		<styles>
<definition>
<style name="Anim1" type="animation" param="_xscale" start="0" duration="1"/>
<style name="Anim2" type="animation" param="_alpha" start="0" duration="0.6"/>
<style name="DataShadow" type="Shadow" alpha="40"/>
</definition>
<application>
<apply toObject="DIVLINES" styles="Anim1"/>
<apply toObject="HGRID" styles="Anim2"/>
<apply toObject="DATALABELS" styles="DataShadow,Anim2"/>
</application>
</styles>
		</chart>';
		echo $chart;
	}

	public function base_params($data) {
		$data['title'] = "Weighbridge Management";
		$data['content_view'] = "weighbridge_v";
		$data['quick_link'] = "add_account";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);
	}

}
