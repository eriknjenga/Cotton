<?php
class Ward extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Region', 'varchar', 10);
		$this -> hasColumn('District', 'varchar', 10);
		$this -> hasColumn('Deleted', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('ward');
		$this -> hasOne('Region as Region_Object', array('local' => 'Region', 'foreign' => 'id'));
		$this -> hasOne('District as District_Object', array('local' => 'District', 'foreign' => 'id'));
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Ward")->where("Deleted = '0'") -> orderBy("Name asc");
		$wards = $query -> execute();
		return $wards;
	}

	public function getTotalWards() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Wards") -> from("Ward");
		$total = $query -> execute();
		return $total[0]['Total_Wards'];
	}

	public function getPagedWards($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Ward") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$wards = $query -> execute(array());
		return $wards;
	}

	public function getWard($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Ward") -> where("id = '$id'");
		$ward = $query -> execute();
		return $ward[0];
	} 

}
