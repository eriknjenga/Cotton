<?php
class Cash_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($batch, $offset = 0) {
		$items_per_page = 20;
		$number_of_disbursements = Cash_Disbursement::getTotalDisbursements($batch);
		$disbursements = Cash_Disbursement::getPagedDisbursements($batch, $offset, $items_per_page);
		if ($number_of_disbursements > $items_per_page) {
			$config['base_url'] = base_url() . "cash_management/listing/" . $batch . "/";
			$config['total_rows'] = $number_of_disbursements;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batch'] = $batch;
		$data['disbursements'] = $disbursements;
		$data['title'] = "Cash Disbursements";
		$data['content_view'] = "list_cash_disbursements_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function search_cih() {
		$search_term = $this -> input -> post("search_value2");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$cih = Cash_Disbursement::getSearchedCih($db_search_term);
		$data['cih'] = $cih;
		$data['listing_title'] = "CIH Search Results For <b>' " . $search_term . "</b>";
		$data['content_view'] = "list_cihc_search_results_v";
		$this -> base_params($data);
	}

	public function issue_cash($data = null) {
		$batch = $this -> session -> userdata('cihc_batch');
		if (strlen($batch) == 0) {
			redirect("batch_management/no_batch");
		}
		if ($data == null) {
			$data = array();
		}
		$data['field_cashiers'] = Field_Cashier::getAll();
		$data['content_view'] = "add_cash_disbursement_v";
		$data['quick_link'] = "issue_cash";
		$data['batch_information'] = "You are entering records into batch number: <b>" . $this -> session -> userdata('cihc_batch') . "</b>";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_disbursement($id) {
		$disbursement = Cash_Disbursement::getDisbursement($id);
		$data['disbursement'] = $disbursement;
		$this -> issue_cash($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$temp_disb = new Cash_Disbursement();
			$temp_disb -> Field_Cashier = $this -> input -> post("field_cashier");
			$details_desc = "{Field Cashier: '" . $temp_disb -> Field_Cashier_Object -> Field_Cashier_Name . "' Amount: '" . $this -> input -> post("amount") . "' CIH(c) Voucher: '" . $this -> input -> post("cih") . "' Date: '" . $this -> input -> post("date") . "'}";
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$disbursement = Cash_Disbursement::getDisbursement($editing);
				$log -> Log_Type = "2";
				$log -> Log_Message = "Edited Cash Disbursement Record From {Field Cashier: '" . $disbursement -> Field_Cashier_Object -> Field_Cashier_Name . "' Amount: '" . $disbursement -> Amount . "' CIH(c) Voucher: '" . $disbursement -> CIH . "' Date: '" . $disbursement -> Date . "'} to " . $details_desc;
			} else {
				$disbursement = new Cash_Disbursement();
				$log -> Log_Type = "1";
				$log -> Log_Message = "Created Cash Disbursement Record " . $details_desc;
			}
			$disbursement -> Field_Cashier = $this -> input -> post("field_cashier");
			$disbursement -> Amount = $this -> input -> post("amount");
			$disbursement -> CIH = $this -> input -> post("cih");
			$disbursement -> Date = $this -> input -> post("date");
			$disbursement -> Adjustment = $this -> input -> post("adjustment");
			$disbursement -> Batch = $this -> session -> userdata('cihc_batch');
			$disbursement -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New") {
				redirect("cash_management/issue_cash");
			} else if ($submit_button == "Save & View List") {
				$batch = $this -> session -> userdata('cihc_batch');
				$link = "cash_management/listing/" . $batch;
				redirect($link);
			}
		} else {
			$this -> issue_cash();
		}
	}

	public function cih_duplication($cih) {
		$adjustment = $this -> input -> post("adjustment");
		$editing = $this -> input -> post("editing_id");
		//If this is an adjustment or a record update, then there's no need to check for duplication
		if ($adjustment == "1" || strlen($editing) > 0) {
			return TRUE;
		}
		// Else, check for duplications
		else {
			$duplicate = Cash_Disbursement::checkDuplicate($cih);
			if ($duplicate == 0) {
				return TRUE;
			} else if ($duplicate > 0) {
				$this -> form_validation -> set_message('cih_duplication', 'A CIH(c) with the same number already exists!');
				return FALSE;
			}
		}
	}

	public function delete_disbursement($id) {
		$disbursement = Cash_Disbursement::getDisbursement($id);
		$disbursement -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Cash Disbursement Record {Field Cashier: '" . $disbursement -> Field_Cashier_Object -> Field_Cashier_Name . "' Amount: '" . $disbursement -> Amount . "' CIH(c) Voucher: '" . $disbursement -> CIH . "' Date: '" . $disbursement -> Date . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('field_cashier', 'Field Cashier', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('amount', 'Amount Disbursed', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('cih', 'CIH Voucher', 'trim|required|max_length[50]|xss_clean');
		$temp_validation = $this -> form_validation -> run();
		if ($temp_validation) {
			$this -> form_validation -> set_rules('cih', 'Duplicate CIH(c) Number', 'trim|required|callback_cih_duplication');
			return $this -> form_validation -> run();
		} else {
			return $temp_validation;
		}
	}

	public function base_params($data) {
		$data['title'] = "Cash Management";
		$data['link'] = "cash_management";
		$this -> load -> view("demo_template", $data);
	}

}
