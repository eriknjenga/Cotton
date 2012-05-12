<?php
class Mopping_Payment_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($batch, $offset = 0) {
		$items_per_page = 20;
		$number_of_payments = Mopping_Payment::getTotalPayments($batch);
		$payments = Mopping_Payment::getPagedPayments($batch, $offset, $items_per_page);
		if ($number_of_payments > $items_per_page) {
			$config['base_url'] = base_url() . "mopping_payment_management/listing/" . $batch . "/";
			$config['total_rows'] = $number_of_payments;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batch'] = $batch;
		$data['payments'] = $payments;
		$data['content_view'] = "list_mopping_payments_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function new_payment($data = null) {
		$batch = $this -> session -> userdata('mopping_payment_batch');
		if (strlen($batch) == 0) {
			echo "No batch selected";
			redirect("batch_management/no_batch");
		}
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_mopping_payment_v";
		$data['quick_link'] = "add_mopping_payment";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$data['batch_information'] = "You are entering records into batch number: <b>" . $this -> session -> userdata('mopping_payment_batch') . "</b>";
		$data['depots'] = Depot::getAll();
		$this -> base_params($data);
	}

	public function edit_payment($id) {
		$payment = Mopping_payment::getPayment($id);
		$data['payment'] = $payment;
		$this -> new_payment($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$message = "";
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$voucher_number = $this -> input -> post("voucher_number");
			$date = $this -> input -> post("date");
			$amount = $this -> input -> post("amount");
			$depot = $this -> input -> post("depot");
			$batch = $this -> session -> userdata('mopping_payment_batch');
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$payment = Mopping_payment::getPayment($editing);
				$log -> Log_Type = "2";
				$message = "Edited Mopping Payment Record From {Depot: '" . $payment -> Depot_Object -> Depot_Name . "' Voucher Number: '" . $payment -> Voucher_Number . "' Date: '" . $payment -> Date . "' Amount: '" . $payment -> Amount . "' Batch '" . $payment -> Batch . "'} to ";
			} else {
				$payment = new Mopping_Payment();
				$log -> Log_Type = "1";
				$message = "Created New Mopping Payment Record ";
			}
			$payment -> clearRelated();
			$payment -> Voucher_Number = $voucher_number;
			$payment -> Date = $date;
			$payment -> Amount = $amount;
			$payment -> Depot = $depot;
			$payment -> Batch = $batch;
			$payment -> save();
			$message .= "{Depot: '" . $payment -> Depot_Object -> Depot_Name . "' Voucher Number: '" . $payment -> Voucher_Number . "' Date: '" . $payment -> Date . "' Amount: '" . $payment -> Amount . "' Batch '" . $payment -> Batch . "'}";
			$log -> Log_Message = $message;
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New") {
				redirect("mopping_payment_management/new_payment");
			} else if ($submit_button == "Save & View List") {
				$batch = $this -> session -> userdata('mopping_payment_batch');
				$link = "mopping_payment_management/listing/" . $batch;
				redirect($link);
			}
		} else {
			$this -> new_payment();
		}
	}

	public function delete_payment($id) {
		$payment = Mopping_Payment::getPayment($id);
		$payment -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Mopping Payment Record {Depot: '" . $payment -> Depot_Object -> Depot_Name . "' Voucher Number: '" . $payment -> Voucher_Number . "' Date: '" . $payment -> Date . "' Amount: '" . $payment -> Amount . "' Batch '" . $payment -> Batch . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('depot', 'Depot', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('voucher_number', 'Voucher Number', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('date', 'Transaction Date', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('amount', 'Amount Payed', 'trim|required|xss_clean');
		return $this -> form_validation -> run();

	}

	public function base_params($data) {
		$data['title'] = "Mopping Payments";
		$data['banner_text'] = "New Payment";
		$data['link'] = "mopping_payment_management";
		$this -> load -> view("demo_template", $data);
	}

}
