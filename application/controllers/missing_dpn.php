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
		$season = $this -> input -> post("season");
		$date = date("m/d/Y");
		$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 700px;
			}
			table.data-table td {
			width: 200px; 
			}
			</style>
			";
		$data_buffer .= "<table class='data-table'><tr><th>Buying Center</th><th>Center Code</th><th>Missing Sequence Numbers</th></tr>";
		$this -> load -> database();
		//Retrieve all buying centers that have started reporting
		$sql_reporting_depots = "select * from (select depot,max(abs(dpn)) as dpn from purchase where season = '$season' group by depot) reported_depots left join depot d on reported_depots.depot = d.id order by depot_name asc";
		$query = $this -> db -> query($sql_reporting_depots);

		//Loop through all the returned depots
		foreach ($query->result_array() as $depot_data) {
			if (strlen($depot_data['depot']) > 0) {
				//echo the start of the table

				//sql to get the current book being used
				$sql_get_depot_sequence = "select * from dpn_sequence where season = '" . $season . "' and '" . $depot_data['dpn'] . "' between first and last and depot = '" . $depot_data['depot'] . "'";
				
				$query = $this -> db -> query($sql_get_depot_sequence);
				$sequence_data = $query -> row_array();
				if (isset($sequence_data['first'])) {
					$sql = "select sequence_numbers from (select (@start_sq := @start_sq +1) as sequence_numbers from dps_sequence,(select @start_sq := " . $sequence_data['first'] . ") s where @start_sq < " . $depot_data['dpn'] . ") sequence where sequence_numbers not in (select dpn from purchase p where depot = '" . $depot_data['depot'] . "' and season = '$season'  and dpn>" . $sequence_data['first'] . ")";
					
					$query = $this -> db -> query($sql);
					if ($query -> num_rows() > 0) {
						$data_buffer .= "<tr><td>" . $depot_data['depot_name'] . "</td><td>" . $depot_data['depot_code'] . "</td><td>";
						foreach ($query->result_array() as $missing_data) {
							$data_buffer .= $missing_data['sequence_numbers'] . ", ";
						}
						$data_buffer .= "</td></tr>";
					}
				}
			}
		}
		$data_buffer .= "</table>";
		$log = new System_Log();
		$log -> Log_Type = "4";
		$log -> Log_Message = "Downloaded Missing Buying Center DPN Report PDF";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$this -> generatePDF($data_buffer, $season, $date);

	}

	public function echoTitles() {
		return "<tr><th>Missing DPS Numbers</th></tr>";
	}

	function generatePDF($data, $season, $date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>Missing DPNs</h3>";
		$html_title .= "<h5 style='text-align:center;'> For the " . $season . " Season as at " . $date . "</h5>";
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
