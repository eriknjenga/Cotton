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
		//destroy the sessions first
		$this -> session -> sess_destroy();
		$this -> quick_menus -> sess_destroy();
		$this -> menus -> sess_destroy();
		redirect("user_management/login");
	}

	public function search_user() {
		$search_term = $this -> input -> post("search_value5");
		$this -> load -> database();
		$db_search_term = $this -> db -> escape_str($search_term);
		$users = User::getSearchedUser($db_search_term);
		$data['users'] = $users;
		$data['title'] = "Users";
		$data['content_view'] = "list_users_v";
		$data['listing_title'] = "Ward Search Results For <b>' " . $search_term . "</b>";
		$this -> base_params($data);
	}

	public function change_password() {
		$data = array();
		$data['title'] = "Change User Password";
		$data['content_view'] = "change_password_v";
		$data['scripts'] = array("validationEngine-en.js", "validator.js");
		$data['styles'] = array("validator.css");
		$data['link'] = "home";
		$this -> load -> view('demo_template', $data);
	}

	public function save_new_password() {
		$valid = $this -> _submit_validate_password();
		if ($valid) {
			$user = User::getUser($this -> session -> userdata('user_id'));
			$user -> Password = $this -> input -> post("new_password");
			$user -> save();
			redirect("user_management/logout");
		} else {
			$this -> change_password();
		}
	}

	private function _submit_validate_password() {
		// validation rules
		$this -> form_validation -> set_rules('old_password', 'Current Password', 'trim|required|min_length[6]|max_length[20]');
		$this -> form_validation -> set_rules('new_password', 'New Password', 'trim|required|min_length[6]|max_length[20]|matches[new_password_confirm]');
		$this -> form_validation -> set_rules('new_password_confirm', 'New Password Confirmation', 'trim|required|min_length[6]|max_length[20]');
		$temp_validation = $this -> form_validation -> run();
		if ($temp_validation) {
			$this -> form_validation -> set_rules('old_password', 'Current Password', 'trim|required|callback_correct_current_password');
			return $this -> form_validation -> run();
		} else {
			return $temp_validation;
		}

	}

	public function correct_current_password($pass) {
		$user = User::getUser($this -> session -> userdata('user_id'));
		$dummy_user = new User();
		$dummy_user -> Password = $pass;
		if ($user -> Password != $dummy_user -> Password) {
			$this -> form_validation -> set_message('correct_current_password', 'The current password you provided is not correct.');
			return FALSE;
		} else {
			return TRUE;
		}

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
					$session_data = array('user_id' => $logged_in -> id, 'user_indicator' => $logged_in -> Access -> Indicator, 'management_type' => $logged_in -> Access -> Management_Type, 'access_level' => $logged_in -> Access_Level, 'full_name' => $logged_in -> Name);
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

	public function print_users() {
		$users = User::getAll();
		$data_buffer = "Full Name\tAccess Level\t\n";
		foreach ($users as $user) {
			$data_buffer .= $user -> Name . "\t" . $user -> Access -> Level_Name . "\n";
		}
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: filename=System Purchase Routes.xls");
		// Fix for crappy IE bug in download.
		header("Pragma: ");
		header("Cache-Control: ");
		echo $data_buffer;
	}

	public function save() {
		$editing = $this -> input -> post("editing_id");
		//Check if we are editing the record first
		if (strlen($editing) > 0) {
			$user = User::getUser($editing);
			$valid = $this -> validate_form($user);
		} else {
			$user = new User();
			$valid = $this -> validate_form();
		}
		//If the fields have been validated, save the input
		if ($valid) {
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
		$user -> save();
		$previous_page = $this -> session -> userdata('old_url');
		redirect($previous_page);
	}

	public function validate_form($user = false) {
		$this -> form_validation -> set_rules('name', 'Full Name', 'trim|required|max_length[100]|xss_clean');
		$this -> form_validation -> set_rules('username', 'Username', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('password', 'Password', 'trim|required|max_length[20]|xss_clean');
		$this -> form_validation -> set_rules('access_level', 'Access Level', 'trim|required|xss_clean');
		$temp_validation = $this -> form_validation -> run();
		if ($temp_validation) {
			//If the user is editing, if the username changes, check whether the new username exists!
			if ($user) {
				if ($user -> Username != $this -> input -> post('username')) {
					$this -> form_validation -> set_rules('username', 'Username', 'trim|required|callback_unique_username');
				}
			} else {
				$this -> form_validation -> set_rules('username', 'Username', 'trim|required|callback_unique_username');
			}

			return $this -> form_validation -> run();
		} else {
			return $temp_validation;
		}
	}

	private function _submit_validate() {
		// validation rules
		$this -> form_validation -> set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[20]');
		$this -> form_validation -> set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[20]');
		return $this -> form_validation -> run();
	}

	public function unique_username($usr) {
		$exists = User::userExists($usr);
		if ($exists) {
			$this -> form_validation -> set_message('unique_username', 'The Username already exists. Enter another one.');
			return FALSE;
		} else {
			return TRUE;
		}

	}

	public function user_authorization() {
		$data = array();
		$validated = $this -> _submit_validate();
		if ($validated) {
			$username = $this -> input -> post("username");
			$password = $this -> input -> post("password");
			$authorization_from = $this -> input -> post("authorization_from");
			$error_callback = $this -> input -> post("error_callback");
			$request_url = $this -> input -> post("request_url");
			$log_message = $this -> input -> post("log_message");
			$logged_in = User::login($username, $password);
			//This code checks if the credentials are valid
			if ($logged_in == false) {
				redirect($error_callback);

			}
			//If the credentials are valid, continue
			else {
				//check to see whether the user is active
				if ($logged_in -> Active == "0") {
					redirect($error_callback);
				}
				//looks good. Continue!
				else {
					if ($logged_in -> Access -> Management_Type != $authorization_from) {
						redirect($error_callback);
					} else {
						$log = new System_Log();
						$log -> Log_Type = "1";
						$log -> Log_Message = $log_message;
						$log -> User = $logged_in -> id;
						$log -> Timestamp = date('U');
						$log -> save();
						redirect($request_url);
					}
				}

			}

		} else {
			redirect($error_callback);
		}
	}

	public function base_params($data) {
		$data['title'] = "User Management";
		$data['sub_link'] = "user_management";
		$data['link'] = "people_management";

		$this -> load -> view("demo_template", $data);
	}

}
