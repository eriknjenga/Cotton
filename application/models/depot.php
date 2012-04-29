<?php
class Depot extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Depot_Code', 'varchar', 20);
		$this -> hasColumn('Depot_Name', 'varchar', 100);
		$this -> hasColumn('Buyer', 'varchar', 10);
		$this -> hasColumn('Region', 'varchar', 10);
		$this -> hasColumn('Deleted', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('depot');
		$this -> hasOne('Buyer as Buyer_Object', array('local' => 'Buyer', 'foreign' => 'id'));
		$this -> hasOne('Region as Region_Object', array('local' => 'Region', 'foreign' => 'id'));
	}

	public function getTotalDepots() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Depots") -> from("Depot") -> where("Deleted = '0'");
		$total = $query -> execute();
		return $total[0]['Total_Depots'];
	}

	public function getPagedDepots($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Depot") -> where("Deleted = '0'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$depots = $query -> execute(array());
		return $depots;
	}

	public function getTotalSearchedDepots($search_value) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Depots") -> from("depot") -> where("(Depot_Code like '%$search_value%' or Depot_Name like '%$search_value%') and Deleted = '0'");
		$total = $query -> execute();
		return $total[0]['Total_Depots'];
	}

	public function getPagedSearchedDepots($search_value, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("depot") -> where("(Depot_Code like '%$search_value%' or Depot_Name like '%$search_value%') and Deleted = '0'") -> offset($offset) -> limit($items);

		$depots = $query -> execute(array());
		return $depots;
	}

	public function getDepot($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Depot") -> where("id = '$id'");
		$depot = $query -> execute();
		return $depot[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Depot") -> where("Deleted = '0'");
		$depots = $query -> execute();
		return $depots;
	}

}
