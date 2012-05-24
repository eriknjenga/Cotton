<?php
class Region extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Region_Code', 'varchar', 100);
		$this -> hasColumn('Region_Name', 'varchar', 100);
	}

	public function setUp() {
		$this -> setTableName('region');
		$this -> hasMany('Depot as Depot_Objects', array('local' => 'id', 'foreign' => 'Region'));
	}

	public function getTotalRegions() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Regions") -> from("Region");
		$total = $query -> execute();
		return $total[0]['Total_Regions'];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Region")->orderBy("Region_Name asc");
		$regions = $query -> execute();
		return $regions;
	}

	public function getPagedRegions($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Region") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$regions = $query -> execute(array());
		return $regions;
	}

	public function getRegion($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Region") -> where("id = '$id'");
		$region = $query -> execute();
		return $region[0];
	}

	public function getRegionArray($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Region") -> where("id = '$id'");
		$region = $query -> execute();
		return $region;
	}

}
