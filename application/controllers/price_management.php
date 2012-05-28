<?php
class Price_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_prices = Cotton_Price::getTotalPrices();
		$prices = Cotton_Price::getPagedPrices($offset, $items_per_page);
		if ($number_of_prices > $items_per_page) {
			$config['base_url'] = base_url() . "price_management/listing/";
			$config['total_rows'] = $number_of_prices;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['prices'] = $prices;
		$data['title'] = "All Cotton Prices";
		$data['content_view'] = "list_cotton_prices_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);
	}

	public function new_price() {
		$data['content_view'] = "add_cotton_price_v";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("Validator.css");
		$this -> base_params($data);
	}

	public function save_price() {
		$valid = $this -> validate_form();
		if ($valid) {
			$log = new System_Log();
			$date = $this -> input -> post("date");
			$season = $this -> input -> post("season");
			$price = $this -> input -> post("price");
			$log -> Log_Type = "1";
			$log -> Log_Message = "Created New Cotton Price Record {Effective Date: '" . $date . "' Season: '" . $season . "' Cotton Price: '" . $price . "'}";
			$cotton_price = new Cotton_Price();
			$cotton_price -> Date = $date;
			$cotton_price -> Price = $price;
			$cotton_price -> Season = $season;
			$cotton_price -> save();
			$log -> User = $this -> session -> userdata('user_id');
			$log -> Timestamp = date('U');
			$log -> save();
		} else {
			$this -> new_price();
		}
		redirect("price_management/listing");
	}

	public function delete_price($id) {
		$price = Cotton_Price::getPrice($id);
		$price -> delete();
		$log = new System_Log();
		$log -> Log_Type = "3";
		$log -> Log_Message = "Deleted Cotton Price Record {Effective Date: '" . $price -> Date . "' Season: '" . $price -> Season . "' Cotton Price: '" . $price -> Price . "'}";
		$log -> User = $this -> session -> userdata('user_id');
		$log -> Timestamp = date('U');
		$log -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('date', 'Effective Date', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('season', 'Season', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('price', 'Price', 'trim|required|max_length[100]|xss_clean');
		return $this -> form_validation -> run();

	}

	public function base_params($data) {
		$data['title'] = "Cotton Price Management";
		$data['link'] = "price_management";

		$this -> load -> view("demo_template", $data);
	}

}
