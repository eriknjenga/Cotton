<?php
class Agent_Input_Issue_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_issues = Agent_Input_Issue::getTotalIssues();
		$issues = Agent_Input_Issue::getPagedIssues($offset, $items_per_page);
		if ($number_of_issues > $items_per_page) {
			$config['base_url'] = base_url() . "agent_input_issue_management/listing/";
			$config['total_rows'] = $number_of_issues;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['issues'] = $issues;
		$data['title'] = "All Farm Inputs";
		$data['content_view'] = "list_agent_input_issues_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_issue($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "issue_agent_v";
		$data['quick_link'] = "new_issue";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$data['farm_inputs'] = Farm_Input::getAll();
		$data['agents'] = Agent::getAll();
		$this -> base_params($data);
	}

	public function edit_issue($id) {
		$issue = Agent_Input_Issue::getIssue($id);
		$data['issue'] = $issue;
		$this -> new_issue($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			$delivery_note_number = $this -> input -> post("delivery_note_number");
			$date = $this -> input -> post("date");
			$farm_inputs = $this -> input -> post("farm_input");
			$quantities = $this -> input -> post("quantity");
			$total_values = $this -> input -> post("total_value");
			$seasons = $this -> input -> post("season");
			$agent = $this -> input -> post("agent");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$issue = Agent_Input_Issue::getIssue($editing);
			} else {
				$issue = new Agent_Input_Issue();
			}
			$issue -> Delivery_Note_Number = $delivery_note_number;
			$issue -> Date = $date;
			$issue -> Farm_Input = $farm_inputs[0];
			$issue -> Quantity = $quantities[0];
			$issue -> Total_Value = $total_values[0];
			$issue -> Season = $seasons[0];
			$issue -> Timestamp = date('U');
			$issue -> Agent = $agent;

			$issue -> save();

			//Loop through the rest of the disbursements
			for ($x = 1; $x < sizeof($farm_inputs); $x++) {
				$issue = new Agent_Input_Issue();
				$issue -> Delivery_Note_Number = $delivery_note_number;
				$issue -> Date = $date;
				$issue -> Farm_Input = $farm_inputs[$x];
				$issue -> Quantity = $quantities[$x];
				$issue -> Total_Value = $total_values[$x];
				$issue -> Season = $seasons[$x];
				$issue -> Timestamp = date('U');
				$issue -> Agent = $agent;
				$issue -> save();
			}
			redirect("agent_input_issue_management/listing");
		} else {
			$this -> new_issue();
		}
	}

	public function delete_issue($id) {
		$issue = Agent_Input_Issue::getIssue($id);
		$issue -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('agent', 'Agent', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('delivery_note_number', 'Delivery Note Number', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('date[]', 'Date', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('farm_input[]', 'Farm Input', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('quantity[]', 'Invoice Number', 'trim|required|xss_clean');
		return $this -> form_validation -> run();

	}

	public function base_params($data) {
		$data['link'] = "agent_input_issue_management";

		$this -> load -> view("demo_template", $data);
	}

}
