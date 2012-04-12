<?php
class Purchase_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_purchases = Purchase::getTotalPurchases();
		$purchases = Purchase::getPagedPurchases($offset, $items_per_page);
		if ($number_of_purchases > $items_per_page) {
			$config['base_url'] = base_url() . "purchase_management/listing/";
			$config['total_rows'] = $number_of_purchases;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['purchases'] = $purchases;
		$data['title'] = "Cotton Disbursements";
		$data['content_view'] = "list_purchases_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_purchase($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['depots'] = Depot::getAll();
		$data['prices'] = Cotton_Price::getCottonPrices();
		$data['content_view'] = "add_purchase_v";
		$data['quick_link'] = "add_purchase";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_purchase($id) {
		$purchase = Purchase::getPurchase($id);
		$fbg = $purchase->FBG;
		$recipient = FBG::getFbg($fbg);
		$data['disbursements'] = Disbursement::getFBGDisbursements($fbg);
		$data['purchase'] = $purchase;
		$this -> new_purchase($data);
	}

	public function purchase_produce($fbg) {
		$recipient = FBG::getFbg($fbg);
		$data['disbursements'] = Disbursement::getFBGDisbursements($fbg);
		$data['fbg'] = $recipient;
		$this -> new_purchase($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			$dpn = $this -> input -> post("dpn");
			$date = $this -> input -> post("date");
			$quantity = $this -> input -> post("quantity");
			$total_value = $this -> input -> post("purchased_value");
			$season = $this -> input -> post("season");
			$fbg = $this -> input -> post("fbg");
			$depot = $this -> input -> post("depot");
			$loan_recovery = $this -> input -> post("loan_recovery");
			$farmer_registration = $this -> input -> post("farmer_registration");
			$other_recoveries = $this -> input -> post("other_recoveries");
			$buyer = $this -> input -> post("buyer");
			$net_value = $this -> input -> post("net_value");
			$price = $this -> input -> post("price");

			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$purchase = Purchase::getPurchase($editing);
			} else {
				$purchase = new Purchase();
			}
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
			$purchase -> Timestamp = date('U');
			$purchase -> save();
			redirect("purchase_management/listing");
		} else {
			$this -> new_disbursement();
		}
	}

	public function delete_purchase($id) {
		$purchase = Purchase::getPurchase($id);
		$purchase -> delete();
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
