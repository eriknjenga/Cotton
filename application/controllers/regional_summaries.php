<?php
class Regional_Summaries extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
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

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$details_desc = "{Code: '" . $this -> input -> post("buyer_code") . "' Name: '" . $this -> input -> post("name") . "' Phone Number: '" . $this -> input -> post("phone_number") . "' National ID: '" . $this -> input -> post("national_id") . "'}";
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$buyer = Buyer::getBuyer($editing);
				$log -> Log_Type = "2";
				$log -> Log_Message = "Edited Buyer Record From {Code: '" . $buyer -> Buyer_Code . "' Name: '" . $buyer -> Name . "' Phone Number: '" . $buyer -> Phone_Number . "' National ID: '" . $buyer -> National_Id . "'} to " . $details_desc;
			} else {
				$buyer = new Buyer();
				$log -> Log_Type = "1";
				$log -> Log_Message = "Created Buyer Record " . $details_desc;
			}
			$buyer -> Buyer_Code = $this -> input -> post("buyer_code");
			$buyer -> National_Id = $this -> input -> post("national_id");
			$buyer -> Phone_Number = $this -> input -> post("phone_number");
			$buyer -> Name = $this -> input -> post("name");
			$buyer -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			redirect("buyer_management/listing");
		} else {
			$this -> new_buyer();
		}
	}

	public function download() {
		$valid = $this -> validate_form();
		$regions = array();
		if ($valid) {
			$data_buffer = "";
			$region = $this -> input -> post("region");
			$date = $this -> input -> post("date");
			if ($region == 0) {
				//Get the region
				$regions = Region::getAll();
			} else {
				$regions = Region::getRegionArray($region);
			}
			//echo the start of the table
			$data_buffer .= "<table class='data-table'>";

			foreach ($regions as $region) {
				$data_buffer .= "<tr><td><b>Region: </b></td><td><b>" . $region -> Region_Name . "</b></td></tr>";
				$data_buffer .= $this -> echoTitles();
				//Get data for each depot
				foreach ($region->Depot_Objects as $depot) {
					$data_buffer .= "<tr><td>" . $depot -> Depot_Name . "</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
				}
			}
			$data_buffer.="</table>";
			$this->generatePDF($data_buffer,$date);
		} else {
			$this -> view_interface();
		}

	}

	public function echoTitles() {
		return "<tr><th>Depot</th><th>Last Transaction Date</th><th>Cash Received</th><th>Cash Paid</th><th>Purchases (Tsh.)</th><th>Purchases (Kgs.)</th><th>Dispatch (Kgs.)</th><th>Avg. Per KG.</th><th>Cash Balance</th><th>Product Balance</th><th>Last Price</th></tr>";
	}

	function generatePDF($data,$date) {
		$html_title = "<img src='Images/logo.png' style='position:absolute; width:134px; height:46px; top:0px; left:0px; '></img>";
		$html_title .= "<h2 style='text-align:center; text-decoration:underline;'>Alliance Ginneries</h2>";
		
		$html_title .= "<h1 style='text-align:center; text-decoration:underline;'>Regional Summaries</h1>";
		$html_title .= "<h3 style='text-align:center;'> as at: ".$date."</h3>";
	 
			$this -> load -> library('mpdf');
			$this -> mpdf = new mPDF('', 'A4-L', 0, '', 15, 15, 16, 16, 9, 9, '');
			$this -> mpdf -> SetTitle('Regional Summaries');
			$this -> mpdf -> WriteHTML($html_title);
			$this -> mpdf -> simpleTables = true;
			$this -> mpdf -> WriteHTML('<br/>');
			$this -> mpdf -> WriteHTML('<br/>');
			$this -> mpdf -> WriteHTML('<br/>');		 
			$this -> mpdf -> WriteHTML($data);
			$this -> mpdf -> WriteHTML($html_footer); 
			$report_name = "Regional Summaries.pdf"; 
			$this -> mpdf -> Output($report_name, 'D');  
	}

	public function delete_buyer($id) {
		$buyer = Buyer::getBuyer($id);
		$buyer -> Deleted = '1';
		$buyer -> save();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Buyer Record {Code: '" . $buyer -> Buyer_Code . "' Name: '" . $buyer -> Name . "' Phone Number: '" . $buyer -> Phone_Number . "' National ID: '" . $buyer -> National_Id . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('date', 'Report Date', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
