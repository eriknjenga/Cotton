<?php
class District extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Region', 'varchar', 10);
		$this -> hasColumn('Deleted', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('district');
		$this -> hasOne('Region as Region_Object', array('local' => 'Region', 'foreign' => 'id'));

	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("District") ->where("Deleted = '0'") -> orderBy("Name asc");
		$districts = $query -> execute();
		return $districts;
	}

	public function getTotalDistricts() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Districts") -> from("District");
		$total = $query -> execute();
		return $total[0]['Total_Districts'];
	}

	public function getPagedDistricts($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("District") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$districts = $query -> execute(array());
		return $districts;
	}

	public function getDistrict($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("District") -> where("id = '$id'");
		$ward = $query -> execute();
		return $ward[0];
	}

}
