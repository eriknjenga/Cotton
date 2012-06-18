<?php
class Region_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_regions = Region::getTotalRegions();
		$regions = Region::getPagedRegions($offset, $items_per_page);
		if ($number_of_regions > $items_per_page) {
			$config['base_url'] = base_url() . "region_management/listing/";
			$config['total_rows'] = $number_of_regions;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['regions'] = $regions;
		$data['title'] = "Regions";
		$data['content_view'] = "list_regions_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_region($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_region_v";
		$data['quick_link'] = "add_region";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_region($id) {
		$region = Region::getRegion($id);
		$data['region'] = $region;
		$this -> new_region($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$region = Region::getRegion($editing);
			} else {
				$region = new Region();
			}
			$region -> Region_Code = $this -> input -> post("region_code");
			$region -> Region_Name = $this -> input -> post("region_name"); 
			$region -> save();
			redirect("region_management/listing");
		} else {
			$this -> new_region();
		}
	}

	public function delete_region($id) {
		$region = Region::getRegion($id);
		$region -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('region_code', 'Zone Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('region_name', 'Zone Name', 'trim|required|max_length[100]|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Region Management";
		$data['sub_link'] = "region_management";
		$data['link'] = "geography_management";

		$this -> load -> view("demo_template", $data);
	}


}
