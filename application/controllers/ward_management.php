<?php
class Ward_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_wards = Ward::getTotalWards();
		$wards = Ward::getPagedWards($offset, $items_per_page);
		if ($number_of_wards > $items_per_page) {
			$config['base_url'] = base_url() . "ward_management/listing/";
			$config['total_rows'] = $number_of_wards;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['wards'] = $wards;
		$data['title'] = "Wards";
		$data['content_view'] = "list_wards_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_ward($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['regions'] = Region::getAll();
		$data['content_view'] = "add_ward_v";
		$data['quick_link'] = "add_ward";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_ward($id) {
		$ward = Ward::getWard($id);
		$data['ward'] = $ward;
		$this -> new_ward($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$ward = Ward::getWard($editing);
			} else {
				$ward = new Ward();
			}
			$ward -> Region = $this -> input -> post("region");
			$ward -> Name = $this -> input -> post("ward_name"); 
			$ward -> save();
			redirect("ward_management/listing");
		} else {
			$this -> new_ward();
		}
	}

	public function delete_ward($id) {
		$ward = Ward::getWard($id);
		$ward -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('region', 'Zone', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('ward_name', 'Ward Name', 'trim|required|max_length[100]|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Ward Management";
		$data['sub_link'] = "ward_management";
		$data['link'] = "geography_management";

		$this -> load -> view("demo_template", $data);
	}


}
