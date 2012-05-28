<?php
class District extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Region', 'varchar',10);
		$this -> hasColumn('Deleted', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('district');
		$this -> hasOne('Region as Region_Object', array('local' => 'Region', 'foreign' => 'id'));
		
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("District")->orderBy("Name asc");
		$districts = $query -> execute();
		return $districts;
	}

}
