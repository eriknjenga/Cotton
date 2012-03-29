<?php
class Farm_Input_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_inputs = Farm_Input::getTotalInputs();
		$inputs = Farm_Input::getPagedInputs($offset, $items_per_page);
		if ($number_of_inputs > $items_per_page) {
			$config['base_url'] = base_url() . "farm_input_management/listing/";
			$config['total_rows'] = $number_of_inputs;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['inputs'] = $inputs;
		$data['title'] = "All Farm Inputs";
		$data['content_view'] = "list_farm_inputs_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_input($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_farm_input_v";
		$data['quick_link'] = "add_farm_input";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$this -> base_params($data);
	}

	public function edit_input($id) {
		$input = Farm_Input::getInput($id);
		$data['input'] = $input;
		$this -> new_input($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$input = Farm_Input::getInput($editing);
			} else {
				$input = new Farm_Input();
			}
			$input -> Product_Code = $this -> input -> post("product_code");
			$input -> Product_Name = $this -> input -> post("product_name");
			$input -> Product_Description = $this -> input -> post("product_desc");
			$input -> Unit_Price = $this -> input -> post("unit_price");
			$input -> save();
			redirect("farm_input_management/listing");
		} else {
			$this -> new_input();
		}
	}

	public function delete_input($id) {
		$input = Farm_Input::getInput($id);
		$input -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('product_code', 'Product Code', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('product_name', 'Product Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('unit_price', 'Unit Price', 'trim|required|max_length[10]|xss_clean');
		return $this -> form_validation -> run();

	}

	public function base_params($data) {
		$data['title'] = "Farmer Inputs Management";
		$data['link'] = "farm_input_management";

		$this -> load -> view("demo_template", $data);
	}

	public function issue_to_distributor() {
		$data['content_view'] = "issue_distributor_v";
		$data['quick_link'] = "issue_to_distributor";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$data['farm_inputs'] = Farm_Input::getAll();
		$data['distributors'] = Distributor::getAll();
		$this -> base_params($data);
	}

	public function get_loan_movement_graph_data() {
		echo '<chart caption="Total Loans Outstanding" xAxisName="Month" yAxisName="Total Value (Millions)" showValues="0" decimals="0" formatNumberScale="0">
<set label="Jan" value="462"/>
<set label="Feb" value="857"/>
<set label="Mar" value="671"/>
<set label="Apr" value="494"/>
<set label="May" value="761"/>
<set label="Jun" value="960"/>
<set label="Jul" value="629"/>
<set label="Aug" value="622"/>
<set label="Sep" value="376"/>
<set label="Oct" value="494"/>
<set label="Nov" value="761"/>
<set label="Dec" value="960"/>
</chart>';
	}

}
