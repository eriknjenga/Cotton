<?php
class Disbursement_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($batch, $offset = 0) {
		$items_per_page = 20;
		$number_of_disbursements = Disbursement::getTotalDisbursements($batch);
		$disbursements = Disbursement::getPagedDisbursements($batch, $offset, $items_per_page);
		if ($number_of_disbursements > $items_per_page) {
			$config['base_url'] = base_url() . "disbursement_management/listing/" . $batch . "/";
			$config['total_rows'] = $number_of_disbursements;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batch'] = $batch;
		$data['disbursements'] = $disbursements;
		$data['title'] = "Input Disbursements";
		$data['content_view'] = "list_disbursements_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_disbursement($data = null) {
		$batch = $this -> session -> userdata('input_disbursement_batch');
		if (strlen($batch) == 0) {
			echo "No batch selected";
			redirect("batch_management/no_batch");
		}
		if ($data == null) {
			$data = array();
		}
		$data['farm_inputs'] = Farm_Input::getAll();
		$data['agents'] = Agent::getAll();
		$data['content_view'] = "add_disbursement_v";
		$data['quick_link'] = "add_disbursement";
		$data['batch_information'] = "You are entering records into batch number: <b>" . $this -> session -> userdata('input_disbursement_batch') . "</b>";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function search_fbg() {
		$batch = $this -> session -> userdata('input_disbursement_batch');
		if (strlen($batch) == 0) {
			echo "No batch selected";
			redirect("batch_management/no_batch");
		}
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
		$invoice = $disbursement -> Invoice_Number;
		$data['fbg_disbursement'] = Disbursement::getInvoiceDisbursements($invoice);
		$data['farmer_disbursements'] = Farmer_Input::getInvoiceDisbursements($invoice);
		$data['issued_inputs'] = Disbursement::getInvoiceInputs($invoice);
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
			$editing = $this -> input -> post("editing_id");
			$invoice = $this -> input -> post("invoice_number");
			$date = $this -> input -> post("date");
			$farm_inputs = $this -> input -> post("farm_input");
			$quantities = $this -> input -> post("quantity");
			$total_values = $this -> input -> post("total_value");
			$seasons = $this -> input -> post("season");
			$id_batch = $this -> session -> userdata('input_disbursement_batch');
			$fbg = $this -> input -> post("fbg");
			$agent = $this -> input -> post("agent");
			$farmers = $this -> input -> post("farmer");
			$farmer_inputs = $this -> input -> post("farmer_farm_input");
			$farmer_quantities = $this -> input -> post("farmer_quantity");
			$farmer_total_values = $this -> input -> post("farmer_total_value");
			$edited_input_disbursements = $this -> input -> post("disbursements");
			$edited_farmer_disbursements = $this -> input -> post("farmer_disbursements");
			$input_counter = 0;
			$farmer_counter = 0;
			foreach ($farm_inputs as $input) {
				$log = new System_Log();
				$message = "";
				//If we are editing, retrieve the edited record
				if (isset($edited_input_disbursements[$input_counter])) {
					$log -> Log_Type = "2";
					$disbursement = Disbursement::getDisbursement($edited_input_disbursements[$input_counter]);
					$message = "Edited FBG Input Disbursement Record From {FGB: '" . $disbursement -> FBG_Object -> Group_Name . "' Invoice: '" . $disbursement -> Invoice_Number . "' Date: '" . $disbursement -> Date . "' Farm Input: '" . $disbursement -> Farm_Input_Object -> Product_Name . "' Quantity: '" . $disbursement -> Quantity . "' Total Value: '" . $disbursement -> Total_Value . "' Season: '" . $disbursement -> Season . "' ID Batch: '" . $disbursement -> ID_Batch . "' Agent: '" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "'} to ";
				}
				// else, create a new record
				else {
					$log -> Log_Type = "1";
					$disbursement = new Disbursement();
					$message = "Created New FBG Input Disbursement Record ";
				}
				$disbursement -> clearRelated();
				$disbursement -> FBG = $fbg;
				$disbursement -> Invoice_Number = $invoice;
				$disbursement -> Date = $date;
				$disbursement -> Farm_Input = $farm_inputs[$input_counter];
				$disbursement -> Quantity = $quantities[$input_counter];
				$disbursement -> Total_Value = $total_values[$input_counter];
				$disbursement -> Season = $seasons[$input_counter];
				$disbursement -> ID_Batch = $id_batch;
				$disbursement -> Timestamp = date('U');
				$disbursement -> Agent = $agent;
				$disbursement -> save();
				$message .= "{FGB: '" . $disbursement -> FBG_Object -> Group_Name . "' Invoice: '" . $disbursement -> Invoice_Number . "' Date: '" . $disbursement -> Date . "' Farm Input: '" . $disbursement -> Farm_Input_Object -> Product_Name . "' Quantity: '" . $disbursement -> Quantity . "' Total Value: '" . $disbursement -> Total_Value . "' Season: '" . $disbursement -> Season . "' ID Batch: '" . $disbursement -> ID_Batch . "' Agent: '" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "'}";
				$log -> Log_Message = $message;
				$log -> User = $this -> session -> userdata('user_id');
				$log -> Timestamp = date('U');
				$log -> save();
				$input_counter++;
			}
			foreach ($farmer_inputs as $input) {
				if (strlen($farmers[$farmer_counter]) > 0) {
					//If we are editing, retrieve the edited record
					if (isset($edited_farmer_disbursements[$farmer_counter])) {
						$farmer_input = Farmer_Input::getDisbursement($edited_farmer_disbursements[$farmer_counter]);
					}
					// else, create a new record
					else {
						$farmer_input = new Farmer_Input();
					}
					$farmer_input -> FBG = $fbg;
					$farmer_input -> Invoice_Number = $invoice;
					$farmer_input -> Date = $date;
					$farmer_input -> Farmer = $farmers[$farmer_counter];
					$farmer_input -> Farm_Input = $farmer_inputs[$farmer_counter];
					$farmer_input -> Quantity = $farmer_quantities[$farmer_counter];
					$farmer_input -> Total_Value = $farmer_total_values[$farmer_counter];
					$farmer_input -> Batch_Id = $id_batch;
					$farmer_input -> Timestamp = date('U');
					$farmer_input -> save();
					$farmer_counter++;
				}
			}
			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New") {
				redirect("disbursement_management/search_fbg");
			} else if ($submit_button == "Save & View List") {
				$batch = $this -> session -> userdata('input_disbursement_batch');
				$link = "disbursement_management/listing/" . $batch;
				redirect($link);
			}

		} else {
			$this -> new_disbursement();
		}
	}

	public function delete_disbursement($id) {
		$disbursement = Disbursement::getDisbursement($id);
		$invoice = $disbursement -> Invoice_Number;
		$disbursements = Disbursement::getInvoiceDisbursements($invoice);
		$farmer_disbursements = Farmer_Input::getInvoiceDisbursements($invoice);
		foreach ($disbursements as $disbursement) {
			$disbursement -> delete();
			$log = new System_Log();
			$log -> Log_Type = "3";
			$log -> Log_Message = "Deleted FBG Input Disbursement Record {FGB: '" . $disbursement -> FBG_Object -> Group_Name . "' Invoice: '" . $disbursement -> Invoice_Number . "' Date: '" . $disbursement -> Date . "' Farm Input: '" . $disbursement -> Farm_Input_Object -> Product_Name . "' Quantity: '" . $disbursement -> Quantity . "' Total Value: '" . $disbursement -> Total_Value . "' Season: '" . $disbursement -> Season . "' ID Batch: '" . $disbursement -> ID_Batch . "' Agent: '" . $disbursement -> Agent_Object -> First_Name . " " . $disbursement -> Agent_Object -> Surname . "'}";
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
		}
		foreach ($farmer_disbursements as $farmer_disbursement) {
			$farmer_disbursement -> delete();
		}
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
