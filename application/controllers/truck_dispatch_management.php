<?php
class Truck_Dispatch_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_dispatches = Truck_Dispatch::getTotalDispatches();
		$dispatches = Truck_Dispatch::getPagedDispatches($offset, $items_per_page);
		if ($number_of_dispatches > $items_per_page) {
			$config['base_url'] = base_url() . "truck_dispatch_management/listing/";
			$config['total_rows'] = $number_of_dispatches;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['dispatches'] = $dispatches;
		$data['title'] = "Truck Dispatches";
		$data['content_view'] = "list_truck_dispatches_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_dispatch($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['depots'] = Depot::getAll();
		$data['content_view'] = "add_dispatch_v";
		$data['quick_link'] = "add_dispatch";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_dispatch($id) {
		$dispatch = Truck_Dispatch::getDispatch($id);
		$data['dispatch'] = $dispatch;
		$this -> new_dispatch($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$dispatch = Truck_Dispatch::getDispatch($editing);
			} else {
				$dispatch = new Truck_Dispatch();
			}
			$dispatch -> Depot = $this -> input -> post("depot");
			$dispatch -> Truck = $this -> input -> post("truck");
			$dispatch -> Date = $this -> input -> post("date");
			$dispatch -> save();
			redirect("truck_dispatch_management/listing");
		} else {
			$this -> new_dispatch();
		}
	}

	public function delete_dispatch($id) {
		$dispatch = Truck_Dispatch::getDispatch($id);
		$dispatch -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('depot', 'Buying Center', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('date', 'Dispatch Date', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('truck', 'Truck Registration', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Truck DispatchManagement";
		$data['link'] = "truck_dispatch_management";
		$this -> load -> view("demo_template", $data);
	}

}
