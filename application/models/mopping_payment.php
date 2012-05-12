<?php
class Mopping_Payment extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('Voucher_Number', 'varchar', 100);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Depot', 'varchar', 10);
		$this -> hasColumn('Amount', 'varchar', 20);  
		$this -> hasColumn('Batch', 'varchar', 10);
		$this -> hasColumn('Batch_Status', 'varchar', 5);

	}

	public function setUp() {
		$this -> setTableName('Mopping_Payment');
		$this -> hasOne('Depot as Depot_Object', array('local' => 'Depot', 'foreign' => 'id')); 
	}

	public function getTotalPayments($batch) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Payments") -> from("Mopping_Payment") -> where("Batch = '$batch'");
		$total = $query -> execute();
		return $total[0]['Total_Payments'];
	}

	public function getPagedPayments($batch, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Mopping_Payment") -> where("Batch = '$batch'") -> offset($offset) -> limit($items);
		$payments = $query -> execute(array());
		return $payments;
	}

	public function getPayment($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Mopping_Payment") -> where("id = '$id'");
		$payment = $query -> execute();
		return $payment[0];
	}

	public function getBatchPayments($batch_id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Mopping_Payment") -> where("Batch = '$batch_id'");
		$payments = $query -> execute(array());
		return $payments;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Mopping_Payment");
		$payments = $query -> execute();
		return $payments;
	}

}
