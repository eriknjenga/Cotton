<?php
class Cash_Receipt extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Receipt_Number', 'varchar', 100);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Field_Cashier', 'varchar', 10);
		$this -> hasColumn('Amount', 'varchar', 10); 
		$this -> hasColumn('Timestamp', 'varchar', 32); 
		$this -> hasColumn('Batch', 'varchar', 10);
		$this -> hasColumn('Batch_Status', 'varchar', 5);

	}

	public function setUp() {
		$this -> setTableName('Cash_Receipt');
		$this -> hasOne('Field_Cashier as Field_Cashier_Object', array('local' => 'Field_Cashier', 'foreign' => 'id')); 
	}

	public function getTotalReceipts($batch) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Receipts") -> from("Cash_Receipt") -> where("Batch = '$batch'");
		$total = $query -> execute();
		return $total[0]['Total_Receipts'];
	}

	public function getPagedReceipts($batch, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Cash_Receipt") -> where("Batch = '$batch'") -> offset($offset) -> limit($items);
		$receipts = $query -> execute(array());
		return $receipts;
	}

	public function getReceipt($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Cash_Receipt") -> where("id = '$id'");
		$receipt = $query -> execute();
		return $receipt[0];
	}

	public function getBatchReceipts($batch_id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Cash_Receipt") -> where("Batch = '$batch_id'");
		$receipts = $query -> execute(array());
		return $receipts;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Cash_Receipt");
		$receipts = $query -> execute();
		return $receipts;
	}

}
