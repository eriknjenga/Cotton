<?php
class Cash_Receipt_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($batch, $offset = 0) {
		$items_per_page = 20;
		$number_of_receipts = Cash_Receipt::getTotalReceipts($batch);
		$receipts = Cash_Receipt::getPagedReceipts($batch, $offset, $items_per_page);
		if ($number_of_receipts > $items_per_page) {
			$config['base_url'] = base_url() . "cash_receipt_management/listing/" . $batch . "/";
			$config['total_rows'] = $number_of_receipts;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batch'] = $batch;
		$data['receipts'] = $receipts;
		$data['content_view'] = "list_cash_receipts_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}
	public function search_receipt() {
		$search_term = $this -> input -> post("search_value5");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$receipt = Cash_Receipt::getSearchedReceipt($db_search_term);
		$data['receipt'] = $receipt;
		$data['content_view'] = "list_fc_return_search_results_v";
		$this -> base_params($data);
	}
	public function new_receipt($data = null) {
		$batch = $this -> session -> userdata('cash_receipt_batch');
		if (strlen($batch) == 0) {
			redirect("batch_management/no_batch");
		}
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_cash_receipt_v";
		$data['quick_link'] = "new_cash_receipt";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$data['batch_information'] = "You are entering records into batch number: <b>" . $this -> session -> userdata('cash_receipt_batch') . "</b>";
		$data['field_cashiers'] = Field_Cashier::getAll();
		$this -> base_params($data);
	}

	public function edit_receipt($id) {
		$receipt = Cash_Receipt::getReceipt($id);
		$data['receipt'] = $receipt;
		$this -> new_receipt($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$message = "";
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$receipt_number = $this -> input -> post("receipt_number");
			$date = $this -> input -> post("date");
			$amount = $this -> input -> post("amount");
			$field_cashier = $this -> input -> post("field_cashier");
			$batch = $this -> session -> userdata('cash_receipt_batch');
			$adjustment = $this -> input -> post("adjustment"); 
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$receipt = Cash_Receipt::getReceipt($editing);
				$log -> Log_Type = "2";
				$message = "Edited Cash Receipt Record From {Field Cashier: '" . $receipt -> Field_Cashier_Object -> Field_Cashier_Name . "' Receipt Number: '" . $receipt -> Receipt_Number . "' Date: '" . $receipt -> Date . "' Amount: '" . $receipt -> Amount . "' Batch '" . $receipt -> Batch . "'} to ";
			} else {
				$receipt = new Cash_Receipt();
				$log -> Log_Type = "1";
				$message = "Created New Cash Receipt Record ";
			}
			$receipt -> clearRelated();
			$receipt -> Receipt_Number = $receipt_number;
			$receipt -> Date = $date;
			$receipt -> Amount = $amount;
			$receipt -> Field_Cashier = $field_cashier;
			$receipt -> Timestamp = date('U');
			$receipt -> Batch = $batch;
			$receipt -> Adjustment = $adjustment;
			$receipt -> save();
			$message .= "{Field Cashier: '" . $receipt -> Field_Cashier_Object -> Field_Cashier_Name . "' Receipt Number: '" . $receipt -> Receipt_Number . "' Date: '" . $receipt -> Date . "' Amount: '" . $receipt -> Amount . "' Batch '" . $receipt -> Batch . "'}";
			$log -> Log_Message = $message;
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New") {
				redirect("cash_receipt_management/new_receipt");
			} else if ($submit_button == "Save & View List") {
				$batch = $this -> session -> userdata('cash_receipt_batch');
				$link = "cash_receipt_management/listing/" . $batch;
				redirect($link);
			}
		} else {
			$this -> new_receipt();
		}
	}

	public function delete_receipt($id) {
		$receipt = Cash_Receipt::getReceipt($id);
		$receipt -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Cash Record {Field Cashier: '" . $receipt -> Field_Cashier_Object -> Field_Cashier_Name . "' Receipt Number: '" . $receipt -> Receipt_Number . "' Date: '" . $receipt -> Date . "' Amount: '" . $receipt -> Amount . "' Batch '" . $receipt -> Batch . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('field_cashier', 'Field Cashier', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('receipt_number', 'Receipt Number', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('date', 'Transaction Date', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('amount', 'Amount Returned', 'trim|required|xss_clean');
		$temp_validation = $this -> form_validation -> run();
		if ($temp_validation) {
			$this -> form_validation -> set_rules('receipt_number', 'Duplicate Receipt Number', 'trim|required|callback_receipt_duplication');
			return $this -> form_validation -> run();
		} else {
			return $temp_validation;
		}
	}

	public function receipt_duplication($receipt) {
		$adjustment = $this -> input -> post("adjustment");
		$editing = $this -> input -> post("editing_id");
		//If this is an adjustment or a record update, then there's no need to check for duplication
		if ($adjustment == "1" || strlen($editing) > 0) {
			return TRUE;
		}
		// Else, check for duplications
		else {
			$duplicate = Cash_Receipt::checkDuplicate($receipt);
			if ($duplicate == 0) {
				return TRUE;
			} else if ($duplicate > 0) {
				$this -> form_validation -> set_message('receipt_duplication', 'A Cash Receipt with the same number already exists!');
				return FALSE;
			}
		}
	}

	public function base_params($data) {
		$data['link'] = "cash_receipt_management";

		$this -> load -> view("demo_template", $data);
	}

}
