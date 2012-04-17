<?php
class Farm_Input extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Product_Code', 'varchar', 100);
		$this -> hasColumn('Product_Name', 'varchar', 100);
		$this -> hasColumn('Product_Description', 'text'); 
	}

	public function setUp() {
		$this -> setTableName('farm_input');
		$this -> hasMany('Input_Price as Prices', array('local' => 'id', 'foreign' => 'Farm_Input'));
	}

	public function getTotalInputs() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Inputs") -> from("Farm_Input");
		$total = $query -> execute();
		return $total[0]['Total_Inputs'];
	}

	public function getPagedInputs($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input") -> offset($offset) -> limit($items)->orderBy("id Desc");
		$inputs = $query -> execute(array());
		return $inputs;
	}

	public function getInput($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input")->where("id = '$id'");
		$input = $query -> execute();
		return $input[0];
	}
	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input");
		$inputs = $query -> execute();
		return $inputs;
	}
}
