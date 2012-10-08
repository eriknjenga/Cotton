<?php
class Production_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> fetch_data();
	}

	public function search_lot_number() {
		$search_term = $this -> input -> post("search_value7");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$ticket = Production_Data::getSearchedLotNumber($db_search_term);
		$data['ticket'] = $ticket;
		$data['content_view'] = "list_lot_number_search_results_v";
		$this -> load -> view("demo_template", $data);
	}

	public function upload_interface() {
		$data['title'] = "Production Management";
		$data['content_view'] = "production_upload_v";
		$data['quick_link'] = "add_account";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);
	}

	public function fetch_data() {
		$this -> load -> library('csvreader');

		$filePath1 = '/sg.csv';
		$filePath2 = '/dr.csv';
		$resource1 = @fopen($filePath1, 'r');
		$resource2 = @fopen($filePath2, 'r');
		if (!$resource1 || !$resource2) {
			$data['content_view'] = "production_error";
			$data['link'] = "home";
			$this -> load -> view("demo_template", $data);
			return;
		}
		//start with the sow gin
		$data1 = $this -> csvreader -> parse_file($filePath1, false);
		$records1 = 0;
		if ($data1 == true) {
			$records = count($data1);
			foreach ($data1 as $row) {
				$production_data = new Production_Data();
				$production_data -> Ginnery = '1';
				$production_data -> Date = $row[0];
				$production_data -> Time = $row[1];
				$production_data -> Lot_Number = $row[2];
				$production_data -> Consecutive_Number = $row[3];
				$production_data -> Gross_Weight = $row[4];
				$production_data -> save();
			}
		}
		$file1 = fopen($filePath1, 'w');
		fclose($file1);
		$data['sow_records'] = $records1;
		//then the other ginnery
		$data2 = $this -> csvreader -> parse_file($filePath2, false);
		$records2 = 0;
		if ($data2 == true) {
			$records = count($data2);
			foreach ($data2 as $row) {
				$production_data = new Production_Data();
				$production_data -> Ginnery = '2';
				$production_data -> Date = $row[0];
				$production_data -> Time = $row[1];
				$production_data -> Lot_Number = $row[2];
				$production_data -> Consecutive_Number = $row[3];
				$production_data -> Gross_Weight = $row[4];
				$production_data -> save();
			}
		}
		$file2 = fopen($filePath2, 'w');
		fclose($file2);
		$data['other_records'] = $records2;
		$this -> base_params($data);
		return;
	}

	function getDailyTrend($type, $from = "", $to = "") {
		if ($from == "") {
			$from = date('d-m-Y', strtotime('-7 days', date('U')));
		}
		if ($to == "") {
			$to = date('d-m-Y');
		}
		$ginneries = array(1 => "Sow Gin", 2 => "Roller Gin");
		$label_from = date('M-d-Y', strtotime($from));
		$label_to = date('M-d-Y', strtotime($to));
		$this -> load -> database();
		$sql = "SELECT count(*) as total_bales,date FROM `production_data` p where ginnery = '" . $type . "'  and str_to_date(p.date,'%m/%d/%Y') between str_to_date('" . $from . "','%d-%m-%Y') and str_to_date('" . $to . "','%d-%m-%Y') group by str_to_date(p.date,'%m/%d/%Y') order by str_to_date(p.date,'%m/%d/%Y') asc";
		echo $sql;
		$query = $this -> db -> query($sql);
		$production_data = $query -> result_array();
		$chart = '<chart caption="Daily '.$ginneries[$type].' Production Trend" subcaption="From ' . $label_from . ' to ' . $label_to . '" xAxisName="Day" yAxisName="Purchases (Tsh.)" showValues="0" showBorder="0" showAlternateHGridColor="0" divLineAlpha="10"  bgColor="FFFFFF"  exportEnabled="1" exportHandler="' . base_url() . 'Scripts/FusionCharts/ExportHandlers/PHP/FCExporter.php" exportAtClient="0" exportAction="download">';
		foreach ($production_data as $data) {
			$date = date('M-d', strtotime($data['date']));
			$chart .= '<set label="' . $date . '" value="' . $data['total_bales'] . '"/>';
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
		$data['content_view'] = "production_v";
		$data['quick_link'] = "add_account";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);
	}

}
