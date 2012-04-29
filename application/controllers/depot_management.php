<?php
class Depot_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_depots = Depot::getTotalDepots();
		$depots = Depot::getPagedDepots($offset, $items_per_page);
		if ($number_of_depots > $items_per_page) {
			$config['base_url'] = base_url() . "depot_management/listing/";
			$config['total_rows'] = $number_of_depots;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['depots'] = $depots;
		$data['title'] = "Depots";
		$data['content_view'] = "list_depots_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function search_depot() {
		$data['content_view'] = "search_depot_v";
		$data['quick_link'] = "search_depot";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$data['search_title'] = "Search For a Depot";
		$this -> base_params($data);
	}

	public function search($search_term = '', $offset = 0) {
		if ($search_term == '') {
			$search_term = $this -> input -> post("search_value");
		}
		if (strlen($search_term) == 0) {
			redirect("depot_management/search_depot");
		}
		$items_per_page = 10;
		$number_of_depots = Depot::getTotalSearchedDepots($search_term);
		$depots = Depot::getPagedSearchedDepots($search_term, $offset, $items_per_page);
		if ($number_of_depots > $items_per_page) {
			$config['base_url'] = base_url() . "depot_management/search/" . $search_term . "/";
			$config['total_rows'] = $number_of_depots;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 4;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['depots'] = $depots;
		$data['listing_title'] = "Depot Search Results For '$search_term'";
		$data['title'] = "Cotton Depots";
		$data['content_view'] = "list_depots_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function new_depot($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['buyers'] = Buyer::getAll();
		$data['regions'] = Region::getAll();
		$data['content_view'] = "add_depot_v";
		$data['quick_link'] = "add_depot";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_depot($id) {
		$depot = Depot::getDepot($id);
		$data['depot'] = $depot;
		$this -> new_depot($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$temp_depot = new Depot();
			$temp_depot -> Buyer = $this -> input -> post("buyer");
			$temp_depot -> Region = $this -> input -> post("region");
			$details_desc = "{Depot Code: '" . $this -> input -> post("depot_code") . "' Depot Name: '" . $this -> input -> post("depot_name") . "' Buyer: '" . $temp_depot -> Buyer_Object -> Name . "' Region: '" . $temp_depot -> Region_Object -> Region_Name . "'}";
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$depot = Depot::getDepot($editing);
				$log -> Log_Type = "2";
				$log -> Log_Message = "Edited Depot Record From {Depot Code: '" . $depot -> Depot_Code . "' Depot Name: '" . $depot -> Depot_Name . "' Buyer: '" . $depot -> Buyer_Object -> Name . "' Region: '" . $depot -> Region_Object -> Region_Name . "'} to " . $details_desc;
			} else {
				$depot = new Depot();
				$log -> Log_Type = "1";
				$log -> Log_Message = "Created Depot Record " . $details_desc;
			}
			$depot -> Depot_Code = $this -> input -> post("depot_code");
			$depot -> Depot_Name = $this -> input -> post("depot_name");
			$depot -> Buyer = $this -> input -> post("buyer");
			$depot -> Region = $this -> input -> post("region");
			$depot -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			redirect("depot_management/listing");
		} else {
			$this -> new_depot();
		}
	}

	public function delete_depot($id) {
		$depot = Depot::getDepot($id);
		$depot -> Deleted = '1';
		$depot -> save();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Depot Record {Depot Code: '" . $depot -> Depot_Code . "' Depot Name: '" . $depot -> Depot_Name . "' Buyer: '" . $depot -> Buyer_Object -> Name . "' Region: '" . $depot -> Region_Object -> Region_Name . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('depot_code', 'Depot Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('depot_name', 'Depot Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('buyer', 'Buyer', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Depot Management";
		$data['link'] = "depot_management";

		$this -> load -> view("demo_template", $data);
	}

}
