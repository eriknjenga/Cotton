<?php
class Area extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Area_Code', 'varchar', 20);
		$this -> hasColumn('Area_Name', 'varchar', 100);
		$this -> hasColumn('Region', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('area');
		$this -> hasOne('Region as Region_Object', array('local' => 'Region', 'foreign' => 'id'));
	}

	public function getTotalAreas() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Areas") -> from("Area");
		$total = $query -> execute();
		return $total[0]['Total_Areas'];
	}

	public function getPagedAreas($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Area") -> offset($offset) -> limit($items)->orderBy("id Desc");
		$areas = $query -> execute(array());
		return $areas;
	}

	public function getArea($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Area") -> where("id = '$id'");
		$area = $query -> execute();
		return $area[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Area");
		$areas = $query -> execute();
		return $areas;
	}

}
