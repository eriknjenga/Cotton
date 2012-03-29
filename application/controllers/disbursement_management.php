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
		$data['distributors'] = Distributor::getAll();
		$data['content_view'] = "add_disbursement_v";
		$data['quick_link'] = "add_disbursement";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$this -> base_params($data);
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
			$editing = $this -> input -> post("editing_id");
			$invoices = $this -> input -> post("invoice_number");
			$dates = $this -> input -> post("date");
			$farm_inputs = $this -> input -> post("farm_input");
			$quantities = $this -> input -> post("quantity");
			$total_values = $this -> input -> post("total_value");
			$seasons = $this -> input -> post("season");
			$gd_batches = $this -> input -> post("gd_batch");
			$id_batches = $this -> input -> post("id_batch");
			$fbg = $this -> input -> post("fbg"); 
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$disbursement = Disbursement::getDisbursement($editing);
			} else {
				$disbursement = new Disbursement();
			}
			$disbursement -> FBG = $fbg;
			$disbursement -> Invoice_Number = $invoices[0];
			$disbursement -> Date = $dates[0];
			$disbursement -> Farm_Input = $farm_inputs[0];
			$disbursement -> Quantity = $quantities[0];
			$disbursement -> Total_Value = $total_values[0];
			$disbursement -> Season = $seasons[0];
			$disbursement -> GD_Batch = $gd_batches[0];
			$disbursement -> ID_Batch = $id_batches[0];
			$disbursement -> Timestamp = date('U');
			$disbursement -> save();

			//Loop through the rest of the disbursements
			for ($x = 1; $x < sizeof($invoices); $x++) {
				$disbursement = new Disbursement();
				$disbursement -> FBG = $fbg;
				$disbursement -> Invoice_Number = $invoices[$x];
				$disbursement -> Date = $dates[$x];
				$disbursement -> Farm_Input = $farm_inputs[$x];
				$disbursement -> Quantity = $quantities[$x];
				$disbursement -> Total_Value = $total_values[$x];
				$disbursement -> Season = $seasons[$x];
				$disbursement -> GD_Batch = $gd_batches[$x];
				$disbursement -> ID_Batch = $id_batches[$x];
				$disbursement -> Timestamp = date('U');
				$disbursement -> save();
			}
			redirect("disbursement_management/listing");
		} else {
			$this -> new_disbursement();
		}
	}

	public function delete_disbursement($id) {
		$disbursement = Disbursement::getDisbursement($id);
		$disbursement -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('invoice_number[]', 'Invoice Number', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('date[]', 'Date', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('farm_input[]', 'Farm Input', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('quantity[]', 'Invoice Number', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('season[]', 'Planting Season', 'trim|required|max_length[20]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Disbursement Management";
		$data['link'] = "disbursement_management";

		$this -> load -> view("demo_template", $data);
	}

}
