<?php
class Access_Level extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Level_Name', 'varchar', 50);
		$this -> hasColumn('Description', 'text');
		$this -> hasColumn('Indicator', 'varchar', 100);
		$this -> hasColumn('Management_Type', 'varchar', 15);
	}

	public function setUp() {
		$this -> setTableName('access_level');
		$this -> hasMany('User as Users', array('local' => 'id', 'foreign' => 'Access_Level'));
	}

	public function getTotalLevels() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Levels") -> from("Access_Level");
		$total = $query -> execute();
		return $total[0]['Total_Levels'];
	}

	public function getPagedLevels($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Access_Level") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$levels = $query -> execute(array());
		return $levels;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Access_Level");
		$levels = $query -> execute();
		return $levels;
	}
	public function getLevel($level) {
		$query = Doctrine_Query::create() -> select("*") -> from("Access_Level")->where("id='$level'");
		$levels = $query -> execute();
		return $levels[0];
	}
}
