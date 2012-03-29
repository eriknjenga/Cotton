<?php
class Field_Officer extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Officer_Code', 'varchar', 100);
		$this -> hasColumn('Officer_Name', 'varchar', 100); 
		$this -> hasColumn('National_Id', 'varchar', 20);
		$this -> hasColumn('Area', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('field_officer');
		$this -> hasOne('Area as Area_Object', array('local' => 'Area', 'foreign' => 'id'));
	}

	public function getTotalOfficers() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Officers") -> from("Field_Officer");
		$total = $query -> execute();
		return $total[0]['Total_Officers'];
	}

	public function getPagedOfficers($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Officer") -> offset($offset) -> limit($items);
		$officers = $query -> execute(array());
		return $officers;
	}

	public function getOfficer($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Officer")->where("id = '$id'");
		$officer = $query -> execute();
		return $officer[0];
	}
	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Officer");
		$field_officers = $query -> execute();
		return $field_officers;
	}

}
