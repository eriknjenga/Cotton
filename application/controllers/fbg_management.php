<?php
class FBG_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_fbgs= FBG::getTotalFbgs();
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
		$data['content_view'] = "list_fbgs_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_fbg($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['distributors'] = Distributor::getAll();
		$data['content_view'] = "add_fbg_v";
		$data['quick_link'] = "add_fbg";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$this -> base_params($data);
	}

	public function edit_fbg($id) {
		$fbg = FBG::getFbg($id);
		$data['fbg'] = $fbg;
		$this -> new_fbg($data);
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
			$fbg -> GD_Id = $this -> input -> post("gd_id");
			$fbg -> CPC_Number = $this -> input -> post("cpc_number");
			$fbg -> Group_Name = $this -> input -> post("group_name"); 
			$fbg -> Distributor = $this -> input -> post("distributor");  
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
		$this -> form_validation -> set_rules('gd_id', 'GD Id', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('cpc_number', 'CPC Number', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('group_name', 'First Name', 'trim|required|max_length[100]|xss_clean');  
		$this -> form_validation -> set_rules('distributor', 'Distributor', 'trim|required|max_length[20]|xss_clean'); 
		$this -> form_validation -> set_rules('hectares_available', 'Hectares Available', 'trim|required|max_length[20]|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Farmer Business Group Management";
		$data['link'] = "fbg_management";

		$this -> load -> view("demo_template", $data);
	}
 

}
