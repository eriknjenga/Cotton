<?php
class Farm_Input extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Product_Code', 'varchar', 100);
		$this -> hasColumn('Product_Name', 'varchar', 100);
		$this -> hasColumn('Product_Description', 'text');
		$this -> hasColumn('Deleted', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('farm_input');
		$this -> hasMany('Input_Price as Prices', array('local' => 'id', 'foreign' => 'Farm_Input'));
	}

	public function getTotalInputs() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Inputs") -> from("Farm_Input") -> where("Deleted = '0'");
		$total = $query -> execute();
		return $total[0]['Total_Inputs'];
	}

	public function getPagedInputs($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input") -> where("Deleted = '0'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$inputs = $query -> execute(array());
		return $inputs;
	}

	public function getInput($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input") -> where("id = '$id'");
		$input = $query -> execute();
		return $input[0];
	}

	public function getInputArray($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input") -> where("id = '$id'");
		$input = $query -> execute();
		return $input;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input") -> where("Deleted = '0'");
		$inputs = $query -> execute();
		return $inputs;
	}

}
