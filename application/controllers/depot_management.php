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
		$data['search_title'] = "Search For a Buying Center";
		$this -> base_params($data);
	}

	public function search($search_term = '', $offset = 0) {
		$db_search_term = "";
		if ($search_term == '') {
			$search_term = $this -> input -> post("search_value");
		}
		if (strlen($search_term) == 0) {
			redirect("depot_management/search_depot");
		}
		$this -> load -> database();
		$search_term = urldecode($search_term);
		$db_search_term = $this -> db -> escape_str(urldecode($search_term));
		$search_term = urlencode($search_term);
		$items_per_page = 10;
		$number_of_depots = Depot::getTotalSearchedDepots($db_search_term);
		$depots = Depot::getPagedSearchedDepots($db_search_term, $offset, $items_per_page);
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
		$data['listing_title'] = "Depot Search Results For <b>' ".urldecode($search_term)."</b>";
		$data['title'] = "Cotton Depots";
		$data['content_view'] = "list_depots_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function new_depot($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_depot_v";
		$data['quick_link'] = "add_depot";
		$data['scripts'] = array("validationEngine-en.js", "validator.js", "jquery.ui.autocomplete.js");
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
			$temp_depot -> Buyer = $this -> input -> post("buyer_id");
			$temp_depot -> Village = $this -> input -> post("village_id");
			$fbg = array("0" => "No", "1" => "Yes");
			$details_desc = "{Code: '" . $this -> input -> post("depot_code") . "' Name: '" . $this -> input -> post("depot_name") . "' Capacity: '" . $this -> input -> post("capacity") . "' Distance: '" . $this -> input -> post("distance") . "' FBG/Non-FBG: '" . $fbg[$this -> input -> post("fbg")] . "' Acreage: '" . $this -> input -> post("acreage") . "' Yield/Acre: '" . $this -> input -> post("acre_yield") . "' Buyer: '" . $temp_depot -> Buyer_Object -> Name . "' Village: '" . $temp_depot -> Village_Object -> Name . "'}";
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$depot = Depot::getDepot($editing);
				$log -> Log_Type = "2";
				$log -> Log_Message = "Edited Buying Center Record From {Code: '" . $depot -> Depot_Code . "' Name: '" . $depot -> Depot_Name . "' Capacity: '" . $depot -> Capacity . "' Distance: '" . $depot -> Distance . "' FBG/Non-FBG: '" . $fbg[$depot -> FBG] . "' Acreage: '" . $depot -> Acreage . "' Yield/Acre: '" . $depot -> Acre_Yield . "'  Buyer: '" . $depot -> Buyer_Object -> Name . "' Village: '" . $depot -> Village_Object -> Name . "'} to " . $details_desc;
			} else {
				$depot = new Depot();
				$log -> Log_Type = "1";
				$log -> Log_Message = "Created Buying Center Record " . $details_desc;
			}
			$depot -> Depot_Code = $this -> input -> post("depot_code");
			$depot -> Depot_Name = $this -> input -> post("depot_name");
			$depot -> Buyer = $this -> input -> post("buyer_id");
			$depot -> Village = $this -> input -> post("village_id");
			$depot -> Capacity = $this -> input -> post("capacity");
			$depot -> Distance = $this -> input -> post("distance");
			$depot -> FBG = $this -> input -> post("fbg");
			$depot -> Acreage = $this -> input -> post("acreage");
			$depot -> Acre_Yield = $this -> input -> post("acre_yield");
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
		$fbg = array("0" => "No", "1" => "Yes");
		$depot -> save();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Buying Center Record {Code: '" . $depot -> Depot_Code . "' Name: '" . $depot -> Depot_Name . "' Capacity: '" . $depot -> Capacity . "' Distance: '" . "' FBG/Non-FBG: '" . $fbg[$depot -> FBG] . "' Acreage: '" . $depot -> Acreage . "' Yield/Acre: '" . $depot -> Acre_Yield . $depot -> Distance . "' Buyer: '" . $depot -> Buyer_Object -> Name . "' Village: '" . $depot -> Village_Object -> Name . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function close_center($id) {
		$depot = Depot::getDepot($id);
		$data['depot'] = $depot;
		$data['content_view'] = "close_depot_v";
		$data['scripts'] = array("validationEngine-en.js", "validator.js", "jquery.ui.autocomplete.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function save_closure() {
		$log = new System_Log();
		$depot = Depot::getDepot($this -> input -> post("buying_center"));
		$log -> Log_Type = "1";
		$details_desc = "{Code: '" . $depot -> Depot_Code . "' Name: '" . $depot -> Depot_Name . "' Capacity: '" . $depot -> Capacity . "' Distance: '" . "' FBG/Non-FBG: '" . $fbg[$depot -> FBG] . "' Acreage: '" . $depot -> Acreage . "' Yield/Acre: '" . $depot -> Acre_Yield . $depot -> Distance . "' Buyer: '" . $depot -> Buyer_Object -> Name . "' Village: '" . $depot -> Village_Object -> Name . "' Closure Date:  '" . $this -> input -> post("date") . "' Reason:  '" . $this -> input -> post("reason") . "' }";
		$log -> Log_Message = "Closed Buying Center " . $details_desc;
		$depot_closure = new Depot_Closure();
		$depot_closure -> Depot = $this -> input -> post("buying_center");
		$depot_closure -> Date_Closed = $this -> input -> post("date");
		$depot_closure -> Reason = $this -> input -> post("reason");
		$depot_closure -> save();
		$depot -> Deleted = "2";
		$depot -> save();
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		redirect("depot_management/listing");
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('depot_name', 'Buying Center Name', 'trim|required|max_length[100]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Buying Center Management";
		$data['link'] = "depot_management";

		$this -> load -> view("demo_template", $data);
	}

}
