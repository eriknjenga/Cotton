<?php
class DPN_Sequence_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_sequences= DPN_Sequence::getTotalSequences();
		$sequences = DPN_Sequence::getPagedSequences($offset, $items_per_page);
		if ($number_of_sequences > $items_per_page) {
			$config['base_url'] = base_url() . "dpn_sequence_management/listing/";
			$config['total_rows'] = $number_of_sequences;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['sequences'] = $sequences;
		$data['title'] = "DPN Sequences";
		$data['content_view'] = "list_dpn_sequences_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_sequence($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['depots'] = Depot::getAll();
		$data['content_view'] = "add_dpn_sequence_v";
		$data['quick_link'] = "add_sequence";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_sequence($id) {
		$sequence = DPN_Sequence::getSequence($id);
		$data['sequence'] = $sequence;
		$this -> new_sequence($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$area = Area::getArea($editing);
			} else {
				$area = new Area();
			}
			$area -> Area_Code = $this -> input -> post("area_code");
			$area -> Area_Name = $this -> input -> post("area_name");
			$area -> Region = $this -> input -> post("region");  
			$area -> save();
			redirect("area_management/listing");
		} else {
			$this -> new_area();
		}
	}

	public function delete_sequence($id) {
		$sequence = DPN_Sequence::getSequence($id);
		$sequence -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('area_code', 'Area Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('area_name', 'Area Name', 'trim|required|max_length[100]|xss_clean'); 
		$this -> form_validation -> set_rules('region', 'Region', 'trim|required|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "DPN Sequence Management";
		$data['link'] = "dpn_sequence_management";
		$this -> load -> view("demo_template", $data);
	}
 

}
