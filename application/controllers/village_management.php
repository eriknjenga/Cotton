<?php
class Village_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_villages = Village::getTotalVillages();
		$villages = Village::getPagedVillages($offset, $items_per_page);
		if ($number_of_villages > $items_per_page) {
			$config['base_url'] = base_url() . "village_management/listing/";
			$config['total_rows'] = $number_of_villages;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['villages'] = $villages;
		$data['title'] = "Villages";
		$data['content_view'] = "list_villages_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_village($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['wards'] = Ward::getAll();
		$data['content_view'] = "add_village_v";
		$data['quick_link'] = "add_village";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function print_villages() {
		$villages = Village::getAll();
		$data_buffer = "Village Name\tWard\t\n";
		foreach ($villages as $villages) {
			$data_buffer .= $villages -> Name . "\t" . $villages -> Ward_Object -> Name . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Villages.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
	}

	public function edit_village($id) {
		$village = Village::getVillage($id);
		$data['village'] = $village;
		$this -> new_village($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$village = Village::getVillage($editing);
			} else {
				$village = new Village();
			}
			$village -> Ward = $this -> input -> post("ward");
			$village -> Name = $this -> input -> post("village_name");
			$village -> save();
			redirect("village_management/listing");
		} else {
			$this -> new_village();
		}
	}

	public function delete_village($id) {
		$village = Village::getVillage($id);
		$village -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('ward', 'Ward', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('village_name', 'Village Name', 'trim|required|max_length[100]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Village Management";
		$data['sub_link'] = "village_management";
		$data['link'] = "geography_management";

		$this -> load -> view("demo_template", $data);
	}

}
