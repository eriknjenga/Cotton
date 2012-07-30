<?php
class Field_Cashier_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_field_cashiers = Field_Cashier::getTotalFieldCashiers();
		$field_cashiers = Field_Cashier::getPagedFieldCashiers($offset, $items_per_page);
		if ($number_of_field_cashiers > $items_per_page) {
			$config['base_url'] = base_url() . "field_cashier_management/listing/";
			$config['total_rows'] = $number_of_field_cashiers;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['field_cashiers'] = $field_cashiers;
		$data['title'] = "Field Cashiers";
		$data['content_view'] = "list_field_cashiers_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_field_cashier($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_field_cashier_v";
		$data['quick_link'] = "add_field_cashier";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_field_cashier($id) {
		$field_cashier = Field_Cashier::getFieldCashier($id);
		$data['field_cashier'] = $field_cashier;
		$this -> new_field_cashier($data);
	}

	public function print_cashiers() {
		$cashiers = Field_Cashier::getAll();
		$data_buffer = "Cashier Code\tFull Name\tNational Id\tPhone Number\t\n";
		foreach ($cashiers as $cashier) {
			$data_buffer .= $cashier -> Field_Cashier_Code . "\t" . $cashier -> Field_Cashier_Name ."\t" . $cashier -> National_Id."\t" . $cashier -> Phone_Number. "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Field Cashiers.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$field_cashier = Field_Cashier::getFieldCashier($editing);
			} else {
				$field_cashier = new Field_Cashier();
			}
			$field_cashier -> Field_Cashier_Code = $this -> input -> post("field_cashier_code");
			$field_cashier -> National_Id = $this -> input -> post("national_id");
			$field_cashier -> Phone_Number = $this -> input -> post("phone_number");
			$field_cashier -> Field_Cashier_Name = $this -> input -> post("field_cashier_name");
			$field_cashier -> save();
			redirect("field_cashier_management/listing");
		} else {
			$this -> new_field_cashier();
		}
	}

	public function delete_field_cashier($id) {
		$field_cashier = Field_Cashier::getFieldCashier($id);
		$field_cashier -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('field_cashier_code', 'Field Cashier Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('field_cashier_name', 'Field Cashier Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('national_id', 'National Id', 'trim|required|max_length[30]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['link'] = "field_cashier_management";

		$this -> load -> view("demo_template", $data);
	}

}
