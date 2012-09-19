<?php
class Buying_Center_Summary_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($batch, $offset = 0) {
		$items_per_page = 20;
		$number_of_summaries = Buying_Center_Summary::getTotalSummaries($batch);
		$summaries = Buying_Center_Summary::getPagedSummaries($batch, $offset, $items_per_page);
		if ($number_of_summaries > $items_per_page) {
			$config['base_url'] = base_url() . "buying_center_summary_management/listing/" . $batch . "/";
			$config['total_rows'] = $number_of_summaries;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batch'] = $batch;
		$data['summaries'] = $summaries;
		$data['content_view'] = "list_buying_center_summaries_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function search_summary() {
		$search_term = $this -> input -> post("search_value10");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$summaries = Buying_Center_Summary::getSearchedSummary($db_search_term);
		$data['summaries'] = $summaries;
		$data['content_view'] = "list_buying_center_summary_search_results_v";
		$this -> base_params($data);
	}

	public function new_summary($data = null) {
		$batch = $this -> session -> userdata('buying_center_summary_batch');
		if (strlen($batch) == 0) {
			redirect("batch_management/no_batch");
		}
		if ($data == null) {
			$data = array();
		}
		$data['depots'] = Depot::getAll();
		$data['content_view'] = "add_buying_center_summary_v";
		$data['quick_link'] = "new_buying_center_summary";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$data['batch_information'] = "You are entering records into batch number: <b>" . $this -> session -> userdata('buying_center_summary_batch') . "</b>";
		$this -> base_params($data);
	}

	public function edit_summary($id) {
		$summary = Buying_Center_Summary::getSummary($id);
		$data['summary'] = $summary;
		$this -> new_summary($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$message = "";
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$summary_number = $this -> input -> post("summary_number");
			$date = $this -> input -> post("date");
			$depot = $this -> input -> post("depot");
			$opening_bags = $this -> input -> post("opening_bags");
			$opening_stock = $this -> input -> post("opening_stock");
			$opening_cash = $this -> input -> post("opening_cash");
			$bags_received = $this -> input -> post("bags_received");
			$cash_received = $this -> input -> post("cash_received");
			$start_ppv = $this -> input -> post("start_ppv");
			$end_ppv = $this -> input -> post("end_ppv");
			$purchase_quantity = $this -> input -> post("purchase_quantity");
			$purchase_value = $this -> input -> post("purchase_value");
			$input_deductions = $this -> input -> post("input_deductions");
			$delivery_note = $this -> input -> post("delivery_note");
			$cotton_deliveries = $this -> input -> post("cotton_deliveries");
			$closing_bags = $this -> input -> post("closing_bags");
			$closing_stock = $this -> input -> post("closing_stock");
			$closing_cash = $this -> input -> post("closing_cash");
			$prepared_by = $this -> input -> post("prepared_by");
			$batch = $this -> session -> userdata('buying_center_summary_batch');
			$adjustment = $this -> input -> post("adjustment");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$summary = Buying_Center_Summary::getSummary($editing);
				$log -> Log_Type = "2";
				$message = "Edited Buying Center Summary Record From {Buying Center: '" . $summary -> Depot_Object -> Depot_Name . "' Summary Number: '" . $summary -> Summary_Number . "' Date: '" . $summary -> Date . "' Closing Cash: '" . $summary -> Closing_Cash . "' Closing Stock: '" . $summary -> Closing_Stock . "' Batch '" . $summary -> Batch . "'} to ";
			} else {
				$summary = new Buying_Center_Summary();
				$log -> Log_Type = "1";
				$message = "Created New Buying Center Summary Record ";
			}
			$summary -> clearRelated();
			$summary -> Depot = $depot;
			$summary -> Summary_Number = $summary_number;
			$summary -> Date = $date;
			$summary -> Opening_Bags = $opening_bags;
			$summary -> Opening_Stocks = $opening_stock;
			$summary -> Opening_Cash = $opening_cash;
			$summary -> Bags_Received = $bags_received;
			$summary -> Cash_Received = $cash_received;
			$summary -> Start_Ppv = $start_ppv;
			$summary -> End_Ppv = $end_ppv;
			$summary -> Purchase_Quantity = $purchase_quantity;
			$summary -> Purchase_Value = $purchase_value;
			$summary -> Input_Deductions = $input_deductions;
			$summary -> Cotton_Deliveries = $cotton_deliveries;
			$summary -> Delivery_Note = $delivery_note;
			$summary -> Closing_Bags = $closing_bags;
			$summary -> Closing_Stock = $closing_stock;
			$summary -> Closing_Cash = $closing_cash;
			$summary -> Prepared_By = $prepared_by;
			$summary -> Batch = $batch;
			$summary -> Adjustment = $adjustment;
			$summary -> save();
			$message .= "{Buying Center: '" . $summary -> Depot_Object -> Depot_Name . "' Summary Number: '" . $summary -> Summary_Number . "' Date: '" . $summary -> Date . "' Closing Cash: '" . $summary -> Closing_Cash . "' Closing Stock: '" . $summary -> Closing_Stock . "' Batch '" . $summary -> Batch . "'}";
			$log -> Log_Message = $message;
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New") {
				redirect("buying_center_summary_management/new_summary");
			} else if ($submit_button == "Save & View List") {
				$batch = $this -> session -> userdata('buying_center_summary_batch');
				$link = "buying_center_summary_management/listing/" . $batch;
				redirect($link);
			}
		} else {
			$this -> new_summary();
		}
	}

	public function delete_summary($id) {
		$summary = Buying_Center_Summary::getSummary($id);
		$summary -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Buying Center Summary Record {Buying Center: '" . $summary -> Depot_Object -> Depot_Name . "' Summary Number: '" . $summary -> Summary_Number . "' Date: '" . $summary -> Date . "' Closing Cash: '" . $summary -> Closing_Cash . "' Closing Stock: '" . $summary -> Closing_Stock . "' Batch '" . $summary -> Batch . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('depot', 'Buying Center', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('summary_number', 'Summary Number', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('date', 'Summary Date', 'trim|required|max_length[100]|xss_clean'); 
		$temp_validation = $this -> form_validation -> run();
		if ($temp_validation) {
			$this -> form_validation -> set_rules('summary_number', 'Duplicate BCS Number', 'trim|required|callback_summary_duplication');
			return $this -> form_validation -> run();
		} else {
			return $temp_validation;
		}
	}

	public function summary_duplication($summary) {
		$adjustment = $this -> input -> post("adjustment");
		$editing = $this -> input -> post("editing_id");
		//If this is an adjustment or a record update, then there's no need to check for duplication
		if ($adjustment == "1" || strlen($editing) > 0) {
			return TRUE;
		}
		// Else, check for duplications
		else {
			$duplicate = Buying_Center_Summary::checkDuplicate($summary);
			if ($duplicate == 0) {
				return TRUE;
			} else if ($duplicate > 0) {
				$this -> form_validation -> set_message('summary_duplication', 'A Buying Center Summary with the same number already exists!');
				return FALSE;
			}
		}
	}

	public function base_params($data) {
		$data['link'] = "buying_center_summary_management";

		$this -> load -> view("demo_template", $data);
	}

}
