<?php
class Field_Officer_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_officers = Field_Officer::getTotalOfficers();
		$officers = Field_Officer::getPagedOfficers($offset, $items_per_page);
		if ($number_of_officers > $items_per_page) {
			$config['base_url'] = base_url() . "field_officer_management/listing/";
			$config['total_rows'] = $number_of_officers;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['officers'] = $officers;
		$data['title'] = "Field Officers";
		$data['content_view'] = "list_field_officers_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function search_feo() {
		$search_term = $this -> input -> post("search_value6");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$officers = Field_Officer::getSearchedOfficer($db_search_term);
			$data['officers'] = $officers;
		$data['listing_title'] = "Ward Search Results For <b>' " . $search_term . "</b>";
		$data['content_view'] = "list_field_officers_v";
		$this -> base_params($data);
	}

	public function new_officer($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_field_officer_v";
		$data['regions'] = Region::getAll();
		$data['quick_link'] = "add_field_officer";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_officer($id) {
		$officer = Field_Officer::getOfficer($id);
		$data['officer'] = $officer;
		$this -> new_officer($data);
	}

	public function print_feos() {
		$feos = Field_Officer::getAll();
		$data_buffer = "FEO Code\tName\tNational Id\tZone\t\n";
		foreach ($feos as $feo) {
			$data_buffer .= $feo -> Officer_Code . "\t" . $feo -> Officer_Name . "\t" . $feo -> National_Id . "\t" . $feo -> Region_Object -> Region_Name . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Field Extension Officers.xls");
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
				$officer = Field_Officer::getOfficer($editing);
			} else {
				$officer = new Field_Officer();
			}
			$officer -> Officer_Name = $this -> input -> post("officer_name");
			$officer -> National_Id = $this -> input -> post("national_id");
			$officer -> Officer_Code = $this -> input -> post("officer_code");
			$officer -> Region = $this -> input -> post("region");
			$officer -> save();
			redirect("field_officer_management/listing");
		} else {
			$this -> new_officer();
		}
	}

	public function delete_officer($id) {
		$officer = Field_Officer::getOfficer($id);
		$officer -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('officer_name', 'Officer Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('national_id', 'National Id', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('officer_code', 'Officer Code', 'trim|required|max_length[10]|xss_clean');
		return $this -> form_validation -> run();

	}

	public function base_params($data) {
		$data['title'] = "Field Officer Management";
		$data['sub_link'] = "field_officer_management";
		$data['link'] = "people_management";
		$this -> load -> view("demo_template", $data);
	}

}
