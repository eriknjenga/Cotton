<?php
class Input_Price extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Farm_Input', 'varchar', 10);
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('Price', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('input_price');
		$this -> hasOne('Farm_Input as Input', array('local' => 'Farm_Input', 'foreign' => 'id'));
	} 

	public function getInputPrices($input) {
		$query = Doctrine_Query::create() -> select("*") -> from("Input_Price")->where("Farm_Input = '$input'");
		$prices = $query -> execute();
		return $prices;
	}

}
