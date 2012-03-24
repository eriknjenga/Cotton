<?php
class Farm_Input extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Product_Code', 'varchar', 100);
		$this -> hasColumn('Product_Name', 'varchar', 100);
		$this -> hasColumn('Product_Description', 'text');
		$this -> hasColumn('Unit_Price', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('farm_input');
	}

	public function getTotalInputs() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Inputs") -> from("Farm_Input");
		$total = $query -> execute();
		return $total[0]['Total_Inputs'];
	}

	public function getPagedInputs($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input") -> offset($offset) -> limit($items);
		$inputs = $query -> execute(array());
		return $inputs;
	}

	public function getInput($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Farm_Input")->where("id = '$id'");
		$input = $query -> execute();
		return $input[0];
	}

}
