<?php
class Purchase_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($batch, $offset = 0) {
		$items_per_page = 20;
		$number_of_purchases = Purchase::getTotalPurchases($batch);
		$purchases = Purchase::getPagedPurchases($batch, $offset, $items_per_page);
		if ($number_of_purchases > $items_per_page) {
			$config['base_url'] = base_url() . "purchase_management/listing/";
			$config['total_rows'] = $number_of_purchases;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['batch'] = $batch;
		$data['purchases'] = $purchases;
		$data['title'] = "Cotton Disbursements";
		$data['content_view'] = "list_purchases_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function record_purchase($depot) {
		$depot_object = Depot::getDepot($depot);
		$data['depot'] = $depot_object;
		$this -> session -> set_userdata(array('saved_depot' => $depot));
		$this -> new_purchase($data);
	}

	public function new_purchase($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['batch_information'] = "You are entering records into batch number: <b>" . $this -> session -> userdata('purchases_batch') . "</b>";
		$data['prices'] = Cotton_Price::getCottonPrices();
		$data['content_view'] = "add_purchase_v";
		$data['quick_link'] = "add_purchase";
		$data['scripts'] = array("validationEngine-en.js", "validator.js", "jquery.ui.autocomplete.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function search_depot() {
		$batch = $this -> session -> userdata('purchases_batch');
		if (strlen($batch) == 0) {
			echo "No batch selected";
			redirect("batch_management/no_batch");
		}
		$data['content_view'] = "search_depot_v";
		$data['link'] = "purchase_management";
		$data['quick_link'] = "search_depot";
		$data['search_title'] = "Search For an Depot to Record Purchases For";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> load -> view("demo_template", $data);
	}

	public function edit_purchase($id) {
		$purchase = Purchase::getPurchase($id);
		$fbg = $purchase -> FBG;
		$recipient = FBG::getFbg($fbg);
		$data['disbursements'] = Disbursement::getFBGDisbursements($fbg);
		$data['purchase'] = $purchase;
		$this -> new_purchase($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$dpn = $this -> input -> post("dpn");
			$date = $this -> input -> post("date");
			$quantity = $this -> input -> post("quantity");
			$total_value = $this -> input -> post("purchased_value");
			$season = $this -> input -> post("season");
			$fbg = $this -> input -> post("fbg_id");
			$depot = $this -> input -> post("depot");
			$loan_recovery = $this -> input -> post("loan_recovery");
			$farmer_registration = $this -> input -> post("farmer_registration");
			$other_recoveries = $this -> input -> post("other_recoveries");
			$buyer = $this -> input -> post("buyer");
			$net_value = $this -> input -> post("net_value");
			$price = $this -> input -> post("price");
			$batch = $this -> session -> userdata('purchases_batch');

			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$log -> Log_Type = "2";
				$purchase = Purchase::getPurchase($editing);
				$message = "Edited Purchase Record From {FGB: '" . $purchase -> FBG_Object -> Group_Name . "' DPN: '" . $purchase -> DPN . "' Date: '" . $purchase -> Date . "' $purchase: '" . $purchase -> Depot_Object -> Depot_Name . "' Quantity: '" . $purchase -> Quantity . "' Unit Price: '" . $purchase -> Unit_Price . "' Season: '" . $purchase -> Season . "' Loan Recovery: '" . $purchase -> Loan_Recovery . "' Farmer Registration Fee: '" . $purchase -> Farmer_Reg_Fee . "' Other Recoveries: '" . $purchase -> Other_Recoveries . "' Buyer: '" . $purchase -> Buyer_Object -> Name . "' Net_Value: '" . $purchase -> Net_Value . "' Gross_Value: '" . $purchase -> Gross_Value . "'} to ";
			} else {
				$log -> Log_Type = "1";
				$purchase = new Purchase();
				$message = "Created New Purchase Record ";
			}
			$purchase -> clearRelated();
			$purchase -> FBG = $fbg;
			$purchase -> DPN = $dpn;
			$purchase -> Date = $date;
			$purchase -> Depot = $depot;
			$purchase -> Quantity = $quantity;
			$purchase -> Unit_Price = $price;
			$purchase -> Gross_Value = $total_value;
			$purchase -> Net_Value = $net_value;
			$purchase -> Season = $season;
			$purchase -> Loan_Recovery = $loan_recovery;
			$purchase -> Farmer_Reg_Fee = $farmer_registration;
			$purchase -> Other_Recoveries = $other_recoveries;
			$purchase -> Buyer = $buyer;
			$purchase -> Batch = $batch;
			$purchase -> Timestamp = date('U');
			$purchase -> save();
			$purchase = Purchase::getPurchase($purchase -> id);
			$message .= "{FGB: '" . $purchase -> FBG_Object -> Group_Name . "' DPN: '" . $purchase -> DPN . "' Date: '" . $purchase -> Date . "' $purchase: '" . $purchase -> Depot_Object -> Depot_Name . "' Quantity: '" . $purchase -> Quantity . "' Unit Price: '" . $purchase -> Unit_Price . "' Season: '" . $purchase -> Season . "' Loan Recovery: '" . $purchase -> Loan_Recovery . "' Farmer Registration Fee: '" . $purchase -> Farmer_Reg_Fee . "' Other Recoveries: '" . $purchase -> Other_Recoveries . "' Buyer: '" . $purchase -> Buyer_Object -> Name . "' Net_Value: '" . $purchase -> Net_Value . "' Gross_Value: '" . $purchase -> Gross_Value . "'}";
			$log -> Log_Message = $message;
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();

			$submit_button = $this -> input -> post("submit");
			if ($submit_button == "Save & Add New From Depot") {
				$saved_depot = $this -> session -> userdata('saved_depot');
				$url = "purchase_management/record_purchase/" . $saved_depot;
				redirect($url);
			} else if ($submit_button == "Save & View List") {
				$batch = $this -> session -> userdata('purchases_batch');
				$link = "purchase_management/listing/" . $batch;
				redirect($link);
			}
		} else {
			echo "Could not save. Error occured!";
		}
	}

	public function delete_purchase($id) {
		$purchase = Purchase::getPurchase($id);
		$purchase -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Purchase Record {FGB: '" . $purchase -> FBG_Object -> Group_Name . "' DPN: '" . $purchase -> DPN . "' Date: '" . $purchase -> Date . "' $purchase: '" . $purchase -> Depot_Object -> Depot_Name . "' Quantity: '" . $purchase -> Quantity . "' Unit Price: '" . $purchase -> Unit_Price . "' Season: '" . $purchase -> Season . "' Loan Recovery: '" . $purchase -> Loan_Recovery . "' Farmer Registration Fee: '" . $purchase -> Farmer_Reg_Fee . "' Other Recoveries: '" . $purchase -> Other_Recoveries . "' Buyer: '" . $purchase -> Buyer_Object -> Name . "' Net_Value: '" . $purchase -> Net_Value . "' Gross_Value: '" . $purchase -> Gross_Value . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('dpn', 'Daily Purchases Number', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('depot', 'Depot', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('date', 'Date', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('price', 'Unit Price', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('quantity', 'Quantity', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['link'] = "purchase_management";

		$this -> load -> view("demo_template", $data);
	}

}
