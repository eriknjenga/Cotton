<?php
class Buyer extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Buyer_Code', 'varchar', 100);
		$this -> hasColumn('Name', 'varchar', 100);
		$this -> hasColumn('National_Id', 'varchar', 30);
		$this -> hasColumn('Phone_Number', 'varchar', 30);
		$this -> hasColumn('Deleted', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('buyer');

	}

	public function getTotalBuyers() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Buyers") -> from("Buyer") -> where("Deleted = '0'");
		$total = $query -> execute();
		return $total[0]['Total_Buyers'];
	}

	public function getPagedBuyers($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Buyer") -> where("Deleted = '0'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$buyers = $query -> execute(array());
		return $buyers;
	}

	public function getPagedSearchedBuyers($search_value, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("buyer") -> where("Buyer_Code like '%$search_value%' or Name like '%$search_value%' or National_Id like '%$search_value%' or Phone_Number like '%$search_value%'") -> offset($offset) -> limit($items);
		$buyers = $query -> execute(array());
		return $buyers;
	}

	public function getBuyer($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Buyer") -> where("id = '$id'");
		$buyer = $query -> execute();
		return $buyer[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Buyer") -> where("Deleted = '0'") -> orderBy("Name asc");
		$buyers = $query -> execute();
		return $buyers;
	}

}
