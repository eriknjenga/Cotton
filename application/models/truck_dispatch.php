<?php
class Truck_Dispatch extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Depot', 'varchar', 10);
		$this -> hasColumn('Date', 'varchar', 15);
		$this -> hasColumn('Truck', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('truck_dispatch');
		$this -> hasOne('Depot as Depot_Object', array('local' => 'Depot', 'foreign' => 'id'));
		$this -> hasOne('Truck as Truck_Object', array('local' => 'Truck', 'foreign' => 'id'));
	}

	public function getTotalDispatches() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Dispatches") -> from("Truck_Dispatch");
		$total = $query -> execute();
		return $total[0]['Total_Dispatches'];
	}

	public function getPagedDispatches($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Truck_Dispatch") -> offset($offset) -> limit($items)->orderBy("id Desc");
		$dispatches = $query -> execute(array());
		return $dispatches;
	}

	public function getDispatch($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Truck_Dispatch") -> where("id = '$id'");
		$dispatch = $query -> execute();
		return $dispatch[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Truck_Dispatch");
		$dispatches = $query -> execute();
		return $dispatches;
	}

}
