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

	public function getTotalPrices() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Prices") -> from("Cotton_Price");
		$total = $query -> execute();
		return $total[0]['Total_Prices'];
	}

	public function getPagedPrices($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Cotton_Price") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$prices = $query -> execute(array());
		return $prices;
	}

	public function getPrice($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Cotton_Price") -> where("id = '$id'");
		$price = $query -> execute();
		return $price[0];
	}

}
