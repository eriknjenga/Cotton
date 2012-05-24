<?php
class Route_Depot extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Route', 'varchar', 10);
		$this -> hasColumn('Depot', 'varchar', 10); 
	}

	public function setUp() {
		$this -> setTableName('route_depot'); 
		$this -> hasOne('Route as Route_Object', array('local' => 'Route', 'foreign' => 'id'));
		$this -> hasOne('Depot as Depot_Object', array('local' => 'Depot', 'foreign' => 'id'));
	} 

	public function getAllForRoute($route) {
		$query = Doctrine_Query::create() -> select("*") -> from("Route_Depot")->where("Route = '$route'");
		$depots = $query -> execute();
		return $depots;
	}

}
