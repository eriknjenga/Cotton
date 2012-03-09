<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Home_Controller extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	public function index() {

		$this -> home();
	}

	public function home() {
		$quick_menu_rights = Quick_Menu_User_Right::getRights($this -> session -> userdata('access_level'));
		$menu_rights = User_Right::getRights($this -> session -> userdata('access_level'));
		$dashboard_rights = Dashboard_User_Right::getRights($this -> session -> userdata('access_level'));
		$quick_menus = array();
		$counter = 0;
		foreach ($quick_menu_rights as $right) {
			$quick_menus['quick_menu_items'][$counter]['url'] = $right -> Menu_Item -> Menu_Url;
			$quick_menus['quick_menu_items'][$counter]['text'] = $right -> Menu_Item -> Menu_Text;
			$counter++;
		}
		$this -> session -> set_userdata($quick_menus);

		$menus = array();
		$counter = 0;
		foreach ($menu_rights as $right) {
			$menus['menu_items'][$counter]['url'] = $right -> Menu_Item -> Menu_Url;
			$menus['menu_items'][$counter]['text'] = $right -> Menu_Item -> Menu_Text;
			$counter++;
		}
		$this -> session -> set_userdata($menus);

		$dashboards = array();
		$counter = 0;
		foreach ($dashboard_rights as $right) {
			$dashboards['dashboard_items'][$counter]['url'] = $right -> Dashboard_Item -> Dashboard_Url;
			$dashboards['dashboard_items'][$counter]['text'] = $right -> Dashboard_Item -> Dashboard_Text;
			$dashboards['dashboard_items'][$counter]['icon'] = $right -> Dashboard_Item -> Dashboard_Icon;
			$dashboards['dashboard_items'][$counter]['tooltip'] = $right -> Dashboard_Item -> Dashboard_Tooltip;
			$counter++;
		}
		$this -> session -> set_userdata($dashboards);

		$data['title'] = "System Home";
		$data['content_view'] = "home_v";
		$data['banner_text'] = "System Home";
		$data['link'] = "home";
		$this -> load -> view("demo_template", $data);

	}

}
