<?php
class FBG extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('CPC_Number', 'varchar', 20);
		$this -> hasColumn('Group_Name', 'varchar', 100);
		$this -> hasColumn('Field_Officer', 'varchar', 10);
		$this -> hasColumn('Village', 'varchar', 10);
		$this -> hasColumn('Hectares_Available', 'varchar', 20);
		$this -> hasColumn('Chairman_Name', 'varchar', 100);
		$this -> hasColumn('Chairman_Phone', 'varchar', 100);
		$this -> hasColumn('Secretary_Name', 'varchar', 100);
		$this -> hasColumn('Acre_Yield', 'varchar', 10);
		$this -> hasColumn('Secretary_Phone', 'varchar', 100);
	}

	public function setUp() {
		$this -> setTableName('fbg');
		$this -> hasOne('Field_Officer as Officer_Object', array('local' => 'Field_Officer', 'foreign' => 'id'));
		$this -> hasOne('Village as Village_Object', array('local' => 'Village', 'foreign' => 'id'));
	}

	public function getTotalFbgs() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Fbgs") -> from("fbg");
		$total = $query -> execute();
		return $total[0]['Total_Fbgs'];
	}

	public function getPagedFbgs($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("fbg") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$fbgs = $query -> execute(array());
		return $fbgs;
	}

	public function getTotalSearchedFbgs($search_value) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Fbgs") -> from("fbg") -> where("CPC_Number like '%$search_value%' or Group_Name like '%$search_value%' or Chairman_Name like '%$search_value%' or Chairman_Phone like '%$search_value%' or Secretary_Name like '%$search_value%' or Secretary_Phone like '%$search_value%'");
		$total = $query -> execute();
		return $total[0]['Total_Fbgs'];
	}

	public function getPagedSearchedFbgs($search_value, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("fbg") -> where("CPC_Number like '%$search_value%' or Group_Name like '%$search_value%' or Chairman_Name like '%$search_value%' or Chairman_Phone like '%$search_value%' or Secretary_Name like '%$search_value%' or Secretary_Phone like '%$search_value%'") -> offset($offset) -> limit($items);
		$fbgs = $query -> execute(array());
		return $fbgs;
	}

	public function getFbg($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("fbg") -> where("id = '$id'");
		$fbg = $query -> execute();
		return $fbg[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("fbg") -> orderBy("Group_Name asc");
		$fbgs = $query -> execute();
		return $fbgs;
	}
 

}
