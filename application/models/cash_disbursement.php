<?php
class Cash_Disbursement extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('CIH', 'varchar', 20);
		$this -> hasColumn('Field_Cashier', 'varchar', 10);
		$this -> hasColumn('Amount', 'varchar', 20);
		$this -> hasColumn('Date', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('cash_disbursement');
		$this -> hasOne('Field_Cashier as Field_Cashier_Object', array('local' => 'Field_Cashier', 'foreign' => 'id'));
	}

	public function getTotalDisbursements() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Disbursments") -> from("Cash_Disbursement");
		$total = $query -> execute();
		return $total[0]['Total_Disbursments'];
	}

	public function getPagedDisbursements($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Cash_Disbursement") -> offset($offset) -> limit($items);
		$disbursements = $query -> execute(array());
		return $disbursements;
	}

	public function getDisbursement($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Cash_Disbursement") -> where("id = '$id'");
		$disbursement = $query -> execute();
		return $disbursement[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Cash_Disbursement");
		$disbursements = $query -> execute();
		return $disbursements;
	}

}
