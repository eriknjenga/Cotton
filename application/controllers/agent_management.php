<?php
class Agent_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_agents= Agent::getTotalAgents();
		$agents = Agent::getPagedAgents($offset, $items_per_page);
		if ($number_of_agents > $items_per_page) {
			$config['base_url'] = base_url() . "agent_management/listing/";
			$config['total_rows'] = $number_of_agents;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['agents'] = $agents;
		$data['title'] = "Agents";
		$data['content_view'] = "list_agents_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_agent($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['agents'] = Agent::getAll();
		$data['content_view'] = "add_agent_v";
		$data['quick_link'] = "add_agent";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$this -> base_params($data);
	}

	public function edit_agent($id) {
		$agent = Agent::getAgent($id);
		$data['agent'] = $agent;
		$this -> new_agent($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$agent = Agent::getAgent($editing);
			} else {
				$agent = new Agent();
			}
			$agent -> Agent_Code = $this -> input -> post("agent_code");
			$agent -> First_Name = $this -> input -> post("first_name");
			$agent -> Surname = $this -> input -> post("surname");
			$agent -> National_Id = $this -> input -> post("national_id"); 
			$agent -> save();
			redirect("agent_management/listing");
		} else {
			$this -> new_agent();
		}
	}

	public function delete_agent($id) {
		$agent = Agent::getAgent($id);
		$agent -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('agent_code', 'Agent Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('first_name', 'First Name', 'trim|required|max_length[50]|xss_clean'); 
		$this -> form_validation -> set_rules('surname', 'Surname', 'trim|required|max_length[50]|xss_clean');
		$this -> form_validation -> set_rules('national_id', 'National Id', 'trim|required|max_length[50]|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Agent Management";
		$data['link'] = "agent_management";

		$this -> load -> view("demo_template", $data);
	}
 

}
