<?php
class FBG extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('GD_Id', 'varchar', 20);
		$this -> hasColumn('CPC_Number', 'varchar', 20);
		$this -> hasColumn('Group_Name', 'varchar','100'); 
		$this -> hasColumn('Field_Officer', 'varchar', 10);
		$this -> hasColumn('Hectares_Available', 'varchar', 20);
		$this -> hasColumn('Type', 'varchar', 5);
	}

	public function setUp() {
		$this -> setTableName('fbg');
		$this -> hasOne('Field_Officer as Officer_Object', array('local' => 'Field_Officer', 'foreign' => 'id'));
	}

	public function getTotalFbgs() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Fbgs") -> from("fbg");
		$total = $query -> execute();
		return $total[0]['Total_Fbgs'];
	}

	public function getPagedFbgs($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("fbg") -> offset($offset) -> limit($items);
		$fbgs = $query -> execute(array());
		return $fbgs;
	}

	public function getFbg($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("fbg") -> where("id = '$id'");
		$fbg = $query -> execute();
		return $fbg[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("fbg");
		$fbgs = $query -> execute();
		return $fbgs;
	}

}
