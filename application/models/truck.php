<?php
class Truck extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Number_Plate', 'varchar', 100);
		$this -> hasColumn('Category', 'varchar', 1);
		$this -> hasColumn('Capacity', 'varchar', 10);
		$this -> hasColumn('Agreed_Rate', 'varchar', 10);
		$this -> hasColumn('Deleted', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('truck');
	}

	public function getTotalTrucks() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Trucks") -> from("truck") -> where("Deleted = '0'");
		$total = $query -> execute();
		return $total[0]['Total_Trucks'];
	}

	public function getPagedTrucks($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("truck") -> where("Deleted = '0'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$trucks = $query -> execute(array());
		return $trucks;
	}

	public function getTruck($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("truck") -> where("id = '$id'");
		$truck = $query -> execute();
		return $truck[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Truck")-> where("Deleted = '0'");
		$trucks = $query -> execute();
		return $trucks;
	}

}
