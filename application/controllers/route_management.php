<?php
class Route_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_routes = Route::getTotalRoutes();
		$routes = Route::getPagedRoutes($offset, $items_per_page);
		if ($number_of_routes > $items_per_page) {
			$config['base_url'] = base_url() . "route_management/listing/";
			$config['total_rows'] = $number_of_routes;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['routes'] = $routes;
		$data['title'] = "Routes";
		$data['content_view'] = "list_routes_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_route($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['depots'] = Depot::getAll();
		$data['field_cashiers'] = Field_Cashier::getAll();
		$data['content_view'] = "add_route_v";
		$data['quick_link'] = "new_route";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_route($id) {
		$route = Route::getRoute($id);
		$data['route'] = $route;
		$data['route_depots'] = Route_Depot::getAllForRoute($id);
		$this -> new_route($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$route = Route::getRoute($editing);
				$route_depots = Route_Depot::getAllForRoute($editing);
				foreach ($route_depots as $depot) {
					$depot -> delete();
				}
			} else {
				$route = new Route();
			}
			$route -> Route_Code = $this -> input -> post("route_code");
			$route -> Route_Name = $this -> input -> post("route_name");
			$route -> Field_Cashier = $this -> input -> post("field_cashier");
			$route -> save();
			$depots = $this -> input -> post("depot");
			foreach ($depots as $depot) {
				$route_depot = new Route_Depot();
				$route_depot -> Route = $route -> id;
				$route_depot -> Depot = $depot;
				$route_depot -> save();
			}
			redirect("route_management/listing");
		} else {
			$this -> new_route();
		}
	}

	public function delete_route($id) {
		$route = Route::getRoute($id);
		$route -> delete();
		$route_depots = Route_Depot::getAllForRoute($id);
		foreach ($route_depots as $depot) {
			$depot -> delete();
		}
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('route_code', 'Area Code', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('route_name', 'Area Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('field_cashier', 'Field_Cashier', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['link'] = "route_management";

		$this -> load -> view("demo_template", $data);
	}

}
