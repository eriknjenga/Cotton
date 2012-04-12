<?php
class Cash_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_disbursements = Cash_Disbursement::getTotalDisbursements();
		$disbursements = Cash_Disbursement::getPagedDisbursements($offset, $items_per_page);
		if ($number_of_disbursements > $items_per_page) {
			$config['base_url'] = base_url() . "cash_management/listing/";
			$config['total_rows'] = $number_of_disbursements;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['disbursements'] = $disbursements;
		$data['title'] = "Cash Disbursements";
		$data['content_view'] = "list_cash_disbursements_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function issue_cash($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['field_cashiers'] = Field_Cashier::getAll();
		$data['content_view'] = "add_cash_disbursement_v";
		$data['quick_link'] = "issue_cash";
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
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$disbursement = Cash_Disbursement::getDisbursement($editing);
			} else {
				$disbursement = new Cash_Disbursement();
			}
			$disbursement -> Field_Cashier = $this -> input -> post("field_cashier");
			$disbursement -> Amount = $this -> input -> post("amount");
			$disbursement -> CIH = $this -> input -> post("cih");
			$disbursement -> Date = $this -> input -> post("date");
			$disbursement -> save();
			redirect("cash_management/listing");
		} else {
			$this -> issue_cash();
		}
	}

	public function delete_disbursement($id) {
		$disbursement = Cash_Disbursement::getDisbursement($id);
		$disbursement -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('field_cashier', 'Field Cashier', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('amount', 'Amount Disbursed', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('cih', 'CIH Voucher', 'trim|required|max_length[50]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Cash Management";
		$data['link'] = "cash_management";
		$this -> load -> view("demo_template", $data);
	}

}
