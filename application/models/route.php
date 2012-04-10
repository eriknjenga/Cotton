<?php
class Route extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Route_Code', 'varchar', 20);
		$this -> hasColumn('Route_Name', 'varchar', 100);
		$this -> hasColumn('Field_Cashier', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('route');
		$this -> hasOne('Field_Cashier as Field_Cashier_Object', array('local' => 'Field_Cashier', 'foreign' => 'id'));
	}

	public function getTotalRoutes() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Routes") -> from("Route");
		$total = $query -> execute();
		return $total[0]['Total_Routes'];
	}

	public function getPagedRoutes($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Route") -> offset($offset) -> limit($items);
		$routes = $query -> execute(array());
		return $routes;
	}

	public function getRoute($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Route") -> where("id = '$id'");
		$route = $query -> execute();
		return $route[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Route");
		$routes = $query -> execute();
		return $routes;
	}

}
