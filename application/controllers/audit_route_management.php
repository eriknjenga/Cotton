<?php
class Audit_Route_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_routes = Audit_Route::getTotalRoutes();
		$routes = Audit_Route::getPagedRoutes($offset, $items_per_page);
		if ($number_of_routes > $items_per_page) {
			$config['base_url'] = base_url() . "audit_route_management/listing/";
			$config['total_rows'] = $number_of_routes;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['routes'] = $routes;
		$data['title'] = "Routes";
		$data['content_view'] = "list_audit_routes_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_route($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['content_view'] = "add_audit_route_v";
		$data['quick_link'] = "new_audit_route";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_route($id) {
		$route = Audit_Route::getRoute($id);
		$data['route'] = $route;
		$this -> new_route($data);
	}

	public function print_routes() {
		$routes = Audit_Route::getAll();
		$data_buffer = "Route Code\tRoute Name\t\n";
		foreach ($routes as $route) {
			$data_buffer .= $route -> Route_Code . "\t" . $route -> Route_Name . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Collection Routes.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$route = Audit_Route::getRoute($editing);
			} else {
				$route = new Audit_Route();
			}
			$route -> Route_Code = $this -> input -> post("route_code");
			$route -> Route_Name = $this -> input -> post("route_name");
			$route -> save();
			redirect("audit_route_management/listing");
		} else {
			$this -> new_route();
		}
	}

	public function delete_route($id) {
		$route = Audit_Route::getRoute($id);
		$route -> delete();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('route_code', 'Area Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('route_name', 'Area Name', 'trim|required|max_length[100]|xss_clean'); 
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['sub_link'] = "audit_route_management";
		$data['link'] = "geography_management";
		$this -> load -> view("demo_template", $data);
	}

}
