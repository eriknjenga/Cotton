<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class User_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
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

	public function listing() {
		$data['content_view'] = "list_users_v";
		$this -> base_params($data);
	}

	public function authenticate() {
		$data = array();
		$validated = $this -> _submit_validate();
		if ($validated) {
			$username = $this -> input -> post("username");
			$password = $this -> input -> post("password");
			$remember = $this -> input -> post("remember");
			$logged_in = Users::login($username, $password);
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

	private function _submit_validate() {
		// validation rules
		$this -> form_validation -> set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[12]');
		$this -> form_validation -> set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[12]');

		return $this -> form_validation -> run();
	}

	public function new_user() {
		$data['content_view'] = "add_user_v";
		$data['quick_link'] = "register_user";
		$this -> base_params($data);
	}

	public function go_home($data) {
		$data['title'] = "System Home";
		$data['content_view'] = "home_v";
		$data['banner_text'] = "Dashboards";
		$data['link'] = "home";
		$this -> load -> view("template", $data);
	}

	public function base_params($data) {
		$data['link'] = "user_management";
		$this -> load -> view("demo_template", $data);
	}

}
