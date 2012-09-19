<?php
class District_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_districts = District::getTotalDistricts();
		$districts = District::getPagedDistricts($offset, $items_per_page);
		if ($number_of_districts > $items_per_page) {
			$config['base_url'] = base_url() . "district_management/listing/";
			$config['total_rows'] = $number_of_districts;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['districts'] = $districts;
		$data['title'] = "Districts";
		$data['content_view'] = "list_districts_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function search_district() {
		$search_term = $this -> input -> post("search_value2");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$districts = District::getSearchedDistrict($db_search_term);
		$data['districts'] = $districts;
		$data['listing_title'] = "District Search Results For <b>' " . $search_term . "</b>";
		$data['content_view'] = "list_districts_v";
		$this -> base_params($data);
	}

	public function new_district($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['regions'] = Region::getAll();
		$data['content_view'] = "add_district_v";
		$data['quick_link'] = "add_district";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_district($id) {
		$district = District::getDistrict($id);
		$data['district'] = $district;
		$this -> new_district($data);
	}

	public function print_districts() {
		$districts = District::getAll();
		$data_buffer = "District Name\tZone\t\n";
		foreach ($districts as $district) {
			$data_buffer .= $district -> Name . "\t" . $district -> Region_Object -> Region_Name . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Districts.xls");
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
				$district = District::getDistrict($editing);
			} else {
				$district = new District();
			}
			$district -> Region = $this -> input -> post("region");
			$district -> Name = $this -> input -> post("district_name");
			$district -> save();
			redirect("district_management/listing");
		} else {
			$this -> new_district();
		}
	}

	public function delete_district($id) {
		$district = District::getDistrict($id);
		$district -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('region', 'Zone', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('district_name', 'District Name', 'trim|required|max_length[100]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "District Management";
		$data['sub_link'] = "district_management";
		$data['link'] = "geography_management";

		$this -> load -> view("demo_template", $data);
	}

}
