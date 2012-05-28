<?php
class Village extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Ward', 'varchar', 10);
		$this -> hasColumn('Deleted', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('village');
		$this -> hasOne('Ward as Ward_Object', array('local' => 'Ward', 'foreign' => 'id'));
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Village") -> orderBy("Name asc");
		$villages = $query -> execute();
		return $villages;
	}

	public function getPagedSearchedVillages($search_value, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Village") -> where("Name like '%$search_value%'") -> offset($offset) -> limit($items);
		$villages = $query -> execute(array());
		return $villages;
	}

}
