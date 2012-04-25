<?php
class Disbursement_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_disbursements = Disbursement::getTotalDisbursements();
		$disbursements = Disbursement::getPagedDisbursements($offset, $items_per_page);
		if ($number_of_disbursements > $items_per_page) {
			$config['base_url'] = base_url() . "disbursement_management/listing/";
			$config['total_rows'] = $number_of_disbursements;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['disbursements'] = $disbursements;
		$data['title'] = "Input Disbursements";
		$data['content_view'] = "list_disbursements_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_disbursement($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['farm_inputs'] = Farm_Input::getAll();
		$data['agents'] = Agent::getAll();
		$user = $this -> session -> userdata('user_id');
		$data['batches'] = Transaction_Batch::getActiveUserBatches($user,'0');
		$data['content_view'] = "add_disbursement_v";
		$data['quick_link'] = "add_disbursement";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function search_fbg() {
		$data['content_view'] = "search_fbg_v";
		$data['link'] = "disbursement_management";
		$data['quick_link'] = "search_fbg";
		$data['search_title'] = "Search For an FBG to Disburse Inputs to";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> load -> view("demo_template", $data);
	}

	public function edit_disbursement($id) {
		$disbursement = Disbursement::getDisbursement($id);
		$data['disbursement'] = $disbursement;
		$this -> new_disbursement($data);
	}

	public function disburse_inputs($fbg) {
		$recipient = FBG::getFbg($fbg);
		$data['fbg'] = $recipient;
		$this -> new_disbursement($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$invoice = $this -> input -> post("invoice_number");
			$date = $this -> input -> post("date");
			$farm_inputs = $this -> input -> post("farm_input");
			$quantities = $this -> input -> post("quantity");
			$total_values = $this -> input -> post("total_value");
			$seasons = $this -> input -> post("season");
			$gd_batches = $this -> input -> post("gd_batch");
			$id_batches = $this -> input -> post("id_batch");
			$fbg = $this -> input -> post("fbg");
			$agent = $this -> input -> post("agent");

			$temp_disbursement = new Disbursement();
			$temp_disbursement -> FBG = $fbg;
			$temp_disbursement -> Farm_Input = $farm_inputs[0];
			$temp_disbursement -> Agent = $agent;
			$details_desc = "{FGB: '" . $temp_disbursement -> FBG_Object -> Group_Name . "' Invoice: '" . $invoice . "' Date: '" . $date . "' Farm Input: '" . $temp_disbursement -> Farm_Input_Object -> Product_Name . "' Quantity: '" . $quantities[0] . "' Total Value: '" . $total_values[0] . "' Season: '" . $seasons[0] . "' GD Batch: '" . $gd_batches[0] . "' ID Batch: '" . $id_batches[0] . "' Agent: '" . $temp_disbursement -> Agent_Object -> First_Name . " " . $temp_disbursement -> Agent_Object -> Surname . "'}";
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$disbursement = Disbursement::getDisbursement($editing);
				$log -> Log_Type = "2";
				$log -> Log_Message = "Edited Input Disbursement Record From {FGB: '" . $disbursement -> FBG_Object -> Group_Name . "' Invoice: '" . $disbursement -> Invoice_Number . "' Date: '" . $disbursement -> Date . "' Farm Input: '" . $disbursement -> Farm_Input_Object -> Product_Name . "' Quantity: '" . $disbursement -> Quantity . "' Total Value: '" . $disbursement -> Total_Value . "' Season: '" . $disbursement -> Season . "' GD Batch: '" . $disbursement -> GD_Batch . "' ID Batch: '" . $disbursement -> ID_Batch . "' Agent: '" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "'} to " . $details_desc;
			} else {
				$disbursement = new Disbursement();
				$log -> Log_Type = "1";
				$log -> Log_Message = "Created Input Disbursement Record " . $details_desc;
			}
			$disbursement -> FBG = $fbg;
			$disbursement -> Invoice_Number = $invoice;
			$disbursement -> Date = $date;
			$disbursement -> Farm_Input = $farm_inputs[0];
			$disbursement -> Quantity = $quantities[0];
			$disbursement -> Total_Value = $total_values[0];
			$disbursement -> Season = $seasons[0];
			$disbursement -> GD_Batch = $gd_batches[0];
			$disbursement -> ID_Batch = $id_batches[0];
			$disbursement -> Timestamp = date('U');
			$disbursement -> Agent = $agent;
			$disbursement -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			//Loop through the rest of the disbursements
			for ($x = 1; $x < sizeof($farm_inputs); $x++) {
				$disbursement = new Disbursement();
				$disbursement -> FBG = $fbg;
				$disbursement -> Invoice_Number = $invoice;
				$disbursement -> Date = $date;
				$disbursement -> Farm_Input = $farm_inputs[$x];
				$disbursement -> Quantity = $quantities[$x];
				$disbursement -> Total_Value = $total_values[$x];
				$disbursement -> Season = $seasons[$x];
				$disbursement -> GD_Batch = $gd_batches[$x];
				$disbursement -> ID_Batch = $id_batches[$x];
				$disbursement -> Timestamp = date('U');
				$disbursement -> Agent = $agent;
				$details_desc = "{FGB: '" . $disbursement -> FBG_Object -> Group_Name . "' Invoice: '" . $invoice . "' Date: '" . $date . "' Farm Input: '" . $disbursement -> Farm_Input_Object -> Product_Name . "' Quantity: '" . $quantities[$x] . "' Total Value: '" . $total_values[$x] . "' Season: '" . $seasons[$x] . "' GD Batch: '" . $gd_batches[$x] . "' ID Batch: '" . $id_batches[$x] . "' Agent: '" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "'}";
				$log = new System_Log();
				$log -> Log_Type = "1";
				$log -> Log_Message = "Created  Input Disbursement Record " . $details_desc;
				$disbursement -> save();
				$log -> User = $this -> session -> userdata('user_id');
				$log -> Timestamp = date('U');
				$log -> save();
			}
			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New") {
				redirect("disbursement_management/search_fbg");
			} else if ($submit_button == "Save & View List") {
				redirect("disbursement_management/listing");
			}

		} else {
			$this -> new_disbursement();
		}
	}

	public function delete_disbursement($id) {
		$disbursement = Disbursement::getDisbursement($id);
		$disbursement -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Input Disbursement Record {FGB: '" . $disbursement -> FBG_Object -> Group_Name . "' Invoice: '" . $disbursement -> Invoice_Number . "' Date: '" . $disbursement -> Date . "' Farm Input: '" . $disbursement -> Farm_Input_Object -> Product_Name . "' Quantity: '" . $disbursement -> Quantity . "' Total Value: '" . $disbursement -> Total_Value . "' Season: '" . $disbursement -> Season . "' GD Batch: '" . $disbursement -> GD_Batch . "' ID Batch: '" . $disbursement -> ID_Batch . "' Agent: '" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('invoice_number', 'Invoice Number', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('agent', 'Agent', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('date', 'Date', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('farm_input[]', 'Farm Input', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('quantity[]', 'Invoice Number', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['link'] = "disbursement_management";

		$this -> load -> view("demo_template", $data);
	}

}
