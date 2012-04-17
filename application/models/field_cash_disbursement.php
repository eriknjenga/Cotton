<?php
class Field_Cash_Disbursement extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('CIH', 'varchar', 20);
		$this -> hasColumn('Receipt', 'varchar', 20);
		$this -> hasColumn('Buyer', 'varchar', 10);
		$this -> hasColumn('Field_Cashier', 'varchar', 10);
		$this -> hasColumn('Amount', 'varchar', 20);
		$this -> hasColumn('Date', 'varchar', 20);
	}

	public function setUp() {
		$this -> setTableName('field_cash_disbursement');
		$this -> hasOne('Field_Cashier as Field_Cashier_Object', array('local' => 'Field_Cashier', 'foreign' => 'id'));
		$this -> hasOne('Buyer as Buyer_Object', array('local' => 'Buyer', 'foreign' => 'id'));
	}

	public function getTotalDisbursements() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Disbursments") -> from("Field_Cash_Disbursement");
		$total = $query -> execute();
		return $total[0]['Total_Disbursments'];
	}

	public function getPagedDisbursements($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement") -> offset($offset) -> limit($items)->orderBy("id Desc");
		$disbursements = $query -> execute(array());
		return $disbursements;
	}

	public function getDisbursement($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement") -> where("id = '$id'");
		$disbursement = $query -> execute();
		return $disbursement[0];
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement");
		$disbursements = $query -> execute();
		return $disbursements;
	}

}
