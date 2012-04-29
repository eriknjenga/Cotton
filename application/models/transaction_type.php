<?php
class Transaction_Type extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Indicator', 'varchar', 30);
	}

	public function setUp() {
		$this -> setTableName('transaction_type');
		$this -> hasMany('Transaction_Batch as Batches', array('local' => 'id', 'foreign' => 'Transaction_Type'));
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Transaction_Type");
		$types = $query -> execute();
		return $types;
	}

	public function getTypeId($type) {
		$query = Doctrine_Query::create() -> select("id") -> from("Transaction_Type")->where("Indicator = '$type'");
		$types = $query -> execute();
		return $types[0]->id;
	}

}
