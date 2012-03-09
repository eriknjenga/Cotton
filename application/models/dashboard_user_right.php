<?php
class Dashboard_User_Right extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Access_Level', 'varchar', 10);
		$this -> hasColumn('Dashboard', 'varchar', 10); 
	}

	public function setUp() {
		$this -> setTableName('dashboard_user_right');
		$this -> hasOne('Dashboard as Dashboard_Item', array('local' => 'Dashboard', 'foreign' => 'id'));
	}

	public static function getRights($access_level) {
		$query = Doctrine_Query::create() -> select("*") -> from("Dashboard_User_Right") -> where("Access_Level = '" . $access_level . "'");
		$rights = $query -> execute();
		return $rights;
	}

}
