<?php
class FBG_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
		$this -> load -> database();
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_fbgs = FBG::getTotalFbgs();
		$fbgs = FBG::getPagedFbgs($offset, $items_per_page);
		if ($number_of_fbgs > $items_per_page) {
			$config['base_url'] = base_url() . "fbg_management/listing/";
			$config['total_rows'] = $number_of_fbgs;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['fbgs'] = $fbgs;
		$data['title'] = "Farmer Business Groups";
		$data['listing_title'] = "FBG Listing";
		$data['content_view'] = "list_fbgs_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function search($search_term = '', $offset = 0) {
		$db_search_term = "";
		if ($search_term == '') {
			$search_term = $this -> input -> post("search_value");
		}
		if (strlen($search_term) == 0) {
			redirect("fbg_management/search_fbg");
		}
		$this -> load -> database();
		$search_term = urldecode($search_term);
		$db_search_term = $this -> db -> escape_str(urldecode($search_term));
		$search_term = urlencode($search_term);
		$items_per_page = 10;
		$number_of_fbgs = FBG::getTotalSearchedFbgs($db_search_term);
		$fbgs = FBG::getPagedSearchedFbgs($db_search_term, $offset, $items_per_page);
		if ($number_of_fbgs > $items_per_page) {
			$config['base_url'] = base_url() . "fbg_management/search/" . $search_term . "/";
			$config['total_rows'] = $number_of_fbgs;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['fbgs'] = $fbgs;
		$data['listing_title'] = "FBG Search Results For <b>' " . urldecode($search_term) . "</b> '";
		$data['title'] = "Farmer Business Groups";
		$data['content_view'] = "list_fbgs_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function new_fbg($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['field_officers'] = Field_Officer::getAll();
		$data['content_view'] = "add_fbg_v";
		$data['quick_link'] = "add_fbg";
		$data['scripts'] = array("validationEngine-en.js", "validator.js", "jquery.ui.autocomplete.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function search_fbg() {
		$data['content_view'] = "search_fbg_v";
		$data['quick_link'] = "search_fbg";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$data['search_title'] = "Search For an FBG";
		$this -> base_params($data);
	}

	public function autocomplete_fbg() {
		$search_term = $this -> input -> post("term");
		if (strlen($search_term) == 0) {
			redirect("fbg_management/search_fbg");
		}
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		//Limit search results to 10
		$fbgs = FBG::getPagedSearchedFbgs($db_search_term, 0, 10);
		$final_results = array();
		$counter = 0;
		foreach ($fbgs as $fbg) {
			$fbg_details = $fbg -> Group_Name . " (" . $fbg -> Village_Object -> Name . ")";
			$final_results[$counter] = array("value" => $fbg -> id, "label" => $fbg_details);
			$counter++;
		}
		echo json_encode($final_results);
	}

	public function autocomplete_village() {
		$this -> load -> database();
		$search_term = $this -> input -> post("term");
		//Limit search results to 10
		$villages = Village::getPagedSearchedVillages($this -> db -> escape_str($search_term), 0, 10);
		$final_results = array();
		$counter = 0;
		foreach ($villages as $village) {
			$village_details = $village -> Name . " (" . $village -> Ward_Object -> Name . ")";
			$final_results[$counter] = array("value" => $village -> id, "label" => $village_details);
			$counter++;
		}
		echo json_encode($final_results);
	}

	public function edit_fbg($id) {
		$fbg = FBG::getFbg($id);
		$data['fbg'] = $fbg;
		$this -> new_fbg($data);
	}

	public function print_fbgs() {
		$fbgs = FBG::getAll();
		$data_buffer = "Contract Number\tGroup Name\tField Extension Officer\tHectares Available\tChairman Name\tChairman Phone\tSecretary Name\tSecretary Phone\tVillage\t\n";
		foreach ($fbgs as $fbg) {
			$data_buffer .= $fbg -> CPC_Number . "\t" . $fbg -> Group_Name ."\t" . $fbg -> Officer_Object->Officer_Name ."\t" . $fbg -> Hectares_Available ."\t" . $fbg -> Chairman_Name ."\t" . $fbg -> Chairman_Phone ."\t" . $fbg -> Secretary_Name ."\t" . $fbg -> Secretary_Phone ."\t" . $fbg -> Village_Object->Name . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System FBGs.xls");
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
				$fbg = FBG::getFbg($editing);
			} else {
				$fbg = new FBG();
			}
			$fbg -> CPC_Number = $this -> input -> post("cpc_number");
			$fbg -> Group_Name = $this -> input -> post("group_name");
			$fbg -> Chairman_Name = $this -> input -> post("chairman_name");
			$fbg -> Chairman_Phone = $this -> input -> post("chairman_phone");
			$fbg -> Secretary_Name = $this -> input -> post("secretary_name");
			$fbg -> Secretary_Phone = $this -> input -> post("secretary_phone");
			$fbg -> Field_Officer = $this -> input -> post("field_officer");
			$fbg -> Village = $this -> input -> post("village_id");
			$fbg -> Hectares_Available = $this -> input -> post("hectares_available");
			$fbg -> save();
			redirect("fbg_management/listing");
		} else {
			$this -> new_fbg();
		}
	}

	public function delete_fbg($id) {
		$fbg = FBG::getFbg($id);
		$fbg -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() { 
		$this -> form_validation -> set_rules('group_name', 'First Name', 'trim|required|max_length[100]|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Farmer Management";
		$data['link'] = "fbg_management";
		$this -> load -> view("demo_template", $data);
	}

}
