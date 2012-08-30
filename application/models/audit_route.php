<?php
class Audit_Route extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Route_Code', 'varchar', 20);
		$this -> hasColumn('Route_Name', 'varchar', 100); 
	}

	public function setUp() {
		$this -> setTableName('audit_route');
		$this -> hasMany('Depot as Route_Depot_Objects', array('local' => 'id', 'foreign' => 'Audit_Route'));
	}

	public function getTotalRoutes() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Routes") -> from("Audit_Route");
		$total = $query -> execute();
		return $total[0]['Total_Routes'];
	}

	public function getPagedRoutes($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Audit_Route") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$routes = $query -> execute(array());
		return $routes;
	}

	public function getRoute($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Audit_Route") -> where("id = '$id'");
		$route = $query -> execute();
		return $route[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Audit_Route")->orderBy("abs(Route_Code) asc");
		$routes = $query -> execute();
		return $routes;
	}

	public function getRouteArray($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Audit_Route") -> where("id = '$id'");
		$route = $query -> execute();
		return $route;
	}

}
