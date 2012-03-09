<?php
class Dashboard extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Dashboard_Icon', 'varchar', 50);
		$this -> hasColumn('Dashboard_Text', 'varchar', 100);
		$this -> hasColumn('Dashboard_Url', 'varchar', 50);
		$this -> hasColumn('Dashboard_Tooltip', 'varchar', 100);
		$this -> hasColumn('Description', 'text');
	}

	public function setUp() {
		$this -> setTableName('dashboard');
	}

	public static function getAllHydrated() {
		$query = Doctrine_Query::create() -> select("*") -> from("Dashboard");
		$dashboards = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $dashboards;
	}

}
