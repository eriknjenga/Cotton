<?php
class Buyer_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_buyers = Buyer::getTotalBuyers();
		$buyers = Buyer::getPagedBuyers($offset, $items_per_page);
		if ($number_of_buyers > $items_per_page) {
			$config['base_url'] = base_url() . "buyer_management/listing/";
			$config['total_rows'] = $number_of_buyers;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['buyers'] = $buyers;
		$data['title'] = "System Buyers";
		$data['content_view'] = "list_buyers_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_buyer($data = null) {
		if ($data == null) {
			$data = array();
		} 
		$data['content_view'] = "add_buyer_v";
		$data['quick_link'] = "add_buyer";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$this -> base_params($data);
	}

	public function edit_buyer($id) {
		$buyer = Buyer::getBuyer($id);
		$data['buyer'] = $buyer;
		$this -> new_buyer($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$buyer = Buyer::getBuyer($editing);
			} else {
				$buyer = new Buyer();
			}
			$buyer -> Buyer_Code = $this -> input -> post("buyer_code");
			$buyer -> National_Id = $this -> input -> post("national_id");
			$buyer -> Phone_Number = $this -> input -> post("phone_number");
			$buyer -> Name = $this -> input -> post("name");
			$buyer -> save();
			redirect("buyer_management/listing");
		} else {
			$this -> new_buyer();
		}
	}

	public function delete_buyer($id) {
		$buyer = Buyer::getBuyer($id);
		$buyer -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('buyer_code', 'Buyer Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('name', 'Buyer Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('national_id', 'National Id', 'trim|required|max_length[30]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Buyer Management";
		$data['link'] = "buyer_management";

		$this -> load -> view("demo_template", $data);
	}

}
