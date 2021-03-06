<?php
class Field_Cashier extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Field_Cashier_Code', 'varchar', 20);
		$this -> hasColumn('Field_Cashier_Name', 'varchar', 100);
		$this -> hasColumn('National_Id', 'varchar', 30);
		$this -> hasColumn('Phone_Number', 'varchar', 30);
	}

	public function setUp() {
		$this -> setTableName('field_cashier');
	}

	public function getTotalFieldCashiers() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Field_Cashiers") -> from("Field_Cashier");
		$total = $query -> execute();
		return $total[0]['Total_Field_Cashiers'];
	}

	public function getPagedFieldCashiers($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cashier") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$field_cashiers = $query -> execute(array());
		return $field_cashiers;
	}

	public function getFieldCashier($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cashier") -> where("id = '$id'");
		$field_cashier = $query -> execute();
		return $field_cashier[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cashier") -> orderBy("Field_Cashier_Name asc");
		$field_cashiers = $query -> execute();
		return $field_cashiers;
	}

	public function getSearchedCashier($search_value) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cashier") -> where("Field_Cashier_Code like '%$search_value%' or Field_Cashier_Name like '%$search_value%' or National_Id like '%$search_value%' or Phone_Number like '%$search_value%'");
		$results = $query -> execute();
		return $results;
	}

}
