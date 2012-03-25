<?php
class Distributor_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_distributors= Distributor::getTotalDistributors();
		$distributors = Distributor::getPagedDistributors($offset, $items_per_page);
		if ($number_of_distributors > $items_per_page) {
			$config['base_url'] = base_url() . "area_management/listing/";
			$config['total_rows'] = $number_of_distributors;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['distributors'] = $distributors;
		$data['title'] = "Distributors";
		$data['content_view'] = "list_distributors_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_distributor($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['areas'] = Area::getAll();
		$data['content_view'] = "add_distributor_v";
		$data['quick_link'] = "add_distributor";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$this -> base_params($data);
	}

	public function edit_distributor($id) {
		$distributor = Distributor::getDistributor($id);
		$data['distributor'] = $distributor;
		$this -> new_distributor($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$distributor = Distributor::getDistributor($editing);
			} else {
				$distributor = new Distributor();
			}
			$distributor -> Distributor_Code = $this -> input -> post("distributor_code");
			$distributor -> First_Name = $this -> input -> post("first_name");
			$distributor -> Surname = $this -> input -> post("surname");
			$distributor -> National_Id = $this -> input -> post("national_id");
			$distributor -> Area = $this -> input -> post("area");  
			$distributor -> save();
			redirect("distributor_management/listing");
		} else {
			$this -> new_distributor();
		}
	}

	public function delete_distributor($id) {
		$distributor = Distributor::getDistributor($id);
		$distributor -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('distributor_code', 'Distributor Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('first_name', 'First Name', 'trim|required|max_length[50]|xss_clean'); 
		$this -> form_validation -> set_rules('surname', 'Surname', 'trim|required|max_length[50]|xss_clean');
		$this -> form_validation -> set_rules('national_id', 'National Id', 'trim|required|max_length[50]|xss_clean');
		$this -> form_validation -> set_rules('area', 'Area', 'trim|required|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Distributor Management";
		$data['link'] = "distributor_management";

		$this -> load -> view("demo_template", $data);
	}
 

}
