<?php
class Missing_Dpn extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {
		$this -> view_interface();
	}

	public function view_interface($data = null) {
		$this -> load -> database();
		if ($data == null) {
			$data = array();
		}
		$data['depots'] = Depot::getAll();
		$sql = "select distinct season from purchase where batch_status = '2'";
		$query = $this -> db -> query($sql);
		$data['seasons'] = $query -> result_array();
		$data['content_view'] = "missing_dpn_v";
		$data['quick_link'] = "missing_dpn";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function download() {
		$regions = array();
		$depot = $this -> input -> post("depot");
		$season = $this -> input -> post("season");
		$start = $this -> input -> post("start");
		$end = $this -> input -> post("end");
		$start -= 1;
		$date = date("m/d/Y");
		$this -> load -> database();
		$depot_object = Depot::getDepot($depot);
		//Retrieve all buying centers that have started reporting
		$sql_reporting_depots = "select * from (select distinct depot from purchase where season = '$season') reported_depots left join depot d on reported_depots.depot = d.id left join village v on d.village = v.id left join ward w on v.ward = w.id left join region r on w.region = r.id order by r.id asc";
		$data_buffer = "";
		//echo the start of the table
		$data_buffer .= "<h3>Buying Center: " . $depot_object -> Depot_Name . "</h3>";
		$data_buffer .= "<table class='data-table'>";
		$total_purchased = 0;
		$total_dispatched = 0;
		$total_balance = 0;
		$data_buffer .= $this -> echoTitles();
		//Get data for each zone
		//$sql = "select sequence_numbers from (select (@start_sq := @start_sq +1) as sequence_numbers  from dps_sequence,(select @start_sq := $start) s  where @start_sq < $end) sequence where sequence_numbers not in (select dpn from purchase p where depot = '$depot' and season = '$season')";
		$sql = "select sequence_numbers from (select (@start_sq := @start_sq +1) as sequence_numbers from dps_sequence,(select @start_sq := $start) s where @start_sq < (select max(abs(dpn)) from purchase where depot = '$depot' and season = '$season')) sequence where sequence_numbers not in (select dpn from purchase p where depot = '$depot' and season = '$season'  and dpn>start_sq)";
		$query = $this -> db -> query($sql);
		foreach ($query->result_array() as $depot_data) {
			$data_buffer .= "<tr><td>" . $depot_data['sequence_numbers'] . "</td></tr>";
		}
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Missing Buying Center DPS Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		//$this -> generatePDF($data_buffer, $start, $end, $date);

	}

	public function echoTitles() {
		return "<tr><th>Missing DPS Numbers</th></tr>";
	}

	function generatePDF($data, $start, $end, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Missing DPNs</h3>";
		$html_title .= "<h5 style='text-align:center;'> Sequence Starting " . ($start + 1) . " to " . $end . " as at " . $date . "</h5>";
		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4');
		$this -> mpdf -> SetTitle('Missing DPNs');
		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "Missing DPNs.pdf";
		$this -> mpdf -> Output($report_name, 'D');
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
