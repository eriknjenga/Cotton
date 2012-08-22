<?php
class Field_Cash_Disbursement extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('CIH', 'varchar', 20);
		$this -> hasColumn('Receipt', 'varchar', 20);
		$this -> hasColumn('Depot', 'varchar', 10);
		$this -> hasColumn('Field_Cashier', 'varchar', 10);
		$this -> hasColumn('Amount', 'varchar', 20);
		$this -> hasColumn('Date', 'varchar', 20);
		$this -> hasColumn('Details', 'text');
		$this -> hasColumn('Batch', 'varchar', 10);
		$this -> hasColumn('Batch_Status', 'varchar', 5);
		$this -> hasColumn('Adjustment', 'varchar', 1);
	}

	public function setUp() {
		$this -> setTableName('field_cash_disbursement');
		$this -> hasOne('Field_Cashier as Field_Cashier_Object', array('local' => 'Field_Cashier', 'foreign' => 'id'));
		$this -> hasOne('Depot as Depot_Object', array('local' => 'Depot', 'foreign' => 'id'));
		$this -> hasOne('Transaction_Batch as Batch_Object', array('local' => 'Batch', 'foreign' => 'id'));
	}

	public function getTotalDisbursements($batch) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Disbursments") -> from("Field_Cash_Disbursement") -> where("Batch = '$batch'");
		$total = $query -> execute();
		return $total[0]['Total_Disbursments'];
	}

	public function getPagedDisbursements($batch, $offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement") -> where("Batch = '$batch'") -> offset($offset) -> limit($items) -> orderBy("id Desc");
		$disbursements = $query -> execute(array());
		return $disbursements;
	}

	public function getDisbursement($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement") -> where("id = '$id'");
		$disbursement = $query -> execute();
		return $disbursement[0];
	}

	public function getBatchDisbursements($batch) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement") -> where("Batch = '$batch'");
		$disbursements = $query -> execute(array());
		return $disbursements;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement");
		$disbursements = $query -> execute();
		return $disbursements;
	}

	public function checkDuplicate($cih) {
		$query = Doctrine_Query::create() -> select("count(*) as Records") -> from("Field_Cash_Disbursement") -> where("CIH = '$cih' and Adjustment != '1'");
		$disbursments = $query -> execute();
		return $disbursments[0] -> Records;
	}

	public function getSearchedCih($cih) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement") -> where("CIH = '$cih'");
		$cih = $query -> execute();
		return $cih;
	}
	public function getSearchedBcr($bcr) {
		$query = Doctrine_Query::create() -> select("*") -> from("Field_Cash_Disbursement") -> where("Receipt = '$bcr'");
		$cih = $query -> execute();
		return $cih;
	}

}
