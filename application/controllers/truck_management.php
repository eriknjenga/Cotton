<?php
class Truck_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_trucks = Truck::getTotalTrucks();
		$trucks = Truck::getPagedTrucks($offset, $items_per_page);
		if ($number_of_trucks > $items_per_page) {
			$config['base_url'] = base_url() . "truck_management/listing/";
			$config['total_rows'] = $number_of_trucks;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['trucks'] = $trucks;
		$data['title'] = "System Trucks";
		$data['content_view'] = "list_trucks_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_truck($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_truck_v";
		$data['quick_link'] = "new_truck";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_truck($id) {
		$truck = Truck::getTruck($id);
		$data['truck'] = $truck;
		$this -> new_truck($data);
	}

	public function print_trucks() {
		$trucks = Truck::getAll();
		$categories = array(1=>"Alliance Truck",2=>"Contracted Truck");
		$data_buffer = "Number Plate\tCategory\tCapacity\tAgreed Rate\t\n";
		foreach ($trucks as $truck) {
			$data_buffer .= $truck -> Number_Plate . "\t" . $categories[$truck -> Category] . "\t" . $truck -> Capacity . "\t" . $truck -> Agreed_Rate . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Trucks.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$log = new System_Log();
			$editing = $this -> input -> post("editing_id");
			$details_desc = "{Number Plate: '" . $this -> input -> post("number_plate") . "' Capacity: '" . $this -> input -> post("capacity") . "' Agreed Rate: '" . $this -> input -> post("agreed_rate") . "'}";
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$truck = Truck::getTruck($editing);
				$log -> Log_Type = "2";
				$log -> Log_Message = "Edited Truck Record From {Number Plate: '" . $truck -> Number_Plate . "' Capacity: '" . $truck -> Capacity . "' Agreed Rate: '" . $truck -> Agreed_Rate. "'} to " . $details_desc;
			} else {
				$truck = new Truck();
				$log -> Log_Type = "1";
				$log -> Log_Message = "Created Truck Record " . $details_desc;
			}
			$truck -> Number_Plate = $this -> input -> post("number_plate");
			$truck -> Category = $this -> input -> post("capacity");
			$truck -> Capacity = $this -> input -> post("capacity");
			$truck -> Agreed_Rate = $this -> input -> post("agreed_rate");
			$truck -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
			redirect("truck_management/listing");
		} else {
			$this -> new_truck();
		}
	}

	public function delete_truck($id) {
		$truck = Truck::getTruck($id);
		$truck -> Deleted = '1';
		$truck -> save();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Truck Record {Number Plate: '" . $truck -> Number_Plate . "' Capacity: '" . $truck -> Capacity . "' Agreed Rate: '" . $truck -> Agreed_Rate. "'} ";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();

		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('number_plate', 'Number Plate', 'trim|required|max_length[100]|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "Truck Management";
		$data['link'] = "truck_management";
		$this -> load -> view("demo_template", $data);
	}

}
