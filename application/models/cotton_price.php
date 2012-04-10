<?php
class Cotton_Price extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Season', 'varchar', 10);
		$this -> hasColumn('Date', 'varchar', 32);
		$this -> hasColumn('Price', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('cotton_price');
	} 

	public function getCottonPrices() {
		$query = Doctrine_Query::create() -> select("*") -> from("Cotton_Price");
		$prices = $query -> execute();
		return $prices;
	}

}
