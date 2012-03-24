<?php

class  MY_Controller  extends  CI_Controller {

	function __construct() {
		parent::__construct();
		// Create session for holding quick menus
		$config_session1 = array('sess_cookie_name' => 'quick_menus');
		$this -> load -> library('session', $config_session1, 'quick_menus');

		// Create session for holding  menus
		$config_session = array('sess_cookie_name' => 'menus');
		$this -> load -> library('session', $config_session, 'menus');

		//Save the url of the current page in a session
		$array = array('old_url' => $this -> session -> userdata('new_url'), 'new_url' => $this -> uri -> uri_string());
		$this -> session -> set_userdata($array);
	}

}
