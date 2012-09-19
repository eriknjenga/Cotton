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
		// Create session for holding quick menus
		$config_session1 = array('sess_cookie_name' => 'quick_menus');
		$this -> load -> library('session', $config_session1, 'quick_menus');

		// Create session for holding  menus
		$config_session = array('sess_cookie_name' => 'menus');
		$this -> load -> library('session', $config_session, 'menus');

		$quick_menu_rights = Quick_Menu_User_Right::getRights($this -> session -> userdata('access_level'));
		$menu_rights = User_Right::getRights($this -> session -> userdata('access_level'));
		$dashboard_rights = Dashboard_User_Right::getRights($this -> session -> userdata('access_level'));
		$quick_menus = array();
		$counter = 0;
		foreach ($quick_menu_rights as $right) {
			$quick_menus['quick_menu_items'][$counter]['url'] = $right -> Menu_Item -> Menu_Url;
			$quick_menus['quick_menu_items'][$counter]['text'] = $right -> Menu_Item -> Menu_Text;
			$quick_menus['quick_menu_items'][$counter]['indicator'] = $right -> Menu_Item -> Indicator;
			$counter++;
		}
		$this -> quick_menus -> set_userdata($quick_menus);
		$menus = array();
		$counter = 0;
		foreach ($menu_rights as $right) {
			$menus['menu_items'][$counter]['url'] = $right -> Menu_Item -> Menu_Url;
			$menus['menu_items'][$counter]['text'] = $right -> Menu_Item -> Menu_Text;
			$counter++;
		}
		$this -> menus -> set_userdata($menus);

		$dashboards = array();
		$counter = 0;
		foreach ($dashboard_rights as $right) {
			$dashboards['dashboard_items'][$counter]['url'] = $right -> Dashboard_Item -> Dashboard_Url;
			$dashboards['dashboard_items'][$counter]['text'] = $right -> Dashboard_Item -> Dashboard_Text;
			$dashboards['dashboard_items'][$counter]['icon'] = $right -> Dashboard_Item -> Dashboard_Icon;
			$dashboards['dashboard_items'][$counter]['tooltip'] = $right -> Dashboard_Item -> Dashboard_Tooltip;
			$dashboards['dashboard_items'][$counter]['dashboard_id'] = $right -> Dashboard_Item -> Dashboard_Id;
			$counter++;
		}
		$data['dashboards'] = $dashboards;
		$data['title'] = "System Home";
		$data['content_view'] = "home_v";
		$data['banner_text'] = "System Home";
		$data['link'] = "home";
		$data['scripts'] = array("FusionCharts/FusionCharts.js");
		$this -> load -> view("demo_template", $data);

	}

	function management_dashboard() {
		$data['title'] = "System Home";
		$data['content_view'] = "management_dashboard_v";
		$todays_date = date("m/d/Y");
		$season = date('Y');
		$this -> load -> database();
		//Get the number of dormant buying centers
		$sql = "select count(*) as total_dormant from (select max(str_to_date(date,'%m/%d/%Y')) as last_purchase,depot from purchase group by depot)last_dates where datediff(str_to_date('" . $todays_date . "','%m/%d/%Y') ,last_purchase) >2 ";
		$query = $this -> db -> query($sql);
		$result = $query -> result_array();
		$data['total_dormant'] = $result[0]['total_dormant'];
		//Get the total number of missing dpns
		$centers_missing_dpns = 0;
		$missing_dpns = 0;
		$sql_reporting_depots = "select * from (select depot,max(abs(dpn)) as dpn from purchase where season = '$season' group by depot) reported_depots left join depot d on reported_depots.depot = d.id order by depot_name asc";
		$query = $this -> db -> query($sql_reporting_depots);
		//Loop through all the returned depots
		foreach ($query->result_array() as $depot_data) {
			if (strlen($depot_data['depot']) > 0) {
				//echo the start of the table
				//sql to get the current book being used
				$sql_get_depot_sequence = "select * from dpn_sequence where season = '" . $season . "' and '" . $depot_data['dpn'] . "' between first and last and depot = '" . $depot_data['depot'] . "'";
				$query = $this -> db -> query($sql_get_depot_sequence);
				$sequence_data = $query -> row_array();
				if (isset($sequence_data['first'])) {
					$sql = "select sequence_numbers from (select (@start_sq := @start_sq +1) as sequence_numbers from dps_sequence,(select @start_sq := " . $sequence_data['first'] . ") s where @start_sq < " . $depot_data['dpn'] . ") sequence where sequence_numbers not in (select dpn from purchase p where depot = '" . $depot_data['depot'] . "' and season = '$season'  and dpn>" . $sequence_data['first'] . ")";
					$query = $this -> db -> query($sql);
					$centers_missing_dpns++;
					if ($query -> num_rows() > 0) {
						foreach ($query->result_array() as $missing_data) {
							$missing_dpns++;
						}
					}
				}
			}
		}
		$data['centers_missing'] = $centers_missing_dpns;
		$data['missing_dpns'] = $missing_dpns;
		$data['banner_text'] = "System Home";
		$data['link'] = "home";
		$data['scripts'] = array("FusionCharts/FusionCharts.js");
		$this -> load -> view("demo_template", $data);
	}

}
