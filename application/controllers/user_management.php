<?php
class User_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> listing();
	}

	public function login() {
		$data = array();
		$data['title'] = "System Login";
		$this -> load -> view("login_v", $data);
	}

	public function logout() {
		//destroy the session first
		$this -> session -> sess_destroy();
		redirect("user_management/login");
	}

	public function authenticate() {
		$data = array();
		$validated = $this -> _submit_validate();
		if ($validated) {
			$username = $this -> input -> post("username");
			$password = $this -> input -> post("password");
			$remember = $this -> input -> post("remember");
			$logged_in = User::login($username, $password);
			//This code checks if the credentials are valid
			if ($logged_in == false) {
				$data['invalid'] = true;
				$data['title'] = "System Login";
				$this -> load -> view("login_v", $data);
			}
			//If the credentials are valid, continue
			else {
				//check to see whether the user is active
				if ($logged_in -> Active == "0") {
					$data['inactive'] = true;
					$data['title'] = "System Login";
					$this -> load -> view("login_v", $data);
				}
				//looks good. Continue!
				else {
					$session_data = array('user_id' => $logged_in -> id, 'user_indicator' => $logged_in -> Access -> Indicator, 'access_level' => $logged_in -> Access_Level, 'full_name' => $logged_in -> Name);
					$this -> session -> set_userdata($session_data);

					redirect("home_controller");
				}

			}

		} else {
			$data = array();
			$data['title'] = "System Login";
			$this -> load -> view("login_v", $data);
		}
		redirect("home_controller");
	}

	public function listing($offset = 0) {
		$items_per_page = 20;
		$number_of_users = User::getTotalUsers();
		$users = User::getPagedUsers($offset, $items_per_page);
		if ($number_of_users > $items_per_page) {
			$config['base_url'] = base_url() . "user_management/listing/";
			$config['total_rows'] = $number_of_users;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}
		$data['users'] = $users;
		$data['title'] = "Users";
		$data['content_view'] = "list_users_v";
		$data['styles'] = array("pagination.css");
		$this -> base_params($data);

	}

	public function new_user($data = null) {
		if ($data == null) {
			$data = array();
		}
		$data['access_levels'] = Access_Level::getAll();
		$data['content_view'] = "add_user_v";
		$data['quick_link'] = "add_user";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$this -> base_params($data);
	}

	public function edit_user($id) {
		$user = User::getUser($id);
		$data['user'] = $user;
		$this -> new_user($data);
	}

	public function save() {
		$valid = $this -> validate_form();
		//If the fields have been validated, save the input
		if ($valid) {
			$editing = $this -> input -> post("editing_id");
			//Check if we are editing the record first
			if (strlen($editing) > 0) {
				$user = User::getUser($editing);
			} else {
				$user = new User();
			}

			$user -> Name = $this -> input -> post("name");
			$user -> Username = $this -> input -> post("username");
			$user -> Password = $this -> input -> post("password");
			$user -> Access_Level = $this -> input -> post("access_level");
			$user -> Phone_Number = $this -> input -> post("phone");
			$user -> Created_By = $this -> session -> userdata('user_id');
			$user -> Email_Address = $this -> input -> post("email");
			$user -> Time_Created = date('U');
			$user -> Active = '1';

			$user -> save();
			redirect("user_management/listing");
		} else {
			$this -> new_user();
		}
	}

	public function delete_user($id) {
		$user = User::getUser($id);
		$user -> Active = '0';
		$user->save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form() {
		$this -> form_validation -> set_rules('name', 'Full Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('username', 'Username', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('password', 'Password', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('access_level', 'Access Level', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

	private function _submit_validate() {
		// validation rules
		$this -> form_validation -> set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[20]');
		$this -> form_validation -> set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[20]');
		return $this -> form_validation -> run();
	}

	public function base_params($data) {
		$data['title'] = "User Management";
		$data['link'] = "user_management";

		$this -> load -> view("demo_template", $data);
	}

}
